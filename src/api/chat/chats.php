<?php
    header("Content-Type: application/json");

    session_start();

    if (empty($_SESSION["user"])) {
        http_response_code(401);
        die();
    }

    $current_user_id = $_SESSION["user"]->id;

    require_once(__DIR__ . "/../database/chat-db-helpers.php");

    $method = $_SERVER["REQUEST_METHOD"];

    $chat_id = $_GET["chat_id"] ?? null;

    if ($chat_id !== null && !is_user_member_of_chat($current_user_id, $chat_id)) {
        http_response_code(403);
        die();
    }

    switch ($method) {
    case "GET":
        $filter_text = $_GET["filter_text"] ?? null;

        if ($chat_id === null) {
            $chats = fetch_chats(user_id: $current_user_id, filter_text: $filter_text);
            echo json_encode($chats);
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
        $is_private = $_POST["is_private"] ?? null;
        $icon_name = $_POST["icon_name"] ?? null;

        if ($chat_id === null && $is_private !== null) {
            if ($is_private === "true") {
                $result = add_private_chat($current_user_id);
            } else {
                $result = add_group_chat($name, $icon_name, $current_user_id);
            }

            http_response_code($result ? 201 : 500);
        } else {
            http_response_code(400);
        }

        break;

    case "PUT":
        $put_data = file_get_contents("php://input");
        $params = json_decode($put_data, true);

        $name = $params["name"] ?? null;
        $icon_name = $params["icon_name"] ?? null;

        if ($chat_id !== null) {
            $result = update_chat($chat_id, name: $name, icon_name: $icon_name);
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
