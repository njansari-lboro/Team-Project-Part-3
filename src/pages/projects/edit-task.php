<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=projects");
        die();
    }

    include_once __DIR__ . "/../../database/users-db-helpers.php";
    include_once __DIR__ . "/../../database/projects-db-helpers.php";

    //check if the 'id' parameter exists in the URL
    if (isset($_GET["id"])) {
        //retrieve the value of the 'id' parameter
        $project_id = $_GET["id"];
    } else {
        // 'id' parameter is not present in the URL
        echo "id parameter is missing";
    }

    //check if the 'id' parameter exists in the URL
    if (isset($_GET["task_id"])) {
        //retrieve the value of the 'id' parameter
        $task_id = $_GET["task_id"];
    } else {
        // 'id' parameter is not present in the URL
        echo "name parameter is missing";
    }

    /*
        //retrieve form data
        $newTaskName = $_POST['taskName'];
        $newTaskDescription = $_POST['taskDescription'];
        $newStartDate = $_POST['startDate'];
        $newResourceHours = filter_input(INPUT_POST,'resourceHours',FILTER_VALIDATE_FLOAT);
        $endDate = $_POST['endDate'];
        $newAssignee = $_POST['assignee'];
        */

    //get all task information for given id
    $task = get_task($task_id);
    $task_name = $task->name;
    $task_description = $task->description;
    $task_date = $task->start_date;
    $task_hours = $task->resource_hours;
    $task_user = $task->assigned_user_id;
    $task_end = $task->estimated_end_date;
 
    $user = get_assignee($task_user);
    $user_name = $user->full_name;
 
    //query database for given project id
    $project = get_project($project_id);
    $project_id = $project->id;
    //echo($project_id);
    //get project name from query results
    $project_name = $project->name;
    $user_project = fetch_records("SELECT * FROM project_team_member WHERE project_id = ?", "i", $project_id);
    //$userIdArray = $user_projectArray->user_id;
    //var_dump($user_project);
    $user_ids = []; //empty array to store user IDs

    //loop through the array and extract user IDs
    foreach ($user_project as $obj) {
        $user_ids[] = $obj->user_id;
    }
    //var_dump($user_ids);
    $user_names = [];
    foreach ($user_ids as $user_id) {
        //$user = get_user($user_id);
        //$user_names[] = $user->full_name;
        $user_names[] = get_assignee_name($user_id);
    }
    //var_dump($user_names);
    $user_list = [];
    foreach ($user_names as $item) {
        // Check if the item is an array
        if (is_array($item)) {
            foreach ($item as $obj) {
                $user_list[] = $obj->full_name;
            }
        } else {
            $user_list[] = $item->full_name;
        }
    }

    //important-- not same as db helpers
    function get_assignee_name(int $user_id): ?object {
        $sql = "SELECT full_name FROM user WHERE id = ?";
        return get_record($sql, "i", $user_id);
    }
    function get_assignee(int $task_user): ?object {
        return get_user($task_user);
    }
    //predicts the end dte based off of start date and resource hours
    function predict_end_date(string $task_date, int $task_hours): string {
        //convert start date to DateTime object
        $start_date_obj = new DateTime($task_date);
        $start_time = $start_date_obj->format("H");
        $work_hours_remaining = 8 - $start_time; // assuming 8 hours per workday

        if ($task_hours <= $work_hours_remaining) {
            // if resource hours are less than or equal to work hours remaining, end date is on the same day
            $end_date_obj = clone $start_date_obj;
            $end_date_obj->modify("+$task_hours hours");
        } else {
            $task_hours -= $work_hours_remaining;
            $end_date_obj = clone $start_date_obj;
            while ($task_hours >= 8) {
                $end_date_obj->modify("+1 day"); // move to the next day
                $task_hours -= 8; // subtract 8 hours for each work day
            }
            // if there are remaining hours, add them to the end date
            if ($task_hours > 0) {
                $end_date_obj->modify("+$task_hours hours");
            }
        }
        // format end date
        return $end_date_obj->format("Y-m-d");
    }
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="projects/add-tasks.css">

        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

        <title>Edit Task</title>
    </head>

    <body>
        <div class="container">
            <h1>Edit Task</h1>
            <p>Fill in the form to edit the task</p>

            <div>
                <form method="post" id="up_form">
                    <div>
                        <label for="task_name">Task Name:</label>
                        <textarea id="task_name" name="task_name" rows="1" placeholder="<?php echo @$task_name ?>"><?php echo @$task_name ?></textarea>
                    </div>

                    <div>
                        <label for="task_description">Task Description:</label>
                        <textarea id="task_description" name="task_description" rows="3" placeholder="<?php echo @$task_description ?>"><?php echo @$task_description ?></textarea>
                    </div>

                    <div class="grid">
                        <div class="box">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo @$task_date ?>">
                        </div>

                        <div class="box">
                            <label for="resource_hours">Resource Hours:</label>
                            <input type="number" id="resource_hours" name="resource_hours" placeholder="Enter resource hours" step="0.5" value="<?php echo @$task_hours ?>">
                        </div>

                        <div class="box">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" value="<?php echo @$task_end ?>" readonly>
                        </div>
                        <div class="box">
                            <label for="assignee">Assign To:</label>
                            <select id="assignee" name="assignee">
                                <?php
                                    foreach ($user_list as $option) {
                                        //echo "<option value=\"$option\">$option</option>";
                                        $selected = ($user_name == $option) ? 'selected' : ''; // check if the option should be selected
                                        echo "<option value=\"$option\" $selected>$option</option>"; // output the option with selected attribute if necessary
                                    }
                                ?>
                            </select>

                            <input type="hidden" id="task_id" value="<?php echo @$task_id ?>">
                            <input type="hidden" id="project_id" value="<?php echo @$project_id ?>">
                        </div>
                    </div>

                    <div class="footer">
                        <button id="cancel">Cancel</button>
                        <!-- page = me -->
                        <button id="update">Update Task</button>
                        <!-- page = projects -->
                    </div>
                </form>
            </div>
        </div>

        <script src="projects/edit-task.js"></script>
    </body>
</html>
