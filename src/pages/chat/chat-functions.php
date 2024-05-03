<?php
    include_once(__DIR__ . "/../../database/users-db-helpers.php");

    $task = $_GET["task"] ?? die();

    switch ($task) {
    case "get_user":
        $user_id = $_POST["user_id"];
        $user = get_user($user_id);

        $path = null;
        $name = $user->profile_image_name;

        if ($name) {
            $absolute_path = realpath(__DIR__ . "/../uploads/user-profile-images/$name");
            $path = "/" . trim(str_replace($_SERVER["DOCUMENT_ROOT"], "", $absolute_path), "/");
        }

        $user->profile_image_path = $path;

        echo json_encode($user);

        break;

    case "get_chat_icon":
        $icon_name = $_POST["icon_name"] ?? null;

        $path = null;

        if ($icon_name) {
            $absolute_path = realpath(__DIR__ . "/../../uploads/chat-icon-images/$icon_name");
            $path = "/" . trim(str_replace($_SERVER["DOCUMENT_ROOT"], "", $absolute_path), "/");
        }

        echo json_encode($path);

        break;
    }
