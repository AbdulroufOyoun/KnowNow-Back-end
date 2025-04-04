<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Collection extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_active',
        'price',
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
    public function Courses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Course::class,          // The final model you want to retrieve
            CourseCollection::class, // The intermediate model
            'collection_id',         // Foreign key on the `CourseCollection` table
            'id',                    // Foreign key on the `Course` table
            'id',                    // Local key on the `Collection` table
            'course_id'              // Local key on the `CourseCollection` table
        );
    }
}
