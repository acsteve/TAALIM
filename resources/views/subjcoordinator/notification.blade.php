@extends('layouts.subjcoordinator.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Notification History</h2>
            <p class="text-gray-500 text-sm">Stay updated with reviews from KP and SME.</p>
        </div>
        <button class="text-xs font-bold text-gray-400 hover:text-red-500 transition uppercase tracking-widest">Clear All</button>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 divide-y divide-gray-50 overflow-hidden">
        
        <div class="p-6 flex items-start gap-5 hover:bg-blue-50/30 transition relative group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-600"></div>
            <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Material Rejected by SME</h4>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            Prof. Michael Wong requested changes on <span class="font-bold text-gray-800">Final Exam Paper</span>. Please check the feedback and re-upload.
                        </p>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase whitespace-nowrap">10:45 AM</span>
                </div>
                <div class="mt-4 flex gap-3">
                    <a href="{{ url('subjcoordinator/assessment-status') }}" class="px-3 py-1.5 bg-blue-600 text-white text-[10px] font-bold rounded-lg hover:bg-blue-700 transition">View Comment</a>
                </div>
            </div>
        </div>

        <div class="p-6 flex items-start gap-5 hover:bg-gray-50 transition grayscale opacity-60">
            <div class="w-10 h-10 rounded-2xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600"></i>
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Material Approved by KP</h4>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            Dr. Sarah Johnson has validated the <span class="font-bold text-gray-800">Assignment 2</span> materials.
                        </p>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Yesterday</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection