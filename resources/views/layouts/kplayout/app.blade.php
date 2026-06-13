<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'KP Portal | eCourse Folder')</title>
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> 
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 font-sans" x-data="{ notificationsOpen: false }">

    <div class="flex">
        <aside class="w-72 bg-slate-900 shadow-2xl h-screen hidden lg:flex flex-col sticky top-0 border-r border-slate-800 z-40">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <h1 class="text-xl font-black text-white tracking-tighter italic">eCourse <span class="text-emerald-400">Folder</span></h1>
                </div>

                <nav class="space-y-2">
                    <a href="{{ url('kp/dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::is('kp/dashboard') ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 {{ Request::is('kp/dashboard') ? '' : 'group-hover:text-emerald-400' }}"></i>
                        <span class="text-sm">Dashboard</span>
                    </a>

                    <a href="{{ route('kp.verification') }}" 
                       class="flex items-center justify-between px-4 py-3 rounded-2xl transition {{ Request::is('kp/assessment-verification*') || Request::is('kp/review-assessment*') ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 group' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-square" class="w-5 h-5 {{ Request::is('kp/assessment-verification*') ? '' : 'group-hover:text-emerald-400' }}"></i>
                            <span class="text-sm">Assessments Approval</span>
                        </div>
                        
                        {{-- Dynamic Badge: Only shows if there are pending approvals --}}
                        @if(isset($pendingKpCount) && $pendingKpCount > 0)
                            <span class="{{ Request::is('kp/assessment-verification*') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-emerald-500' }} text-[10px] px-2 py-0.5 rounded-full text-white font-black">
                                {{ sprintf('%02d', $pendingKpCount) }}
                            </span>
                        @endif
                    </a>
                </nav>
            </div>

            <div class="mt-auto p-6 border-t border-slate-800/50 bg-slate-900/50">
                <div class="flex items-center gap-4 px-2 mb-6">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 border-2 border-emerald-500/20 flex items-center justify-center overflow-hidden">
                        <span class="text-emerald-700 font-black text-xs">{{ substr(Auth::user()->name, 0, 2) }}</span>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-black text-white truncate leading-none mb-1">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Ketua Program</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-3 py-3 bg-slate-800 hover:bg-red-500/10 hover:text-red-400 transition rounded-2xl text-slate-400 text-xs font-black uppercase tracking-widest border border-slate-700">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">@yield('title')</h2>
            </header>
            
            <main class="p-8 lg:p-12">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>