<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\JobApplication;
use App\Models\Reminder;

class JobApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(15)->create()->each(function ($user) {
            $numOfJobApplication = random_int(5, 20);
            JobApplication::factory()->count($numOfJobApplication)
                ->for($user)
                ->generateRandomEmployementId()
                ->generateJobApplicationStatus()
                ->generateRandomWorkArrangementId()
                ->create()->each(function ($jobApplication) {
                    $numOfReminder = random_int(2, 5);
                    Reminder::factory()
                        ->count($numOfReminder)
                        ->for($jobApplication)
                        ->create();
                });
        });
    }
}
