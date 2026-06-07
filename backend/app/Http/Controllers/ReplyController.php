<?php

namespace App\Http\Controllers;

use App\Models\PointLog;
use App\Models\Reply;
use App\Models\Topic;
use App\Services\LevelService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Topic $topic)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:5|max:5000',
        ], [
            'content.required' => '回复内容不能为空',
            'content.min' => '回复内容至少5个字符',
            'content.max' => '回复内容最多5000个字符',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $content = $request->content;

        $reply = Reply::create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'content' => $content,
        ]);

        $topic->increment('reply_count');

        if ($topic->isQuestion() && $user->id !== $topic->user_id) {
            PointService::addPoints(
                $user,
                LevelService::POINTS_ANSWER,
                PointLog::TYPE_ANSWER,
                "回答问题：{$topic->title}",
                $reply->id,
                'reply'
            );
        }

        return back()->with('success', '回复成功');
    }

    public function destroy(Reply $reply)
    {
        if ($reply->user_id !== auth()->id()) {
            abort(403, '无权限操作');
        }

        $topic = $reply->topic;
        $reply->delete();
        $topic->decrement('reply_count');

        return back()->with('success', '删除成功');
    }
}
