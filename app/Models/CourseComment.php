<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseComment extends Model
{
    use HasFactory;
    // protected $appends = ['subComments'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'comment',
        'user_id',
        'sub_comment',
        'video_id',
        'comment_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'updated_at'
    ];

    public function User(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }
    public function SubComments(): HasMany
    {
        return $this->hasMany(CourseComment::class, 'comment_id');
    }
}