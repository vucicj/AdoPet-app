<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'status'
    ];

    public function shelter()
    {
        return $this->belongsTo(User::class, 'shelter_id');
    }
}
