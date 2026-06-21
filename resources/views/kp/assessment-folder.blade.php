@extends('layouts.lecturerlayout.app')

@section('title', 'Assessment Folder | ' . $assessment->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center text-sm text-gray-500 gap-2 mb-2">
        <a href="{{ route('kp.assessment-archive') }}" class="hover:text-emerald-600 transition">Assessment Archive</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <a href="{{ route('kp.subject.assessments', [$assessment->subject_id, 'session' => $assessment->session_id ?? '']) }}" 
           class="hover:text-emerald-600 transition">Subject Assessments</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-gray-900 font-semibold">{{ $assessment->title }} Folder</span>
    </nav>

    {{-- Header Info --}}
    <div class="bg-white border-t-8 border-emerald-600 rounded-2xl shadow-sm border border-slate-200 p-8 relative overflow-hidden">
        
        
        <div class="relative z-10">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">{{ $assessment->title }}</h1>
                    <p class="text-lg text-emerald-600 font-medium mt-1">
                        {{ $assessment->subject->subject_code ?? 'N/A' }} - {{ $assessment->subject->subject_name ?? 'N/A' }} 
                        <span class="text-slate-400">({{ $assessment->session }})</span>
                    </p>
                </div>
                {{-- Add your Download Zip route here if available --}}
                <a href="{{ route('subjcoordinator.assessment.download-zip', $assessment->id) }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold text-sm flex items-center gap-2 hover:bg-emerald-700 shadow-sm transition">
                    <i data-lucide="download" class="w-4 h-4"></i> Download Full Folder
                </a>
            </div>

            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Coordinator</p>
                    <p class="text-sm font-bold text-slate-800">{{ $assessment->subject->coordinator->name ?? 'N/A' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Validated By (KP)</p>
                    <p class="text-sm font-bold text-slate-800">{{ $assessment->kp->name ?? 'Pending' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Validated By (SME)</p>
                    <p class="text-sm font-bold text-slate-800">
                        {{ $assessment->sme1->name ?? '---' }} & {{ $assessment->sme2->name ?? '---' }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date Finalized</p>
                    <p class="text-sm font-bold text-slate-800">{{ $assessment->updated_at ? $assessment->updated_at->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Files Section --}}
    <div class="grid md:grid-cols-2 gap-6">
        @foreach(['Question Paper' => 'question_file', 'Marking Scheme' => 'schema_file'] as $label => $field)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 {{ $loop->first ? 'bg-emerald-50' : 'bg-slate-100' }} rounded-lg">
                    <i data-lucide="{{ $loop->first ? 'file-text' : 'key' }}" class="w-6 h-6 {{ $loop->first ? 'text-emerald-600' : 'text-slate-600' }}"></i>
                </div>
                <h3 class="font-bold text-slate-800">Approved {{ $label }}</h3>
            </div>
            <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div class="flex items-center gap-3">
                    <i data-lucide="file" class="w-5 h-5 text-slate-400"></i>
                    <span class="text-sm font-medium text-slate-600 truncate max-w-[200px]">
                        {{ $loop->first ? $assessment->question_filename : $assessment->schema_filename }}
                    </span>
                </div>
                <a href="{{ Storage::url($assessment->$field ?? '') }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 text-xs font-black uppercase tracking-tighter">View</a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Student Samples Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-indigo-600"></i> Student Answer Samples
            </h3>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach(['best' => 'emerald', 'medium' => 'amber', 'weak' => 'rose'] as $category => $color)
            <div class="space-y-3">
                <h4 class="text-xs font-black text-{{ $color }}-600 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span> {{ ucfirst($category) }} Samples
                </h4>
                
                @forelse(($assessment->answerSamples ?? collect())->where('category', $category) as $sample)
                <a href="{{ Storage::url($sample->file_path) }}" target="_blank" class="group flex items-center justify-between p-3 bg-slate-50 hover:bg-slate-100 border border-transparent rounded-xl transition">
                    <div class="flex items-center gap-2">
                        <i data-lucide="file-check-2" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-xs font-medium text-slate-700">{{ $sample->filename }}</span>
                    </div>
                    <i data-lucide="eye" class="w-3 h-3 text-slate-300"></i>
                </a>
                @empty
                <p class="text-[10px] text-slate-400 italic">No files uploaded.</p>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection