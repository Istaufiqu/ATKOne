<?php
header("Content-Type: application/json");
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$database = "user_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Koneksi database gagal: " . $conn->connect_error]));
}

// Ambil data yang dikirim dari JavaScript
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

// Validasi input
if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "error" => "Email dan password wajib diisi!"]);
    exit;
}

// Cek apakah email ada di database
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["success" => false, "error" => "Email tidak terdaftar"]);
    exit;
}

// Verifikasi password dengan password_verify()
if (!password_verify($password, $user["password"])) {
    echo json_encode(["success" => false, "error" => "Password salah"]);
    exit;
}

// Jika login berhasil, simpan session
$_SESSION["user_id"] = $user["id"];
$_SESSION["email"] = $user["email"];

echo json_encode(["success" => true, "message" => "Login berhasil"]);
