<?php
    require_once(__DIR__ . "/base-db-helpers.php");

    // NOTIFICATION PREFERENCES

    /**
     * Fetches the notifications preferences from the database for the specified user.
     *
     * @param int $user_id The ID of the user whose notifications preferences are to be fetched.
     *
     * @return ?object Returns the user's notifications preferences as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $notification_preferences = get_user_notification_preferences(10);
     * echo $notification_preferences->favourited_forum_posts; // true
     * ```
     */
    function get_user_notification_preferences(int $user_id): ?object {
        $sql = "SELECT * FROM user_notification_preferences WHERE user_id = ?";
        return get_record($sql, "i", $user_id);
    }

    /**
     * Updates the specified notification preferences for a user.
     *
     * @param int $user_id The ID of the user whose notification preferences are being updated.
     * @param ?bool $favourited_tutorials [optional] The updated favourited tutorial notifications preference for the user.
     * @param ?bool $favourited_forum_posts [optional] The updated favourited forum post notifications preference for the user.
     * @param ?bool $created_forum_posts [optional] The updated created forum post notifications preference for the user.
     * @param ?bool $project_tasks [optional] The updated project tasks notifications preference for the user.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_user_notification_preferences(10, favourited_tutorials: false, created_forum_posts: true);
     * ```
     */
    function update_user_notification_preferences(int $user_id, ?bool $favourited_tutorials = null, ?bool $favourited_forum_posts = null, ?bool $created_forum_posts = null, ?bool $project_tasks = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($favourited_tutorials !== null) {
            $update_fields[] = "favourited_tutorials = ?";
            $types .= "i";
            $vars[] = $favourited_tutorials;
        }

        if ($favourited_forum_posts !== null) {
            $update_fields[] = "favourited_forum_posts = ?";
            $types .= "i";
            $vars[] = $favourited_forum_posts;
        }

        if ($created_forum_posts !== null) {
            $update_fields[] = "created_forum_posts = ?";
            $types .= "i";
            $vars[] = $created_forum_posts;
        }

        if ($project_tasks !== null) {
            $update_fields[] = "project_tasks = ?";
            $types .= "i";
            $vars[] = $project_tasks;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE user_notification_preferences SET " . implode(", ", $update_fields) . " WHERE user_id = ?";
        $types .= "i";
        $vars[] = $user_id;

        return modify_record($sql, $types, ...$vars);
    }

    // NOTIFICATION MANAGEMENT

    const FAVOURITED_TUTORIALS_NOTIFICATION_TYPE = "favourited_tutorials";
    const FAVOURITED_FORUM_POSTS_NOTIFICATION_TYPE = "favourited_forum_posts";
    const CREATED_FORUM_POSTS_NOTIFICATION_TYPE = "created_forum_posts";

    /**
     * Fetches the notifications from the database for the specified user.
     *
     * @param int $user_id The ID of the user whose notifications are to be fetched.
     *
     * @return array An array of notifications as objects sorted by newest first.
     *
     * Usage example:
     * ```
     * $notifications = fetch_user_notifications(10);
     * ```
     */
    function fetch_user_notifications(int $user_id): array {
        $sql = "SELECT * FROM notification WHERE user_id = ? ORDER BY date_posted DESC";
        return fetch_records($sql, "i", $user_id);
    }

    /**
     * Fetches the total number of notifications from the database for the specified user.
     *
     * @param int $user_id The ID of the user whose notifications count is to be fetched.
     *
     * @return int Returns the total number of notifications for the user.
     *
     * Usage example:
     * ```
     * $number_of_notifications = fetch_user_notification_count(10);
     * ```
     */
    function fetch_user_notification_count(int $user_id): int {
        $sql = "SELECT * FROM notification WHERE user_id = ?";
        return fetch_records_count($sql, "i", $user_id);
    }

    /**
     * Deletes a notification from the system with the specified ID.
     *
     * @param int $notification_id The ID of the notification to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_tutorial_notification(3);
     * ```
     */
    function delete_notification(int $notification_id): bool {
        $sql = "DELETE FROM notification WHERE id = ?";
        return modify_record($sql, "i", $notification_id);
    }
