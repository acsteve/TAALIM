@extends('layouts.lecturerlayout.app')

@section('title', 'Assessment List')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Assessment Verification Progress</h2>
            <p class="text-slate-500 font-medium mt-1 text-sm">Monitor your assessment progress and submit required materials.</p>
        </div>
        <a href="{{ route('subjcoordinator.upload') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-xl text-xs font-black shadow-lg hover:bg-indigo-700 transition-all active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i> New Assessment
        </a>
    </div>

    {{-- Success/Error Alerts --}}
    @if(session('success'))
        <div id="alert-success" class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-lg flex justify-between items-center text-sm font-bold">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg flex justify-between items-center text-sm font-bold">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-500"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
    @endif

    {{-- Main Table Container --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full text-left border-separate border-spacing-0">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 py-5 text-[11px] font-black text-slate-700 uppercase tracking-widest border-b border-slate-200">Assessment Details</th>
                    <th class="px-4 py-5 text-[11px] font-black text-slate-700 uppercase tracking-widest text-center border-b border-slate-200">Approval Status</th>
                    <th class="px-4 py-5 text-[11px] font-black text-slate-700 uppercase tracking-widest text-center border-b border-slate-200">Answer Samples</th>
                    <th class="px-6 py-5 text-[11px] font-black text-slate-700 uppercase tracking-widest text-right border-b border-slate-200">Actions</th>
                </tr>
            </thead>
            
            @forelse($assessments as $type => $group)
                <tbody class="border-t border-slate-200">
                    <tr class="bg-slate-900">
                        <td colspan="4" class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></div>
                                <span class="text-[11px] font-black text-white uppercase tracking-[0.2em]">
                                    {{ $type }} <span class="ml-2 text-slate-400 font-bold">({{ $group->count() }})</span>
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>

                <tbody class="divide-y divide-slate-100">
                    @foreach($group as $index => $assessment)
                    @php 
                        $sme1Approved = strtolower(trim($assessment->sme1_status)) === 'approved'; 
                        $sme2Approved = strtolower(trim($assessment->sme2_status)) === 'approved';
                        $bothSmesApproved = ($sme1Approved && $sme2Approved);
                        $kpApproved = strtolower(trim($assessment->kp_status)) === 'approved';
                        $isRejected = (
                            strtolower(trim($assessment->sme1_status)) === 'rejected' || 
                            strtolower(trim($assessment->sme2_status)) === 'rejected' || 
                            strtolower(trim($assessment->kp_status)) === 'rejected'
                        );
                    @endphp
                    <tr class="{{ $index % 2 != 0 ? 'bg-slate-50/50' : 'bg-white' }} hover:bg-indigo-50/20 transition-colors">
                        
                        {{-- Assessment Details --}}
                        <td class="px-6 py-5">
                            <span class="font-bold text-slate-900 text-[14px] block">{{ $assessment->title }}</span>
                            <span class="inline-block mt-1.5 text-[10px] bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded font-black uppercase tracking-wider border border-slate-200">
                                {{ $assessment->subject->subject_code ?? 'N/A' }}
                            </span>
                        </td>

                        {{-- Approval Pipeline --}}
                        <td class="px-4 py-5">
                            <div class="flex flex-col gap-1.5 w-36 mx-auto">
                                @foreach(['SME 1' => $assessment->sme1_status, 'SME 2' => $assessment->sme2_status, 'KP' => $assessment->kp_status] as $label => $status)
                                    <div class="flex items-center justify-between bg-white px-3 py-1.5 rounded border border-slate-200 shadow-sm">
                                        <span class="text-[9px] font-black text-slate-400 uppercase">{{ $label }}</span>
                                        <span class="text-[10px] font-black uppercase {{ strtolower($status) == 'approved' ? 'text-emerald-600' : (strtolower($status) == 'rejected' ? 'text-red-600' : 'text-amber-500') }}">
                                            @if($label == 'KP' && !$bothSmesApproved && $status == 'pending') LOCKED @else {{ $status }} @endif
                                        </span>
                                    </div>
                                @endforeach

                                @if($isRejected)
                                    <button 
                                        @click="$dispatch('open-reupload', { id: '{{ $assessment->id }}', title: '{{ addslashes($assessment->title) }}', sme: '{{ addslashes($assessment->sme1_comments) }}', kp: '{{ addslashes($assessment->kp_comments) }}' })" 
                                        class="mt-1 w-full py-1.5 bg-red-600 text-white text-[10px] font-black uppercase rounded shadow-sm hover:bg-red-700 transition">
                                        Fix Corrections
                                    </button>
                                @endif
                            </div>
                        </td>

                        {{-- Answer Samples --}}
                        <td class="px-4 py-5 text-center">
                            @if($kpApproved)
                                <a href="{{ route('subjcoordinator.answersample') }}" class="inline-flex flex-col items-center gap-1 group">
                                    <div class="p-2.5 bg-white border border-slate-200 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                        <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase text-blue-600">Upload</span>
                                </a>
                            @else
                                <div class="opacity-40"><i data-lucide="lock" class="w-5 h-5 mx-auto text-slate-400"></i></div>
                            @endif
                        </td>

                        {{-- Actions Column --}}
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end items-center gap-2">
                                @if($kpApproved)
                                    <a href="{{ route('subjcoordinator.folder', $assessment->id) }}" class="p-2.5 bg-white border border-slate-200 text-indigo-600 hover:bg-indigo-50 rounded-xl shadow-sm" title="View Folder">
                                        <i data-lucide="folder-check" class="w-5 h-5"></i>
                                    </a>
                                @elseif(!$sme1Approved && !$sme2Approved)
                                    <button @click="$dispatch('open-reupload', { id: '{{ $assessment->id }}', title: '{{ addslashes($assessment->title) }}', sme: '', kp: '' })" class="p-2.5 text-amber-500 hover:bg-amber-50 rounded-xl" title="Update">
                                        <i data-lucide="file-up" class="w-5 h-5"></i>
                                    </button>
                                    <form action="{{ route('subjcoordinator.destroy', $assessment->id) }}" method="POST" onsubmit="return confirm('Confirm deletion?');">
                                        @csrf @method('DELETE')
                                        <button class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                                    </form>
                                @else
                                    <i data-lucide="lock-keyhole" class="w-5 h-5 text-slate-300"></i>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            @empty
                <tbody>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-bold text-sm">No assessments found</td>
                    </tr>
                </tbody>
            @endforelse
        </table>
    </div>
</div>
@include('subjcoordinator.partials.reupload-modal')
@endsection