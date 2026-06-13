@extends('layouts.lecturerlayout.app')

@section('title', 'Reviewing: ' . $assessment->title)

@section('content')
@php
    $userId = auth()->id();
    
    // Fallback logic: If assessment column is null, check the Subject assignment
    $assignedSme1 = $assessment->sme1_id ?? optional($assessment->subject)->sme1_id;
    $assignedSme2 = $assessment->sme2_id ?? optional($assessment->subject)->sme2_id;

    $isSme1 = ($userId == $assignedSme1);
    
    // Pull the correct status and comments based on whether the user is SME 1 or 2
    $currentStatus = $isSme1 ? $assessment->sme1_status : $assessment->sme2_status;
    $currentComments = $isSme1 ? $assessment->sme1_comments : $assessment->sme2_comments;
@endphp

<div class="max-w-6xl mx-auto space-y-6">
    {{-- Header Section --}}
    <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('sme.verification') }}" class="p-2 hover:bg-slate-100 rounded-xl transition">
                <i data-lucide="arrow-left" class="w-5 h-5 text-slate-500"></i>
            </a>
            <div>
                <h2 class="text-xl font-black text-slate-800">{{ $assessment->title }}</h2>
                <p class="text-xs text-slate-500 font-medium italic">
                    Logged in as SME {{ $isSme1 ? '1' : '2' }} 
                    @if(!$assessment->sme1_id && !$assessment->sme2_id)
                        <span class="text-indigo-500 font-bold ml-1">(Linked via Subject Assignment)</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 {{ $currentStatus == 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : ($currentStatus == 'rejected' ? 'bg-red-50 text-red-600 border-red-200' : 'bg-amber-50 text-amber-600 border-amber-200') }} text-[10px] font-black uppercase rounded-lg border">
                {{ $currentStatus }}
            </span>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-2xl text-sm">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- File Preview Section --}}
        <div class="lg:col-span-1 space-y-4">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Assessment Files</h3>
            
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-red-50 rounded-xl"><i data-lucide="file-text" class="w-5 h-6 text-red-500"></i></div>
                    <h4 class="text-sm font-bold text-slate-800">Question Paper</h4>
                </div>
                <a href="{{ asset('storage/' . $assessment->question_file) }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-3 bg-slate-50 text-slate-600 rounded-xl text-[10px] font-bold hover:bg-slate-100 transition">
                    View Document
                </a>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl"><i data-lucide="check-square" class="w-5 h-6 text-emerald-500"></i></div>
                    <h4 class="text-sm font-bold text-slate-800">Answer Scheme</h4>
                </div>
                <a href="{{ asset('storage/' . $assessment->schema_file) }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-3 bg-slate-50 text-slate-600 rounded-xl text-[10px] font-bold hover:bg-slate-100 transition">
                    View Document
                </a>
            </div>
        </div>

        {{-- Review Form Section --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                @if($currentStatus == 'pending')
                    <form action="{{ route('sme.update-status', $assessment->id) }}" method="POST" id="decision-form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" id="status-input" value="">

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">SME Feedback</label>
                                <textarea 
                                    name="comments" 
                                    required
                                    class="w-full h-48 p-5 text-sm bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition placeholder:text-slate-300 resize-none"
                                    placeholder="Provide detailed feedback (at least 5 characters)...">{{ old('comments', $currentComments) }}</textarea>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <button type="button" onclick="submitDecision('rejected')" class="flex-1 py-4 bg-white text-red-600 border border-red-200 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-50 transition">
                                    Request Changes
                                </button>
                                <button type="button" onclick="submitDecision('approved')" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-700 transition shadow-lg">
                                    Approve Assessment
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    {{-- Read-only view for reviewed items --}}
                    <div class="space-y-6">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Your Previous Feedback</label>
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 italic text-slate-600 text-sm">
                            "{{ $currentComments }}"
                        </div>
                        <div class="flex items-center gap-3 p-4 bg-indigo-50 text-indigo-700 rounded-2xl border border-indigo-100">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            <span class="text-xs font-bold uppercase tracking-wider">Review Locked - Approval Submitted</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function submitDecision(status) {
        const commentBox = document.querySelector('textarea[name="comments"]');
        
        if (commentBox.value.trim().length < 5) {
            alert('Please provide a more detailed comment (minimum 5 characters).');
            return;
        }

        if (confirm('Are you sure you want to ' + status.toUpperCase() + ' this assessment?')) {
            document.getElementById('status-input').value = status;
            document.getElementById('decision-form').submit();
        }
    }
</script>
@endsection