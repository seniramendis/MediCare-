<?php
// repair_db.php
include 'db_connect.php';

echo "<h2>🚑 Repairing Database...</h2>";

// 1. Add missing 'patient_name' column
$check = mysqli_query($conn, "SHOW COLUMNS FROM appointments LIKE 'patient_name'");
if (mysqli_num_rows($check) == 0) {
    $sql = "ALTER TABLE appointments ADD COLUMN patient_name VARCHAR(100) AFTER patient_id";
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color:green'>✅ Fixed: Added 'patient_name' column.</p>";
    } else {
        echo "<p style='color:red'>❌ Error adding column: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color:blue'>ℹ️ Column 'patient_name' already exists.</p>";
}

// 2. Fix existing appointments (Populate empty names)
$fix_sql = "UPDATE appointments a 
            JOIN users u ON a.patient_id = u.id 
            SET a.patient_name = u.full_name 
            WHERE a.patient_name IS NULL OR a.patient_name = ''";
if (mysqli_query($conn, $fix_sql)) {
    echo "<p style='color:green'>✅ Fixed: Synced names for existing appointments.</p>";
}

echo "<h3>🚀 Database Repaired. Delete this file and reload your Dashboard.</h3>";
