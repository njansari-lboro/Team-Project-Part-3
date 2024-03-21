$(() => {
  // Initialise appropriate functions
  // Mostly event listeneres to buttons
  // Some jQuery functions

  // Toggles jQuery slideToggle to hide comments on open
  hideComments();
  // Adds event listener to clearList button
  clearList();
  // Adds jQuery datepicker to due date field
  datePicker();
  // Adds styling to backgrounds of priority inputs if they have had them set
  setPriorityBackgroundColours();
  // Adds eventListeners for the toggle complete buttons
  toggleComplete();
  // Adds eventListener to the add button
  addButton();
  // Adds eventListener to the delete all button
  deleteButtonAll();
  // Adds jQuery timepicker to due time field
  timePicker();
  // Add event listener to all the ellipsis buttons
  $(".items-container").on("click", ".ellipsis", addToggles);
  // Makes sure any items that are completed when loading have their fields disabled
  disableInitialCompleted();
  // Call updateTextToDisplay initially to set up the event listener with the default textToDisplay value
  updateTextToDisplay();
});

// jQuery function that sets intervals of time allowed for input
function timePicker() {
  $("input.timepicker").timepicker({
    timeFormat: "HH:mm",
    minTime: "05",
    maxTime: "20",
    disableTextInput: true,
  });
}

// function that hides the comments
function hideComments() {
  $(".comments").hide();
}

// function that finds all classes with comments and applies a jQuery slideToggle
function addToggles() {
  const comments = $(this).closest(".item").find(".comments");
  comments.slideToggle();
}

// function that adds new item to page when add button is clicked
function addButton() {
  $(".add").click(() => {
    const date = new Date();
    // Get day, month, and year components
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();

    // Format the date as "dd/mm/yyyy"
    const formattedDate = `${day}/${month}/${year}`;

    // HTML for new item to be inserted
    const newItemHtml = `
        <form class="item" data-todo-id="">
                        <div class="inputs">
                            <div class="task-name">
                                <svg xmlns="http://www.w3.org/2000/svg" class="ionicon tick" viewBox="0 0 512 512">
                                    <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M352 176L217.6 336 160 272" />
                                </svg>

                                <input type="text" placeholder="Task name" class="task-input input">

                                <svg xmlns="http://www.w3.org/2000/svg" class="ionicon ellipsis" viewBox="0 0 512 512">
                                    <circle cx="256" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                    <circle cx="416" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                    <circle cx="96" cy="256" r="32" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                </svg>
                            </div>

                            <div class="due-date">
                                <input type="text" class="due-date-input input datepicker" value="${formattedDate}">
                            </div>

                            <div class="due-time">
                                <input type="text" readonly="true" class="due-time-input input timepicker" value="09:00">
                            </div>

                            <div class="priority">
                                <select name="priority" class="priority-select priority-input input select-input">
                                    <option value="none">None</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>

                            <div class="delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="ionicon del" viewBox="0 0 512 512">
                                    <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/>
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M320 320L192 192M192 320l128-128"/>
                                </svg>
                            </div>
                        </div>

                        <div class="comments hidden">
                            <textarea name="comments-input" class="comments-input"></textarea>
                        </div>
                    </form>
        `;

    // Find the container that the content is stored inside of
    const container = $(".scrollable-content");

    // Append the newly created Html element to the container
    container.append(newItemHtml);

    // Apply the necessary event listeners and jQuery functions to the new Html element
    hideComments();
    datePicker();
    addToggles();
    deleteButton();
    timePicker();

    // Remove event listener for completion then re-assign to all
    $(".item").off("click");
    toggleComplete();
  });
}

