<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptionPlans = [
            [
                "plan_name" => "1 Month Subscription",
                "price" => 120.00,
                "duration_days" => 30
            ],
            [
                "plan_name" => "4 Month Subscription",
                "price" => 320.00,
                "duration_days" => 120
            ],
            [
                "plan_name" => "10 Month Subscription",
                "price" => 500.00,
                "duration_days" => 300
            ],
        ];

        foreach ($subscriptionPlans as $subscriptionPlan) {
            SubscriptionPlan::factory()
                ->setPlanName($subscriptionPlan['plan_name'])
                ->setPrice($subscriptionPlan['price'])
                ->setDurationDays($subscriptionPlan['duration_days'])
                ->create();
        }
    }
}
