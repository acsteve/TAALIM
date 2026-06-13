@extends('layouts.lecturerlayout.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ tab: '{{ $coordinatorSubjects->isNotEmpty() ? 'coordinator' : ($smeSubjects->isNotEmpty() ? 'sme' : 'kp') }}' }">
    
    <div class="mb-10">
        <h2 class="text-3xl font-black text-slate-900">Lecturer Dashboard</h2>
        <p class="text-slate-500">Welcome back, {{ auth()->user()->name }}. Here are your active academic assignments.</p>
    </div>

    <div class="flex flex-wrap gap-2 mb-8 bg-slate-100 p-1.5 rounded-2xl w-fit">
        @if($coordinatorSubjects->isNotEmpty())
            <button @click="tab = 'coordinator'" :class="tab === 'coordinator' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500'" 
                class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition">
                Coordinator
            </button>
        @endif

        @if($smeSubjects->isNotEmpty())
            <button @click="tab = 'sme'" :class="tab === 'sme' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" 
                class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition">
                SME
            </button>
        @endif

        @if($kpPrograms->isNotEmpty())
            <button @click="tab = 'kp'" :class="tab === 'kp' ? 'bg-white shadow-sm text-emerald-600' : 'text-slate-500'" 
                class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition">
                Ketua Program (KP)
            </button>
        @endif
    </div>

    <div class="space-y-6">
        <div x-show="tab === 'coordinator'" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($coordinatorSubjects as $subject)
                @php
                    $reportCount = $subject->course_reports_count;
                    $reportPercentage = min(round(($reportCount / 11) * 100), 100);
                @endphp

                <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:border-blue-300 transition group flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg font-black text-xs tracking-wide">
                                {{ $subject->subject_code }}
                            </span>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-4 text-lg">{{ $subject->subject_name }}</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-[10px] font-black uppercase text-slate-500 mb-1">
                                    <span>Report: {{ $reportPercentage }}% Uploaded</span>
                                    <span>{{ $reportCount }} / 11</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-blue-600 h-full transition-all" style="width: {{ $reportPercentage }}%"></div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase text-slate-400">Total Assessments</span>
                                    <span class="font-bold text-slate-700 text-sm">{{ $subject->assessments_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 grid grid-cols-2 gap-2 mt-4">
                        <a href="{{ route('subjcoordinator.reports.index', [
                            'subject_id' => $subject->id, 
                            'session' => $activeSession->id ?? 1 
                        ]) }}" 
                        class="text-center px-3 py-2 bg-slate-50 hover:bg-blue-50 text-slate-600 hover:text-blue-700 font-bold text-[10px] uppercase rounded-xl transition">
                            View Report
                        </a>
                        <a href="{{ route('subjcoordinator.index') }}" 
                        class="text-center px-3 py-2 bg-slate-50 hover:bg-blue-50 text-slate-600 hover:text-blue-700 font-bold text-[10px] uppercase rounded-xl transition">
                            View Assessment
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div x-show="tab === 'sme'" x-transition>
            <div class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">Subject</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">Coordinator</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase text-slate-400 tracking-wider text-center">Pending Reviews</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($smeSubjects as $subject)
                            @php
                                $userId = auth()->id();
                                // Logic: Filter based on SME assignment at Assessment OR Subject level
                                $pendingCount = $subject->assessments->filter(function ($assessment) use ($userId, $subject) {
                                    $isSme1 = ($assessment->sme1_id == $userId || $subject->sme1_id == $userId);
                                    $isSme2 = ($assessment->sme2_id == $userId || $subject->sme2_id == $userId);
                                    
                                    $isSme1Pending = $isSme1 && $assessment->sme1_status === 'pending';
                                    $isSme2Pending = $isSme2 && $assessment->sme2_status === 'pending';
                                    
                                    return $isSme1Pending || $isSme2Pending;
                                })->count();
                            @endphp

                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $subject->subject_code }}</span>
                                        <span class="text-xs text-slate-500">{{ $subject->subject_name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-sm text-slate-600 font-medium">
                                    {{ $subject->coordinator->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="{{ $pendingCount > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-400' }} px-3 py-1 rounded-full font-black text-xs">
                                        {{ $pendingCount }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="bg-slate-50 p-6 border-t border-slate-200 flex justify-end">
                    <a href="{{ url('sme/assessment-verification') }}" 
                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase rounded-xl transition shadow-lg shadow-indigo-600/20">
                        Go to Assessment Verification &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div x-show="tab === 'kp'" x-transition>
            @foreach($kpPrograms as $program)
            @php
                // Calculate totals for this specific program
                $totalSubjects = $program->subjects->count();
                
                // Count assessments across all subjects in this program that are pending KP approval
                $pendingApprovalCount = $program->subjects->flatMap->assessments
                    ->where('kp_status', 'pending') // Adjust this key based on your database column
                    ->count();
            @endphp

            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="text-emerald-600 font-black text-[10px] uppercase tracking-widest block mb-1">Program Overview</span>
                    <h3 class="text-2xl font-black text-slate-900">{{ $program->course_name }}</h3>
                </div>

                <div class="flex gap-6">
                    <!-- Total Subjects Stat -->
                    <div class="text-center">
                        <span class="block text-2xl font-black text-slate-900">{{ $totalSubjects }}</span>
                        <span class="text-[10px] font-bold uppercase text-slate-400">Total Subjects</span>
                    </div>
                    
                    <!-- Pending Approval Stat -->
                    <div class="text-center">
                        <span class="block text-2xl font-black {{ $pendingApprovalCount > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                            {{ $pendingApprovalCount }}
                        </span>
                        <span class="text-[10px] font-bold uppercase text-slate-400">Pending Approvals</span>
                    </div>
                </div>

                <a href="{{ route('kp.verification', ['program_id' => $program->id]) }}" 
                class="px-8 py-3 bg-slate-900 hover:bg-emerald-600 text-white font-black text-xs uppercase rounded-xl transition shadow-lg shadow-slate-900/10">
                    Review Approvals &rarr;
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection