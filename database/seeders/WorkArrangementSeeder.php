<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkArrangement;
class WorkArrangementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workArrangementArr = [
            [
                "title" => "Internship",
                "description" => "Temporary employment, often for students or recent graduates, typically for learning and experience."
            ],

            [
                "title" => "Temporary",
                "description" => "Employees hired for short-term needs, sometimes through staffing agencies."
            ],
        ];
        
        foreach ($workArrangementArr as $workArrangement) {
            WorkArrangement::factory()->setTitle($workArrangement['title'])->setDescription($workArrangement['description'])->create();
        }
    }
}
