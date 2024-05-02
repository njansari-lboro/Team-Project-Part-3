<?php
    // Handle uploading a user's new profile image

    $profile_image = $_FILES["upload_profile_image"] ?? null;

    if ($profile_image) {
        upload_profile_image($profile_image);
        exit();
    }

    session_start();

    if (empty($_SESSION["user"])) {
        header("Location: ../helpers/logout.php");
        die();
    }

    include_once("../database/users-db-helpers.php");
    include_once("../database/notifications-db-helpers.php");

    // Handle AJAX calls for performing database tasks

    $action = $_GET["action"] ?? null;

    if ($action) {
        include_once(__DIR__ . "/../database/users-db-helpers.php");
        include_once(__DIR__ . "/../database/invites-db-helpers.php");

        switch ($action) {
        case "validate_password":
            validate_password();
            break;
        case "save_user":
            save_updated_user();
            break;
        case "update_user_notifications":
            update_user_notifications();
            break;
        case "update_notification_preferences":
            update_notification_preferences();
            break;
        case "invite_user":
            invite_new_user();
            break;
        }

        exit();
    }

    $current_user = $_SESSION["user"];

    update_session_user_pfp();

    // Set the list of sidebar items based on the current user's role

    switch ($current_user->role) {
    case "Employee":
        $pages = ["dashboard", "tasks", "todo", "tutorials", "forums", "analytics", "chat"];
        break;
    case "Manager":
        $pages = ["dashboard", "projects", "todo", "tutorials", "forums", "analytics", "chat"];
        break;
    case "Admin":
        $pages = ["dashboard", "projects", "todo", "tutorials", "forums", "users", "analytics", "chat"];
        break;
    default:
        echo "Invalid role: $current_user->role";
        die();
    }

    // A flag that is set so subpages know they are being included from the main index file
    const MAIN_RAN = 0;
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="theme-color" content="#ff7a00">

        <link rel="stylesheet" href="../global.css">
        <link rel="stylesheet" href="style.css">

        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="../load-svg-custom-tag.js"></script>
        <script type="text/javascript" src="../show-dialog.js"></script>
        <script type="text/javascript" src="../relative-date-formatter.js"></script>

        <title>Make-It-All</title>
    </head>

    <body>
        <!-- Navigation bar -->

        <div id="profile-menu-dim" class="dimmed-overlay"></div>

        <div class="nav-bar">
            <load-svg id="sidebar-toggle" src="../assets/sidebar-toggle-icon.svg">
                <style shadowRoot>
                    svg {
                        height: 2em;
                    }

                    .fill {
                        fill: var(--text-color);
                    }
                </style>
            </load-svg>

            <!-- Main title logo – switches based on screen width -->
            <div>
                <load-svg id="title-logo" class="logo center" src="../assets/title-logo.svg">
                    <style shadowRoot>
                        svg {
                            height: 4em;
                        }

                        .fill {
                            fill: var(--text-color);
                        }
                    </style>
                </load-svg>

                <load-svg id="simple-logo" class="logo center" src="../assets/logo.svg">
                    <style shadowRoot>
                        svg {
                            height: 4em;
                        }

                        .fill {
                            fill: var(--text-color);
                        }
                    </style>
                </load-svg>
            </div>

            <!-- Profile menu -->
            <div id="profile-menu-dim" class="dimmed-overlay"></div>

            <div id="profile-details">
                <div id="profile-name">
                    <span id="name">
                        <?php echo $current_user->full_name ?>
                    </span>

                    <span id="role">
                        <?php echo $current_user->role ?>
                    </span>
                </div>

                <div id="profile-menu">
                    <button id="profile-menu-button">
                        <div class="notification-badge-container">
                            <!-- User's profile image with an optional notification badge -->
                            <?php
                                $image_name = $current_user->profile_image_path;

                                if ($image_name) {
                                    echo "<img id='profile-icon' src='$image_name' alt='User profile image'>";
                                } else {
                                    echo "<picture>";
                                    echo "<source id='profile-icon-dark' srcset='../img/default-user-profile-image-dark.png' media='(prefers-color-scheme: dark)'>";
                                    echo "<img id='profile-icon' src='../img/default-user-profile-image.png' alt='User profile image'>";
                                    echo "</picture>";
                                }
                            ?>

                            <?php
                                $notification_count = fetch_user_notification_count($current_user->id);

                                if ($notification_count) {
                                    echo "<span class='notification-badge'>$notification_count</span>";
                                }
                            ?>
                        </div>

                        <load-svg id="profile-menu-arrow" src="../assets/menu-arrow.svg">
                            <style shadowRoot>
                                svg {
                                    height: 0.8em;
                                }

                                .fill {
                                    fill: var(--text-color)
                                }
                            </style>
                        </load-svg>
                    </button>

                    <div id="profile-menu-items" class="menu-items">
                        <div id="profile-menu-name">
                            <span id="name">
                                <?php echo $current_user->full_name ?>
                            </span>

                            <span id="role">
                                <?php echo $current_user->role ?>
                            </span>
                        </div>

                        <button id="edit-profile-button" class="menu-item">Edit Profile</button>
                        <button id="notifications-button" class="menu-item">
                            <div class="notification-badge-container">
                                Notifications
                                <?php
                                    if ($notification_count) {
                                        echo "<span class='notification-badge'>$notification_count</span>";
                                    }
                                ?>
                            </div>
                        </button>
                        <div class="divider horizontal" style="width: calc(100% - 16px); margin: 8px"></div>
                        <a href="../helpers/logout.php" class="menu-item">Log Out</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->

        <div id="sidebar">
            <div id="sidebar-links">
                <!-- Sidebar items calculated based on user role -->
                <?php function dashboard_sidebar_item() { ?>
                    <a id="dashboard-sidebar-item" class="sidebar-item" href="?page=dashboard">
                        <load-svg class="sidebar-item-icon" src="../assets/dashboard-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: 2em;
                                    margin-bottom: 0.1em;
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">Dashboard</span>
                    </a>
                <?php } ?>

                <?php function tasks_sidebar_item() { ?>
                    <a id="tasks-sidebar-item" class="sidebar-item" href="?page=tasks">
                        <load-svg class="sidebar-item-icon" src="../assets/tasks-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    height: 2.6em;
                                    margin: 0 0.3em;
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">Tasks</span>
                    </a>
                <?php } ?>

                <?php function projects_sidebar_item() { ?>
                    <a id="projects-sidebar-item" class="sidebar-item" href="?page=projects">
                        <load-svg class="sidebar-item-icon" src="../assets/projects-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    height: 2.4em;
                                    margin: 0 0.1em;
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">Projects</span>
                    </a>
                <?php } ?>

                <?php function todo_sidebar_item() { ?>
                    <a id="todo-sidebar-item" class="sidebar-item" href="?page=todo">
                        <load-svg class="sidebar-item-icon" src="../assets/todo-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: 2.2em;
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">To-do List</span>
                    </a>
                <?php } ?>

                <?php function tutorials_sidebar_item() { ?>
                    <a id="tutorials-sidebar-item" class="sidebar-item" href="?page=tutorials">
                        <load-svg class="sidebar-item-icon" src="../assets/tutorials-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: 2.4em;
                                    padding-bottom: 0.1em
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">Tutorials</span>
                    </a>
                <?php } ?>

                <?php function forums_sidebar_item() { ?>
                    <a id="forums-sidebar-item" class="sidebar-item" href="?page=forums">
                        <load-svg class="sidebar-item-icon" src="../assets/forums-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: 2.4em;
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">Forums</span>
                    </a>
                <?php } ?>

                <?php function users_sidebar_item() { ?>
                    <a id="users-sidebar-item" class="sidebar-item" href="?page=users">
                        <load-svg class="sidebar-item-icon" src="../assets/users-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: 3.2em;
                                    padding-bottom: 0.15em;
                                    margin-left: -0.4em
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">Users</span>
                    </a>
                <?php } ?>

                <?php function analytics_sidebar_item() { ?>
                    <a id="analytics-sidebar-item" class="sidebar-item" href="?page=analytics">
                        <load-svg class="sidebar-item-icon" src="../assets/analytics-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: 2.6em;
                                    margin-bottom: 0.1em;
                                    margin-left: -0.2em
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">Analytics</span>
                    </a>
                <?php } ?>

                <?php function chat_sidebar_item() { ?>
                    <a id="chat-sidebar-item" class="sidebar-item" href="?page=chat">
                        <load-svg class="sidebar-item-icon" src="../assets/chat-sidebar-item-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: 2.4em;
                                }

                                .fill {
                                    fill: var(--fill-color);
                                }
                            </style>
                        </load-svg>
                        <span class="sidebar-item-text">Chat</span>
                    </a>
                <?php } ?>

                <?php
                    // Map page names to defined sidebar item functions

                    $sidebarItems = [
                        "dashboard" => "dashboard_sidebar_item",
                        "projects" => "projects_sidebar_item",
                        "tasks" => "tasks_sidebar_item",
                        "todo" => "todo_sidebar_item",
                        "tutorials" => "tutorials_sidebar_item",
                        "forums" => "forums_sidebar_item",
                        "users" => "users_sidebar_item",
                        "analytics" => "analytics_sidebar_item",
                        "chat" => "chat_sidebar_item"
                    ];

                    foreach ($pages as $page) {
                        if (array_key_exists($page, $sidebarItems)) {
                            $sidebarItem = $sidebarItems[$page];
                            $sidebarItem();
                        }
                    }
                ?>
            </div>

            <div style="flex-grow: 1"></div>

            <!-- Invite button -->
            <div id="sidebar-bottom-content">
                <div class="divider horizontal"></div>

                <button id="invite-button">
                    <load-svg src="../assets/invite-icon.svg">
                        <style shadowRoot>
                            svg {
                                width: 3em;
                            }

                            .fill {
                                fill: var(--accent-color);
                            }
                        </style>
                    </load-svg>

                    <span>Invite</span>
                </button>
            </div>
        </div>

        <script>
            // Handle showing or hiding the sidebar based on screen width and persisted boolean expanded state variable

            const compactWidthMediaQuery = window.matchMedia("(max-width: 800px)")
            compactWidthMediaQuery.addEventListener("change", handleSidebarDimOverlay)

            function handleSidebarDimOverlay(mediaQuery) {
                const dimmedOverlay = $("#sidebar-dim.dimmed-overlay")

                if (mediaQuery.matches) {
                    if ($(document.body).hasClass("sidebar-expanded")) {
                        dimmedOverlay.fadeIn(500)
                        dimmedOverlay.css("width", "calc(100% - 250px)")
                        return
                    }
                }

                dimmedOverlay.fadeOut(500)
                dimmedOverlay.css("width", "100%")
            }

            function toggleSidebar() {
                $(document.body).toggleClass("sidebar-expanded")
                localStorage.setItem("sidebarExpanded", $(document.body).hasClass("sidebar-expanded"))

                handleSidebarDimOverlay(compactWidthMediaQuery)
            }

            const body = $(document.body)

            body.addClass("no-transition")

            // Persist sidebar toggle between refreshes
            if (localStorage.getItem("sidebarExpanded") === "true") {
                body.toggleClass("sidebar-expanded")
            }

            const params = new URLSearchParams(document.location.search)
            const page = params.get("page")
            $(`#${page}-sidebar-item`).addClass("selected")

            document.body.offsetHeight // Force reflow
            body.removeClass("no-transition")

            if (compactWidthMediaQuery.matches) {
                if (body.hasClass("sidebar-expanded")) {
                    toggleSidebar()
                }
            }
        </script>

        <div id="sidebar-dim" class="dimmed-overlay"></div>

        <!-- Main content – derived from selected sidebar item and "page" GET parameter -->

        <div id="main-content-wrapper">
            <div id="main-content">
                <?php
                    // Check "page" GET parameter exists else default to showing the dashboard

                    if (!empty($_GET["page"]) && in_array($_GET["page"], $pages)) {
                        $page = $_GET["page"];
                    } else {
                        $page = "dashboard";
                    }

                    $dir = $page;

                    // Account for managers/admins and employees having different dashboards

                    if ($page == "dashboard") {
                        switch ($_SESSION["user"]->role) {
                        case "Admin":
                        case "Manager":
                            $page = "manager-dashboard";
                            break;
                        case "Employee":
                            $page = "employee-dashboard";
                            break;
                        }
                    }

                    include(__DIR__ . "/$dir/$page.php");
                ?>
            </div>
        </div>

        <!-- Edit profile modal -->

        <div id="edit-profile-modal" class="modal">
            <script>
                const user = JSON.parse(`<?php echo json_encode($_SESSION["user"]) ?>`)
            </script>

            <div class="dimmed-overlay"></div>

            <div id="edit-profile-card" class="modal-card center">
                <div style="width: 100%">
                    <div id="edit-profile-image">
                        <picture>
                            <source id="edit-profile-user-image-dark" srcset="../img/default-user-profile-image-dark.png" media="(prefers-color-scheme: dark)">
                            <img id="edit-profile-user-image" src="../img/default-user-profile-image.png" alt="User profile image">
                        </picture>

                        <div id="edit-profile-upload-image">
                            <load-svg id="edit-profile-upload-image-icon" src="../assets/image-upload-icon.svg">
                                <style shadowRoot>
                                    svg {
                                        height: 3em;
                                    }

                                    .fill {
                                        fill: var(--fill-color)
                                    }
                                </style>
                            </load-svg>
                        </div>

                        <input class="image-upload" type="file" accept="image/png, image/jpeg">
                    </div>
                </div>

                <div class="fade-in-overlay"></div>

                <div id="edit-profile-form">
                    <div id="edit-first-name" class="edit-profile-detail">
                        <span id="edit-first-name-label">First Name</span>
                        <input id="edit-first-name-input" type="text">
                    </div>

                    <div id="edit-last-name" class="edit-profile-detail">
                        <span id="edit-last-name-label">Last Name</span>
                        <input id="edit-last-name-input" type="text">
                    </div>

                    <div id="edit-email" class="edit-profile-detail">
                        <span id="edit-email-label">Email Address</span>
                        <input id="edit-email-input" type="email" readonly>
                    </div>

                    <button id="edit-profile-change-password-button">
                        <span>Change Password</span>
                    </button>

                    <div id="edit-profile-change-password">
                        <div class="divider horizontal" style="width: calc(90% - 16px); margin: 3em auto"></div>

                        <div id="edit-current-password" class="edit-profile-detail">
                            <span id="edit-current-password-label">Current Password</span>

                            <div id="edit-current-password-input-container">
                                <input id="edit-current-password-input" type="password">
                            </div>
                        </div>

                        <div id="edit-new-password" class="edit-profile-detail">
                            <span id="edit-new-password-label">New Password</span>

                            <div id="edit-new-password-input-container">
                                <input id="edit-new-password-input" type="password">

                                <button id="show-hide-password-button">
                                    <load-svg id="show-password-icon" src="../assets/show-icon.svg">
                                        <style shadowRoot>
                                            svg {
                                                height: 1.25em;
                                                padding-top: 0.2em
                                            }

                                            .fill {
                                                fill: var(--icon-color)
                                            }
                                        </style>
                                    </load-svg>

                                    <load-svg id="hide-password-icon" src="../assets/hide-icon.svg">
                                        <style shadowRoot>
                                            svg {
                                                height: var(--body);
                                            }

                                            .fill {
                                                fill: var(--icon-color)
                                            }
                                        </style>
                                    </load-svg>
                                </button>
                            </div>

                            <span id="edit-new-password-requirements">
                                Password must be a <span id="edit-password-reqs-min-chars" class="edit-password-req">minimum of 12 characters</span>, contain <span id="edit-password-reqs-uppercase" class="edit-password-req">an uppercase letter</span>, <span id="edit-password-reqs-lowercase" class="edit-password-req">a lowercase letter</span>, <span id="edit-password-reqs-number" class="edit-password-req">a number</span>, and <span id="edit-password-reqs-symbol" class="edit-password-req">a symbol</span>.
                            </span>
                        </div>

                        <div id="edit-confirm-password" class="edit-profile-detail">
                            <span id="edit-confirm-password-label">Confirm Password</span>

                            <div id="edit-confirm-password-input-container">
                                <input id="edit-confirm-password-input" type="password">
                            </div>
                        </div>
                    </div>

                    <div id="dismiss-buttons">
                        <div class="fade-out-overlay"></div>

                        <button id="cancel-button" class="dismiss-edit-profile-button modal-dismiss-button">Cancel</button>
                        <button id="save-button" class="dismiss-edit-profile-button" disabled>Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications modal -->

        <div id="notifications-modal" class="modal">
            <div class="dimmed-overlay"></div>

            <div id="notifications-card" class="modal-card center">
                <button id="close-notifications-modal-button" class="modal-dismiss-button">
                    <load-svg id="close-notifications-modal-icon" src="../assets/close-icon.svg">
                        <style shadowRoot>
                            svg {
                                width: 1.5em;
                                height: 1.5em;
                            }

                            .fill {
                                fill: var(--secondary-label-color)
                            }
                        </style>
                    </load-svg>
                </button>

                <h1>Notifications<?php echo $notification_count ? " ($notification_count)" : "" ?></h1>

                <div class="fade-in-overlay"></div>

                <span id="no-notifications-placeholder">No Notifications</span>

                <div id="notifications-list"></div>

                <div id="notifications-preferences-container">
                    <div class="fade-out-overlay"></div>

                    <div class="divider horizontal" style="width: calc(100% - 16px); margin: 8px"></div>

                    <div id="notifications-preferences">
                        Receive notifications when:

                        <div id="notifications-preference-options">
                            <div>
                                <input type="checkbox" id="favourited-tutorial-notifications-checkbox" name="favourited-tutorial-notifications"/>
                                <label for="favourited-tutorial-notifications-checkbox"> my favourited tutorials are updated</label>
                            </div>

                            <div>
                                <input type="checkbox" id="favourited-forum-post-notifications-checkbox" name="favourited-forum-post-notifications"/>
                                <label for="favourited-forum-post-notifications-checkbox"> my favourited forum posts are updated</label>
                            </div>

                            <div>
                                <input type="checkbox" id="created-forum-post-notifications-checkbox" name="created-forum-post-notifications"/>
                                <label for="created-forum-post-notifications-checkbox"> my forum posts receive replies</label>
                            </div>

                            <div>
                                <input type="checkbox" id="project-tasks-notifications-checkbox" name="project-tasks-notifications"/>
                                <label for="project-tasks-notifications-checkbox">
                                    <?php
                                        switch ($current_user->role) {
                                        case "Admin":
                                        case "Manager":
                                            echo "tasks in my projects are updated";
                                            break;
                                        case "Employee":
                                            echo "a new task is assigned to me";
                                            break;
                                        }
                                    ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invite member modal -->

        <div id="invite-member-modal" class="modal">
            <div class="dimmed-overlay"></div>

            <div id="invite-member-card" class="modal-card center">
                <button id="close-invite-member-modal-button" class="modal-dismiss-button">
                    <load-svg id="close-invite-member-modal-icon" src="../assets/close-icon.svg">
                        <style shadowRoot>
                            svg {
                                width: 1.5em;
                                height: 1.5em;
                            }

                            .fill {
                                fill: var(--secondary-label-color)
                            }
                        </style>
                    </load-svg>
                </button>

                <h1>Invite Member</h1>

                <div>
                    <input id="invite-member-email" type="email" placeholder="Member email address">

                    <button id="invite-member-button" disabled>Invite</button>
                </div>

                <span>Email must be added to the system and not yet registered.</span>

                <div>
                    <input id="invite-link" type="text" placeholder="Invite link" readonly>

                    <button id="copy-invite-link-button" disabled>
                        <load-svg id="copy-invite-link-icon" src="../assets/copy-icon.svg">
                            <style shadowRoot>
                                svg {
                                    height: var(--body);
                                    padding-top: 0.2em;
                                }

                                .fill {
                                    fill: var(--icon-color)
                                }
                            </style>
                        </load-svg>
                    </button>
                </div>

                <span>This invite link will expire in 48 hours.</span>
            </div>
        </div>

        <!-- Dialog -->

        <div id="dialog">
            <div id="dialog-dim" class="dimmed-overlay"></div>

            <div id="dialog-card" class="dialog-card center">
                <div>
                    <span class="dialog-title"></span>
                    <span class="dialog-message"></span>
                </div>

                <div id="dialog-buttons"></div>
            </div>
        </div>

        <script type="text/javascript" src="script.js"></script>
    </body>
