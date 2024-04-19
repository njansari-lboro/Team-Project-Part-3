<?php
    $db_name = "chat_system";
    require_once(__DIR__ . "/base-db-helpers.php");

    function get_chat(int $id): ?object {
        $sql = "SELECT * FROM chat WHERE id = :id";
        return get_record($sql, "i", $id);
    }

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
