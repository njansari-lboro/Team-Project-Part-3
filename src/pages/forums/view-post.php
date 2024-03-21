<?php
    if (!defined("MAIN_RAN")) {
        header("Location: ../?page=forums");
        die();
    }
?>

<?php
    include_once(__DIR__ . "/../../database/database-connect.php");

    if (empty(trim($_GET["id"]))) {
        echo "No post ID provided.";
        exit();
    }

    $id = $_GET["id"];

    // Fetch the post details including the author and topic name by post ID.
    $stmt = $conn->prepare("SELECT f.id, f.title, f.author_id, f.description, f.last_updated, u.full_name, t.name as topic_name
    FROM forum f 
    INNER JOIN user u ON f.author_id = u.id 
    INNER JOIN topic t ON f.topic_id = t.id 
    WHERE f.id = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    // Fetch all comments for the post, including the commenter's full name.
    $stmt = $conn->prepare("
    SELECT c.id, c.forum_id, c.owner_id, c.content, u.full_name, c.created
    FROM comment c
    INNER JOIN user u ON c.owner_id = u.id 
    WHERE c.forum_id = ?
    ORDER BY c.created DESC");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $comments_result = $stmt->get_result();
    $comments = $comments_result->fetch_all(MYSQLI_ASSOC);

    // Determine if the current user has admin or manager roles for conditional display of edit/delete options.
    $is_admin_or_manager = in_array($_SESSION["user"]->role, ["Admin", "Manager"]);
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="forums/view-post.css">
    </head>

    <body>
        <main>
            <div class="content-box">
                <div class="header">
                    <button class="exit-button">
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

                    <h1>
                        <span class="topic-color"><?php echo htmlspecialchars($post["topic_name"]) ?></span> |
                        <span class="question-title-color"><?php echo htmlspecialchars($post["title"]) ?></span>
                    </h1>

                    <div class="author-info">
                        <?php $user_pfp_path = get_user_pfp_from_id($post["author_id"]) ?>

                        <?php if ($user_pfp_path): ?>
                            <img class="author-avatar" src="<?php echo $user_pfp_path ?>" alt="User profile image">
                        <?php else: ?>
                            <load-svg class="author-avatar" src="../assets/profile-icon.svg">
                                <style shadowRoot>
                                    svg {
                                        width: 40px;
                                        height: 40px;
                                    }

                                    .fill {
                                        fill: var(--label-color)
                                    }
                                </style>
                            </load-svg>
                        <?php endif ?>

                        <div class="author-text">
                            <span class="author-name"><?php echo htmlspecialchars($post["full_name"]) ?></span>
                            <span class="author">Author</span>
                        </div>
                    </div>
                </div>

                <div class="post-content">
                    <p><?php echo htmlspecialchars($post["description"]) ?></p>
                </div>

                <div class="reply">
                    <?php foreach ($comments as $comment): ?>
                        <div class="reply-user">
                            <?php $user_pfp_path = get_user_pfp_from_id($comment["owner_id"]) ?>

                            <?php if ($user_pfp_path): ?>
                                <img class="user-avatar" src="<?php echo $user_pfp_path ?>" alt="User profile image">
                            <?php else: ?>
                                <load-svg class="user-avatar" src="../assets/profile-icon.svg">
                                    <style shadowRoot>
                                        svg {
                                            width: 30px;
                                            height: 30px;
                                        }

                                        .fill {
                                            fill: var(--label-color)
                                        }
                                    </style>
                                </load-svg>
                            <?php endif ?>

                            <div class="reply-user-details">
                                <span class="user-name"><?php echo htmlspecialchars($comment["full_name"]) ?></span>
                                <span class="reply-created-date"><?php echo $comment["created"] ?></span>
                            </div>
                        </div>

                        <div class="reply-content">
                            <p><?php echo htmlspecialchars($comment["content"]) ?></p>

                            <div style="display: flex; gap: 10px; margin: -10px 40px 20px">
                            <!-- Display edit and delete options only for the comment's author or admin/manager users. -->
                                <?php $is_comment_author = ($_SESSION["user"]->id === $comment["owner_id"]) ?>

                                <?php if ($is_comment_author): ?>
                                    <a href="?page=forums&task=edit_comment&edit=true&comment_id=<?php echo $comment["id"] ?>&forum_id=<?php echo $post["id"] ?>" class="edit-btn">Edit</a>
                                <?php endif ?>

                                <?php if ($is_admin_or_manager || $is_comment_author): ?>
                                    <a href="?page=forums&task=delete_comment&comment_id=<?php echo $comment["id"] ?>&forum_id=<?php echo $post["id"] ?>" class="delete-btn">Delete</a>
                                <?php endif ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <button class="button">
                <load-svg class="reply-icon" src="../assets/reply-icon.svg">
                    <style shadowRoot>
                        svg {
                            width: 16px;
                            height: 16px;
                        }

                        .fill {
                            fill: var(--label-color)
                        }
                    </style>
                </load-svg>
                Reply
            </button>

            <?php
                $userId = $_SESSION["user"]->id;

                // Prepare an SQL query to check if the forum post is already favorited by the user.
                $checkQuery = "SELECT COUNT(*) as cnt FROM user_forum_favourite WHERE user_id = ? AND forum_id = ?";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("ii", $userId, $id);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                $checkRow = $checkResult->fetch_assoc();

                // Determine if the post is a favorite (count > 0 indicates it is favorited).
                $is_favourite = $checkRow["cnt"] > 0;
            ?>

            <button id="favoriteBtn" data-post-id="<?php echo htmlspecialchars($id) ?>"><?php echo $is_favourite ? "Unfavourite" : "Favourite" ?></button>
        </main>
    </body>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let exitButton = document.querySelector(".exit-button");
            if (exitButton) {
                exitButton.addEventListener("click", () => {
                    window.location.href = "?page=forums";
                });
            }

            let replyButton = document.querySelector(".button");
            if (replyButton) {
                replyButton.addEventListener("click", () => {
                    window.location.href = "?page=forums&task=reply&forum_id=<?php echo $id ?>";
                });
            }

            const elements = document.getElementsByClassName("reply-created-date")

            for (const element of elements) {
                const dateText = element.textContent.trim()

                const postDate = new Date(dateText)
                element.textContent = formatRelativeDate(postDate);
            }
        });

        document.getElementById("favoriteBtn").addEventListener("click", function() {
            const postId = this.getAttribute("data-post-id")
            window.location.href = `?page=forums&task=favorite&post_id=${postId}`;
        });

        Array.from(document.getElementsByClassName("delete-btn")).forEach(function(element) {
            element.removeEventListener("click", confirmDeleteComment)
            element.addEventListener("click", confirmDeleteComment)
        });

        async function confirmDeleteComment(event) {
            event.preventDefault()

            let shouldDelete = false

            await showDialogAsync(
                "Are you sure you want to delete this comment?",
                "You cannot undo this action.",
                { title: "Delete", role: DESTRUCTIVE, action: () => shouldDelete = true }
            )

            if (shouldDelete) {
                window.location.href = this.href
            }
        }
    </script>
</html>
