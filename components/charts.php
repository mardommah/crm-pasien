        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Rekap Kota -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col h-[400px]">
                <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <i data-lucide="map-pin" class="w-5 h-5 mr-2 text-slate-400"></i>
                    Sebaran Kota
                </h3>
                <div class="flex-grow relative">
                    <canvas id="chartKota"></canvas>
                </div>
            </div>

            <!-- Rekap Alamat -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col h-[400px]">
                <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <i data-lucide="home" class="w-5 h-5 mr-2 text-slate-400"></i>
                    Konsentrasi Alamat (Top 10)
                </h3>
                <div class="flex-grow relative">
                    <canvas id="chartAlamat"></canvas>
                </div>
            </div>
        </div>
