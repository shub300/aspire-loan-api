<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Http\Resources\LoanResource;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loan = $request->user()->loans()->get();
        return $this->respondWithSuccess('Loan Records', LoanResource::make($loan));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric',
            'loan_term' => 'required|numeric',
            'interest_rate' => 'numeric|between:0,100',
            'repayment_amount' => 'numeric',
        ];

        $response = $this->validateWithJson($request->all(), $rules);

        if ($response === true) {
            $payload = [
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'loan_term' => $request->loan_term,
                'status' => 'Pending',
            ];
            if (isset($request->repayment_frequency))
                $payload['repayment_frequency'] = $request->repayment_frequency;
            if (isset($request->repayment_amount))
                $payload['repayment_amount'] = $request->repayment_amount;
            if (isset($request->interest_rate))
                $payload['interest_rate'] = $request->interest_rate;

            $loan = Loan::create($payload);

            if ($loan) {
                return $this->respondWithSuccess('Loan Created Successfully',  LoanResource::make($loan));
            }
        }

        return $this->respondWithError('Validation Error', $response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $loan = Loan::find($id);
        if (!$loan)
            return $this->respondWithError('No loan information found with this id.', [], 404);
        // check if current authenticated user is the owner of the loan record
        if ($request->user()->id !== $loan->user_id) {
            return $this->respondWithError('You can only access your own loan information.', [], 403);
        }

        return $this->respondWithSuccess('Loan Information Found Successfully', LoanResource::make($loan));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::find($id);
        if (!$loan)
            return $this->respondWithError('No loan information found with this id.', [], 404);

        // check if current authenticated user is the owner of the loan record
        if ($request->user()->id !== $loan->user_id) {
            return $this->respondWithError('You can only access your own loan information.', [], 403);
        }

        if ($loan->status == 'Pending') {

            if (isset($request->amount))
                $loan->amount = $request->amount;

            if (isset($request->loan_term))
                $loan->loan_term = $request->loan_term;

            if (isset($request->repayment_frequency))
                $loan->repayment_frequency = $request->repayment_frequency;

            if (isset($request->interest_rate))
                $loan->interest_rate = $request->interest_rate;

            if (isset($request->repayment_amount))
                $loan->repayment_amount = $request->repayment_amount;

            if (isset($request->status))
                $loan->status = $request->status;
        } else {
            return $this->respondWithError('You\'re not allowed to update this loan information.', [], 403);
        }

        $loan->save();

        return $this->respondWithSuccess('Loan Information Updated Successfully',  LoanResource::make($loan));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $loan = Loan::find($id);
        if (!$loan)
            return $this->respondWithError('No loan information found with this id.', [], 404);

        // check if current authenticated user is the owner of the loan record
        if ($request->user()->id !== $loan->user_id) {
            return $this->respondWithError('You can only delete your own loan information.', [], 403);
        }

        if ($loan->status == 'Pending') {
            $loan_id = $loan->id;
            $loan->delete();

            return $this->respondWithSuccess("Loan id $loan_id is deleted successfully");
        } else {
            return $this->respondWithError('You\'re not allowed to delete this loan information.', [], 403);
        }
    }
}
