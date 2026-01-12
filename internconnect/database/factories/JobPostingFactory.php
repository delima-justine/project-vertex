<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPosting>
 */
class JobPostingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraph(),
            'requirements' => $this->faker->sentence(),
            'department' => $this->faker->randomElement(['IT', 'HR', 'Marketing', 'Finance']),
            'salary_range' => $this->faker->numberBetween(30000, 100000),
            'posted_by_user_id' => 1, // Assuming user with ID 1 exists
            'post_date' => $this->faker->dateTimeBetween('-7 years', '-6 years')->format('Y-m-d'),
        ];
    }
}
