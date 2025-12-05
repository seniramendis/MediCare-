<?php
include 'db_connect.php';

echo "<h2>Appointments Table Columns:</h2>";
$result = mysqli_query($conn, "SHOW COLUMNS FROM appointments");

if (!$result) {
    echo "Error: " . mysqli_error($conn);
} else {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li><strong>" . $row['Field'] . "</strong> (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
}
