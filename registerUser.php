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

    $name = $input['name'];
    $email = $input['email'];
    $password = $input['password'];

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['message' => 'All fields are required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['message' => 'Email already in use']);
        exit;
    }

    // Хешування пароля
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :password, NOW())");
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => $hashedPassword,
    ]);

    echo json_encode(['message' => 'User registered successfully']);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
