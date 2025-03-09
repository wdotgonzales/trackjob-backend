<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkArrangement extends Model
{
    public function jobApplication(){
        return $this->hasMany(JobApplication::class);
    }
}
