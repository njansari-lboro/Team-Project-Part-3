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

    if (empty($_SESSION["user"])) {
        echo json_encode(["error" => "Not logged in"]);
        die();
    }

    $method = $_SERVER["REQUEST_METHOD"];

    $chat_id = $_GET["chat_id"] ?? null;

    switch ($method) {
    case "GET":
        if ($chat_id === null) {
            echo json_encode(fetch_chats());
        } else {
            echo json_encode(get_chat($chat_id));
        }

        break;

    case "POST":
        $name = $_POST["name"] ?? null;

        if ($chat_id === null && $name !== null) {
            add_chat($name);
        } else {
            http_response_code(400);
        }

        break;

    case "PUT":
        $name = $_POST["name"] ?? null;

        if ($chat_id === null) {
            http_response_code(400);
        } else {
            update_chat($chat_id, name: $name);
        }

        break;

    case "DELETE":
        if ($chat_id === null) {
            http_response_code(400);
        } else {
            delete_chat($chat_id);
        }

        break;
    }
