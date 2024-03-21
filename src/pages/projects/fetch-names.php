<?php
    include __DIR__ . "/../../database/users-db-helpers.php";

    $filter = $_POST["filter"]; // search term to filter with

    // fetch users with search term in name, only employees
    $users = fetch_users(filter_text: $filter, role: "Employee");
    $names_ids = [];

    // fill with names and ids
    foreach ($users as $user) {
        $names_ids[] = [
            "label" => $user->full_name,
            "value" => $user->id
        ];
    }

    // return names and ids
    echo json_encode($names_ids);
