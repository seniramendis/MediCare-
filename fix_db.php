<?php

include 'db_connect.php';

echo "<h2>🛠️ Fixing Database Structure...</h2>";


$check = mysqli_query($conn, "SHOW COLUMNS FROM appointments LIKE 'patient_name'");
if (mysqli_num_rows($check) == 0) {

    $sql = "ALTER TABLE appointments ADD COLUMN patient_name VARCHAR(255) AFTER patient_id";
    if (mysqli_query($conn, $sql)) {
        echo "<h3 style='color:green'>✅ SUCCESS: Added 'patient_name' column.</h3>";
    } else {
        echo "<h3 style='color:red'>❌ Error: " . mysqli_error($conn) . "</h3>";
    }
} else {
    echo "<h3 style='color:blue'>ℹ️ Column 'patient_name' already exists. Good.</h3>";
}


$update = "UPDATE appointments a 
           JOIN users u ON a.patient_id = u.id 
           SET a.patient_name = u.full_name 
           WHERE a.patient_name IS NULL OR a.patient_name = ''";
mysqli_query($conn, $update);
echo "<p>Synced existing appointment names.</p>";

echo "<h3>👉 Now delete this file and try booking again!</h3>";
