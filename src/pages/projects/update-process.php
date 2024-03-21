<?php
    include_once __DIR__ . "/../../database/users-db-helpers.php";
    include_once __DIR__ . "/../../database/projects-db-helpers.php";

    //check if the request method is POST
    //validate and sanitize form data
    $task_id = trim($_POST["task_id"] ?? "");
    $task_name = trim($_POST["task_name"] ?? "");
    $task_description = trim($_POST["task_description"] ?? "");
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";
    $resource_hours = isset($_POST["resource_hours"]) ? filter_var($_POST["resource_hours"], FILTER_VALIDATE_FLOAT) : null;
    $assignee = trim($_POST["assignee"] ?? "");

    function get_assignee(string $assignee): ?object {
        $sql = "SELECT * FROM user WHERE full_name = ?";
        return get_record($sql, "s", $assignee);
    }

    // get task id from database
    $user = get_assignee($assignee);
    $user_id = $user?->id;
    // use the prebuilt helpers to update the database
    $result = update_task($task_id, $task_name, $user_id, $task_description, $start_date, $end_date, $resource_hours);

    if ($result === false) {
        echo json_encode(["error" => "Failed to update task"]);
    } else {
        echo "success";
    }
