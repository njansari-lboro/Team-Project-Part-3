<?php
    include "../../database/database-connect.php";
    include "../../database/projects-db-helpers.php";

    // receiving query conditions through POST
    $projectId = $_POST["projectId"];
    $deadline = $_POST["date"];

    // updating project with $projectId, with new deadline
    update_project($projectId, deadline: $deadline);
