@extends('layouts.lecturerlayout.app')

@section('title', 'Assessment Folder')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    
    {{-- Back Navigation --}}
    <a href="{{ route('subjcoordinator.index') }}" 
       class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-indigo-600 transition">
        <i data-lucide="arrow-left" class="w-3 h-3"></i>
        Back to Assessment List
    </a>
    
    {{-- Header Section --}}
    <div class="flex flex-col gap-1">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Assessment Overview</h2>
        <p class="text-slate-500 text-sm">Reviewing: <span class="font-bold text-slate-800">{{ $assessment->title }}</span></p>
    </div>

    {{-- Context Banner --}}
    <div class="bg-slate-900 p-8 rounded-2xl flex items-center justify-between shadow-lg">
        <div class="space-y-0.5">
            <span class="text-[9px] font-black tracking-[0.2em] uppercase text-slate-400">Subject Details</span>
            <h3 class="text-xl font-black text-white">{{ $assessment->subject->subject_name ?? 'N/A' }}</h3>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black bg-indigo-600 text-white px-4 py-2 rounded-lg uppercase tracking-widest">
                {{ $assessment->subject->subject_code ?? 'N/A' }}
            </span>
            <a href="{{ route('subjcoordinator.assessment.download-zip', $assessment->id) }}" 
               class="bg-white text-slate-900 px-4 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition">
                Download All
            </a>
        </div>
    </div>

    {{-- Metadata Ledger --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            'Coordinator' => $assessment->subject->coordinator->name ?? 'N/A',
            'Validated By (KP)' => $assessment->kp->name ?? 'Pending',
            'Validated By (SME)' => ($assessment->sme1->name ?? '---') . ' / ' . ($assessment->sme2->name ?? '---'),
            'Date Finalized' => $assessment->updated_at ? $assessment->updated_at->format('M d, Y') : 'N/A'
        ] as $label => $value)
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">{{ $label }}</p>
            <p class="text-xs font-black text-slate-700 truncate">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    {{-- Files Section --}}
    <div class="grid md:grid-cols-2 gap-6">
        @foreach([
            'Question Paper' => ['file' => 'question_file', 'name' => 'question_filename'], 
            'Marking Scheme' => ['file' => 'schema_file', 'name' => 'schema_filename']
        ] as $label => $fields)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <i data-lucide="{{ $label == 'Question Paper' ? 'file-text' : 'key' }}" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">{{ $label }}</h3>
            </div>
            <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
                {{-- Use the 'name' column instead of basename() --}}
                <span class="text-xs font-bold text-slate-600 truncate max-w-[200px]">
                    {{ $assessment->{$fields['name']} ?? 'No file' }}
                </span>
                <a href="{{ route('assessment.view-file', ['id' => $assessment->id, 'type' => $label == 'Question Paper' ? 'question' : 'schema']) }}" 
                target="_blank" 
                class="text-[10px] font-black text-indigo-600 uppercase hover:underline">View</a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Student Samples Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Student Answer Samples</h3>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach(['best' => 'Best', 'medium' => 'Medium', 'weak' => 'Weak'] as $category => $label)
            <div class="space-y-4">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</h4>
                
                @forelse(($assessment->answerSamples ?? collect())->where('category', $category) as $sample)
                    <a href="{{ route('assessment.view-file', ['id' => $sample->id, 'type' => 'sample']) }}" 
                    target="_blank" 
                    class="flex items-center justify-between p-3 bg-slate-50 hover:bg-indigo-50 border border-slate-100 rounded-xl transition">
                        <span class="text-[10px] font-bold text-slate-700 truncate">
                            {{ $sample->filename ?? 'Student_Script.pdf' }}
                        </span>
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400"></i>
                    </a>
                @empty
                <p class="text-[10px] text-slate-400 italic">No files</p>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection