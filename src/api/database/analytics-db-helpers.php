<?php
    $db_name = "make_it_all";
    require_once(__DIR__ . "/base-db-helpers.php");


        /**
     * Fetches the projects from the database filtered using the specified properties.
     *
     * @param ?int $owner_id [optional] The ID of the project owner to filter by.
     *
     * @return array An array of projects as objects.
     *
     * Usage example:
     * ```
     * $all_projects = fetch_users();
     *
     * // Get the all projects that are owned by the user with id 10
     * $projects = fetch_users(owner_id: 10);
     * ```
     */
    function fetch_projects(?int $owner_id = null): array {
        $sql = "SELECT * FROM project WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($owner_id !== null) {
            $sql .= " AND owner_id = ?";
            $types .= "i";
            $vars[] = $owner_id;
        }

        return fetch_records($sql, $types, ...$vars);
    }


        /**
     * Fetches the project with the specified ID from the database.
     *
     * @param int $project_id The ID of the project to be fetched.
     *
     * @return ?object Returns the project as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $project = get_projecst(4);
     * echo $project->name; // "Website Design"
     * ```
     */
    function get_project(int $project_id): ?object {
        $sql = "SELECT * FROM project WHERE id = ?";
        return get_record($sql, "i", $project_id);
    }


    function get_project_tasks(int $project_id): array {
        $sql = "SELECT * FROM task WHERE project_id = ?";
        return fetch_records($sql, "i", $project_id);
    }

    function get_users(): array {
        $sql = "SELECT * from user";
        return fetch_records($sql);
    }

    function get_user_tasks(int $user_id): ?array {
        $sql = "SELECT * from task where owner_id = ?";
        return fetch_records($sql, "i", $user_id);
    }

    function get_user_projects(int $user_id): array{
        $sql = "SELECT project_id from project_team_member where user_id = ?";
        return fetch_records($sql, "i", $user_id);
    }