<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SME Portal | eCourse Folder')</title>
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> 
        [x-cloak] { display: none !important; }
        .animate-spin-slow { animation: spin 8s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 font-sans" x-data="{ notificationsOpen: false }">

    <div class="flex">
        <aside class="w-72 bg-slate-900 shadow-2xl h-screen hidden lg:flex flex-col sticky top-0 border-r border-slate-800 z-40">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-8">
                    
                    <h1 class="text-xl font-black text-white tracking-tighter italic">eCourse <span class="text-indigo-400">Folder</span></h1>
                </div>

                <div class="mb-8 px-4 py-3 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl">
                    <p class="text-[9px] uppercase font-black text-indigo-400 leading-none mb-1.5 tracking-widest">Verification Role</p>
                    <p class="text-xs font-bold text-indigo-100">Subject Matter Expert</p>
                </div>

                <nav class="space-y-2">
                    <a href="{{ url('sme/dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl transition group {{ Request::is('sme/dashboard') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 {{ Request::is('sme/dashboard') ? '' : 'group-hover:text-indigo-400' }}"></i>
                        <span class="text-sm">Dashboard</span>
                    </a>

                    <a href="{{ url('sme/assessment-verification') }}" 
                       class="flex items-center justify-between px-4 py-3 rounded-2xl transition {{ Request::is('sme/assessment-verification*') || Request::is('sme/review-workspace*') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="clipboard-list" class="w-5 h-5 {{ Request::is('sme/assessment-verification*') ? '' : 'group-hover:text-indigo-400' }}"></i>
                            <span class="text-sm">Assessment Verification</span>
                        </div>
                        <span class="{{ Request::is('sme/assessment-verification*') ? 'bg-white/20' : 'bg-slate-800 group-hover:bg-indigo-500' }} text-[10px] px-2 py-0.5 rounded-full text-white font-black">
                            <p class="">{{ sprintf('%02d', $pendingCount) }}</p>
                        </span>
                    </a>

                    <a href="{{ url('sme/history') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::is('sme/history') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                        <i data-lucide="archive" class="w-5 h-5 {{ Request::is('sme/history') ? '' : 'group-hover:text-indigo-400' }}"></i>
                        <span class="text-sm">Approval History</span>
                    </a>

                    <div class="pt-6 pb-2 px-4">
                        <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em]">System</p>
                    </div>

                    <a href="{{ url('sme/notifications') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::is('sme/notifications') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                        <i data-lucide="bell" class="w-5 h-5 {{ Request::is('sme/notifications') ? '' : 'group-hover:text-indigo-400' }}"></i>
                        <span class="text-sm">Notifications</span>
                    </a>
                </nav>
            </div>

            <div class="mt-auto p-6 border-t border-slate-800/50 bg-slate-900/50">
                <div class="flex items-center gap-4 px-2 mb-6">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 border-2 border-indigo-500/20 flex items-center justify-center overflow-hidden">
                        <span class="text-indigo-700 font-black text-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-black text-white truncate leading-none mb-1">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] text-slate-500 uppercase font-black tracking-widest">Subject Matter Expert</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                        class="w-full flex items-center justify-center gap-3 py-3 bg-slate-800 hover:bg-red-500/10 hover:text-red-400 transition rounded-2xl text-slate-400 text-xs font-black uppercase tracking-widest border border-slate-700">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <div class="lg:hidden p-2 hover:bg-slate-100 rounded-xl cursor-pointer">
                        <i data-lucide="menu" class="w-6 h-6 text-slate-600"></i>
                    </div>
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">SME Portal / <span class="text-indigo-600">@yield('title')</span></h2>
                </div>

                <div class="flex items-center gap-4">
                    {{-- <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-xl">
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-black text-emerald-700 uppercase tracking-tight">Active Reviewer</span>
                    </div> --}}

                    <button @click="notificationsOpen = true" class="relative p-2.5 bg-slate-50 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 rounded-2xl transition border border-slate-200/50 group">
                        <i data-lucide="bell" class="w-5 h-5 transition group-hover:scale-110"></i>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    
                    <div class="h-8 w-px bg-slate-200 mx-2"></div>
                    
                    <div class="text-right hidden sm:block leading-none">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1 tracking-tighter">Current Session</p>
                        <p class="text-[11px] font-bold text-slate-800">2025/2026 - SEM 1</p>
                    </div>
                </div>
            </header>

            <main class="p-8 lg:p-12">
                @yield('content')
            </main>
        </div>
    </div>

    <div x-show="notificationsOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" x-cloak class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="notificationsOpen = false"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full bg-white shadow-2xl">
            <div class="h-full flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="bell-ring" class="w-4 h-4 text-indigo-500"></i> Verification Tasks
                    </h3>
                    <button @click="notificationsOpen = false" class="p-2 hover:bg-white rounded-xl transition text-slate-400">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 relative group transition hover:bg-indigo-50">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-indigo-100">
                                <i data-lucide="file-warning" class="w-5 h-5 text-indigo-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-slate-800 leading-snug">New Submission</p>
                                <p class="text-[10px] text-slate-500 mt-1 italic">Final Exam Paper for CSE310 needs your expert verification.</p>
                                <p class="text-[9px] font-bold text-indigo-600 mt-2 uppercase tracking-widest">Just Now</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100">
                    <button class="w-full py-3 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-600 transition">
                        Mark All as Read
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>