<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$host = '127.0.0.1:3308';
$dbname = 'tour_database';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['message' => 'Invalid input']);
        exit;
    }

    $userId = $input['userId'];
    $tourId = $input['tourId'];
    $name = $input['name'];
    $email = $input['email'];
    $phone = $input['phone'];
    $participants = $input['participants'];

    if (empty($userId) || empty($tourId) || empty($name) || empty($email) || empty($phone) || empty($participants)) {
        echo json_encode(['message' => 'All fields are required']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO tour_reservations (user_id, tour_id, name, email, phone, participants) VALUES (:userId, :tourId, :name, :email, :phone, :participants)");
    $stmt->execute([
        'userId' => $userId,
        'tourId' => $tourId,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'participants' => $participants
    ]);

    echo json_encode(['success' => true, 'message' => 'Reservation successful']);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
