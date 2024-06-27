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
    $hotelId = $input['hotelId'];
    $name = $input['name'];
    $email = $input['email'];
    $phone = $input['phone'];
    $participants = $input['participants'];
    $roomType = $input['roomType'];

    if (empty($userId) || empty($hotelId) || empty($name) || empty($email) || empty($phone) || empty($participants) || empty($roomType)) {
        echo json_encode(['message' => 'All fields are required']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO hotel_reservations (user_id, hotel_id, name, email, phone, participants, room_type) VALUES (:userId, :hotelId, :name, :email, :phone, :participants, :roomType)");
    $stmt->execute([
        'userId' => $userId,
        'hotelId' => $hotelId,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'participants' => $participants,
        'roomType' => $roomType
    ]);

    echo json_encode(['success' => true, 'message' => 'Reservation successful']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'General error: ' . $e->getMessage()]);
}
?>
