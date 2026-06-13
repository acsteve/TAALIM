@extends('layouts.adminlayout.app')
@section('title', 'Manage Subjects')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ 
    showModal: false, 
    editMode: false,
    search: '', 
    courseFilter: 'All',
    smeSearch: '', 
    formData: { 
        id: '', code: '', name: '', course_id: '', 
        coordinator_id: '', sme1_id: '', sme2_id: '' 
    },
    
    openAddModal() {
        this.editMode = false;
        this.formData = { id: '', code: '', name: '', course_id: '', coordinator_id: '', sme1_id: '', sme2_id: '' };
        this.showModal = true;
    },
    
    openEditModal(subject) {
        this.editMode = true;
        this.formData = { 
            id: subject.id, 
            code: subject.subject_code, 
            name: subject.subject_name, 
            course_id: subject.course_id,
            coordinator_id: subject.coordinator_id || '',
            sme1_id: subject.sme1_id || '',
            sme2_id: subject.sme2_id || ''
        };
        this.showModal = true;
    }
}">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Manage Subject</h3>
            <p class="text-slate-500 text-sm font-medium">Subject Library</p>
        </div>
        
        <button @click="openAddModal()" class="px-6 py-3 bg-blue-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Add New Subject
        </button>
    </div>

    <div class="bg-slate-900 p-4 rounded-3xl shadow-xl mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
            <input type="text" x-model="search" placeholder="Search subject code or name..." 
                class="w-full bg-slate-800 border-slate-700 text-white rounded-xl pl-11 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
        </div>
        
        <select x-model="courseFilter" class="bg-slate-800 border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition outline-none cursor-pointer">
            <option value="All">All Courses</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Subject</th>
                    <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest">Program</th>
                    <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">Coordinator</th>
                    <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-center">SME Reviews</th>
                    <th class="px-6 py-5 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($subjects as $subject)
                <tr class="hover:bg-slate-50/30 transition group" 
                    x-show="(courseFilter === 'All' || '{{ $subject->course_id }}' === courseFilter) && 
                            ('{{ strtolower($subject->subject_code) }}'.includes(search.toLowerCase()) || '{{ strtolower($subject->subject_name) }}'.includes(search.toLowerCase()))">
                    <td class="px-6 py-5">
                        <div class="flex flex-col">
                            <span class="font-black text-blue-600 text-sm tracking-tight">{{ $subject->subject_code }}</span>
                            <span class="font-bold text-slate-700 text-xs">{{ $subject->subject_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter border border-slate-200 px-2 py-1 rounded-md">{{ $subject->course->course_code ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="text-xs font-bold {{ $subject->coordinator ? 'text-slate-700' : 'text-rose-400 italic' }}">
                            {{ $subject->coordinator->name ?? 'Not Assigned' }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-[9px] font-bold text-slate-500 uppercase">SME1: {{ $subject->sme1->name ?? '---' }}</span>
                            <span class="text-[9px] font-bold text-slate-500 uppercase">SME2: {{ $subject->sme2->name ?? '---' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex justify-end gap-1">
                            <button @click="openEditModal({{ $subject }})" class="p-2 text-slate-400 hover:text-blue-600 transition">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete subject?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-8 py-20 text-center text-slate-400 italic">No subjects found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
        
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl relative z-10 overflow-hidden" x-transition>
            <form :action="editMode ? '/admin/subjects/' + formData.id : '{{ route('admin.subjects.store') }}'" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="editMode ? 'PATCH' : 'POST'">

                <div class="p-10">
                    <h4 class="text-2xl font-black text-slate-900 mb-8" x-text="editMode ? 'Subject Assignment' : 'New Subject'"></h4>

                    @if ($errors->any())
                        <div class="p-4 mb-6 text-xs font-bold text-rose-600 bg-rose-50 rounded-2xl">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Subject Code & Name</label>
                                <input type="text" name="subject_code" x-model="formData.code" placeholder="Code" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold mb-2">
                                <input type="text" name="subject_name" x-model="formData.name" placeholder="Full Name" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-medium">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">Program</label>
                                <select name="course_id" x-model="formData.course_id" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold">
                                    <option value="">Select Program</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4 bg-blue-50/50 p-6 rounded-[2rem] border border-blue-100" x-data="{ smeSearch: '' }">
                            <input type="text" x-model="smeSearch" placeholder="Filter names..." class="w-full bg-white border-blue-200 rounded-xl px-4 py-2 text-xs font-bold mb-2 focus:ring-2 focus:ring-blue-200 outline-none">
                            
                            <div>
                                <label class="text-[10px] font-black uppercase text-blue-500 ml-1">Subject Coordinator</label>
                                <select name="coordinator_id" x-model="formData.coordinator_id" class="w-full bg-white border-blue-200 rounded-xl px-4 py-2 text-sm">
                                    <option value="">Choose Coordinator</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" x-show="'{{ strtolower($lecturer->name) }}'.includes(smeSearch.toLowerCase())">
                                            {{ $lecturer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">SME Reviewer 1</label>
                                <select name="sme1_id" x-model="formData.sme1_id" class="w-full bg-white border-slate-200 rounded-xl px-4 py-2 text-sm">
                                    <option value="">Choose SME 1</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" x-show="'{{ strtolower($lecturer->name) }}'.includes(smeSearch.toLowerCase())">{{ $lecturer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-1">SME Reviewer 2</label>
                                <select name="sme2_id" x-model="formData.sme2_id" class="w-full bg-white border-slate-200 rounded-xl px-4 py-2 text-sm">
                                    <option value="">Choose SME 2</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" x-show="'{{ strtolower($lecturer->name) }}'.includes(smeSearch.toLowerCase())">{{ $lecturer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="text-xs font-black uppercase text-slate-400 px-6">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-10 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 shadow-xl shadow-blue-600/20">
                        <span x-text="editMode ? 'Save Assignments' : 'Create Subject'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection