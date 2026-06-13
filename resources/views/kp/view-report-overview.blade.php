@extends('layouts.lecturerlayout.app')

@section('title', 'Program Reports Overview')

@section('content')
<div class="space-y-6">
    {{-- Top Action Filter Section --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Subject Reports</h3>
            <p class="text-xs text-slate-500">Monitor and view reports submitted by Subject Coordinators.</p>
        </div>
        
        {{-- Unified Filter and Search bar --}}
        <form method="GET" action="{{ route('kp.midterm.audit') }}" class="w-full xl:w-auto flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="relative flex-1 md:w-72">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search code or subject title..." 
                    class="w-full text-xs border-slate-200 rounded-xl pl-9 pr-4 py-2.5 bg-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- DYNAMIC: Populated purely from the database sessions records --}}
                <select name="session" onchange="this.form.submit()" class="text-xs font-bold border-slate-200 rounded-xl p-2.5 bg-white shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    @foreach($sessions as $sessionItem)
                        <option value="{{ $sessionItem->id }}" {{ $selectedSession == $sessionItem->id ? 'selected' : '' }}>
                            {{ $sessionItem->name }}
                        </option>
                    @endforeach
                </select>
                @if($search)
                    <a href="{{ route('kp.midterm.audit') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Grid Layout Output Elements --}}
    @if($subjects->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <i data-lucide="folder-search" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h4 class="text-sm font-bold text-slate-800">No Program Folders Located</h4>
            <p class="text-xs text-slate-400 mt-1">Adjust search tracking filters or select a different semester tracking module layout.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($subjects as $subject)
                @php
                    // FIXED: Dynamically load real section count from your Eloquent setup
                    $realSectionsCount = $subject->sections->count();
                    
                    // Base admin files plus 2 tracking nodes per unique section
                    $expectedDocs = $baseStandardCount + ($realSectionsCount * 2);
                    $uploadedDocsCount = $subject->courseReports->count();
                    
                    // Protect against division by zero if a course has zero sections configured yet
                    $percentage = $expectedDocs > 0 ? round(($uploadedDocsCount / $expectedDocs) * 100) : 0;
                    
                    // SYSTEM SAFEGUARD CAP: Enforce absolute mathematical limit to eliminate overflow bug states
                    if ($percentage > 100) {
                        $percentage = 100;
                    }
                @endphp
                
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all duration-200">
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-black px-2.5 py-1 bg-slate-100 text-slate-800 rounded-md uppercase tracking-wider">{{ $subject->subject_code }}</span>
                            <span class="text-[11px] font-bold {{ $percentage == 100 ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50' }} px-2 py-0.5 rounded">
                                {{ $percentage }}% Uploaded
                            </span>
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-900 text-base line-clamp-1">{{ $subject->subject_name }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Managed Class Sections: <strong>{{ $realSectionsCount }}</strong></p>
                        </div>

                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-600 transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-500">Submissions: <strong>{{ $uploadedDocsCount }} / {{ $expectedDocs }}</strong></span>
                        
                        <a href="{{ route('reports.show', ['subject_id' => $subject->id, 'session' => $selectedSession]) }}" 
                           class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition flex items-center gap-1">
                            View Subject <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection