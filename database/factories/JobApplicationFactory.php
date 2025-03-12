<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JobApplication;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'position_title' => $this->faker->jobTitle,
            'company_name' => $this->faker->company,
            'job_posting_link' => $this->faker->url,
            'date_applied' => $this->faker->date(),
            'company_logo_url' => $this->faker->imageUrl(100, 100, 'business', true),
            'job_location' => json_encode([
                'lat' => $this->faker->latitude(-90, 90),
                'lng' => $this->faker->longitude(-180, 180)
            ])
        ];
    }

    public function generateRandomEmployementId()
    {
        $random_id = random_int(1, 6);
        return $this->state(fn(array $attributes) => [
            'employment_type_id' => $random_id
        ]);
    }

    public function generateJobApplicationStatus()
    {
        $random_id = random_int(1, 8);
        return $this->state(fn(array $attributes) => [
            'job_application_status_id' => $random_id
        ]);
    }

    public function generateRandomWorkArrangementId()
    {
        $random_id = random_int(1, 5);
        return $this->state(fn(array $attributes) => [
            'work_arragement_id' => $random_id
        ]);
    }
}
