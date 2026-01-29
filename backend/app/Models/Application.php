<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'status',
        'applied_at',
        'full_name',
        'email',
        'phone_number',
        'age',
        'residence_type',
        'own_or_rent',
        'has_yard',
        'owned_pets_before',
        'has_other_pets',
        'other_pets_details',
        'hours_alone',
        'adoption_reason',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
