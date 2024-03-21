<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=dashboard");
        die();
    }

    include_once __DIR__ . "/../database.php";
    // retrieving projects belonging to current user from database
    connect_to_database();
    // only retrieving incomplete projects
    $projects = get_records_sql("SELECT * FROM project WHERE owner_id = {$_SESSION['user']->id} AND is_completed = false");

    // getting project names as an associative array
    $projectDetails = [];
    if ($projects) {
        foreach ($projects as $project){
            $projectDetails[] = [
                'id' => $project['id'],
                'name' => $project['name']
            ];
        }
    }
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="dashboard/manager-dashboard.css">
    </head>

    <body>
        <h2 id="today">
            <script>
                // somewhat lazily retrieving today's date
                let today = moment().format("dddd, Do MMMM");
                document.write(today);
            </script>
        </h2>
        <?php 
        // if there are active projects, display their information
        if ($projects) echo('
        <div class="container" id="all">
            <select id="project-dropdown" class="inline-large-title"></select>
            <input class="inline-large-title" type="text" id="date-picker">
            <div class="container" id="charts-tables">
                <div class="container-no-border" id="chart">
                    <canvas class="chart-canvas" id="progress-chart"></canvas>
                </div>
                <div class="container" id="tables">
                    <div class="container-inline" id="overdue">
                        <h4 class="text-center">Overdue</h4>
                        <table class="table" id="overdue-table">
                            <thead>
                                <tr class="table-heading">
                                    <th>Employee</th>
                                    <th>Task</th>
                                    <th>Hours Spent</th>
                                    <th>Hours Allocated</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="container-inline" id="imminent">
                        <h4 class="text-center">Imminent</h4>
                        <table class="table" id="imminent-table">
                            <thead>
                                <tr class="table-heading">
                                    <th>Employee</th>
                                    <th>Task</th>
                                    <th>Hours Spent</th>
                                    <th>Hours Allocated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        ');
        // if there are no currently active projects, prompt user to add a new project
        else echo("<div class='container' id='new-project-link' style='text-align: center'><h3>Add new project to view here</h3></div>");
        ?>

        <script src="dashboard/manager-dashboard.js"></script>
        <script>
            $(() => {
                // listener to bring user to adding a new project page
                $("#new-project-link").click(function() {
                    window.location.href = "?page=projects&task=new_project";
                });

                // filling the project dropdown
                let projectDropdown = $("#project-dropdown").get(0);
                let projectDetails = <?php echo json_encode($projectDetails) ?>;
                // iterating through projects retrieved, adding an option for each
                projectDetails.forEach((project) => {
                    let option = document.createElement("option");
                    option.text = project['name'];
                    option.value = project['id'];
                    projectDropdown.add(option);
                });

                // update page to display default project
                updateChartsTables();

                // on change of the date picker's value, update the project's deadline
                $("#date-picker").change(function(){
                    let date = $("#date-picker").datepicker('getDate');
                    // formatting date as SQL input
                    let formattedDate = $.datepicker.formatDate('yy-mm-dd', date);
                    $.ajax({
                        url: "dashboard/update-deadline.php",
                        type: "POST",
                        data: {
                            date: formattedDate,
                            projectId: projectDropdown.options[projectDropdown.selectedIndex].value
                        },
                        success: (response) => {
                        },
                        error: (error) => {
                            console.log("Error: ", error);
                        }
                    });
                });
                
                // on selection of new project from dropdown, update display
                $("#project-dropdown").change(updateChartsTables);
            });
        </script>
    </body>
</html>
