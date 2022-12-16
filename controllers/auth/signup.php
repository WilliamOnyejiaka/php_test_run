<?php

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

require(__DIR__ . '/../../models/Users.php');
require(__DIR__ . '/../../modules/Database.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $body = json_decode(file_get_contents("php://input"));
    $name = $body->name ?? null;
    $email = $body->email ?? null;
    $password = $body->password ?? null;

    if (!$name) {
        http_response_code(400);
        echo json_encode(
            array(
                'error' => true,
                'message' => "name cannot be empty"
            )
        );
        exit();
    } elseif (!$email) {
        http_response_code(400);
        echo json_encode(
            array(
                'error' => true,
                'message' => "email cannot be empty"
            )
        );
        exit();
    } elseif (preg_match("/^([a-zA-Z0-9\.]+@+[a-zA-Z]+(\.)+[a-zA-Z]{2,3})$/", $email) == 0) {
        http_response_code(400);
        echo json_encode(
            array(
                'error' => true,
                'message' => "email is invalid"
            )
        );
        exit();
    } elseif (!$password) {
        http_response_code(400);
        echo json_encode(
            array(
                'error' => true,
                'message' => "password cannot be empty"
            )
        );
        exit();
    } elseif (strlen($password) < 3) {
        http_response_code(400);
        echo json_encode(
            array(
                'error' => true,
                'message' => "password length must be greater than 3"
            )
        );
        exit();
    } else {
        $connection = new Database("localhost", "root", "", "php_test_run");
        $users = new Users($connection->connect());

        $email_exits = $users->get_user_by_email($email);
        if ($email_exits->num_rows > 0) {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => true,
                    'message' => "email exists",
                )
            );
            exit();
        } else {
            if ($users->create_user($name, $email, $password)) {
                http_response_code(200);
                echo json_encode(
                    array(
                        'error' => false,
                        'message' => "user added successfully",
                    )
                );
                exit();
            } else {
                http_response_code(500);
                echo json_encode(
                    array(
                        'error' => true,
                        'message' => "something went wrong",
                    )
                );
                exit();
            }
        }

    }
}else {
    http_response_code(405);

    echo json_encode([
        'error' => true,
        'message' => "method not allowed"
    ]);
}