<?php
    $server_name = "localhost";
    $username = "root";
    $password = "";
    $db_name = "team002";

    // Create connection
    $conn = new mysqli($server_name, $username, $password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: $conn->connect_error");
    }
