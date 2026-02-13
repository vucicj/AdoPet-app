<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'breed',
        'age',
        'gender',
        'location',
        'distance',
        'image',
        'shelter_id',
        'adopted_by_user_id',
        'status'
    ];

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shelter_id');
    }

    public function adoptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adopted_by_user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
