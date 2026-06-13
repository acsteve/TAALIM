@extends('layouts.sqalayout.app')
@section('title', 'Assessment Folder | ' . $assessment->title)

@section('content')
<div class="max-w-5xl mx-auto py-10 space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center text-sm text-gray-500 gap-2 mb-2">
        <a href="{{ route('sqa.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 font-semibold">{{ $assessment->title }} Folder</span>
    </nav>

    {{-- Header Info --}}
    <div class="bg-white border-t-8 border-indigo-600 rounded-2xl shadow-md p-8 relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">{{ $assessment->title }}</h1>
                    <p class="text-lg text-indigo-600 font-medium mt-1">
                        {{ $assessment->subject->subject_code ?? 'N/A' }} - {{ $assessment->subject->subject_name ?? 'N/A' }} 
                        <span class="text-gray-400">({{ $assessment->session }})</span>
                    </p>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Coordinator</p>
                    <p class="text-sm font-bold text-gray-800">{{ $assessment->subject->coordinator->name ?? 'N/A' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Validated By (KP)</p>
                    <p class="text-sm font-bold text-gray-800">{{ $assessment->kp->name ?? 'Pending' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Validated By (SME)</p>
                    <p class="text-sm font-bold text-gray-800">
                        {{ $assessment->sme1->name ?? '---' }} & {{ $assessment->sme2->name ?? '---' }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date Finalized</p>
                    <p class="text-sm font-bold text-gray-800">{{ $assessment->updated_at ? $assessment->updated_at->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Files Section --}}
    <div class="grid md:grid-cols-2 gap-6">
        @foreach(['Question Paper' => 'question_file', 'Marking Scheme' => 'schema_file'] as $label => $field)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4">Approved {{ $label }}</h3>
            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-200">
                <span class="text-sm text-gray-600 truncate">{{ basename($assessment->$field) }}</span>
                <a href="{{ Storage::url($assessment->$field) }}" target="_blank" class="text-indigo-600 font-black text-xs uppercase">View</a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Student Samples Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h3 class="font-bold text-gray-800">Student Answer Samples</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach(['best' => 'green', 'medium' => 'yellow', 'weak' => 'red'] as $category => $color)
            <div class="space-y-3">
                <h4 class="text-xs font-black text-{{ $color }}-600 uppercase">{{ ucfirst($category) }} Samples</h4>
                @forelse(($assessment->answerSamples ?? collect())->where('category', $category) as $sample)
                <a href="{{ Storage::url($sample->file_path) }}" target="_blank" class="block p-3 bg-gray-50 rounded-xl hover:bg-gray-100 text-xs text-gray-700">
                    {{ $sample->filename ?? 'View Sample' }}
                </a>
                @empty
                <p class="text-[10px] text-gray-400 italic">No files.</p>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection