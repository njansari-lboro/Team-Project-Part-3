<?php
include_once "../../database/projects-db-helpers.php";

// retrieving project ID to update
$project_id = $_POST['project_id'];

// setting project with $project_id to be complete
update_project($project_id, is_completed: true);