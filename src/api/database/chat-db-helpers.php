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
     * @param ?string $filter_text [optional] A filter string that matches a chat based on its name.
     *
     * @return array An array of chats as objects.
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
    function fetch_chats(?string $filter_text = null): array {
        $sql = "SELECT * FROM chat WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($filter_text !== null) {
            $sql .= " AND name LIKE ?";

            $filter_text = "%$filter_text%"; // Adding wildcards for partial matching

            $types .= "s";
            $vars[] = $filter_text;
        }

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING CHATS

    /**
     * Adds a new chat to the database with the specified property values.
     *
     * @param string $name The new chat's name.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_chat("Team 12");
     * ```
     */
    function add_chat(string $name): bool {
        $sql = "INSERT INTO chat (name) VALUES (?)";
        return modify_record($sql, "s", $name);
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
    function update_chat(int $chat_id, ?string $name = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($name !== null) {
            $update_fields[] = "name = ?";
            $types .= "s";
            $vars[] = $name;
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
     * @return array An array of messages as objects.
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
        $sql = "INSERT INTO messages (chat_id, author_id, body) VALUES (?, ?, ?)";
        return modify_record($sql, "iis", $chat_id, $author_id, $body);
    }

    /**
     * Updates the specified property values of a message.
     *
     * @param int $message_id The ID of the message being updated.
     * @param ?string $body [optional] The updated body of the message.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_message(2, name: "Sup");
     * ```
     */
    function update_message(int $message_id, ?string $body = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($body !== null) {
            $update_fields[] = "body = ?";
            $types .= "s";
            $vars[] = $body;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE message SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $types .= "i";
        $vars[] = $message_id;

        return modify_record($sql, $types, ...$vars);
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
