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
            'otp' => $this->faker->numerify('######'), // Default OTP generation
        ];
    }

    public function setSubscriptionDate($subscription_id): static
    {
        return $this->state(function (array $attributes) use ($subscription_id) {
            $monthsToAdd = match ($subscription_id) {
                1 => 1,
                2 => 4,
                3 => 10,
                default => 0,
            };

            return [
                'start_date' => now(),
                'expiration_date' => now()->addMonths($monthsToAdd)
            ];
        });
    }
}
