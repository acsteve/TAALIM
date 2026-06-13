<div x-data="{ open: false, id: '', title: '', sme_comments: '', kp_comments: '' }" 
     @open-reupload.window="open = true; id = $event.detail.id; title = $event.detail.title; sme_comments = $event.detail.sme; kp_comments = $event.detail.kp"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    
    <div class="bg-white w-full max-w-lg rounded-[2rem] p-8 shadow-2xl" @click.away="open = false">
        <h3 class="text-2xl font-black text-slate-900 mb-2">Update Assessment</h3>
        <p class="text-slate-500 text-sm mb-6">Updating the title or files will reset the approval process.</p>

        {{-- Action URL dynamically injected via Alpine --}}
        <form :action="'/subjcoordinator/reupload/' + id" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')

            {{-- 1. Rename Field --}}
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Assessment Title</label>
                <input type="text" name="title" x-model="title" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-slate-700">
            </div>

            {{-- 2. File Uploads (Names aligned with Controller) --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 border-2 border-dashed border-slate-100 rounded-2xl">
                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-2">Question Paper (PDF/Doc)</label>
                    <input type="file" name="assessment_file" class="text-[10px] text-slate-500">
                </div>
                <div class="p-4 border-2 border-dashed border-slate-100 rounded-2xl">
                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-2">Marking Scheme (PDF/Doc)</label>
                    <input type="file" name="inventory_file" class="text-[10px] text-slate-500">
                </div>
            </div>

            {{-- Reviewer Feedback --}}
            <div x-show="sme_comments || kp_comments" class="p-4 bg-red-50 rounded-2xl border border-red-100 mt-4">
                <span class="text-[9px] font-black text-red-600 uppercase tracking-widest">Reviewer Feedback:</span>
                <div class="text-xs text-red-800 mt-1 italic">
                    <p x-show="sme_comments" x-text="'SME: ' + sme_comments"></p>
                    <p x-show="kp_comments" class="mt-1" x-text="'KP: ' + kp_comments"></p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" @click="open = false" class="px-6 py-3 text-slate-400 font-bold hover:text-slate-600 transition-colors">Cancel</button>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all active:scale-95">
                    Update & Resubmit
                </button>
            </div>
        </form>
    </div>
</div>