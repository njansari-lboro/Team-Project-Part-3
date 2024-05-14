<?php
    header("Content-Type: application/json");

    session_start();

    if (empty($_SESSION["user"])) {
        http_response_code(401);
        die();
    }

    require_once(__DIR__ . "/../database/chat-db-helpers.php");

    $method = $_SERVER["REQUEST_METHOD"];

    $chat_id = $_GET["chat_id"] ?? null;
    $message_id = $_GET["message_id"] ?? null;

    if ($chat_id !== null && !is_user_member_of_chat($_SESSION["user"]->id, $chat_id)) {
        http_response_code(403);
        die();
    }

    switch ($method) {
    case "GET":
        if ($chat_id !== null) {
            if ($message_id === null) {
                echo json_encode(fetch_messages(chat_id: $chat_id));
            } else {
                $message = get_message($message_id);

                if ($message) {
                    echo json_encode($message);
                } else {
                    http_response_code(404);
                }
            }
        } else {
            http_response_code(400);
        }

        break;

    case "POST":
        $body = $_POST["body"] ?? null;

        if ($chat_id !== null && $message_id === null && $body !== null) {
            $result = add_message($chat_id, $_SESSION["user"]->id, $body);
            http_response_code($result ? 201 : 500);
        } else {
            http_response_code(400);
        }

        break;

    case "DELETE":
        if ($chat_id !== null && $message_id !== null) {
            $result = delete_message($message_id);
            http_response_code($result ? 204 : 404);
        } else {
            http_response_code(400);
        }

        break;

    default:
        http_response_code(405);
    }
