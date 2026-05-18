        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card: Total Pasien -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center card-hover">
                <div class="p-4 rounded-xl bg-blue-50 text-blue-600 mr-4">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Pasien</p>
                    <h3 class="text-3xl font-bold text-slate-900" id="total-pasien">-</h3>
                </div>
            </div>
            
            <!-- Card: Ulang Tahun Hari Ini -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center card-hover relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-pink-500/10 to-purple-500/10 translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-500 ease-in-out"></div>
                <div class="p-4 rounded-xl bg-pink-50 text-pink-600 mr-4 relative z-10">
                    <i data-lucide="gift" class="w-8 h-8"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-slate-500 mb-1">Ulang Tahun Hari Ini</p>
                    <h3 class="text-3xl font-bold text-slate-900" id="ulang-tahun-hari-ini">-</h3>
                </div>
            </div>
        </div>