// function that applies event listener to all delete buttons on each item
function deleteButtonAll() {
  // find all elements with the class del and apply this event listener
  $(".del").on("click", async function () {
    // get the item as a jQuery object
    const item = $(this).parent().parent().parent();
    // get the id of the item
    const todoId = $(this).parent().parent().parent().attr("data-todo-id");

    // For testing
    // console.log(todoId);
    // console.log(item);

    // Create variable to allow logic for upcoming dialog boxes
    let deleteTrue = false;

    // Call asynchronous dialog box to confirm if the user wants to delete the item
    // See documentation in show-dialog.js
    await showDialogAsync(
      "Are you sure you want to delete this item?",
      "You cannot undo this action.",
      {
        title: "Delete",
        role: DESTRUCTIVE,
        action: () => {
          deleteTrue = true;
        },
      }
    );

    // If user confirms they want to delete the item
    if (deleteTrue) {
      // Check it is not a record not yet saved by checking if id !== ""
      if (todoId !== "") {
        // For testing
        // console.log("Confirmed");
        // console.log(todoId, currentUser);

        // Delete the record using the above id and checking that current_user is the owner of the above id record using asynchronous ajax call
        $.ajax({
          url: "../pages/todo/todofunctions.php",
          type: "POST",
          data: {
            // pass the relevatn data
            function: 0,
            todoId: todoId.toString(),
            currentUser: currentUser,
          },
          success: function (response) {
            if (response === "Record successfully deleted") {
              // Update HTML once database gives response
              item.remove();
            } else {
              // Log errors in deleting the records
              console.log(response);
              window.alert("Error in deleting record");
            }
          },
          // Log any errors generated from the ajax request
          error: function (xhr, status, error) {
            console.error("AJAX ERROR: ", error);
          },
        });
      } else {
        // Just update the HTML to remove the not yet saved record from the webpage
        item.remove();
      }
    } else {
      // Testing
      console.log("Cancelled");
    }
  });
}

// function that applies event listener to dynamically added items
function deleteButton() {
  // get the last item to be added
  $(".del:last").on("click", async function () {
    // get the item as a jQuery object
    const item = $(this).parent().parent().parent();
    // get the id
    const todoId = $(this).parent().parent().parent().attr("data-todo-id");

    // Testing
    // console.log(todoId);
    // console.log(item);

    // Create variable to allow logic for upcoming dialog boxes
    let deleteTrue = false;

    // Call asynchronous dialog box to confirm if the user wants to delete the item
    // See documentation in show-dialog.js
    await showDialogAsync(
      "Are you sure you want to delete this item?",
      "You cannot undo this action.",
      {
        title: "Delete",
        role: DESTRUCTIVE,
        action: () => (deleteTrue = true),
      },
      {
        title: "Cancel",
        role: CANCEL,
        action: () => console.log("Cancel clicked"),
      }
    );

    // if user confirms that they want to delete the item
    if (deleteTrue) {
      // Check it is not a record not yet saved by checking if id !== ""
      if (todoId !== "") {
        console.log("Confirmed");
        console.log(todoId, currentUser);
        // Delete the record using the above id and checking that current_user is the owner of the above id record
        $.ajax({
          url: "../pages/todo/todofunctions.php",
          type: "POST",
          data: {
            // Send the relevant data
            function: 0,
            todoId: todoId.toString(),
            currentUser: currentUser,
          },
          success: function (response) {
            if (response === "Record successfully deleted") {
              // Update HTML once database gives response
              item.remove();
            } else {
              window.alert("Error in deleting record");
            }
          },
          // Log any errors with the ajax request
          error: function (xhr, status, error) {
            console.error("AJAX ERROR: ", error);
          },
        });
      } else {
        // Just update the HTML to remove the not yet saved record from the webpage
        item.remove();
      }
    } else {
      // Testing
      console.log("Cancelled");
    }
  });
}

