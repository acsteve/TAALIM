@extends('layouts.lecturerlayout.app')

@section('title', 'Upload Assessment Portfolio')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    
    {{-- Top Heading Header Profile --}}
    <div class="flex flex-col gap-1">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Upload Assessment</h2>
        
    </div>

    {{-- Success Alert Notification Banner --}}
    @if(session('success'))
        <div id="alert-success" class="p-5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex justify-between items-center shadow-sm transition-all duration-500">
            <div class="flex items-center gap-2.5 text-sm font-bold">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <form action="{{ route('subjcoordinator.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">

        {{-- Premium Read-Only Context Banner Layout --}}
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-8 rounded-2xl border border-slate-950 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                
                <h3 class="text-xl font-black text-white mt-1">{{ $subject->subject_name }}</h3>
            </div>
            <div class="text-right">
                <span class="text-sm font-black bg-indigo-600 text-white px-4 py-1.5 rounded-xl border border-emerald-500/20 uppercase tracking-wider">
                    {{ $subject->subject_code }}
                </span>
            </div>
        </div>

        {{-- Main Metadata Form Architecture --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[13px] font-black uppercase tracking-wider text-slate-700 mb-2">Assessment Type</label>
                    <select name="assessment_type" class="w-full text-sm font-medium border-slate-200 rounded-xl p-4 bg-slate-50/50 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                        <option value="quiz">Quiz</option>
                        <option value="test">Test</option>
                        <option value="assignment">Assignment</option>
                        <option value="final">Final Examination</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[13px] font-black uppercase tracking-wider text-slate-700 mb-2">Assessment Title</label>
                    <input type="text" name="title" required placeholder="e.g., Quiz 1, Project Phase A" 
                        class="w-full text-sm font-medium border-slate-200 rounded-xl p-4 bg-slate-50/50 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Structural Document Upload Targets --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Question Paper Block --}}
                <div class="space-y-3">
                    <label class="block text-[13px] font-black uppercase tracking-wider text-slate-700">Question Paper (PDF)</label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-2xl p-8 bg-slate-50/30 hover:bg-indigo-50/5 transition-all text-center group relative cursor-pointer">
                        <input type="file" name="question_file" accept=".pdf" required class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10 file-input-field">
                        <div class="space-y-3 pointer-events-none layout-feedback-container">
                            <svg class="w-9 h-9 mx-auto text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"></path>
                            </svg>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-slate-900">Click to locate file binary</p>
                            <p class="text-[12px] text-slate-400">PDF standard documentation limits up to 10MB</p>
                        </div>
                    </div>
                </div>

                {{-- Marking Scheme Block --}}
                <div class="space-y-3">
                    <label class="block text-[13px] font-black uppercase tracking-wider text-slate-700">Marking Scheme (PDF)</label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-2xl p-8 bg-slate-50/30 hover:bg-indigo-50/5 transition-all text-center group relative cursor-pointer">
                        <input type="file" name="schema_file" accept=".pdf" required class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10 file-input-field">
                        <div class="space-y-3 pointer-events-none layout-feedback-container">
                            <svg class="w-9 h-9 mx-auto text-slate-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-slate-900">Click to locate file binary</p>
                            <p class="text-[12px] text-slate-400">PDF standard documentation limits up to 10MB</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Submit Trigger --}}
            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-7 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-md hover:shadow transition duration-150 flex items-center gap-1.5">
                    Upload &rarr;
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    setTimeout(function() {
        const alert = document.getElementById('alert-success');
        if (alert) {
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);

    document.querySelectorAll('.file-input-field').forEach(input => {
        input.addEventListener('change', function(e) {
            const container = this.nextElementSibling;
            if (this.files && this.files[0]) {
                const filename = this.files[0].name;
                container.innerHTML = `
                    <div class="p-2 space-y-2">
                        <svg class="w-8 h-8 mx-auto text-emerald-500 animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-black text-emerald-700 truncate max-w-xs mx-auto">${filename}</p>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-tight">Stage Locked & Ready</p>
                    </div>
                `;
                this.parentElement.className = "border-2 border-dashed border-emerald-300 rounded-2xl p-8 bg-emerald-50/10 text-center group relative cursor-pointer";
            }
        });
    });
</script>
@endsection