<!DOCTYPE html>

<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=projects");
        die();
    }

    include_once __DIR__ . "/../../database/projects-db-helpers.php";
    include_once __DIR__ . "/../../database/users-db-helpers.php";

    // retrieving project ID from the URL
    $projectId = $_GET["id"];
    // getting project details from the retrieved ID
    $projectDetails =  get_project($projectId);

    // owner of the project (currently logged in)
    $owner = get_user($projectDetails->owner_id);
    // project lead, set on creation/edit
    $lead = get_user($projectDetails->lead_id);

    // all tasks pertaining to a given project
    $tasks = fetch_tasks($projectId);
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="projects/projects.css">
        <link rel="stylesheet" href="dashboard/manager-dashboard.css">
        <link rel="stylesheet" href="projects/project-report.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    </head>

    <body>
        <div class="header">
            <button class="exit-btn">
                <load-svg src="../assets/close-icon.svg">
                    <style shadowRoot>
                        svg {
                            width: var(--title-2);
                            height: var(--title-2);
                        }

                        .fill {
                            fill: var(--secondary-label-color)
                        }
                    </style>
                </load-svg>
            </button>

            <!-- project name from URL -->
            <h2 id="project-name-report"><?php echo $projectDetails->name ?></h2>
        </div>

        <!-- rounded borders - like manager dashboard -->
        <div class="container" id="report-container">
            <div class="container" id="project-information-container">
                <div class="container-inline" id="project-information">
                    <!-- project owner, project lead, deadline and total estimated resource hours displayed here -->
                    <h3 class="text-center">Project Information</h3>
                    <table id="project-information-table">
                        <tr class="vertical-table-heading">
                            <th>Owner</th>
                            <td><?php echo $owner->full_name ?></td>
                        </tr>
                        <tr class="vertical-table-heading">
                            <th>Lead</th>
                            <td><?php echo $lead->full_name ?></td>
                        </tr>
                        <tr class="vertical-table-heading">
                            <th>Deadline</th>
                            <td><?php echo DateTime::createFromFormat("Y-m-d", $projectDetails->deadline)->format("d/m/Y") ?></td>
                        </tr>
                        <tr class="vertical-table-heading">
                            <th>Total Resource Hours</th>
                            <td><?php echo $projectDetails->resource_hours ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="container-inline" id="workload-chart">
                <canvas id="workload-bar-chart"></canvas>
            </div>

            <div class="container table" id="tasks" style="display: flex; justify-content: space-between;">
                <div class="container-inline table" id="incomplete-tasks-container" style="margin: 1%;">
                    <h3 class="text-center">Incomplete Tasks</h3>
                    <div class="scroll-wrapper">
                        <table id="incomplete-tasks">
                            <thead>
                                <tr>
                                    <th>Assignee</th>
                                    <th>Assigned Task</th>
                                    <th>Task Owner</th>
                                    <th>Start Date</th>
                                    <th>Resource Hours</th>
                                    <th>Hours Spent</th>
                                </tr>
                            </thead>

                            <?php
                                foreach ($tasks as $task) {
                                    // if a task has been completed, do not display here
                                    if ($task->is_completed) continue;

                                    // get user information pertaining to the task
                                    $assignee_user = get_user($task->assigned_user_id);
                                    $task_owner_user = get_user($task->owner_id);

                                    // getting task information to display
                                    $assignee = $assignee_user->full_name;
                                    $assigned_task = $task->name;
                                    $task_owner = $task_owner_user->full_name;
                                    $start_date = DateTime::createFromFormat("Y-m-d", $task->start_date)->format("d/m/Y");
                                    $resource_hours = $task->resource_hours;
                                    $hours_spent = $task->hours_spent;

                                    // displaying task information as a row in the table
                                    $row = "
                                    <tr>
                                        <td>$assignee</td>
                                        <td>$assigned_task</td>
                                        <td>$task_owner</td>
                                        <td>$start_date</td>
                                        <td>$resource_hours</td>
                                        <td>$hours_spent</td>
                                    </tr>
                                    ";
                                    echo $row;
                                }
                            ?>
                        </table>
                    </div>
                </div>

                <div class="container-inline table" id="complete-tasks-container" style="margin: 1%;">
                    <h3 class="text-center">Complete Tasks</h3>
                    <div class="scroll-wrapper">
                        <table id="Complete-tasks">
                            <thead>
                                <tr>
                                    <th>Assignee</th>
                                    <th>Assigned Task</th>
                                    <th>Task Owner</th>
                                    <th>Start Date</th>
                                    <th>Resource Hours</th>
                                    <th>Hours Spent</th>
                                </tr>
                            </thead>

                            <?php
                                foreach ($tasks as $task) {
                                    // if a task is not completed, do not display here
                                    if (!$task->is_completed) continue;

                                    // get user information pertaining to the task
                                    $assignee_user = get_user($task->assigned_user_id);
                                    $task_owner_user = get_user($task->owner_id);

                                    // getting task information to display
                                    $assignee = $assignee_user->full_name;
                                    $assigned_task = $task->name;
                                    $task_owner = $task_owner_user->full_name;
                                    $start_date = DateTime::createFromFormat("Y-m-d", $task->start_date)->format("d/m/Y");
                                    $resource_hours = $task->resource_hours;
                                    $hours_spent = $task->hours_spent;

                                    // displaying task information as row in table
                                    $row = "
                                    <tr>
                                        <td>$assignee</td>
                                        <td>$assigned_task</td>
                                        <td>$task_owner</td>
                                        <td>$start_date</td>
                                        <td>$resource_hours</td>
                                        <td>$hours_spent</td>
                                    </tr>
                                    ";
                                    echo $row;
                                }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
            <!-- link to mark a project as completed -->
            <span id="complete-project" class="complete-project-link link">Mark Project as Completed</span>
        </div>

        <script src="projects/project-report.js"></script>
        <script>
            $(() => {
                let tasks = <?php echo json_encode($tasks) // encoding php tasks array as javaScript array ?>;
                let userTaskCount = {}; // task count for each user assigned

                for (let i = 0; i < tasks.length; i++){
                    // id of task's assigned user
                    let assigneeId = tasks[i].assigned_user_id;
                    // if assigned user not a key in the count array
                    if (!userTaskCount[assigneeId]) {
                        // add one to the count of that key
                        userTaskCount[assigneeId] = 1;
                    }
                    else {
                        // increment count
                        userTaskCount[assigneeId]++;
                    }
                }

                // creating separate arrays for IDs and names
                let userIds = Object.keys(userTaskCount);
                let userNames = [];
                // filling the userNames array with names corresponding to IDs
                userIds.map(function(userId, response) {
                    $.ajax({
                        url: "projects/fetch-name.php",
                        method: "POST",
                        data: { id: userId },
                        success: function(data) {
                            userNames.push(data.trim());
                        }
                    });
                });

                // creating separate count array
                let taskCounts = Object.values(userTaskCount);

                // creating a bar chart to show names and task counts for each user
                let ctx = $("#workload-bar-chart").get(0).getContext("2d");
                setTimeout(() => {
                    let barChart = new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: userNames,
                            datasets: [{
                                label: "Number of Tasks",
                                data: taskCounts,
                                backgroundColor: getComputedStyle(document.body).getPropertyValue("--accent-color"),
                                borderColor: getComputedStyle(document.body).getPropertyValue("--secondary-accent-color"),
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: "y",
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        callback: value => value % 1 === 0 ? value : null
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: true,
                                    text: "Assigned Workload per Employee"
                                },
                                labels: {
                                    font: {
                                        family: "Arial, Helvetica, sans-serif"
                                    }
                                }
                            }
                        }
                    });
                }, 500);
            });
        </script>
    </body>
</html>
