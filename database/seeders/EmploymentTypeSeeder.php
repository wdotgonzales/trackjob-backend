<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmploymentType;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employmentTypeArr = [
            [
                "title" => "Full-time",
                "description" => "Employees working a standard number of hours per week (e.g., 40 hours)."
            ],

            [
                "title" => "Part-time",
                "description" => "Employees working fewer hours than full-time, often with fewer benefits."
            ],

            [
                "title" => "Contract",
                "description" => "Employees hired for a specific period or project, often without full employee benefits."
            ],

            [
                "title" => "Freelance",
                "description" => "Self-employed individuals who work on a project basis without being tied to a company."
            ],


            [
                "title" => "Internship",
                "description" => "Temporary employment, often for students or recent graduates, typically for learning and experience."
            ],

            [
                "title" => "Temporary",
                "description" => "Employees hired for short-term needs, sometimes through staffing agencies."
            ],
        ];

        foreach ($employmentTypeArr as $employmentType) {
            EmploymentType::factory()->setTitle($employmentType['title'])->setDescription($employmentType['description'])->create();

        }
    }
}
