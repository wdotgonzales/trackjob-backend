<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplicationStatus>
 */
class JobApplicationStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    public function setTitle(string $title){
        return $this->state(fn (array $attributes) => [
            'title' => $title
        ]);
    }
    
    public function setDescription(string $description){
        return $this->state(fn (array $attributes) => [
            'description' => $description
        ]);
    }
}
