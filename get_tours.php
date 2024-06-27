<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$servername = "127.0.0.1:3308";
$username = "root";
$password = "";
$dbname = "tour_database";

// Створення підключення
$conn = new mysqli($servername, $username, $password, $dbname);

// Перевірка підключення
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Виконання SQL запиту
$sql = "SELECT img.id, img.src, img.alt 
        FROM tours 
        JOIN img ON tours.id = img.tour_id 
        WHERE img.alt = 'tour-cover'";
$result = $conn->query($sql);

$images = array();

if ($result->num_rows > 0) {
    // Виведення даних кожного рядка
    while($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
} else {
    echo json_encode(array("message" => "No images found."));
}

echo json_encode($images);

$conn->close();
?>
