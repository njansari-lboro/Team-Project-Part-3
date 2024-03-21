<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=projects");
        die();
    }
?>

<?php
$task = $_GET["task"] ?? "dashboard";

switch ($task) {
    case "new_project":
        new_project();
        break;
    case "new_project_tasks":
        new_project_tasks();
        break;
    case "edit_task":
        edit_task();
        break;
    case "view_project_report":
        view_project_report();
        break;
    default:
        display_default();
}
?>

<?php function display_default()
{
    include_once __DIR__ . "/../database.php";

    connect_to_database();
    // retrieving projects belonging to current user from database
    $projects = get_records_sql("SELECT * FROM project WHERE owner_id = {$_SESSION["user"]->id} ORDER BY is_completed ASC");

    function get_dropdown_content($project) {
        // if a project is completed no dropdown content is generated for it
        if ($project['is_completed']) return;

        // getting all tasks belonging to a given project
        $tasks = get_records_sql("SELECT * FROM task WHERE project_id = {$project["id"]} ORDER BY is_completed ASC");
        $dropdownContent = ("
        <!-- toggle-able dropdown -->
        <div class=\"dropdown-content\" id=\"project-{$project["id"]}-content\" data-value=\"{$project["id"]}\">
            <h3>Brief</h3>
            <p>{$project["brief"]}</p>
            <div class='scroll-wrapper'>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Assigned Task</th>
                        <th>Description</th>
                        <th>Percentage of Estimated Hours Spent</th>
                        <th>Estimated Hours</th>
                    </tr>
                </thead>
                <tbody>
        ");

        // if the request returns no tasks
        if (!$tasks) {
            return $dropdownContent .= "</tbody></table><h4 style=\"text-align: center\">No Tasks Currently Assigned</h4></div><span id=\"edit-project-{$project["id"]}-tasks-link\" class=\"edit-project-tasks-link link\">Edit Tasks</span></div>";
        }

        foreach ($tasks as $task) {
            // getting the names of assignees and owners of all given tasks in the project
            $assignee = get_records_sql("SELECT full_name FROM user WHERE id = {$task["assigned_user_id"]}");
            $assignee = $assignee[0]["full_name"];
            $owner = get_records_sql("SELECT full_name FROM user WHERE id = {$task["owner_id"]}");
            $owner = $owner[0]["full_name"];
            $progress = ($task["hours_spent"] / $task["resource_hours"]) * 100;
            $is_complete = $task["is_completed"] == 0 ? "incomplete" : "complete";

            $dropdownContent .= ("
            <tr class='task-row $is_complete' data-value='{$task["id"]}'>
                <td class='assignee-cell'>$assignee</td>
                <td>{$task["name"]}</td>
                <td>{$task["description"]}</td>
                <td class=\"progress-bar\" hours-spent='{$task["hours_spent"]}'>$progress</td>
                <td class='hours'>{$task["resource_hours"]}</td>
                <td class=\"edit-button-cell\" id=\"edit-cell\"><button class=\"edit-task-button\">Edit Task</button></td>
                <td class=\"edit-button-cell\" id=\"save-cancel-cell\" style=\"display: none\">
                    <button class=\"save-task-button\">Save</button>
                    <button class=\"cancel-task-button\">Cancel</button>
                </td>
            </tr>
            ");
        }

        $dropdownContent .= ("
                </tbody>
            </table>
            </div>

            <span id=\"edit-project-{$project["id"]}-tasks-link\" class=\"edit-project-tasks-link link\">Edit Tasks</span>
            <span id=\"view-project-{$project["id"]}-report-link\" class=\"view-project-report-link link\">View Report</span>
        </div>
        ");

        return $dropdownContent;
    }
?>

    <!DOCTYPE html>

    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="projects/projects.css">
    </head>

    <body>
        <!-- heading row -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 id="projects-header">Projects</h2>
            </div>

            <!-- brings user to project creation page -->
            <button id="new-project-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 512 512">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M256 112v288M400 256H112" />
                </svg>
            </button>
        </div>

        <div>
            <hr>
        </div>

            <?php
                // if there are projects to display, display projects
                if ($projects) {
                    foreach ($projects as $project) {
                        // for completed projects, their name is displayed at the bottom of the projects page greyed-out and marked as completed
                        if ($project['is_completed']){
                            echo ("
                            <div id=\"dropdown-div\" style='color: var(--tertiary-label-color)'>
                                <span>{$project["name"]}</span>
                                <span>- Completed</span>
                            </div>
                            ");
                            echo "<div><hr></div>";
                        } else {
                            echo ("
                            <div id=\"dropdown-div\">
                                <span class=\"dropdown link\" id=\"project-{$project["id"]}-link\">{$project["name"]}</span>
                    
                                <button class=\"dropdown\" id=\"project-{$project["id"]}\">
                                    <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-chevron-down\" viewBox=\"0 0 16 16\">
                                        <path fill-rule=\"evenodd\" d=\"M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z\" />
                                    </svg>
                                </button>
                            </div>
                            ");
                            echo get_dropdown_content($project);
                            echo "<div><hr></div>";
                        }
                    }
                }
            ?>

            <script src="projects/projects.js"></script>

            <script>
                $(() => {
                    <?php foreach ($projects as $project): // links to edit existing projects/tasks as well as view report for a given project ?>
                        $("#project-<?php echo $project["id"] ?>-link").click(() => {
                            // console.log("Clicked");
                            window.location.href = `?page=projects&task=new_project&id=<?php echo $project["id"] ?>`;
                        });

                        $("#view-project-<?php echo $project["id"] ?>-report-link").click(() => {
                            // console.log("Clicked");
                            window.location.href = `?page=projects&task=view_project_report&id=<?php echo $project["id"] ?>`;
                        });

                        $("#edit-project-<?php echo $project["id"] ?>-tasks-link").click(() => {
                            // console.log("Clicked");
                            window.location.href = `?page=projects&task=new_project_tasks&id=<?php echo $project["id"] ?>`;
                        });
                    <?php endforeach ?>
                });
            </script>
        </body>
    </html>

<?php
}

function new_project()
{
    include __DIR__ . "/create-project.php";
}

function new_project_tasks()
{
    include __DIR__ . "/add-tasks.php";
}

function edit_task()
{
    include __DIR__ . "/edit-task.php";
}

function view_project_report()
{
    include __DIR__ . "/project-report.php";
}
