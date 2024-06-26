<?php
// api/excursion_detail.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$host = '127.0.0.1:3308';
$dbname = 'tour_database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $excursionId = isset($_GET['id']) ? $_GET['id'] : null;

    if ($excursionId) {
        $query = "
            SELECT 
                e.id, 
                e.name, 
                e.description, 
                e.language, 
                e.duration, 
                e.date, 
                e.start_time, 
                e.end_time,
                e.max_participants,
                e.price,
                e.meeting_point,
                e.city,
                e.country,
                i.src AS img_src, 
                i.alt AS img_alt
            FROM excursions e
            LEFT JOIN img i ON e.id = i.excursion_id
            WHERE e.id = :id
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $excursionId, PDO::PARAM_INT);
        $stmt->execute();
        $excursion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($excursion) {
            $imagesQuery = "SELECT src AS img_src, alt AS img_alt FROM img WHERE excursion_id = :id";
            $imagesStmt = $pdo->prepare($imagesQuery);
            $imagesStmt->bindParam(':id', $excursionId, PDO::PARAM_INT);
            $imagesStmt->execute();
            $images = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);

            $excursion['images'] = $images;
            echo json_encode($excursion);
        } else {
            echo json_encode(['error' => 'Excursion not found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid excursion ID']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
