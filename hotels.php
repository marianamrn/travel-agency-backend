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

    $stmt = $pdo->prepare("
        SELECT 
            h.id, 
            h.name, 
            h.rating, 
            h.city, 
            h.std_price, 
            i.src AS img_src, 
            i.alt AS img_alt
        FROM hotels h
        JOIN img i ON h.id = i.hotel_id
        WHERE i.alt = 'hotel-cover'
    ");
    $stmt->execute();
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Замінюємо зворотні слеші на прямі
    foreach ($hotels as &$hotel) {
        $hotel['img_src'] = str_replace('\\', '/', $hotel['img_src']);
    }

    echo json_encode($hotels);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
