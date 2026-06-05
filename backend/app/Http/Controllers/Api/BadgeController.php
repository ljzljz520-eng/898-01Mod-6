<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index(): JsonResponse
    {
        $badges = BadgeService::getAllBadges();

        return response()->json([
            'data' => $badges,
        ]);
    }

    public function userBadges(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => '未认证',
            ], 401);
        }

        $userBadges = BadgeService::getUserBadges($user);

        return response()->json([
            'data' => $userBadges,
        ]);
    }
}
