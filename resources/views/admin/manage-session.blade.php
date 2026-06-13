@extends('layouts.adminlayout.app')
@section('title', 'Manage Sessions')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ showModal: false }">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Academic Sessions</h3>
            <p class="text-slate-500 text-sm">Control the active semester for the entire TAALIM UMPSA System.</p>
        </div>
        
        <button @click="showModal = true" class="px-6 py-3 bg-blue-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Create New Session
        </button>
    </div>

    @php $active = $sessions->where('is_active', true)->first(); @endphp
    <div class="relative overflow-hidden bg-slate-900 rounded-[2rem] p-8 mb-8 shadow-2xl shadow-blue-900/20">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 border border-blue-500/30 text-blue-400 text-[10px] font-black uppercase tracking-widest mb-3">
                    Manage Academic Session
                </span>
                <h2 class="text-4xl font-black text-white tracking-tight">
                    {{ $active->name ?? 'No Session Active' }}
                </h2>
                <p class="text-slate-400 text-sm mt-2 font-medium">All new workflows and file uploads will be tied to this semester.</p>
            </div>
            @if($active)
            <div class="flex items-center gap-2 bg-green-500/10 border border-green-500/20 px-4 py-2 rounded-xl">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-green-500 text-xs font-black uppercase tracking-widest">Live</span>
            </div>
            @endif
        </div>
        <i data-lucide="calendar" class="absolute -right-8 -bottom-8 w-48 h-48 text-white/5 -rotate-12"></i>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Semester Name</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Status</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sessions as $session)
                <tr class="{{ $session->is_active ? 'bg-blue-50/30' : '' }} hover:bg-slate-50 transition">
                    <td class="px-8 py-5">
                        <span class="font-bold text-slate-700 text-sm">{{ $session->name }}</span>
                    </td>
                    <td class="px-8 py-5">
                        @if($session->is_active)
                            <span class="text-[10px] font-black text-blue-600 bg-blue-100 px-3 py-1 rounded-full uppercase tracking-widest">Active</span>
                        @else
                            <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-3 py-1 rounded-full uppercase tracking-widest">Inactive</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-right flex justify-end items-center gap-4">
                        @if(!$session->is_active)
                            <form action="{{ route('admin.sessions.activate', $session->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-black uppercase tracking-widest text-blue-600 hover:text-blue-700 transition">
                                    Set as Active
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Delete this session? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-black uppercase tracking-widest text-rose-500 hover:text-rose-700 transition">
                                    Delete
                                </button>
                            </form>
                        @else
                            <span class="text-xs font-black uppercase tracking-widest text-slate-300">Currently in use</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-8 py-20 text-center text-slate-400 italic">No academic sessions found. Create one to begin.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
        
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden" 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            
            <form action="{{ route('admin.sessions.store') }}" method="POST">
                @csrf
                <div class="p-8">
                    <h4 class="text-xl font-black text-slate-900 mb-6">Add New Session</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Session Label</label>
                            <input type="text" name="name" placeholder="e.g. 2025/2026 - Semester 1" required 
                                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500/20 outline-none transition">
                            <p class="text-[10px] text-slate-400 mt-2 ml-1 italic">Format: Year/Year - Semester Number</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="text-xs font-black uppercase text-slate-400 px-4">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition">Save Session</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection