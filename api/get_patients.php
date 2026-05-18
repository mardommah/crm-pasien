<?php
header('Content-Type: application/json');
require_once '../config.php';

try {
    $today = isset($_GET['today']) && $_GET['today'] === '1' ? true : false;
    $day = isset($_GET['day']) && $_GET['day'] !== '' ? (int)$_GET['day'] : null;
    $month = isset($_GET['month']) && $_GET['month'] !== '' ? (int)$_GET['month'] : null;
    $year = isset($_GET['year']) && $_GET['year'] !== '' ? (int)$_GET['year'] : null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    if ($page < 1) $page = 1;
    if ($limit < 1 || $limit > 100) $limit = 10;
    
    $offset = ($page - 1) * $limit;
    
    // Query dasar
    $query = "SELECT p.no_rkm_medis as id, p.nm_pasien as nama, p.tgl_lahir as tanggal_lahir, TIMESTAMPDIFF(YEAR, p.tgl_lahir, CURDATE()) AS umur, p.alamat, COALESCE(k.nm_kab, p.kd_kab) as kota, p.no_tlp FROM pasien p LEFT JOIN kabupaten k ON p.kd_kab = k.kd_kab";
    $countQuery = "SELECT COUNT(*) FROM pasien p";
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
        $countQuery .= " WHERE " . implode(" AND ", $where);
    }
    
    // Dapatkan total data untuk pagination info
    $stmtCount = $pdo->prepare($countQuery);
    $stmtCount->execute($params);
    $totalItems = (int)$stmtCount->fetchColumn();
    
    // Tambahkan pagination & order
    $query .= " ORDER BY p.no_rkm_medis DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($query);
    
    // Bind limit & offset as integers (penting jika emulate prepares = false)
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    // Bind parameter lain (seperti :dob)
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    
    $stmt->execute();
    $patients = $stmt->fetchAll();
    
    $totalPages = ceil($totalItems / $limit);
    
    echo json_encode([
        'status' => 'success',
        'data' => $patients,
        'pagination' => [
            'current_page' => $page,
            'limit' => $limit,
            'total_items' => $totalItems,
            'total_pages' => $totalPages
        ]
    ]);
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data pasien: ' . $e->getMessage()
    ]);
}
?>
