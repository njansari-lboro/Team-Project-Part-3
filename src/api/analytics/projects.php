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
    $project_id = $_GET["project_id"] ?? null;
    $tasks = $_GET["tasks"] ?? null;
    $taskCount = $_GET["taskCount"] ?? null;

    $permission = get_manager_or_admin($requester_id);
    switch ($method) {
    case "GET":
        if ($permission->id == $requester_id){
        if($project_id === null){
            echo json_encode(get_all_projects());
        } else if ($tasks == "true") {
            echo json_encode(get_project_tasks($project_id)); 
        } else if($taskCount != null){
            echo json_encode(get_project_task_count($project_id));
        } else{
            echo json_encode(get_project($project_id));
        }
    } else{
        if($project_id === null){
            echo json_encode(get_team_leader_projects($requester_id));
        } else if ($tasks == "true"  && $requester_id == get_project_team_leader($project_id)[0]->lead_id) {
            echo json_encode(get_project_tasks($project_id)); 
        } else if($taskCount != null && $requester_id == get_project_team_leader($project_id)[0]->lead_id){
            echo json_encode(get_project_task_count($project_id));
        } else if ($requester_id == get_project_team_leader($project_id)[0]->lead_id){
            echo json_encode(get_project($project_id));
        } else{
            echo "Access Denied";  
        }

    }
        break;
    case "POST":
        echo "Post";
        break;
    }

