<?php
    $is_admin_or_manager = in_array($_SESSION["user"]->role, ["Admin", "Manager"]);
?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="forums/forums.css">

        <title>Forums</title>
    </head>

    <body>
        <main>
            <h1>
                <span class="dropdown-wrapper">
                    Topics -
                    <select id="topic-dropdown" onchange="location = this.value;">
                        <option value="?page=forums&topic=all">All</option>
                        <?php foreach($forum_topics as $topic): ?>
                            <option value="?page=forums&topic=<?php echo $topic["id"] ?>">
                                <?php echo htmlspecialchars($topic["name"]) ?>
                            </option>
                        <?php endforeach ?>
                        <option value="?page=forums&topic=favourites">Favourites</option>
                    </select>
                </span>
    
                <!-- Form for adding a new topic, visible only to admin and managers. -->
                <?php if ($is_admin_or_manager): ?>
                    <form action="?page=forums&task=add_topic" method="post" id="add-topic-form">
                        <input type="text" name="topic_name" id="topic_name" placeholder="New topic name" oninput="checkTopicInput()" required>
                        <input type="submit" id="add-topic-button" value="Add Topic" disabled>
                    </form>
                <?php endif ?>

                <button id="post-topic">
                    <load-svg src="../assets/add-icon.svg">
                        <style shadowRoot>
                            svg {
                                width: 30px;
                                height: 30px;
                            }

                            .fill {
                                fill: var(--icon-color);
                            }
                        </style>
                    </load-svg>
                </button>
            </h1>

            <!-- Search form for forum posts. It retains the search query after submission for easy modification and uses a clear button (×) for resetting the field. -->
            <form action="?page=forums&task=search" method="get" class="search-bar">
                <input type="text" name="search_query" id="search_query" value="<?php echo $_GET["search_query"] ?? "" ?>" placeholder="Search posts" aria-label="Search posts" oninput="checkSearchInput()">
                <span class="clear-search">×</span>
                <button type="submit" id="search-button">Search</button>
            </form>

            <div id="forum-posts-container">
                <?php if (isset($search_results) && count($search_results) > 0): ?>
                    <?php foreach ($search_results as $topic): ?>
                        <article class="forum-topic">
                            <div class="topic-content">
                                <a href="?page=forums&task=view&id=<?php echo $topic["id"] ?>" class="read-link">
                                    <h2>
                                        <span class="topic-label"><?php echo htmlspecialchars($topic["topic_name"]) ?> | <span class="question-title"><?php echo htmlspecialchars($topic["title"]) ?></span></span>
                                    </h2>
                                </a>

                                <p><?php echo htmlspecialchars($topic["description"]) ?></p>

                                <div class="post-author-container">
                                    <?php $user_pfp_path = get_user_pfp_from_id($topic["author_id"]) ?>

                                    <?php if ($user_pfp_path): ?>
                                        <img class="topic-avatar" src="<?php echo $user_pfp_path ?>" alt="User profile image">
                                    <?php else: ?>
                                        <load-svg class="topic-avatar" src="../assets/profile-icon.svg">
                                            <style shadowRoot>
                                                svg {
                                                    width: 25px;
                                                    height: 25px;
                                                }
                                                .fill {
                                                    fill: var(--label-color);
                                                }
                                            </style>
                                        </load-svg>
                                    <?php endif ?>

                                    <p class="post-author">
                                        <?php echo htmlspecialchars($topic["author_name"]) ?>
                                        <span class="post-last-updated">&nbsp;<?php echo $topic["last_updated"] ?></span>
                                    </p>
                                </div>

                                <div class="modify-post-btns">
                                    <?php $is_author = ($_SESSION["user"]->id === $topic["author_id"]) ?>

                                    <?php if ($is_author): ?>
                                        <a href="?page=forums&task=edit&edit=true&post_id=<?php echo $topic["id"] ?>" class="edit-btn">Edit</a>
                                    <?php endif ?>

                                    <?php if ($is_admin_or_manager || $is_author): ?>
                                        <a href="?page=forums&task=delete&post_id=<?php echo $topic["id"] ?>" class="delete-btn">Delete</a>
                                    <?php endif ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach ?>
                <?php elseif (isset($topics) && count($topics) > 0): ?>
                    <?php foreach ($topics as $topic): ?>
                        <article class="forum-topic">
                            <div class="topic-content">
                                <a href="?page=forums&task=view&id=<?php echo $topic["id"] ?>" class="read-link">
                                    <h2>
                                        <span class="topic-label"><?php echo htmlspecialchars($topic["topic_name"]) ?> | <span class="question-title"><?php echo htmlspecialchars($topic["title"]); ?></span></span>
                                    </h2>
                                </a>

                                <p><?php echo htmlspecialchars($topic["description"]) ?></p>

                                <div class="post-author-container">
                                    <?php $user_pfp_path = get_user_pfp_from_id($topic["author_id"]) ?>

                                    <?php if ($user_pfp_path): ?>
                                        <img class="topic-avatar" src="<?php echo $user_pfp_path ?>" alt="User profile image">
                                    <?php else: ?>
                                        <load-svg class="topic-avatar" src="../assets/profile-icon.svg">
                                            <style shadowRoot>
                                                svg {
                                                    width: 25px;
                                                    height: 25px;
                                                }
                                                .fill {
                                                    fill: var(--label-color);
                                                }
                                            </style>
                                        </load-svg>
                                    <?php endif ?>

                                    <p class="post-author">
                                        <?php echo htmlspecialchars($topic["author_name"]) ?>
                                        &nbsp;<span class="post-last-updated"><?php echo $topic["last_updated"] ?></span>
                                    </p>
                                </div>

                                <div class="modify-post-btns">
                                    <?php $is_author = ($_SESSION["user"]->id == $topic["author_id"]) ?>

                                    <?php if ($is_author): ?>
                                        <a href="?page=forums&task=edit&edit=true&post_id=<?php echo $topic["id"] ?>" class="edit-btn">Edit</a>
                                    <?php endif ?>

                                    <?php if ($is_admin_or_manager || $is_author): ?>
                                        <a href="?page=forums&task=delete&post_id=<?php echo $topic["id"] ?>" class="delete-btn">Delete</a>
                                    <?php endif ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach ?>
                <?php else: ?>
                    <p id="no-posts-placeholder">No posts here yet</p>
                <?php endif ?>
            </div>
        </main>

        <script>
            document.getElementById("topic-dropdown").addEventListener("change", function() {
                window.location.href = this.value;
            });

            // On page load, set the selected topic in the dropdown based on URL query parameters.
            document.addEventListener("DOMContentLoaded", function() {
                const queryParams = new URLSearchParams(window.location.search);
                const selectedTopic = queryParams.get("topic");

                if (selectedTopic) {
                    const topicDropdown = document.getElementById("topic-dropdown");
                    topicDropdown.value = `?page=forums&topic=${selectedTopic}`;
                }
            });


            document.getElementById("post-topic").addEventListener("click", () => {
                window.location.href = "?page=forums&task=new"
            })


            // Listen for the DOMContentLoaded event to ensure the HTML is fully loaded before attaching event listeners.
            document.addEventListener("DOMContentLoaded", function() {
                const searchForm = document.querySelector(".search-bar")
                searchForm.addEventListener("submit", function(event) {
                    event.preventDefault();

                    const searchQuery = document.getElementById("search_query").value.trim()

                    if (searchQuery) {
                        window.location.href = "?page=forums&task=search&search_query=" + encodeURIComponent(searchQuery);
                    }
                });
            });


            // Listen for the DOMContentLoaded event to ensure the HTML is fully loaded.
            document.addEventListener("DOMContentLoaded", function() {
                const elements = document.getElementsByClassName("post-last-updated")

                for (const element of elements) {
                    const dateText = element.textContent.trim()

                    const postDate = new Date(dateText)
                    element.textContent = formatRelativeDate(postDate);
                }
            })

            let replyLinks = document.querySelectorAll(".reply");
            replyLinks.forEach((link) => {
                link.addEventListener("click", (event) => {
                    event.preventDefault();
                    window.location.href = "?page=forums&task=view";
                });
            });

            function checkTopicInput() {
                const topicName = document.getElementById("topic_name").value
                const submitButton = document.getElementById("add-topic-button")
                submitButton.disabled = topicName.trim() === ""
            }

            function checkSearchInput() {
                const searchQuery = document.getElementById("search_query").value
                document.querySelector(".clear-search").style.display = searchQuery.trim() === "" ? "none" : "block";
            }

            checkSearchInput()

            document.querySelector(".clear-search").addEventListener("click", () => {
                document.getElementById("search_query").value = ""
                window.location.href = "?page=forums"
            })

            document.getElementsByClassName("delete-btn")[0].addEventListener("click", async function(event) {
                event.preventDefault()

                if (await confirmDeletePost()) {
                    window.location.href = this.href
                }
            });

            async function confirmDeletePost() {
                let shouldDelete = false

                await showDialogAsync(
                    "Are you sure you want to delete this post?",
                    "You cannot undo this action.",
                    { title: "Delete", role: DESTRUCTIVE, action: () => shouldDelete = true }
                )

                return shouldDelete
            }
        </script>
    </body>
</html>
