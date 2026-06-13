@extends('layouts.lecturerlayout.app')

@section('title', 'Subject Assessments')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <a href="{{ route('kp.assessment-archive') }}" class="text-xs text-slate-500 hover:text-emerald-600 flex items-center gap-1 mb-2">
            <i data-lucide="arrow-left" class="w-3 h-3"></i> Back to Archive
        </a>
        <h2 class="text-xl font-black text-slate-900">{{ $subject->subject_name }} ({{ $subject->subject_code }})</h2>
        <p class="text-xs text-slate-400">Viewing completed assessments for session: <strong>{{ $sessionName }}</strong></p>
    </div>

    {{-- Assessment List --}}
    @if($assessments->isEmpty())
        <div class="bg-white p-12 text-center rounded-2xl border border-slate-200 shadow-sm">
            <i data-lucide="folder-x" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-500 text-sm">No completed assessments found for this subject in the selected session.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($assessments as $assessment)
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between hover:border-emerald-200 transition">
                <div>
                    <h4 class="font-bold text-slate-800">{{ $assessment->title }}</h4>
                    <span class="text-[10px] uppercase font-black tracking-wider bg-slate-100 text-slate-500 px-2 py-0.5 rounded">{{ $assessment->type }}</span>
                </div>
                
                <div class="flex gap-2">
                    {{-- View Button (Opens in new tab) --}}
                    <a href="{{ route('kp.assessment.folder', $assessment->id) }}" 
                        class="text-xs font-bold bg-slate-100 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-200 transition">
                        View Folder
                    </a>

                    {{-- Download Button --}}
                    {{-- <a href="{{ route('kp.assessment.download', $assessment->id) }}" 
                       class="text-xs font-bold bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition">
                        Download
                    </a> --}}
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection