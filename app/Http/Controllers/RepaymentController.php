<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Repayment;
use App\Http\Resources\RepaymentResource;

class RepaymentController extends Controller
{
    public function store(Request $request, $id)
    {

        $loan = Loan::find($id);
        if (!$loan)
            return $this->respondWithError('No loan information found with this id.', [], 404);

        // check if current authenticated user is the owner of the loan
        if ($request->user()->id !== $loan->user_id) {
            return $this->respondWithError('You can only add repayments to your own loan.', [], 403);
        }

        $rules = [
            'repayment_amount' => 'required|numeric'

        ];

        $response = $this->validateWithJson($request->all(), $rules);

        if ($response === true) {
            if ($loan->status == 'Paid') {
                return $this->respondWithError('Loan amount has already been paid.', [], 403);
            } else if ($loan->status == 'Approved') {
                $repay = Repayment::create(
                    [
                        'user_id' => $request->user()->id,
                        'loan_id' => $loan->id,
                        'repayment_amount' => $request->repayment_amount,
                    ]
                );
                if ($loan->repayment()->sum('repayment_amount') >= $loan->finalLoanAmount()) {
                    // update status to complete
                    $loan->status = 'Paid';
                    $loan->save();
                }
                return $this->respondWithSuccess('Amount repaid successfully.', RepaymentResource::make($repay));
            }
            return $this->respondWithError('Your loan status is not yet Approved.', [], 403);
        }

        return $this->respondWithError('Validation Error', $response);
    }
}
