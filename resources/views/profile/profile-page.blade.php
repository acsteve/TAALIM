@extends('layouts.lecturerlayout.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-2xl space-y-8">
    <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Edit Profile</h2>

    {{-- Success/Error Feedback Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs rounded-2xl">
            <ul class="list-disc ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form for Name & Email --}}
    <form action="{{ route('profile.update') }}" method="POST" class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm space-y-6">
        @csrf @method('PUT')
        <h3 class="font-bold text-slate-800">Personal Details</h3>
        
        <div>
            <label class="text-[10px] font-black uppercase text-slate-400">Full Name</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full mt-1 p-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-sm">
        </div>
        <div>
            <label class="text-[10px] font-black uppercase text-slate-400">Email Address</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full mt-1 p-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-sm">
        </div>
        <button type="submit" class="px-8 py-4 bg-emerald-600 text-white rounded-2xl font-black uppercase text-xs hover:bg-emerald-700 transition">Update Details</button>
    </form>

    {{-- Form for Password Change --}}
    <form action="{{ route('password.change') }}" method="POST" 
          onsubmit="return confirm('Are you sure you want to change your password?');"
          class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm space-y-6"
          x-data="{ show: false }">
        @csrf @method('PUT')
        <h3 class="font-bold text-slate-800">Security Settings</h3>
        
        <div>
            <label class="text-[10px] font-black uppercase text-slate-400">Current Password</label>
            <input type="password" name="current_password" class="w-full mt-1 p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm" required>
        </div>

        <div>
            <label class="text-[10px] font-black uppercase text-slate-400">New Password</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" class="w-full mt-1 p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm" required>
                <button type="button" @click="show = !show" class="absolute right-4 top-5 text-[10px] font-black uppercase text-slate-400 hover:text-emerald-600">
                    <span x-text="show ? 'Hide' : 'Show'"></span>
                </button>
            </div>
        </div>

        <div>
            <label class="text-[10px] font-black uppercase text-slate-400">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="w-full mt-1 p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm" required>
        </div>

        <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase text-xs hover:bg-slate-800 transition">Change Password</button>
    </form>
</div>
@endsection