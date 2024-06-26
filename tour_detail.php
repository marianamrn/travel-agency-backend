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

    $tour_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $query = "
        SELECT 
            t.id, 
            t.name, 
            t.description, 
            t.included_services, 
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
        WHERE t.id = :tour_id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['tour_id' => $tour_id]);
    $tourData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tourData) {
        $tour = [
            'id' => $tourData[0]['id'],
            'name' => $tourData[0]['name'],
            'description' => $tourData[0]['description'],
            'included_services' => $tourData[0]['included_services'],
            'duration' => $tourData[0]['duration'],
            'price' => $tourData[0]['price'],
            'start_country' => $tourData[0]['start_country'],
            'end_country' => $tourData[0]['end_country'],
            'start_city' => $tourData[0]['start_city'],
            'end_city' => $tourData[0]['end_city'],
            'start_date' => $tourData[0]['start_date'],
            'category' => $tourData[0]['category'],
            'max_participants' => $tourData[0]['max_participants'],
            'images' => []
        ];

        foreach ($tourData as $data) {
            $tour['images'][] = [
                'img_src' => str_replace('\\', '/', $data['img_src']),
                'img_alt' => $data['img_alt']
            ];
        }

        echo json_encode($tour);
    } else {
        echo json_encode(['error' => 'Tour not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
