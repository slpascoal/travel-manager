<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Travel",
 *     type="object",
 *     required={"applicant_name", "destiny", "departure_date", "return_date", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="applicant_name", type="string", example="Traveler"),
 *     @OA\Property(property="destiny", type="string", example="Paris"),
 *     @OA\Property(property="departure_date", type="string", format="date", example="2024-11-15"),
 *     @OA\Property(property="return_date", type="string", format="date", example="2024-11-20"),
 *     @OA\Property(property="status", type="string", enum={"requested", "approved", "canceled"}, example="requested"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-09T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-09T00:00:00Z")
 * )
 */

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
