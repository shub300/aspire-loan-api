<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Models\Loan;
use App\Models\User;
use App\Http\Resources\LoanResource;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /*
        test to get all loan information user without authentication
        expected status 401 with message Unauthenticated.
    */
    public function test_it_retrieves_all_loans_without_authentication()
    {
        $response = $this->json('GET', '/api/loan');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test retrieves all loan information of the authenticated user
        expected status 200 with the collection of Loan created by the authenticated user
    */
    public function test_it_retrieves_all_loans_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/loan');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Loan Records"]);
        $response->assertJsonStructure(['data']);
    }


    /*
        test to create loan without authorization token
        expected status 401 with json error message Unauthenticated.
    */
    public function test_it_creates_loan_without_authentication()
    {
        $loan = Loan::factory(Loan::class)->make();
        $response = $this->json('POST', '/api/loan', $loan->toArray());
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test performs loan creation with authenticated user
        expected status 200 with json information
    */
    public function test_it_creates_loan_with_authentication()
    {
        $loan = Loan::factory(Loan::class)->make();
        $user = User::factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('POST', '/api/loan', $loan->toArray());
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => 'Loan Created Successfully']);
        $response->assertJson(['data' => $loan->toArray()]);
    }

    /*
        test retrieves specific loan information of the authenticated user
        expected status 200 with single loan data by user 
    */
    public function test_it_retrieves_specific_loans_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user, 'api')->json('GET', '/api/loan/' . $loan->id);
        $response->assertStatus(200);
        // assert the user
        $response->assertJson(['success' => true]);

        $response->assertJson(['data' => $loan->toArray()]);
    }

    /*
        test trys to retrieve non existing loan information from the authenticated user
        expected status 404 with json error message.
    */
    public function test_it_retrieves_non_existing_loans_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/loan/0');
        $response->assertStatus(404);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => 'No loan information found with this id.']);
    }

    /*
        test trys to retrieve other users loan information
        expected status 403 with json error message.
    */
    public function test_it_retrieves_one_other_loan_with_authentication()
    {
        $user1 = User::factory(User::class)->create();
        $loan1 = Loan::factory(Loan::class)->create([
            'user_id' => $user1->id,
        ]);
        $user2 = User::factory(User::class)->create();
        $loan2 = Loan::factory(Loan::class)->create([
            'user_id' => $user2->id,
        ]);
        $response = $this->actingAs($user1, 'api')->json('GET', '/api/loan/' . $loan2->id);
        $response->assertStatus(403);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => 'You can only access your own loan information.']);
    }

    /*
        test trys to update existing loan of the user without authentication
        expected status 401 with message Unauthenticated.
    */
    public function test_it_updates_loan_without_authentication()
    {
        $response = $this->json('PATCH', '/api/loan/0');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /*
        test it updates existing loan of the authenticated user
        expected status 200 with json information
    */
    public function test_it_update_loan_info_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);
        $response = $this->actingAs($user, 'api')->json('PATCH', '/api/loan/' . $loan->id);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['data' => $loan->toArray()]);
    }

    /*
        test to update one existing loan information to Approved status owned by authenticated user
        expected status 200 with json information
    */
    public function test_it_update_loan_status_to_approved_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);
        $response = $this->actingAs($user, 'api')->json('PATCH', '/api/loan/' . $loan->id, ['status' => 'Approved', 'amount' => 15000, 'interest_rate' => 5]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['data']);
    }

    /*
        test trys to update one existing Approved loan information owned by authenticated user
        expected status 403 with json information
    */
    public function test_it_updates_approved_loan_information_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Approved',
        ]);
        $response = $this->actingAs($user, 'api')->json('PATCH', '/api/loan/' . $loan->id);
        $response->assertStatus(403);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => "You're not allowed to update this loan information."]);
    }

    /*
        test trys to update one existing Rejected loan information owned by authenticated user
        expected status 403 with json information
    */
    public function test_it_updates_rejected_loan_information_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
            'status' => 'Rejected',
        ]);
        $response = $this->actingAs($user, 'api')->json('PATCH', '/api/loan/' . $loan->id, ['status' => 'Approved']);
        $response->assertStatus(403);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => "You're not allowed to update this loan information."]);
    }

    /*
        test trys to delete other users loan information
        expected status 403 with json error message.
    */
    public function test_it_delete_one_other_loan_with_authentication()
    {
        $user1 = User::factory(User::class)->create();
        $loan1 = Loan::factory(Loan::class)->create([
            'user_id' => $user1->id,
        ]);
        $user2 = User::factory(User::class)->create();
        $loan2 = Loan::factory(Loan::class)->create([
            'user_id' => $user2->id,
        ]);
        $response = $this->actingAs($user1, 'api')->json('DELETE', '/api/loan/' . $loan2->id);
        $response->assertStatus(403);
        $response->assertJson(['error' => true]);
        $response->assertJson(['message' => 'You can only delete your own loan information.']);
    }

    /*
        test delets specific loan information of the authenticated user
        expected status 200 with single loan data by user 
    */
    public function test_it_deletes_specific_loans_with_authentication()
    {
        $user = User::factory(User::class)->create();
        $loan = Loan::factory(Loan::class)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user, 'api')->json('DELETE', '/api/loan/' . $loan->id);
        $response->assertStatus(200);
        // assert the user
        $response->assertJson(['success' => true]);

        $response->assertJson(['message' => "Loan id $loan->id is deleted successfully"]);
    }
}
