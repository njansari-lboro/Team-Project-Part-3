<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=dashboard");
        die();
    }

    $id = $_SESSION["user"]->id;
    include_once __DIR__ . "/../database.php";
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="dashboard/employee-dashboard.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    </head>

    <body>
        <?php
        //connects to database to get the name of user and display welcome message
            connect_to_database();
            $records = get_records_sql("SELECT * FROM user WHERE id = $id");
            foreach ($records as $record) {
                echo "<h1 id='welcome-message' class='inline-message'>Welcome {$record["first_name"]}, it is </h1>";
            }
        ?>

        <h1 id="today" class="inline-message">
            <script>
                //gets current date
                let today = moment().format("dddd, Do MMMM")
                $("#welcome-message").append(today)
            </script>
        </h1>

        <div class="general">
            <div class="tasks-preview">
                <h2 class="preview-title">Your Upcoming tasks</h2>
                <table class="table" id="tasks-table">

                    <thead>
                        <tr>
                            <th id="table-headers">Task</th>
                            <th id="table-headers">Hours Spent</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                            //gets tasks that are assigned to that user
                            $tasks = get_records_sql("SELECT * FROM task WHERE assigned_user_id = $id AND NOT is_completed ORDER BY estimated_end_date LIMIT 3");

                            foreach ($tasks as $task) {
                                //displays the tasks
                                echo "<tr><td>{$task["name"]}</td><td>{$task["hours_spent"]}</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="previews">
                <div class="project-preview">
                    <h3 class="preview-title">Your Projects</h3>

                    <table class="table" id="project-table">
                        <thead>
                            <tr>
                                <th id="table-headers">Project Name</th>
                                <th id="table-headers">Due Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                //gets tasks that are assigned to that user
                                $tasks = get_records_sql("SELECT * FROM task WHERE assigned_user_id = $id ORDER BY estimated_end_date LIMIT 3");

                                foreach ($tasks as $task) {
                                    //displays the tasks
                                    $formatted_end_date = DateTime::createFromFormat("Y-m-d", $task["estimated_end_date"])->format("d/m/Y");
                                    echo "<tr><td>{$task["name"]}</td><td>$formatted_end_date</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="forums-preview">
                    <h3 class="preview-title">Recent Forum Posts</h3>

                    <table class="table" id="forums-table">
                        <thead>
                            <tr>
                                <th id="table-headers">Post Title</th>
                                <th id="table-headers">Last updated</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                //gets forums that are recently updated
                                $forums = get_records_sql("SELECT * FROM forum ORDER BY last_updated DESC LIMIT 3");

                                foreach ($forums as $forum) {
                                    //displays the name of forum
                                    $formatted_last_updated = DateTime::createFromFormat("Y-m-d H:i:s", $forum["last_updated"])->format("d/m/Y H:i");
                                    echo "<tr onclick=\"window.location='?page=forums&task=view&id={$forum["id"]}'\" style='cursor: pointer'><td>{$forum["title"]}</td><td>$formatted_last_updated</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(() => {
                $(".tasks-preview").click(() => {
                    //takes to tasks page
                    window.location.href = "?page=tasks"
                })

                $(".project-preview").click(() => {
                    //takes to task page
                    window.location.href = "?page=tasks"
                })

                $(".forums-preview").click(() => {
                    window.location.href = "?page=forums"
                })
            })
        </script>
    </body>
</html>
