@extends('layouts.lecturerlayout.app')

@section('title', 'Subject Reports')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-8">
    
    {{-- Top Context Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <span class="text-sm font-bold text-indigo-600 uppercase tracking-wider">{{ $subject->subject_code }}</span>
            <h1 class="text-3xl font-black text-slate-900 mt-1">Subject Reports</h1>
            <p class="text-sm text-slate-500 mt-1">
                Subject Reports For: 
                <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded ml-1">
                    Semester {{ $activeSession->name }}
                </span>
            </p>
        </div>
        <div class="text-right">
            @php
                $registeredSectionsCount = $subject->sections->count();
                $totalExpected = count($standardTypes) + ($registeredSectionsCount * 2);
                $totalUploaded = $subject->courseReports->count();
                $percentage = $totalExpected > 0 ? round(($totalUploaded / $totalExpected) * 100) : 0;

                if ($percentage > 100) {
                    $percentage = 100;
                }
            @endphp
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 rounded-xl">
                <span class="text-sm font-bold text-slate-700">Folder Completion Status:</span>
                <span class="text-sm font-black text-indigo-600">{{ $totalUploaded }} / {{ $totalExpected }} ({{ $percentage }}%)</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm font-bold rounded-r-xl">{{ session('success') }}</div>
    @endif

    {{-- Root Alpine State Engine --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ selectedSection: '{{ $subject->sections->first()->id ?? '' }}' }">
        
        {{-- Left Workspace Columns --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Section 1: Midterm Reports Core Block --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="font-black text-slate-900 text-base flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            Mid Term Report
                        </h2>
                        <p class="text-sm text-slate-500 mt-0.5">Select a target section node below to manage files.</p>
                    </div>
                    
                    <div>
                        @if($subject->sections->isNotEmpty())
                            <select x-model="selectedSection" class="text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm">
                                @foreach($subject->sections as $sec)
                                    <option value="{{ $sec->id }}">Section {{ $sec->section_name }} ({{ $sec->lecturer_nameBlock ?? $sec->lecturer_name }})</option>
                                @endforeach
                            </select>
                        @else
                            <span class="text-sm font-bold text-rose-600 bg-rose-50 px-4 py-2.5 rounded-xl border border-rose-100 block">No Class Sections Created</span>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    @if($subject->sections->isNotEmpty())
                        @foreach($subject->sections as $sec)
                            <div x-show="selectedSection == '{{ $sec->id }}'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-1" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <h3 class="text-sm font-black text-slate-800 tracking-wide uppercase">Current Section: {{ $sec->section_name }}</h3>
                                    <span class="text-xs text-slate-400 font-medium">Lecturer: {{ $sec->lecturer_name }}</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @include('subjcoordinator.partials.report-row-card', ['type' => 'midterm_boe', 'label' => 'Midterm BOE Report', 'section' => $sec->id])
                                    @include('subjcoordinator.partials.report-row-card', ['type' => 'midterm_overall', 'label' => 'Midterm Overall Report', 'section' => $sec->id])
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <p class="text-sm text-slate-400 font-medium">Please initialize at least one section tracking node within your setup configuration module before continuing.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Section 2: Standard Administrative Files --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h2 class="font-black text-slate-900 text-base flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                        Subject Report
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($standardTypes as $typeKey => $typeName)
                        @include('subjcoordinator.partials.report-row-card', ['type' => $typeKey, 'label' => $typeName, 'section' => null])
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Explanatory Rules Card Side Panel --}}
        <div class="space-y-6">
            <div class="bg-slate-900 text-slate-100 p-6 rounded-2xl space-y-4 shadow-sm">
                <h3 class="font-black text-sm tracking-wide text-white uppercase">Operational Directives</h3>
                <ul class="space-y-3 text-sm text-slate-300 font-medium">
                    <li class="flex items-start gap-2">✔ <span class="pt-0.5">All assets must be saved as standard PDF formats capped at 10MB per file payload.</span></li>
                    <li class="flex items-start gap-2">✔ <span class="pt-0.5">Uploading a file over an existing entry automatically deletes the old file and applies the update.</span></li>
                    
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection