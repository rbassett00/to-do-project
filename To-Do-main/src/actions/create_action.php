<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../views/login.php");
    exit();
}

// Database connection
$mysql_servername = getenv("MYSQL_SERVERNAME");
$mysql_user = getenv("MYSQL_USER");
$mysql_password = getenv("MYSQL_PASSWORD");
$mysql_database = getenv("MYSQL_DATABASE");
$conn = new mysqli($mysql_servername, $mysql_user, $mysql_password, $mysql_database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['id'];
$text = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
$date = $_POST['date'];

$stmt = $conn->prepare("INSERT INTO task (user_id, text, date, done) VALUES (?, ?, ?, 0)");
$stmt->bind_param("iss", $user_id, $text, $date);

if ($stmt->execute()) {
    header("Location: ../index.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
