<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    protected $appends = [
        'is_best',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsBestAttribute()
    {
        return $this->topic && $this->topic->best_reply_id === $this->id;
    }
}
