<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");


// Aktifkan debugging untuk sementara (pastikan dinonaktifkan di produksi)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bersihkan buffer output untuk mencegah output yang mengganggu JSON
ob_clean();
ob_start();

// Koneksi database
$host = "localhost";
$user = "root";
$password = "";
$database = "user_db";

$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi database
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Gagal terhubung ke database: " . $conn->connect_error]));
}

// Periksa apakah request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari request
    $name = isset($_POST["signup-name"]) ? trim($_POST["signup-name"]) : "";
    $email = isset($_POST["signup-email"]) ? trim($_POST["signup-email"]) : "";
    $phone = isset($_POST["signup-phone"]) ? trim($_POST["signup-phone"]) : "";
    $password = isset($_POST["signup-password"]) ? trim($_POST["signup-password"]) : "";

    // Debug: Simpan data POST ke file (hapus ini setelah debugging)
    file_put_contents("debug_log.txt", print_r($_POST, true), FILE_APPEND);

    // Validasi input kosong
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        die(json_encode(["status" => "error", "message" => "Harap isi semua kolom!"]));
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(["status" => "error", "message" => "Format email tidak valid!"]));
    }

    // Periksa apakah email atau nomor telepon sudah terdaftar
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die(json_encode(["status" => "error", "message" => "Email atau nomor telepon sudah digunakan!"]));
    }
    $stmt->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Simpan data ke database
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Pendaftaran berhasil!"]);
    } else {
        die(json_encode(["status" => "error", "message" => "Kesalahan saat menyimpan data: " . $stmt->error]));
    }

    $stmt->close();
}

$conn->close();
ob_end_flush(); // Tutup output buffer untuk memastikan hanya JSON yang dikirim
