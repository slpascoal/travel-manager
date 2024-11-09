<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_name',
        'destiny',
        'departure_date',
        'return_date',
        'status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
