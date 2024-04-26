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

        
        <!--<link rel = "stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">-->
        <link rel="stylesheet" href="analytics/analytics.css">
    </head>

    <body>
        <p class="some-text">Data Analytics</p>
        <p>Project Progresses</p>
        
        <script src = "https://www.gstatic.com/charts/loader.js"></script>
        <script>
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
                titleTextStyle: {color: '#ffffff'},
                legendTextStyle: {color: '#ffffff'} 

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
                titleTextStyle: {color: '#ffffff'},
                legendTextStyle: {color: '#ffffff'}

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
                ['Uncompleted',3],
                ['In Process', 3]]
                );
            var optionsTitle = {
                title: 'Project 3',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: '#ffffff'},
                legendTextStyle: {color: '#ffffff'}

            };
            var taskpiechart3 = new google.visualization.PieChart(document.getElementById('taskspiechart3'));
            taskpiechart3.draw(project3Data, optionsTitle);
        }
        </script>
    <div id = "taskspiechart" class = "chart-container"></div>
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
                titleTextStyle: {color: '#ffffff'},
                legendTextStyle: {color: '#ffffff'},
                legend: {position:'bottom'}

            };
            var linechart1 = new google.visualization.LineChart(document.getElementById('linechart1'));
            linechart1.draw(LineData, optionsTitle);
        }
        </script>
    <div id = "linechart1" class = "chart-container"></div>
    </body>
</html>
