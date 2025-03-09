<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    public function user() : BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function employmentType(){
        return $this->belongsTo(EmploymentType::class);
    }
}
