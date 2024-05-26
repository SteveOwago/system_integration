<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Course::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3), // Generates a random course name
            'description' => $this->faker->paragraph, // Generates a random course description
            'price' => $this->faker->randomFloat(2, 100, 1000), // Generates a random price between 100 and 1000
        ];
    }
}
