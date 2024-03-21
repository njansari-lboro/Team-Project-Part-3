<?php
    // Initialise the session
    session_start();

    // Unset all the session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Regenerate session ID for security
    session_regenerate_id();

    header("Location: ../");
    exit();
