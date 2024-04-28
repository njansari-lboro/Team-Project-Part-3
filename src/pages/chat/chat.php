<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=chat");
        die();
    }
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="chat/chat.css">
    </head>

    <body>
        <p class="some-text">This is the chat.</p>
    </body>
</html>
