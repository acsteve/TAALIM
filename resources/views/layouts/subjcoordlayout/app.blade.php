<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Coordinator | eCourse Folder')</title>
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> 
        [x-cloak] { display: none !important; }
        .animate-spin-slow { animation: spin 8s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 font-sans" x-data="{ notificationsOpen: false, sidebarOpen: false }">

    <div class="flex">
        <aside class="w-72 bg-slate-900 shadow-2xl h-screen hidden lg:flex flex-col sticky top-0 border-r border-slate-800 z-40">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-8">
                    <h1 class="text-xl font-black text-white tracking-tighter italic">Taalim <span class="text-blue-400">UMPSA</span></h1>
                </div>

                <div class="mb-8 px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-2xl">
                    <p class="text-[9px] uppercase font-black text-blue-400 leading-none mb-1.5 tracking-widest">Active Course</p>
                    <p class="text-xs font-bold text-white truncate">CSE310 - Web Development</p>
                </div>

<nav class="space-y-2" x-data="{ 
    openAssess: {{ Request::is('subjcoordinator/assessment-*') ? 'true' : 'false' }},
    openReports: {{ Request::is('subjcoordinator/reports-*') ? 'true' : 'false' }} 
}">
    
    <!-- 1. DASHBOARD OVERVIEW -->
    <a href="{{ route('subjcoordinator.index') }}" 
        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::routeIs('subjcoordinator.index') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
        <span class="text-sm">Main Dashboard</span>
    </a>

    <!-- 2. ASSESSMENT SECTION (The Approval Workflow) -->
    <div>
        <button @click="openAssess = !openAssess" 
                class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition group {{ Request::is('subjcoordinator/assessment-*') ? 'text-white font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <div class="flex items-center gap-3">
                <i data-lucide="check-square" class="w-5 h-5 group-hover:text-blue-400"></i>
                <span class="text-sm">Assessment Files</span>
            </div>
            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="openAssess ? 'rotate-180' : ''"></i>
        </button>

        <div x-show="openAssess" x-cloak x-transition class="mt-2 ml-6 space-y-1 border-l-2 border-slate-800 pl-4">
            <a href="{{ route('subjcoordinator.upload') }}" class="block py-2 text-xs {{ Request::routeIs('subjcoordinator.upload') ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">New Submission</a>
            <a href="{{ route('subjcoordinator.answersample') }}" class="block py-2 text-xs {{ Request::routeIs('subjcoordinator.answersample') ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">Answer Samples</a>
        </div>
    </div>

    <!-- 3. REPORTS SECTION (Direct Upload / No Approval Needed) -->
    <div>
        <button @click="openReports = !openReports" 
                class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition group {{ Request::is('subjcoordinator/reports-*') ? 'text-white font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
            <div class="flex items-center gap-3">
                <i data-lucide="file-text" class="w-5 h-5 group-hover:text-emerald-400"></i>
                <span class="text-sm">Course Records</span>
            </div>
            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="openReports ? 'rotate-180' : ''"></i>
        </button>

        <div x-show="openReports" x-cloak x-transition class="mt-2 ml-6 space-y-1 border-l-2 border-slate-800 pl-4">
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Lecture Notes</a>
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Attendance Records</a>
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Midterm Report</a>
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Coordination Report</a>
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Coordination Report</a>
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Coordination Report</a>
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Coordination Report</a>
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Coordination Report</a>
            <a href="#" class="block py-2 text-xs text-slate-500 hover:text-white italic">Coordination Report</a>
        </div>
    </div>
</nav>
            </div>

            <div class="mt-auto p-6 border-t border-slate-800/50 bg-slate-900/50">
                <div class="flex items-center gap-4 px-2 mb-6">
                    <div class="w-10 h-10 rounded-full bg-blue-100 border-2 border-blue-500/20 flex items-center justify-center overflow-hidden">
                        <span class="text-blue-700 font-black text-xs">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-black text-white truncate leading-none mb-1">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] text-slate-500 uppercase font-black tracking-widest">{{ Auth::user()->role }}</p>
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
                <div class="flex items-center gap-3">
                    <div class="lg:hidden p-2 hover:bg-slate-100 rounded-xl cursor-pointer">
                        <i data-lucide="menu" class="w-6 h-6 text-slate-600"></i>
                    </div>
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Coordinator / <span class="text-blue-600">@yield('title')</span></h2>
                </div>

                <div class="flex items-center gap-4">
                    @if(session('success'))
                        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-xl">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-blue-500"></i>
                            <span class="text-[10px] font-black text-blue-700 uppercase tracking-tight">{{ session('success') }}</span>
                        </div>
                    @endif

                    <button @click="notificationsOpen = true" class="relative p-2.5 bg-slate-50 hover:bg-blue-50 text-slate-400 hover:text-blue-600 rounded-2xl transition border border-slate-200/50 group">
                        <i data-lucide="bell" class="w-5 h-5 transition group-hover:scale-110"></i>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    
                    <div class="h-8 w-px bg-slate-200 mx-2"></div>
                    
                    <div class="text-right hidden sm:block leading-none">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1 tracking-tighter">Current Session</p>
                        <p class="text-[11px] font-bold text-slate-800">
                            {{ $activeSession->name ?? 'NO ACTIVE SESSION' }}
                        </p>
                    </div>
                </div>
            </header>

            <main class="p-8 lg:p-12">
                @yield('content')
            </main>
        </div>
    </div>

    <div x-show="notificationsOpen" x-cloak class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="notificationsOpen = false"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full bg-white shadow-2xl transition-transform duration-300" 
             x-transition:enter="transform transition ease-in-out duration-300" 
             x-transition:enter-start="translate-x-full" 
             x-transition:enter-end="translate-x-0" 
             x-transition:leave="transform transition ease-in-out duration-300" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="translate-x-full">
            <div class="h-full flex flex-col">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="bell-ring" class="w-4 h-4 text-blue-500"></i> Recent Alerts
                    </h3>
                    <button @click="notificationsOpen = false" class="p-2 hover:bg-white rounded-xl transition text-slate-400">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 relative group transition hover:bg-blue-50">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-blue-100">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-slate-800 leading-snug">Review Update</p>
                                <p class="text-[10px] text-slate-500 mt-1 italic">The Management Hub has been updated with your latest SME feedback.</p>
                                <p class="text-[9px] font-bold text-blue-600 mt-2 uppercase tracking-widest">Just Now</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100">
                    <button class="w-full py-3 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition">
                        Mark All as Read
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>