// function that applies event listener to the clear list to allow user to clear whole list
function clearList() {
  // find clear list button and apply event listener
  $(".clear-list").click(async function () {
    // find all the items on the page
    let items = $(".item");

    // initialise empty array
    let todoItemsId = [];

    // loop through each element to find id and append to array
    $.each(items, function (index, element) {
      let todoId = $(element).attr("data-todo-id");

      // Check that the record is not a newly created record that has not yet been saved
      // Otherwise database tries to delete record of data id = ""
      if (todoId !== "") {
        // Push the todoId value to the todoItemsId array
        todoItemsId.push(todoId);
      }
    });

    // Create variable to allow logic for upcoming dialog boxes
    let deleteTrue = false;

    // Call asynchronous dialog box to confirm if the user wants to delete the item
    // See documentation in show-dialog.js
    await showDialogAsync(
      "Are you sure you want to clear the list?",
      "This action will remove all items from the list. You cannot undo this action.",
      {
        title: "Clear List",
        role: DESTRUCTIVE,
        action: () => (deleteTrue = true),
      },
      {
        title: "Cancel",
        role: CANCEL,
        action: () => console.log("Cancel clicked"),
      }
    );

    // If user confirms they want to delete and the number of saved toDoItems is greater than 0
    if (deleteTrue && todoItemsId.length > 0) {
      // Delete the entire todo list using the above ids and checking that current_user is the owner of the all of the above items
      $.ajax({
        url: "../pages/todo/todofunctions.php",
        type: "POST",
        data: {
          // Send relevant data
          function: 1,
          todoItemsId: todoItemsId,
          currentUser: currentUser,
        },
        success: function (response) {
          if (response === "ToDo List successfully deleted") {
            // Update HTML once database gives response
            $(".scrollable-content").empty();
          } else {
            // Log errors
            console.log(response);
            window.alert("Error in deleting records");
          }
        },
        // Log ajax errors
        error: function (xhr, status, error) {
          console.error("AJAX ERROR: ", error);
        },
      });
      // If there are no saved items then just clear html of newly added but unsaved items
    } else if (deleteTrue) {
      // Update HTML
      $(".scrollable-content").empty();
    } else {
      // Testing
      console.log("Cancelled");
    }
  });
}

// function that adds eventlistener to specific input for jQuery datePicker
function datePicker() {
  $(".items-container").on("focus", ".datepicker", function () {
    // Initialize datepicker for dynamically added elements
    $(this).datepicker({ dateFormat: "dd/mm/yy" });
  });
}

// function that sets the background colours of priority input elements
function setPriorityBackgroundColours() {
  $(".items-container").on("change", ".priority-select", function () {
    // get the selected priority
    let selectedPriority = $(this).val().toLowerCase();
    let selectedElement = $(this);

    // remove any previous styling
    selectedElement.removeClass("high low medium");

    // apply the necessary class so that the CSS styling is applied
    if (selectedPriority !== "none") {
      selectedElement.addClass(selectedPriority);
    }
  });
}

// function that adds event listener to item class and logic of clicking tick element
function toggleComplete() {
  $(".item").on("click", ".tick", async function () {
    // find the item clicked as a jQuery object
    let parentDiv = $(this).closest(".item");
    // get the id of the item
    let todoId = parentDiv.attr("data-todo-id");

    // If the item has no id and has not been saved yet
    if (todoId === "") {
      showDialog("Items must have a name and be saved before completing");
    } else {
      $.ajax({
        url: "../pages/todo/todofunctions.php",
        type: "POST",
        data: {
          // Send relevant data
          function: 2,
          todoId: todoId.toString(),
          currentUser: currentUser,
        },
        success: function (response) {
          if (response === "Record completion successfully toggled") {
            // Toggle the class complete on the div
            parentDiv.toggleClass("complete");

            // Logic to change whether the inputs should be disabled or not based on complete class
            if (parentDiv.hasClass("complete")) {
              let inputs = parentDiv.find("input, select, textarea");
              inputs.prop("readonly", true).prop("disabled", true);
            } else {
              let inputs = parentDiv.find("input, select, textarea");
              inputs.prop("readonly", false).prop("disabled", false);
            }
          } else {
            console.log(response);
            window.alert("Error in toggling completion of record");
          }
        },
        // Log ajax errors
        error: function (xhr, status, error) {
          console.error("AJAX ERROR: ", error);
        },
      });
    }
  });
}

