<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Models\Loan;
use App\Models\User;

class RepaymentTest extends TestCase
{
    /*
        test to add a repayment for the user without authentication
        expected status 401 with message Unauthenticated.
    */
    public function test_it_create_repayment_without_authentication()
    {
        $response = $this->json('POST', '/api/repayments/0');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test to add a repayment to non existing loan with authentication
        expected status 401 with message Unauthenticated.
    */
    public function test_it_create_repayment_for_non_exiting_loan_with_authentication()
    {
        $user1 = User::factory(User::class)->create();
        $response = $this->actingAs($user1, 'api')->json('POST', '/api/repayments/0', ['repayment_amount' => 20]);
        $response->assertStatus(404);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => 'No loan information found with this id.']);
    }

    /*
        test to create a repayment for other user with authentication
        expected status 403 with json error message.
    */
    public function test_it_create_one_other_repayment_with_authentication()
    {
        $user1 = User::factory(User::class)->create();
        $loan1 = Loan::factory(Loan::class)->create([
            'user_id' => $user1->id,
        ]);
        $user2 = User::factory(User::class)->create();
        $response = $this->actingAs($user2, 'api')->json('POST', '/api/repayments/' . $loan1->id, ['repayment_amount' => 20]);
        $response->assertStatus(403);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => 'You can only add repayments to your own loan.']);
    }

    /*
        test validates repayment amount 
        expected status 400 with json error information.
    */
    public function test_it_validates_repayment_amount_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);
        $response = $this->actingAs($user, 'api')->json('POST', '/api/repayments/' . $loan->id, []);
        $response->assertStatus(400);
        $response->assertJson(['error' => true])
            ->assertJson([
                "error" => true,
                'data' =>
                [
                    'repayment_amount' => ['The repayment amount field is required.'],

                ]
            ]);
    }

    /*
        test to create one repayment for non Accepted Loan owned by this user with authentication
        expected status 403 with message Your loan status is not Accepted.
    */
    public function test_it_create_repayment_on_pending_loan_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);
        $response = $this->actingAs($user, 'api')->json('POST', '/api/repayments/' . $loan->id, ['repayment_amount' => 1000]);

        $response->assertStatus(403);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => 'Your loan status is not yet Approved.']);
    }

    /*
        test adds a repayment for the Approved Loan owned by the authentication user
        expected status 200
    */
    public function test_it_creates_repayment_on_approved_loan_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Approved',
        ]);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/repayments/' . $loan->id, ['repayment_amount' => '1000']);
        $response->assertStatus(200);
        // assert repayment amount
        $response->assertJsonStructure(['data']);
    }

    /*
        test to create a full repayment of the Approved Loan amount owned by the authenticated user
        expected status 200
    */
    public function test_it_create_full_loan_repayment_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Approved',
        ]);

        $amount = ($loan->amount + ($loan->amount * $loan->loan_term * $loan->interest_rate) / 100);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/repayments/' . $loan->id, ['repayment_amount' => $amount]);
        $response->assertStatus(200);

        // assert repayment amount
        $response->assertJson(['data' => ['repayment_amount' => $amount]]);
    }

    /*
        test trys to add repayment on the paid loans
        expected status 403 with json error messgae
    */
    public function test_it_creates_repayment_on_paid_loans_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Paid',
        ]);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/repayments/' . $loan->id, ['repayment_amount' => '10000']);
        $response->assertStatus(403);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => 'Loan amount has already been paid.']);
    }
}
