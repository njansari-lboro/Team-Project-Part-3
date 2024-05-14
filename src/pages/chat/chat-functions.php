<?php
    include_once(__DIR__ . "/../../database/users-db-helpers.php");

    $task = $_GET["task"] ?? die();

    session_start();

    switch ($task) {
    case "get_user":
        $user_id = $_POST["user_id"] ?? null;
        if ($user_id === null) return;

        $user = get_user($user_id);
        $user->profile_image_path = get_user_profile_image_path($user->profile_image_name);

        echo json_encode($user);

        break;

    case "get_chat_icon":
        $icon_name = $_POST["icon_name"] ?? null;

        $path = null;

        if ($icon_name) {
            $path = get_chat_icon_path($icon_name);
        }

        echo json_encode($path);

        break;

    case "fetch_users":
        $users = fetch_users();
        echo json_encode($users);

        break;

    case "get_user_profile_image":
        $user_id = $_POST["user_id"] ?? null;
        if ($user_id === null) return;

        $user = get_user($user_id);
        $profile_image_path = get_user_profile_image_path($user->profile_image_name);

        echo json_encode($profile_image_path);

        break;

    case "upload_chat_icon":
        $chat_icon = $_FILES["upload_chat_icon"] ?? null;
        if ($chat_icon === null) return;

        $extension = pathinfo($chat_icon["name"], PATHINFO_EXTENSION);

        $image_tmp_name = $chat_icon["tmp_name"];

        $file_name = uniqid() . ".$extension";
        $file_url = __DIR__ . "/../../uploads/chat-icon-images/$file_name";

        if (move_uploaded_file($image_tmp_name, $file_url)) {
            echo json_encode(["success" => true, "file_name" => $file_name]);
        } else {
            echo json_encode(["success" => false, "message" => "Error moving file"]);
        }

        break;
    }

    function get_user_profile_image_path($image_name): ?string {
        $path = null;

        if ($image_name) {
            $absolute_path = realpath(__DIR__ . "/../../uploads/user-profile-images/$image_name");
            $path = "/" . trim(str_replace($_SERVER["DOCUMENT_ROOT"], "", $absolute_path), "/");
        }

        return $path;
    }

    function get_chat_icon_path(string $icon_name): ?string {
        $path = null;

        if ($icon_name) {
            $absolute_path = realpath(__DIR__ . "/../../uploads/chat-icon-images/$icon_name");
            $path = "/" . trim(str_replace($_SERVER["DOCUMENT_ROOT"], "", $absolute_path), "/");
        }

        return $path;
    }
