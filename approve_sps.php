<?php
include "config/db.php";

$id = intval($_GET['id']);

// Update status approval menjadi "Approved"
$stmt = $conn->prepare("UPDATE sps SET approval = 'Approved' WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php?approved=1");
} else {
    header("Location: index.php?error=1");
}
?>
