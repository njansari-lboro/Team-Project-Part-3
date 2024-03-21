<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["projectId"])) {
        include_once "../database.php";
        connect_to_database();

        // receiving project ID to use as condition
        $id = $_POST["projectId"];

        // retrieving and formatting the deadline as d/m/Y, e.g. 19/02/2024
        $deadline = (get_records_sql("SELECT deadline FROM project WHERE id = $id"))[0]["deadline"];
        $deadline = (new DateTime($deadline))->format("d/m/Y");

        // retrieving tasks that are overdue, where the hours spent exceed the estimated resource hours 
        $overdue = get_records_sql("SELECT full_name AS emp_name, 
                                name, hours_spent, resource_hours FROM 
                                task JOIN user ON task.assigned_user_id = user.id 
                                WHERE task.project_id = $id AND task.is_completed = FALSE
                                AND task.hours_spent > task.resource_hours");
        // if no data found, set as an empty array
        $overdue = $overdue ? $overdue : [];

        // retrieving tasks that should be finished soon, where the difference between hours spent and resource hours is less than 5
        $imminent = get_records_sql("SELECT full_name AS emp_name, name, hours_spent, resource_hours 
                                FROM task JOIN user ON task.assigned_user_id = user.id  
                                WHERE project_id = $id
                                AND task.is_completed = FALSE
                                AND (task.hours_spent < task.resource_hours) 
                                AND (resource_hours - hours_spent) BETWEEN 0 AND 5");
        // if no data found, set as an empty array                                
        $imminent = $imminent ? $imminent : [];

        // retrieving the amount of tasks that are in progress, where hours spent have been logged
        $inProgressCount = get_records_sql("SELECT COUNT(*) AS in_progress FROM task WHERE project_id = $id AND is_completed = 0 AND hours_spent > 0");

        // retrieving the amount of tasks that have been completed, where they have been marked as completed
        $completedCount = get_records_sql("SELECT COUNT(*) AS completed FROM task WHERE project_id = $id AND is_completed = 1");

        // converting the previous counts into a single array
        $counts = [(int)$inProgressCount[0]["in_progress"],
            (int)$completedCount[0]["completed"],
            count($overdue)];

        // formatting the data to output
        $data = [
            "data" => $counts,
            "overdue" => $overdue,
            "imminent" => $imminent,
            "deadline" => $deadline
        ];

        echo json_encode($data);
    } else {
        echo "Invalid request";
    }
