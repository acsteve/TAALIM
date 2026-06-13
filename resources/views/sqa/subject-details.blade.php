@extends('layouts.sqalayout.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-900">{{ $subject->subject_code }}</h2>
        <p class="text-slate-500 uppercase font-bold text-[10px]">{{ $subject->subject_name }}</p>
    </div>

    <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-900 mb-6">Subject Audit Documents</h3>
        
        <div class="space-y-4">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                <p class="text-sm font-bold text-slate-700">Audit in progress for {{ $subject->subject_name }}</p>
            </div>
        </div>
    </div>
</div>
@endsection