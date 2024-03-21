<?php

    // include('../database.php');
    include __DIR__ . '/../../database/database-connect.php';
    include __DIR__ . '/../../database/to-do-db-helpers.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve data from the POST request
        $function = $_POST['function'];

        // If the function is delete a single record in the to-do list
        if ($function == 0) {
            $to_do_id = $_POST['todoId'];
            $current_user = $_POST['currentUser'];

            // Using helper function
            $record = get_to_do_item($to_do_id);

            // If there is a successful match for the record then delete the record from the database
            if ($record) {
                delete_to_do_item($to_do_id);
                echo "Record successfully deleted";
            } else {
                echo "Error in deleting record";
            }
        }

        // If the function is delete all items in the to-do list
        if ($function == 1) {
            $to_do_ids = $_POST['todoItemsId'];
            $current_user = $_POST['currentUser'];

            foreach ($to_do_ids as $id) {

                // Using helper function
                $record = get_to_do_item($id);

                if (!$record) {
                    echo "Error in deleting list";
                    return;
                }
            }

            foreach ($to_do_ids as $id) {

                // Using helper function
                $record = get_to_do_item($id);

                delete_to_do_item($id);
            }

            echo "ToDo List successfully deleted";

        }

        // If the function is toggle complete of a single record in the to-do list
        if ($function == 2) {
            $to_do_id = $_POST['todoId'];
            $current_user = $_POST['currentUser'];

            // Using helper function
            $record = get_to_do_item($to_do_id);

            if ($record) {
                // Edit record to toggle complete

                if ($record->completed == 0) {
                    update_to_do_item($to_do_id, is_completed: true);
                } else {
                    update_to_do_item($to_do_id, is_completed: false);
                }
                echo "Record completion successfully toggled";
            }
        }

        // If the function is save items in the list
        if ($function == 3) {
            // Save stuff
            $current_user = $_POST['currentUser'];
            if (isset($_POST['savedTodoItems'])) {
                $savedToDoItems = $_POST['savedTodoItems'];

                foreach ($savedToDoItems as $savedItem) {
                    update_to_do_item($savedItem[0], name: $savedItem[1], description: $savedItem[2], due_date: $savedItem[3], priority: $savedItem[4]);
                }
            }

            if (isset($_POST['newItems'])) {
                $newItems = $_POST['newItems'];

                foreach ($newItems as $newItem) {
                    add_to_do_item($newItem[0], $current_user, $newItem[1], $newItem[2], $newItem[3]);
                }
            }

            echo "ToDo List successfully saved";
        }

        // If the function is get all items for a user
        if ($function == 4) {
            $current_user = $_POST['currentUser'];

            $items = fetch_to_do_items($current_user);

            $items_to_json = json_encode($items);

            print_r($items_to_json);

            header('Content-Type: application/json');

            return $items_to_json;
        }
    }

?>
