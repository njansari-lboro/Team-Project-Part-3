<?php
    include_once __DIR__ . "/../../database/users-db-helpers.php";
    include_once __DIR__ . "/../../database/projects-db-helpers.php";
    //deletes the task with the given id
    $task_id = $_POST["task_id"];
    delete_task(intval($task_id));
