<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'otp',
        'expiration_date',
        'start_date'
    ];

    // protected $table = 'verification_codes';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    //

}
