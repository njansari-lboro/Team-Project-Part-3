<?php
    require_once(__DIR__ . "/base-db-helpers.php");

    // FETCHING TO-DO ITEMS

    /**
     * Fetches the to-do item with the specified ID from the database.
     *
     * @param int $item_id The ID of the to-do item to be fetched.
     *
     * @return ?object Returns the to-do item as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $item = get_to_do_item(2);
     * echo $item->name; // "Make coffee"
     * ```
     */
    function get_to_do_item(int $item_id): ?object {
        $sql = "SELECT * FROM todo WHERE id = ?";
        return get_record($sql, "i", $item_id);
    }

    /**
     * Fetches the to-do items from the database filtered using the specified properties.
     *
     * @param ?int $user_id [optional] The ID of the user to filter by.
     *
     * @return array An array of to-do items as objects.
     *
     * Usage example:
     * ```
     * $all_to_do_items = fetch_to_do_items();
     *
     * // Get the all to-do items for the user with id 10
     * $to_do_items = fetch_to_do_items(user_id: 10);
     * ```
     */
    function fetch_to_do_items(?int $user_id = null): array {
        $sql = "SELECT * FROM todo WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($user_id !== null) {
            $sql .= " AND user_id = ?";
            $types .= "i";
            $vars[] = $user_id;
        }

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING TO-DO ITEMS

    /**
     * Adds a new to-do item to the database with the specified property values.
     *
     * @param string $name The new to-do item's name.
     * @param int $user_id The ID of the user to whom the new to-do item belongs to.
     * @param string $description The new to-do item's description.
     * @param string $due_date The new to-do item's due date, as a date in the format "YYYY-MM-DD hh:mm:ss".
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_to_do_item("Make coffee", 10, "Make an espresso in the morning", "2023-12-08 09:00:00");
     * ```
     */
    function add_to_do_item(string $name, int $user_id, string $description, string $due_date, string $priority): bool {
        $sql = "INSERT INTO todo (name, user_id, description, due_date, priority) VALUES (?, ?, ?, ?, ?)";
        return modify_record($sql, "sisss", $name, $user_id, $description, $due_date, $priority);
    }

    /**
     * Updates the specified property values of a to-do item.
     *
     * @param int $item_id The ID of the to-do item being updated.
     * @param ?string $name [optional] The updated name of the to-do item.
     * @param ?string $description [optional] The updated description of the to-do item.
     * @param ?string $due_date [optional] The updated due date of the to-do item, as a date in the format "YYYY-MM-DD hh:mm:ss".
     * @param ?string $priority [optional] The updated priority of the to-do item.
     * @param ?bool $is_completed [optional] The updated value for the completion of the to-do item.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_to_do_item(2, priority: "High", is_completed: true);
     * ```
     */
    function update_to_do_item(int $item_id, ?string $name = null, ?string $description = null, ?string $due_date = null, ?string $priority = null, ?bool $is_completed = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($name !== null) {
            $update_fields[] = "name = ?";
            $types .= "s";
            $vars[] = $name;
        }

        if ($description !== null) {
            $update_fields[] = "description = ?";
            $types .= "s";
            $vars[] = $description;
        }

        if ($due_date !== null) {
            $update_fields[] = "due_date = ?";
            $types .= "s";
            $vars[] = $due_date;
        }

        if ($priority !== null) {
            $update_fields[] = "priority = ?";
            $types .= "s";
            $vars[] = $priority;
        }

        if ($is_completed !== null) {
            $update_fields[] = "completed = ?";
            $types .= "i";
            $vars[] = $is_completed;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE todo SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $types .= "i";
        $vars[] = $item_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a to-do item from the system with the specified ID.
     *
     * @param int $item_id
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_to_do_item(2);
     * ```
     */
    function delete_to_do_item(int $item_id): bool {
        $sql = "DELETE FROM todo WHERE id = ?";
        return modify_record($sql, "i", $item_id);
    }
