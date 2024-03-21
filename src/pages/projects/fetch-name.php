<?php
    include __DIR__ . "/../../database/users-db-helpers.php";

    // retrieve the full name of a given user
    $id = $_POST["id"];

    $user = get_user($id);

    echo $user->full_name;
