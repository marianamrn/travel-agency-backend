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
            t.id, 
            t.name, 
            t.duration, 
            t.price, 
            t.start_country, 
            t.end_country,
            t.start_city, 
            t.end_city,
            t.start_date,
            t.category,
            t.max_participants,
            i.src AS img_src, 
            i.alt AS img_alt
        FROM tours t
        JOIN img i ON t.id = i.tour_id
        WHERE i.alt = 'tour-cover'
    ";

    $params = [];
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input) {
        if (!empty($input['start_city'])) {
            $query .= " AND t.start_city = :start_city";
            $params['start_city'] = $input['start_city'];
        }
        if (!empty($input['end_city'])) {
            $query .= " AND t.end_city = :end_city";
            $params['end_city'] = $input['end_city'];
        }
        if (!empty($input['start_date'])) {
            $query .= " AND t.start_date >= :start_date";
            $params['start_date'] = $input['start_date'];
        }
        if (!empty($input['duration'])) {
            $query .= " AND t.duration = :duration";
            $params['duration'] = $input['duration'];
        }
        if (!empty($input['category'])) {
            $query .= " AND t.category = :category";
            $params['category'] = $input['category'];
        }
        if (!empty($input['max_participants'])) {
            $query .= " AND t.max_participants >= :max_participants";
            $params['max_participants'] = $input['max_participants'];
        }
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Замінюємо зворотні слеші на прямі
    foreach ($tours as &$tour) {
        $tour['img_src'] = str_replace('\\', '/', $tour['img_src']);
    }

    echo json_encode($tours);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
