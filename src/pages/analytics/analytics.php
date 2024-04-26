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

        <link rel="stylesheet" href="analytics/analytics.css">
    </head>

    <body>
        <p class="some-text">Data Analytics</p>
        
        <script src = "https://www.gstatic.com/charts/loaders.js"></script>
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
                title: 'Project 1'

            };
            var taskpiechart = new google.visualization.PieChart(document.getElementById('taskspiechart'));
            taskpiechart.draw(project1Data, optionsTitle);
        }
        </script>
    </body>
</html>
