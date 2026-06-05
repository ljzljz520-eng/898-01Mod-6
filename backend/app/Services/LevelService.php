<?php

namespace App\Services;

use App\Models\User;

class LevelService
{
    const POINTS_ANSWER = 5;

    const POINTS_ADOPT = 15;

    const POINTS_CHARITY = 20;

    const POINTS_AD = -30;

    const POINTS_QUARREL = -20;

    public static function getLevelByPoints(int $points): int
    {
        return match (true) {
            $points >= 5000 => 7,
            $points >= 2000 => 6,
            $points >= 1000 => 5,
            $points >= 600 => 4,
            $points >= 300 => 3,
            $points >= 100 => 2,
            default => 1,
        };
    }

    public static function getLevelName(int $level): string
    {
        return match ($level) {
            7 => '社区元老',
            6 => '社区领袖',
            5 => '社区之星',
            4 => '骨干居民',
            3 => '活跃居民',
            2 => '居民',
            default => '新人',
        };
    }

    public static function getPostIntervalMinutes(int $level): int
    {
        return match ($level) {
            1 => 30,
            2 => 15,
            default => 0,
        };
    }

    public static function getActivityPriority(int $points): int
    {
        return (int) ($points / 100);
    }

    public static function getNextLevelPoints(int $points): array
    {
        $level = self::getLevelByPoints($points);
        $nextLevelPoints = match ($level) {
            1 => 100,
            2 => 300,
            3 => 600,
            4 => 1000,
            5 => 2000,
            6 => 5000,
            default => null,
        };

        return [
            'current_level' => $level,
            'current_level_name' => self::getLevelName($level),
            'next_level' => $level < 7 ? $level + 1 : null,
            'next_level_name' => $level < 7 ? self::getLevelName($level + 1) : null,
            'next_level_points' => $nextLevelPoints,
            'points_needed' => $nextLevelPoints ? max(0, $nextLevelPoints - $points) : 0,
            'progress_percent' => $nextLevelPoints ? (int) (($points / $nextLevelPoints) * 100) : 100,
        ];
    }

    public static function canPost(User $user): bool
    {
        $interval = self::getPostIntervalMinutes($user->level);
        if ($interval === 0) {
            return true;
        }

        $lastPost = $user->topics()
            ->where('created_at', '>', now()->subMinutes($interval))
            ->exists();

        return ! $lastPost;
    }
}
