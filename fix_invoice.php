<?php
// fix_invoice.php
include 'db_connect.php';

echo "<h2>🔧 Repairing Invoices Table...</h2>";

// Check if 'service_name' column exists
$check = mysqli_query($conn, "SHOW COLUMNS FROM invoices LIKE 'service_name'");

if (mysqli_num_rows($check) == 0) {
    // Column is missing, so add it
    $sql = "ALTER TABLE invoices ADD COLUMN service_name VARCHAR(255) NOT NULL AFTER amount";

    if (mysqli_query($conn, $sql)) {
        echo "<h3 style='color:green'>✅ SUCCESS: Added 'service_name' column.</h3>";
    } else {
        echo "<h3 style='color:red'>❌ Error adding column: " . mysqli_error($conn) . "</h3>";
    }
} else {
    echo "<h3 style='color:blue'>ℹ️ Good news: The column 'service_name' already exists.</h3>";
}

echo "<h3>👉 You can now delete this file and try sending the invoice again!</h3>";
