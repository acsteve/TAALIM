@extends('layouts.adminlayout.app')

@section('title', 'Program Management')

@section('content')
<div class="max-w-5xl mx-auto space-y-8" 
     x-data="{ 
        openAddModal: false, 
        openEditModal: false, 
        currentCourse: {id: '', code: '', name: '', kp_id: ''},
        assignedKpIds: {{ json_encode($assignedKpIds) }},
        searchQuery: ''
     }">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Manage Programs</h3>
            
        </div>
        
        <button @click="openAddModal = true" class="flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-2xl text-sm font-bold hover:bg-emerald-600 transition shadow-lg shadow-slate-200">
            <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-400"></i> 
            Add New Program
        </button>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-100 text-rose-600 px-6 py-4 rounded-2xl text-sm font-bold">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Table Container --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full text-left border-collapse">
            <thead class="bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Code</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Program Name</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Ketua Program (KP)</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($courses as $course)
                <tr class="hover:bg-slate-50/50 transition group">
                    <td class="px-8 py-6">
                        <span class="font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg text-xs border border-emerald-100">
                            {{ $course->course_code }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-sm font-bold text-slate-800">{{ $course->course_name }}</td>
                    <td class="px-8 py-6 text-center">
                        @if($course->kp)
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-bold text-slate-800">{{ $course->kp->name }}</span>
                                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-tight flex items-center gap-1">
                                    <i data-lucide="user-check" class="w-3 h-3"></i> Active KP
                                </span>
                            </div>
                        @else
                            <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 uppercase">Vacant</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex justify-end items-center gap-2">
                            <button @click="currentCourse = { 
                                        id: '{{ $course->id }}', 
                                        code: '{{ $course->course_code }}', 
                                        name: '{{ $course->course_name }}', 
                                        kp_id: '{{ $course->kp_id ?? '' }}' 
                                    }; openEditModal = true" 
                                    class="p-2 text-slate-300 hover:text-emerald-500 transition-colors">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>

                            <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Delete this program?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-300 hover:text-rose-500 transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-12 text-center text-slate-400 text-sm italic">No programs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal: ADD --}}
    <div x-show="openAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
        <div @click.away="openAddModal = false" class="bg-white w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl">
            <h3 class="text-xl font-black text-slate-800 mb-6">Create New Program</h3>
            <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Program Code</label>
                    <input type="text" name="course_code" required class="w-full mt-1.5 bg-slate-50 border-slate-200 rounded-2xl p-3 text-sm font-bold uppercase focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Program Name</label>
                    <input type="text" name="course_name" required class="w-full mt-1.5 bg-slate-50 border-slate-200 rounded-2xl p-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Assign Ketua Program</label>
                    <input type="text" x-model="searchQuery" placeholder="Search lecturer name..." class="w-full mt-1.5 mb-2 bg-slate-50 border-slate-200 rounded-2xl p-2 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                    <select name="kp_id" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3 text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="">-- No KP Assigned --</option>
                        @foreach($allLecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" 
                                    {{ in_array($lecturer->id, $assignedKpIds) ? 'disabled' : '' }}
                                    x-show="'{{ strtolower($lecturer->name) }}'.includes(searchQuery.toLowerCase())">
                                {{ $lecturer->name }} {{ in_array($lecturer->id, $assignedKpIds) ? '(Already KP)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-600 transition">Save Program</button>
            </form>
        </div>
    </div>

    {{-- Modal: EDIT --}}
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
        <div @click.away="openEditModal = false" class="bg-white w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl">
            <h3 class="text-xl font-black text-slate-800 mb-6">Update Program</h3>
            <form :action="`/admin/courses/${currentCourse.id}`" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Course Code</label>
                    <input type="text" name="course_code" x-model="currentCourse.code" required class="w-full mt-1.5 bg-slate-50 border-slate-200 rounded-2xl p-3 text-sm font-bold uppercase focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Program Name</label>
                    <input type="text" name="course_name" x-model="currentCourse.name" required class="w-full mt-1.5 bg-slate-50 border-slate-200 rounded-2xl p-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Update Ketua Program</label>
                    <input type="text" x-model="searchQuery" placeholder="Search lecturer name..." class="w-full mt-1.5 mb-2 bg-slate-50 border-slate-200 rounded-2xl p-2 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                    <select name="kp_id" x-model="currentCourse.kp_id" class="w-full bg-slate-50 border-slate-200 rounded-2xl p-3 text-sm font-bold focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="">-- No KP Assigned --</option>
                        @foreach($allLecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" 
                                    :disabled="currentCourse.kp_id != '{{ $lecturer->id }}' && assignedKpIds.includes({{ $lecturer->id }})"
                                    x-show="'{{ strtolower($lecturer->name) }}'.includes(searchQuery.toLowerCase())">
                                {{ $lecturer->name }} 
                                <template x-if="currentCourse.kp_id != '{{ $lecturer->id }}' && assignedKpIds.includes({{ $lecturer->id }})">
                                    <span>(Already KP)</span>
                                </template>
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="openEditModal = false" class="w-full bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-xs uppercase hover:bg-slate-200 transition">Cancel</button>
                    <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black text-xs uppercase hover:bg-emerald-700 transition shadow-lg">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection