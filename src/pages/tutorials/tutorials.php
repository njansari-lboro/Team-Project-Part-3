<?php
include_once __DIR__ . '/../database.php';

//setting action - view new or default
$task = $_GET["task"] ?? "default";

switch ($task) {
    case "new_tut":
        new_tut();
        break;
    case "view":
        view_tut();
        break;
    case "save":
        save_tut();
        break;
    case "favourite":
        update_favourite();
        break;
    case "edit_save":
        save_edit();
        break;
    default:
        if (!defined("MAIN_RAN")) {
            header("Location: ../?page=tutorials");
            die();
        }

        display_default();
}
//first page shown
function display_default()
{
?>

    <!DOCTYPE html>

    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="stylesheet" href="tutorials/tutorials.css">
        <script src="tutorials/tutorials.js" defer></script>

        <title>Make-It-All!</title>
    </head>

    <body class="customBody">
        <div class="search-filter-container">

            <div class="dropdown">
                <select id="tutorialFilter">
                    <option value="all">All Tutorials</option>
                    <option value="favorites">Favourited Tutorials</option>
                    <option value="mine">My Tutorials</option>
                </select>
            </div>

            <div class="search-bar">
                <form action="" method="">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search tutorials...">
                        <span class="clear-search">×</span>
                    </div>
                </form>
            </div>
        </div>

        <div class="tutHeaderSection">
            <h1 class="tutHeader">Technical Information</h1>

            <?php if ($_SESSION["user"]->role != "Employee") { ?>
                <a href="?page=tutorials&task=new_tut&technical=1" class="plus-icon-link">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 11H13V5C13 4.45 12.55 4 12 4C11.45 4 11 4.45 11 5V11H5C4.45 11 4 11.45 4 12C4 12.55 4.45 13 5 13H11V19C11 19.55 11.45 20 12 20C12.55 20 13 19.55 13 19V13H19C19.55 13 20 12.55 20 12C20 11.45 19.55 11 19 11Z" />
                    </svg>
                </a>
            <?php } ?>
        </div>
        <div id="technicalTutorials">
            <?php $is_technical = 1; ?>
            <?php include "tutorials/dynamic-carousel.php" ?>
        </div>
        <br>

        <div class="tutHeaderSection">
            <h1 class="tutHeader">Non-Technical Information</h1>

            <a href="?page=tutorials&task=new_tut&technical=0" class="plus-icon-link">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 11H13V5C13 4.45 12.55 4 12 4C11.45 4 11 4.45 11 5V11H5C4.45 11 4 11.45 4 12C4 12.55 4.45 13 5 13H11V19C11 19.55 11.45 20 12 20C12.55 20 13 19.55 13 19V13H19C19.55 13 20 12.55 20 12C20 11.45 19.55 11 19 11Z" />
                </svg>
            </a>
        </div>
        <div id="nonTechnicalTutorials">
            <?php $is_technical = 0; ?>
            <?php include("tutorials/dynamic-carousel.php") ?>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script>
            // filtering functionality - sends the type of filter selected via ajax to the dynamic carousel so sql can be adjusted
            $(document).ready(function() {
                $('#tutorialFilter').change(function() {
                    var filter = $(this).val();
                    updateTutorialsDisplay(filter);
                });

                function updateTutorialsDisplay(filter) {
                    console.log(filter);
                    $('#technicalTutorials').load('tutorials/dynamic-carousel.php', {
                        is_technical: 1,
                        filter: filter
                    }, function() {
                        reinitializeSliderState();
                    });
                    $('#nonTechnicalTutorials').load('tutorials/dynamic-carousel.php', {
                        is_technical: 0,
                        filter: filter
                    }, function() {
                        reinitializeSliderState();
                    });
                }
            });
        </script>
    </body>

    </html>

<?php
}
//viewing individual tutorial
function view_tut()
{
    if (!isset($_GET["id"])) {
        die("No tutorial ID provided.");
    }

    if (!connect_to_database()) {
        die("Error: Unable to connect to the database.");
    }
    global $mysqli;


    $tutorialId = intval($_GET["id"]);


    $tutorial = get_record_sql("select * from tutorial where id=" . $tutorialId);
    // print_r($tutorial);
    $steps = get_records_sql("select * from step where tutorial_id =" . $tutorialId);
    // print_r($steps);
    $owner = get_record_sql("select * from user where id=" . $tutorial['owner_id']);

    $userId = $_SESSION['user']->id;
    $sql = "SELECT * FROM user_tutorial_favourite WHERE user_id = $userId AND tutorial_id = $tutorialId";
    $isfavourited = get_record_sql($sql);
    // getting tutorial data
?>

    <!DOCTYPE html>

    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="tutorials/tutorials.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>

        <title>View Tutorial</title>
    </head>

    <body>
        <div class="title-with-back">
            <a href="?page=tutorials" class="back-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?php
            if ($_SESSION['user']->id == $tutorial['owner_id']) {
            ?>
                <div class="back-icon-spacer"></div>
            <?php
            }
            ?>
            <span id="tutname"><?php echo $tutorial["name"]; ?></span>
            <input type="text" id="tutnameInput" class="" value="<?php echo $tutorial["name"]; ?>" style="display: none; text-align: center;">

            <?php
            if ($_SESSION['user']->id == $tutorial['owner_id']) {
            ?>
                <!-- <a class="back-icon" href="?page=tutorials&task=edit&id=<?php echo $tutorialId; ?>"> -->
                <a id="edit-pencil" class="back-icon edit-icon" href=#>
                    <i class="fas fa-pencil-alt"></i>

                    <div id="edit-buttons-container" style="display: none;">
                </a><button type="button" id="add-step" class="">Add Step</button>
                <div class="button-stack">
                    <button style="color:green" type="button" id="save-button">
                        <i class="fas fa-check"></i>
                    </button>
                    <button style="color:red" type="button" id="cancel-button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
        </div>

    <?php
            }
    ?>
    <div class="favorite-icon">
        <i class="<?php echo $isfavourited ? 'fas fa-star gold-color' : 'far fa-star'; ?>" id="favorite-icon" style="font-size: 2.5em;"></i>
    </div>



    </div>

    <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 2em">
        <h3 id="author"><?php echo "Author: {$owner['full_name']}" ?></h3>
        <span id="tut-last-updated">Last updated <span><?php echo $tutorial['last_updated'] ?></span></span>
    </div>

    <div id="step-container" class="clearfix">
        <?php
        $stepNumber = 1;

        foreach ($steps as $step) :
        ?>

            <div class="step" data-step="<?php echo $stepNumber ?>" data-step-id=" <?php echo $step['id'] ?>">
                <div class="image-container">
                    <div class="step-counter">
                        Step <?php echo $stepNumber ?>
                    </div>

                    <?php
                    if ($_SESSION['user']->id == $tutorial['owner_id']) {
                    ?>
                        <div class="default-image-radio" style="display: none;">
                            <input type="radio" name="defaultImage" value="<?php echo $stepNumber ?>" data-tutorial-id="<?php echo $tutorialId ?>" data-img-path="<?php echo htmlspecialchars($step["image_name"]); ?>" <?php echo ($step['image_name'] == $tutorial['cover_image']) ? 'checked' : ''; ?> required>
                            <label class="radioLabel">Set as cover image</label>
                        </div>
                    <?php
                    }
                    ?>
                    <img style="width: 100%;" src="<?php echo $step["image_name"] ?>" class="step-image corner editable-image">

                    <input style="pointer-events: none;" type="file" class="edit-image" accept="image/*" disabled>

                </div>

                <p class="step-description">
                    <?php echo $step["description"] ?>
                </p>
            </div>

        <?php
            $stepNumber++;
        endforeach
        ?>
    </div>
    <script>
        // tutorial favourite functionality
        $(document).ready(function() {
            $('#favorite-icon').click(function() {
                var tutorialId = <?php echo $tutorialId; ?>;
                $.ajax({
                    type: 'POST',
                    url: 'tutorials/tutorials.php?task=favourite',
                    data: {
                        tutorial_id: tutorialId
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            var icon = $('#favorite-icon');
                            icon.removeClass('fas far').addClass('pop-animation');
                            if (response.favourited) {
                                icon.addClass('fas gold-color');
                            } else {
                                icon.addClass('far').removeClass('gold-color');
                            }
                            setTimeout(() => {
                                icon.removeClass('pop-animation');
                            }, 400);
                        } else {
                            console.log('Failed to toggle favorite');
                        }
                    },
                    error: function(response, xhr, status, error) {
                        console.log(response);
                        console.error("AJAX Error: ", status, error);
                    }
                });
            });

            const lastUpdatedText = $("#tut-last-updated span")

            const dateText = lastUpdatedText.text().trim()

            const postDate = new Date(dateText)
            lastUpdatedText.text(formatRelativeDate(postDate));
        });

        var originalDescriptions = [];
        var originalImages = [];
        var originalStepIds = [];
        //editing
        $("#edit-pencil").click(function() {
            $(".step-description").each(function() {
                originalDescriptions.push($(this).html());
            });

            $(".step").each(function() {
                var stepId = $(this).data("step-id").trim();
                if (stepId) {
                    originalStepIds.push(stepId.toString().trim());
                }
            });

            var tutnameSpan = $("#tutname");
            var tutnameInput = $("#tutnameInput");
            tutnameSpan.hide();
            tutnameInput.show();
            tutnameInput.val(tutnameSpan.text().trim());

            $(".step-image").each(function() {
                originalImages.push($(this).attr("src"));
            });

            $(".default-image-radio").show();
            $("#edit-buttons-container").show();

            $(".step-description").each(function() {
                var text = $(this).text().trim();
                $(this).html('<textarea class="form-control1">' + text + '</textarea>');
            });

            $(".edit-image").show();

            $(".edit-image").prop("disabled", false);
            $(".edit-image").css("cursor", "pointer").css("pointer-events", "auto");

            $(".step[data-step]").each(function() {
                $(this).append('<button type="button" class="remove-step-btn">Remove Step</button>');
            });
            $(this).hide();
        });
        $("#step-container").on("change", ".edit-image", function() {
            var inputElement = $(this);
            var imageElement = inputElement.siblings(".editable-image")[0];

            if (inputElement[0].files && inputElement[0].files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    imageElement.src = e.target.result;
                };
                reader.readAsDataURL(inputElement[0].files[0]);
            }
        });

        function checkSteps() {
            let stepCount = $("#step-container .step").length;

            if (stepCount < 1) {
                $("#submitBtn").hide();
            } else {
                $("#submitBtn").show();
            }
        }

        $("#add-step").click(checkSteps);

        $("#step-container").on("click", ".remove-step-btn", function() {
            $(this).closest(".step").remove();

            $(".step").each((index, element) => {
                const stepNum = index + 1;
                $(element).attr("data-step", stepNum).find(".step-counter").text("Step " + stepNum);
            });

            checkSteps();
        });


        checkSteps();
        $("#step-container").on("change", "input[type='file']", function() {
            console.log("uploaded");

            let file = this.files[0];

            if (file) {
                let ext = file.name.split(".").pop().toLowerCase();

                if (["gif", "png", "jpeg", "jpg"].includes(ext)) {
                    let reader = new FileReader();
                    let imgElement = $(this).siblings("picture").find("img.placeholder");

                    reader.onload = function(e) {
                        imgElement.attr("src", e.target.result);
                        imgElement.siblings("source").attr("srcset", e.target.result);
                    }

                    reader.readAsDataURL(file);
                } else {
                    $(this).siblings("picture").find("img.placeholder").attr("src", "../img/placeholder.jpg");
                    $(this).siblings("picture").find("source").attr("srcset", "../img/placeholderDARK.jpg");
                }
            }
        });
        //adding step
        $("#add-step").click(() => {
            var tutorialId = <?php echo $tutorialId; ?>;
            let stepCount = $("#step-container .step").length + 1;
            //this html is added to the dom
            let stepTemplate = `
                    <div class="step" data-step="${stepCount}">
                        <div class="image-container">
                            <div class="step-counter">Step ${stepCount}</div>

                            <div class="default-image-radio">
                                <input type="radio" name="defaultImage" value="step${stepCount}" data-img-path="<?php echo htmlspecialchars($step["image_name"]); ?>" data-tutorial-id="${tutorialId}" required>
                                <label class="radioLabel">Set as cover image</label>
                            </div>

                            <img style="width: 100%;" src="../img/placeholder.jpg" alt="Placeholder" class="step-image corner editable-image">

                            <input type="file" class="edit-image" accept="image/*" name="step${stepCount}-image" required>
                        </div>
                        <p class="step-description">
                        <textarea placeholder="Enter step description" name="step${stepCount}-text" class="form-control1" required></textarea>
                        </p>

                        <button type="button" class="btn btn-danger remove-step-btn">Remove Step</button>
                    </div>
                    `;

            $("#step-container").append(stepTemplate);
        });

        //saving
        $("#save-button").click(function() {

            if ($("input[name='defaultImage']:checked").length === 0) {
                alert("Please select a cover image.");
                return;
            }

            var remainingStepIds = [];
            $(".step").each(function() {
                var stepId = $(this).data("step-id");
                if (stepId) {
                    remainingStepIds.push(String(stepId).trim());
                }
            });



            $("#edit-buttons-container").hide();
            $("#edit-pencil").show();
            console.log($("#tutnameInput").val().trim());
            var tutorialData = {
                tutorial_name: $("#tutnameInput").val().trim(),
                owner_id: <?php echo $tutorial['owner_id']; ?>,
                cover_image: $("input[name='defaultImage']:checked").data('img-path'),
                is_technical: <?php echo $tutorial['is_technical']; ?>
            };


            var stepData = [];

            $(".step").each(function(index) {
                var stepNum = $(this).data("step");
                var stepId = $(this).data("step-id");
                var image = $(this).find(".editable-image").attr("src");
                var descriptionElement = $(this).find(".step-description textarea");
                var description = descriptionElement.length > 0 ? descriptionElement.val().trim() : $(this).find(".step-description").text().trim();
                console.log("Step Num" + stepNum + "Step ID: " + stepId + ", Description: " + description);
                stepData.push({
                    step_id: stepId,
                    tutorial_id: <?php echo $tutorialId; ?>,
                    step_image: image,
                    step_description: description
                });
            });
            //WHY DELETE?!
            var postData = {
                tutorial: tutorialData,
                steps: stepData,
                originalStepIds: originalStepIds,
                remainingStepIds: remainingStepIds
            };
            console.log(postData);

            $.ajax({
                type: "POST",
                url: "tutorials/tutorials.php?task=edit_save",
                data: JSON.stringify(postData),
                contentType: "application/json",
                success: function(response) {
                    console.log("Data saved successfully: " + response);
                    var tutnameInput = $("#tutnameInput");
                    var tutnameSpan = $("#tutname");
                    tutnameSpan.text(tutnameInput.val());
                    tutnameInput.hide();
                    tutnameSpan.show();
                    $(".default-image-radio").hide();
                    $(".edit-image").prop("disabled", true);
                    $(".edit-image").css("cursor", "default").css("pointer-events", "none");
                    $(".remove-step-btn").remove();
                    $(".step-description").each(function(index) {
                        $(this).html(originalDescriptions[index]);
                    });

                },
                error: function(xhr, status, error) {
                    console.error("Error saving data: " + error);
                }
            });
        });
        const originalStepCount = $(".step").length;
        //cancelling
        $("#cancel-button").click(function() {
            $(".step-description").each(function(index) {
                $(this).html(originalDescriptions[index]);
            });

            $(".editable-image").each(function(index) {
                var originalSrc = originalImages[index];
                var imgElement = $(this);

                imgElement.attr("src", originalSrc);

                var inputElement = imgElement.siblings("input[type='file']");
                if (inputElement.length > 0) {
                    inputElement.val("");
                }
            });
            $(".default-image-radio").hide();
            $(".edit-image").prop("disabled", true);
            $(".edit-image").css("cursor", "default").css("pointer-events", "none");
            $(".step[data-step]").each(function(index) {
                if (index >= originalStepCount) {
                    $(this).remove();
                }
            });

            $("#tutnameInput").hide();
            $("#tutname").show();
            $("#edit-buttons-container").hide();
            $(".remove-step-btn").remove();
            $("#edit-pencil").show();
        });
    </script>
    </body>

    </html>

