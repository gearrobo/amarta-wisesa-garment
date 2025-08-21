<?php
include 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

// Validasi status yang diizinkan
$allowed_status = ['pending', 'proses', 'selesai', 'batal'];
if (!in_array($status, $allowed_status)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit();
}

// Update status
$sql = "UPDATE persiapan SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status']);
}

$stmt->close();
$conn->close();
?>
