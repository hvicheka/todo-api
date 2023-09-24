<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'user_id' => 1,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(5),
            'priority' => $this->faker->randomElement(array_values(TaskPriority::all()->toArray())),
            'date' => $this->faker->dateTimeBetween('now', '+5 days'),
            'status' => $this->faker->randomElement(array_values(TaskStatus::all()->toArray())),
        ];
    }
}
