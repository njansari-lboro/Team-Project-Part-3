<?php
    require_once(__DIR__ . "/base-db-helpers.php");

    // FETCHING PROJECTS

    /**
     * Fetches the project with the specified ID from the database.
     *
     * @param int $project_id The ID of the project to be fetched.
     *
     * @return ?object Returns the project as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $project = get_project(4);
     * echo $project->name; // "Website Design"
     * ```
     */
    function get_project(int $project_id): ?object {
        $sql = "SELECT * FROM project WHERE id = ?";
        return get_record($sql, "i", $project_id);
    }

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

    // MODIFYING PROJECTS

    /**
     * Adds a new project to the database with the specified property values.
     *
     * @param string $name The new project's name.
     * @param int $owner_id The ID of the user who is the new project's owner.
     * @param int $lead_id The ID of the user who is the new project's lead.
     * @param string $deadline The new project's deadline, as a date in the format "YYYY-MM-DD".
     * @param float $resource_hours The new project's estimated resource hours.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_project("Website Design", 10, 8, "2023-12-15", 50.0);
     * ```
     */
    function add_project(string $name, int $owner_id, int $lead_id, string $brief, string $deadline, float $resource_hours): bool {
        $sql = "INSERT INTO project (name, owner_id, lead_id, brief, deadline, resource_hours) VALUES (?, ?, ?, ?, ?, ?)";
        return modify_record($sql, "siissd", $name, $owner_id, $lead_id, $brief, $deadline, $resource_hours);
    }

    /**
     * Updates the specified property values of a project.
     *
     * @param int $project_id The ID of the project being updated.
     * @param ?string $name [optional] The updated name of the project.
     * @param ?int $lead_id [optional] The updated user ID of the lead for the project
     * @param ?string $brief [optional] The updated brief for the project.
     * @param ?string $deadline [optional] The updated deadline for the project, as a date in the format "YYYY-MM-DD".
     * @param ?float $resource_hours [optional] The updated estimated resource hours for the project.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_project(4, name: "Website Development", deadline: "2024-02-23");
     * ```
     */
    function update_project(int $project_id, ?string $name = null, ?int $lead_id = null, ?string $brief = null, ?string $deadline = null, ?float $resource_hours = null, ?bool $is_completed = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($name !== null) {
            $update_fields[] = "name = ?";
            $types .= "s";
            $vars[] = $name;
        }

        if ($lead_id !== null) {
            $update_fields[] = "lead_id = ?";
            $types .= "i";
            $vars[] = $lead_id;
        }

        if ($brief !== null) {
            $update_fields[] = "brief = ?";
            $types .= "s";
            $vars[] = $brief;
        }

        if ($deadline !== null) {
            $update_fields[] = "deadline = ?";
            $types .= "s";
            $vars[] = $deadline;
        }

        if ($resource_hours !== null) {
            $update_fields[] = "resource_hours = ?";
            $types .= "d";
            $vars[] = $resource_hours;
        }

        if ($is_completed !== null) {
            $update_fields[] = "is_completed = ?";
            $types .= "i";
            $vars[] = $is_completed;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE project SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $types .= "i";
        $vars[] = $project_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a project from the system with the specified ID.
     *
     * @param int $project_id The ID of the project to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_project(4);
     * ```
     */
    function delete_project(int $project_id): bool {
        $sql = "DELETE FROM project WHERE id = ?";
        return modify_record($sql, "i", $project_id);
    }

    // FETCHING TASKS

    /**
     * Fetches the task with the specified id from the database.
     *
     * @param int $task_id The ID of the task to be fetched.
     *
     * @return ?object Returns the task as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $task = get_task(5);
     * echo $task->name; // "Add sidebar to mock-up"
     * ```
     */
    function get_task(int $task_id): ?object {
        $sql = "SELECT * FROM task WHERE id = ?";
        return get_record($sql, "i", $task_id);
    }

    /**
     * Fetches the tasks from the database filtered using the specified properties.
     *
     * @param ?int $project_id [optional] The ID of the project to filter by.
     * @param ?int $assigned_user_id [optional] The ID of the assigned user to filter by.
     *
     * @return array An array of tasks as objects.
     *
     * Usage example:
     * ```
     * $all_tasks = fetch_tasks();
     *
     * // Get the all tasks that are in the project with id 4 assigned to the user with id 2
     * $tasks = fetch_tasks(project_id: 4, assigned_user_id: 2);
     * ```
     */
    function fetch_tasks(?int $project_id = null, ?int $assigned_user_id = null): array {
        $sql = "SELECT * FROM task WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($project_id !== null) {
            $sql .= " AND project_id = ?";
            $types .= "i";
            $vars[] = $project_id;
        }

        if ($assigned_user_id !== null) {
            $sql .= " AND assigned_user_id = ?";
            $types .= "i";
            $vars[] = $assigned_user_id;
        }

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING TASKS

    /**
     * Adds a new task to the database with the specified property values.
     *
     * @param string $name The new task's name.
     * @param int $project_id The ID of the project the new task is a part of.
     * @param int $owner_id The ID of the user who is the new task's owner.
     * @param int $assigned_user_id The ID of the user to whom the new task is assigned to.
     * @param string $description The new task's description.
     * @param string $start_date The new task's start date, as a date in the format "YYYY-MM-DD".
     * @param string $estimated_end_date The new task's estimated end date, as a date in the format "YYYY-MM-DD".
     * @param float $resource_hours The new task's estimated resource hours.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_task("Add sidebar to mock-up", 4, 10, 8, "Add a prototype sidebar to the mock-up design file.", "2023-12-04", 2.5);
     * ```
     */
    function add_task(string $name, int $project_id, int $owner_id, int $assigned_user_id, string $description, string $start_date, string $estimated_end_date, float $resource_hours): bool {
        $sql = "INSERT INTO task (name, project_id, owner_id, assigned_user_id, description, start_date, estimated_end_date, resource_hours) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return modify_record($sql, "siiisssd", $name, $project_id, $owner_id, $assigned_user_id, $description, $start_date, $estimated_end_date, $resource_hours);
    }

    /**
     * Updates the specified property values of a task.
     *
     * @param int $task_id The ID of the task being updated.
     * @param ?string $name [optional] The updated name of the task
     * @param ?int $assigned_user_id [optional] The updated ID of the user to whom the task is assigned to.
     * @param ?string $description [optional] The updated description of the task.
     * @param ?string $start_date [optional] The updated start date for the task, as a date in the format "YYYY-MM-DD".
     * @param ?string $estimated_end_date [optional] The updated estimated end date for the task, as a date in the format "YYYY-MM-DD".
     * @param ?float $resource_hours [optional] The updated estimated resource hours for the task.
     * @param ?float $hours_employed [optional] The updated number of hours employed for the task.
     * @param ?bool $is_completed [optional] The updated value for the completion of the task.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_task(5, hours_employed: 3.0, is_completed: true);
     * ```
     */
    function update_task(int $task_id, ?string $name = null, ?int $assigned_user_id = null, ?string $description = null, ?string $start_date = null, ?string $estimated_end_date = null, ?float $resource_hours = null, ?float $hours_employed = null, ?bool $is_completed = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($name !== null) {
            $update_fields[] = "name = ?";
            $types .= "s";
            $vars[] = $name;
        }

        if ($assigned_user_id !== null) {
            $update_fields[] = "assigned_user_id = ?";
            $types .= "i";
            $vars[] = $assigned_user_id;
        }

        if ($description !== null) {
            $update_fields[] = "description = ?";
            $types .= "s";
            $vars[] = $description;
        }

        if ($start_date !== null) {
            $update_fields[] = "start_date = ?";
            $types .= "s";
            $vars[] = $start_date;
        }

        if ($estimated_end_date !== null) {
            $update_fields[] = "estimated_end_date = ?";
            $types .= "s";
            $vars[] = $estimated_end_date;
        }

        if ($resource_hours !== null) {
            $update_fields[] = "resource_hours = ?";
            $types .= "d";
            $vars[] = $resource_hours;
        }

        if ($hours_employed !== null) {
            $update_fields[] = "hours_spent = ?";
            $types .= "d";
            $vars[] = $hours_employed;
        }

        if ($is_completed !== null) {
            $update_fields[] = "is_completed = ?";
            $types .= "i";
            $vars[] = $is_completed;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE task SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $types .= "i";
        $vars[] = $task_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a task from the system with the specified ID.
     *
     * @param int $task_id The ID of the task to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_task(5);
     * ```
     */
    function delete_task(int $task_id): bool {
        $sql = "DELETE FROM task WHERE id = ?";
        return modify_record($sql, "i", $task_id);
    }

    // PROJECT TEAM MEMBERS

    /**
     * Fetches whether the specified user is a team member in the specified project.
     *
     * @param int $user_id The ID of the user to be checked whether they are a team member.
     * @param int $project_id The ID of the project whose team members are to be checked.
     *
     * @return bool Returns a boolean value of whether user is a team member in the project or not.
     *
     * Usage example:
     * ```
     * $is_member = is_user_project_team_member(15, 4);
     * ```
     *
     * @return bool
     */
    function is_user_project_team_member(int $user_id, int $project_id): bool {
        $sql = "SELECT * FROM project_team_member WHERE user_id = ? AND project_id = ?";
        return get_record($sql, "ii", $user_id, $project_id) !== null;
    }

    /**
     * Adds the specified user as a team member in the specified project.
     *
     * @param int $user_id The ID of the user to be added as a team member.
     * @param int $project_id The ID of the project to gain a team member.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_project_team_member(15, 4);
     * ```
     */
    function add_project_team_member(int $user_id, int $project_id): bool {
        $sql = "INSERT INTO project_team_member (user_id, project_id) VALUES (?, ?)";
        return modify_record($sql, "ii", $user_id, $project_id) !== null;
    }

    /**
     * Deletes the specified user from the specified project's list of team members.
     *
     * @param int $user_id The ID of the user to be removed as a team member.
     * @param int $project_id The ID of the project to lose a team member.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_project_team_member(15, 4);
     * ```
     */
    function delete_project_team_member(int $user_id, int $project_id): bool {
        $sql = "DELETE FROM project_team_member WHERE user_id = ? AND project_id = ?";
        return modify_record($sql, "ii", $user_id, $project_id) !== null;
    }

    function project_has_incomplete_tasks(int $project_id): bool {
        $sql = "SELECT * FROM task WHERE project_id = ? AND is_completed = false";
        return get_record($sql, "i", $project_id) !== null;
    }
