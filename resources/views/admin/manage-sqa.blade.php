@extends('layouts.adminlayout.app')

@section('title', 'QA Management')

@section('content')
<div class="max-w-3xl mx-auto py-10" x-data="{ search: '', courseFilter: 'All' }">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-900">Create New QA Auditor</h2>
        <p class="text-slate-500 text-sm">Register a new QA auditor and assign subjects for quality review.</p>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('admin.sqas.store') }}" method="POST" class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Full Name</label>
                <input type="text" name="name" class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 text-sm font-medium text-slate-700" required>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">User ID</label>
                <input type="text" name="staff_id" class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 text-sm font-medium text-slate-700" required>
            </div>
        </div>

        <div class="mb-8">
            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Initial Password</label>
            <input type="password" name="password" class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 text-sm font-medium text-slate-700" required>
        </div>

        <div class="mb-8">
            <label class="block text-[10px] font-black uppercase text-slate-400 mb-4">Assign Subjects to Audit</label>
            
            {{-- Filters --}}
            <div class="flex gap-3 mb-4">
                <input type="text" x-model="search" placeholder="Search subject..." class="flex-1 border-slate-200 bg-slate-50 rounded-xl p-3 text-xs font-bold">
                <select x-model="courseFilter" class="border-slate-200 bg-slate-50 rounded-xl p-3 text-xs font-bold">
                    <option value="All">All Programs</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- List --}}
            <div class="grid grid-cols-2 gap-4 h-64 overflow-y-auto border border-slate-200 p-5 rounded-2xl bg-slate-50">
                @foreach($subjects as $subject)
                    <label class="flex items-center p-3 bg-white rounded-lg border border-slate-200 hover:border-indigo-300 transition cursor-pointer"
                           x-show="(courseFilter === 'All' || '{{ $subject->course_id }}' === courseFilter) && 
                                   ('{{ strtolower($subject->subject_code) }}'.includes(search.toLowerCase()) || '{{ strtolower($subject->subject_name) }}'.includes(search.toLowerCase()))">
                        <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" class="mr-3 text-indigo-600 rounded focus:ring-indigo-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">{{ $subject->subject_code }}</span>
                            <span class="block text-[10px] text-slate-500 truncate">{{ $subject->subject_name }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-slate-900 text-white font-black text-xs uppercase rounded-xl hover:bg-indigo-600 transition shadow-lg">
                Create QA Auditor
            </button>
        </div>
    </form>
</div>
@endsection