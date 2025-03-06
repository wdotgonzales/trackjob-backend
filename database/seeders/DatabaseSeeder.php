<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(7)->create()->each(function ($user) {
            $random_subscription_plan_id = random_int(1, 3);

            VerificationCode::factory()
                ->for($user) // Associate VerificationCode with User
                ->setSubscriptionDate($random_subscription_plan_id) // Set expiration date
                ->create(); // Persist to database
        });
    }
}
