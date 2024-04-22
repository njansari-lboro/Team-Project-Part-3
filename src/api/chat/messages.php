<?php
    /*
    HTTP GET /chats/{id}/messages            // Get all messages in chat with given id
    HTTP POST /chats/{id}/messages           // Create new message in chat with given id

    HTTP GET /chats/{id}/messages/{id}       // Get message for given id in chat with given id
    HTTP PUT /chats/{id}/messages/{id}       // Update message for given id in chat with given id
    HTTP DELETE /chats/{id}/messages/{id}    // Delete message for given id in chat with given id
    */

    require_once(__DIR__ . "/../database/chat-db-helpers.php");

    header("Content-Type: application/json");

    if (empty($_SESSION["user"])) {
        echo json_encode(["error" => "Not logged in"]);
        die();
    }

    $method = $_SERVER["REQUEST_METHOD"];

    $chat_id = $_GET["chat_id"] ?? null;
    $message_id = $_GET["message_id"] ?? null;

    switch ($method) {
    case "GET":
        if ($chat_id === null) {
            http_response_code(400);
        } else {
            if ($message_id === null) {
                echo json_encode(fetch_messages(chat_id: $chat_id));
            } else {
                echo json_encode(get_message($message_id));
            }
        }

        break;

    case "POST":
        $author_id = $_POST["author_id"] ?? null;
        $body = $_POST["body"] ?? null;

        if ($chat_id !== null && $message_id === null && $author_id !== null && $body !== null) {
            add_message($chat_id, $author_id, $body);
        } else {
            http_response_code(400);
        }

        break;

    case "PUT":
        $body = $_POST["body"] ?? null;

        if ($chat_id === null || $message_id === null) {
            http_response_code(400);
        } else {
            update_message($message_id, $body);
        }

        break;

    case "DELETE":
        if ($chat_id === null || $message_id === null) {
            http_response_code(400);
        } else {
            delete_message($message_id);
        }

        break;
    }
