<?php

namespace App\Models;

use App\Services\LevelService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'role',
        'status',
        'points',
        'level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'points' => 'integer',
        'level' => 'integer',
    ];

    protected $appends = [
        'level_name',
        'level_progress',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function pointLogs()
    {
        return $this->hasMany(PointLog::class);
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('source_proof', 'awarded_at')
            ->withTimestamps('awarded_at');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getLevelNameAttribute()
    {
        return LevelService::getLevelName($this->level);
    }

    public function getLevelProgressAttribute()
    {
        return LevelService::getNextLevelPoints($this->points);
    }

    public function canPost()
    {
        return LevelService::canPost($this);
    }

    public function getPostIntervalMinutesAttribute()
    {
        return LevelService::getPostIntervalMinutes($this->level);
    }

    public function getActivityPriorityAttribute()
    {
        return LevelService::getActivityPriority($this->points);
    }
}
