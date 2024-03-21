<?php
    include __DIR__ . "/../../database/projects-db-helpers.php";

    $project_id = $_POST["project_id"] ?? null;
    $task_id = $_POST["task_id"] ?? null;
    $assignee = $_POST["assignee"] ?? null;
    $name = $_POST["name"] ?? null;
    $description = $_POST["description"] ?? null;
    $resource_hours = $_POST["resource_hours"] ?? null;

    // if an employee assigned to an task who was not previously a part of a given project, add them to the project
    if ($assignee !== null && !is_user_project_team_member($assignee, $project_id)) {
        add_project_team_member($assignee, $project_id);
    }

    // update an already existing task
    update_task(task_id: $task_id, name: $name, assigned_user_id: $assignee, description: $description, resource_hours: $resource_hours);
