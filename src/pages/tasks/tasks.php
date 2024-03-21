<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=tasks");
        die();
    }
?>

<?php
    include_once __DIR__ . "/../../database/projects-db-helpers.php";
    include_once __DIR__ . "/../../database/users-db-helpers.php";

    echo "<script> let currentUser = " . json_encode($_SESSION["user"]->id) . "</script>";

    // Get the tasks assigned to this user
    $tasks = fetch_tasks(assigned_user_id: $_SESSION["user"]->id);
    $projects = [];

    // Logic to get the projects based on the tasks id
    if ($tasks) {
        foreach ($tasks as $task) {
            if (!isset($task->project_id, $projects)) {
                $projects[$task->project_id] = [];
            }

            $projects[$task->project_id][] = $task;
        }
    }

    array_filter($projects, function($id) {
        return !get_project($id)?->is_completed;
    }, ARRAY_FILTER_USE_KEY);
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <link rel="stylesheet" href="tasks/tasks.css">
    </head>

    <body>
        <!-- heading row -->
        <div class="title-div">
            <h2 id="projects-header">Tasks</h2>
        </div>

        <div><hr></div>

        <!-- For each project create the HTML required using project variables as needed -->
        <?php if ($projects) : ?>
            <?php foreach ($projects as $project) : ?>
                <div class="dropdown-div">
                    <span class="dropdown">
                        <?php
                            $project_name = get_project($project[0]->project_id);
                            echo $project_name->name;
                        ?>
                    </span>

                    <button class="dropdown" id="<?php echo "project-{$project[0]->project_id}" ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                </div>

                <!-- toggle-able dropdown -->
                <div class="dropdown-content" id="<?php echo "project-{$project[0]->project_id}-content" ?>">
                    <div class="to-do-container">
                        <div class="top-section">
                            <div class="headers">
                                <div class="header header-task-name">
                                    <p>Task Name</p>
                                </div>

                                <div class="header header-start-date">
                                    <p>Start</p>
                                </div>

                                <div class="header header-end-date">
                                    <p>Estimated End</p>
                                </div>

                                <div class="header header-resource-hours">
                                    <p>Resource Hours</p>
                                </div>
                            </div>

                            <div class="items-container-wrapper">
                                <div class="items-container">
                                    <!-- For each task within a project create the relevant HTML to be inserted using relevant attributes -->
                                    <?php foreach ($project as $task) : ?>
                                        <entry class="item <?php if ($task->is_completed) echo "complete" ?>" data-task-id="<?php echo $task->id ?>">
                                            <div class="inputs">
                                                <div class="task-name">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon tick" viewBox="0 0 512 512">
                                                        <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/>
                                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M352 176L217.6 336 160 272"/>
                                                    </svg>

                                                    <p class="task-input input"><?php echo $task->name ?></p>

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon ellipsis" viewBox="0 0 512 512">
                                                        <circle cx="256" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/>
                                                        <circle cx="416" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/>
                                                        <circle cx="96" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/>
                                                    </svg>
                                                </div>

                                                <div class="start-date">
                                                    <p class="due-date-input input datepicker">
                                                        <?php
                                                            $start_date = new DateTime($task->start_date);
                                                            echo $start_date->format("d/m/Y");
                                                        ?>
                                                    </p>
                                                </div>

                                                <div class="end-date">
                                                    <p class="due-date-input input datepicker">
                                                        <?php
                                                            $estimated_end_date = new DateTime($task->estimated_end_date);
                                                            echo $estimated_end_date->format("d/m/Y");
                                                        ?>
                                                    </p>
                                                </div>

                                                <div class="resource-hours">
                                                    <input class="resource-hours-input input" type="number" id="hours" name="hours" min="0" step="0.25" value="<?php echo $task->hours_spent ?>">
                                                    <button class="update button">Update</button>
                                                </div>
                                            </div>

                                            <div class="comments hidden">
                                                <ul class="comments-input">
                                                    <?php echo $task->description ?>
                                                </ul>

                                                <div class="task-owner">
                                                    <?php $pfp_path = get_user_pfp_from_id($task->owner_id) ?>

                                                    <?php if ($pfp_path): ?>
                                                        <img src="<?php echo $pfp_path?>" alt="User profile image" style="width: 25%; aspect-ratio: 1; object-fit: cover; object-position: center; border-radius: 50%; border: 1px solid var(--separator-color)">
                                                    <?php else: ?>
                                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 20.2832 19.9316" preserveAspectRatio="xMinYMin meet" class="icon-owner">
                                                            <g>
                                                                <rect height="19.9316" opacity="0" width="20.2832" x="0" y="0" />
                                                                <path class="fill primary" d="M9.96094 19.9219C15.4102 19.9219 19.9219 15.4004 19.9219 9.96094C19.9219 4.51172 15.4004 0 9.95117 0C4.51172 0 0 4.51172 0 9.96094C0 15.4004 4.52148 19.9219 9.96094 19.9219ZM9.96094 18.2617C5.35156 18.2617 1.66992 14.5703 1.66992 9.96094C1.66992 5.35156 5.3418 1.66016 9.95117 1.66016C14.5605 1.66016 18.2617 5.35156 18.2617 9.96094C18.2617 14.5703 14.5703 18.2617 9.96094 18.2617ZM16.6406 16.3965L16.6113 16.2891C16.1328 14.8535 13.5547 13.2812 9.96094 13.2812C6.37695 13.2812 3.79883 14.8535 3.31055 16.2793L3.28125 16.3965C5.03906 18.1348 8.05664 19.1504 9.96094 19.1504C11.875 19.1504 14.8633 18.1445 16.6406 16.3965ZM9.96094 11.6211C11.8457 11.6406 13.3105 10.0391 13.3105 7.93945C13.3105 5.9668 11.8359 4.33594 9.96094 4.33594C8.08594 4.33594 6.60156 5.9668 6.61133 7.93945C6.62109 10.0391 8.08594 11.6016 9.96094 11.6211Z" />
                                                            </g>
                                                        </svg>
                                                    <?php endif ?>

                                                    <div class="owner-details">
                                                        <p class="owner">Owner</p>
                                                        <p class="owner-name">
                                                            <?php
                                                                $owner = get_user($task->owner_id);
                                                                echo $owner->full_name;
                                                            ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </entry>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fade-out-overlay"></div>
                </div>

                <div><hr></div>
            <?php endforeach ?>
        <?php endif ?>

        <script src="tasks/tasks.js"></script>
    </body>
</html>
