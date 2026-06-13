@extends('layouts.adminlayout.app')
@section('title', 'Workflow Deployment')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="mb-10">
        <h3 class="text-3xl font-black text-slate-900 tracking-tight">Workflow Deployment</h3>
        <p class="text-slate-500 mt-1">Assign the lead Coordinator and SMEs for each subject in <b>{{ $activeSession->session_name }}</b>.</p>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Subject</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Sections</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Assignment Registry</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Deployment</th>
                    <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($subjects as $subject)
                @php $assignment = $subject->assignments->first(); @endphp
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-6 py-5">
                        <div class="font-black text-slate-900">{{ $subject->subject_code }}</div>
                        <div class="text-[11px] text-slate-500 uppercase tracking-tight">{{ $subject->subject_name }}</div>
                    </td>
                    
                    <td class="px-6 py-5">
                        @if($assignment)
                            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md border border-slate-200">
                                {{ $assignment->section }}
                            </span>
                        @else
                            <span class="text-slate-300 text-xs italic">N/A</span>
                        @endif
                    </td>

                    <td class="px-6 py-5">
                        @if($assignment)
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-blue-700">Coord: {{ $assignment->coordinator->name }}</span>
                                <span class="text-[10px] text-slate-500 italic">SME: {{ $assignment->sme1->name }} @if($assignment->sme2) & {{ $assignment->sme2->name }} @endif</span>
                            </div>
                        @else
                            <span class="text-slate-300 text-xs italic">Registry Empty</span>
                        @endif
                    </td>

                    <td class="px-6 py-5">
                        @if($assignment)
                            <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase border border-emerald-100">
                                <i data-lucide="check-circle-2" class="w-3 h-3"></i> Ready
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-black uppercase border border-amber-100">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> Not Deployed
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-5 text-right">
                        <button 
                            x-on:click="$dispatch('open-modal', { 
                                subject_id: {{ $subject->id }}, 
                                code: '{{ $subject->subject_code }}',
                                name: '{{ $subject->subject_name }}',
                                section: '{{ $assignment->section ?? '' }}',
                                coord_id: '{{ $assignment->coordinator_id ?? '' }}',
                                sme1_id: '{{ $assignment->sme1_id ?? '' }}',
                                sme2_id: '{{ $assignment->sme2_id ?? '' }}'
                            })"
                            class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl transition {{ $assignment ? 'text-slate-400 hover:bg-slate-100 hover:text-slate-900' : 'text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white' }}">
                            <i data-lucide="{{ $assignment ? 'edit-3' : 'plus' }}" class="w-3 h-3"></i>
                            {{ $assignment ? 'Update' : 'Deploy' }}
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Unified Assignment Modal -->
<div x-data="{ open: false, data: {} }" 
     x-show="open" 
     @open-modal.window="open = true; data = $event.detail"
     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" x-cloak x-transition>
    
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="open = false">
        <div class="p-6 bg-slate-900 text-white flex justify-between items-center">
            <div>
                <h4 class="font-black tracking-tight" x-text="data.code"></h4>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest" x-text="data.name"></p>
            </div>
            <button @click="open = false" class="text-slate-500 hover:text-white transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.assignments.store') }}" method="POST" class="p-8 space-y-5">
            @csrf
            <input type="hidden" name="subject_id" :value="data.subject_id">
            <input type="hidden" name="academic_session_id" value="{{ $activeSession->id }}">

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Sections Covered</label>
                <input type="text" name="section" x-model="data.section" required placeholder="e.g. 01G, 02G" 
                       class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold mt-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Subject Coordinator</label>
                <select name="coordinator_id" x-model="data.coord_id" required 
                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold mt-1 outline-none appearance-none">
                    <option value="">Select Coordinator</option>
                    @foreach($staff as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-1">SME 1 (Reviewer)</label>
                    <select name="sme1_id" x-model="data.sme1_id" required 
                            class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold mt-1 outline-none appearance-none">
                        <option value="">Select SME</option>
                        @foreach($staff as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-1">SME 2 (Optional)</label>
                    <select name="sme2_id" x-model="data.sme2_id" 
                            class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold mt-1 outline-none appearance-none">
                        <option value="">None</option>
                        @foreach($staff as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-none transition-all mt-4">
                Confirm Deployment
            </button>
        </form>
    </div>
</div>
@endsection