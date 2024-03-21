<?php
    $server_name = "localhost";
    $username = "team12";
    $password = "team12!";
    $db_name = "make_it_all";

    // Create connection
    $conn = new mysqli($server_name, $username, $password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: $conn->connect_error");
    }
