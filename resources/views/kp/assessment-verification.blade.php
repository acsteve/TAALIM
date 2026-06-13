@extends('layouts.lecturerlayout.app')

@section('title', 'Assessments Approval')

@section('content')
<div class="max-w-6xl mx-auto space-y-10" x-data="{ tab: 'pending' }">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Assessments Approval List</h2>
            <p class="text-slate-500 text-sm font-medium">Manage Assessment Approvals</p>
        </div>
        
        {{-- Tab Navigation --}}
        <div class="flex bg-slate-100 p-1 rounded-2xl border border-slate-200">
            <button @click="tab = 'pending'" 
                :class="tab === 'pending' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Pending Approval ({{ $pendingKpAssessments->count() }})
            </button>
            <button @click="tab = 'progress'" 
                :class="tab === 'progress' ? 'bg-white text-amber-600 shadow-sm' : 'text-slate-500'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                In Progress ({{ $inProgressAssessments->count() }})
            </button>
            <button @click="tab = 'archived'" 
                :class="tab === 'archived' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Archived
            </button>
        </div>
    </div>

    {{-- Helper for grouping --}}
    @php
        $groupData = function($collection) {
            return $collection->groupBy('subject_id')->sortKeys();
        };
    @endphp

    {{-- Tab 1: Pending KP Approval --}}
    <div x-show="tab === 'pending'" x-transition class="space-y-8">
        @forelse($groupData($pendingKpAssessments) as $subjectId => $items)
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">{{ optional($items->first()->subject)->subject_name }}</h3>
                    <p class="text-[12px] font-bold text-slate-400 uppercase mt-1">{{ optional($items->first()->subject)->subject_code }}</p>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase">
                        <tr>
                            <th class="px-8 py-4">Assessment Title</th>
                            <th class="px-8 py-4">SME Status</th>
                            <th class="px-8 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($items as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-4 font-bold text-slate-700">{{ $item->title }}</td>
                            <td class="px-8 py-4">
                                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100 uppercase">Verified</span>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <a href="{{ route('kp.review', $item->id) }}" class="text-[11px] font-black uppercase bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-emerald-600 transition">Review</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center py-20 border-2 border-dashed border-slate-200 rounded-[2rem] text-slate-400 font-bold uppercase text-[12px]">No pending approvals.</div>
        @endforelse
    </div>

    {{-- Tab 2: In Progress --}}
    <div x-show="tab === 'progress'" x-transition style="display: none;" class="space-y-8">
        @forelse($groupData($inProgressAssessments) as $subjectId => $items)
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-sm font-black text-slate-800 uppercase">{{ optional($items->first()->subject)->subject_name }}</h3>
                </div>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-100">
                        @foreach($items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-8 py-4 font-bold text-slate-700">{{ $item->title }}</td>
                            <td class="px-8 py-4 text-[10px] font-black uppercase text-slate-500">
                                SME 1: {{ $item->sme1_status }} | SME 2: {{ $item->sme2_status }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center py-20 border-2 border-dashed border-slate-200 rounded-[2rem] text-slate-400 font-bold uppercase text-[12px]">All active assessments cleared.</div>
        @endforelse
    </div>

    {{-- Tab 3: Archived --}}
    <div x-show="tab === 'archived'" x-transition style="display: none;" class="space-y-8">
        @forelse($groupData($archivedAssessments) as $subjectId => $items)
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-sm font-black text-slate-800 uppercase">{{ optional($items->first()->subject)->subject_name }}</h3>
                </div>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-100">
                        @foreach($items as $item)
                        <tr class="bg-emerald-50/10">
                            <td class="px-8 py-4 font-bold text-slate-700">{{ $item->title }}</td>
                            <td class="px-8 py-4 text-[10px] text-slate-400 font-black uppercase">
                                {{ $item->kp_verified_at ? \Carbon\Carbon::parse($item->kp_verified_at)->format('d M Y') : 'N/A' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center py-20 border-2 border-dashed border-slate-200 rounded-[2rem] text-slate-400 font-bold uppercase text-[12px]">No archived assessments.</div>
        @endforelse
    </div>
</div>
@endsection