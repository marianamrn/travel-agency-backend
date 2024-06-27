<?php
// api/news_detail.php
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

    $news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $query = "
        SELECT 
            n.id, 
            n.title, 
            n.content, 
            n.author, 
            n.published_at,
            i.src AS img_src, 
            i.alt AS img_alt
        FROM news n
        LEFT JOIN img i ON n.id = i.news_id
        WHERE n.id = :news_id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['news_id' => $news_id]);
    $newsItem = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($newsItem);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
