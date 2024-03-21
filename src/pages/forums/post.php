<?php
//    if (!defined("MAIN_RAN")) {
//        header("Location: ../?page=forums");
//        die();
//    }
?>

<?php
    include_once(__DIR__ . "/../../database/database-connect.php");

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Determine if the user is editing an existing post and capture the post ID for editing.
    $isEditing = ($_GET["edit"] ?? null) === "true";
    $postId = $_GET["post_id"] ?? 0;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $topic = $_POST["topic"] ?? null;
        $title = $_POST["title"];
        $body = $_POST["body"];
        $author_id = $_SESSION["user"]->id;

        if ($topic && $title && $body) {
            // Update existing post if editing, else insert new post.
            if ($isEditing && $postId) {
                $stmt = $conn->prepare("UPDATE forum SET topic_id = ?, title = ?, description = ? WHERE id = ? AND author_id = ?");
                $stmt->bind_param("issii", $topic, $title, $body, $postId, $author_id);
            } else {
                $stmt = $conn->prepare("INSERT INTO forum (topic_id, title, description, author_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("issi", $topic, $title, $body, $author_id);
            }

            if ($stmt->execute()) {
                header("Location: ../?page=forums");
                exit();
            } else {
                echo "Error: $stmt->error";
            }

            $stmt->close();
        } else {
            echo "Please fill in all fields.";
        }
    }

    // Fetch existing post details for editing.
    if ($isEditing && $postId) {
        $stmt = $conn->prepare("SELECT topic_id, title, description FROM forum WHERE id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "Post not found.";
            $stmt->close();
            exit();
        }

        $stmt->close();
    }

    $forum_topics = get_forum_topics($conn);
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="forums/post.css">
    </head>

    <body>
        <div class="modal-container">
            <form action="forums/post.php<?php if ($isEditing) echo "?edit=true&post_id=$postId" ?>" method="post">
                <div class="row flex-row">
                    <button class="exit-btn" type="button">
                        <load-svg src="../assets/close-icon.svg">
                            <style shadowRoot>
                                svg {
                                    width: var(--title-1);
                                    height: var(--title-1);
                                }

                                .fill {
                                    fill: var(--secondary-label-color)
                                }
                            </style>
                        </load-svg>
                    </button>

                    <!-- Topic Dropdown -->
                    <select class="dropdown" id="topicDropdown" name="topic">
                        <option value="" disabled <?php if (!$isEditing) echo "selected" ?>>Topic</option>
                        <?php foreach ($forum_topics as $topic): ?>
                            <option value="<?php echo $topic["id"] ?>" <?php if ($isEditing && $topic["id"] == $row["topic_id"]) echo "selected"; ?>>
                                <?php echo htmlspecialchars($topic["name"]) ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                    <!-- Question Title -->
                    <input type="text" class="input" name="title" placeholder="Enter question title" value="<?php if ($isEditing) echo htmlspecialchars($row["title"]) ?>" required>
                </div>

                <div class="row">
                    <!-- Question Body -->
                    <textarea class="textarea" name="body" placeholder="Enter question body" required><?php if ($isEditing) echo htmlspecialchars($row["description"]) ?></textarea>
                </div>

                <div class="row">
                    <!-- Submit Button -->
                    <button class="post-btn" type="submit">Post</button>
                </div>
            </form>
        </div>

        <script>
            let exitButton = document.querySelector(".exit-btn");
            exitButton.addEventListener("click", () => {
                window.location.href = "?page=forums";
            });
        </script>
    </body>
</html>
