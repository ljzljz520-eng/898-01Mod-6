<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'related_id',
        'related_type',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    const TYPE_ANSWER = 'answer';

    const TYPE_ADOPT = 'adopt';

    const TYPE_CHARITY = 'charity';

    const TYPE_AD = 'ad';

    const TYPE_QUARREL = 'quarrel';

    const TYPE_OTHER = 'other';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function getTypeTextAttribute()
    {
        return match ($this->type) {
            self::TYPE_ANSWER => '回答问题',
            self::TYPE_ADOPT => '答案被采纳',
            self::TYPE_CHARITY => '参与公益',
            self::TYPE_AD => '广告扣分',
            self::TYPE_QUARREL => '恶意争吵扣分',
            self::TYPE_OTHER => '其他',
            default => $this->type,
        };
    }
}
