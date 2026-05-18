        <!-- Data Table Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-lg font-semibold text-slate-900">Daftar Pasien</h3>
                        <span id="filter-badge" class="hidden inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500 animate-ping"></span> Ultah Hari Ini
                        </span>
                    </div>
                    <button id="btn-reset-filter" class="hidden text-sm text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1 font-medium bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg px-2.5 py-1.5" title="Reset Semua Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset Filter
                    </button>
                </div>
                
                <!-- Filter Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50/50 rounded-xl border border-slate-100/80">
                    <!-- Filter Tanggal Lahir -->
                    <div class="flex flex-col space-y-1.5">
                        <label for="filter-day" class="text-xs font-semibold text-slate-500 uppercase tracking-wider flex items-center">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1 text-slate-400"></i> Tanggal Lahir
                        </label>
                        <select id="filter-day" class="text-sm border-slate-200 rounded-lg px-3 py-2 border bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                            <option value="">Semua Tanggal</option>
                            <!-- Diisi secara dinamis oleh JavaScript -->
                        </select>
                    </div>
                    
                    <!-- Filter Bulan Lahir -->
                    <div class="flex flex-col space-y-1.5">
                        <label for="filter-month" class="text-xs font-semibold text-slate-500 uppercase tracking-wider flex items-center">
                            <i data-lucide="calendar-days" class="w-3.5 h-3.5 mr-1 text-slate-400"></i> Bulan Lahir
                        </label>
                        <select id="filter-month" class="text-sm border-slate-200 rounded-lg px-3 py-2 border bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                            <option value="">Semua Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    
                    <!-- Filter Tahun Lahir -->
                    <div class="flex flex-col space-y-1.5">
                        <label for="filter-year" class="text-xs font-semibold text-slate-500 uppercase tracking-wider flex items-center">
                            <i data-lucide="calendar-range" class="w-3.5 h-3.5 mr-1 text-slate-400"></i> Tahun Lahir
                        </label>
                        <select id="filter-year" class="text-sm border-slate-200 rounded-lg px-3 py-2 border bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                            <option value="">Semua Tahun</option>
                            <!-- Diisi secara dinamis oleh JavaScript -->
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No. RM</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Pasien</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Lahir</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Alamat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kota</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="bg-white divide-y divide-slate-200">
                        <!-- Data will be loaded here via JS -->
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="loader-2" class="w-8 h-8 text-primary-500 animate-spin mb-2"></i>
                                    Memuat data...
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Controls -->
            <div id="pagination-container" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-col sm:flex-row gap-4">
                <div class="text-sm text-slate-500">
                    Menampilkan <span id="pagination-start" class="font-medium text-slate-800">0</span> sampai <span id="pagination-end" class="font-medium text-slate-800">0</span> dari <span id="pagination-total" class="font-medium text-slate-800">0</span> pasien
                </div>
                <div class="flex items-center space-x-1" id="pagination-controls">
                    <!-- Dinamis dari JS -->
                </div>
            </div>
            
            <!-- Empty State (Hidden by default) -->
            <div id="empty-state" class="hidden flex-col items-center justify-center py-12 px-4 text-center">
                <div class="bg-slate-100 rounded-full p-4 mb-4">
                    <i data-lucide="search-x" class="w-8 h-8 text-slate-400"></i>
                </div>
                <h3 class="text-sm font-medium text-slate-900">Tidak ada pasien</h3>
                <p class="text-sm text-slate-500 mt-1">Tidak ditemukan data pasien dengan filter tersebut.</p>
            </div>
        </div>
