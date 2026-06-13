@extends('layouts.lecturerlayout.app')

@section('title', 'Final Program Verification')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        {{-- Link back to the index verification dashboard route --}}
        <a href="{{ route('kp.verification') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-emerald-600 transition flex items-center gap-1">
            <i data-lucide="chevron-left" class="w-3 h-3"></i> Back to Queue
        </a>
    </div>

    {{-- Validation Error Alerts --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-[2rem] text-sm">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        {{-- Header Section --}}
        <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-start">
            <div class="flex gap-5">
                <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-100">
                    <i data-lucide="folder-check" class="w-8 h-8"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $assessment->title }}</h2>
                    <p class="text-sm text-slate-500 font-medium italic">
                        {{ optional($assessment->subject)->subject_code }} • Coordinator: 
                        <span class="text-slate-900 font-bold">{{ optional($assessment->coordinator)->name ?? 'Unassigned' }}</span>
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-wider border border-emerald-200">Validated by Both SMEs</span>
            </div>
        </div>

        <div class="p-8">
            <form action="{{ route('kp.finalize', $assessment->id) }}" method="POST" id="kpForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" id="actionInput" value="">

                <div class="space-y-10">
                    {{-- 1. Evidence Section --}}
                    <section>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">1. SME Verification</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- SME 1 Review Block --}}
                            <div class="p-5 border border-slate-200 rounded-3xl bg-white shadow-sm">
                                <p class="text-[10px] font-black text-emerald-600 uppercase mb-2">SME 1 Feedback</p>
                                <p class="text-xs text-slate-600 italic">
                                    "{{ $assessment->sme1_comments ?? 'No comments provided.' }}"
                                </p>
                            </div>

                            {{-- SME 2 Review Block --}}
                            <div class="p-5 border border-slate-200 rounded-3xl bg-white shadow-sm">
                                <p class="text-[10px] font-black text-emerald-600 uppercase mb-2">SME 2 Feedback</p>
                                <p class="text-xs text-slate-600 italic">
                                    "{{ $assessment->sme2_comments ?? 'No comments provided.' }}"
                                </p>
                            </div>

                            {{-- Document View Buttons --}}
                            <div class="p-5 border border-slate-200 rounded-3xl bg-slate-50 flex flex-col justify-center gap-2">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1 text-center">Assessment Files</p>
                                <a href="{{ asset('storage/' . $assessment->question_file) }}" target="_blank" class="w-full py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase rounded-xl text-center hover:bg-slate-800 transition">
                                    View Question Paper
                                </a>
                                <a href="{{ asset('storage/' . $assessment->schema_file) }}" target="_blank" class="w-full py-2.5 bg-white text-slate-600 border border-slate-200 text-[10px] font-black uppercase rounded-xl text-center hover:bg-slate-100 transition">
                                    View Answer Scheme
                                </a>
                            </div>
                        </div>
                    </section>

                    {{-- 2. Final Declaration --}}
                    <section class="pt-6 border-t border-slate-100">
                        <div class="flex items-center gap-2 mb-6">
                            <i data-lucide="check-square" class="w-5 h-5 text-emerald-600"></i>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Final Declaration</h3>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">KP Remarks/Comments</label>
                                <textarea 
                                    name="kp_remarks" 
                                    class="w-full h-32 p-5 text-sm bg-slate-50 border border-slate-200 rounded-[2rem] focus:ring-2 focus:ring-emerald-500 outline-none transition italic resize-none" 
                                    placeholder="Add final KP remarks or verification declarations here...">{{ old('kp_remarks', $assessment->kp_comments) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <button type="button" onclick="submitKp('reject')" class="flex items-center justify-center gap-3 py-5 bg-white text-red-600 rounded-3xl text-xs font-black uppercase tracking-widest hover:bg-red-50 border border-red-200 transition">
                                    <i data-lucide="x-circle" class="w-5 h-5"></i> Request Changes
                                </button>
                                <button type="button" onclick="submitKp('approve')" class="flex items-center justify-center gap-3 py-5 bg-emerald-600 text-white rounded-3xl text-xs font-black uppercase tracking-widest hover:bg-emerald-700 transition shadow-xl shadow-emerald-100">
                                    <i data-lucide="archive" class="w-5 h-5"></i> Approve & Archive Assessment
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function submitKp(action) {
        const remarksBox = document.querySelector('textarea[name="kp_remarks"]');
        
        // Require remarks if rejecting back down the pipeline
        if (action === 'reject' && remarksBox.value.trim().length < 5) {
            alert('Please provide some descriptive remarks explaining why you are returning this folder.');
            return;
        }

        if (confirm('Are you sure you want to ' + action.toUpperCase() + ' this assessment folder?')) {
            document.getElementById('actionInput').value = action;
            document.getElementById('kpForm').submit();
        }
    }
</script>
@endsection