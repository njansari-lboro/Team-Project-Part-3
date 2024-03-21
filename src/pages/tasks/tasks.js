$(() => {
  // on clicking a project title dropdown
  $(".dropdown").click(function () {
    // content = #project-n-content
    let buttonId = "#" + $(this).attr("id");
    let contentId = buttonId + "-content";

    // For testing
    // console.log(buttonId);
    // console.log(contentId);

    // toggle the relevant classes
    $(buttonId).toggleClass("rotated");
    $(contentId).slideToggle();
  });

  // inserting JQuery progressbar into <td>
  $(".progress-bar").each(function () {
    let cell = $(this).get(0);
    let progress = parseInt($(this).text(), 10);

    $(this).attr("data-value", progress + "%");

    cell.textContent = "";

    $(this).progressbar({
      value: progress,
      max: 100,
    });
  });

  // Toggles jQuery slideToggle to hide comments on open
  hideComments();
  // Adds eventListeners for the toggle complete buttons
  toggleComplete();
  // Adds event listener to the update resource hours input field
  updateResourceHours();
  // Event listener to make the ellipsis' have a toggle
  $(".items-container").on("click", ".ellipsis", addToggles);

  // Find all tasks that are complete
  let completeElements = $(".complete");
  // For each element that is complete
  completeElements.each(function () {
    // Find input elements beneath each complete element
    let input = $(this).find("input");
    let button = $(this).find("button");

    // Set readonly and disabled properties for found input elements
    input.prop("readonly", true).prop("disabled", true);
    button.prop("disabled", true);
  });

  // Run once to dropdown the first project in the tasks page
  initializeDropdown();
});

function hideComments() {
  $(".comments").hide();
}

// function that adds toggles to all the comments elements to allow jQuery slide
function addToggles() {
  const comments = $(this).closest(".item").find(".comments");
  comments.slideToggle();
}

// function that deals with the logic around toggling a task complete
function toggleComplete() {
  $(".items-container").on("click", ".tick", function () {
    // find the element that has been clicked
    let parentDiv = $(this).closest(".item");
    let taskId = parentDiv.data("task-id");

    $.ajax({
      url: "../pages/tasks/tasksfunctions.php",
      type: "POST",
      data: {
        // send relevant data
        function: 0,
        taskId: taskId.toString(),
        currentUser: currentUser,
      },
      success: function (response) {
        if (response === "Record completion successfully toggled") {
          // Update HTML once database gives response
          parentDiv.toggleClass("complete");

          // logic to deal with whether the item is complete or not
          if (parentDiv.hasClass("complete")) {
            let input = parentDiv.find("input");
            let button = parentDiv.find("button");
            input.prop("readonly", true).prop("disabled", true);
            button.prop("disabled", true);
          } else {
            let input = parentDiv.find("input");
            let button = parentDiv.find("button");
            input.prop("readonly", false).prop("disabled", false);
            button.prop("disabled", false);
          }
        } else {
          console.log(response);
          window.alert("Error in toggling completion of record");
        }
      },
      // Logging ajax errors
      error: function (xhr, status, error) {
        console.error("AJAX ERROR: ", error);
      },
    });
  });
}

// function that allows user to update resource hours
function updateResourceHours() {
  $(".update").on("click", async function () {
    // get the resource hours
    let resourceHoursInput = $(this).siblings("input").val().trim();

    // given the resource hours is not empty
    if (resourceHoursInput === "" || resourceHoursInput < 0) {
      resourceHoursInput = 0;
      $(this).siblings("input").val(resourceHoursInput);
    }

    // Rounds down to the nearest 15 mins
    if (resourceHoursInput % 0.25 !== 0) {
      amountToRound = resourceHoursInput % 0.25;
      resourceHoursInput -= amountToRound;
      $(this).siblings("input").val(resourceHoursInput);
    }

    // find the div that is associated with the click
    let parentDiv = $(this).closest(".item");
    // get the id
    let taskId = parentDiv.data("task-id");

    // variable to allow logic for future dialog boxes
    let updateTrue = false;

    // Call asynchronous dialog box to confirm if the user wants to delete the item
    // See documentation in show-dialog.js
    await showDialogAsync(
      "Are you sure you want to update?",
      "This action will save your changes",
      {
        title: "Save",
        role: "default",
        action: () => (updateTrue = true),
      },
      {
        title: "Cancel",
        role: "cancel",
        action: () => console.log("Cancel clicked"),
      }
    );

    // if user confirms they want to save the resource hours
    if (updateTrue) {
      $.ajax({
        url: "../pages/tasks/tasksfunctions.php",
        type: "POST",
        data: {
          // send relevant data
          function: 1,
          taskId: taskId.toString(),
          resourceHours: resourceHoursInput,
          currentUser: currentUser,
        },
        success: function (response) {
          if (response === "Resource hours successfully updated") {
            console.log(response);
          } else {
            console.log(response);
            window.alert("Error in updating resource hours");
          }
        },
        // Logging ajax errors
        error: function (xhr, status, error) {
          console.error("AJAX ERROR: ", error);
        },
      });
    }
  });
}

// function that drops down the first project on the page
function initializeDropdown() {
  // Find the first .dropdown element
  let firstDropdown = $(".dropdown").first();

  // Check if the first .dropdown element exists
  if (firstDropdown.length > 0) {
    // Execute the code for the first .dropdown element
    console.log(firstDropdown);

    // Find the first sibling button element and get its ID
    let firstButtonId = firstDropdown.siblings("button:first").attr("id");

    console.log(firstButtonId);

    let buttonId = "#" + firstButtonId;
    let contentId = buttonId + "-content";

    $(buttonId).toggleClass("rotated");
    $(contentId).slideToggle();
  } else {
    console.log("HI");
    // Create the element that will tell user that they have no tasks assigned
    let noTasksMessage = $("<h2>").text("No tasks currently assigned");

    // Append the element
    $(".title-div").nextAll().eq(1).after(noTasksMessage);
  }
}
