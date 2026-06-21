@extends('layouts.sqalayout.app')
@section('title', 'Academic Records')

@section('content')
<div class="max-w-6xl mx-auto py-10">
    <h2 class="text-3xl font-black text-slate-900 mb-10">Verified Records</h2>

    @forelse($subjects as $subject)
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm mb-8 overflow-hidden">
        <div class="bg-slate-50 px-8 py-6 border-b border-slate-200">
            <h3 class="text-lg font-black text-slate-800">{{ $subject->subject_code }} - {{ $subject->subject_name }}</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="border-r border-slate-100">
                <div class="px-8 py-4 text-[10px] font-black uppercase text-slate-400">Completed Assessments</div>
                <ul class="divide-y divide-slate-100">
                    @forelse($subject->assessments as $assessment)
                        <li class="px-8 py-4 flex justify-between items-center text-sm">
                            <div class="flex flex-col">
                                <span class="text-slate-700 font-bold">{{ $assessment->title }}</span>
                                <span class="text-[10px] text-slate-400 font-black uppercase">
                                    {{ $assessment->updated_at ? $assessment->updated_at->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            
                            <a href="{{ route('sqa.assessment.show', $assessment->id) }}" 
                               class="px-4 py-2 bg-slate-900 hover:bg-indigo-600 text-white font-black text-[10px] uppercase rounded-xl transition shadow-lg">
                                View Full Info
                            </a>
                        </li>
                    @empty
                        <li class="px-8 py-4 text-xs text-slate-400 italic">No completed assessments.</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <div class="px-8 py-4 text-[10px] font-black uppercase text-slate-400">Course Reports</div>
                <ul class="divide-y divide-slate-100">
                    @forelse($subject->courseReports as $report)
                        <li class="px-8 py-4 flex justify-between items-center text-sm">
                            <span class="text-slate-700 font-medium capitalize">{{ $report->type }}</span>
                            <a href="{{ asset('storage/' . $report->file_path) }}" 
                               target="_blank"
                               class="text-indigo-600 font-bold text-[10px] uppercase hover:underline">
                               View
                            </a>
                        </li>
                    @empty
                        <li class="px-8 py-4 text-xs text-slate-400 italic">No reports uploaded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-20 text-slate-400 italic">
        No subjects found with completed assessments or uploaded reports.
    </div>
    @endforelse
</div>
@endsection