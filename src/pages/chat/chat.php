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
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>

        <link rel="stylesheet" href="chat/chat.css">
    </head>
                <form action="" class="sidebar-search">

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
                <div class="conversation-messages">

                    <div class="message-container">
                        <div class ="arrived-chat">
                            <p>Welcome to computer Science Semester 2. What team are you currently in?</p>
                        </div><!--end of arrived chat-->
                    </div><!--end of message-container-->

                    <div class="message-container">
                        <div class="sent-chat">
                            <p> I am currently in team 012</p>
                        </div><!--end of sent chat--> 
                    </div><!--end of message-container-->

                </div>
                <form action="" class="container-messages">
                    <input type="text" placeholder="Enter Message Here..." class="message-input">
                    <button type="submit" class="message-submit"><i class="ri-send-plane-fill"></i></button>
                </form>
            </div><!--end of container-->
        </div>
    </div> <!--end of wrapper-->


    <script>

    </script>
    </body>
</html>
