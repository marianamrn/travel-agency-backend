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

    $city = isset($_GET['city']) ? $_GET['city'] : '';
    $rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
    $minPrice = isset($_GET['minPrice']) ? (int)$_GET['minPrice'] : 0;

    $sql = "
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
    ";

    $conditions = [];
    $params = [];

    if ($city) {
        $conditions[] = "h.city LIKE :city";
        $params[':city'] = '%' . $city . '%';
    }
    if ($rating) {
        $conditions[] = "h.rating >= :rating";
        $params[':rating'] = $rating;
    }
    if ($minPrice) {
        $conditions[] = "h.std_price >= :minPrice";
        $params[':minPrice'] = $minPrice;
    }

    if ($conditions) {
        $sql .= ' AND ' . implode(' AND ', $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($hotels as &$hotel) {
        $hotel['img_src'] = str_replace('\\', '/', $hotel['img_src']);
    }

    echo json_encode($hotels);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
