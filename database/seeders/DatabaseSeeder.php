<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VerificationCode;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create(); // Creates a new user
        
            VerificationCode::factory()
                ->insertUserId($user->id) // Make sure this method returns $this for chaining
                ->generateRandomOtp()
                ->create(); // Ensure the OTP record is saved
        }
    }
}
