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
         <!--beginning-->
    <div class="chat-section">
        <div class="container">
            <!--start:Content-->
            <div class="content">
                <!--start of content side-->
                <div class="sidebar">
                    <div class="sidebar-title" style="text-align:center;"> My Messages  <i class="ri-message-2-line"></i> </div>
                    <form action="" class="sidebar-search">
                        <input type="search" class="sidebar-input"><!--making the type earch means an x will show up to delete what you were typing-->
                        <button type="submit" class="sidebar-submit"><i class="ri-search-line"></i></button>
                    </form>
                    <div class="messages">
                        <ul class="messages-list">
                            <li class="message-title" style="font-size:18px;">Recent</li><br>
                            <li>
                                <a href="#">
                            <span class="message-info">
                                <span class="message-name">Angela</span>
                                <span class="message-text">Thank you, I recieved your email!</span>
                            </span>
                            <span>
                                <span class="message-unread">1</span>
                                <span class="message-time">11:40</span>
                            </span>
                            </a>
                        </li>
                        </ul>
                    </div>
                </div>
                <!--end of content side-->
            </div>
            <!--end:Content-->
        </div>
    </div>
    <!--end of beginning-->
    </body>
</html>
