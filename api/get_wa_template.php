<?php
// api/get_wa_template.php
header('Content-Type: application/json');

$db_dir = __DIR__ . '/../db';
if (!file_exists($db_dir)) {
    mkdir($db_dir, 0777, true);
}

$db_path = $db_dir . '/settings.sqlite';

try {
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buat tabel jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )");
    
    // Ambil template
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->execute([':key' => 'wa_birthday_template']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $default_template = "Halo {nama},\n\nKami dari Klinik Syamsinar ingin mengucapkan Selamat Hari Ulang Tahun! Semoga sehat selalu dan dilancarkan rezekinya. Terima kasih telah mempercayakan layanan kesehatan Anda bersama kami.";
    
    $template = $row ? $row['value'] : $default_template;
    
    echo json_encode([
        'status' => 'success',
        'template' => $template
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal membaca database SQLite: ' . $e->getMessage()
    ]);
}
?>
