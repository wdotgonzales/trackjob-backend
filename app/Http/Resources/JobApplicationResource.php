<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\JobApplicationStatusResource;
use App\Http\Resources\EmploymentTypeResource;
use App\Http\Resources\WorkArrangementResource;
use App\Http\Resources\ReminderResource;
class JobApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'position_title' => $this->position_title,
            'company_name' => $this->company_name,
            'job_posting_link' => $this->job_posting_link,
            'date_applied' => $this->date_applied,
            'company_logo_url' => $this->company_logo_url,
            'job_location' => json_decode($this->job_location),
            'job_application_status' => new JobApplicationStatusResource($this->whenLoaded('jobApplicationStatus')),
            'employment_type' => new EmploymentTypeResource($this->whenLoaded('employmentType')),
            'work_arrangement' => new WorkArrangementResource($this->whenLoaded('workArrangement')),
            'reminders' => new ReminderResource($this->whenLoaded('reminder')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
