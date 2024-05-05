<?php
    /*
    HTTP GET /
    */
    session_start();
    require_once(__DIR__ . "/../database/analytics-db-helpers.php");
    header("Content-Type: application/json");
    /*
    if (empty($_SESSION["user"])) {
        echo json_encode(["error" => "Not logged in"]);
        die();
    }
    */
    $method = $_SERVER["REQUEST_METHOD"];
    $requester_id = $_SESSION["user"]->id;
    $user_id = $_GET["user_id"] ?? null;
    $project_id = $_GET["project_id"] ?? null;


    switch ($method) {
    case "GET":
        if ($user_id === null){
            echo json_encode(get_user_task_stats($requester_id));
        } else if ($project_id === null){
            echo json_encode(get_user_tasks($user_id));
        } else if ($project_id == -1){
            echo json_encode(get_user_projects($user_id));
        } else{
            
        }
        break;
    }
