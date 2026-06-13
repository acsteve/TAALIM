@php
    // Get the ID whether $section is passed as an object, an integer, or null
    $secId = is_object($section) ? $section->id : $section;

    // Constructs the exact composite lookup key (e.g., 'midterm_boe_12' or 'teaching_plan')
    $lookupKey = $type . ($secId ? '_' . $secId : '');
    $fileRecord = $uploadedDocs[$lookupKey] ?? null;
@endphp

<div class="p-4 border rounded-xl transition shadow-sm {{ $fileRecord ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-white' }}">
    <span class="block text-[11px] font-black text-slate-700 tracking-tight">{{ $label }}</span>
    
    <div class="mt-3">
        @if($fileRecord)
            <div class="flex items-center justify-between gap-2">
                <a href="{{ asset('storage/' . $fileRecord->file_path) }}" target="_blank" class="text-xs font-bold text-emerald-700 underline truncate hover:text-emerald-900 flex items-center gap-1">
                    📄 View Uploaded Document
                </a>
                <form action="{{ route('subjcoordinator.reports.destroy', $fileRecord->id) }}" method="POST" onsubmit="return confirm('Remove file permanently?')" class="flex-shrink-0">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition text-xs font-bold">
                        Delete
                    </button>
                </form>
            </div>
            
            {{-- Quick Replacement Action Trigger --}}
            <div class="mt-2 pt-2 border-t border-emerald-100/60">
                <form action="{{ route('subjcoordinator.reports.upload', ['subject_id' => $subject->id, 'session' => $session]) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    
                    {{-- Sends the exact database primary ID --}}
                    <input type="hidden" name="section_id" value="{{ $secId }}">
                    
                    <input type="file" name="report_file" required onchange="this.form.submit()" class="block w-full text-[10px] text-slate-400 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300">
                </form>
            </div>
        @else
            {{-- Empty State Upload Drop Input Box Element --}}
            <form action="{{ route('subjcoordinator.reports.upload', ['subject_id' => $subject->id, 'session' => $session]) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                
                {{-- Sends the exact database primary ID --}}
                <input type="hidden" name="section_id" value="{{ $secId }}">
                
                <div class="flex items-center gap-2">
                    <input type="file" name="report_file" required class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    <button type="submit" class="px-2 py-1 bg-slate-800 text-white font-bold text-[10px] rounded hover:bg-slate-900 uppercase">Save</button>
                </div>
            </form>
        @endif
    </div>
</div>