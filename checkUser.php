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

    $email = $input['email'];
    $password = $input['password'];

    if (empty($email) || empty($password)) {
        echo json_encode(['message' => 'Email and password are required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Перевірка пароля з хешуванням
    if ($user && password_verify($password, $user['password'])) {
        $token = bin2hex(random_bytes(16));
        echo json_encode([
            'message' => 'User found',
            'token' => $token,
            'user' => $user
        ]);
    } else {
        echo json_encode(['message' => 'Invalid credentials']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
