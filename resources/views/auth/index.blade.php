<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TAALIM UMPSA</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] { display: none !important; }
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="bg-pattern min-h-screen flex items-center justify-center p-6 font-sans">

<div class="max-w-md w-full" x-data="{ role: 'lecturer' }">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-white rounded-2xl shadow-xl mb-4 border border-slate-100 overflow-hidden">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
        </div>

        <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase">
            TAALIM <span class="text-blue-600">UMPSA</span>
        </h1>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 p-8 md:p-10">

        <div class="mb-8">
            <h2 class="text-xl font-bold text-slate-800">Welcome</h2>
            {{-- <p class="text-sm text-slate-500 italic">Access your academic workspace</p> --}}
        </div>

        @if($errors->any() || session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl">
                <div class="flex items-center gap-2 text-xs text-red-600 font-bold">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span>{{ session('error') ?? $errors->first() }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                    User Role
                </label>

                <div class="relative">
                    <i data-lucide="shield" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>

                    <select name="role"
                        x-model="role"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 py-4 text-sm font-bold appearance-none cursor-pointer focus:ring-4 transition outline-none"
                        :class="role === 'admin' ? 'focus:ring-slate-900/10 focus:border-slate-900' : (role === 'sqa' ? 'focus:ring-emerald-500/10 focus:border-emerald-500' : 'focus:ring-blue-500/10 focus:border-blue-500')">
                        
                        <option value="lecturer">Academic Staff (Lecturer)</option>
                        <option value="sqa">SQA Auditor</option>
                        <option value="admin">System Administrator</option>
                    </select>

                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                    User ID
                </label>
                <div class="relative">
                    <i data-lucide="user-circle" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="staff_id" value="{{ old('staff_id') }}" placeholder="Enter ID" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 py-4 text-sm font-bold focus:ring-4 transition outline-none" required>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                    Password
                </label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="password" name="password" placeholder="••••••••" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 py-4 text-sm font-bold focus:ring-4 transition outline-none" required>
                </div>
                <br>
                <div class="flex justify-end mt-1">
                    <a href="{{ route('password.request') }}" class="text-[10px] font-black uppercase text-blue-600 hover:text-blue-800 transition">
                        Forgot Password?
                    </a>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-4 rounded-2xl text-white text-xs font-black uppercase tracking-widest transition-all shadow-xl flex items-center justify-center gap-3 group"
                    :class="role === 'admin' ? 'bg-slate-900 hover:bg-slate-800' : (role === 'sqa' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700')">
                    
                    Log In
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-8">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            Trusted Archive And Learning Information Management System UMPSA
        </p>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>