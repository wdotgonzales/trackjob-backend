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
                "title" => "On-site",
                "description" => "Employees work at the company's physical location."
            ],

            [
                "title" => "Remote",
                "description" => "Employees work from home or another location outside the office."
            ],

            [
                "title" => "Hybrid",
                "description" => "A mix of remote and on-site work."
            ],

            [
                "title" => "Flexible Hours",
                "description" => "Employees choose when they start and end their workday within agreed-upon limits."
            ],

            [
                "title" => "Shift Work",
                "description" => "Employees work in rotating shifts, such as night shifts or split shifts."
            ],
        ];
        
        foreach ($workArrangementArr as $workArrangement) {
            WorkArrangement::factory()->setTitle($workArrangement['title'])->setDescription($workArrangement['description'])->create();
        }
    }
}
