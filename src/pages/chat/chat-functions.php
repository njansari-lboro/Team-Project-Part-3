<?php
    $task = $_GET["task"] ?? null;

    if ($task) {
        include_once(__DIR__ . "/../../database/users-db-helpers.php");

        switch ($task) {
        case "get_user":
            $user_id = $_POST["user_id"];
            $user = get_user($user_id);
            $user->profile_image_path = get_user_pfp($user);

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

        exit();
    }

    // Gets the full path (from root) of the specified user's profile image
    function get_user_pfp(object $user): ?string {
        $path = null;
        $name = $user->profile_image_name;

        if ($name) {
            $absolute_path = realpath(__DIR__ . "/../uploads/user-profile-images/$name");
            $path = "/" . trim(str_replace($_SERVER["DOCUMENT_ROOT"], "", $absolute_path), "/");
        }

        return $path;
    }
