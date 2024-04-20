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
