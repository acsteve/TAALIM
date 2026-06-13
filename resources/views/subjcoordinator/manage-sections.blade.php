@extends('layouts.lecturerlayout.app')

@section('title', 'Manage Subject Sections')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    {{-- Header Section --}}
    <div class="flex flex-col gap-1">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Manage Subject Sections</h2>
        
    </div>

    {{-- Notification Alerts --}}
    @if(session('success'))
        <div id="alert-success" class="p-3 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-lg flex justify-between items-center text-xs font-bold transition-opacity duration-500">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    {{-- Context Banner --}}
    <div class="bg-slate-900 p-6 rounded-2xl flex items-center justify-between shadow-lg">
        <div class="space-y-0.5">
            <span class="text-[9px] font-black tracking-[0.2em] uppercase text-slate-400">Coordinated Subject</span>
            <h3 class="text-lg font-black text-white">{{ $subject->subject_name }}</h3>
        </div>
        <span class="text-[10px] font-black bg-indigo-600 text-white px-3 py-1 rounded-lg uppercase tracking-widest">
            {{ $subject->subject_code }}
        </span>
    </div>

    {{-- Management Console --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- Creation Form --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] pb-2 border-b border-slate-100">Add New Section</h3>
            
            <form action="{{ route('subjcoordinator.sections.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-2">Section Identifier</label>
                    <input type="text" name="section_name" required placeholder="e.g., L1, Section A" 
                        class="w-full text-sm font-medium border-slate-200 rounded-xl p-3 bg-slate-50 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-2">Lecturer Name</label>
                    <input type="text" name="lecturer_name" required placeholder="Lecturer's full name" 
                        class="w-full text-sm font-medium border-slate-200 rounded-xl p-3 bg-slate-50 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>

                <button type="submit" class="w-full justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg transition active:scale-95">
                    Register Section
                </button>
            </form>
        </div>

        {{-- Sections Ledger --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Active Sections</h3>
                <span class="text-[10px] font-black text-slate-400 uppercase">Total: {{ $subject->sections->count() }}</span>
            </div>

            @if($subject->sections->isEmpty())
                <div class="p-16 text-center">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">No sections registered yet</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 bg-slate-50/50">
                                <th class="px-6 py-4">Section Name</th>
                                <th class="px-6 py-4">Lecturer</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($subject->sections as $section)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 font-black text-slate-900">{{ $section->section_name }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-600">{{ $section->lecturer_name }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('subjcoordinator.sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('Confirm removal?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[10px] font-black text-red-600 uppercase hover:underline">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        const alert = document.getElementById('alert-success');
        if (alert) {
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);
</script>
@endsection