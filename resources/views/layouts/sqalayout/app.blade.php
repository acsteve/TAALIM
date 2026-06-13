<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SQA Auditor') | Taalim UMPSA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style> [x-cloak] { display: none !important; } </style>
</head>

<body class="bg-slate-50 min-h-screen text-slate-900 font-sans">

    <div class="flex">
        <aside class="w-72 bg-slate-900 shadow-2xl h-screen hidden lg:flex flex-col sticky top-0 border-r border-slate-800 z-40">
            <div class="p-8 flex flex-col h-full">
                
                <div class="flex items-center gap-3 mb-8">
                    <h1 class="text-xl font-black text-white tracking-tighter italic">Taalim <span class="text-emerald-400">UMPSA</span></h1>
                </div>

                <nav class="space-y-6 flex-1">
                    <div>
                        <p class="px-4 mb-2 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">General</p>
                            <a href="{{ route('sqa.dashboard') }}" 
                            class="flex items-center gap-3 px-4 py-3 rounded-2xl transition 
                            {{ Request::routeIs('sqa.dashboard', 'sqa.assessment.show') ? 'bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white group' }}">
                                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                <span class="text-sm">Audit Dashboard</span>
                            </a>
                    </div>

                </nav>

                <div class="mt-auto pt-6 border-t border-slate-800/50">
                    <div class="flex items-center gap-4 px-2 mb-6">
                        <div class="w-10 h-10 rounded-full bg-emerald-900 flex items-center justify-center border border-emerald-800">
                            <span class="text-emerald-100 font-black text-xs">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm font-black text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[9px] text-emerald-500 uppercase font-black tracking-widest">SQA Auditor</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-slate-800 hover:bg-red-500/10 hover:text-red-400 transition rounded-2xl text-slate-400 text-[10px] font-black uppercase border border-slate-700">Sign Out</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-20 flex items-center justify-between px-8 sticky top-0 z-30">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                    @yield('title', 'Dashboard')
                </h2>
            </header>
            <main class="p-8"> @yield('content') </main>
        </div>
    </div>

    <script> lucide.createIcons(); </script>
</body>
</html>