{{-- ═══════════════════════════════════════════════
     RESOLVE ALERT MODAL
═══════════════════════════════════════════════ --}}
<div x-show="showResolveModal" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="showResolveModal" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="closeResolveModal()" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

        <div x-show="showResolveModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative inline-block w-full max-w-lg overflow-hidden text-left align-bottom transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle border border-slate-100">
            
            {{-- Modal Header --}}
            <div class="px-8 py-8 border-b border-rose-100/50 bg-gradient-to-r from-rose-50 to-orange-50 flex justify-between items-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center border border-rose-100 shadow-sm text-rose-600 relative z-10">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight relative z-10">Resolve Alert</h3>
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mt-1 relative z-10">Manual Resolution Node</p>
                    </div>
                </div>
                <button @click="closeResolveModal()" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors z-10 cursor-pointer shadow-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-8 space-y-6">
                <!-- Data Summary -->
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-[9px]">Medicine Entity</span>
                        <span class="text-sm font-bold text-slate-800" x-text="selectedAlert?.medicine?.name"></span>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-200/50 pt-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-[9px]">Alert Vector</span>
                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded bg-rose-100 text-rose-700" x-text="selectedAlert?.alert_type?.replace('_', ' ')"></span>
                    </div>
                </div>

                <form @submit.prevent="confirmResolve" id="resolveForm">
                    <div class="space-y-2 focus-within:text-rose-600 transition-colors">
                        <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Resolution Log / Notes</label>
                        <textarea x-model="resolutionNotes" rows="4"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-rose-400 focus:ring-4 focus:ring-rose-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400 text-sm"
                            placeholder="Enter detailed notes about the corrective action taken..."></textarea>
                    </div>
                </form>
            </div>

            {{-- Modal Footer --}}
            <div class="px-8 py-8 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                <button type="button" @click="closeResolveModal()" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm">
                    Abort
                </button>
                <button type="submit" form="resolveForm" class="px-8 py-3 bg-gradient-to-r from-rose-600 to-orange-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-rose-700 hover:to-orange-700 hover:-translate-y-0.5 transition-all shadow-lg shadow-rose-500/30 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Confirm Resolution
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     BULK RESOLVE MODAL
═══════════════════════════════════════════════ --}}
<div x-show="showBulkResolveModal" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="showBulkResolveModal" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="closeBulkResolveModal()" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

        <div x-show="showBulkResolveModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative inline-block w-full max-w-lg overflow-hidden text-left align-bottom transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle border border-slate-100">
            
            {{-- Modal Header --}}
            <div class="px-8 py-8 border-b border-indigo-100/50 bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm text-indigo-600 relative z-10">
                        <i class="fas fa-check-double text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight relative z-10">Mass Resolution</h3>
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mt-1 relative z-10" x-text="`Processing ${selectedAlerts.length} Units`"></p>
                    </div>
                </div>
                <button @click="closeBulkResolveModal()" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors z-10 cursor-pointer shadow-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-8 space-y-6">
                <div class="bg-rose-50 rounded-2xl p-5 border border-rose-100">
                    <p class="text-xs font-bold text-rose-700 leading-relaxed uppercase tracking-wider">
                        <i class="fas fa-triangle-exclamation mr-2"></i>
                        Attention: You are about to mark all selected units as "Resolved" within the system intelligence.
                    </p>
                </div>

                <form @submit.prevent="confirmBulkResolve" id="bulkResolveForm">
                    <div class="space-y-2 focus-within:text-indigo-600 transition-colors">
                        <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Group Resolution Log</label>
                        <textarea x-model="bulkResolutionNotes" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400 text-sm"
                            placeholder="Unified note for the batch resolution..."></textarea>
                    </div>
                </form>
            </div>

            {{-- Modal Footer --}}
            <div class="px-8 py-8 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                <button type="button" @click="closeBulkResolveModal()" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm">
                    Abort Action
                </button>
                <button type="submit" form="bulkResolveForm" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-indigo-700 hover:to-blue-700 hover:-translate-y-0.5 transition-all shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                    <i class="fas fa-check-double"></i> Batch Commit
                </button>
            </div>
        </div>
    </div>
</div>
