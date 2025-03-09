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
            VerificationCode::factory()
                ->for($user) // Associate VerificationCode with User
                ->setExpirationForVerificationCode() // Set expiration date
                ->create(); // Persist to database

        });
    }
}