<?php
}
// php saving functionality called by ajax
function save_edit()
{
    session_start();
    if (!connect_to_database()) {
        die("Error: Unable to connect to the database.");
    }
    global $mysqli;
    $data = json_decode(file_get_contents('php://input'), true);
    $tutorialData = [];
    $stepData = [];
    $postData = $_POST;
    $tutorialData = $data['tutorial'];
    $stepData = $data['steps'];
    $originalStepIds = $data['originalStepIds'];
    $remainingStepIds = $data['remainingStepIds'];
    $stepsToRemove = array_diff($originalStepIds, $remainingStepIds);
    foreach ($stepsToRemove as $stepIdToRemove) {
        delete_record('step', $stepIdToRemove);
    }


    $tutorialName = $tutorialData['tutorial_name'];
    $ownerId = $tutorialData['owner_id'];
    $coverImage = $tutorialData['cover_image'];
    $isTechnical = $tutorialData['is_technical'];
    $tutorialData = [
        'name' => $tutorialName,
        'owner_id' => $ownerId,
        'cover_image' => $coverImage,
        'is_technical' => $isTechnical,
    ];
    $stepData = array();

    if (isset($data['steps']) && is_array($data['steps'])) {
        foreach ($data['steps'] as $step) {
            $tutorialId = $step['tutorial_id'];
            $stepImage = $step['step_image'];
            $stepDescription = $step['step_description'];
            if (strpos($stepImage, 'data:image') === 0) {
                $imagePath = convert_img($stepImage);
                if ($imagePath !== false) {
                    $stepImage = $imagePath;
                } else {
                    $stepImage = '../img/placeholder.jpg';
                }
            }

            $stepData = [
                'tutorial_id' => $tutorialId,
                'image_name' => $stepImage,
                'description' => $stepDescription,
            ];
            // print_r(($stepData));
            savepost('step', $stepData, $step['step_id']);
        }
    }
    $ok = savepost('tutorial', $tutorialData, $step['tutorial_id']);
    if (is_numeric($ok)) {
        echo 'success';
    }
}
// upload to uploads folder
function convert_img($base64Image)
{
    list($type, $data) = explode(';', $base64Image);
    list(, $data)      = explode(',', $data);
    $imageData = base64_decode($data);

    $imageName = time() . '_image.png';
    $targetDir = "../../uploads/";
    $targetFile = $targetDir . $imageName;
    $webPath = '../uploads/' . $imageName;

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $success = file_put_contents($targetFile, $imageData);

    if ($success !== false) {
        return $webPath;
    } else {
        return false;
    }
}
// new tutorial screen
function new_tut()
{


?>

    <!DOCTYPE html>

    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="tutorials/tutorials.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <title>Tutorial Step Addition</title>
    </head>

    <body>
        <div class="">
            <?php $title = isset($_GET["technical"]) && $_GET["technical"] ? "Technical" : "Non-Technical" ?>
            <div class="title-with-back">
                <a href="?page=tutorials" class="back-icon">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="tutHeader" style="margin-bottom: 2rem">
                    Make <?php echo $title ?> Tutorial
                </h2>
                <div class="back-icon-spacer"></div>
            </div>


            <form class="txtform" id="tutorial-form" action="?page=tutorials" method="post" enctype="multipart/form-data">
                <input type="hidden" name="isTechnical" value="<?php echo isset($_GET["technical"]) && $_GET["technical"] ? 1 : 0; ?>">
                <input type="text" name="tutorialTitle" id="tutorialTitle" placeholder="Enter Tutorial Title" required>

                <div id="step-container" class="clearfix">
                    <div class="step" data-step="1">
                        <div class="image-container">
                            <div class="step-counter">Step 1</div>

                            <div class="default-image-radio">
                                <input type="radio" name="coverImage" value="step1" required>
                                <label class="radioLabel">Set as cover image</label>
                            </div>

                            <img src="../img/placeholder.jpg" alt="Placeholder" onChange="readURL(this)" class="placeholder" id="img1">

                            <picture>
                                <source srcset="../img/placeholderDARK.jpg" media="(prefers-color-scheme: dark)">
                                <img src="../img/placeholder.jpg" alt="Placeholder" class="placeholder">
                            </picture>

                            <input type="file" name="step1-image" required>
                        </div>

                        <textarea placeholder="Enter step description" name="step1-text" rows="4" class="form-control1" required></textarea>

                        <button type="button" class="remove-step-btn">Remove Step</button>
                    </div>
                </div>



                <button type="button" id="add-step" class="">Add Step</button>

                <input id="submitBtn" type="submit" value="Post Tutorial!" class="">
            </form>
        </div>

        <script>
            $(() => {
                $("#tutorial-form").submit(function(e) {
                    e.preventDefault();

                    var formData = new FormData(this);
                    console.log(formData);
                    $.ajax({
                        type: 'POST',
                        url: 'tutorials/tutorials.php?task=save',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // alert('tut made baby');
                                console.log('SUCCESS');
                                window.location.href = '?page=tutorials';
                            } else {
                                console.log('FAILURE');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", status, error);
                        }
                    });
                });



                $("#add-step").click(() => {
                    let stepCount = $("#step-container .step").length + 1;

                    let stepTemplate = `
                    <div class="step" data-step="${stepCount}">
                        <div class="image-container">
                            <div class="step-counter">Step ${stepCount}</div>

                            <div class="default-image-radio">
                                <input type="radio" name="coverImage" value="step${stepCount}" required>
                                <label class="radioLabel">Set as cover image</label>
                            </div>

                            <img src="../img/placeholder.jpg" alt="Placeholder" class="placeholder">

                            <picture>
                                <source srcset="../img/placeholderDARK.jpg" media="(prefers-color-scheme: dark)">
                                <img src="../img/placeholder.jpg" alt="Placeholder" class="placeholder">
                            </picture>

                            <input type="file" name="step${stepCount}-image" required>
                        </div>

                        <textarea placeholder="Enter step description" name="step${stepCount}-text" rows="4" class="form-control1" required></textarea>

                        <button type="button" class="btn btn-danger remove-step-btn">Remove Step</button>
                    </div>
                    `;

                    $("#step-container").append(stepTemplate);
                });

                $("#step-container").on("change", "input[type='file']", function() {
                    console.log("uploaded");

                    let file = this.files[0];

                    if (file) {
                        let ext = file.name.split(".").pop().toLowerCase();

                        if (["gif", "png", "jpeg", "jpg"].includes(ext)) {
                            let reader = new FileReader();
                            let imgElement = $(this).siblings("picture").find("img.placeholder");

                            reader.onload = function(e) {
                                imgElement.attr("src", e.target.result);
                                imgElement.siblings("source").attr("srcset", e.target.result);
                            }

                            reader.readAsDataURL(file);
                        } else {
                            $(this).siblings("picture").find("img.placeholder").attr("src", "../img/placeholder.jpg");
                            $(this).siblings("picture").find("source").attr("srcset", "../img/placeholderDARK.jpg");
                        }
                    }
                });

                function checkSteps() {
                    let stepCount = $("#step-container .step").length;

                    if (stepCount < 1) {
                        $("#submitBtn").hide();
                    } else {
                        $("#submitBtn").show();
                    }
                }

                $("#add-step").click(checkSteps);

                $("#step-container").on("click", ".remove-step-btn", function() {
                    $(this).closest(".step").remove();

                    $(".step").each((index, element) => {
                        const stepNum = index + 1;
                        $(element).attr("data-step", stepNum).find(".step-counter").text("Step " + stepNum);
                    });

                    checkSteps();
                });

                checkSteps();
            });
        </script>
    </body>

    </html>

