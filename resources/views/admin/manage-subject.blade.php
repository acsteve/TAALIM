@extends('layouts.adminlayout.app')
@section('title', 'Manage Subjects')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ 
    showModal: {{ $errors->any() ? 'true' : 'false' }}, 
    showCreateCsv: false,
    showAssignCsv: false,
    editMode: false,
    search: '', 
    courseFilter: 'All',
    smeSearch: '',
    assignedIds: {{ json_encode($assignedLecturerIds) }},
    formData: { id: '', code: '', name: '', course_id: '', coordinator_id: '', sme1_id: '', sme2_id: '' },
    
    isCoordinatorBusy(lecturerId) {
        return this.assignedIds.includes(lecturerId) && this.formData.coordinator_id != lecturerId;
    },
    openAddModal() {
        this.editMode = false;
        this.formData = { id: '', code: '', name: '', course_id: '', coordinator_id: '', sme1_id: '', sme2_id: '' };
        this.showModal = true;
    },
    openEditModal(subject) {
        this.editMode = true;
        this.formData = { 
            id: subject.id, code: subject.subject_code, name: subject.subject_name, 
            course_id: subject.course_id, coordinator_id: subject.coordinator_id || '',
            sme1_id: subject.sme1_id || '', sme2_id: subject.sme2_id || ''
        };
        this.showModal = true;
    }
}">

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl mb-6 text-xs font-bold">{{ session('success') }}</div>
    @endif

    {{-- Error Modal for Bulk CSV Imports --}}
    @if (session('import_errors'))
        <div x-data="{ showErrors: true }" x-show="showErrors" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showErrors = false"></div>
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg p-8 relative z-10 max-h-[80vh] flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-xl font-black text-rose-600">Import Errors</h4>
                    <button @click="showErrors = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x"></i></button>
                </div>
                <div class="overflow-y-auto flex-1 pr-2 space-y-2">
                    @foreach (session('import_errors') as $error)
                        <div class="text-xs font-medium text-rose-800 bg-rose-50 p-3 rounded-xl border border-rose-100">{{ $error }}</div>
                    @endforeach
                </div>
                <button @click="showErrors = false" class="mt-6 w-full bg-slate-900 text-white py-3 rounded-2xl font-black text-xs uppercase">Dismiss</button>
            </div>
        </div>
    @endif
    
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Manage Subject</h3>
            <p class="text-slate-500 text-sm font-medium">Subject Library</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="px-4 py-3 bg-white border-2 border-slate-200 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i> Templates
                </button>
                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-20">
                    <a href="{{ route('admin.subjects.download.template', 'create') }}" class="block px-4 py-2 text-[10px] font-bold text-slate-600 hover:bg-slate-50 rounded-xl uppercase">Create Template</a>
                    <a href="{{ route('admin.subjects.download.template', 'assign') }}" class="block px-4 py-2 text-[10px] font-bold text-slate-600 hover:bg-slate-50 rounded-xl uppercase">Assignment Template</a>
                </div>
            </div>

            <button type="button" @click="showCreateCsv = true" class="px-6 py-3 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition flex items-center gap-2">
                <i data-lucide="upload" class="w-4 h-4"></i> Import Subjects
            </button>
            <button type="button" @click="showAssignCsv = true" class="px-6 py-3 bg-amber-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4"></i> Import Staff Assignments
            </button>
            <button type="button" @click="openAddModal()" class="px-6 py-3 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Add New Subject
            </button>
        </div>
    </div>

    <div class="bg-slate-900 p-4 rounded-3xl shadow-xl mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
            <input type="text" x-model="search" placeholder="Search subject code or name..." class="w-full bg-slate-800 border-slate-700 text-white rounded-xl pl-11 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
        </div>
        <select x-model="courseFilter" class="bg-slate-800 border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition outline-none cursor-pointer">
            <option value="All">All Courses</option>
            @foreach($courses as $course) <option value="{{ $course->id }}">{{ $course->course_name }}</option> @endforeach
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
                <tr class="hover:bg-slate-50/30 transition" x-show="(courseFilter === 'All' || '{{ $subject->course_id }}' === courseFilter) && ('{{ strtolower($subject->subject_code) }}'.includes(search.toLowerCase()) || '{{ strtolower($subject->subject_name) }}'.includes(search.toLowerCase()))">
                    <td class="px-6 py-5">
                        <div class="flex flex-col"><span class="font-black text-blue-600 text-sm">{{ $subject->subject_code }}</span><span class="font-bold text-slate-700 text-xs">{{ $subject->subject_name }}</span></div>
                    </td>
                    <td class="px-6 py-5"><span class="text-[10px] font-black text-slate-400 uppercase border border-slate-200 px-2 py-1 rounded-md">{{ $subject->course->course_code ?? 'N/A' }}</span></td>
                    <td class="px-6 py-5 text-center"><span class="text-xs font-bold {{ $subject->coordinator ? 'text-slate-700' : 'text-rose-400 italic' }}">{{ $subject->coordinator->name ?? 'Not Assigned' }}</span></td>
                    <td class="px-6 py-5">
                        <div class="flex flex-col items-center gap-1"><span class="text-[9px] font-bold text-slate-500 uppercase">SME1: {{ $subject->sme1->name ?? '---' }}</span><span class="text-[9px] font-bold text-slate-500 uppercase">SME2: {{ $subject->sme2->name ?? '---' }}</span></div>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex justify-end gap-1">
                            <button type="button" @click="openEditModal({{ $subject }})" class="p-2 text-slate-400 hover:text-blue-600"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete subject?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
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

    {{-- Main Input Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl relative z-10 overflow-hidden">
            <form :action="editMode ? '/admin/subjects/' + formData.id : '{{ route('admin.subjects.store') }}'" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="editMode ? 'PATCH' : 'POST'">
                <div class="p-10">
                    <h4 class="text-2xl font-black text-slate-900 mb-8" x-text="editMode ? 'Subject Assignment' : 'New Subject'"></h4>
                    
                    {{-- Validation Error Display --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-rose-50 rounded-2xl border border-rose-100">
                            <ul class="text-xs font-bold text-rose-800 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <input type="text" name="subject_code" required x-model="formData.code" placeholder="Code" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold">
                            <input type="text" name="subject_name" required x-model="formData.name" placeholder="Full Name" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-medium">
                            <select name="course_id" required x-model="formData.course_id" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold">
                                <option value="">Select Program</option>
                                @foreach($courses as $course) <option value="{{ $course->id }}">{{ $course->course_name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="space-y-4 bg-blue-50/50 p-6 rounded-[2rem] border border-blue-100">
                            <input type="text" x-model="smeSearch" placeholder="Filter names..." class="w-full bg-white border-blue-200 rounded-xl px-4 py-2 text-xs font-bold mb-2">
                            <select name="coordinator_id" x-model="formData.coordinator_id" class="w-full bg-white border-blue-200 rounded-xl px-4 py-2 text-sm">
                                <option value="">Choose Coordinator</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}" :disabled="isCoordinatorBusy({{ $lecturer->id }})" x-text="isCoordinatorBusy({{ $lecturer->id }}) ? '{{ $lecturer->name }} (Coordinating)' : '{{ $lecturer->name }}'" x-show="smeSearch === '' || '{{ strtolower($lecturer->name) }}'.includes(smeSearch.toLowerCase())"></option>
                                @endforeach
                            </select>
                            <select name="sme1_id" x-model="formData.sme1_id" class="w-full bg-white border-slate-200 rounded-xl px-4 py-2 text-sm">
                                <option value="">Choose SME 1</option>
                                @foreach($lecturers as $lecturer) <option value="{{ $lecturer->id }}" x-show="smeSearch === '' || '{{ strtolower($lecturer->name) }}'.includes(smeSearch.toLowerCase())">{{ $lecturer->name }}</option> @endforeach
                            </select>
                            <select name="sme2_id" x-model="formData.sme2_id" class="w-full bg-white border-slate-200 rounded-xl px-4 py-2 text-sm">
                                <option value="">Choose SME 2</option>
                                @foreach($lecturers as $lecturer) <option value="{{ $lecturer->id }}" x-show="smeSearch === '' || '{{ strtolower($lecturer->name) }}'.includes(smeSearch.toLowerCase())">{{ $lecturer->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="text-xs font-black uppercase text-slate-400 px-6">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-10 py-4 rounded-2xl text-xs font-black uppercase tracking-widest"><span x-text="editMode ? 'Save Assignments' : 'Create Subject'"></span></button>
                </div>
            </form>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div x-show="showCreateCsv" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showCreateCsv = false"></div>
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 relative z-10">
            <h4 class="text-xl font-black mb-6">Import Subjects</h4>
            <form action="{{ route('admin.subjects.import.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <select name="course_id" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold mb-4">
                    <option value="">Select Program</option>
                    @foreach($courses as $course) <option value="{{ $course->id }}">{{ $course->course_name }}</option> @endforeach
                </select>
                <input type="file" name="file" class="w-full mb-6" required>
                <button type="submit" class="w-full bg-emerald-600 text-white py-3 rounded-2xl font-black uppercase text-xs">Upload CSV</button>
            </form>
        </div>
    </div>

    {{-- ASSIGN MODAL --}}
    <div x-show="showAssignCsv" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAssignCsv = false"></div>
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md p-10 relative z-10">
            <h4 class="text-xl font-black mb-6">Bulk Assign Lecturers</h4>
            <form action="{{ route('admin.subjects.import.assign') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <select name="course_id" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold mb-4">
                    <option value="">Select Program</option>
                    @foreach($courses as $course) <option value="{{ $course->id }}">{{ $course->course_name }}</option> @endforeach
                </select>
                <input type="file" name="file" class="w-full mb-6" required>
                <button type="submit" class="w-full bg-amber-500 text-white py-3 rounded-2xl font-black uppercase text-xs">Upload CSV</button>
            </form>
        </div>
    </div>

</div>
@endsection