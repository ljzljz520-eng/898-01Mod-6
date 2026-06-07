<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '学习交流论坛') - 学习交流论坛</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-50 flex flex-col">
    <nav class="bg-white border-b border-neutral-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('topics.index') }}" class="flex items-center space-x-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded bg-primary-600 text-white text-lg font-bold">
                            学
                        </span>
                        <span class="text-lg font-semibold text-neutral-800">
                            学习交流论坛
                        </span>
                    </a>
                    <div class="hidden md:flex items-center space-x-4 text-sm">
                        <a href="{{ route('topics.index') }}" class="text-neutral-600 hover:text-primary-600">
                            讨论
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    @auth
                        @php
                            $authUser = auth()->user();
                            $authUser->load('badges');
                        @endphp
                        <a href="{{ route('topics.create') }}" class="btn-primary">
                            发布主题
                        </a>
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-neutral-700 hover:text-neutral-900 cursor-pointer">
                                <span class="font-medium">{{ $authUser->username }}</span>
                                <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs px-2 py-0.5 rounded-full">
                                    Lv{{ $authUser->level }} {{ $authUser->level_name }}
                                </span>
                                <span class="text-amber-600 font-semibold">
                                    ⭐ {{ $authUser->points }}
                                </span>
                                <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute right-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border border-neutral-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="p-4 border-b border-neutral-100">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-lg">
                                            {{ mb_substr($authUser->username, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-semibold text-neutral-800">{{ $authUser->username }}</div>
                                            <div class="text-sm text-neutral-500">
                                                Lv{{ $authUser->level }} · {{ $authUser->level_name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-neutral-50 rounded-lg p-3 mb-3">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-neutral-600">当前积分</span>
                                            <span class="font-semibold text-amber-600">{{ $authUser->points }} 分</span>
                                        </div>
                                        @if($authUser->level_progress['next_level'])
                                            <div class="flex justify-between text-xs text-neutral-500 mb-2">
                                                <span>距离 Lv{{ $authUser->level_progress['next_level'] }} {{ $authUser->level_progress['next_level_name'] }}</span>
                                                <span>还需 {{ $authUser->level_progress['points_needed'] }} 分</span>
                                            </div>
                                            <div class="w-full bg-neutral-200 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $authUser->level_progress['progress_percent']) }}%"></div>
                                            </div>
                                        @else
                                            <div class="text-xs text-green-600 font-medium">🎉 已达到最高等级！</div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-neutral-500 space-y-1">
                                        <div class="flex justify-between">
                                            <span>发帖间隔</span>
                                            <span class="font-medium">{{ $authUser->post_interval_minutes > 0 ? $authUser->post_interval_minutes.' 分钟' : '无限制' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>活动报名优先级</span>
                                            <span class="font-medium">{{ $authUser->activity_priority }} 级</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="text-sm font-medium text-neutral-700 mb-2">获得的徽章</div>
                                    @if($authUser->badges->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($authUser->badges as $badge)
                                                <div class="group/badge relative">
                                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 flex items-center justify-center text-xl cursor-help" title="{{ $badge->name }}">
                                                        {{ $badge->icon }}
                                                    </div>
                                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 bg-neutral-800 text-white text-xs rounded-lg p-2 opacity-0 invisible group-hover/badge:opacity-100 group-hover/badge:visible transition-all duration-200 pointer-events-none z-50">
                                                        <div class="font-medium mb-1">{{ $badge->icon }} {{ $badge->name }}</div>
                                                        <div class="text-neutral-300 text-[10px] mb-1">{{ $badge->description }}</div>
                                                        <div class="text-amber-300 text-[10px]">{{ $badge->pivot->source_proof }}</div>
                                                        <div class="absolute left-1/2 -translate-x-1/2 top-full border-4 border-transparent border-t-neutral-800"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-xs text-neutral-400 text-center py-2">暂无徽章，多参与社区活动获得吧～</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-neutral-500 hover:text-neutral-800">登出</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-neutral-500 hover:text-neutral-800">登录</a>
                        <a href="{{ route('register') }}" class="btn-primary">注册</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 w-full">
        <div class="min-w-0">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative transition-opacity duration-200" role="alert" data-auto-dismiss="3000">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative transition-opacity duration-200" role="alert" data-auto-dismiss="3000">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative transition-opacity duration-200" role="alert" data-auto-dismiss="3000">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-xs text-neutral-500">© 2024 学习交流论坛 · Inspired by SegmentFault UI</p>
        </div>
    </footer>
</body>
</html>
