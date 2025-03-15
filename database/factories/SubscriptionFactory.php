<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Subscription;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_date = $this->faker->dateTimeBetween('-1 year', 'now'); // Random past date
        $end_date = $this->faker->dateTimeBetween($start_date, '+1 month'); // Within 1 month after start_date

        return [
            'start_date' => $start_date,
            'end_date' => $end_date
        ];
    }

    // public function setUserId(int $user_id)
    // {
    //     return $this->state(fn(array $attributes) => [
    //         'user_id' => $user_id
    //     ]);
    // }

    public function setSubscriptionPlanId(int $subscription_plan_id)
    {
        return $this->state(fn(array $attributes) => [
            'subscription_plan_id' => $subscription_plan_id
        ]);
    }
}
