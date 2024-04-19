<?php
    require_once(__DIR__ . "/database/analytics-db-helpers.php");

    $action = htmlspecialchars($_GET["action"]);

    switch ($action) {
    case "repeat":
        echo "Action is $action";
        break;
    default:
        return;
    }
