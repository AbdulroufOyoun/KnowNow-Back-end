<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;


class CourseCode extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'code',
        'is_free',
        'price',
        'expire_at',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    public function Course(): BelongsTo
    {
        return $this->belongsTo(course::class, 'course_id');
    }
        public function CreatedBy(): BelongsTo
    {
        return $this->belongsTo(user::class, 'created_by');
    }
    public function userCode(): HasOne
{
    return $this->hasOne(UserCode::class, 'course_code_id');
}

    public function user(): HasOneThrough
    {

    return $this->hasOneThrough(
        User::class,
        UserCode::class,
        'course_code_id', // Foreign key on UserCode
        'id',             // Foreign key on User
        'id',             // Local key on CourseCode
        'user_id'         // Local key on UserCode
    );
}


}