</html>

<?php
    // Updates the user object stored in the current session
    function update_session_user(): void {
        $current_user = $_SESSION["user"];
        $updated_user = get_user($current_user->id);

        foreach ($updated_user as $key => $value) {
            $current_user->{$key} = $value;
        }

        update_session_user_pfp();
    }

    // Updates the user object stored in the current session by adding a path property to the user's profile image file
    function update_session_user_pfp(): void {
        $current_user = $_SESSION["user"];
        $current_user->profile_image_path = get_user_pfp($current_user);
    }

    // Gets the full path (from root) of the specified user's profile image
    function get_user_pfp_from_id(int $user_id): ?string {
        $user = get_user($user_id);
        return get_user_pfp($user);
    }

    // Gets the full path (from root) of the specified user's profile image
    function get_user_pfp(object $user): ?string {
        $path = null;
        $name = $user->profile_image_name;

        if ($name) {
            $absolute_path = realpath(__DIR__ . "/../uploads/user-profile-images/$name");
            $path = "/" . trim(str_replace($_SERVER["DOCUMENT_ROOT"], "", $absolute_path), "/");
        }

        return $path;
    }

    // Uploads the specified profile image file to the systems uploads folder
    function upload_profile_image(mixed $profile_image): void {
        $extension = pathinfo($profile_image["name"], PATHINFO_EXTENSION);

        $image_tmp_name = $profile_image["tmp_name"];

        $file_name = uniqid() . ".$extension";
        $file_url = __DIR__ . "/../uploads/user-profile-images/$file_name";

        if (move_uploaded_file($image_tmp_name, $file_url)) {
            echo json_encode(["success" => true, "file_name" => $file_name]);
        } else {
            echo json_encode(["success" => false, "message" => "Error moving file"]);
        }

        /* GD lib not configured on VM
        $image = match ($extension) {
            "jpeg" => imagecreatefromjpeg($image_tmp_name),
            "png" => imagecreatefrompng($image_tmp_name),
            default => false
        };

        if (!$image) {
            echo json_encode(["success" => false, "message" => "Invalid image file extension $extension"]);
            return;
        }

        $image_width = imagesx($image);
        $image_height = imagesy($image);
        $crop_size = min($image_width, $image_height);

        $cropped_image = imagecrop($image, [
            "x" => max(0, floor(($image_width / 2) - ($crop_size / 2))),
            "y" => max(0, floor(($image_height / 2) - ($crop_size / 2))),
            "width" => $crop_size,
            "height" => $crop_size
        ]);

        if ($cropped_image !== false) {
            $file_name = uniqid() . ".$extension";
            $file_url = __DIR__ . "/../uploads/user-profile-images/$file_name";

            $result = match ($extension) {
                "jpeg" => imagejpeg($cropped_image, $file_url),
                "png" => imagepng($cropped_image, $file_url),
                default => false
            };

            if ($result) {
                echo json_encode(["success" => true, "file_name" => $file_name]);
            } else {
                echo json_encode(["success" => false, "message" => "Error moving file"]);
            }

            imagedestroy($cropped_image);
        } else {
            echo json_encode(["success" => false, "message" => "Error cropping image"]);
        }

        imagedestroy($image);
        */
    }

    // Validates the POSTed password against the current session user's stored password
    function validate_password(): void {
        $password = $_POST["current_password"] ?? null;
        echo $password && password_verify($password, $_SESSION["user"]->password_hash) ? "true" : "false";
    }

    // Updates the specified POSTed properties for the current session user
    function save_updated_user(): void {
        $first_name = $_POST["first_name"] ?? null;
        $last_name = $_POST["last_name"] ?? null;
        $profile_image_url = $_POST["profile_image_url"] ?? null;
        $password = $_POST["password"] ?? null;

        $user_id = $_SESSION["user"]->id;

        echo update_user($user_id, first_name: $first_name, last_name: $last_name, profile_image_name: $profile_image_url, password: $password);

        update_session_user();
    }

    // Deletes the notification with the POSTed id
    // If the notification id is not specified then return all notifications for the current session user
    function update_user_notifications(): void {
        $current_user = $_SESSION["user"];

        $notification_id = $_POST["notification_id"] ?? null;

        if ($notification_id !== null) {
            echo delete_notification($notification_id);
        } else {
            echo json_encode(fetch_user_notifications($current_user->id));
        }
    }

    // Updates the specified POSTed notifications preferences for the current session user
    // If none are specified, return all the preferences for the current user
    function update_notification_preferences(): void {
        $current_user = $_SESSION["user"];

        $favourited_tutorials_preference = $_POST["favourited_tutorial_notifications_preference"] ?? null;
        $favourited_forum_posts_preference = $_POST["favourited_forum_post_notifications_preference"] ?? null;
        $created_forum_posts_preference = $_POST["created_forum_post_notifications_preference"] ?? null;
        $project_tasks_preference = $_POST["project_tasks_notifications_preference"] ?? null;

        if ($favourited_tutorials_preference !== null || $favourited_forum_posts_preference !== null || $created_forum_posts_preference !== null || $project_tasks_preference != null) {
            echo update_user_notification_preferences($current_user->id, $favourited_tutorials_preference, $favourited_forum_posts_preference, $created_forum_posts_preference, $project_tasks_preference);
        } else {
            echo json_encode(get_user_notification_preferences($current_user->id));
        }
    }

    // Invite a new user with the POSTed email address
    function invite_new_user(): void {
        $email = $_POST["email"] ?? null;

        if ($email) {
            echo invite_user($email) ?? false;
        } else {
            echo false;
        }
    }
