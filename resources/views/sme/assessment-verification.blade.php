@extends('layouts.lecturerlayout.app')

@section('title', 'Assessment Verification')

@section('content')
<div class="max-w-6xl mx-auto space-y-10" x-data="{ tab: 'pending' }">
    
    {{-- Header Section --}}
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Review Assessments</h2>
            <p class="text-slate-500 text-sm font-medium italic">Role: Subject Matter Expert (SME)</p>
            <p class="text-slate-400 text-[10px] font-bold uppercase mt-1">Session: {{ $activeSessionName ?? 'N/A' }}</p>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shadow-inner">
            <button @click="tab = 'pending'" 
                :class="tab === 'pending' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Action Required ({{ sprintf('%02d', $pendingCount) }})
            </button>
            <button @click="tab = 'history'" 
                :class="tab === 'history' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                Review History
            </button>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="relative">
        
        {{-- Tab 1: Pending Reviews --}}
        <div x-show="tab === 'pending'" x-transition class="space-y-8">
            @forelse($pendingAssessments->groupBy('subject_id')->sortKeys() as $subjectId => $items)
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                                {{ $items->first()->subject->name ?? $items->first()->subject->subject_name ?? 'N/A' }}
                            </h3>
                            <p class="text-[12px] font-bold text-slate-400 uppercase mt-1">
                                {{ optional($items->first()->subject)->subject_code }} | Coordinator: {{ optional($items->first()->coordinator)->name }}
                            </p>
                        </div>
                        <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-[11px] font-black uppercase">
                            {{ $items->count() }} Pending
                        </span>
                    </div>

                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 text-[12px] font-black text-slate-400 uppercase">
                            <tr>
                                <th class="px-8 py-4">Assessment Title</th>
                                <th class="px-8 py-4">Date Submitted</th>
                                <th class="px-8 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($items as $assessment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-4 font-bold text-slate-700 text-md">{{ $assessment->title }}</td>
                                <td class="px-8 py-4 text-slate-500 font-bold text-[12px]">
                                    {{ $assessment->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <a href="{{ route('sme.review', $assessment->id) }}" class="text-[12px] font-black uppercase bg-slate-900 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-600 transition">Review</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-[2rem] border border-dashed border-slate-200">
                    <p class="text-slate-400 font-bold uppercase text-[12px]">All caught up! No pending reviews.</p>
                </div>
            @endforelse
        </div>

        {{-- Tab 2: History --}}
        <div x-show="tab === 'history'" x-transition class="space-y-8" style="display: none;">
            @forelse($reviewedAssessments->groupBy('subject_id')->sortKeys() as $subjectId => $histories)
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-100 bg-slate-50">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                            {{ $histories->first()->subject->name ?? $histories->first()->subject->subject_name ?? 'N/A' }}
                        </h3>
                        <p class="text-[12px] font-bold text-slate-400 uppercase mt-1">
                            {{ optional($histories->first()->subject)->subject_code }} | Coordinator: {{ optional($histories->first()->coordinator)->name }}
                        </p>
                    </div>

                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-[12px] font-black text-slate-400 uppercase">
                            <tr>
                                <th class="px-8 py-4">Assessment Title</th>
                                <th class="px-8 py-4">Date Approved</th>
                                <th class="px-8 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($histories as $history)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-4 text-md font-bold text-slate-700">{{ $history->title }}</td>
                                
                                {{-- Date Approved Column --}}
                                <td class="px-8 py-4 text-slate-500 font-bold text-[12px]">
                                    @php 
                                        $isSme1 = ($history->sme1_id == auth()->id());
                                        $approvedAt = $isSme1 ? $history->sme1_verified_at : $history->sme2_verified_at; 
                                    @endphp
                                    {{ $approvedAt ? \Carbon\Carbon::parse($approvedAt)->format('d M Y, h:i A') : 'N/A' }}
                                </td>

                                <td class="px-8 py-4 text-right">
                                    <a href="{{ route('sme.review', $history->id) }}" class="text-[12px] font-black text-indigo-500 uppercase">View Evaluation</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-[2rem] border border-dashed border-slate-200">
                    <p class="text-slate-400 font-bold uppercase text-[12px]">No history found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection