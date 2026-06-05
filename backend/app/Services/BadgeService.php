<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    public static function checkAndAward(User $user): array
    {
        $awarded = [];
        $badges = Badge::all();

        foreach ($badges as $badge) {
            if ($user->badges->contains($badge->id)) {
                continue;
            }

            if (self::meetsCondition($user, $badge)) {
                $awarded[] = self::awardBadge($user, $badge);
            }
        }

        return $awarded;
    }

    private static function meetsCondition(User $user, Badge $badge): bool
    {
        return match ($badge->source_type) {
            Badge::SOURCE_REGISTER => true,
            Badge::SOURCE_ANSWERS => $user->replies()->count() >= $badge->source_value,
            Badge::SOURCE_ADOPTS => $user->replies()
                ->whereHas('topic', function ($q) {
                    $q->whereColumn('best_reply_id', 'replies.id');
                })
                ->count() >= $badge->source_value,
            Badge::SOURCE_CHARITY => $user->topics()->where('is_charity', true)->count() >= $badge->source_value,
            Badge::SOURCE_TOPICS => $user->topics()->count() >= $badge->source_value,
            Badge::SOURCE_POINTS => $user->points >= $badge->source_value,
            Badge::SOURCE_DURATION => $user->created_at->diffInDays() >= $badge->source_value,
            default => false,
        };
    }

    private static function awardBadge(User $user, Badge $badge): UserBadge
    {
        return DB::transaction(function () use ($user, $badge) {
            return UserBadge::create([
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'source_proof' => self::generateSourceProof($user, $badge),
                'awarded_at' => now(),
            ]);
        });
    }

    private static function generateSourceProof(User $user, Badge $badge): string
    {
        $proof = match ($badge->source_type) {
            Badge::SOURCE_REGISTER => "用户完成注册，账号于 {$user->created_at->toDateString()}",
            Badge::SOURCE_ANSWERS => "累计回答 {$user->replies()->count()} 个问题",
            Badge::SOURCE_ADOPTS => "累计 {$user->replies()->whereHas('topic', function ($q) {
                $q->whereColumn('best_reply_id', 'replies.id');
            })->count()} 个答案被采纳",
            Badge::SOURCE_CHARITY => "参与 {$user->topics()->where('is_charity', true)->count()} 次公益活动",
            Badge::SOURCE_TOPICS => "累计发布 {$user->topics()->count()} 个帖子",
            Badge::SOURCE_POINTS => "累计获得 {$user->points} 积分",
            Badge::SOURCE_DURATION => "注册时长已达 {$user->created_at->diffInDays()} 天",
            default => '',
        };

        return "来源：{$badge->condition_text} - {$proof}";
    }

    public static function getUserBadges(User $user)
    {
        return UserBadge::with('badge')
            ->where('user_id', $user->id)
            ->orderBy('awarded_at', 'desc')
            ->get();
    }

    public static function getAllBadges()
    {
        return Badge::orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }
}
