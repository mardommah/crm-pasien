<?php
// api/save_wa_template.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Hanya menerima request POST']);
    exit;
}

// Ambil input JSON
$input = json_decode(file_get_contents('php://input'), true);
$template = isset($input['template']) ? $input['template'] : '';

if (empty($template)) {
    echo json_encode(['status' => 'error', 'message' => 'Template tidak boleh kosong']);
    exit;
}

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
    
    // Simpan atau update
    $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->execute([
        ':key' => 'wa_birthday_template',
        ':value' => $template
    ]);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Template berhasil disimpan di SQLite'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan ke database SQLite: ' . $e->getMessage()
    ]);
}
?>
