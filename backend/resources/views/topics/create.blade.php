@extends('layouts.app')

@section('title', '发布主题')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">发布主题</h1>
    
    <div class="card">
        <form method="POST" action="{{ route('topics.store') }}">
            @csrf
            
            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-medium mb-2">标题</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="input-field @error('title') border-red-500 @enderror"
                       placeholder="请输入主题标题" autofocus>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="category" class="block text-gray-700 text-sm font-medium mb-2">分类</label>
                <select id="category" name="category" required
                        class="input-field @error('category') border-red-500 @enderror">
                    <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>综合讨论</option>
                    <option value="tech" {{ old('category') == 'tech' ? 'selected' : '' }}>技术交流</option>
                    <option value="study" {{ old('category') == 'study' ? 'selected' : '' }}>学习心得</option>
                    <option value="question" {{ old('category') == 'question' ? 'selected' : '' }}>问题求助</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="content" class="block text-gray-700 text-sm font-medium mb-2">内容</label>
                <textarea id="content" name="content" rows="12" required
                          class="input-field @error('content') border-red-500 @enderror"
                          placeholder="请输入主题内容...">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="text-sm font-medium text-amber-800 mb-3">
                    📢 发布选项
                </div>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="is_charity" name="is_charity" value="1" class="w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500" {{ old('is_charity') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">
                            <span class="text-red-500">❤️</span> 这是一个公益活动（发布可获得积分）
                        </span>
                    </label>
                </div>
                <div class="mt-3 text-xs text-amber-600">
                    <p>• 积分等级影响发帖频率：Lv1（新人）30分钟可发1帖，Lv2（居民）15分钟可发1帖，Lv3+无限制</p>
                    <p>• 广告帖会被扣除30积分，请遵守社区规范</p>
                    <p>• 您当前等级：Lv{{ auth()->user()->level }} {{ auth()->user()->level_name }}，积分：{{ auth()->user()->points }}</p>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="btn-primary">发布</button>
                <a href="{{ route('topics.index') }}" class="btn-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
@endsection
