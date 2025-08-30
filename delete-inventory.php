<?php
session_start();
include "config/db.php";


// validasi id
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
    $_SESSION['error'] = 'ID inventory tidak valid!';
    header("Location: inventory.php");
    exit();
}

$id = (int) $_GET['id'];

try {
    $sql_delete = "DELETE FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Data inventory ID $id berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Data inventory dengan ID $id tidak ditemukan!";
    }

    $stmt->close();
    header("Location: inventory.php");
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: inventory.php");
    exit();
}

$conn->close();
