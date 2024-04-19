<?php
    require_once(__DIR__ . "/database-connect.php");

    /*
    **** IMPORTANT INFORMATION ****

    It is imperative that if your SQL statements contain any variable input (i.e. don't use fixed values) that you do not include them directly in the query string – you run the risk of an SQL injection attack. The better and safer method is to use parameterised prepared statements. There are two stages to this: preparation and execution.

    During preparation, an SQL statement template is sent to the server. To include variable input in your statements, you can use anonymous, positional placeholders with the `?` character. For example, in this insert statement, placeholders are used where the user property values would be specified (and will be used during execution):
    ```php
    INSERT INTO users (first_name, last_name, is_technical) VALUES (?, ?, ?)
    ```

    When the statement is being executed, the client binds the parameter values and sends them to the server. The server executes the statement with the bound values and returns the result as it would with a non-parameterised standard query.

    The value for the `types` parameter is a string that contains one or more characters which specify the types for the corresponding bind variables:
        - "s": string
        - "i": integer
        - "d": float
        - "b": blob
    The number of variables and the length of the string `types` must match the parameters in the statement.

    For example, for the previous insert statement, the `types` string would be "ssi" (two strings and one integer) and the values list would be `$first_name, $last_name, $is_technical` (two strings and one integer).
    */


    /**
     * Retrieves a single record from the database based on an SQL query constructed using the provided arguments.
     *
     * @param string $sql The SQL query to execute.
     * @param string $types [optional] The types of parameters in the SQL query.
     * @param mixed ...$vars [optional] The values to bind to the SQL query parameters.
     *
     * @return ?object Returns an object representing the fetched record if found, or `null` if no record is found.
     *
     * Example usage:
     * ```
     * // Using a variable
     * $user = get_record("SELECT * FROM users WHERE id = ?", "i", $user_id);
     *
     * // Using multiple variables
     * $user = get_record("SELECT * FROM users WHERE id = ? AND role = ?", "is", $user_id, $role);
     *
     * // Using fixed values
     * $user = get_record("SELECT * FROM users WHERE id = 10");
     * ```
     */
    function get_record(string $sql, string $types = "", mixed ...$vars): ?object {
        global $conn;

        $expected_num_vars = strlen($types);

        if ($expected_num_vars != count($vars)) return null;

        if ($expected_num_vars) {
            $stmt = $conn->prepare($sql);

            if ($stmt->param_count != $expected_num_vars) return null;

            $stmt->bind_param($types, ...$vars);
            $stmt->execute();

            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }

        if ($result->num_rows) {
            // Fetch the record data as an associative array
            $record_data = $result->fetch_assoc();
            return (object) $record_data;
        } else {
            // Record not found
            return null;
        }
    }

    /**
     * Fetches multiple records from the database based on an SQL query constructed using the provided arguments.
     *
     * @param string $sql The SQL query to execute.
     * @param string $types [optional] The types of parameters in the SQL query.
     * @param mixed ...$vars [optional] The values to bind to the SQL query parameters.
     *
     * @return array An array of objects representing the fetched records.
     *
     * Example usage:
     * ```
     * // Using a variable
     * $users = fetch_records("SELECT * FROM users WHERE role = ?", "s", $role);
     *
     * // Using multiple variables
     * $users = fetch_records("SELECT * FROM users WHERE first_name = ? OR last_name = ?, "ss", $first_name, $last_name);
     *
     * // Using fixed values
     * $users = fetch_records("SELECT * FROM users WHERE is_registered = TRUE");
     * ```
     */
    function fetch_records(string $sql, string $types = "", mixed ...$vars): array {
        global $conn;

        $expected_num_vars = strlen($types);

        if ($expected_num_vars != count($vars)) return [];

        if ($expected_num_vars) {
            $stmt = $conn->prepare($sql);

            if ($stmt->param_count != $expected_num_vars) return [];

            $stmt->bind_param($types, ...$vars);
            $stmt->execute();

            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }

        $records = [];

        while ($record_data = $result->fetch_assoc()) {
            $records[] = (object) $record_data;
        }

        return $records;
    }

    /**
     * Fetches the count of records from the database based on an SQL query constructed using the provided arguments.
     *
     * @param string $sql The SQL query to execute.
     * @param string $types [optional] The types of parameters in the SQL query.
     * @param mixed ...$vars [optional] The values to bind to the SQL query parameters.
     *
     * @return int The number of records fetched by the query.
     *
     * Example usage:
     * ```
     * // Using a variable
     * $num_users = fetch_records_count("SELECT * FROM users WHERE role = ?", "s", $role);
     *
     * // Using multiple variables
     * $num_users = fetch_records_count("SELECT * FROM users WHERE first_name = ? OR last_name = ?, "ss", $first_name, $last_name);
     *
     * // Using fixed values
     * $num_users = fetch_records_count("SELECT * FROM users WHERE is_registered = TRUE");
     * ```
     */
    function fetch_records_count(string $sql, string $types = "", mixed ...$vars): int {
        global $conn;

        $expected_num_vars = strlen($types);

        if ($expected_num_vars != count($vars)) return 0;

        if ($expected_num_vars) {
            $stmt = $conn->prepare($sql);

            if ($stmt->param_count != $expected_num_vars) return 0;

            $stmt->bind_param($types, ...$vars);
            $stmt->execute();
            $stmt->store_result();

            return $stmt->num_rows;
        } else {
            $result = $conn->query($sql);
            return $result->num_rows;
        }
    }

    /**
     * Modifies a record in the database based on an SQL query constructed using the provided arguments.
     *
     * @param string $sql The SQL query to execute.
     * @param string $types [optional] The types of parameters in the SQL query.
     * @param mixed ...$vars [optional] The values to bind to the SQL query parameters.
     *
     * @return bool Returns `true` if the record modification was successful, `false` otherwise.
     *
     * Example usage:
     * ```
     * // Using a variable
     * modify_record("UPDATE users SET is_registered = TRUE WHERE id = ?", "i", $user_id);
     *
     * // Using multiple variables
     * modify_record("INSERT INTO users (first_name, last_name, email) VALUES (?, ?, ?)", "sss", $first_name, $last_name, $email);
     *
     * // Using fixed values
     * modify_record("DELETE FROM users WHERE role = 'Employee'");
     * ```
     */
    function modify_record(string $sql, string $types = "", mixed ...$vars): bool {
        global $conn;

        $expected_num_vars = strlen($types);

        if ($expected_num_vars != count($vars)) return false;

        if ($expected_num_vars) {
            $stmt = $conn->prepare($sql);

            if ($stmt->param_count != $expected_num_vars) return false;

            $stmt->bind_param($types, ...$vars);
            $result = $stmt->execute() && $stmt->affected_rows;
        } else {
            $result = $conn->query($sql) && $conn->affected_rows;
        }

        return $result;
    }
