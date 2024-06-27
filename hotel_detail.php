<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$host = '127.0.0.1:3308';
$dbname = 'tour_database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $hotel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $query = "SELECT * FROM hotels WHERE id = :hotel_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['hotel_id' => $hotel_id]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

    $query_images = "SELECT src, alt FROM img WHERE hotel_id = :hotel_id";
    $stmt_images = $pdo->prepare($query_images);
    $stmt_images->execute(['hotel_id' => $hotel_id]);
    $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

    if ($hotel) {
        $hotel['img_src'] = $hotel['img_src'] ?? ''; // Встановіть значення за замовчуванням, якщо img_src не встановлений
        echo json_encode(['hotel' => $hotel, 'images' => $images]);
    } else {
        echo json_encode(['error' => 'Hotel not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
