<?php
    require_once(__DIR__ . "/../database/chat-db-helpers.php");

    header("Content-Type: application/json");

    if (empty($_SESSION["user"])) {
        http_response_code(401);
        die();
    }

    $method = $_SERVER["REQUEST_METHOD"];

    $chat_id = $_GET["chat_id"] ?? null;
    $message_id = $_GET["message_id"] ?? null;

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
        $author_id = $_POST["author_id"] ?? null;
        $body = $_POST["body"] ?? null;

        if ($chat_id !== null && $message_id === null && $author_id !== null && $body !== null) {
            $result = add_message($chat_id, $author_id, $body);
            http_response_code($result ? 201 : 500);
        } else {
            http_response_code(400);
        }

        break;

//    case "PUT":
//        $put_data = file_get_contents("php://input");
//        parse_str($put_data, $params);
//
//        $body = $params["body"] ?? null;
//
//        if ($chat_id !== null && $message_id !== null) {
//            $result = update_message($message_id, $body);
//            http_response_code($result ? 204 : 404);
//        } else {
//            http_response_code(400);
//        }
//
//        break;

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
