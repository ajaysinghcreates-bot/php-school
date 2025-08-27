<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/db.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read SQL file
$sql = file_get_contents(__DIR__ . '/app/sql/database.sql');

if ($conn->multi_query($sql)) {
    echo "Database tables created successfully.<br>";
} else {
    echo "Error creating tables: " . $conn->error . "<br>";
}

// IMPORTANT: Wait for multi_query to finish
while ($conn->next_result()) {;}

// Create admin user
$username = 'admin';
$password = 'password'; // Change this!
$email = 'admin@example.com';
$fullName = 'Administrator';
$role = 'admin';

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $hashed_password, $email, $fullName, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully.<br>";
    echo "Username: admin<br>";
    echo "Password: password<br>";
} else {
    echo "Error creating admin user: " . $stmt->error . "<br>";
}

$stmt->close();
$conn->close();

echo "<hr><strong>Installation complete. Please delete this file (install.php) for security reasons.</strong>";

?>