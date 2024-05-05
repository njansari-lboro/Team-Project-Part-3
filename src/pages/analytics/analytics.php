<?php
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
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawProjectPieChart);
        
        function drawProjectPieChart(){
            var project1Data = google.visualization.arrayToDataTable([
                ['Task', 'Count'],
                ['Completed',5],
                ['Uncompleted',2],
                ['In Process', 1]]
                );
            var optionsTitle = {
                title: 'Project 1',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor} 

            };
            var taskpiechart = new google.visualization.PieChart(document.getElementById('taskspiechart'));
            taskpiechart.draw(project1Data, optionsTitle);
        }
        </script>
        <script>

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawProject2PieChart);
        function drawProject2PieChart(){
            var project2Data = google.visualization.arrayToDataTable([
                ['Task', 'Count'],
                ['Completed',2],
                ['Uncompleted',5],
                ['In Process', 2]]
                );
            var optionsTitle = {
                title: 'Project 2',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor}

            };
            var taskpiechart2 = new google.visualization.PieChart(document.getElementById('taskspiechart2'));
            taskpiechart2.draw(project2Data, optionsTitle);
        }
        </script>
        <script>
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawProject3PieChart);
        function drawProject3PieChart(){
            var project3Data = google.visualization.arrayToDataTable([
                ['Task', 'Count'],
                ['Completed',1],
                ['Uncompleted',2],
                ['In Process', 3]]
                );
            var optionsTitle = {
                title: 'Project 3',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor}

            };
            var taskpiechart3 = new google.visualization.PieChart(document.getElementById('taskspiechart3'));
            taskpiechart3.draw(project3Data, optionsTitle);
        }





        function userPieChart(){
    
            $.ajax({
                dataType: "json",
                url: "/api/analytics/users.php",
                method: "get",
                success: function (data) {
                    console.log(data)
                    console.log(data[0].overall)
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
                            ['In Process', parseInt(data[2].in_progress)]]
                        );
            var optionsTitle = {
                title: 'User statistics',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor}

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


    <select id = "projectDropdownMenu" onchange = "displayProject(this.id)">Choose Project:</label>
        </select>

    <div id = "taskspiechart" style = "display: none;" class = "chart-container"></div>
    <div id = "taskspiechart2" class = "chart-container"></div>
    <div id = "taskspiechart3" class = "chart-container"></div>
<p>Employee Performance</p>
<script>
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawLineChart);
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
                }
            })
        }

        function displayProject(selectedProjectId){
            const project_dropdown = document.getElementById("projectDropdownMenu")
            console.log(project_dropdown.options[project_dropdown.selectedIndex].projectID);
            const project_id = project_dropdown.options[project_dropdown.selectedIndex].projectID
            $.ajax({
                dataType: "json",
                url: "/api/analytics/projects.php",
                data: {project_id : project_id}, 
                method: "get",
                success: function (projects) {
                    console.log(projects)
                    projects.forEach(project => {
                        console.log(project);
                        console.log(project.id);
                        console.log(project.name)
                    });
                }
            })
        }

        userPieChart();
        fillProjectDropdown();
        
        </script>
    <div id = "linechart1" class = "chart-container"></div>
    </body>
</html>
