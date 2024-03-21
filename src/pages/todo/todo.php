<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=todo");
        die();
    }
?>

<?php
    include __DIR__ . '/../../database/database-connect.php';
    include __DIR__ . '/../../database/to-do-db-helpers.php';

    // Get the current users saved items
    echo "<script> let currentUser = " . json_encode($_SESSION['user']->id) . ";</script>";
    $items = fetch_to_do_items($_SESSION['user']->id);
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="stylesheet" href="todo/todo.css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

        <title>To-Do List</title>
    </head>

    <body>
        <div class="to-do-container">
            <div class="items-container-wrapper">
                <div class="items-container">
                    <div class="headers">
                        <div class="header header-task-name">
                            <p>Item</p>
                        </div>

                        <div class="header header-due-date">
                            <p>Due Date</p>
                        </div>

                        <div class="header header-due-time">
                            <p>Due Time</p>
                        </div>

                        <div class="header header-priority">
                            <p>Priority</p>
                        </div>

                        <div class="header delete">
                        </div>

                    </div>

                    <div class="scrollable-content">
                        <!-- For each item in user's list create Html -->
                    <?php if ($items) :
                        foreach ($items as $item) : ?>
                            <form class="item <?php
                            if ($item->completed) {
                                echo "complete";
                            }
                            ?>" data-todo-id = "<?php echo $item->id; ?>">
                                <div class="inputs">
                                    <div class="task-name">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon tick" viewBox="0 0 512 512">
                                            <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M352 176L217.6 336 160 272" />
                                        </svg>

                                        <input type="text" placeholder="Task name" class="task-input input" value="<?php echo $item->name ?>">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon ellipsis" viewBox="0 0 512 512">
                                            <circle cx="256" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                            <circle cx="416" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                            <circle cx="96" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                        </svg>
                                    </div>

                                    <div class="due-date">
                                        <input type="text" placeholder="Due Date" class="due-date-input input datepicker" value="<?php
                                        $date = new DateTime(substr($item->due_date, 0, -9));
                                        echo $date->format("d/m/Y");?>">
                                    </div>

                                    <div class="due-time">
                                        <input type="text" readonly="true" placeholder="Due Time" class="due-time-input input timepicker" value="<?php echo substr($item->due_date, -8, -3)?>">
                                    </div>

                                    <div class="priority">
                                        <select name="priority" class="priority-select priority-input input select-input
                                        <?php echo lcfirst($item->priority) ?>">
                                            <option value="Low"
                                            <?php if ($item->priority == "Low") {
                                                echo "selected";
                                            } ?>
                                            >Low</option>
                                            <option value="Medium"
                                            <?php if ($item->priority == "Medium") {
                                                echo "selected";
                                            } ?>
                                            >Medium</option>
                                            <option value="High"
                                            <?php if ($item->priority == "High") {
                                                echo "selected";
                                            } ?>
                                            >High</option>
                                            <option value="None"
                                            <?php if ($item->priority == "None") {
                                                echo "selected";
                                            } ?>
                                            >None</option>
                                        </select>
                                    </div>

                                    <div class="delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon del" viewBox="0 0 512 512">
                                            <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/>
                                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M320 320L192 192M192 320l128-128"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="comments hidden">
                                    <textarea name="comments-input" class="comments-input"><?php
                                    $description = $item->description;
                                    echo $description;
                                    ?></textarea>
                                </div>
                            </form>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            </div>

            <div class="buttons-container">
                <div class="new-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon add" viewBox="0 0 512 512">
                        <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M256 176v160M336 256H176" />
                    </svg>
                </div>

                <div class="right-buttons">
                    <button class="save-list button">Save list</button>
                    <button class="clear-list button">Clear list</button>
                </div>


            </div>
        </div>

        <!-- <script src="../show-dialog.js"></script> -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="todo/todo.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    </body>
</html>
