<?php
    include(__DIR__ . "/../database/users-db-helpers.php");

    session_start();

    $user_email = filter_var($_POST["email"] ?? null, FILTER_SANITIZE_EMAIL);
    $user_password = $_POST["password"] ?? null;

    if ($user_email && $user_password) {
        $user = get_user_from_email($user_email);

        if ($user && password_verify($user_password, $user->password_hash)) {
            $_SESSION["user"] = $user;

            session_regenerate_id();

            echo "true";
        } else {
            echo "false";
        }
    } else {
        echo "false";
    }
