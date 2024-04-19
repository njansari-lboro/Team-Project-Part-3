<?php
    global $db_name;

    $server_name = "localhost";
    $username = "team12";
    $password = "team12!";

    // Create connection
    $conn = new mysqli($server_name, $username, $password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: $conn->connect_error");
    }
