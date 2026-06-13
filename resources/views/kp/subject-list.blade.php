@extends('layouts.lecturerlayout.app')

@section('title', 'Subject Assessments Archive')

@section('content')
<div class="space-y-6">
    {{-- Top Action Filter Section --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Subject Assessments Archive</h3>
            <p class="text-xs text-slate-500">Browse subjects to access their completed assessment archives.</p>
        </div>
        
        <form method="GET" action="{{ route('kp.assessment-archive') }}" class="w-full xl:w-auto flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative flex-1 md:w-72">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search code or subject title..." 
                       class="w-full text-xs border-slate-200 rounded-xl pl-9 pr-4 py-2.5 bg-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <select name="session" onchange="this.form.submit()" 
                        class="text-xs font-bold border-slate-200 rounded-xl p-2.5 bg-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    @foreach($sessions as $sessionItem)
                        <option value="{{ $sessionItem->id }}" {{ (isset($selectedSessionId) && $selectedSessionId == $sessionItem->id) ? 'selected' : '' }}>
                            {{ $sessionItem->name }}
                        </option>
                    @endforeach
                </select>
                @if(isset($search) && $search)
                    <a href="{{ route('kp.assessment-archive') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Grid Layout --}}
    @if($subjects->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <i data-lucide="folder-search" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h4 class="text-sm font-bold text-slate-800">No Subjects Found</h4>
            <p class="text-xs text-slate-400 mt-1">Try adjusting your search criteria or selecting a different session.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($subjects as $subject)
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-emerald-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-black px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md uppercase tracking-wider">{{ $subject->subject_code }}</span>
                    <h3 class="font-bold text-slate-900 text-base mt-2 line-clamp-1">{{ $subject->subject_name }}</h3>
                </div>
                
                <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-100">
                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
                        {{ $subject->assessments_count ?? 0 }} Completed
                    </span>
                    
                    {{-- Corrected Link: Now passes the selected session ID forward --}}
                    <a href="{{ route('kp.subject.assessments', [$subject->id, 'session' => $selectedSessionId]) }}" 
                       class="text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 px-3 py-1.5 rounded-xl shadow-sm transition">
                        View Archive
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection