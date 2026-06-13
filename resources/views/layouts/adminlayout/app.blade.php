<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Portal | eCourse Folder')</title>
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> 
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 font-sans">

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-72 bg-slate-900 shadow-2xl h-screen hidden lg:flex flex-col sticky top-0 border-r border-slate-800 z-40">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <h1 class="text-xl font-black text-white tracking-tighter italic">TAALIM <span class="text-emerald-400">UMPSA</span></h1>
                </div>

                <nav class="space-y-2">
                    <div class="pb-2">
                        <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Management</p>
                        <a href="{{ url('admin/users') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::is('admin/users*') ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                            <i data-lucide="users" class="w-5 h-5 {{ Request::is('admin/users*') ? '' : 'group-hover:text-emerald-400' }}"></i>
                            <span class="text-sm">Manage Users</span>
                        </a>
                        <a href="{{ url('admin/courses') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::is('admin/courses*') ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                            <i data-lucide="layers" class="w-5 h-5 {{ Request::is('admin/courses*') ? '' : 'group-hover:text-emerald-400' }}"></i>
                            <span class="text-sm">Program/Courses</span>
                        </a>
                    </div>

                    <div class="pt-2 pb-2">
                        <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Academic</p>
                        <a href="{{ url('admin/manage-session') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::is('admin/manage-session*') ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                            <i data-lucide="calendar" class="w-5 h-5 {{ Request::is('admin/manage-session*') ? '' : 'group-hover:text-emerald-400' }}"></i>
                            <span class="text-sm">Academic Sessions</span>
                        </a>

                        <a href="{{ url('admin/subjects') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::is('admin/subjects*') ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                            <i data-lucide="book-open" class="w-5 h-5 {{ Request::is('admin/subjects*') ? '' : 'group-hover:text-emerald-400' }}"></i>
                            <span class="text-sm">Manage Subjects</span>
                        </a>
                    </div>

                    <div class="pt-2">
                        <p class="px-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Quality Assurance</p>
                        <a href="{{ url('admin/manage-sqa') }}" 
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ Request::is('admin/manage-sqa*') ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                            <i data-lucide="shield-check" class="w-5 h-5 {{ Request::is('admin/manage-sqa*') ? '' : 'group-hover:text-emerald-400' }}"></i>
                            <span class="text-sm">Manage QA</span>
                        </a>
                    </div>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Admin / @yield('title')</h2>

                <div class="flex items-center gap-6">
                    <div class="hidden md:flex items-center gap-3 px-4 py-2 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-right">
                            <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Active Session</p>
                            <p class="text-[11px] font-bold text-slate-700 leading-none">{{ $activeSession->name ?? 'None Set' }}</p>
                        </div>
                        <div class="w-2 h-2 rounded-full {{ isset($activeSession) ? 'bg-emerald-500' : 'bg-rose-500' }} shadow-sm"></div>
                    </div>

                    {{-- User Profile Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 pl-6 border-l border-slate-200 focus:outline-none">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-black text-slate-800 leading-none">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-tighter leading-none mt-1">{{ Auth::user()->role }}</p>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-slate-900 flex items-center justify-center text-white font-black text-xs">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                        </button>

                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-cloak 
                             class="absolute top-14 right-0 w-40 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 overflow-hidden">
                            
                            <a href="{{ route('admin.profile.edit') }}" 
                               class="block px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 hover:text-emerald-600 transition">
                                Edit Profile
                            </a>
                            
                            <div class="border-t border-slate-50"></div>
                            
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 text-xs font-black uppercase tracking-widest text-red-500 hover:bg-slate-50 transition">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-8 lg:p-12">
                @yield('content')
            </main>
        </div>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>