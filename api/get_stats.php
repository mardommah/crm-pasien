<?php
header('Content-Type: application/json');
require_once '../config.php';

try {
    // 1. Total Pasien (Kecuali dummy header row)
    $stmtTotal = $pdo->query("SELECT COUNT(*) as total FROM pasien WHERE no_rkm_medis != 'no_rkm_medis'");
    $totalPasien = $stmtTotal->fetch()['total'];

    // 2. Pasien Ulang Tahun Hari Ini (Berdasarkan hari dan bulan yang sama, kecuali dummy header row)
    $stmtUlangTahun = $pdo->query("
        SELECT COUNT(*) as total 
        FROM pasien 
        WHERE DATE_FORMAT(tgl_lahir, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')
          AND no_rkm_medis != 'no_rkm_medis'
    ");
    $totalUlangTahun = $stmtUlangTahun->fetch()['total'];

    // 3. Rekap Berdasarkan Kota (menggunakan nama kabupaten hasil JOIN)
    $stmtKota = $pdo->query("
        SELECT COALESCE(k.nm_kab, p.kd_kab) as kota, COUNT(*) as jumlah 
        FROM pasien p
        LEFT JOIN kabupaten k ON p.kd_kab = k.kd_kab
        WHERE p.no_rkm_medis != 'no_rkm_medis'
        GROUP BY p.kd_kab, k.nm_kab
        ORDER BY jumlah DESC
    ");
    $rekapKota = $stmtKota->fetchAll();

    // 4. Rekap Berdasarkan Alamat (kecuali dummy header row)
    // Karena alamat bisa sangat bervariasi, kita limit agar tidak terlalu panjang di UI
    $stmtAlamat = $pdo->query("
        SELECT alamat, COUNT(*) as jumlah 
        FROM pasien 
        WHERE no_rkm_medis != 'no_rkm_medis'
        GROUP BY alamat 
        ORDER BY jumlah DESC
        LIMIT 10
    ");
    $rekapAlamat = $stmtAlamat->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'total_pasien' => $totalPasien,
            'ulang_tahun_hari_ini' => $totalUlangTahun,
            'rekap_kota' => $rekapKota,
            'rekap_alamat' => $rekapAlamat
        ]
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data statistik: ' . $e->getMessage()
    ]);
}
?>
