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

    $forum_id = $_GET["forum_id"];

    $isEditing = ($_GET["edit"] ?? null) === "true";
    $commentId = $_GET["comment_id"] ?? 0;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $comment = $_POST["content"];
        $forum_id = $_POST["forum_id"] ?? 0;

        if ($comment) {
            if ($isEditing && $commentId) {
                $stmt = $conn->prepare("UPDATE comment SET content = ? WHERE id = ?");
                $stmt->bind_param("si", $comment, $commentId);
            } else {
                $owner_id = $_SESSION["user"]->id;
                $stmt = $conn->prepare("INSERT INTO comment (forum_id, owner_id, content) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $forum_id, $owner_id, $comment);
            }

            if ($stmt->execute()) {
                header("Location: ../?page=forums&task=view&id=$forum_id");
                exit();
            } else {
                echo "Error: $stmt->error";
            }

            $stmt->close();
        } else {
            echo "Please fill in the comment field.";
        }
    }

    if ($isEditing && $commentId > 0) {
        $stmt = $conn->prepare("SELECT content FROM comment WHERE id = ?");
        $stmt->bind_param("i", $commentId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
            } else {
                echo "Comment not found";
            }
        } else {
            echo "Error: $stmt->error";
        }

        $stmt->close();
    }

    $conn->close();
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="forums/reply.css">

        <title><?php echo $isEditing ? "Edit Comment" : "Reply to Post" ?></title>
    </head>

    <body>
        <div class="modal-container">
            <form action="forums/reply.php<?php if ($isEditing) echo "?edit=true&comment_id=$commentId" ?>" method="post">
                <input type="hidden" name="forum_id" value="<?php echo htmlspecialchars($forum_id) ?>">
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

                <div class="row">
                    <textarea class="textarea" name="content" placeholder="Enter your comment" required><?php if ($isEditing) echo htmlspecialchars($row["content"]) ?></textarea>
                </div>

                <div class="row">
                    <button class="post-btn" type="submit"><?php echo $isEditing ? "Edit" : "Reply" ?></button>
                </div>
            </form>
        </div>

        <script>
            let exitButton = document.querySelector(".exit-btn");
            if (exitButton) {
                exitButton.addEventListener("click", () => {
                    window.location.href = "?page=forums&task=view&id=<?php echo $forum_id ?>";
                });
            }
        </script>
    </body>
</html>
