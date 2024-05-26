<?php

namespace Database\Factories;

use App\Models\MpesaPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MpesaPayment>
 */
class MpesaPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = MpesaPayment::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->numberBetween(1, 1000),
            'mpesa_receipt_number' => $this->faker->unique()->bothify('##??##??'),
            'transaction_date' => $this->faker->dateTimeThisYear,
            'phone_number' => '2547' . $this->faker->numerify('########'),
            'course_id' => \App\Models\Course::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
