<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' =>  $this->faker->numberBetween(10000, 1000000),
            'loan_term' =>  $this->faker->numberBetween(1, 60),
            'repayment_frequency' => 'WEEKLY',
            'interest_rate' =>  $this->faker->randomFloat(2, 5, 7),
            'repayment_amount' =>  $this->faker->numberBetween(1000, 10000),
        ];
    }
}