// function to disable inputs of items completed on load
function disableInitialCompleted() {
  $(".complete").find("input, select, textarea").prop({
    readonly: true,
    disabled: true,
  });
}

// function that handles saving data logic
function setUpSaveListClickListener(textToDisplay) {
  // get the save list item and apply event listener
  $(".save-list").click(async function () {
    // get all items
    let items = $(".item");
    // New items array to store items that will need to be newly saved to the database
    let newItems = [];
    // items that already have an id so are saved items
    let savedTodoItems = [];
    // variable to deal with logic of checking items have required fields before being saved
    let requiredFieldsComplete = true;

    // loop through each item
    $.each(items, function (index, element) {
      // get the id of the item
      let todoId = $(element).attr("data-todo-id");

      // Get the name input value
      const nameInput = element.querySelector(".task-input");
      let name = nameInput.value;
      // create an error span element to display error messages
      const nameErrorSpan = document.createElement("span");
      nameErrorSpan.className = "error-message";

      // Get the description input value
      const descriptionInput = element.querySelector(".comments-input");
      const description = descriptionInput.value;

      // Get the due date input value
      const dueDateInput = element.querySelector(".due-date-input");
      // format the date to align with database standards
      let dueDate = dueDateInput.value;
      let splitDueDate = dueDate.split("/");
      dueDate = splitDueDate[2] + "-" + splitDueDate[1] + "-" + splitDueDate[0];

      // Get the due date input value
      const dueTimeInput = element.querySelector(".due-time-input");
      let dueTime = dueTimeInput.value;

      // Get the priority select value
      const prioritySelect = element.querySelector(".priority-select");
      const priority = prioritySelect.value;

      // Check that the record is not a newly created record that has not yet been saved
      // Otherwise database tries to delete record of data id = ""
      if (todoId !== "") {
        // Means we are updating a record that already exists on the database
        let recordToUpdate = [
          todoId,
          name,
          description,
          dueDate + " " + dueTime,
          priority,
        ];
        // append to the array
        savedTodoItems.push(recordToUpdate);
      } else {
        // deal with an item that currently has no name
        if (name === "") {
          // Remove any existing error message span
          const existingErrorSpan =
            nameInput.parentNode.querySelector(".error-message");
          if (existingErrorSpan) {
            nameInput.parentNode.removeChild(existingErrorSpan);
          }

          // Add red asterisk next to the name input
          nameErrorSpan.textContent = "*";
          nameInput.parentNode.appendChild(nameErrorSpan);

          // Apply error message next to save button
          // Get the parent div
          const rightButtonsDiv = document.querySelector(".right-buttons");

          // Check if there is an existing span with the class
          const existingTextSpan = rightButtonsDiv.querySelector(".error-text");

          // If an existing span is found, remove it
          if (existingTextSpan) {
            existingTextSpan.remove();
          }

          // Create a span element with the desired text
          const newText = document.createElement("span");
          newText.textContent = textToDisplay;
          newText.classList.add("error-text"); // Add a class to the span

          // Insert the span element before the first button
          rightButtonsDiv.insertBefore(newText, rightButtonsDiv.firstChild);

          // Insert the text node before the first button
          rightButtonsDiv.insertBefore(newText, rightButtonsDiv.firstChild);

          // update this variable to false now as check has found an illegal item
          requiredFieldsComplete = false;
        } else {
          // Get the parent div
          const rightButtonsDiv = document.querySelector(".right-buttons");

          // Check if there is an existing span with the class
          const existingTextSpan = rightButtonsDiv.querySelector(".error-text");

          // If an existing span is found, remove it
          if (existingTextSpan) {
            existingTextSpan.remove();
          }

          // Remove any existing error message span
          const existingErrorSpan =
            nameInput.parentNode.querySelector(".error-message");
          if (existingErrorSpan) {
            nameInput.parentNode.removeChild(existingErrorSpan);
          }

          // Checking and formatting empty inputs of certain fields
          if (dueDate === "undefined-undefined-") {
            console.log("EMPTY DATE");
            // Get today's date
            let today = new Date();

            // Format the date as "YYYY-MM-DD"
            let formattedDate =
              today.getFullYear() +
              "-" +
              ("0" + (today.getMonth() + 1)).slice(-2) +
              "-" +
              ("0" + today.getDate()).slice(-2);
            dueDate = formattedDate;
          }

          if (dueTime === "") {
            console.log("EMPTY TIME");
            dueTime = "12:00:00";
          }

          // Create new record to be saved
          let record = [name, description, dueDate + " " + dueTime, priority];

          // push the record to the appropriate array
          newItems.push(record);
        }
      }
    });

    // variable required for upcoming dialogs logic
    let saveTrue = false;

    // if there are no errors with the input then this variable will remain true
    if (requiredFieldsComplete) {
      // Call the appropriate dialog box
      await showDialogAsync(
        "Please confirm you want to save",
        "This action will save your changes.",
        {
          title: "Save",
          role: "default",
          action: () => (saveTrue = true),
        },
        {
          title: "Cancel",
          role: "cancel",
          action: () => console.log("Cancel clicked"),
        }
      );
    }

    // If user confirms they want to save
    if (saveTrue) {
      // Save the entire to do list. Update existing ones and create new records
      $.ajax({
        url: "../pages/todo/todofunctions.php",
        type: "POST",
        data: {
          function: 3,
          savedTodoItems: savedTodoItems,
          newItems: newItems,
          currentUser: currentUser,
        },
        success: async function (response) {
          console.log("RESPONSE", response);
          if (response === "ToDo List successfully saved") {
            // Get the new ids of the new items
            $.ajax({
              url: "../pages/todo/todofunctions.php",
              type: "POST",
              data: {
                function: 4,
                currentUser: currentUser,
              },
              success: async function (response) {
                // Update the id of all the new items
                for (let i = 0; i < response.length; i++) {
                  let items = $(".item");
                  let currentItem = items.eq(i);

                  // Set the data-todo-id attribute for the current item
                  currentItem.attr("data-todo-id", response[i]["id"]);
                }

                // assign toggles for each item
                $(".item").off("click");
                toggleComplete();
              },
              // Log ajax errors
              error: function (xhr, status, error) {
                console.error("AJAX ERROR: ", error);
              },
            });
          } else {
            console.log(response);
            window.alert("Error in deleting records");
          }
        },
        // Log ajax errors
        error: function (xhr, status, error) {
          console.error("AJAX ERROR: ", error);
        },
      });
    } else {
      console.log("Cancelled");
    }
  });
}

// Function to update the textToDisplay value based on screen width
function updateTextToDisplay() {
  const screenWidth = window.innerWidth;
  let textToDisplay;

  if (screenWidth < 400) {
    textToDisplay = "*";
  } else if (screenWidth < 550) {
    textToDisplay = "* Required";
  } else if (screenWidth < 750) {
    textToDisplay = "* Task Name required"; // Change textToDisplay for small screens
  } else {
    textToDisplay = "* Please insert required Task Name field"; // Default textToDisplay
  }

  // Call setupSaveListClickListener with the updated textToDisplay value
  setUpSaveListClickListener(textToDisplay);
}

// Add event listener to handle screen size changes
window.addEventListener("resize", updateTextToDisplay);

// function that changes the size of text depending on the screen size
function updateHeaderText() {
  const screenWidth = window.innerWidth;
  const headerText = document.querySelector(".header-due-time p");

  if (screenWidth < 750) {
    // Change content to "Time" when screen width is less than 750px
    headerText.textContent = "Time";
  } else {
    // Revert back to original content
    headerText.textContent = "Due Time";
  }
}

// Call the function initially and whenever the window is resized
updateHeaderText();
window.addEventListener("resize", updateHeaderText);
