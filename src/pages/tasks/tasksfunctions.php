<?php
    // include("../database.php");
    include __DIR__ . "/../../database/projects-db-helpers.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Retrieve data from the POST request
        $function = $_POST["function"];

        switch ($function) {
        case 0:
            // If the function is complete a task
            $task_id = $_POST["taskId"];
            $current_user = $_POST["currentUser"];

            // Using helper functions
            $record = get_task($task_id);

            if ($record) {
                // Edit record to toggle complete

                if ($record->is_completed) {
                    update_task($task_id, is_completed: false);
                } else {
                    update_task($task_id, is_completed: true);
                }

                echo "Record completion successfully toggled";
            }

            break;
        case 1:
            // If the function is update the resource hours of a task
            $task_id = $_POST["taskId"];
            $resource_hours = $_POST["resourceHours"];

            // Using helper functions
            $record = get_task($task_id);

            if ($record) {
                update_task($task_id, hours_employed: $resource_hours);
            }

            echo "Resource hours successfully updated";

            break;
        }
    }
