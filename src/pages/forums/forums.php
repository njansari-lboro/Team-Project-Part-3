<?php
    /*
     * This file serves as a centralized controller for managing forum-related functionalities
     * within a web-based application. It orchestrates a variety of operations including the creation,
     * viewing, editing, and deletion of forum posts and comments, alongside managing user favorites
     * and executing search queries against forum content.Decision routing is achieved through evaluation of the 'task' parameter
     * eleveraging a switch-case mechanism to delegate specific actions.
     */

    $task = $_GET["task"] ?? "default";

    // Determine the action to take based on the 'task' query parameter.
    switch ($task) {
    case "new":
        new_post($conn);
        break;
    case "view":
        view_post($conn);
        break;
    case "reply":
        reply($conn);
        break;
    case "search":
        $search_query = $_GET["search_query"] ?? "";

        if (empty(trim($search_query))) {
            header("Location: ../?page=forums");
            exit();
        } else {
            $search_results = search_posts($conn, $search_query);
            include(__DIR__ . "/forums-display.php");
        }

        break;
    case "add_topic":
        add_topic($conn);
        break;
    case "favorite":
        favorite_post($conn);
        break;
    case "delete":
        delete_post($conn);
        break;
    case "edit":
        edit_post($conn);
        break;
    case "edit_comment":
        edit_comment($conn);
        break;
    case "delete_comment":
        delete_comment($conn);
        break;
    default:
        if (!defined("MAIN_RAN")) {
            header("Location: ../?page=forums");
            die();
        }

        $forum_topics = get_forum_topics($conn);
        display_default($conn, $forum_topics);
        break;
    }

    function view_post($conn): void {
        include(__DIR__ . "/view-post.php");
    }

    function new_post($conn) {
        include(__DIR__ . "/post.php");
    }

    function reply($conn) {
        include(__DIR__ . "/reply.php");
    }

    function get_forum_topics($conn) {
        $sql = "SELECT id, name FROM topic";
        $result = $conn->query($sql);

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            error_log("Error fetching topics: $conn->error");
            return [];
        }
    }

    // Adds or removes the specified post from the user's favorites based on its current status.
    function favorite_post($conn) {
        $postId = $_GET["post_id"] ?? 0;
        $userId = $_SESSION["user"]->id;

        if ($postId > 0) {
            $checkQuery = "SELECT COUNT(*) as cnt FROM user_forum_favourite WHERE user_id = ? AND forum_id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("ii", $userId, $postId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $checkRow = $checkResult->fetch_assoc();

            if ($checkRow["cnt"] === 0) {
                $insertQuery = "INSERT INTO user_forum_favourite (user_id, forum_id) VALUES (?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("ii", $userId, $postId);
                $insertStmt->execute();

                echo "<script>window.location.href = '?page=forums&task=view&id=$postId'</script>";
            } else {
                $deleteQuery = "DELETE FROM user_forum_favourite WHERE user_id = ? AND forum_id = ?";
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param("ii", $userId, $postId);
                $deleteStmt->execute();

                echo "<script>window.location.href = '?page=forums&task=view&id=$postId'</script>";
            }
        }

        exit();
    }

    function search_posts($conn, $search_query) {
        $search_term = "%$search_query%";

        $sql = "SELECT f.id, f.author_id, u.full_name AS author_name, t.name AS topic_name, f.title, f.description, f.last_updated 
        FROM forum f 
        JOIN user u ON f.author_id = u.id
        JOIN topic t ON f.topic_id = t.id
        WHERE f.title LIKE ? OR f.description LIKE ?
        ORDER BY f.last_updated DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    function add_topic($conn) {
        $topic_name = $_POST["topic_name"] ?? "";

        if (empty($topic_name)) {
            echo "Topic name is required.";
            return;
        }

        $stmt = $conn->prepare("INSERT INTO topic (name) VALUES (?)");
        $stmt->bind_param("s", $topic_name);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>window.location = '?page=forums'</script>";
        } else {
            echo "Failed to add topic.";
        }

        $stmt->close();
    }

    function delete_post($conn) {
        $is_admin_or_manager = in_array($_SESSION["user"]->role, ["Admin", "Manager"]);

        $postId = $_GET["post_id"] ?? 0;
        $userId = $_SESSION["user"]->id;
        $userRole = $_SESSION["user"]->role;

        if ($is_admin_or_manager || is_author($conn, $userId, $postId)) {
            $sql = "DELETE FROM forum WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $postId);

            if ($stmt->execute()) {
                echo "<script>window.location.href = '?page=forums'</script>";
            }

            $stmt->close();
        }
    }

    function is_author($conn, $userId, $postId) {
        $sql = "SELECT COUNT(*) as cnt FROM forum WHERE id = ? AND author_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $postId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row["cnt"] > 0;
    }

    function edit_post($conn) {
        $postId = $_GET["post_id"] ?? 0;
        $userId = $_SESSION["user"]->id;

        include(__DIR__ . "/post.php");

        exit();
    }

    function delete_comment($conn) {
        $commentId = $_GET["comment_id"] ?? 0;
        $forumId = $_GET["forum_id"] ?? 0;
        $userId = $_SESSION["user"]->id;
        $is_admin_or_manager = in_array($_SESSION["user"]->role, ["Admin", "Manager"]);

        if ($is_admin_or_manager || is_comment_author($conn, $userId, $commentId)) {
            $sql = "DELETE FROM comment WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $commentId);

            if ($stmt->execute()) {
                echo "<script>window.location.href = '?page=forums&task=view&id=$forumId'</script>";
            }

            $stmt->close();
        }
    }

    function is_comment_author($conn, $userId, $commentId) {
        $sql = "SELECT COUNT(*) as cnt FROM comment WHERE id = ? AND owner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $commentId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row["cnt"] > 0;
    }

    function edit_comment($conn) {
        $commentId = $_GET["comment_id"] ?? 0;
        $userId = $_SESSION['user']->id;

        include(__DIR__ . "/reply.php");

        exit();
    }

    // Displays forum topics based on the selected topic ID or user's favorites.
    function display_default($conn, $forum_topics) {
        $selectedTopicId = isset($_GET["topic"]) ? $conn->real_escape_string($_GET["topic"]) : "all";
        $userId = $_SESSION["user"]->id ?? null;

        if ($selectedTopicId === "favourites" && $userId) {
            $sql = "SELECT f.id, f.author_id, u.full_name AS author_name, t.name AS topic_name, f.title, f.description, f.last_updated 
            FROM forum f 
            JOIN user u ON f.author_id = u.id
            JOIN topic t ON f.topic_id = t.id
            JOIN user_forum_favourite uff ON f.id = uff.forum_id
            WHERE uff.user_id = '$userId'
            ORDER BY f.last_updated DESC";
        } else {
            $sql = "SELECT f.id, f.author_id, u.full_name AS author_name, t.name AS topic_name, f.title, f.description, f.last_updated	
            FROM forum f 
            JOIN user u ON f.author_id = u.id
            JOIN topic t ON f.topic_id = t.id";

            if ($selectedTopicId !== "all") {
                $sql .= " WHERE t.id = '$selectedTopicId'";
            }

            $sql .= " ORDER BY f.last_updated DESC";
        }

        $result = $conn->query($sql);

        if (!$result) {
            echo "An error occurred fetching the topics: $conn->error";
            return;
        }

        $topics = $result->fetch_all(MYSQLI_ASSOC);
        include(__DIR__ . '/forums-display.php');
    }
