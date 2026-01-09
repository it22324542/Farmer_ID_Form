<?php
$servername = "localhost";  // Default for XAMPP
$username = "root";         // Default PhpMyAdmin user
$password = "";             // Default is empty; set if you created a user
$dbname = "farmer_id_db";   // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


