<?php

namespace Database\Factories;

use App\Models\MpesaStk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MpesaStk>
 */
class MpesaStkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = MpesaStk::class;

    public function definition()
    {
        return [
            'merchant_request_id' => $this->faker->uuid,
            'checkout_request_id' => $this->faker->uuid,
            'course_id' => \App\Models\Course::factory(),
            'user_id' => \App\Models\User::factory(),
            'status' => '0', // Default status
        ];
    }
}
