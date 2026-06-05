<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\PointLog;
use App\Models\Reply;
use App\Models\Topic;
use App\Services\LevelService;
use App\Services\PointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Topic::with('user')
            ->where('status', 1)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $topics = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $topics->items(),
            'meta' => [
                'current_page' => $topics->currentPage(),
                'per_page' => $topics->perPage(),
                'total' => $topics->total(),
                'last_page' => $topics->lastPage(),
            ],
        ]);
    }

    public function show(Topic $topic): JsonResponse
    {
        $topic->increment('view_count');
        $topic->load(['user', 'replies.user', 'bestReply']);

        return response()->json([
            'data' => $topic,
        ]);
    }

    public function store(TopicRequest $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => '未认证',
            ], 401);
        }

        if (! $user->canPost()) {
            return response()->json([
                'message' => "发帖过于频繁，请等待 {$user->post_interval_minutes} 分钟后再试",
            ], 429);
        }

        $isCharity = $request->boolean('is_charity', false);
        $isAd = $request->boolean('is_ad', false);

        $topic = Topic::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category ?? 'general',
            'is_charity' => $isCharity,
            'is_ad' => $isAd,
        ]);

        $topic->load('user');

        if ($isCharity) {
            PointService::addPoints(
                $user,
                LevelService::POINTS_CHARITY,
                PointLog::TYPE_CHARITY,
                "发布公益活动：{$topic->title}",
                $topic->id,
                'topic'
            );
        }

        if ($isAd) {
            PointService::deductPoints(
                $user,
                abs(LevelService::POINTS_AD),
                PointLog::TYPE_AD,
                "发布广告帖：{$topic->title}",
                $topic->id,
                'topic'
            );
        }

        return response()->json([
            'data' => $topic,
            'message' => '发布成功',
        ], 201);
    }

    public function update(TopicRequest $request, Topic $topic): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => '未认证',
            ], 401);
        }

        if ($topic->user_id !== $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => '无权限操作',
            ], 403);
        }

        $topic->update([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category ?? $topic->category,
        ]);

        $topic->load('user');

        return response()->json([
            'data' => $topic,
            'message' => '更新成功',
        ]);
    }

    public function destroy(Request $request, Topic $topic): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => '未认证',
            ], 401);
        }

        if ($topic->user_id !== $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => '无权限操作',
            ], 403);
        }

        $topic->delete();

        return response()->json([
            'message' => '删除成功',
        ]);
    }

    public function setBestReply(Request $request, Topic $topic, Reply $reply): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => '未认证',
            ], 401);
        }

        if ($topic->user_id !== $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => '无权限操作',
            ], 403);
        }

        if ($reply->topic_id !== $topic->id) {
            return response()->json([
                'message' => '回复不属于该帖子',
            ], 400);
        }

        if ($topic->best_reply_id) {
            return response()->json([
                'message' => '已设置最佳回复',
            ], 400);
        }

        if ($reply->user_id === $user->id) {
            return response()->json([
                'message' => '不能采纳自己的回复',
            ], 400);
        }

        $topic->update([
            'best_reply_id' => $reply->id,
            'best_reply_set_at' => now(),
        ]);

        $replyUser = $reply->user;
        PointService::addPoints(
            $replyUser,
            LevelService::POINTS_ADOPT,
            PointLog::TYPE_ADOPT,
            "在帖子「{$topic->title}」中的回答被采纳为最佳答案",
            $reply->id,
            'reply'
        );

        return response()->json([
            'message' => '设置成功',
        ]);
    }

    public function markAsAd(Request $request, Topic $topic): JsonResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isAdmin()) {
            return response()->json([
                'message' => '无权限操作',
            ], 403);
        }

        if ($topic->is_ad) {
            return response()->json([
                'message' => '该帖已标记为广告',
            ], 400);
        }

        $topic->update(['is_ad' => true]);

        $topicUser = $topic->user;
        PointService::deductPoints(
            $topicUser,
            abs(LevelService::POINTS_AD),
            PointLog::TYPE_AD,
            "帖子「{$topic->title}」被管理员标记为广告帖",
            $topic->id,
            'topic'
        );

        return response()->json([
            'message' => '已标记为广告帖并扣除积分',
        ]);
    }

    public function markAsQuarrel(Request $request, Topic $topic): JsonResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isAdmin()) {
            return response()->json([
                'message' => '无权限操作',
            ], 403);
        }

        $topicUser = $topic->user;
        PointService::deductPoints(
            $topicUser,
            abs(LevelService::POINTS_QUARREL),
            PointLog::TYPE_QUARREL,
            "帖子「{$topic->title}」因恶意争吵被管理员处罚",
            $topic->id,
            'topic'
        );

        foreach ($topic->replies as $reply) {
            if ($reply->user_id !== $topicUser->id) {
                PointService::deductPoints(
                    $reply->user,
                    abs(LevelService::POINTS_QUARREL),
                    PointLog::TYPE_QUARREL,
                    "在帖子「{$topic->title}」中参与恶意争吵被管理员处罚",
                    $reply->id,
                    'reply'
                );
            }
        }

        $topic->update(['status' => 0]);

        return response()->json([
            'message' => '已处罚恶意争吵并扣除积分',
        ]);
    }

    public function joinCharity(Request $request, Topic $topic): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => '未认证',
            ], 401);
        }

        if (! $topic->is_charity) {
            return response()->json([
                'message' => '该帖子不是公益活动',
            ], 400);
        }

        $hasJoined = $user->replies()
            ->where('topic_id', $topic->id)
            ->where('content', 'like', '%报名%')
            ->exists();

        if ($hasJoined) {
            return response()->json([
                'message' => '您已报名该公益活动',
            ], 400);
        }

        Reply::create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'content' => '报名参加公益活动',
        ]);

        $topic->increment('reply_count');

        PointService::addPoints(
            $user,
            LevelService::POINTS_CHARITY,
            PointLog::TYPE_CHARITY,
            "报名参与公益活动：{$topic->title}",
            $topic->id,
            'topic'
        );

        return response()->json([
            'message' => '报名成功，积分已发放',
            'priority' => $user->activity_priority,
        ]);
    }
}
