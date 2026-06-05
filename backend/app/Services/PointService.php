<?php

namespace App\Services;

use App\Models\PointLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointService
{
    public static function addPoints(
        User $user,
        int $points,
        string $type,
        string $description,
        ?int $relatedId = null,
        ?string $relatedType = null
    ): PointLog {
        return DB::transaction(function () use ($user, $points, $type, $description, $relatedId, $relatedType) {
            $user->increment('points', $points);
            $user->level = LevelService::getLevelByPoints($user->points);
            $user->save();

            $log = PointLog::create([
                'user_id' => $user->id,
                'points' => $points,
                'type' => $type,
                'description' => $description,
                'related_id' => $relatedId,
                'related_type' => $relatedType,
            ]);

            BadgeService::checkAndAward($user);

            return $log;
        });
    }

    public static function deductPoints(
        User $user,
        int $points,
        string $type,
        string $description,
        ?int $relatedId = null,
        ?string $relatedType = null
    ): PointLog {
        return self::addPoints($user, -abs($points), $type, $description, $relatedId, $relatedType);
    }

    public static function getPointLogs(User $user, int $perPage = 20)
    {
        return PointLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
