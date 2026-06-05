<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'icon',
        'description',
        'source_type',
        'source_value',
        'sort',
    ];

    const SOURCE_REGISTER = 'register';

    const SOURCE_ANSWERS = 'answers';

    const SOURCE_ADOPTS = 'adopts';

    const SOURCE_CHARITY = 'charity';

    const SOURCE_TOPICS = 'topics';

    const SOURCE_POINTS = 'points';

    const SOURCE_DURATION = 'duration';

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function getSourceTypeTextAttribute()
    {
        return match ($this->source_type) {
            self::SOURCE_REGISTER => '注册',
            self::SOURCE_ANSWERS => '累计回答',
            self::SOURCE_ADOPTS => '累计采纳',
            self::SOURCE_CHARITY => '公益活动',
            self::SOURCE_TOPICS => '累计发帖',
            self::SOURCE_POINTS => '累计积分',
            self::SOURCE_DURATION => '注册时长',
            default => $this->source_type,
        };
    }

    public function getConditionTextAttribute()
    {
        return match ($this->source_type) {
            self::SOURCE_REGISTER => '完成注册',
            self::SOURCE_ANSWERS => "累计回答 {$this->source_value} 个问题",
            self::SOURCE_ADOPTS => "累计 {$this->source_value} 个答案被采纳",
            self::SOURCE_CHARITY => "参与 {$this->source_value} 次公益活动",
            self::SOURCE_TOPICS => "累计发布 {$this->source_value} 个帖子",
            self::SOURCE_POINTS => "累计获得 {$this->source_value} 积分",
            self::SOURCE_DURATION => "注册满 {$this->source_value} 天",
            default => '',
        };
    }
}
