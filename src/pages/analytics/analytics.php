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
        <h1>Employee Performance</h1>
        <select id = "userDropdownMenu" onchange = "userPieChart(this.options[this.selectedIndex].userID);userLineChart(this.options[this.selectedIndex].userID)">Choose Project:</label>
        </select>
        <div id = "usersPieChart" class = "chart-container"></div>
        

    
    <div id = "lineChart" class = "chart-container"></div>
    <div id = "userBarChart" class = "chart-container"></div>
    <h1 id = "projectTitle">Project Progress</h1>

    <div id="projectAnalysis">
    <select id = "projectDropdownMenu" onchange = "displayProject(this.options[this.selectedIndex].projectID);ProjectLineChart(this.options[this.selectedIndex].projectID)">Choose Project:</label>
        </select>
        <h1 id="projectName"></h1>
        <div id = "projectAnalysisPieChart" class = "chart-container"></div>
        <h1 id = "projectLineName"></h1>
        <div id = "ProjectlineChart" class = "chart-container"></div>
    </div>



        <script src = "https://www.gstatic.com/charts/loader.js"></script>
        <script>
            const isDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches
            const bgColor = isDarkMode ? "#lelele" : "#ffffff"
            const textColor = isDarkMode ? "#ffffff" : "#000000"
            console.log(user)


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
            console.log("hello")
            console.log(data)
            //console.log(data[0].overall)
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(DrawEmployeeLineChart);
            function DrawEmployeeLineChart(){
                console.log("goodbye");
                console.log(data);
                console.log(data[0]);
                console.log(data[0][0]);
                console.log(data[0][1].completed);


                console.log("below");

                console.log(data[1].completed)
                //console.log(parseInt(data[0].overall));
                //console.log(parseInt(data[2].in_progress));
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
        title: 'Employee statistics',
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
            console.log("display:");
            console.log(display);
            if (display == true){
            document.getElementById('userBarChart').style.display = "block"           
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(DrawEmployeeBarChart);
            function DrawEmployeeBarChart(){
                console.log("goodbye");
                console.log(data);
                console.log(data[0]);
                console.log(data[0][0]);
                console.log(data[0][1].completed);


                console.log("below");

                console.log(data[1].completed)
                //console.log(parseInt(data[0].overall));
                //console.log(parseInt(data[2].in_progress));
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
        title: 'Employee statistics',
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
            console.log("hello")
            console.log(data)
            //console.log(data[0].overall)
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(DrawProjectLineChart);
            function DrawProjectLineChart(){
                console.log("goodbye");
                console.log(data);
                console.log(data[0]);
                console.log(data[0][0]);
                console.log(data[0][1].completed);


                console.log("below");

                console.log(data[1].completed)
                //console.log(parseInt(data[0].overall));
                //console.log(parseInt(data[2].in_progress));
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
        title: 'Project statistics',
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
                    console.log("get all users");
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
        
if (user.role == "Employee"){
    userPieChart(user.id);
    userLineChart(user.id);
    document.getElementById("projectDropdownMenu").style.display = "none";
    document.getElementById("userDropdownMenu").style.display = "none";
    document.getElementById("projectName").style.display = "none";
    document.getElementById("projectLineName").style.display = "none";
    document.getElementById("projectTitle").style.display = "none";
}
else{
    fillProjectDropdown();
    fillUserDropdown();
    //ProjectLineChart();
}
  
        
        </script>



    </body>
</html>
