<?php
    require_once(__DIR__ . "/base-db-helpers.php");

    // FETCHING USERS

    /**
     * Fetches the user with the specified ID from the database.
     *
     * @param int $user_id The ID of the user to be fetched.
     *
     * @return ?object Returns the user as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $user = get_user(10);
     * echo $user->first_name; // "John"
     * ```
     */
    function get_user(int $user_id): ?object {
        $sql = "SELECT * FROM user WHERE id = ?";
        return get_record($sql, "i", $user_id);
    }

    /**
     * Fetches the user with the specified email address from the database.
     *
     * @param string $email The email address of the user to be fetched.
     *
     * @return ?object Returns the user as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $user = get_user_from_email("johncena@make-it-all.co.uk");
     * echo $user->first_name; // "John"
     * ```
     */
    function get_user_from_email(string $email): ?object {
        $sql = "SELECT * FROM user WHERE email = ?";
        return get_record($sql, "s", $email);
    }

    /**
     * Fetches the users from the database filtered using the specified properties.
     *
     * @param ?string $filter_text [optional] A filter string that matches a user based on their name properties.
     * @param ?string $role [optional] The role of a user to filter by.
     *
     * @return array An array of users as objects.
     *
     * Usage example:
     * ```
     * $all_users = fetch_users();
     *
     * // Filter users on whether their name contains "jo"
     * $users = fetch_users(filter_text: "jo");
     * echo $users; // [(John Cena), (Joe Smith)]
     * ```
     */
    function fetch_users(?string $filter_text = null, ?string $role = null): array {
        $sql = "SELECT * FROM user WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($filter_text !== null) {
            $sql .= " AND full_name LIKE ?";

            $filter_text = "%$filter_text%"; // Adding wildcards for partial matching

            $types .= "s";
            $vars[] = $filter_text;
        }

        if ($role !== null) {
            $sql .= " AND role = ?";
            $types .= "s";
            $vars[] = $role;
        }

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING USERS

    /**
     * Adds a new user to the database with the specified property values.
     *
     * @param string $first_name The new user's first name.
     * @param string $last_name The new user's last name.
     * @param string $email The new users email address.
     * @param string $role The role of the new user.
     * @param bool $is_technical Whether the new user is a technical specialist.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_user("John", "Cena", "johncena@make-it-all.co.uk", "Manager", true);
     * ```
     */
    function add_user(string $first_name, string $last_name, string $email, string $role, bool $is_technical): bool {
        $sql = "INSERT INTO user (first_name, last_name, email, role, is_technical) VALUES (?, ?, ?, ?, ?)";
        return modify_record($sql, "ssssi", $first_name, $last_name, $email, $role, $is_technical);
    }

    /**
     * Registers a user with the system with the specified property values.
     *
     * @param int $user_id The ID of the user being registered.
     * @param string $first_name The user's first name.
     * @param string $last_name The user's last name.
     * @param string $password The user's password.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * register_user(10, "John", "Cena", "Y0uCantSeeM3!");
     * ```
     */
    function register_user(int $user_id, string $first_name, string $last_name, string $password): bool {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE user SET first_name = ?, last_name = ?, password_hash = ?, registered = TRUE WHERE id = ?";
        return modify_record($sql, "sssi", $first_name, $last_name, $hashed_password, $user_id);
    }

    /**
     * Updates the specified property values of a user.
     *
     * @param int $user_id The ID of the user being updated.
     * @param ?string $first_name [optional] The updated first name of the user.
     * @param ?string $last_name [optional] The updated last name of the user.
     * @param ?string $role [optional] The updated role of the user.
     * @param ?bool $is_technical [optional] The updated technicality of the user.
     * @param ?string $profile_image_name [optional] The file name of the updated profile image of the user.
     * @param ?string $password [optional] The updated password for the user.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_user(10, first_name: "Joe", password: "youCanSeeM3?");
     * ```
     */
    function update_user(int $user_id, ?string $first_name = null, ?string $last_name = null, ?string $role = null, ?bool $is_technical = null, ?string $profile_image_name = null, ?string $password = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($first_name !== null) {
            $update_fields[] = "first_name = ?";
            $types .= "s";
            $vars[] = $first_name;
        }

        if ($last_name !== null) {
            $update_fields[] = "last_name = ?";
            $types .= "s";
            $vars[] = $last_name;
        }

        if ($role !== null) {
            $update_fields[] = "role = ?";
            $types .= "s";
            $vars[] = $role;
        }

        if ($is_technical !== null) {
            $update_fields[] = "is_technical = ?";
            $types .= "i";
            $vars[] = $is_technical;
        }

        if ($profile_image_name !== null) {
            $update_fields[] = "profile_image_name = ?";
            $types .= "s";
            $vars[] = $profile_image_name;
        }

        if ($password !== null) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $update_fields[] = "password_hash = ?";
            $types .= "s";
            $vars[] = $hashed_password;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE user SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $types .= "i";
        $vars[] = $user_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a user from the system with the specified ID.
     *
     * @param int $user_id The ID of the user to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_user(10);
     * ```
     */
    function delete_user(int $user_id): bool {
        $sql = "DELETE FROM user WHERE id = ?";
        return modify_record($sql, "i", $user_id);
    }