<?php }
// saving tutorial and steps to respective tables
function save_tut()
{
    session_start();
    if (!connect_to_database()) {
        die("Error: Unable to connect to database.");
    }
    global $mysqli;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tutorialTitle = $_POST['tutorialTitle'];
        $isTechnical = $_POST['isTechnical'];
        $tutorialData = [
            'name' => $tutorialTitle,
            'owner_id' => $_SESSION['user']->id,
            'is_technical' => $isTechnical,
            'cover_image' => ''
        ];
        $tutorialId = savepost('tutorial', $tutorialData);

        if ($tutorialId) {
            $coverImageStep = $_POST['coverImage'];
            foreach ($_FILES as $key => $file) {
                preg_match('/step(\d+)-image/', $key, $matches);
                $stepNumber = $matches[1];


                $imageName = time() . '_' . basename($file["name"]);
                $target_file = "../../uploads/" . $imageName;
                $cover_image = '../uploads/' . $imageName;

                if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    $stepText = $_POST["step{$stepNumber}-text"];
                    $stepData = [
                        'tutorial_id' => $tutorialId,
                        'step_number' => $stepNumber,
                        'image_name' => $cover_image,
                        'description' => $stepText
                    ];
                    savepost('step', $stepData);

                    if ($key == "{$coverImageStep}-image") {
                        $tutorialData['cover_image'] = $target_file;


                        updateTutorialCoverImage($tutorialId, $cover_image);
                    }
                }
            }


            echo json_encode(['success' => true, 'message' => 'Tutorial saved successfully']);
        } else {

            echo json_encode(['success' => false, 'message' => 'Failed to save tutorial']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
    exit;
}

function updateTutorialCoverImage($tutorialId, $coverImagePath)
{
    savepost('tutorial', ['cover_image' => $coverImagePath], $tutorialId);
}

function update_favourite()
{
    header('Content-Type: application/json');
    session_start();
    if (!connect_to_database()) {
        die("Error: Unable to connect to database.");
    }
    global $mysqli;
    $favouriteStatus = false;
    if (isset($_POST['tutorial_id']) && isset($_SESSION['user']->id)) {
        $tutorialId = intval($_POST['tutorial_id']);
        $userId = $_SESSION['user']->id;

        $existingFavourite = get_record_params('user_tutorial_favourite', ['tutorial_id' => $tutorialId, 'user_id' => $userId]);
        if ($existingFavourite) {

            $mysqli->query("DELETE FROM user_tutorial_favourite WHERE user_id = $userId AND tutorial_id = $tutorialId");
            $favouriteStatus = false;
        } else {
            $favouritedata = [
                'user_id' => $userId,
                'tutorial_id' => $tutorialId
            ];
            savepost('user_tutorial_favourite', $favouritedata);
            $favouriteStatus = true;
        }

        echo json_encode(['success' => true, 'favourited' => $favouriteStatus, "data" => $existingFavourite]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }
}
