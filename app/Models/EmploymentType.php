<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmploymentType extends Model
{
    use HasFactory;
    public function jobApplication(){
        return $this->hasMany(JobApplication::class);
    }
}
