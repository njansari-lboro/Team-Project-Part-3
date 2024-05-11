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
                <div class="sidebar-title">
                    My Chats

                    <button id="add-chat-button">
                        <load-svg src="../assets/add-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: 20px;
                                    height: 20px;
                                }

                                .fill {
                                    fill: var(--icon-color);
                                }
                            </style>
                        </load-svg>
                    </button>
                </div>

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
                <div id="header">
                    <div id="header-chat-info">
                        <div id="header-chat-icon-container"></div>
                        <div id="header-chat-name"></div>
                    </div>

                    <div id="chat-details">
                        <button id="edit-chat-button">Edit Chat</button>

                        <div id="chat-users"></div>
                    </div>
                </div>

                <div id="container">
                    <div id="conversation-messages"></div>

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
