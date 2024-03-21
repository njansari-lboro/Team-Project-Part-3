<?php
    require_once(__DIR__ . "/base-db-helpers.php");

    // FETCHING TUTORIALS

    /**
     * Fetches the tutorial with the specified ID from the database.
     *
     * @param int $tutorial_id The ID of the tutorial to be fetched.
     *
     * @return ?object Returns the tutorial as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $tutorial = get_tutorial(1);
     * echo $tutorial->name; // "How to print"
     * ```
     */
    function get_tutorial(int $tutorial_id): ?object {
        $sql = "SELECT * FROM tutorial WHERE id = ?";
        return get_record($sql, "i", $tutorial_id);
    }

    /**
     * Fetches the tutorials from the database filtered using the specified properties.
     *
     * @param ?string $filter_text [optional] A filter string that matches a tutorial based on their name property.
     * @param ?int $creator_id [optional] The ID of the tutorial's creator to filter by.
     * @param ?bool $is_technical [optional] The technical status of the tutorial to filter by.
     *
     * @return array An array of tutorials as objects sorted by most recently updated.
     *
     * Usage example:
     * ```
     * $all_tutorials = fetch_tutorials();
     *
     * // Filter tutorials on whether their name contains "how to"
     * $tutorials = fetch_tutorials(filter_text: "how to");
     * echo $tutorials; // [(How to print), (How to email)]
     *
     * // Get all tutorials that were created by the user with id 10 that are technical
     * $tutorials = fetch_tutorials(creator_id: 10, is_technical: true);
     * ```
     */
    function fetch_tutorials(?string $filter_text = null, ?int $creator_id = null, ?bool $is_technical = null): array {
        $sql = "SELECT * FROM tutorial WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($filter_text !== null) {
            $sql .= " AND name LIKE ?";
            $types .= "";
            $vars[] = "%$filter_text%"; // Adding wildcards for partial matching
        }

        if ($creator_id !== null) {
            $sql .= " AND owner_id = ?";
            $types .= "i";
            $vars[] = $creator_id;
        }

        if ($is_technical !== null) {
            $sql .= " AND is_technical = ?";
            $types .= "i";
            $vars[] = $is_technical;
        }

        $sql .= " ORDER BY last_updated DESC";

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING TUTORIALS

    /**
     * Adds a new tutorial to the database with the specified property values.
     *
     * @param string $name The new tutorial's name.
     * @param int $creator_id The ID of the user who is the new tutorial's creator.
     * @param string $cover_image The new tutorial's cover image.
     * @param bool $is_technical The new tutorial's technical status.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_tutorial("How to print", 10, "/img/print-image.png", false);
     * ```
     */
    function add_tutorial(string $name, int $creator_id, string $cover_image, bool $is_technical): bool {
        $sql = "INSERT INTO tutorial (name, owner_id, cover_image, is_technical) VALUES (?, ?, ?, ?)";
        return modify_record($sql, "sisi", $name, $creator_id, $cover_image, $is_technical);
    }

    /**
     * Updates the specified property values of a tutorial.
     *
     * @param int $tutorial_id The ID of the tutorials being updated.
     * @param ?string $name [optional] The updated name of the tutorial.
     * @param ?string $cover_image [optional] The updated cover image of the tutorial.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_tutorial(1, cover_image: "/img/office-printer.png");
     * ```
     */
    function update_tutorial(int $tutorial_id, ?string $name = null, ?string $cover_image = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($name !== null) {
            $update_fields[] = "name = ?";
            $types .= "s";
            $vars[] = $name;
        }

        if ($cover_image !== null) {
            $update_fields[] = "cover_image = ?";
            $types .= "s";
            $vars[] = $cover_image;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE tutorial SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $types .= "i";
        $vars[] = $tutorial_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a tutorial from the system with the specified ID.
     *
     * @param int $tutorial_id The ID of the tutorial to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_tutorial(1);
     * ```
     */
    function delete_tutorial(int $tutorial_id): bool {
        $sql = "DELETE FROM tutorial WHERE id = ?";
        return modify_record($sql, "i", $tutorial_id);
    }

    // FETCHING TUTORIAL STEPS

    /**
     * Fetches the tutorial step with the specified ID from the database.
     *
     * @param int $step_id The ID of the tutorial step to be fetched.
     *
     * @return ?object Returns the tutorial step as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $tutorial_step = get_tutorial_step(3);
     * echo $tutorial_step->description; // "Press Ctrl+P on the keyboard."
     * ```
     */
    function get_tutorial_step(int $step_id): ?object {
        $sql = "SELECT * FROM step WHERE id = ?";
        return get_record($sql, "i", $step_id);
    }

    /**
     * Fetches the tutorial steps from the database filtered using the specified properties.
     *
     * @param ?int $tutorial_id [optional] The ID of the tutorial to filter by.
     *
     * @return array An array of tutorial steps as objects.
     *
     * Usage example:
     * ```
     * $all_tutorial_steps = fetch_tutorial_steps();
     *
     * // Get the all steps that are in the tutorial with id 1
     * $tutorial_steps = fetch_tutorial_steps(tutorial_id: 1);
     * ```
     */
    function fetch_tutorial_steps(?int $tutorial_id = null): array {
        $sql = "SELECT * FROM step WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars =  [];

        if ($tutorial_id !== null) {
            $sql .= " AND tutorial_id = ?";
            $types .= "i";
            $vars[] = $tutorial_id;
        }

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING TUTORIAL STEPS

    /**
     * Adds a new tutorial step to the database with the specified property values.
     *
     * @param int $tutorial_id The ID of the tutorial to whom the new tutorial step belongs to.
     * @param string $image_name The file name of the new tutorial step's image.
     * @param string $description The new tutorial step's description.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_tutorial_step(1, "print-image.png", "Press Ctrl+P on the keyboard.");
     * ```
     */
    function add_tutorial_step(int $tutorial_id, string $image_name, string $description): bool {
        $sql = "INSERT INTO step (tutorial_id, image_name, description) VALUES (?, ?, ?)";
        return modify_record($sql, "iss", $tutorial_id, $image_name, $description);
    }

    /**
     * Updates the specified property values of a tutorial step.
     *
     * @param int $step_id The ID of the tutorial step being updated.
     * @param ?string $image_name [optional] The updated file name of the image of the tutorial step.
     * @param ?string $description [optional] The updated description of the tutorial step.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_tutorial_step(3, description: "Press Ctrl+P on the keyboard, or go to File>Print.");
     * ```
     */
    function update_tutorial_step(int $step_id, string $image_name = null, string $description = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($image_name !== null) {
            $update_fields[] = "image_name = ?";
            $types .= "s";
            $vars[] = $image_name;
        }

        if ($description !== null) {
            $update_fields[] = "description = ?";
            $types .= "s";
            $vars[] = $description;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE step SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $types .= "i";
        $vars[] = $step_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a tutorial step from the system with the specified ID.
     *
     * @param int $step_id The ID of the tutorial step to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_tutorial_step(3);
     * ```
     */
    function delete_tutorial_step(int $step_id): bool {
        $sql = "DELETE FROM step WHERE id = ?";
        return modify_record($sql, "i", $step_id);
    }

    // FETCHING USER-FAVOURITED TUTORIALS

    /**
     * Fetches the tutorials from the database that are favourited by the user with the specified ID.
     *
     * @param int $user_id The ID of the user to fetch favourited tutorials from.
     *
     * @return array An array of tutorials as objects sorted by most recently updated.
     *
     * Usage example:
     * ```
     * $all_favourited_tutorials = fetch_user_favourited_tutorials(10);
     * ```
     */
    function fetch_user_favourited_tutorials(int $user_id): array {
        $sql = "SELECT tutorial.* FROM tutorial JOIN user_tutorial_favourite AS favourites ON tutorial.id = favourites.tutorial_id WHERE favourites.user_id = ? ORDER BY tutorial.last_updated DESC";
        return fetch_records($sql, "i", $user_id);
    }

    // MODIFYING USER-FAVOURITED TUTORIALS

    /**
     * Favourites a tutorial with the specified ID for the user with the specified ID.
     *
     * @param int $user_id The ID of the user who is favouriting the tutorial.
     * @param int $tutorial_id The ID of the tutorial being favourited.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * favourite_tutorial(10, 1);
     * ```
     */
    function favourite_tutorial(int $user_id, int $tutorial_id): bool {
        $sql = "INSERT INTO user_tutorial_favourite (user_id, tutorial_id) VALUES (?, ?)";
        return modify_record($sql, "ii", $user_id, $tutorial_id);
    }

    /**
     * Unfavourites a tutorial with the specified ID for the user with the specified ID.
     *
     * @param int $user_id The ID of the user who is unfavouriting the tutorial.
     * @param int $tutorial_id The ID of the tutorial being unfavourited.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * unfavourite_tutorial(10, 1);
     * ```
     */
    function unfavourite_tutorial(int $user_id, int $tutorial_id): bool {
        $sql = "DELETE FROM user_tutorial_favourite WHERE user_id = ? AND tutorial_id = ?";
        return modify_record($sql, "ii", $user_id, $tutorial_id);
    }
