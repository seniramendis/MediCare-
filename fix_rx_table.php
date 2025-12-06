<?php

include 'db_connect.php';

echo "<h2>💊 Fixing Prescriptions Table...</h2>";


$check = mysqli_query($conn, "SHOW COLUMNS FROM prescriptions LIKE 'dosage_instructions'");

if (mysqli_num_rows($check) == 0) {

    $sql = "ALTER TABLE prescriptions ADD COLUMN dosage_instructions TEXT AFTER medicine_list";

    if (mysqli_query($conn, $sql)) {
        echo "<h3 style='color:green'>✅ SUCCESS: Added 'dosage_instructions' column.</h3>";
    } else {
        echo "<h3 style='color:red'>❌ Error adding column: " . mysqli_error($conn) . "</h3>";
    }
} else {
    echo "<h3 style='color:blue'>ℹ️ Column 'dosage_instructions' already exists.</h3>";
}

echo "<h3>👉 You can now delete this file and try sending the prescription again!</h3>";
