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
        <div id="wrapper">
            <div id="left-panel" class="expanded">
                <div class="sidebar-title" style="text-align: center">My Chats</div>

                <input type="search" placeholder="Search" class="sidebar-input">

                <div id="chats-list"></div>
            </div>

            <load-svg id="close-chat-list" class="toggle-chat-list" src="../assets/close-leading-bar-icon.svg">
                <style shadowRoot>
                    svg {
                        width: 20px;
                        padding-bottom: 1px;
                    }

                    .fill {
                        fill: var(--fill-color);
                    }
                </style>
            </load-svg>

            <load-svg id="open-chat-list" class="toggle-chat-list" src="../assets/open-leading-bar-icon.svg">
                <style shadowRoot>
                    svg {
                        width: 20px;
                        padding-bottom: 1px;
                    }

                    .fill {
                        fill: var(--fill-color);
                    }
                </style>
            </load-svg>

            <div id="right-panel">
                <div id="header"></div>

                <div id="container">
                    <div id="conversation-messages">
                        <div class="message-group-timestamp"><strong>Yesterday</strong> at 10:09</div>

                        <div class="message-user-container">
                            <picture>
                                <source class="message-user-profile-icon-dark" srcset="../img/default-user-profile-image-dark.png" media="(prefers-color-scheme: dark)">
                                <img class="message-user-profile-icon" src="../img/default-user-profile-image.png" alt="User profile image">
                            </picture>

                            <!-- <img id="profile-icon" src="$image_name" alt="User profile image"> -->

                            <div class="messages-container arrived">
                                <div class="message-user-name">John Cena</div>

                                <div class="message">
                                    <p>Welcome to computer Science Semester 2. What team are you currently in?</p>
                                </div>
                            </div>
                        </div>

                        <div class="messages-container sent">
                            <div class="message">
                                <p>I am currently in Team 12</p>
                            </div>

                            <div class="message">
                                <p>But I wanted to be in Team 02</p>
                            </div>
                        </div>

                        <div class="message-group-timestamp"><strong>Today</strong> at 09:41</div>

                        <div class="message-user-container">
                            <picture>
                                <source class="message-user-profile-icon-dark" srcset="../img/default-user-profile-image-dark.png" media="(prefers-color-scheme: dark)">
                                <img class="message-user-profile-icon" src="../img/default-user-profile-image.png" alt="User profile image">
                            </picture>

                            <!-- <img id="profile-icon" src="$image_name" alt="User profile image"> -->

                            <div class="messages-container arrived">
                                <div class="message-user-name">John Cena</div>

                                <div class="message">
                                    <p>Welcome to computer Science Semester 2. What team are you currently in?</p>
                                </div>
                            </div>
                        </div>

                        <div class="messages-container sent">
                            <div class="message">
                                <p>I am currently in Team 12</p>
                            </div>

                            <div class="message">
                                <p>But I wanted to be in Team 02</p>
                            </div>
                        </div>

                        <div class="message-user-container">
                            <picture>
                                <source class="message-user-profile-icon-dark" srcset="../img/default-user-profile-image-dark.png" media="(prefers-color-scheme: dark)">
                                <img class="message-user-profile-icon" src="../img/default-user-profile-image.png" alt="User profile image">
                            </picture>

                            <!-- <img id="profile-icon" src="$image_name" alt="User profile image"> -->

                            <div class="messages-container arrived">
                                <div class="message-user-name">John Cena</div>

                                <div class="message">
                                    <p>That's sounds amazing</p>
                                </div>

                                <div class="message">
                                    <p>I am in Team 17</p>
                                </div>
                            </div>
                        </div>

                        <div class="message-group-timestamp"><strong>Today</strong> at 13:37</div>

                        <div class="message-user-container">
                            <picture>
                                <source class="message-user-profile-icon-dark" srcset="../img/default-user-profile-image-dark.png" media="(prefers-color-scheme: dark)">
                                <img class="message-user-profile-icon" src="../img/default-user-profile-image.png" alt="User profile image">
                            </picture>

                            <!-- <img id="profile-icon" src="$image_name" alt="User profile image"> -->

                            <div class="messages-container arrived">
                                <div class="message-user-name">John Cena</div>

                                <div class="message">
                                    <p>Welcome to computer Science Semester 2. What team are you currently in?</p>
                                </div>
                            </div>
                        </div>

                        <div class="messages-container sent">
                            <div class="message">
                                <p>I am currently in Team 12</p>
                            </div>

                            <div class="message">
                                <p>But I wanted to be in Team 02</p>
                            </div>
                        </div>

                        <div class="message-user-container">
                            <picture>
                                <source class="message-user-profile-icon-dark" srcset="../img/default-user-profile-image-dark.png" media="(prefers-color-scheme: dark)">
                                <img class="message-user-profile-icon" src="../img/default-user-profile-image.png" alt="User profile image">
                            </picture>

                            <!-- <img id="profile-icon" src="$image_name" alt="User profile image"> -->

                            <div class="messages-container arrived">
                                <div class="message-user-name">John Cena</div>

                                <div class="message">
                                    <p>That's sounds amazing</p>
                                </div>

                                <div class="message">
                                    <p>I am in Team 17</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="compose-message-container">
                        <input type="text" placeholder="New Message" id="compose-message-input">

                        <button type="submit" id="compose-message-submit" disabled>
                            <load-svg class="message-icon" src="../assets/message-icon.svg">
                                <style shadowRoot>
                                    svg {
                                        width: 28px;
                                        height: 28px;
                                        margin-top: -1px;
                                        margin-right: -0.75px
                                    }

                                    .fill {
                                        fill: var(--fill-color)
                                    }
                                </style>
                            </load-svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="chat/chat.js"></script>
    </body>
</html>
