<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobApplicationStatus;

class JobApplicationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /* ----- PHASE 2 ----- */
        $jobApplicationStatusArr = [
            [
                "title" => "Applied",
                "description" => "You have submitted your application, but no further updates yet."
            ],
            [
                "title" => "Rejected",
                "description" => "You have submitted your application, but no further updates yet."
            ],
            [
                "title" => "Ghosted",
                "description" => "The employer has stopped responding without providing any updates."
            ],
            [
                "title" => "Under Review",
                "description" => "Your application is being assessed by the hiring team."
            ],
            [
                "title" => "Offer Received",
                "description" => "The company has extended a job offer to you."
            ],
            [
                "title" => "Interview Scheduled",
                "description" => "You have been invited for an interview, and a date is set."
            ],
            [
                "title" => "Withdrawn/Closed",
                "description" => "The job listing is no longer active, or you have withdrawn your application."
            ],
            [
                "title" => "Accepted Offer",
                "description" => "You have accepted a job offer and will be joining the company."
            ],
        ];

        foreach ($jobApplicationStatusArr as $jobApplicationStatus) {
            JobApplicationStatus::factory()->setTitle($jobApplicationStatus['title'])->setDescription($jobApplicationStatus['description'])->create();
        }
    }
}
