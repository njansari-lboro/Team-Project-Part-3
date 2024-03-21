<?php
    require_once(__DIR__ . "/base-db-helpers.php");

    /**
     * Fetches the user corresponding to the specified invite code from the database.
     *
     * @param string $invite_code The invite code for the user to be fetched that also must not be expired.
     *
     * @return ?object Returns the user as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $user = get_user_for_invite_code("invite_658714ba5eaac");
     * echo $user->first_name; // "John"
     * ```
     */
    function get_user_for_invite_code(string $invite_code): ?object {
        $sql = "SELECT user.* FROM user JOIN user_invite_code AS invites ON user.id = invites.user_id WHERE invites.invite_id = ? AND expiry_date > NOW()";
        return get_record($sql, "s", $invite_code);
    }

    /**
     * Invites a user to the system with the specified email address.
     *
     * @param string $user_email The email address of the user, who must not yet be registered with the system.
     * @param int $valid_hours The number of hours the invite code is valid for before expiring. The default is 48 hours.
     *
     * @return ?string Returns an invite code, or `null` if no code could be created.
     *
     * Usage example:
     * ```
     * $invite_code = invite_user("johncena@make-it-all.co.uk");
     * ```
     */
    function invite_user(string $user_email, int $valid_hours = 48): ?string {
        $user = get_user_from_email($user_email);

        if ($user && !$user->registered) {
            $sql = "INSERT INTO user_invite_code (invite_id, user_id, expiry_date) VALUES (?, ?, NOW() + INTERVAL ? HOUR)";
            $invite_code = uniqid("invite_");

            if (modify_record($sql, "sii", $invite_code, $user->id, $valid_hours)) {
                return $invite_code;
            }
        }

        return null;
    }
