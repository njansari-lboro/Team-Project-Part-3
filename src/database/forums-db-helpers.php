<?php
    require_once(__DIR__ . "/base-db-helpers.php");

    // FETCHING FORUM TOPICS

    /**
     * Fetches the forum topic with the specified ID from the database.
     *
     * @param int $topic_id The ID of the forum topic to be fetched.
     *
     * @return ?object Returns the forum topic as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $topic = get_forum_topic(2);
     * echo $topic->name; // "Design Software"
     * ```
     */
    function get_forum_topic(int $topic_id): ?object {
        $sql = "SELECT * FROM topic WHERE id = ?";
        return get_record($sql, "i", $topic_id);
    }

    /**
     * Fetches the forum topics from the database.
     *
     * @return array An array of forum topic as objects.
     *
     * Usage example:
     * ```
     * $all_topics = fetch_forum_topics();
     * ```
     */
    function fetch_forum_topics(): array {
        $sql = "SELECT * FROM topic";
        return fetch_records($sql);
    }

    // MODIFYING FORUM TOPICS

    /**
     * Adds a new forum topic to the database with the specified property values.
     *
     * @param string $name The new forum topic's name.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_forum_topic("Design Software");
     * ```
     */
    function add_forum_topic(string $name): bool {
        $sql = "INSERT INTO topic (name) VALUES (?)";
        return modify_record($sql, "s", $name);
    }

    /**
     * Deletes a forum topic from the system with the specified ID.
     *
     * @param int $topic_id The ID of the forum topic to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_forum_topic(2);
     * ```
     */
    function delete_forum_topic(int $topic_id): bool {
        $sql = "DELETE FROM topic WHERE id = ?";
        return modify_record($sql, "i", $topic_id);
    }

    // FETCHING FORUM POSTS

    /**
     * Fetches the forum post with the specified ID from the database.
     *
     * @param int $post_id The ID of the forum post to be fetched.
     *
     * @return ?object Returns the forum post as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $post = get_forum_post(24);
     * echo $post->title; // "I can't edit the design file"
     * ```
     */
    function get_forum_post(int $post_id): ?object {
        $sql = "SELECT * FROM forum WHERE id = ?";
        return get_record($sql, "i", $post_id);
    }

    /**
     * Fetches the forum posts from the database filtered using the specified properties.
     *
     * @param ?int $topic_id [optional] The ID of the forum topic to filter by.
     * @param ?string $filter_text [optional] A filter string that matches a forum post based on their title property.
     * @param ?int $limit [optional] The maximum number of forum posts to fetch.
     * @param ?int $offset [optional] The offset value to start fetching from.
     *
     * @return array An array of forum posts as objects.
     *
     * Usage example:
     * ```
     * $all_posts = fetch_forum_posts();
     *
     * // Get all the posts in the topic with id 2 and filter them on whether their title contains "print"
     * $print_posts = fetch_forum_posts(topic_id: 2, filter_text: "print");
     *
     * // Get the all posts from #21 to #30
     * $posts = fetch_forum_posts(limit: 10, offset: 20);
     * ```
     */
    function fetch_forum_posts(?int $topic_id = null, ?string $filter_text = null, ?int $limit = null, ?int $offset = null): array {
        $sql = "SELECT * FROM forum WHERE 1";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($topic_id !== null) {
            $sql .= " AND topic_id = ?";
            $types .= "i";
            $vars[] = $topic_id;
        }

        if ($filter_text !== null) {
            $sql .= " AND title LIKE ?";
            $types .= "s";
            $vars[] = "%$filter_text%"; // Adding wildcards for partial matching
        }

        $sql .= " ORDER BY last_updated DESC";

        if ($limit !== null) {
            if ($offset !== null) {
                $sql .= " LIMIT ?, ?";

                $types .= "i";
                $vars[] = $offset;

                $types .= "i";
                $vars[] = $limit;
            } else {
                $sql .= " LIMIT ?";
                $types .= "i";
                $vars[] = $limit;
            }
        }

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING FORUM POSTS

    /**
     * Adds a new forum post to the database with the specified property values.
     *
     * @param string $title The new forum post's title.
     * @param int $author_id The ID of user who is the new forum post's author.
     * @param int $topic_id The ID of the forum topic to whom the new forum post's belongs to.
     * @param string $description The new forum post's description.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_forum_post("I can't edit the design file", 10, 2, "When I open the design file, it lets me view it, but I cannot edit it. Any ideas why?");
     * ```
     */
    function add_forum_post(string $title, int $author_id, int $topic_id, string $description): bool {
        $sql = "INSERT INTO forum (title, author_id, topic_id, description) VALUES (?, ?, ?, ?)";
        return modify_record($sql, "siis", $title, $author_id, $topic_id, $description);
    }

    /**
     * Updates the specified property values of a forum reply.
     *
     * @param int $post_id The ID of the forum reply being updated.
     * @param ?string $name [optional] The updated name of the forum post.
     * @param ?string $description [optional] The updated description of the forum post.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_forum_post(24, name: "I can't edit the dashboard design file");
     * ```
     */
    function update_forum_post(int $post_id, ?string $name = null, ?string $description = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($name !== null) {
            $update_fields[] = "name = ?";
            $types .= "s";
            $vars[] = $name;
        }

        if ($description !== null) {
            $update_fields[] = "description = ?";
            $types .= "s";
            $vars[] = $description;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE comment SET " . implode(", ", $update_fields) . " WHERE id = ?";

        $types .= "i";
        $vars[] = $post_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a forum post from the system with the specified ID.
     *
     * @param int $post_id The ID of the forum post to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_forum_post(24);
     * ```
     */
    function delete_forum_post(int $post_id): bool {
        $sql = "DELETE FROM forum WHERE id = ?";
        return modify_record($sql, "i", $post_id);
    }

    // FETCHING FORUM REPLIES

    /**
     * Fetches the forum reply with the specified ID from the database.
     *
     * @param int $reply_id The ID of the forum reply to be fetched.
     *
     * @return ?object Returns the forum reply as an object, if found, else `null`.
     *
     * Usage example:
     * ```
     * $reply = get_forum_reply(6);
     * echo $reply->description; // "You need to be logged in to edit the file."
     * ```
     */
    function get_forum_reply(int $reply_id): ?object {
        $sql = "SELECT * FROM comment WHERE id = ?";
        return get_record($sql, "i", $reply_id);
    }

    /**
     * Fetches the forum replies from the database filtered using the specified properties.
     *
     * @param ?int $limit [optional] The maximum number of forum replies to fetch.
     * @param ?int $offset [optional] The offset value to start fetching from.
     *
     * @return array An array of forum replies as objects sorted by date posted.
     *
     * Usage example:
     * ```
     * $all_replies = fetch_forum_replies();
     *
     * // Get the all replies from #1 to #10
     * $replies = fetch_forum_replies(limit: 10);
     * ```
     */
    function fetch_forum_replies(?int $limit = null, ?int $offset = null): array {
        $sql = "SELECT * FROM comment ORDER BY created";

        // Add filters based on the specified parameters
        // Then bind parameters for filters

        $types = "";
        $vars = [];

        if ($limit !== null) {
            if ($offset !== null) {
                $sql .= " LIMIT ?, ?";

                $types .= "i";
                $vars[] = $offset;

                $types .= "i";
                $vars[] = $limit;
            } else {
                $sql .= " LIMIT ?";
                $types .= "i";
                $vars[] = $limit;
            }
        }

        return fetch_records($sql, $types, ...$vars);
    }

    // MODIFYING FORUM REPLIES

    /**
     * Adds a new forum reply to the database with the specified property values.
     *
     * @param int $post_id The ID of the forum post to whom the new forum reply was belongs to.
     * @param int $author_id The ID of the user who is the new forum reply's author.
     * @param string $description The new forum reply's description.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * add_forum_reply(24, 8, "You need to be logged in to edit the file.");
     * ```
     */
    function add_forum_reply(int $post_id, int $author_id, string $description): bool {
        $sql = "INSERT INTO comment (forum_id, owner_id, content) VALUES (?, ?, ?)";
        return modify_record($sql, "iis", $post_id, $author_id, $description);
    }

    /**
     * Updates the specified property values of a forum reply.
     *
     * @param int $reply_id The ID of the forum reply being updated.
     * @param ?string $description [optional] The updated description of the forum reply.
     * @param ?bool $is_answer [optional] The updated answer status of the forum reply.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * update_forum_reply(6, is_answer: true);
     * ```
     */
    function update_forum_reply(int $reply_id, ?string $description = null, ?bool $is_answer = null): bool {
        $update_fields = [];

        $types = "";
        $vars = [];

        if ($description !== null) {
            $update_fields[] = "description = ?";
            $types .= "s";
            $vars[] = $description;
        }

        if ($is_answer !== null) {
            $update_fields[] = "is_answer = ?";
            $types .= "i";
            $vars[] = $is_answer;
        }

        if (empty($update_fields)) return false;

        $sql = "UPDATE comment SET " . implode(", ", $update_fields) . " WHERE id = ?";

        $types .= "i";
        $vars[] = $reply_id;

        return modify_record($sql, $types, ...$vars);
    }

    /**
     * Deletes a forum reply from the system with the specified ID.
     *
     * @param int $reply_id The ID of the forum reply to be deleted.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * delete_forum_reply(6);
     * ```
     */
    function delete_forum_reply(int $reply_id): bool {
        $sql = "DELETE FROM comment WHERE id = ?";
        return modify_record($sql, "i", $reply_id);
    }

    // FETCHING USER-FAVOURITED FORUM POSTS

    /**
     * Fetches the forum posts from the database that are favourited by the user with the specified ID.
     *
     * @param int $user_id The ID of the user to fetch favourited forum posts from.
     *
     * @return array An array of forum posts as objects sorted by most recently updated.
     *
     * Usage example:
     * ```
     * $all_favourited_posts = fetch_user_favourited_forum_posts(10);
     * ```
     */
    function fetch_user_favourited_forum_posts(int $user_id): array {
        $sql = "SELECT forum.* FROM forum JOIN user_forum_favourite AS favourites ON forum.id = favourites.forum_id WHERE favourites.user_id = ? ORDER BY forum.last_updated DESC";
        return fetch_records($sql, "i", $user_id);
    }

    // MODIFYING USER-FAVOURITED FORUM POSTS

    /**
     * Favourites a forum post with the specified ID for the user with the specified ID.
     *
     * @param int $user_id The ID of the user who is favouriting the forum post.
     * @param int $post_id The ID of the forum post being favourited.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * favourite_forum_post(10, 24);
     * ```
     */
    function favourite_forum_post(int $user_id, int $post_id): bool {
        $sql = "INSERT INTO user_forum_favourite (user_id, forum_id) VALUES (?, ?)";
        return modify_record($sql, "ii", $user_id, $post_id);
    }

    /**
     * Unfavourites a forum post with the specified ID for the user with the specified ID.
     *
     * @param int $user_id The ID of the user who is unfavouriting the forum post.
     * @param int $post_id The ID of the forum post being unfavourited.
     *
     * @return bool Returns a boolean value of whether the operation was a success or not.
     *
     * Usage example:
     * ```
     * unfavourite_forum_post(10, 24);
     * ```
     */
    function unfavourite_forum_post(int $user_id, int $post_id): bool {
        $sql = "DELETE FROM user_forum_favourite WHERE user_id = ? AND forum_id = ?";
        return modify_record($sql, "ii", $user_id, $post_id);
    }
