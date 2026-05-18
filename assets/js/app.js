// Variabel global untuk instance Chart.js agar bisa dihancurkan/diupdate
let chartKotaInstance = null;
let chartAlamatInstance = null;

// State untuk pagination pasien
let currentPatientPage = 1;
let currentPatientDay = '';
let currentPatientMonth = '';
let currentPatientYear = '';
let currentPatientToday = '1'; // Default: filter pasien ultah hari ini
let currentWaTemplate = '';    // State untuk template WhatsApp

document.addEventListener('DOMContentLoaded', () => {
    // 0. Load template WhatsApp dari SQLite
    loadWaTemplate();

    // 1. Load data statistik (Total, Ulang Tahun, Chart)
    loadStats();

    // 2. Inisialisasi opsi tanggal/hari (1 hingga 31)
    const daySelect = document.getElementById('filter-day');
    if (daySelect) {
        for (let d = 1; d <= 31; d++) {
            const opt = document.createElement('option');
            opt.value = d;
            opt.innerText = d;
            daySelect.appendChild(opt);
        }
    }

    // 3. Inisialisasi opsi tahun (dari tahun sekarang hingga 1920)
    const yearSelect = document.getElementById('filter-year');
    if (yearSelect) {
        const currentYear = new Date().getFullYear();
        const startYear = 1920;
        for (let y = currentYear; y >= startYear; y--) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.innerText = y;
            yearSelect.appendChild(opt);
        }
    }

    // 4. Load data tabel pasien awal (semua)
    loadPatients();

    // 5. Event Listener untuk Filter
    const monthSelect = document.getElementById('filter-month');
    const btnReset = document.getElementById('btn-reset-filter');
    const filterBadge = document.getElementById('filter-badge');

    function checkResetBtnVisibility() {
        if (currentPatientDay || currentPatientMonth || currentPatientYear || currentPatientToday === '1') {
            btnReset.classList.remove('hidden');
        } else {
            btnReset.classList.add('hidden');
        }

        if (currentPatientToday === '1') {
            filterBadge.classList.remove('hidden');
            filterBadge.classList.add('inline-flex');
        } else {
            filterBadge.classList.add('hidden');
            filterBadge.classList.remove('inline-flex');
        }
    }

    // Panggil sekali untuk set status awal tombol reset & badge
    checkResetBtnVisibility();

    if (daySelect) {
        daySelect.addEventListener('change', (e) => {
            currentPatientDay = e.target.value;
            currentPatientToday = '0'; // Matikan filter hari ini jika ada filter lain
            currentPatientPage = 1; // Reset ke halaman pertama jika filter berubah
            checkResetBtnVisibility();
            loadPatients();
        });
    }

    monthSelect.addEventListener('change', (e) => {
        currentPatientMonth = e.target.value;
        currentPatientToday = '0'; // Matikan filter hari ini jika ada filter lain
        currentPatientPage = 1; // Reset ke halaman pertama jika filter berubah
        checkResetBtnVisibility();
        loadPatients();
    });

    if (yearSelect) {
        yearSelect.addEventListener('change', (e) => {
            currentPatientYear = e.target.value;
            currentPatientToday = '0'; // Matikan filter hari ini jika ada filter lain
            currentPatientPage = 1; // Reset ke halaman pertama jika filter berubah
            checkResetBtnVisibility();
            loadPatients();
        });
    }

    // Reset filter
    btnReset.addEventListener('click', () => {
        if (daySelect) daySelect.value = '';
        monthSelect.value = '';
        if (yearSelect) yearSelect.value = '';
        
        currentPatientDay = '';
        currentPatientMonth = '';
        currentPatientYear = '';
        currentPatientToday = '0'; // Matikan default filter hari ini sehingga menampilkan semua data
        currentPatientPage = 1;
        
        checkResetBtnVisibility();
        loadPatients();
    });

    // Download Excel
    const btnDownloadExcel = document.getElementById('btn-download-excel');
    if (btnDownloadExcel) {
        btnDownloadExcel.addEventListener('click', () => {
            let url = 'api/export_excel.php?';
            if (currentPatientToday) {
                url += `today=${encodeURIComponent(currentPatientToday)}`;
            }
            if (currentPatientDay) {
                url += `&day=${encodeURIComponent(currentPatientDay)}`;
            }
            if (currentPatientMonth) {
                url += `&month=${encodeURIComponent(currentPatientMonth)}`;
            }
            if (currentPatientYear) {
                url += `&year=${encodeURIComponent(currentPatientYear)}`;
            }
            // Unduh file dengan mengarahkan window location
            window.location.href = url;
        });
    }

    // --- EVENT LISTENERS MODAL WHATSAPP ---
    const btnWaSettings = document.getElementById('btn-wa-settings');
    const waSettingsModal = document.getElementById('wa-settings-modal');
    const btnCloseWaSettings = document.getElementById('btn-close-wa-settings');
    const btnCancelWaSettings = document.getElementById('btn-cancel-wa-settings');
    const btnSaveWaTemplate = document.getElementById('btn-save-wa-template');
    const waTemplateInput = document.getElementById('wa-template-input');

    const waPreviewModal = document.getElementById('wa-preview-modal');
    const btnCloseWaPreview = document.getElementById('btn-close-wa-preview');
    const btnCancelWaPreview = document.getElementById('btn-cancel-wa-preview');
    const btnSendWa = document.getElementById('btn-send-wa');
    const waPreviewPhone = document.getElementById('wa-preview-phone');
    const waPreviewMessage = document.getElementById('wa-preview-message');

    // Settings Modal
    if (btnWaSettings) {
        btnWaSettings.addEventListener('click', () => {
            if (waTemplateInput) {
                waTemplateInput.value = currentWaTemplate || DEFAULT_WA_TEMPLATE;
            }
            openModal('wa-settings-modal');
        });
    }

    if (btnCloseWaSettings) btnCloseWaSettings.addEventListener('click', () => closeModal('wa-settings-modal'));
    if (btnCancelWaSettings) btnCancelWaSettings.addEventListener('click', () => closeModal('wa-settings-modal'));

    if (btnSaveWaTemplate && waTemplateInput) {
        btnSaveWaTemplate.addEventListener('click', async () => {
            const newTemplate = waTemplateInput.value;
            // Disable tombol agar terlihat premium dan mencegah dobel submit
            btnSaveWaTemplate.disabled = true;
            btnSaveWaTemplate.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Menyimpan...';
            lucide.createIcons();
            
            try {
                const response = await fetch('api/save_wa_template.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ template: newTemplate })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    currentWaTemplate = newTemplate;
                    closeModal('wa-settings-modal');
                } else {
                    alert('Gagal menyimpan template: ' + result.message);
                }
            } catch (e) {
                console.error(e);
                alert('Gagal terhubung ke server untuk menyimpan template.');
            } finally {
                btnSaveWaTemplate.disabled = false;
                btnSaveWaTemplate.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> Simpan Template';
                lucide.createIcons();
            }
        });
    }

    // Preview Modal
    if (btnCloseWaPreview) btnCloseWaPreview.addEventListener('click', () => closeModal('wa-preview-modal'));
    if (btnCancelWaPreview) btnCancelWaPreview.addEventListener('click', () => closeModal('wa-preview-modal'));

    if (btnSendWa) {
        btnSendWa.addEventListener('click', () => {
            const phone = waPreviewPhone.value.replace(/[^\d]/g, '');
            const message = waPreviewMessage.value;
            if (!phone) {
                alert('Silakan masukkan nomor WhatsApp tujuan yang valid.');
                return;
            }
            
            // Buka tab baru menuju wa.me
            const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
            window.open(waUrl, '_blank');
            closeModal('wa-preview-modal');
        });
    }
});

