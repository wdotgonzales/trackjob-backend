<?php

namespace Database\Factories;

use App\Models\VerificationCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VerificationCode>
 */
class VerificationCodeFactory extends Factory
{
    protected $model = VerificationCode::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        ];
    }
    
    public function generateRandomOtp(): static
    {
        return $this->state(fn (array $attributes) => [
            'otp' => $this->faker->numerify('######'), // Generates a 6-digit number
        ]);
    }

    public function insertUserId($user_id){
        return $this->state(fn(array $attributes) => [
            'user_id' => $user_id
        ]);
    }
}
