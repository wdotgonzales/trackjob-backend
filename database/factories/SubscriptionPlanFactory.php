<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SubscriptionPlan;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;
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

    public function setPlanName(string $plan_name)
    {
        return $this->state(fn(array $attributes) => [
            'plan_name' => $plan_name
        ]);
    }

    public function setPrice(float $price)
    {
        return $this->state(fn(array $attributes) => [
            'price' => $price
        ]);
    }

    public function setDurationDays(int $duration_days)
    {
        return $this->state(fn(array $attributes) => [
            'duration_days' => $duration_days
        ]);
    }


}
