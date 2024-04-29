<?php
    header("Content-Type: application/json");

    session_start();

    if (empty($_SESSION["user"])) {
        http_response_code(401);
        die();
    }

    require_once(__DIR__ . "/../database/chat-db-helpers.php");

    $method = $_SERVER["REQUEST_METHOD"];

    $user_id = $_GET["user_id"] ?? null;
    $chat_id = $_GET["chat_id"] ?? null;

    if ($chat_id !== null && !is_user_member_of_chat($_SESSION["user"]->id, $chat_id)) {
        http_response_code(403);
        die();
    }

    switch ($method) {
    case "GET":
        if ($user_id === null && $chat_id !== null) {
            echo json_encode(fetch_users_in_chat(chat_id: $chat_id));
        } else {
            http_response_code(400);
        }

        break;

    case "POST":
        $user_id = $_POST["user_id"] ?? null;

        if ($user_id !== null && $chat_id !== null) {
            $result = add_user_to_chat($user_id, $chat_id);
            http_response_code($result ? 201 : 500);
        } else {
            http_response_code(400);
        }

        break;

    case "DELETE":
        if ($user_id !== null && $chat_id !== null) {
            $result = delete_user_from_chat($user_id, $chat_id);
            http_response_code($result ? 204 : 404);
        } else {
            http_response_code(400);
        }

        break;

    default:
        http_response_code(405);
    }
