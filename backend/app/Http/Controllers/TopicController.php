<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\PointLog;
use App\Models\Reply;
use App\Models\Topic;
use App\Services\LevelService;
use App\Services\PointService;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Request $request)
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

        $topics = $query->paginate(20)->appends(request()->query());

        return view('topics.index', compact('topics'));
    }

    public function show(Topic $topic)
    {
        $topic->increment('view_count');
        $topic->load(['user', 'replies' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }, 'replies.user', 'bestReply']);

        return view('topics.show', compact('topic'));
    }

    public function create()
    {
        return view('topics.create');
    }

    public function store(TopicRequest $request)
    {
        $user = auth()->user();

        if (! $user->canPost()) {
            return back()->with('error', "发帖过于频繁，请等待 {$user->post_interval_minutes} 分钟后再试")->withInput();
        }

        $isCharity = $request->boolean('is_charity', false);

        $topic = Topic::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category ?? 'general',
            'is_charity' => $isCharity,
        ]);

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

        return redirect()->route('topics.show', $topic)->with('success', '发布成功');
    }

    public function edit(Topic $topic)
    {
        if ($topic->user_id !== auth()->id()) {
            abort(403, '无权限操作');
        }

        return view('topics.edit', compact('topic'));
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        if ($topic->user_id !== auth()->id()) {
            abort(403, '无权限操作');
        }

        $topic->update([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category ?? $topic->category,
        ]);

        return redirect()->route('topics.show', $topic)->with('success', '更新成功');
    }

    public function destroy(Topic $topic)
    {
        if ($topic->user_id !== auth()->id()) {
            abort(403, '无权限操作');
        }

        $topic->delete();

        return redirect()->route('topics.index')->with('success', '删除成功');
    }

    public function setBestReply(Request $request, Topic $topic, Reply $reply)
    {
        $user = auth()->user();

        if ($topic->user_id !== $user->id) {
            abort(403, '无权限操作');
        }

        if ($reply->topic_id !== $topic->id) {
            return back()->with('error', '回复不属于该帖子');
        }

        if ($topic->best_reply_id) {
            return back()->with('error', '已设置最佳回复');
        }

        if ($reply->user_id === $user->id) {
            return back()->with('error', '不能采纳自己的回复');
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

        return back()->with('success', '已采纳为最佳答案，+15 积分');
    }

    public function joinCharity(Request $request, Topic $topic)
    {
        $user = auth()->user();

        if (! $topic->is_charity) {
            return back()->with('error', '该帖子不是公益活动');
        }

        $hasJoined = $user->replies()
            ->where('topic_id', $topic->id)
            ->where('content', 'like', '%报名%')
            ->exists();

        if ($hasJoined) {
            return back()->with('error', '您已报名该公益活动');
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

        return back()->with('success', "报名成功，+20 积分！您的报名优先级：{$user->activity_priority} 级");
    }
}
