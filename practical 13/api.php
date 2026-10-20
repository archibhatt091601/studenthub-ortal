<?php
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

$action = $_GET["action"] ?? "";

if ($action === "search") {
    $q = trim($_GET["q"] ?? "");
    $stmt = $pdo->prepare("SELECT id, name, email, mobile, course, year, gender, status FROM students WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT 50");
    $stmt->execute(["%$q%", "%$q%"]);
    $rows = $stmt->fetchAll();
    echo json_encode(["status" => "success", "data" => $rows]);
    exit;
}

if ($action === "list") {
    $stmt = $pdo->query("SELECT id, name, email, mobile, course, year, gender, status FROM students ORDER BY id DESC");
    $rows = $stmt->fetchAll();
    echo json_encode(["status" => "success", "data" => $rows]);
    exit;
}

if ($action === "create" && ($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $mobile = trim($input["mobile"] ?? "");
    $course = trim($input["course"] ?? "");
    $year = trim($input["year"] ?? "");
    $gender = trim($input["gender"] ?? "Male");

    if (empty($name) || empty($email)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Name and email are required."]);
        exit;
    }

    try {
        $check = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "A student with this email already exists."]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO students (name, email, mobile, course, year, gender, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$name, $email, $mobile, $course, $year, $gender]);
        $newId = $pdo->lastInsertId();

        echo json_encode([
            "status" => "success",
            "message" => "Student added successfully via Fetch API!",
            "data" => [
                "id" => $newId,
                "name" => $name,
                "email" => $email,
                "mobile" => $mobile,
                "course" => $course,
                "year" => $year,
                "gender" => $gender,
                "status" => "active"
            ]
        ]);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit;
    }
}

if ($action === "delete" && ($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $raw = file_get_contents("php://input");
    $input = json_decode($raw, true);
    if (!is_array($input)) {
        $input = $_POST;
    }
    $id = intval($input["id"] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid student ID."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "success", "message" => "Student #$id deleted successfully via AJAX!"]);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit;
    }
}

http_response_code(400);
echo json_encode(["status" => "error", "message" => "Unknown action requested."]);
exit;
?>