<?php
// fix_column.php
include 'db_connect.php';

echo "<h2>🔧 Fixing Database Column Name...</h2>";

// This SQL command renames 'service_description' to 'service_name'
$sql = "ALTER TABLE invoices CHANGE service_description service_name VARCHAR(255) NOT NULL";

if (mysqli_query($conn, $sql)) {
    echo "<h3 style='color:green'>✅ SUCCESS: Renamed 'service_description' to 'service_name'.</h3>";
} else {
    // If it fails, maybe the column is already named correctly or doesn't exist?
    echo "<h3 style='color:red'>❌ Error: " . mysqli_error($conn) . "</h3>";
    echo "<p>Check if the column is already named 'service_name' in phpMyAdmin.</p>";
}

echo "<h3>👉 You can now delete this file and try sending the invoice again!</h3>";
