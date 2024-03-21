<?php
include_once "../../database/projects-db-helpers.php";

// return whether or not a project has tasks currently active
$project_id = $_POST['project_id'];
echo project_has_incomplete_tasks($project_id);
