<?php
include __DIR__ . "/../../database/users-db-helpers.php";
include __DIR__ . "/../../database/projects-db-helpers.php";

if (isset($_POST["search"])) {
    //for selecting name for a project
    $input = $_POST["search"];

    $content = "";

    $records = fetch_users($input);
    //print_r ($records);

    foreach ($records as $user) {
        if ($user->role === "Employee") {
            //checks the users displayed are employees
            $content .= "<li onclick=selectInput(this)>$user->full_name</li>";
        }
    }

    echo "<ul>$content</ul>";
} else if (isset($_POST["search_leader"])) {
    //for displaying names for a leader
    $input = $_POST["search_leader"];

    $content = "";

    $records = fetch_users($input);
    //print_r ($records);
    foreach ($records as $user) {
        //displays names
        $content .= "<li onclick=selectInputLeader(this)>$user->full_name</li>";
    }

    echo "<ul>$content</ul>";
} else if (isset($_POST["project_name"])) {
    //gets the name from a project
    $projectName = $_POST["project_name"];
    $projectDetails = get_project($projectName);

    echo $projectDetails;
}
