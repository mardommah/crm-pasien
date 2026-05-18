<?php
// api/export_excel.php
require_once __DIR__ . '/../config.php';

try {
    // Ambil parameter filter
    $today = isset($_GET['today']) ? (int)$_GET['today'] : 0;
    $day = isset($_GET['day']) && $_GET['day'] !== '' ? (int)$_GET['day'] : null;
    $month = isset($_GET['month']) && $_GET['month'] !== '' ? (int)$_GET['month'] : null;
    $year = isset($_GET['year']) && $_GET['year'] !== '' ? (int)$_GET['year'] : null;

    // Query dasar
    $query = "SELECT p.no_rkm_medis as id, p.nm_pasien as nama, p.tgl_lahir as tanggal_lahir, p.alamat, COALESCE(k.nm_kab, p.kd_kab) as kota, p.no_tlp FROM pasien p LEFT JOIN kabupaten k ON p.kd_kab = k.kd_kab";
    $where = ["p.no_rkm_medis != 'no_rkm_medis'"];
    $params = [];

    // Jika filter ulang tahun hari ini aktif
    if ($today) {
        $where[] = "DATE_FORMAT(p.tgl_lahir, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')";
    }
    
    // Jika ada parameter filter tanggal (hari) lahir
    if ($day !== null && $day >= 1 && $day <= 31) {
        $where[] = "DAY(p.tgl_lahir) = :day";
        $params[':day'] = $day;
    }

    // Jika ada parameter filter bulan lahir
    if ($month !== null && $month >= 1 && $month <= 12) {
        $where[] = "MONTH(p.tgl_lahir) = :month";
        $params[':month'] = $month;
    }

    // Jika ada parameter filter tahun lahir
    if ($year !== null && $year >= 1800 && $year <= (int)date('Y')) {
        $where[] = "YEAR(p.tgl_lahir) = :year";
        $params[':year'] = $year;
    }
    
    if (!empty($where)) {
        $query .= " WHERE " . implode(" AND ", $where);
    }
    
    // Urutkan berdasarkan no rekam medis
    $query .= " ORDER BY p.no_rkm_medis DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set header download excel / csv
    $filename = "data_pasien_" . date('Ymd_His') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Buka file pointer php://output
    $output = fopen('php://output', 'w');
    
    // Tambahkan UTF-8 BOM untuk Microsoft Excel agar membaca format dengan benar
    fwrite($output, "\xEF\xBB\xBF");
    
    // Tulis header kolom
    fputcsv($output, ['No. Rekam Medis', 'Nama Pasien', 'Tanggal Lahir', 'No. WhatsApp/HP', 'Alamat', 'Kota']);
    
    // Tulis data baris demi baris
    foreach ($patients as $p) {
        fputcsv($output, [
            $p['id'],
            $p['nama'],
            $p['tanggal_lahir'],
            $p['no_tlp'],
            $p['alamat'],
            $p['kota']
        ]);
    }
    
    fclose($output);
    exit;

} catch (Exception $e) {
    die("Gagal mengekspor data: " . $e->getMessage());
}
?>
