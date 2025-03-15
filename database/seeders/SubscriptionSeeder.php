<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 10; $i++) {
            $user = User::factory()->create();

            $randomSubscriptionPlanId = random_int(1, 3);
            Subscription::factory()
                ->for($user)
                ->setSubscriptionPlanId($randomSubscriptionPlanId)
                ->create();
        }
    }
}
