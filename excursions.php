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

    $query = "
        SELECT 
            e.id, 
            e.name, 
            e.city, 
            e.country,
            e.duration, 
            e.price, 
            i.src AS img_src, 
            i.alt AS img_alt
        FROM excursions e
        JOIN img i ON e.id = i.excursion_id
        WHERE i.alt = 'excursion-cover'
    ";

    $params = [];
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        if (!empty($input['location'])) {
            $query .= " AND e.city = :location";
            $params['location'] = $input['location'];
        }
        if (!empty($input['date'])) {
            $query .= " AND e.date = :date";
            $params['date'] = $input['date'];
        }
        if (!empty($input['duration'])) {
            $query .= " AND e.duration = :duration";
            $params['duration'] = $input['duration'];
        }
        if (!empty($input['category'])) {
            $query .= " AND e.category = :category";
            $params['category'] = $input['category'];
        }
        if (!empty($input['price_min'])) {
            $query .= " AND e.price >= :price_min";
            $params['price_min'] = $input['price_min'];
        }
        if (!empty($input['price_max'])) {
            $query .= " AND e.price <= :price_max";
            $params['price_max'] = $input['price_max'];
        }
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $excursions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Замінюємо зворотні слеші на прямі
    foreach ($excursions as &$excursion) {
        $excursion['img_src'] = str_replace('\\', '/', $excursion['img_src']);
    }

    echo json_encode($excursions);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
