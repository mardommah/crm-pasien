<?php
// Masukkan komponen-komponen UI
include 'components/header.php';
include 'components/navbar.php';
?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Header Section -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Overview Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">Analisis dan rekapitulasi data pasien terkini.</p>
        </div>

        <?php 
        // Summary Cards (Total & Ulang Tahun)
        include 'components/summary_cards.php';
        
        // Charts (Kota & Alamat)
        include 'components/charts.php';
        
        // Data Table Pasien
        include 'components/data_table.php'; 

        // Modals WhatsApp (Template & Preview)
        include 'components/wa_modals.php';
        ?>

<?php
// Footer (Script tags & penutup body)
include 'components/footer.php';
?>