<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now'); // start date
        $endDate   = $this->faker->dateTimeBetween($startDate, '+1 year'); // end date after start

        return [
            'title' => $this->faker->name(),
            'description' => $this->faker->text(50),
            'start_date' =>  $startDate->format('Y-m-d'),
            'end_date'   =>  $endDate->format('Y-m-d'),
        ];
    }
}
