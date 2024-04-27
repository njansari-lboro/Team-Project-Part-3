<?php
    /*
    HTTP GET /
    */
    require_once(__DIR__ . "/../database/analytics-db-helpers.php");
    header("Content-Type: application/json");
    /*
    if (empty($_SESSION["user"])) {
        echo json_encode(["error" => "Not logged in"]);
        die();
    }
    */
    $method = $_SERVER["REQUEST_METHOD"];

    $project_id = $_GET["project_id"] ?? null;
    $tasks = $_GET["tasks"] ?? null;


    switch ($method) {
    case "GET":
        if($project_id === null){
            echo json_encode(fetch_projects());
        } else if ($tasks == "true") {
            echo json_encode(get_project_tasks($project_id)); 
        } else{
            echo json_encode(get_project($project_id));
        }
        break;
    case "POST":
        echo "Post";
        break;
    }
