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
