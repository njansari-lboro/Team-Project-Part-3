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
        <div id="wrapper"><!--start of wrapper-->
        <div id="left_pannel">
            <div class="sidebar-title" style="text-align:center;"> My Messages  <i class="ri-message-2-line"></i> </div>
                <form action="" class="sidebar-search">
                    <input type="search" placeholder="search" class="sidebar-input"><!--making the type earch means an x will show up to delete what you were typing-->
                    <button type="submit" class="sidebar-submit"><i class="ri-search-line"></i></button>
                </form>
        </div>
        <div id="right_pannel">
            <div id="header"></div>
            <div id="container">
            </div><!--end of container-->
        </div>
    </div> <!--end of wrapper-->
    </body>
</html>
