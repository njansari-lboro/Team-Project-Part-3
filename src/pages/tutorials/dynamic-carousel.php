<?php
//    if (!defined("MAIN_RAN")) {
//        header("Location: ../?page=tutorials");
//        die();
//    }

include_once __DIR__ . '/../database.php';
@session_start();
if (!connect_to_database()) {
    die("Error: Unable to connect to database.");
}
global $mysqli;
?>
<!-- carousel module that can be reused -->
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="tutorials/tutorials.css">

    <script src="tutorials/tutorials.js" defer></script>
</head>

<body>
    <div class="row">
        <div class="header" style="display:none;">
            <h3 class="title">Title</h3>
            <div class="progress-bar"></div>
        </div>

        <div class="container1">
            <button class="handle left-handle">
                <div class="text">&lsaquo;</div>
            </button>

            <?php
            $filter = isset($_POST['filter']) ? ($_POST['filter']) : 'all';
            // echo $filter;
            $is_technical_for_sql = '0';

            if (isset($is_technical) && $is_technical == '1') {
                $is_technical_for_sql = '1';
            } elseif (isset($_POST['is_technical']) && $_POST['is_technical'] == '1') {
                $is_technical_for_sql = '1';
            }
            $query = "SELECT * FROM tutorial";
            $conditions = [];
            $conditions[] = "is_technical = $is_technical_for_sql";
            //filtering logic altering the sql
            if ($filter === 'favorites') {
                $query .= " INNER JOIN user_tutorial_favourite ON tutorial.id = user_tutorial_favourite.tutorial_id";
                $conditions[] = "user_tutorial_favourite.user_id = " . $_SESSION['user']->id;
            } elseif ($filter === 'mine') {
                $conditions[] = "owner_id = " . $_SESSION['user']->id;
            }
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(' AND ', $conditions);
            }
            // echo $query;
            $tutorials = get_records_sql($query); ?>
            <div class="slider <?php echo $is_technical ? 'technical-slider' : 'non-technical-slider'; ?>">
                <div class="image-container placeholder" style="display: none;">
                    <a class="card-link">
                        <img src="../img/placeholder.jpg" alt="No Tutorials Found">
                        <span class="tutspan">No Tutorials Found</span>
                    </a>
                </div>
                <?php if (empty($tutorials)) : ?>
                    <!-- Display this if no tutorials are found -->
                    <div class="image-container placeholder">
                        <a class="card-link">
                            <img src="../img/placeholder.jpg" alt="No Tutorials Found">
                            <span class="tutspan">No Tutorials Found</span>
                        </a>
                    </div>
                <?php else : ?>
                    <?php foreach ($tutorials as $tutorial) : ?>
                        <div class="image-container">
                            <a href="?page=tutorials&task=view&id=<?php echo $tutorial["id"] ?>" class="card-link">
                                <img src="<?php echo $tutorial["cover_image"] ?>">

                                <span class="tutspan">
                                    <?php echo $tutorial["name"] ?>
                                </span>
                            </a>
                        </div>
                    <?php endforeach ?>
                <?php endif; ?>
            </div>

            <button class="handle right-handle">
                <div class="text">&rsaquo;</div>
            </button>
        </div>
    </div>
</body>

</html>
