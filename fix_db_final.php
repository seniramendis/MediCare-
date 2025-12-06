<?php
// fix_db_final.php
include 'db_connect.php';

echo "<h2>🚑 Database Auto-Repair</h2>";

// 1. Add 'patient_name' if missing
$check = mysqli_query($conn, "SHOW COLUMNS FROM appointments LIKE 'patient_name'");
if (mysqli_num_rows($check) == 0) {
    // It's missing, so we add it
    $sql = "ALTER TABLE appointments ADD COLUMN patient_name VARCHAR(100) NOT NULL AFTER doctor_id";
    if (mysqli_query($conn, $sql)) {
        echo "<h3 style='color:green'>✅ SUCCESS: Added 'patient_name' column.</h3>";
    } else {
        echo "<h3 style='color:red'>❌ Error: " . mysqli_error($conn) . "</h3>";
    }
} else {
    echo "<h3 style='color:blue'>ℹ️ Column 'patient_name' already exists. Good.</h3>";
}

// 2. Sync existing names
// This fills the new empty column with names from the users table so old appointments appear too
$update = "UPDATE appointments a 
           JOIN users u ON a.patient_id = u.id 
           SET a.patient_name = u.full_name 
           WHERE a.patient_name IS NULL OR a.patient_name = ''";
mysqli_query($conn, $update);
echo "<p>Synced names for existing appointments.</p>";

echo "<h3>👉 Now delete this file and try your dashboard again!</h3>";
