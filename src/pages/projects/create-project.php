<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=projects");
        die();
    }
?>

<?php
include_once __DIR__ . "/../../database/users-db-helpers.php";
include_once __DIR__ . "/../../database/projects-db-helpers.php";

$currentUser = json_encode($_SESSION["user"]->id);

// echo "console.log(".$currentUser.");";
//echo $currentUser;
if (isset($_GET["id"])) {
    //Checks if clicked on an already existing project or createing new project
    //If clicked on an already existing project assigns the project id to variable $projectId
    $projectId = $_GET["id"];

    //gets the project from the database based on project id and stored in variable $projectDetails
    $projectDetails = get_project($projectId);

    //gets the name of the user from database from the lead_id of a project
    $lead = get_user($projectDetails->lead_id);
}
?>

<!DOCTYPE html>

<html>

<head>
    <link rel="stylesheet" href="projects/create-project.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>

<body>
    <!-- <form method="post" id="details" action="?page=projects&task=new_project_tasks"> -->
    <form method="post" id="details">
        <div id="outer-div">
            <!-- outer-div is div containing everything -->
            <div id="top-row">
                <!-- Top row is for title and the button that closes the create project screen -->
                <h2 id="closebtn">&times;</h2>
                <h2 id="title">Create Project</h2>
            </div>

            <div id="project-details">
                <!-- div that contains all the details for the project -->
                <div id="name-leader-brief-deadline">
                    <!-- div that contains the details for the project being the name, leader, brief, deadline and resource hours -->
                    
                    <!-- Entering a name for the project -->
                    <h2 id="headers">Title</h2>
                    <input type="text" id="pname" name="pname" placeholder="Enter a project name" required />

                    <br>

                    <!-- Entering a name for the leader of the project -->
                    <h2 id="headers">Leader</h2>
                    <!-- <input type="text" id="pleader" name="pleader" placeholder="Enter a project leader" onkeyup = searchNamesLeader() autocomplete = "off" required /> -->
                    <input type="text" id="pleader" name="pleader" placeholder="Enter a project leader" autocomplete="off" required />
                    
                    <div class="leader-names"></div>
                    <!-- the div leader-names gets the names from the database and displays them from the div -->

                    <br>

                    <!-- Entering a brief for the project -->
                    <h2 id="headers">Brief</h2>
                    <textarea id="pbrief" name="pbrief" rows="4" cols="50" placeholder="Enter project brief" required></textarea>

                    <br>

                    <!-- Entering a deadline for the project -->
                    <h2 id="headers">Deadline</h2>
                    <input type="text" id="project-deadline" placeholder="Enter Project Deadline">

                    <!-- Entering the total resource hours needed for a project -->
                    <h2 id="headers">Resource Hours</h2>
                    <input type="text" id="resource-hours" placeholder="Enter Resource Hours">

                    <br><br>

                    <!-- Button that when pressed calls function submitDetails to submit the details and move to next page or display error message -->
                    <button type="button" id="submitBtn" onclick=submitDetails()>Submit</button>
                </div>

                <div id="members">
                    <!-- the div that contains the team members where you can enter the team members for a project -->
                    <h1 id="headers">Team</h1>

                    <div id="members-box">
                        
                        <!-- Input box to enter the name of employees to add to a project -->
                        <input type="text" id="members-entry" placeholder="Enter a member" autocomplete="off">

                        <div class="results-box">
                            <!-- the div that displays the names from the database, names appeared is dependent on what is typed into input box above  -->
                        </div>

                        <br><br>

                        <!-- Button that when pressed calls function buttonfunc -->
                        <button type="button" id="add-member" onclick="buttonfunc()">Add Member</button>

                        <ul id="team-members">
                            <!-- After pressing the button and buttonfunc is called, the name of team member added to project is displayed here in form of list -->
                        </ul>
                    </div>

                    <br><br>


                </div>
            </div>
        </div>
    </form>

   
    <script src="projects/create-project.js"></script>

    <script>
        //checks if working with new or already existing project
        const urlParams = new URLSearchParams(window.location.search)
        console.log(urlParams.has("id"))
        if (urlParams.has("id") === true) {
            //if clicked on already existing project on page before, gets information from database about the project and fills in all the details
            const projectID = urlParams.get("id")
            
            //console.log("ajax called");
            
            //the projectDetails from line 22 is stored in each respective variable
            deadlinename = "<?php if ($projectDetails) echo DateTime::createFromFormat("Y-m-d", $projectDetails->deadline)->format("d/m/Y") ?>"
            brief = "<?php echo $projectDetails->brief ?>"
            resource_hours = "<?php echo $projectDetails->resource_hours ?>"
            projectName = "<?php echo $projectDetails->name ?>"

            leaderName = "<?php echo $lead->full_name; ?>"
            //console.log(deadlinename);

            //fills in the information in their respective input box
            $("#project-deadline").val(deadlinename)
            $("#pbrief").val(brief)
            $("#resource-hours").val(resource_hours)
            $("#pleader").val(leaderName)
            $("#pname").val(projectName)

            $.ajax({
                //ajax call for getting the team members of an already existing project
                type: "POST",
                url: "projects/create-project-sql-2.php",
                data: {
                    team_members: projectID
                },
                success: function(response) {
                    //Displays the team members in the list on line 112
                    const listNames = document.getElementById("team-members")
                    listNames.innerHTML = response;
                }
            });
        }
    </script>

    <script>

        const inputBox = document.getElementById("members-entry")
        const resultsBox = document.querySelector(".results-box")

        function selectInput(x) {
            //selectInput is called by clicking on a name displayed in the div results-box
            //the name is then displayed in the input box with id 'members-entry'
            inputBox.value = x.innerHTML
            resultsBox.innerHTML = ""
        }

        function selectInputLeader(x) {
            //selectInputLeader is called by clicking on a name displayed in the div results-box
            //the name is then displayed in the input box with id 'members-leader-names'
            const inputBoxLeader = document.getElementById("pleader")
            const resultsBoxLeader = document.querySelector(".leader-names")
            inputBoxLeader.value = x.innerHTML
            resultsBoxLeader.innerHTML = ""
        }

        function buttonfunc() {
            //buttonfunc is a function that adds the name entered in input box 'members-entry' and displays it in 'team-members- as a list
            const addname = document.getElementById("members-entry").value
            const listnames = document.getElementById("team-members")
            const listItems = listnames.getElementsByTagName("li")

            // Checks for name entered is already in the team
            const membersList = []
            let duplicate = false

            for (let i = 0; i < listItems.length; i++) {
                //for loop goes through the current team members

                //Line below ensures that the string only contains the name
                const thename = listItems[i].textContent.replace("remove", "")

                if (thename === addname) {
                    //if name entered is already a team member then duplicate is set to true
                    duplicate = true
                } else {
                    //else adds name to list of team members
                    membersList.push(listItems[i].textContent.replace("remove", ""))
                }
            }

            $.ajax({
                //ajax is called to add name to team members of a project or displays error message if cannot add
                type: "POST",
                url: "projects/create-project-sql-2.php",
                data: {
                    name_check: addname
                },
                success: function(response) {
                    console.log(response)
                    if (response === "success") {
                        //console.log(listnames);
                        if (duplicate === true) {
                            //displays error message as name is already a team member
                            showDialog("Unable to add user", `The member "${addname}" has already been added.`)
                        } else {
                            //adds name to 'team-members' as a list with button to be able to remove them from list
                            listnames.innerHTML += `<li class="new">${addname}<button type="button" onclick="removeItem(this)">remove</button></li>`
                        }
                    } else {
                        //displays error message as name entered is not an employee or not in database
                        showDialog("Unable to add user", `The user "${addname}" is invalid.`)
                    }
                }
            })

        }

        function removeName(x) {
            $(x).remove()
        }

        function removeItem(element) {
            //removeItem is called when the 'remove' button is pressed to remove a name from a team
            const urlParams = new URLSearchParams(window.location.search)

            
            let listItem = element.parentNode
            console.log(listItem);

            if (urlParams.has("id")) {
                //if working with an already existing project
                const projId = urlParams.get("id")
                
                //variable text is the whole information between <li> tags
                const text = listItem.textContent.trim()

                //gets class name of <li>
                var buttonName = listItem.className;
                if (buttonName == "new") {
                    //if name has just been added
                    const listItem = element.parentNode
                    const list = listItem.parentNode

                    //removes them from project
                    list.removeChild(listItem)
                    console.log(buttonName);
                } else {
                    //else name has not just been added and been part of team project

                    name = text.replace("remove", "") //removes 'remove' from the text
                    console.log(text) // Output the text content
                    console.log(name); 
                    console.log(projId);

                    $.ajax({
                        //ajax is called to see if can remove name from a project
                        type: "POST",
                        url: "projects/create-project-sql-2.php",
                        data: {
                            name_remove: name,
                            projectId: projId
                        },
                        success: function(response) {
                            console.log(response);
                            if (response == "success") {
                                // if response == "success" means that the user has no assigned tasks and can be removed
                                const listItem = element.parentNode

                                
                                const list = listItem.parentNode

                                // Removes the <li> element from the <ul> element 'team-members'
                                list.removeChild(listItem)
                            } else {
                                //Displays error message as name has assigned tasks in the project
                                showDialog("Unable to remove user", `The user "${name}" has assigned task(s).`)
                            }
                        }
                    })
                }
            } else {
                //else name has just been added and can be removed
                listItem = element.parentNode

                // Get the parent <ul> element
                const list = listItem.parentNode

                // Remove the <li> element from the <ul> element
                list.removeChild(listItem)
            }

        }

        function submitDetails() {
            //function that submits the project details and adds to database and moves to next page

            //gets all the details of the project entered
            const projectTitle = document.getElementById("pname").value
            const projectLeader = document.getElementById("pleader").value
            const projectBrief = document.getElementById("pbrief").value
            console.log(projectBrief)
            const projectDeadline = document.getElementById("project-deadline").value
            const resourceHours = document.getElementById("resource-hours").value
            //console.log(typeof projectDeadline);
            let ownerID = "<?php echo $projectDetails?->owner_id ?? $_SESSION["user"]->id ?>"
            // alert("submit works");

            //gets the team members of the project from the names entered
            const namesList = document.getElementById("team-members")

            const listItems = namesList.getElementsByTagName("li")

            const membersList = []

            for (let i = 0; i < listItems.length; i++) {
                membersList.push(listItems[i].textContent.replace("remove", ""))
            }
            //console.log(membersList);


            if (membersList.length === 0) {
                //If no team members in a project, displays error message
                showDialog("You need to add team members")
            } else {
                const urlParams = new URLSearchParams(window.location.search)
                console.log(urlParams.has("id"))

                if (urlParams.has("id")) {
                    //If working with already existing project
                    console.log("Update project!")
                    const projId = urlParams.get("id")

                    $.ajax({
                        //ajax is called to update the information for a project
                        type: "POST",
                        url: "projects/create-project-sql-2.php",
                        data: {
                            id: projId,
                            title_update: projectTitle,
                            leader: projectLeader,
                            ownerid: ownerID,
                            brief: projectBrief,
                            deadline: projectDeadline,
                            resource_hours: resourceHours
                        },
                        success: function(reply) {
                            if (reply.trim() === "success") {
                                //If project information has been updated

                                //gets team members
                                const namesList = document.getElementById("team-members")
                                const listItems = namesList.getElementsByTagName("li")


                                for (let i = 0; i < listItems.length; i++) {
                                    //for loop goes through the team members in list
                                    const name = listItems[i].textContent.replace("remove", "")
                                    console.log(typeof(id))
                                    console.log(typeof(id))

                                    $.ajax({
                                        //ajax is called to add name with project id to project_team_member table
                                        type: "POST",
                                        url: "projects/create-project-sql-2.php",
                                        data: {
                                            new_id: projId,
                                            name_add: name
                                        },
                                        success: function(response) {
                                            'member saved';
                                        }
                                    })
                                    
                                }


                                //console.log(membersNames);
                                

                                const urlParams = new URLSearchParams(window.location.search)
                                const projectID = urlParams.get("id")

                                //Goes to next page to assign tasks to members
                                const test = `?page=projects&task=new_project_tasks&id=${projectID}`
                                console.log(test)
                                window.location.href = test
                                
                            } else {
                                //Displays error message
                                showDialog("There was an error whilst updating the project")
                            }
                        }
                    })
                } else {
                    const idplease = 0

                    console.log("after ajax:")
                    console.log(idplease)

                    $.ajax({
                        //ajax is called to add a project to the project table with the information
                        type: "POST",
                        url: "projects/create-project-sql-2.php",
                        data: {
                            title: projectTitle,
                            leader: projectLeader,
                            ownerid: ownerID,
                            brief: projectBrief,
                            deadline: projectDeadline,
                            resource_hours: resourceHours
                        },
                        success: function(reply) {
                            console.log(reply)
                            if (reply.trim() === "success") {
                                //If project has been added to project table
                                console.log('ggg');

                                //gets team members
                                const namesList = document.getElementById("team-members")
                                const listItems = namesList.getElementsByTagName("li")

                                const membersList = []

                                // for (let i = 0; i < listItems.length; i++) {
                                //     membersList.push(listItems[i].textContent.replace("remove", ""))
                                // }

                                //console.log(membersList);
                                // sessionStorage.setItem("membersNames", JSON.stringify(membersList))

                                console.log("so close")
                                //console.log(idplease);

                                $.ajax({
                                    //ajax is called to get the project id of the project that has just been added
                                    type: "POST",
                                    url: "projects/create-project-sql-2.php",
                                    data: {
                                        title_for_id: idplease
                                    },
                                    success: function(response) {
                                        console.log("during ajax")
                                        console.log(response)
                                        const id = response

                                        for (let i = 0; i < listItems.length; i++) {
                                            const name = listItems[i].textContent.replace("remove", "")
                                            console.log(typeof(id))
                                            console.log(typeof(id))

                                            $.ajax({
                                                //ajax is called add team member with project id to project_team_member
                                                type: "POST",
                                                url: "projects/create-project-sql-2.php",
                                                data: {
                                                    new_id: id,
                                                    name_add: name
                                                },
                                                success: function(response) {
                                                    console.log('deleted');
                                                }
                                            })
                                            //membersList.push(listItems[i].textContent.replace("remove",""));
                                        }

                                        //Taken to next page with project id in url
                                        window.location.href = `?page=projects&task=new_project_tasks&id=${id}`
                                    }
                                })

                                //window.location.href = "?page=projects&task=new_project_tasks&id="+id;
                                //window.location.href = "?page=projects&task=new_project_tasks";
                            } else {
                                //Displays error message
                                showDialog("There was an error whilst adding the project")
                            }
                        }
                    })
                }
            }
        }
    </script>
</body>

</html>
