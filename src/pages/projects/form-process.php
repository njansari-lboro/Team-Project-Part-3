<?php
    include_once __DIR__ . "/../../database/users-db-helpers.php";
    include_once __DIR__ . "/../../database/projects-db-helpers.php";

    //check if the "project id" parameter exists in the URL
    //retrieve the value of the "id" parameter
    $project_id = $_GET["id"] ?? null;

    //check if the request method is POST
    //validate and sanitize form data
    $task_name = trim($_POST["task_name"] ?? "");
    $task_description = trim($_POST["task_description"] ?? "");
    $start_date = $_POST["start_date"] ?? "";
    $resource_hours = isset($_POST["resource_hours"]) ? filter_var($_POST["resource_hours"], FILTER_VALIDATE_FLOAT) : null;
    $end_date = $_POST["end_date"] ?? "";
    $assignee = trim($_POST["assignee"] ?? "");

    function get_assignee(string $assignee): ?object {
        $sql = "SELECT * FROM user WHERE full_name = ?";
        return get_record($sql, "s", $assignee);
    }

    //retrieve user and project IDs from database
    $user = get_assignee($assignee);
    $user_id = $user?->id;
    //echo($user_id);
    $project = get_project($project_id); 
    $project_id = $project?->id;
    //echo($project_id);
    $owner = $project?->owner_id;
    //echo($owner);

    //check if user and project IDs are valid
    if ($user_id === null || $project_id === null || $owner === null) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Invalid user or project"]);

        exit;
    }
    //echo "im before insert";
    //insert task into database
    $sql = "INSERT INTO task (project_id, assigned_user_id, name, description, owner_id, start_date, resource_hours, estimated_end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissisds", $project_id, $user_id, $task_name, $task_description, $owner, $start_date, $resource_hours, $end_date);

    if ($stmt->execute()) {
        //task added successfully
        //echo("successfully added to db");
        //echo json_encode(array("success" => true, "message" => "Task added successfully"));
        //get the ID of the inserted task
        $task_id = $conn->insert_id;

        //return the ID as a JSON response
        echo json_encode(["task_id" => $task_id]);
    } else {
        //failed to add task
        http_response_code(500); // Internal Server Error
        //echo("cannot connect 2");
        echo json_encode(["error" => "Failed to add task"]);
    }

    error_reporting(E_ALL);
    ini_set("display_errors", 1);
