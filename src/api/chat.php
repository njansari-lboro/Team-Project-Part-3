<?php
    require_once(__DIR__ . "/database/chat-db-helpers.php");

    $action = htmlspecialchars($_GET["action"]);

    echo "Action is $action";
    echo "Chats are " . json_encode(fetch_chats());
