<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicationStatus extends Model
{
    public function jobApplication(){
        return $this->hasMany(JobApplication::class);
    }
}
