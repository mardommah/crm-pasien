<!-- Modal Pengaturan Template WhatsApp -->
<div id="wa-settings-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full flex flex-col transform transition-all duration-300 scale-95 opacity-0 active-modal-container">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i data-lucide="settings" class="text-primary-600 w-5 h-5"></i>
                <h3 class="text-lg font-semibold text-slate-900">Template WhatsApp</h3>
            </div>
            <button id="btn-close-wa-settings" class="text-slate-400 hover:text-slate-600 transition-colors p-1.5 hover:bg-slate-50 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <!-- Body -->
        <div class="p-6 space-y-4 flex-grow overflow-y-auto max-h-[70vh]">
            <p class="text-sm text-slate-500">
                Atur pesan otomatis untuk pasien. Anda dapat menggunakan variabel-variabel di bawah ini untuk disisipkan ke dalam pesan:
            </p>
            
            <!-- Tags info -->
            <div class="flex flex-wrap gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                <button type="button" onclick="insertWaTag('{nama}')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> {nama}
                </button>
                <button type="button" onclick="insertWaTag('{no_rkm_medis}')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span> {no_rkm_medis}
                </button>
                <button type="button" onclick="insertWaTag('{tanggal_lahir}')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {tanggal_lahir}
                </button>
                <button type="button" onclick="insertWaTag('{kota}')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> {kota}
                </button>
                <button type="button" onclick="insertWaTag('{alamat}')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> {alamat}
                </button>
                <button type="button" onclick="insertWaTag('{umur}')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span> {umur}
                </button>
            </div>
            
            <!-- Input Textarea -->
            <div class="flex flex-col space-y-1.5">
                <label for="wa-template-input" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Template Pesan
                </label>
                <textarea id="wa-template-input" rows="6" class="text-sm border-slate-200 rounded-lg p-3 border bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all resize-none font-sans" placeholder="Tulis pesan Anda disini..."></textarea>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end space-x-3 rounded-b-2xl">
            <button id="btn-cancel-wa-settings" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-700 rounded-xl text-sm font-semibold transition-all">
                Batal
            </button>
            <button id="btn-save-wa-template" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-primary-500/20 active:scale-95 transition-all">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Template
            </button>
        </div>
    </div>
</div>

<!-- Modal Preview & Kirim WhatsApp Pasien -->
<div id="wa-preview-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full flex flex-col transform transition-all duration-300 scale-95 opacity-0 active-modal-container">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-emerald-50/50 rounded-t-2xl">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Kirim Pesan WhatsApp</h3>
                    <p class="text-xs text-slate-500 leading-none mt-0.5" id="wa-patient-title">Review pesan sebelum dikirim</p>
                </div>
            </div>
            <button id="btn-close-wa-preview" class="text-slate-400 hover:text-slate-600 transition-colors p-1.5 hover:bg-slate-50 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <!-- Body -->
        <div class="p-6 space-y-4 flex-grow overflow-y-auto max-h-[70vh]">
            <!-- Info Pasien -->
            <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 block uppercase font-medium tracking-wider">Nama Pasien</span>
                    <span class="text-slate-800 font-bold text-sm" id="wa-preview-nama">-</span>
                </div>
                <div>
                    <span class="text-slate-400 block uppercase font-medium tracking-wider">No Rekam Medis</span>
                    <span class="text-slate-800 font-bold text-sm" id="wa-preview-id">-</span>
                </div>
            </div>
            
            <!-- Input No Handphone -->
            <div class="flex flex-col space-y-1.5">
                <label for="wa-preview-phone" class="text-xs font-semibold text-slate-500 uppercase tracking-wider flex items-center">
                    <i data-lucide="phone" class="w-3.5 h-3.5 mr-1 text-slate-400"></i> No WhatsApp / Handphone
                </label>
                <input type="text" id="wa-preview-phone" class="text-sm border-slate-200 rounded-lg px-3 py-2 border bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all font-mono" placeholder="Masukkan nomor handphone...">
                <span class="text-[10px] text-slate-400 leading-none">Format: Gunakan format internasional (contoh: 628123456789 atau 08123456789).</span>
            </div>
            
            <!-- Input Textarea Message Preview -->
            <div class="flex flex-col space-y-1.5">
                <label for="wa-preview-message" class="text-xs font-semibold text-slate-500 uppercase tracking-wider flex items-center">
                    <i data-lucide="file-text" class="w-3.5 h-3.5 mr-1 text-slate-400"></i> Pesan (Dapat Diedit Kembali)
                </label>
                <textarea id="wa-preview-message" rows="7" class="text-sm border-slate-200 rounded-lg p-3 border bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all resize-none font-sans" placeholder="Tulis pesan Anda disini..."></textarea>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end space-x-3 rounded-b-2xl">
            <button id="btn-cancel-wa-preview" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-700 rounded-xl text-sm font-semibold transition-all">
                Batal
            </button>
            <button id="btn-send-wa" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold flex items-center gap-1.5 shadow-md shadow-emerald-500/20 active:scale-95 transition-all">
                <i data-lucide="send" class="w-4 h-4"></i> Kirim Chat
            </button>
        </div>
    </div>
</div>

<style>
    .active-modal-container {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
    }
</style>
