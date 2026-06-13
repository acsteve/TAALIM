<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Lecturer Portal') | TAALIM UMPSA</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> [x-cloak] { display: none !important; } </style>
</head>

@php
    $isDashboard = Request::routeIs('lecturer.dashboard');
    $currSmeUrl = Request::is('sme*');
    $currKpUrl = Request::is('kp*');
    $currCoordUrl = Request::is('subjcoordinator*') || $isDashboard;

    $hasSmeAccess = $isSmeAppointed ?? false; 
    $hasKpAccess = $isKpAppointed ?? false;
    $hasCampAccess = $hasCampAccess ?? false;
@endphp

<body class="bg-slate-50 min-h-screen text-slate-900 font-sans" x-data="{ sidebarOpen: false }">

    <div class="flex">
        <aside class="w-72 bg-slate-900 shadow-2xl h-screen hidden lg:flex flex-col sticky top-0 border-r border-slate-800 z-40">
            <div class="p-8 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-8">
                    <h1 class="text-xl font-black text-white tracking-tighter italic">TAALIM <span class="text-blue-400">UMPSA</span></h1>
                </div>

                <nav class="space-y-6 overflow-y-auto pr-2" 
                     x-data="{ 
                       openAssess: {{ (Request::is('subjcoordinator*') || Request::routeIs('subjcoordinator.folder')) ? 'true' : 'false' }}, 
                       openKp: {{ Request::is('kp*') ? 'true' : 'false' }} 
                     }">
                    
                    <div>
                        <a href="{{ route('lecturer.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ $isDashboard ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                            <span class="text-sm">Main Dashboard</span>
                        </a>
                    </div>

                    @if(isset($activeSubject) && $activeSubject)
                    <div>
                        <p class="px-4 mb-2 text-[10px] font-black text-blue-400 uppercase tracking-[0.2em]">Coordinator</p>
                        <div class="space-y-1">
                            <button @click="openAssess = !openAssess" 
                                    class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition group {{ (Request::is('subjcoordinator*') || Request::routeIs('subjcoordinator.folder')) ? 'text-white font-bold bg-slate-800/40' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="folder-up" class="w-5 h-5 group-hover:text-blue-400"></i>
                                    <span class="text-sm">Subject Files</span>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="openAssess ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="openAssess" x-cloak x-transition class="mt-2 ml-6 space-y-1 border-l-2 border-slate-800 pl-4">
                                <a href="{{ route('subjcoordinator.index') }}" 
                                class="block py-2 text-xs {{ (Request::routeIs('subjcoordinator.index') || Request::routeIs('subjcoordinator.folder')) ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">
                                Assessment List
                                </a>

                                <a href="{{ route('subjcoordinator.upload') }}" 
                                class="block py-2 text-xs {{ Request::routeIs('subjcoordinator.upload') ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">
                                Upload New Assessment
                                </a>

                                <a href="{{ route('subjcoordinator.sections.index') }}" 
                                class="block py-2 text-xs {{ Request::routeIs('subjcoordinator.sections.index') ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">
                                Manage Sections
                                </a>

                                <a href="{{ route('subjcoordinator.reports.index', ['subject_id' => $activeSubject->id, 'session' => $activeSession->id ?? 'default']) }}" 
                                class="block py-2 text-xs {{ Request::routeIs('subjcoordinator.reports.index') ? 'text-blue-400 font-bold' : 'text-slate-500 hover:text-white' }}">
                                Subject Reports
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($hasSmeAccess)
                    <div>
                        <p class="px-4 mb-2 text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em]">Subject Matter Expert</p>
                        <a href="{{ url('sme/assessment-verification') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl transition {{ $currSmeUrl ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                            <div class="flex items-center gap-3">
                                <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                                <span class="text-sm">Assessment Approval</span>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if(($hasCampAccess ?? false) || ($hasKpAccess ?? false))
                    <div>
                        <p class="px-4 mb-2 text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em]">Ketua Program</p>
                        <div class="space-y-1">
                            <button @click="openKp = !openKp" class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition group {{ Request::is('kp*') ? 'text-white font-bold bg-slate-800/40' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="shield-check" class="w-5 h-5 group-hover:text-emerald-400"></i>
                                    <span class="text-sm">KP Management</span>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="openKp ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="openKp" x-cloak x-transition class="mt-2 ml-6 space-y-1 border-l-2 border-slate-800 pl-4">
                                <a href="{{ route('kp.verification') }}" class="block py-2 text-xs {{ Request::routeIs('kp.verification') ? 'text-emerald-400 font-bold' : 'text-slate-500 hover:text-white' }}">Assessment Approvals</a>
                                <a href="{{ route('kp.midterm.audit') }}" class="block py-2 text-xs {{ (Request::routeIs('kp.midterm.audit') || Request::is('kp/reports*')) ? 'text-emerald-400 font-bold' : 'text-slate-500 hover:text-white' }}">Subject Report</a>
                                <a href="{{ route('kp.assessment-archive') }}" class="block py-2 text-xs {{ request()->routeIs(['kp.assessment-archive', 'kp.subject.assessments']) ? 'text-emerald-400 font-bold' : 'text-slate-500 hover:text-white' }}">Archived Assessment</a>
                            </div>
                        </div>
                    </div>
                    @endif
                </nav>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">@yield('title')</h2>
                
                <div class="flex items-center gap-6">
                    {{-- Active Session Indicator --}}
                    <div class="hidden md:flex items-center gap-3 px-4 py-2 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-right">
                            <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Active Session</p>
                            <p class="text-[11px] font-bold text-slate-700 leading-none">{{ $activeSession->name ?? 'None Set' }}</p>
                        </div>
                        <div class="w-2 h-2 rounded-full {{ isset($activeSession) ? 'bg-blue-500' : 'bg-rose-500' }} shadow-sm"></div>
                    </div>

                    {{-- Profile Dropdown --}}
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" class="flex items-center gap-3 hover:bg-slate-100 p-2 rounded-2xl transition">
                            <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center">
                                <span class="text-white font-black text-[10px]">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                            </div>
                            <div class="text-left hidden md:block">
                                <p class="text-xs font-black text-slate-800">{{ Auth::user()->name }}</p>
                                <p class="text-[9px] text-slate-400 uppercase font-black tracking-widest">Lecturer</p>
                            </div>
                        </button>

                        <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak x-transition class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl p-2 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 rounded-xl transition">Edit Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs font-black text-red-500 hover:bg-red-50 rounded-xl transition uppercase">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-8"> @yield('content') </main>
        </div>
    </div>
    <script> lucide.createIcons(); </script>
</body>
</html>