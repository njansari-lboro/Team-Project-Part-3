<?php
include __DIR__ . "/../../database/projects-db-helpers.php";
include __DIR__ . "/../../database/users-db-helpers.php";
include __DIR__ . "/../database.php";

if (isset($_POST["title"])) {
    //adds a project to the project table

    $title = $_POST["title"];
    $leader = $_POST["leader"];
    $deadline = $_POST["deadline"];
    $brief = $_POST["brief"];
    $resource_hours = $_POST["resource_hours"];

    $ownerID = $_POST["ownerid"];

    //fetches all users
    $records = fetch_users();
    //print_r ($records);
    $found = false;
    $leaderID = 0;

    foreach ($records as $user) {
        if (strtolower($leader) == strtolower($user->full_name)) {
            //gets the leader id 
            $found = true;
            $leaderID = $user->id;
            break;
        }
    }

    if ($found) {
        //adds project to project table
        $response = "success";
        $formatted_deadline = DateTime::createFromFormat("d/m/Y", $deadline)->format("Y-m-d");
        add_project($title, $ownerID, $leaderID, $brief, $formatted_deadline, $resource_hours);
    } else {
        $response = "error";
    }

    echo $response;
} else if (isset($_POST["title_update"])) {
    //update the information for a project
    $projectId = $_POST["id"];
    $title = $_POST["title_update"];
    $leader = $_POST["leader"];
    $deadline = $_POST["deadline"];
    $brief = $_POST["brief"];
    $resource_hours = $_POST["resource_hours"];

    $ownerID = $_POST["ownerid"];

    $records = fetch_users();
    //print_r ($records);
    $found = false;
    $leaderID = 0;

    foreach ($records as $user) {
        if (strtolower($leader) == strtolower($user->full_name)) {
            //gets id of leader entered
            $found = true;
            $leaderID = $user->id;
            break;
        }
    }

    if ($found) {
        //updates the project in the database
        $response = "success";
        $formatted_deadline = DateTime::createFromFormat("d/m/Y", $deadline)->format("Y-m-d");
        update_project($projectId, $title, $leaderID, $brief, $formatted_deadline, $resource_hours);
    } else {
        $response = "error";
    }

    echo $response;
} else if (isset($_POST["title_for_id"])) {
    //gets the id of the project just added
    connect_to_database();

    $id = get_records_sql("SELECT id FROM project ORDER BY id DESC LIMIT 1");
    foreach ($id as $i) {
        echo $i["id"];
    }
} else if (isset($_POST["team_members"])) {
    //displays the team members part of a project in form of a list
    $projectId = $_POST["team_members"];

    $members = [];

    $all_users = fetch_users();

    foreach ($all_users as $user) {
        if (is_user_project_team_member($user->id, $projectId)) {
            //checks if user is part of a project
            $members[] = $user;
        }
    }

    $content = "";

    foreach ($members as $member) {
        //displays the team members part of a project in form of a list
        $content .= "<li>$member->full_name<button type='button' onclick='removeItem(this)'>remove</button></li>";
        //data-value=$user->id
    }

    echo $content;
} else if (isset($_POST["name_remove"])) {
    //removes the name from the database
    if (!connect_to_database()) {
        die("Error: Unable to connect to the database.");
    }
    global $mysqli;
    $name = $_POST["name_remove"];
    $projId = $_POST["projectId"];
    $projId = (int)$projId;
    $memberID = 0;

    $records = fetch_users();
    //print_r ($records);
    $found = false;

    foreach ($records as $user) {
        if (strtolower($name) == strtolower($user->full_name)) {
            //gets user id
            $found = true;
            $memberID = $user->id;
            break;
        }
    }

    $found_in_project_user = false;

    if (is_user_project_team_member($memberID, $projId)) {
        //if part of a project, checks if task has been assigned to them
        $sql = ("select count(*) as total from task where project_id = ' . $projId . ' and assigned_user_id =" . $memberID);
        $tasks = get_record_sql($sql);
        if ($tasks['total'] < 1) {
            //if task has been assigned to user delete project from project_team_member table
            delete_project_team_member($memberID, $projId);
            $found_in_project_user = true;
        }
    }

    echo $found_in_project_user ? "success" : "error";
} else if (isset($_POST["name_check"])) {
    //checks if name is in database
    $name = $_POST["name_check"];

    $records = fetch_users();
    $memberID = 0;
    $found = false;

    foreach ($records as $user) {
        if (strtolower($name) == strtolower($user->full_name)) {
            $found = true;
            $memberID = $user->id;
            break;
        }
    }

    echo $found ? "success" : "error";
}

if (isset($_POST["name_add"])) {
    //adds name to to project_team_member table
    $name = $_POST["name_add"];
    $id = $_POST["new_id"];
    $id = (int)$id;
    $memberID = 0;

    $records = fetch_users();
    //print_r ($records);
    $found = false;

    foreach ($records as $user) {
        if (strtolower($name) == strtolower($user->full_name)) {
            $found = true;
            $memberID = $user->id;
            break;
        }
    }
    if (!is_user_project_team_member($memberID, $id)) {
        ~add_project_team_member($memberID, $id);
    }
}
