<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json;charset = utf-8");

$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}

ini_set("display_errors", 1);


require(__DIR__ . '/../../models/Users.php');
require(__DIR__ . '/../../modules/Database.php');
require(__DIR__.'/../../vendor/autoload.php');

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $email = $_SERVER['PHP_AUTH_USER'] ?? null;
    $password = $_SERVER['PHP_AUTH_PW'] ?? null;

    $connection = new Database("localhost", "root", "", "php_test_run");
    $user = new Users($connection->connect());

    if ($email) {
        $result = $user->get_user_by_email($email);
        $current_user = array();
        $current_user_password_hash = null;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $current_user['id'] = $row['id'];
                $current_user['name'] = $row['name'];
                $current_user['email'] = $row['email'];
                $current_user['created_at'] = $row['created_at'];
                $current_user['updated_at'] = $row['updated_at'];
                $current_user_password_hash = $row['password'];
            }
            if (password_verify($password, $current_user_password_hash)) {
                http_response_code(404);
                echo json_encode([
                    'error' => true,
                    'message' => $current_user,
                ]);
                exit();
            } else {
                http_response_code(400);
                echo json_encode([
                    'error' => true,
                    'message' => "invalid credentials",
                ]);
                exit();

            }

        } else {
            http_response_code(404);
            echo json_encode([
                'error' => true,
                'message' => "email does not exists",
            ]);
            exit();
        }

    } else {
        http_response_code(400);

        echo json_encode([
            'error' => true,
            'message' => "email missing",
        ]);
        exit();
    }




} else {
    http_response_code(405);

    echo json_encode([
        'error' => true,
        'message' => "method not allowed"
    ]);
    exit();
}