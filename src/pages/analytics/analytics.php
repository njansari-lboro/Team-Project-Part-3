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
        <h1 class="some-text">Data Analytics</h1>
        <h4 id="label_user_project_toggle"><label for="user_project_toggle">View user or project analysis:</label></h4>
        <select id = "user_project_toggle" onchange = "show_stats(this.value)">
        <option value="User">User</option>
        <option value="Project">Project</option>
    </select>
        <h2 id="employeePerformance">Employee Performance</h2>
        <select id = "userDropdownMenu" onchange = "userPieChart(this.options[this.selectedIndex].userID);userLineChart(this.options[this.selectedIndex].userID)">Choose Project:</label>
        </select>
        <h3 id = "userTasksOverdue"></h3>
        <div id = "usersPieChart" class = "chart-container"></div>


    
    <div id = "lineChart" class = "chart-container"></div>
    <div id = "userBarChart" class = "chart-container"></div>
    <h2 id = "projectTitle">Project Progress</h2>

    <div id="projectAnalysis">
    <select id = "projectDropdownMenu" onchange = "displayProject(this.options[this.selectedIndex].projectID);ProjectLineChart(this.options[this.selectedIndex].projectID)">Choose Project:</label>
        </select>
        <h3 id="projectName"></h3>
        <h3 id = "projectTasksOverdue"></h3>
        <div id = "projectAnalysisPieChart" class = "chart-container"></div>

        <h1 id = "projectLineName"></h1>
        <div id = "ProjectlineChart" class = "chart-container"></div>
        <div id = "projectBarChart" class = "chart-container"></div>
    </div>



        <script src = "https://www.gstatic.com/charts/loader.js"></script>
        <script>
            const isDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches
            const bgColor = isDarkMode ? "#lelele" : "#ffffff"
            const textColor = isDarkMode ? "#ffffff" : "#000000"

            let team_leader = false;

        function userPieChart(user_id){
    
            $.ajax({
                dataType: "json",
                url: "/api/analytics/users.php",
                data:{user_id: user_id},
                method: "get",
                success: function (data) {
                    if (data[0].overall == 0){
                        noTasks = 1;
                    } else {
                        noTasks = 0;
                    }
                    console.log("/api/analytcis/users.php?user_id="+user_id)
                    console.log(data);
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(DrawUserChart);
                    function DrawUserChart(){
                        var project3Data = google.visualization.arrayToDataTable([
                            ['Task', 'Count'],
                            ['Completed',parseInt(data[1].completed)],
                            ['Not Started',parseInt(data[3].not_started)],
                            ['In Progress', parseInt(data[2].in_progress)],
                            ['No tasks set', noTasks]]
                        );
            var optionsTitle = {
                title: 'User task progression in the past 30 days',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor},
                colors: ['#0fbf18', '#bf0f0f', '#ed9f0e', 'a3a3a3'],

            };
            var usersPieChart = new google.visualization.PieChart(document.getElementById('usersPieChart'));
            usersPieChart.draw(project3Data, optionsTitle);
            document.getElementById('userTasksOverdue').innerHTML = "user has "+data[4].overdue+" tasks overdue";
        }

                },
            });
        }

        

    
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
        
    function userLineChart(user_id){
    
    $.ajax({
        dataType: "json",
        url: "/api/analytics/users.php",
        data:{taskCount: true,
            user_id: user_id
        },
        method: "get",
        success: function (data) {
            console.log("/api/analytics/users.php?taskCount=true&user_id="+user_id)
            console.log(data)
            //console.log(data[0].overall)
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(DrawEmployeeLineChart);
            function DrawEmployeeLineChart(){
                var employeeData = google.visualization.arrayToDataTable([
                ['Month','Tasks Completed'],
                [NumToMonths[data[5][0]],data[5][1].completed],
                [NumToMonths[data[4][0]],data[4][1].completed],
                [NumToMonths[data[3][0]],data[3][1].completed],
                [NumToMonths[data[2][0]], data[2][1].completed],
                [NumToMonths[data[1][0]], data[1][1].completed],
                [NumToMonths[data[0][0]],data[0][1].completed]]
                );
    var optionsTitle = {
        title: 'Tasks completed per month',
        backgroundColor: 'transparent',
        titleTextStyle: {color: textColor},
        legendTextStyle: {color: textColor},
        hAxis:{textStyle:{color: textColor},title:"Months",titleTextStyle : {
							
							color : textColor
						}},
        vAxis:{textStyle:{color: textColor},title: "Tasks Completed",titleTextStyle : {
							
							color : textColor
						}}

    };
    var lineChart = new google.visualization.LineChart(document.getElementById('lineChart'));
    lineChart.draw(employeeData, optionsTitle);
}
//----------bar chart ---------------------
            let display = false;
            for (let i = 0; i<6; i++){
                if (data[i][1].hours > 0){
                    display = true;
                }
            }
            if (display == true){
            document.getElementById('userBarChart').style.display = "block"           
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(DrawEmployeeBarChart);
            function DrawEmployeeBarChart(){
                var employeeData2 = google.visualization.arrayToDataTable([
                ['Month','Hours spent'],
                [NumToMonths[data[5][0]],data[5][1].hours],
                [NumToMonths[data[4][0]],data[4][1].hours],
                [NumToMonths[data[3][0]],data[3][1].hours],
                [NumToMonths[data[2][0]], data[2][1].hours],
                [NumToMonths[data[1][0]], data[1][1].hours],
                [NumToMonths[data[0][0]],data[0][1].hours]]
                );
    var optionsTitle = {
        title: 'Hours spent on tasks per month',
        backgroundColor: 'transparent',
        titleTextStyle: {color: textColor},
        legendTextStyle: {color: textColor},
        hAxis:{textStyle:{color: textColor},title:"Hours spent",titleTextStyle : {
							color : textColor
						}},
        vAxis:{textStyle:{color: textColor},title: "Month",titleTextStyle : {
							
							color : textColor
						}}

    };
    var barChart = new google.visualization.BarChart(document.getElementById('userBarChart'));
    barChart.draw(employeeData2, optionsTitle);
}
        } else{
            document.getElementById('userBarChart').style.display = "none"
        }


        },
    });
}

