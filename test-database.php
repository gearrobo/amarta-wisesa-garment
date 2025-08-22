<?php
// Test database connection
require_once 'config/db.php';

// Test query
$query = "SELECT COUNT(*) as total FROM suplier";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "Database connection successful! Total suppliers: " . $row['total'];
} else {
    echo "Database connection failed: " . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?>