/**
 * Mengambil data statistik dari backend
 */
async function loadStats() {
    try {
        const response = await fetch('api/get_stats.php');
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            
            // Update Summary Cards
            document.getElementById('total-pasien').innerText = data.total_pasien;
            document.getElementById('ulang-tahun-hari-ini').innerText = data.ulang_tahun_hari_ini;

            // Render Charts
            renderChartKota(data.rekap_kota);
            renderChartAlamat(data.rekap_alamat);
        } else {
            console.error('Error fetching stats:', result.message);
        }
    } catch (error) {
        console.error('Network error fetching stats:', error);
    }
}

/**
 * Mengambil data tabel pasien dari backend dengan pagination
 */
async function loadPatients() {
    const tableBody = document.getElementById('table-body');
    const emptyState = document.getElementById('empty-state');
    const paginationContainer = document.getElementById('pagination-container');
    
    // Tampilkan loading
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                <div class="flex flex-col items-center justify-center">
                    <i data-lucide="loader-2" class="w-8 h-8 text-primary-500 animate-spin mb-2"></i>
                    Memuat data...
                </div>
            </td>
        </tr>
    `;
    lucide.createIcons();
    emptyState.classList.add('hidden');

    try {
        let url = `api/get_patients.php?page=${currentPatientPage}&limit=10`;
        if (currentPatientToday === '1') {
            url += `&today=1`;
        }
        if (currentPatientDay) {
            url += `&day=${encodeURIComponent(currentPatientDay)}`;
        }
        if (currentPatientMonth) {
            url += `&month=${encodeURIComponent(currentPatientMonth)}`;
        }
        if (currentPatientYear) {
            url += `&year=${encodeURIComponent(currentPatientYear)}`;
        }
        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success') {
            const patients = result.data;
            const pagination = result.pagination;
            
            if (patients.length === 0) {
                // Tampilkan empty state
                tableBody.innerHTML = '';
                emptyState.classList.remove('hidden');
                emptyState.classList.add('flex');
                
                // Sembunyikan pagination container
                if (paginationContainer) paginationContainer.classList.add('hidden');
            } else {
                // Render baris tabel
                tableBody.innerHTML = '';
                patients.forEach(p => {
                    const tr = document.createElement('tr');
                    
                    // Ganti apostrop dan kutip ganda agar JSON aman sebagai data attribute
                    const patientJson = JSON.stringify(p).replace(/'/g, "&apos;").replace(/"/g, "&quot;");

                    tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">${p.id || '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 font-semibold">${escapeHTML(p.nama)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${formatDate(p.tanggal_lahir)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${escapeHTML(p.alamat)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                ${escapeHTML(p.kota)}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <button type="button" class="btn-kirim-wa inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:text-emerald-850 active:scale-95 transition-all shadow-sm gap-1" data-patient='${patientJson}'>
                                <i data-lucide="message-square" class="w-3.5 h-3.5 text-emerald-500"></i> Kirim WA
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
                
                // Inisialisasi ulang ikon Lucide untuk ikon di dalam tombol aksi
                lucide.createIcons();

                // Daftarkan event listener untuk setiap tombol "Kirim WA"
                document.querySelectorAll('.btn-kirim-wa').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const patientData = JSON.parse(btn.getAttribute('data-patient'));
                        openWaPreviewModal(patientData);
                    });
                });
                
                // Tampilkan dan update pagination
                if (paginationContainer) {
                    paginationContainer.classList.remove('hidden');
                    paginationContainer.classList.add('flex');
                    
                    const start = (pagination.current_page - 1) * pagination.limit + 1;
                    const end = Math.min(pagination.current_page * pagination.limit, pagination.total_items);
                    
                    document.getElementById('pagination-start').innerText = start;
                    document.getElementById('pagination-end').innerText = end;
                    document.getElementById('pagination-total').innerText = pagination.total_items;
                    
                    renderPaginationControls(pagination.current_page, pagination.total_pages);
                }
            }
        } else {
            tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">${escapeHTML(result.message)}</td></tr>`;
            if (paginationContainer) paginationContainer.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error fetching patients:', error);
        tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Gagal terhubung ke server.</td></tr>`;
        if (paginationContainer) paginationContainer.classList.add('hidden');
    }
}

/**
 * Merender kontrol tombol pagination secara dinamis
 * @param {number} currentPage Halaman aktif saat ini
 * @param {number} totalPages Total seluruh halaman
 */
function renderPaginationControls(currentPage, totalPages) {
    const controls = document.getElementById('pagination-controls');
    if (!controls) return;
    
    controls.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    // Tombol Sebelumnya (Prev)
    const prevBtn = document.createElement('button');
    prevBtn.className = `px-3 py-1.5 rounded-lg border text-sm font-medium transition-all ${
        currentPage === 1 
        ? 'border-slate-100 text-slate-300 cursor-not-allowed' 
        : 'border-slate-200 text-slate-600 hover:bg-slate-50 active:scale-95'
    }`;
    prevBtn.innerHTML = '<i data-lucide="chevron-left" class="w-4 h-4"></i>';
    if (currentPage > 1) {
        prevBtn.addEventListener('click', () => {
            currentPatientPage = currentPage - 1;
            loadPatients();
        });
    }
    controls.appendChild(prevBtn);
    
    // Tombol Angka Halaman
    const range = 1; // Jumlah tombol halaman aktif di sekitar kiri & kanan
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - range && i <= currentPage + range)) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `px-3 py-1.5 rounded-lg border text-sm font-medium transition-all ${
                i === currentPage 
                ? 'bg-sky-500 border-sky-500 text-white shadow-sm' 
                : 'border-slate-200 text-slate-600 hover:bg-slate-50 active:scale-95'
            }`;
            pageBtn.innerText = i;
            pageBtn.addEventListener('click', () => {
                currentPatientPage = i;
                loadPatients();
            });
            controls.appendChild(pageBtn);
        } else if (i === currentPage - range - 1 || i === currentPage + range + 1) {
            const ellipsis = document.createElement('span');
            ellipsis.className = 'px-2 text-slate-400 text-sm';
            ellipsis.innerText = '...';
            controls.appendChild(ellipsis);
        }
    }
    
    // Tombol Berikutnya (Next)
    const nextBtn = document.createElement('button');
    nextBtn.className = `px-3 py-1.5 rounded-lg border text-sm font-medium transition-all ${
        currentPage === totalPages 
        ? 'border-slate-100 text-slate-300 cursor-not-allowed' 
        : 'border-slate-200 text-slate-600 hover:bg-slate-50 active:scale-95'
    }`;
    nextBtn.innerHTML = '<i data-lucide="chevron-right" class="w-4 h-4"></i>';
    if (currentPage < totalPages) {
        nextBtn.addEventListener('click', () => {
            currentPatientPage = currentPage + 1;
            loadPatients();
        });
    }
    controls.appendChild(nextBtn);
    
    // Inisialisasi ulang ikon Lucide untuk ikon chevron
    lucide.createIcons();
}

/**
 * Render Chart untuk Rekap Kota
 */
function renderChartKota(data) {
    const ctx = document.getElementById('chartKota').getContext('2d');
    
    // Destroy existing chart if it exists
    if (chartKotaInstance) {
        chartKotaInstance.destroy();
    }

    // Ambil top 5 kota, sisanya dikelompokkan ke "Lain-lain"
    let processedData = [];
    if (data.length > 5) {
        processedData = data.slice(0, 5);
        const othersCount = data.slice(5).reduce((sum, item) => sum + parseInt(item.jumlah || 0), 0);
        processedData.push({
            kota: 'Lain-lain',
            jumlah: othersCount
        });
    } else {
        processedData = [...data];
    }

    const labels = processedData.map(item => item.kota || 'Tidak Diketahui');
    const values = processedData.map(item => item.jumlah);

    chartKotaInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#0ea5e9', // primary-500
                    '#8b5cf6', // violet-500
                    '#10b981', // emerald-500
                    '#f59e0b', // amber-500
                    '#ef4444', // red-500
                    '#64748b', // slate-500
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 15,
                        font: { family: "'Inter', sans-serif", size: 11 }
                    }
                }
            },
            cutout: '70%'
        }
    });
}

/**
 * Render Chart untuk Rekap Alamat (Top 10)
 */
function renderChartAlamat(data) {
    const ctx = document.getElementById('chartAlamat').getContext('2d');
    
    if (chartAlamatInstance) {
        chartAlamatInstance.destroy();
    }

    const labels = data.map(item => {
        // Potong nama alamat jika terlalu panjang
        let addr = item.alamat || 'Tidak Diketahui';
        return addr.length > 20 ? addr.substring(0, 20) + '...' : addr;
    });
    const values = data.map(item => item.jumlah);

    chartAlamatInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pasien',
                data: values,
                backgroundColor: '#e0f2fe', // primary-100
                borderColor: '#0ea5e9', // primary-500
                borderWidth: 1,
                borderRadius: 4,
                hoverBackgroundColor: '#bae6fd' // primary-200
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        // Tampilkan alamat penuh di tooltip
                        title: function(context) {
                            return data[context[0].dataIndex].alamat;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { family: "'Inter', sans-serif" }
                    },
                    grid: { color: '#f1f5f9' } // slate-100
                },
                x: {
                    ticks: { font: { family: "'Inter', sans-serif", size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });
}

// Utility functions
function escapeHTML(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? dateString : date.toLocaleDateString('id-ID', options);
}

// --- WHATSAPP FUNCTIONS ---
const DEFAULT_WA_TEMPLATE = `Halo {nama},

Kami dari Klinik Syamsinar ingin mengucapkan Selamat Hari Ulang Tahun! Semoga sehat selalu dan dilancarkan rezekinya. Terima kasih telah mempercayakan layanan kesehatan Anda bersama kami.`;

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        const container = modal.querySelector('.active-modal-container');
        if (container) {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }
    }, 20);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const container = modal.querySelector('.active-modal-container');
    if (container) {
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
    }
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 200);
}

window.insertWaTag = function(tag) {
    const textarea = document.getElementById('wa-template-input');
    if (!textarea) return;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const before = text.substring(0, start);
    const after = text.substring(end, text.length);
    textarea.value = before + tag + after;
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + tag.length;
};

async function loadWaTemplate() {
    try {
        const response = await fetch('api/get_wa_template.php');
        const result = await response.json();
        if (result.status === 'success') {
            currentWaTemplate = result.template;
        } else {
            currentWaTemplate = DEFAULT_WA_TEMPLATE;
        }
    } catch (e) {
        console.error('Gagal mengambil template WA:', e);
        currentWaTemplate = DEFAULT_WA_TEMPLATE;
    }
}

function openWaPreviewModal(patient) {
    openModal('wa-preview-modal');
    
    document.getElementById('wa-preview-nama').innerText = patient.nama || '-';
    document.getElementById('wa-preview-id').innerText = patient.id || '-';
    
    // Format nomor telepon
    let rawPhone = patient.no_tlp || '';
    // Hilangkan karakter non-digit selain +
    let formattedPhone = rawPhone.replace(/[^\d+]/g, '');
    if (formattedPhone && !formattedPhone.startsWith('+') && !formattedPhone.startsWith('62')) {
        if (formattedPhone.startsWith('0')) {
            formattedPhone = '62' + formattedPhone.substring(1);
        } else {
            formattedPhone = '62' + formattedPhone;
        }
    }
    document.getElementById('wa-preview-phone').value = formattedPhone || '';
    
    // Kompilasi template
    const template = currentWaTemplate || DEFAULT_WA_TEMPLATE;
    let compiledMessage = template
        .replace(/{nama}/g, patient.nama || '')
        .replace(/{no_rkm_medis}/g, patient.id || '')
        .replace(/{tanggal_lahir}/g, formatDate(patient.tanggal_lahir) || '')
        .replace(/{kota}/g, patient.kota || '')
        .replace(/{alamat}/g, patient.alamat || '');
        
    document.getElementById('wa-preview-message').value = compiledMessage;
}
