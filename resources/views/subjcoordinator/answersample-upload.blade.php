@extends('layouts.lecturerlayout.app')

@section('title', 'Archive Student Answer Samples')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 pb-6">
        <div>
            <h2 class="text-3xl font-black text-slate-950 tracking-tight">Student Answer Samples Repository</h2>
            
        </div>
        <a href="{{ route('subjcoordinator.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Progress Hub
        </a>
    </div>

    {{-- Session System Messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                <span class="font-semibold text-sm">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-xl flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
                <span class="font-semibold text-sm">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- Left Form Column: 2/3 Width --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('subjcoordinator.samples.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                @csrf
                
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <i data-lucide="folder-open" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Upload Answer Samples</h3>
                        <p class="text-xs text-slate-400 font-medium">Select an approved assessment to upload documents</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Dropdown Element --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Assessment</label>
                        <select name="assessment_id" required 
                            class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4 bg-slate-50 border transition">
                            <option value="" disabled selected>-- Choose Verrified Assessment --</option>
                            @foreach($completedAssessments as $ca)
                                @if($ca->answer_samples_count < 9)
                                    <option value="{{ $ca->id }}">{{ $ca->subject->subject_code ?? '' }} - {{ $ca->title }} (Pending Samples)</option>
                                @else
                                    <option value="{{ $ca->id }}" disabled class="text-slate-400 bg-slate-100">🔒 {{ $ca->title }} (9/9 Complete)</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- 9 Files Structured Section Grid --}}
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-3">Answer Samples (9 PDFs Total)</label>
                        
                        @php
                            $matrix = [
                                ['name' => 'Best', 'bg' => 'bg-emerald-50/60', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'badge' => 'bg-emerald-500', 'file_input' => 'file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200'],
                                ['name' => 'Medium', 'bg' => 'bg-amber-50/60', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'badge' => 'bg-amber-500', 'file_input' => 'file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200'],
                                ['name' => 'Weak', 'bg' => 'bg-rose-50/60', 'border' => 'border-rose-200', 'text' => 'text-rose-800', 'badge' => 'bg-rose-500', 'file_input' => 'file:bg-rose-100 file:text-rose-700 hover:file:bg-rose-200']
                            ];
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($matrix as $cat)
                                <div class="{{ $cat['bg'] }} border {{ $cat['border'] }} rounded-2xl p-4 flex flex-col justify-between space-y-4 shadow-inner">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $cat['badge'] }}"></span>
                                        <h4 class="font-bold text-xs uppercase tracking-widest {{ $cat['text'] }}">{{ $cat['name'] }} Category</h4>
                                    </div>

                                    <div class="space-y-3">
                                        @for($i = 1; $i <= 3; $i++)
                                            <div>
                                                <span class="text-[10px] font-bold text-slate-400 block mb-1">Student Script #{{ $i }}</span>
                                                <input type="file" name="samples[{{ $cat['name'] }}][]" required
                                                    class="block w-full text-xs text-slate-500 cursor-pointer
                                                    file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0
                                                    file:text-[10px] file:font-bold border border-slate-200/60 bg-white rounded-lg p-1 shadow-sm
                                                    {{ $cat['file_input'] }} transition">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Action Footer --}}
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest py-3 px-6 rounded-xl shadow-lg shadow-indigo-100 transition active:scale-95">
                        <i data-lucide="cloud-lightning" class="w-4 h-4"></i> Save & Lock Archives
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Side Column: 1/3 Width (Status Summary Pipeline) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i data-lucide="activity" class="text-indigo-600 w-5 h-5"></i>
                    <h3 class="font-bold text-slate-900 text-sm">Upload Status</h3>
                </div>

                <div class="divide-y divide-slate-100 max-h-[480px] overflow-y-auto pr-2 space-y-3">
                    @forelse($recentUploads as $ru)
                        @php $count = $ru->answerSamples->count(); @endphp
                        <div class="pt-3 first:pt-0 flex items-center justify-between gap-2">
                            <div class="truncate">
                                <h4 class="font-bold text-slate-900 text-sm truncate">{{ $ru->title }}</h4>
                                <p class="text-slate-400 text-[11px] font-semibold uppercase mt-0.5">{{ $ru->subject->subject_code ?? 'N/A' }}</p>
                            </div>
                            
                            {{-- State Pill badge tracker --}}
                            @if($count >= 9)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-full text-emerald-700 font-bold text-[10px] uppercase shadow-sm">
                                    <i data-lucide="check" class="w-3 h-3"></i> 9/9 Saved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 rounded-full text-amber-700 font-bold text-[10px] uppercase shadow-sm">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Empty (0/9)
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs font-medium">
                            No processing workflows active.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') { lucide.createIcons(); }
    });
</script>
@endsection