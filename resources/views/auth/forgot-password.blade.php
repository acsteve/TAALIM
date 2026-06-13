<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | TAALIM UMPSA</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-6 font-sans">

<div class="max-w-md w-full">
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
            <h2 class="text-xl font-bold text-slate-800">Password Recovery</h2>
            <p class="text-sm text-slate-500 italic">Enter your User ID to receive a reset link</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-2xl">
                <p class="text-xs text-emerald-700 font-bold leading-relaxed">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">User ID</label>
                <div class="relative">
                    <i data-lucide="user-circle" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="staff_id" placeholder="Enter your User ID" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 py-4 text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none" required>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-blue-600/20 flex items-center justify-center gap-3">
                Send Reset Link
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition">
                Back to Login
            </a>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
</body>
</html>