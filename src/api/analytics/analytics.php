<?php
    require_once(__DIR__ . "/../database/analytics-db-helpers.php");
    header("Content-Type: application/json");

    if (empty($_SESSION["user"])) {
        echo json_encode(["error" => "Not logged in"]);
        die();
    }

    $method = $_SERVER["REQUEST_METHOD"];

    $user_id = $_GET["user_id"] ?? null;
    $project_id = $_GET["project_id"] ?? null;
    $task_id = $_GET["task_id"] ?? null;


    switch ($method) {
    case "GET":
        echo "Get";
        break;
    case "POST":
        echo "Post";
        break;
    }
