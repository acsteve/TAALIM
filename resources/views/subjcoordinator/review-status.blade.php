@extends('layouts.subjcoordlayout.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="mb-10">
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Review & Approval Status</h2>
        <p class="text-sm text-slate-500 font-medium">Grouped by category</p>
    </div>

    @forelse($assessments as $type => $group)
        <div class="mb-12">
            <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-100">
                    <i data-lucide="layers" class="w-4 h-4 text-white"></i>
                </div>
                <h3 class="text-lg font-black text-slate-700 uppercase tracking-widest">{{ $type }}s</h3>
                <span class="ml-auto bg-slate-100 text-slate-500 text-[10px] font-bold px-2 py-1 rounded-md">{{ $group->count() }}</span>
            </div>

            <div class="grid gap-4">
                @foreach($group as $assessment)
                    <div class="bg-white border border-slate-200 rounded-[2rem] p-6 flex items-center justify-between transition hover:border-indigo-200 hover:shadow-xl">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 flex items-center justify-center bg-slate-50 rounded-2xl border border-slate-100">
                                <i data-lucide="file-check" class="w-6 h-6 text-slate-400"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800">{{ $assessment->title }}</h4>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                    {{ $assessment->subject->subject_code }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase
                                {{ $assessment->status == 'rejected' ? 'bg-red-50 text-red-600' : 
                                   ($assessment->status == 'approved' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600') }}">
                                {{ $assessment->status }}
                            </span>

                            @if($assessment->status == 'rejected')
                                <button 
                                    onclick="openReuploadModal('{{ $assessment->id }}', '{{ addslashes($assessment->sme_comments) }}', '{{ addslashes($assessment->kp_comments) }}')"
                                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-2xl text-xs font-black uppercase hover:bg-indigo-700 transition">
                                    Fix
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-center py-20 bg-slate-50 rounded-[3rem] border border-dashed border-slate-200">
            <p class="text-slate-500">No assessments found.</p>
        </div>
    @endforelse

    {{-- THE MODAL PARTIAL --}}
    @include('subjcoordinator.partials.reupload-modal')
</div>
@endsection