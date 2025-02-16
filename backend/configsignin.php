<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "user_db";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
