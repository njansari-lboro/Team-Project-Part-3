<?php
    include(__DIR__ . "/../database/users-db-helpers.php");

    $first_name = $_POST["firstName"] ?? null;
    $last_name = $_POST["lastName"] ?? null;
    $password = $_POST["password"] ?? null;
    $user_id = $_POST["userID"] ?? null;

    if ($first_name && $last_name && $password && $user_id) {
        $result = register_user($user_id, $first_name, $last_name, $password);

        if ($result) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Error saving user."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Required data not provided."]);
    }
