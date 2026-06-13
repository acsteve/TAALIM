@extends('layouts.lecturerlayout.app')

@section('title', 'Subject Report | ' . $subject->subject_name)

@section('content')
<div class="space-y-6">
    
    {{-- Navigation Breadcrumb Banner Layout Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            {{-- Points to your exact master tracking grid route name --}}
            <a href="{{ route('kp.midterm.audit', ['session' => $session]) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1 mb-2 transition">
                &larr; Back to Master Ledger
            </a>
            <span class="text-xs font-black px-2.5 py-1 bg-slate-100 text-slate-800 rounded-md uppercase tracking-wider">{{ $subject->subject_code }}</span>
            <h1 class="text-2xl font-black text-slate-900 mt-2">{{ $subject->subject_name }}</h1>
            
            {{-- Dynamically renders the session name from the database (e.g., 2025/2026 - Semester 1) --}}
            <p class="text-xs text-slate-500 mt-1">
                Audit Mode: Verifying portfolio archives for 
                <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded ml-1">
                    {{ $sessionName ?? 'Semester ' . $session }}
                </span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Part 1: Section Specific PDF Layout Mapping Loops --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="font-bold text-slate-900 text-sm">Mid Term Report</h2>
                </div>
                <div class="p-5 space-y-6 divide-y divide-slate-100">
                    {{-- UPDATED: Loop through real assigned sections from database relationship instead of numerical counts --}}
                    @if($subject->sections->isNotEmpty())
                        @foreach($subject->sections as $section)
                            <div class="pt-4 first:pt-0 space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Section: {{ $section->section_name }}</h3>
                                    <span class="text-[10px] text-slate-400 font-bold bg-slate-100 px-2 py-0.5 rounded">Lecturer: {{ $section->lecturer_name }}</span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach(['midterm_boe' => 'Midterm BOE Report', 'midterm_overall' => 'Midterm Overall Report'] as $type => $label)
                                        @php
                                            // Mapping lookup key points explicitly to the unique database section row primary id identifier 
                                            $key = $type . '_' . $section->id;
                                            $doc = $uploadedDocs[$key] ?? null;
                                        @endphp
                                        <div class="p-4 border rounded-xl {{ $doc ? 'border-emerald-200 bg-emerald-50/10' : 'border-slate-200 bg-slate-50/50' }}">
                                            <span class="block text-[11px] font-black text-slate-700">{{ $label }}</span>
                                            <div class="mt-2 text-xs">
                                                @if($doc)
                                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-emerald-700 font-bold underline inline-flex items-center gap-1 hover:text-emerald-800">
                                                        View Submission File &rarr;
                                                    </a>
                                                @else
                                                    <span class="text-slate-400 italic">No File Uploaded Yet</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Fallback container visualization if coordinator has not generated options --}}
                        <div class="p-8 text-center text-xs text-slate-400 italic">
                            No class sections have been created or registered under this subject scope yet.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Part 2: Administrative Core Files Module Tracking Grid --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="font-bold text-slate-900 text-sm">Subject Report</h2>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($standardTypes as $typeKey => $typeName)
                        @php $doc = $uploadedDocs[$typeKey] ?? null; @endphp
                        <div class="p-4 border rounded-xl {{ $doc ? 'border-emerald-200 bg-emerald-50/10' : 'border-slate-200 bg-slate-50/50' }}">
                            <span class="block text-[11px] font-black text-slate-700">{{ $typeName }}</span>
                            <div class="mt-2 text-xs">
                                @if($doc)
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-emerald-700 font-bold underline inline-flex items-center gap-1 hover:text-emerald-800">
                                        View Submission File &rarr;
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">No File Uploaded Yet</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>


    </div>
</div>
@endsection