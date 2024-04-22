<?php
    /*
    HTTP GET /chats            // Get all chats
    HTTP POST /chats           // Create new chat

    HTTP GET /chats/{id}       // Get chat for given id
    HTTP PUT /chats/{id}       // Update chat for given id
    HTTP DELETE /chats/{id}    // Delete chat for given id
    */

    require_once(__DIR__ . "/../database/chat-db-helpers.php");

    header("Content-Type: application/json");

    session_start();

    if (empty($_SESSION["user"])) {
        http_response_code(401);
        die();
    }

    $method = $_SERVER["REQUEST_METHOD"];

    $chat_id = $_GET["chat_id"] ?? null;

    switch ($method) {
    case "GET":
        if ($chat_id === null) {
            echo json_encode(fetch_chats());
        } else {
            $chat = get_chat($chat_id);

            if ($chat) {
                echo json_encode($chat);
            } else {
                http_response_code(404);
            }
        }

        break;

    case "POST":
        $name = $_POST["name"] ?? null;

        if ($chat_id === null && $name !== null) {
            $result = add_chat($name);
            http_response_code($result ? 201 : 500);
        } else {
            http_response_code(400);
        }

        break;

    case "PUT":
        $put_data = file_get_contents("php://input");
        parse_str($put_data, $params);

        $name = $params["name"] ?? null;

        if ($chat_id !== null) {
            $result = update_chat($chat_id, name: $name);
            http_response_code($result ? 204 : 404);
        } else {
            http_response_code(400);
        }

        break;

    case "DELETE":
        if ($chat_id !== null) {
            $result = delete_chat($chat_id);
            http_response_code($result ? 204 : 404);
        } else {
            http_response_code(400);
        }

        break;

    default:
        http_response_code(405);
    }
