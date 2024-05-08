<div?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=analytics");
        die();
    }
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

           <!-- jQuery (Necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!--<link rel = "stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">-->
        <link rel="stylesheet" href="analytics/analytics.css">
    </head>

    <body>
        <p class="some-text">Data Analytics</p>
        <p>User Progress</p>

        <script src = "https://www.gstatic.com/charts/loader.js"></script>
        <script>
            const isDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches
            const bgColor = isDarkMode ? "#lelele" : "#ffffff"
            const textColor = isDarkMode ? "#ffffff" : "#000000"
            

        function userPieChart(){
    
            $.ajax({
                dataType: "json",
                url: "/api/analytics/users.php",
                method: "get",
                success: function (data) {
                    if (data[0].overall == 0){
                        noTasks = 1;
                    } else {
                        noTasks = 0;
                    }
                    console.log(data);
                    console.log(data[0].overall);
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(DrawUserChart);
                    function DrawUserChart(){
                        console.log(data);
                        console.log(parseInt(data[0].overall));
                        console.log(parseInt(data[2].in_progress));
                        var project3Data = google.visualization.arrayToDataTable([
                            ['Task', 'Count'],
                            ['Completed',parseInt(data[1].completed)],
                            ['Uncompleted',parseInt(data[3].not_started)],
                            ['In Progress', parseInt(data[2].in_progress)],
                            ['No tasks set', noTasks]]
                        );
            var optionsTitle = {
                title: 'User statistics',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor},
                colors: ['#0fbf18', '#bf0f0f', '#ed9f0e', 'a3a3a3'],

            };
            var usersPieChart = new google.visualization.PieChart(document.getElementById('usersPieChart'));
            usersPieChart.draw(project3Data, optionsTitle);
        }

                },
            });
        }

        </script>
        

    <div id = "usersPieChart" class = "chart-container"></div>
    <p>Project Progress</p>

    <div id="projectAnalysis">
    <select id = "projectDropdownMenu" onchange = "displayProject(this.options[this.selectedIndex].projectID)">Choose Project:</label>
        </select>
        <h1 id="projectName"></h1>
        <div id = "projectAnalysisPieChart" class = "chart-container"></div>
    </div>


<p>Employee Performance</p>
<script>

    
    const NumToMonths ={
        1: "January",
        2: "February",
        3: "March",
        4: "April",
        5: "May",
        6: "June",
        7: "July",
        8: "August",
        9: "September",
        10: "October",
        11: "November",
        12: "December"
    }
        
    function userLineChart(){
    
    $.ajax({
        dataType: "json",
        url: "/api/analytics/users.php?taskCount=1",
        method: "get",
        success: function (data) {
            console.log("hello")
            console.log(data)
            //console.log(data[0].overall)
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(DrawEmployeeLineChart);
            function DrawEmployeeLineChart(){
                console.log("goodbye");
                console.log(data[1]);
                console.log(data[1].completed)
                //console.log(parseInt(data[0].overall));
                //console.log(parseInt(data[2].in_progress));
                var employeeData = google.visualization.arrayToDataTable([
                ['Month', 'Tasks Completed'],
                ["January",data[1].completed],
                ["February",data[2].completed],
                ["March", data[3].completed],
                ["April", data[4].completed],
                ["May",data[5].completed]]
                );
    var optionsTitle = {
        title: 'Employee statistics',
        pieHole: 0.4,
        backgroundColor: 'transparent',
        titleTextStyle: {color: textColor},
        legendTextStyle: {color: textColor}

    };
    var lineChart = new google.visualization.LineChart(document.getElementById('lineChart'));
    lineChart.draw(employeeData, optionsTitle);
}

        },
    });
}


        function drawLineChart(){
            var LineData = google.visualization.arrayToDataTable([
                ['Month', 'Tasks Completed'],
                ['January',2],
                ['February',5],
                ['March', 2],
                ['April', 1],
                ['May',1],
                ['June',1],
                ['July', 6],
                ['August', 4],
                ['September',5],
                ['October',3],
                ['November', 3],
                ['December', 3]]
                );
            var optionsTitle = {
                title: 'Employee: Clive Turner',
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor},
                legend: {position:'bottom'},
                hAxis:{ textStyle: {color: textColor}},
                vAxis:{ textStyle: {color: textColor}}

            };

            var linechart1 = new google.visualization.LineChart(document.getElementById('linechart1'));
            linechart1.draw(LineData, optionsTitle);
        }

        function fillProjectDropdown(){
            const project_dropdown = document.getElementById("projectDropdownMenu")
            while (project_dropdown.hasChildNodes()){
                project_dropdown.removeChild(project_dropdown.firstChild);
            }
            $.ajax({
                dataType: "json",
                url: "/api/analytics/projects.php",
                method: "get",
                success: function (projects) {
                    console.log(projects)
                    projects.forEach(project => {
                        console.log(project);
                        console.log(project.id);
                        console.log(project.name)
                        var item = document.createElement("option");
                        item.text = project.name;
                        item.projectID = project.id
                        project_dropdown.add(item)
                    });
                    console.log(projects[0].id)
                    console.log("above")
                    displayProject(projects[0].id);
                }
            })
        }

        function displayProject(project_id){
            console.log("displayProject()");
            const project_dropdown = document.getElementById("projectDropdownMenu")
            $.ajax({
                dataType: "json",
                url: "/api/analytics/projects.php",
                data: {project_id : project_id}, 
                method: "get",
                success: function (project) {
                    console.log(project);
                    console.log(project.name + " - Due in " + project.project_overdue.project_due_in + " days");
                    if (project.is_completed == 1){
                        document.getElementById("projectName").innerHTML = project.name + " - Completed";
                    } else if (project.project_overdue.project_due_in > 0){
                        document.getElementById("projectName").innerHTML = project.name + " - Due in " + project.project_overdue.project_due_in + " days";
                    } else if (project.project_overdue.project_due_in == 0){
                        document.getElementById("projectName").innerHTML = project.name + " - Due in Today";
                    } else {
                        document.getElementById("projectName").innerHTML = project.name + " - Overdue by " + (project.project_overdue.project_due_in*-1) + " days";
                    }
                    if (project.overall.overall == 0){
                        noTasks = 1;
                    } else {
                        noTasks = 0;
                    }
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(DrawProjectPieChart);
                    function DrawProjectPieChart(){
                        console.log(project);
                        console.log(parseInt(project.overall.overall));
                        console.log(parseInt(project.in_progress.in_progress));
                        var projectPieData = google.visualization.arrayToDataTable([
                            ['Task', 'Count'],
                            ['Completed',parseInt(project.completed.completed)],
                            ['Not started',parseInt(project.not_started.not_started)],
                            ['In Progress', parseInt(project.in_progress.in_progress)],
                            ['No tasks set', noTasks]]
                        );
            var optionsTitle = {
                title: 'Project task completion',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor},
                colors: ['#0fbf18', '#bf0f0f', '#ed9f0e', 'a3a3a3'],

            };
            var projectPieChart = new google.visualization.PieChart(document.getElementById('projectAnalysisPieChart'));
            projectPieChart.draw(projectPieData, optionsTitle);
            document.getElementById('projectAnalysisPieChart').style.display = "block";
        }

                }
            })
        }

        userPieChart();
        fillProjectDropdown();
        userLineChart();
        
        </script>
    <div id = "linechart1" class = "chart-container"></div>
    <div id = "lineChart" class = "chart-container"></div>
    </body>
</html>
