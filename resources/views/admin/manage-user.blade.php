@extends('layouts.adminlayout.app')

@section('title', 'User Management')

@section('content')
<div class="max-w-6xl mx-auto space-y-8" x-data="{ openCreateModal: false, openViewModal: false, selectedRole: '', selectedUser: null, search: '', roleFilter: 'All' }">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Manage User</h3>
        </div>
        
        <button @click="openCreateModal = true" class="flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-2xl text-sm font-bold hover:bg-emerald-600 transition shadow-lg shadow-slate-200">
            <i data-lucide="user-plus" class="w-4 h-4 text-emerald-400"></i> 
            Add New User
        </button>
    </div>

    {{-- Search and Filter --}}
    <div class="flex flex-col md:flex-row gap-4">
        <input type="text" x-model="search" placeholder="Search by name or staff ID..." class="flex-1 bg-white border border-slate-200 rounded-2xl text-sm p-4 focus:ring-2 focus:ring-emerald-500 outline-none">
        <select x-model="roleFilter" class="bg-white border border-slate-200 rounded-2xl text-sm font-bold p-4 outline-none">
            <option value="All">All Roles</option>
            <option value="admin">Admin</option>
            <option value="kp">Ketua Program</option>
            <option value="lecturer">Lecturer</option>
        </select>
    </div>

    {{-- User Table --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full text-left border-collapse">
            <thead class="bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identity & Staff ID</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">System Role</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50/50 transition group"
                    x-show="(roleFilter === 'All' || '{{ $user->role }}' === roleFilter) && 
                            ('{{ strtolower($user->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($user->staff_id) }}'.includes(search.toLowerCase()))">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-xs">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $user->name }}</p>
                                <p class="text-[10px] font-mono text-slate-400 uppercase tracking-tight">{{ $user->staff_id }} — {{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        @php
                            $roleStyles = match($user->role) {
                                'admin' => 'text-purple-600 bg-purple-50 border-purple-100',
                                'kp' => 'text-rose-600 bg-rose-50 border-rose-100',
                                'lecturer' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                                default => 'text-slate-500 bg-slate-50 border-slate-100',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-lg border {{ $roleStyles }} text-[10px] font-black uppercase tracking-wider">
                            {{ $user->role === 'kp' ? 'Ketua Program' : $user->role }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right flex justify-end gap-2">
                        <button type="button" 
                            @click="selectedUser = {{ json_encode([
                                'name' => $user->name, 
                                'staff_id' => $user->staff_id, 
                                'email' => $user->email, 
                                'role' => $user->role, 
                                'program' => $user->course->course_name ?? 'N/A',
                                'coordinated' => \App\Models\Subject::where('coordinator_id', $user->id)->pluck('subject_name'),
                                'sme_subjects' => \App\Models\Subject::where('sme1_id', $user->id)->orWhere('sme2_id', $user->id)->pluck('subject_name'),
                                'kp_for_program' => \App\Models\Course::where('kp_id', $user->id)->pluck('course_name')
                            ]) }}; openViewModal = true" 
                            class="p-2 text-slate-300 hover:text-blue-500 transition">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                        
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-300 hover:text-rose-500 transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- View User Info Modal --}}
    <div x-show="openViewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
        <div @click.away="openViewModal = false" class="bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl border border-slate-200 p-8 max-h-[80vh] overflow-y-auto">
            <h3 class="text-xl font-black text-slate-800 mb-6">User Information</h3>
            <div class="space-y-4" x-show="selectedUser">
                <div><label class="text-[10px] font-black text-slate-400 uppercase">Full Name</label><p class="font-bold text-sm" x-text="selectedUser?.name"></p></div>
                <div><label class="text-[10px] font-black text-slate-400 uppercase">User ID</label><p class="font-bold text-sm" x-text="selectedUser?.staff_id"></p></div>
                <div><label class="text-[10px] font-black text-slate-400 uppercase">Role</label><p class="font-bold text-sm uppercase" x-text="selectedUser?.role"></p></div>
                
                <div x-show="selectedUser?.kp_for_program.length > 0" class="pt-2 border-t border-slate-100">
                    <label class="text-[10px] font-black text-rose-600 uppercase">Ketua Program For</label>
                    <template x-for="prog in selectedUser?.kp_for_program">
                        <p class="text-xs font-bold text-slate-700 mt-1"> <span x-text="prog"></span></p>
                    </template>
                </div>
                <div x-show="selectedUser?.coordinated.length > 0" class="pt-2 border-t border-slate-100">
                    <label class="text-[10px] font-black text-emerald-600 uppercase">Coordinating Subjects</label>
                    <template x-for="subj in selectedUser?.coordinated">
                        <p class="text-xs font-bold text-slate-700 mt-1"> <span x-text="subj"></span></p>
                    </template>
                </div>
                <div x-show="selectedUser?.sme_subjects.length > 0" class="pt-2 border-t border-slate-100">
                    <label class="text-[10px] font-black text-purple-600 uppercase">SME For Subjects</label>
                    <template x-for="subj in selectedUser?.sme_subjects">
                        <p class="text-xs font-bold text-slate-700 mt-1"> <span x-text="subj"></span></p>
                    </template>
                </div>
            </div>
            <button @click="openViewModal = false" class="w-full mt-8 py-3 bg-slate-100 rounded-2xl font-black text-xs uppercase text-slate-600 hover:bg-slate-200 transition">Close</button>
        </div>
    </div>

    {{-- Create User Modal --}}
    <div x-show="openCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
        <div @click.away="openCreateModal = false" class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl border border-slate-200">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Register New User</h3>
                    <button @click="openCreateModal = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">User ID</label>
                        <input type="text" name="staff_id" required class="w-full mt-1.5 bg-slate-50 border-slate-200 rounded-2xl text-sm p-3 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                        <input type="text" name="name" required class="w-full mt-1.5 bg-slate-50 border-slate-200 rounded-2xl text-sm p-3 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                        <input type="email" name="email" required class="w-full mt-1.5 bg-slate-50 border-slate-200 rounded-2xl text-sm p-3 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">System Role</label>
                        <select name="role" x-model="selectedRole" required class="w-full mt-1.5 bg-slate-50 border-slate-200 rounded-2xl text-sm font-bold p-3 appearance-none">
                            <option value="">Select a role...</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="kp">Ketua Program (KP)</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div x-show="selectedRole === 'kp'" x-transition class="bg-rose-50 p-4 rounded-2xl border border-rose-100">
                        <label class="text-[10px] font-black text-rose-400 uppercase tracking-widest ml-1">Assign to Program</label>
                        <select name="course_id" :required="selectedRole === 'kp'" class="w-full mt-1.5 bg-white border-rose-200 rounded-xl text-sm font-bold p-2">
                            <option value="">Select Program...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-600 transition shadow-lg">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection