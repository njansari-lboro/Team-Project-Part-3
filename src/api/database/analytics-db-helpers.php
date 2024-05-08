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
        $return_project = get_record($sql, "i", $project_id);
        $sql = "Select Count(project_id) as overall from task where project_id = ?";
        $overall = get_record($sql, "i", $project_id);
        $return_project->overall = $overall;
        $sql = "Select Count(project_id) as completed from task where project_id = ? and is_completed = true";
        $completed = get_record($sql, "i", $project_id);
        $return_project->completed = $completed;
        $sql = "Select Count(project_id) as in_progress from task where project_id = ? and hours_spent > 0 and is_completed = false";
        $in_progress = get_record($sql, "i", $project_id);
        $return_project->in_progress = $in_progress;
        $sql = "Select Count(project_id) as not_started from task where project_id = ? and hours_spent = 0 and is_completed = false";
        $not_started = get_record($sql, "i", $project_id);
        $return_project->not_started = $not_started;
        $sql = "select datediff(deadline, curdate()) as project_due_in from project where id = ?;";
        $project_due_in = get_record($sql, "i", $project_id);
        $return_project->project_overdue = $project_due_in;
        return $return_project;
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
        $sql = "SELECT * from task where assigned_user_id = ?";
        return fetch_records($sql, "i", $user_id);
    }

    function get_user_projects(int $user_id): array{
        $sql = "SELECT project_id from project_team_member where user_id = ?";
        return fetch_records($sql, "i", $user_id);
    }

    function get_all_projects(): array{
        $sql = "SELECT name, id FROM project";
        return fetch_records($sql);
    }

    function get_user_task_stats($user_id): array{
        $return_array = array();
        $sql = "Select count(assigned_user_id) as overall from task where assigned_user_id = ?";
        $overall = get_record($sql, "i", $user_id);
        array_push($return_array, $overall);
        $sql = "Select count(assigned_user_id) as completed from task where is_completed = true and assigned_user_id = ?;";
        $completed = get_record($sql, "i", $user_id);
        array_push($return_array, $completed);
        $sql = "select count(assigned_user_id) as in_progress from task where assigned_user_id = ? and hours_spent > 0 and is_completed = false;";
        $in_progress = get_record($sql, "i", $user_id);
        array_push($return_array, $in_progress);
        $sql = "select count(assigned_user_id) as not_started from task where assigned_user_id = ? and hours_spent = 0 and is_completed = false;";
        $not_started = get_record($sql, "i", $user_id);
        array_push($return_array, $not_started);
        $return_array['id']=$user_id;
        return $return_array;
    }

    function get_user_task_count($user_id): array{
        $return_array = array();
        $sql = "Select curdate() as date";
        $current_date = get_record($sql);
        
        $parts = explode('-', $current_date->date);
        $parts[1] = $parts[1]*1;
        $max_date=date_create($parts[0]."-".$parts[1]."-1");

        $sql = "Select count(id) as completed from task where assigned_user_id = ? and date_completed > ?";
        $first_month = get_record($sql, "id", $user_id, $max_date);
        $return_array[strval($parts[1])]=$first_month;

        if ($parts[1] == 1){
            $parts[0] = $parts[0]-1;
            $parts[1] == 12;
            $min_date = date_create(($parts[0])."-12-1");
        } else{
            $parts[1] = $parts[1]-1;
            $min_date = date_create($parts[0]."-".($parts[1])."-1");
        }

        $sql = "Select count(id) as completed from task where assigned_user_id = ? and date_completed > ? and date_completed < ?";
        $next_month = get_record($sql, "idd", $user_id,$min_date, $max_date);
        $return_array[strval($parts[1])]=$next_month;

        for ($i = 0; $i<4; $i++){
            $max_date = $min_date;
            if ($parts[1] == 1){
                $parts[0] = $parts[0]-1;
                $min_date = date_create(($parts[0])."-12-1");
            } else{
                $parts[1] = $parts[1]-1;
                $min_date = date_create($parts[0]."-".($parts[1]-1)."-1");
            }
    
            $sql = "Select count(id) as completed from task where assigned_user_id = ? and date_completed > ? and date_completed < ?";
            $next_month = get_record($sql, "idd", $user_id,$min_date, $max_date);
            $return_array[strval($parts[1])]=$next_month;
        }


        return $return_array;
    }
    
    function get_all_users(): array{
        $sql = "SELECT id, full_name from user";
        return fetch_records($sql);
    }