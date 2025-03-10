<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplication extends Model
{
    use HasFactory;
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function workArrangement(): BelongsTo
    {
        return $this->belongsTo(WorkArrangement::class);
    }

    public function jobApplicationStatus():BelongsTo {
        return $this->belongsTo(JobApplicationStatus::class);
    }
}
