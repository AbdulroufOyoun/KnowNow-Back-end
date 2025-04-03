<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'university_id',
        'poster',
        'is_active',
        'doctor_id',
        'ratio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function Doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    public function University(): BelongsTo
    {
        return $this->belongsTo(university::class, 'university_id');
    }
}
