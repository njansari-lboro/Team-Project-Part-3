<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=projects");
        die();
    }

    //details
    include_once __DIR__ . "/../../database/users-db-helpers.php";
    include_once __DIR__ . "/../../database/projects-db-helpers.php";

    //check if the 'project id' parameter exists in the URL
    //retrieve the value of the 'id' parameter
    $id = $_GET["id"] ?? null;

    //query database for given project id
    $project = get_project($id);
    $project_id = $project->id;
    
    //get project name from query results
    $project_name = $project->name;
    $user_project = fetch_records("SELECT * FROM project_team_member WHERE project_id = ?", "i", $project_id);
    //$userIdArray = $user_projectArray->user_id;
    // var_dump($user_project);
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
        $user_names[] = get_assignee($user_id);
    }

    //var_dump($user_names);
    $user_list = [];
    foreach ($user_names as $item) {
        //check if the item is an array
        if (is_array($item)) {
            foreach ($item as $obj) {
                $user_list[] = $obj->full_name;
            }
        } else {
            $user_list[] = $item->full_name;
        }
    }

    $exist_tasks = fetch_records("SELECT * FROM task WHERE project_id = ?", "i", $project_id);
    //var_dump($exist_tasks);
    $exist_id = [];
    foreach ($exist_tasks as $exist_task) {
        $exist_id[] = $exist_task->assigned_user_id;
    }

    $exist_name = [];
    foreach ($exist_id as $eId) {
        $exist_name[] = get_assignee($eId);
    }
    //var_dump($exist_name);

    //important-- not same as db helpers
    function get_assignee(int $user_id): ?object {
        $sql = "SELECT full_name FROM user WHERE id = ?";
        return get_record($sql, "i", $user_id);
    }
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="projects/add-tasks.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

        <title>Add Tasks</title>
    </head>

    <body>
        <div class="container">
            <div class="title-with-back">
                <a class="back-icon" id="back_button">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="tutHeader" style="margin-bottom: 2rem">
                    Create New Task
                </h2>
                <div class="back-icon-spacer"></div>
            </div>
            <h1><?php echo $project_name ?></h1>
            <h2>Fill in the form to create and assign new tasks</h2>

            <div id="accordion">
                <h3>Create New Task</h3>

                <div>
                    <form id="task_form" method="post">
                        <div>
                            <label for="task_name">Task Name:</label>
                            <textarea id="task_name" name="task_name" rows="1" placeholder="Enter task name" required></textarea>
                        </div>

                        <div>
                            <label for="task_description">Task Description:</label>
                            <textarea id="task_description" name="task_description" rows="3" placeholder="Enter task description" required></textarea>
                        </div>

                        <div class="grid">
                            <div class="box">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" required>
                            </div>

                            <div class="box">
                                <label for="resource_hours">Resource Hours:</label>
                                <input type="number" id="resource_hours" name="resource_hours" placeholder="Enter resource hours" step="0.5" required>
                            </div>

                            <div class="box">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" readonly>
                            </div>

                            <div class="box">
                                <label for="assignee">Assign To:</label>
                                <select id="assignee" name="assignee" required>
                                    <?php
                                        foreach ($user_list as $option) {
                                            echo "<option value=\"$option\">$option</option>";
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="box">
                                <button id="add_button">Add Task</button>
                                <input type="hidden" id="project_id" value="<?php echo @$project_id ?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="search_sort">
            <input type="text" id="search" placeholder="Search">

            <select class="filter-dropdown" id="filter">
                <?php
                    echo "<option value=''>Filter</option>";
                    foreach ($user_list as $option) {
                        echo "<option value=\"$option\">$option</option>";
                    }
                ?>
            </select>

            <select class="filter-dropdown" id="sort">
                <option value="">Sort By</option>
                <option value="1">Task name – ascending</option>
                <option value="2">Task name – descending</option>
                <option value="3">Assignee – ascending</option>
                <option value="4">Assignee – descending</option>
                <option value="5">Date – ascending</option>
                <option value="6">Date – descending</option>
            </select>
        </div>

        <div class="container">
            <div id="task_list">
                <?php
                    foreach ($exist_tasks as $exist) {
                        $user_id = $exist->assigned_user_id;
                        $assignee_pos = get_assignee($user_id);
                        $full_name = $assignee_pos->full_name;
                        $start_date = DateTime::createFromFormat("Y-m-d", $exist->start_date)->format("d/m/Y");
                        $end_date = DateTime::createFromFormat("Y-m-d", $exist->estimated_end_date)->format("d/m/Y");

                        echo "
                        <div class='task'>
                            <h3>Task: $exist->name</h3>
                            <p id='desc'><strong>Description:</strong> $exist->description</p>
                            <p id='date'><strong>Start Date:</strong> $start_date</p>
                            <p id='hours'><strong>Resource Hours:</strong> $exist->resource_hours</p>
                            <p class='end_date'><strong>End Date:</strong> $end_date</p>
                            <h4><strong>Assignee:</strong> $full_name</h4>
                            <input type='hidden' id='task_id' value='$exist->id'/>
                            <span class='button-x'>&times;</span></button>
                        </div>
                        ";
                    }
                ?>
            </div>
        </div>

        <button type="button" id="submit_button" class="add-button">Finished</button>

        <!-- <div class="footer"> -->
        <!-- <button class="button" id="back_button">Back</button> -->
        <!-- page = aaron -->
        <!-- <button class="button" id="submit_button">Submit</button> -->
        <!-- page = projects -->
        <!-- </div> -->

        <script src="projects/add-tasks.js"></script>
    </body>
</html>
