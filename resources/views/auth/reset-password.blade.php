<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | TAALIM UMPSA</title>
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

<div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 p-10">
    <div class="text-center mb-8">
        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Set New Password</h2>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Please enter your new password</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl text-xs text-red-600 font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.update_forgotten') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="space-y-1">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">New Password</label>
            <input type="password" name="password" placeholder="••••••••" 
                   class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none" required>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Confirm Password</label>
            <input type="password" name="password_confirmation" placeholder="••••••••" 
                   class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none" required>
        </div>
        
        <button type="submit" class="w-full py-4 mt-2 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-xl shadow-blue-600/20">
            Update Password
        </button>
    </form>
</div>

<script>lucide.createIcons();</script>
</body>
</html>