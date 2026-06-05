<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'view_count',
        'reply_count',
        'is_pinned',
        'status',
        'best_reply_id',
        'best_reply_set_at',
        'is_charity',
        'is_ad',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'status' => 'integer',
        'view_count' => 'integer',
        'reply_count' => 'integer',
        'best_reply_id' => 'integer',
        'best_reply_set_at' => 'datetime',
        'is_charity' => 'boolean',
        'is_ad' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function bestReply()
    {
        return $this->belongsTo(Reply::class, 'best_reply_id');
    }

    public function isQuestion()
    {
        return $this->category === 'question';
    }
}
