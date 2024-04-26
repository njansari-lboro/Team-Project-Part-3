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
        <p class="some-text">This is the analytics.</p>
        <p class="some-text">This is the analytics.</p>
        <script src = "https://www.gstatic.com/charts/loaders.js"></script>
        <script>
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallBack(drawProjectPieChart);
        function drawProjectPieChart(){
            var project1Data = google.visualisation.arrayToDataTable([
                ['Completed',5],
                ['Uncompleted',2],
                ['In Process', 1]]
                );
            var optionstitle = {
                title: Project 1

            };
            var taskpiechart = new google.visualisation.PieChart(document.getElementById('taskspiechart'));
            taskpiechart.draw(project1Data, optionstitle);
        }
        </script>
    </body>
</html>