function ProjectLineChart(project_id){
    
    $.ajax({
        dataType: "json",
        url: "/api/analytics/projects.php",
        data:{taskCount: true, project_id:project_id},
        method: "get",
        success: function (data) {
            console.log("/api/analytics/projects.php?taskCount=true&project_id="+project_id);
            console.log(data)

            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(DrawProjectLineChart);
            function DrawProjectLineChart(){
                var employeeData = google.visualization.arrayToDataTable([
                ['Month','Tasks Completed'],
                [NumToMonths[data[5][0]],data[5][1].completed],
                [NumToMonths[data[4][0]],data[4][1].completed],
                [NumToMonths[data[3][0]],data[3][1].completed],
                [NumToMonths[data[2][0]], data[2][1].completed],
                [NumToMonths[data[1][0]], data[1][1].completed],
                [NumToMonths[data[0][0]],data[0][1].completed]]
                );
    var optionsTitle = {
        title: 'Tasks completed per month',
        backgroundColor: 'transparent',
        titleTextStyle: {color: textColor},
        legendTextStyle: {color: textColor},
        hAxis:{textStyle:{color: textColor},title:"Months",titleTextStyle : {
							
							color : textColor
						}},
        vAxis:{textStyle:{color: textColor},title: "Tasks Completed",titleTextStyle : {
							
							color : textColor
						}}

    };
    var ProjectlineChart = new google.visualization.LineChart(document.getElementById('ProjectlineChart'));
    ProjectlineChart.draw(employeeData, optionsTitle);
    //bar chart
    let display = false;
            for (let i = 0; i<6; i++){
                if (data[i][1].hours > 0){
                    display = true;
                }
            }
            if (display == true){
                console.log("display it")
            document.getElementById('projectBarChart').style.display = "block"           
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(DrawProjectBarChart);
            function DrawProjectBarChart(){
                var projectData2 = google.visualization.arrayToDataTable([
                ['Month','Hours spent'],
                [NumToMonths[data[5][0]],data[5][1].hours],
                [NumToMonths[data[4][0]],data[4][1].hours],
                [NumToMonths[data[3][0]],data[3][1].hours],
                [NumToMonths[data[2][0]], data[2][1].hours],
                [NumToMonths[data[1][0]], data[1][1].hours],
                [NumToMonths[data[0][0]],data[0][1].hours]]
                );
    var optionsTitle = {
        title: 'Hours spent to complete tasks per month',
        backgroundColor: 'transparent',
        titleTextStyle: {color: textColor},
        legendTextStyle: {color: textColor},
        hAxis:{textStyle:{color: textColor},title:"Hours spent",titleTextStyle : {
							color : textColor
						}},
        vAxis:{textStyle:{color: textColor},title: "Month",titleTextStyle : {
							
							color : textColor
						}}

    };
    var barChart = new google.visualization.BarChart(document.getElementById('projectBarChart'));
    barChart.draw(projectData2, optionsTitle);
}
        } else{
            document.getElementById('projectBarChart').style.display = "none"
        }
}

        },
    });
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
                    console.log("/api/analytics/projects.php")
                    console.log(projects)
                    projects.forEach(project => {
                        var item = document.createElement("option");
                        item.text = project.name;
                        item.projectID = project.id
                        project_dropdown.add(item)
                    });
                    displayProject(projects[0].id);
                    ProjectLineChart(projects[0].id)
                }
            })
        }

        function fillUserDropdown(){
            const user_dropdown = document.getElementById("userDropdownMenu")
            while (user_dropdown.hasChildNodes()){
                user_dropdown.removeChild(user_dropdown.firstChild);
            }
            $.ajax({
                dataType: "json",
                url: "/api/analytics/allUsers.php",
                method: "get",
                success: function (users) {
                    console.log("/api/analytics/allUsers.php");
                    console.log(users);
                    users.forEach(user => {
                        var item = document.createElement("option");
                        item.text = user.full_name;
                        item.userID = user.id
                        user_dropdown.add(item)
                    });
                    userPieChart(users[0].id);
                    userLineChart(users[0].id)
                }
            })
        }

        function displayProject(project_id){
            const project_dropdown = document.getElementById("projectDropdownMenu")
            $.ajax({
                dataType: "json",
                url: "/api/analytics/projects.php",
                data: {project_id : project_id}, 
                method: "get",
                success: function (project) {
                    console.log("/api/analytics/projects.php?project_id="+project_id);
                    console.log(project);
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
                        var projectPieData = google.visualization.arrayToDataTable([
                            ['Task', 'Count'],
                            ['Completed',parseInt(project.completed.completed)],
                            ['Not started',parseInt(project.not_started.not_started)],
                            ['In Progress', parseInt(project.in_progress.in_progress)],
                            ['No tasks set', noTasks]]
                        );
            var optionsTitle = {
                title: 'Project task completion for the last 30 days',
                pieHole: 0.4,
                backgroundColor: 'transparent',
                titleTextStyle: {color: textColor},
                legendTextStyle: {color: textColor},
                colors: ['#0fbf18', '#bf0f0f', '#ed9f0e', 'a3a3a3'],

            };
            var projectPieChart = new google.visualization.PieChart(document.getElementById('projectAnalysisPieChart'));
            projectPieChart.draw(projectPieData, optionsTitle);
            document.getElementById('projectAnalysisPieChart').style.display = "block";
            document.getElementById('projectTasksOverdue').innerHTML = "project has "+project.overdue.overdue+" tasks overdue";
        }

                }
            })
        }

        function show_stats(type){
            if (type == "User"){
                if (team_leader == false){
                    fillUserDropdown();
                    document.getElementById("userDropdownMenu").style.display = "block";
                } 

                document.getElementById("projectDropdownMenu").style.display = "none";
                document.getElementById("projectName").style.display = "none";
                document.getElementById("projectLineName").style.display = "none";
                document.getElementById("projectTitle").style.display = "none";
                document.getElementById("projectBarChart").style.display = "none";
                document.getElementById("ProjectlineChart").style.display = "none";
                document.getElementById("projectAnalysisPieChart").style.display = "none";
                document.getElementById("projectTasksOverdue").style.display = "none";
                

                document.getElementById("lineChart").style.display = "block";
                document.getElementById("usersPieChart").style.display = "block";
                document.getElementById("userBarChart").style.display = "block";
                document.getElementById("userTasksOverdue").style.display = "block";
                document.getElementById("employeePerformance").style.display = "block";
            } else{
                fillProjectDropdown();
                document.getElementById("userDropdownMenu").style.display = "none";
                document.getElementById("lineChart").style.display = "none";
                document.getElementById("usersPieChart").style.display = "none";
                document.getElementById("userTasksOverdue").style.display = "none";
                document.getElementById("employeePerformance").style.display = "none";
                document.getElementById("userBarChart").style.display = "none";

                document.getElementById("projectDropdownMenu").style.display = "block";
                document.getElementById("projectName").style.display = "block";
                document.getElementById("projectLineName").style.display = "block";
                document.getElementById("projectTitle").style.display = "block";
                document.getElementById("projectBarChart").style.display = "block";
                document.getElementById("ProjectlineChart").style.display = "block";
                document.getElementById("projectAnalysisPieChart").style.display = "block";
                document.getElementById("projectTasksOverdue").style.display = "block";
            }
        }


if (user.role == "Employee"){
$.ajax({
        dataType: "json",
        url: "/api/analytics/projects.php",
        method: "get",
        success: function (projects) {
            console.log(projects.length)
            if (projects.length != 0){
                team_leader = true;
                document.getElementById("user_project_toggle").style.display = "block";
                document.getElementById("label_user_project_toggle").style.display = "block";
            }
        }
    })
}



if (user.role == "Employee" && team_leader == false ){
    userPieChart(user.id);
    userLineChart(user.id);
    document.getElementById("projectDropdownMenu").style.display = "none";
    document.getElementById("userDropdownMenu").style.display = "none";
    document.getElementById("projectName").style.display = "none";
    document.getElementById("projectLineName").style.display = "none";
    document.getElementById("projectTitle").style.display = "none";
    document.getElementById("projectBarChart").style.display = "none";
    document.getElementById("user_project_toggle").style.display = "none";
    document.getElementById("label_user_project_toggle").style.display = "none";
}
else{
    show_stats("User");
    //ProjectLineChart();
}
  
        
        </script>



    </body>
</html>
