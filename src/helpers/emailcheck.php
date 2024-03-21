<?php
    include(__DIR__ . "/../database/users-db-helpers.php");

    session_start();

    $user_email = filter_var($_POST["email"] ?? null, FILTER_SANITIZE_EMAIL);

    if ($user_email) {
        $user = get_user_from_email($user_email);

        if ($user) {
            echo json_encode([
                "status" => $user->registered ? "registered" : "unregistered",
                "userID" => $user->id
            ]);
        } else {
            echo "false";
        }
    } else {
        echo "false";
    }
