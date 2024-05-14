<?php
    $db_name = "chat_system";
    require_once(__DIR__ . "/base-db-helpers.php");

    // FETCHING CHATS

    /**
     * Fetches the chat with the specified ID from the database.
     *
     * @param int $chat_id The ID of the chat to be fetched.
     *
     * @return ?object Returns the chat as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $chat = get_chat(5);
     * echo $chat->name; // "Team 12"
     * ```
     */
    function get_chat(int $chat_id): ?object {
        $sql = "SELECT * FROM chat WHERE id = ?";
        return get_record($sql, "i", $chat_id);
    }

    /**
     * Fetches the chats from the database filtered using the specified properties.
     *
     * @param ?int $user_id [optional] The ID of the user to filter by.
     * @param ?string $filter_text [optional] A filter string that matches a chat based on its name.
     *
     * @return array An array of chats as objects sorted by most recently updated.
     *
     * Usage example:
     * ```
     * $all_chats = fetch_chats();
     *
     * // Filter chats on whether their name contains "team"
     * $chats = fetch_chats(filter_text: "team");
     * echo $chats; // [(Team 12), (The A-Team)]
     * ```
     */
    function fetch_chats(?int $user_id = null, ?string $filter_text = null): array {
        $sql = "SELECT chat.* FROM chat WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($user_id !== null) {
            $sql .= " AND (chat.owner_id = ? OR EXISTS (SELECT 1 FROM chat_user cu WHERE cu.user_id = ? AND cu.chat_id = chat.id))";
            $types .= "ii";
            $vars[] = $user_id;
            $vars[] = $user_id;
        }

        if ($filter_text !== null) {
            $sql .= " AND chat.name LIKE ?";

            $filter_text = "%$filter_text%"; // Adding wildcards for partial matching

            $types .= "s";
            $vars[] = $filter_text;
        }

        $sql .= " ORDER BY chat.last_updated DESC";

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING CHATS

    /**
     * Adds a new private chat to the database with the specified property values.
     *
     * @param int $owner_id The ID of the new chat's owner.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_private_chat(10);
     * ```
     */
    function add_private_chat(int $owner_id): bool {
        $sql = "INSERT INTO chat (is_private, owner_id) VALUES (TRUE, ?)";
        return modify_record($sql, "i", $owner_id);
    }

    /**
     * Adds a new group chat to the database with the specified property values.
     *
     * @param string $name The new chat's name.
     * @param ?string $icon_name The new chat's icon name.
     * @param int $owner_id The ID of the new chat's owner.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_group_chat("Team 12", "team-12-icon.png", 10);
     * ```
     */
    function add_group_chat(string $name, ?string $icon_name = null, int $owner_id): bool {
        $sql = "INSERT INTO chat (name, is_private, icon_name, owner_id) VALUES (?, FALSE, ?, ?)";
        return modify_record($sql, "ssi", $name, $icon_name ?? "null", $owner_id);
    }

    /**
     * Updates the specified property values of a chat.
     *
     * @param int $chat_id The ID of the chat being updated.
     * @param ?string $name [optional] The updated name of the chat.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_chat(5, name: "Team Mieux");
     * ```
     */
    function update_chat(int $chat_id, ?string $name = null, ?string $icon_name = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($name !== null) {
            $update_fields[] = "name = ?";
            $types .= "s";
            $vars[] = $name;
        }

        if ($icon_name !== null) {
            $update_fields[] = "icon_name = ?";
            $types .= "s";
            $vars[] = $icon_name;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE chat SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $types .= "i";
        $vars[] = $chat_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a chat from the system with the specified ID.
     *
     * @param int $chat_id The ID of the chat to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_chat(5);
     * ```
     */
    function delete_chat(int $chat_id): bool {
        $sql = "DELETE FROM chat WHERE id = ?";
        return modify_record($sql, "i", $chat_id);
    }

    // FETCHING MESSAGES

    /**
     * Fetches the message with the specified ID from the database.
     *
     * @param int $message_id The ID of the message to be fetched.
     *
     * @return ?object Returns the message as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $message = get_message(2);
     * echo $chat->body; // "Hello there."
     * ```
     */
    function get_message(int $message_id): ?object {
        $sql = "SELECT * FROM message WHERE id = ?";
        return get_record($sql, "i", $message_id);
    }

    /**
     * Fetches the messages from the database filtered using the specified properties.
     *
     * @param ?int $chat_id [optional] The ID of the chat to filter by.
     *
     * @return array An array of messages as objects sorted by date posted.
     *
     * Usage example:
     * ```
     * $all_messages = fetch_messages();
     *
     * // Get the all messages that are in the chat with id 5
     * $messages = fetch_messages(chat_id: 5);
     * echo $messages; // [(Hi!), (Hello, there.)]
     * ```
     */
    function fetch_messages(?int $chat_id = null): array {
        $sql = "SELECT * FROM message WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($chat_id !== null) {
            $sql .= " AND chat_id = ?";
            $types .= "i";
            $vars[] = $chat_id;
        }

        $sql .= " ORDER BY date_posted";

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING MESSAGES

    /**
     * Adds a new message to the database with the specified property values.
     *
     * @param int $chat_id The ID of the chat to whom the new message belongs to.
     * @param int $author_id The ID of the user who is the new message's author.
     * @param string $body The new message's body.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_message(5, 10, "Hello, there.");
     * ```
     */
    function add_message(int $chat_id, int $author_id, string $body): bool {
        $sql = "INSERT INTO message (chat_id, author_id, body) VALUES (?, ?, ?)";
        return modify_record($sql, "iis", $chat_id, $author_id, $body);
    }

    /**
     * Deletes a message from the system with the specified ID.
     *
     * @param int $message_id The ID of the message to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_message(2);
     * ```
     */
    function delete_message(int $message_id): bool {
        $sql = "DELETE FROM message WHERE id = ?";
        return modify_record($sql, "i", $message_id);
    }

    // FETCHING CHAT USERS

    /**
     * Calculates whether the specified user is a member of the specified chat.
     *
     * @param int $user_id The ID of the user to check membership in the chat.
     * @param int $chat_id The ID of the chat to check membership for the user.
     *
     * @return bool Returns a boolean value of whether the user is a member of the chat or not.
     */
    function is_user_member_of_chat(int $user_id, int $chat_id): bool {
        $sql = "SELECT EXISTS (SELECT 1 FROM chat WHERE owner_id = ? AND id = ?) OR (SELECT 1 FROM chat_user cu WHERE cu.user_id = ? AND cu.chat_id = ?)";
        return get_record($sql, "iiii", $user_id, $chat_id, $user_id, $chat_id) !== null;
    }

    /**
     * Fetches the users in the chat with the specified ID from the database.
     *
     * @param int $chat_id The ID of the chat to fetch member users from.
     *
     * @return array An array of user IDs as objects.
     *
     * Usage example:
     * ```
     * $users = fetch_users_in_chat(2);
     * echo $users; // [(2), (3), (5), (7), (11)]
     * ```
     */
    function fetch_users_in_chat(int $chat_id): array {
        $sql = "SELECT user_id FROM chat_user WHERE chat_id = ? ORDER BY user_id";
        return fetch_records($sql, "i", $chat_id);
    }

    // MODIFYING CHAT USERS

    /**
     * Adds the specified user to the specified chats in the database.
     *
     * @param int $user_id The ID of the user to add to the chat.
     * @param int $chat_id The ID of the chat to add the user to.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_user_to_chat(10, 2);
     * ```
     */
    function add_user_to_chat(int $user_id, int $chat_id): bool {
        $sql = "INSERT INTO chat_user (user_id, chat_id) VALUES (?, ?)";
        return modify_record($sql, "ii", $user_id, $chat_id);
    }

    /**
     * Removes the specified user from the specified chat from the system.
     *
     * @param int $user_id The ID of the user to remove from the chat.
     * @param int $chat_id The ID of the chat to remove the user from.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_user_from_chat(10, 2);
     * ```
     */
    function delete_user_from_chat(int $user_id, int $chat_id): bool {
        $sql = "DELETE FROM chat_user WHERE user_id = ? AND chat_id = ?";
        return modify_record($sql, "ii", $user_id, $chat_id);
    }
