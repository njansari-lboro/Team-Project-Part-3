// Dialog button role constants

const CANCEL = "cancel"
const DESTRUCTIVE = "destructive"

/**
 * Shows a dialog with an optional message to the user requesting they perform an action from a given list.
 *
 * All actions in a dialog dismiss the dialog after the action runs. The default button is shown with greater prominence. You can influence the default button by assigning it the role of DEFAULT.
 * Buttons may be reordered based on their role and prominence.
 * If no actions are present, the system includes a standard "OK" action. A default cancel action is provided where necessary. If you want to show a cancel action, use a button with a role of CANCEL.
 *
 * @param {string} title A text string used as the title of the dialog.
 * @param {?string} message An optional text string used as the message for the dialog. The default value is `null`.
 * @param {...Object} buttons An array of objects describing the buttons for the dialog.
 * Each button object has these properties:
 *   * title – A string that describes the purpose of the button's action.
 *   * role – An optional semantic role describing the button. A value of `null` (the default) means that the button doesn't have an assigned role.
 *   * isDefault – A boolean value indicating whether the button performs the default action. The default value is `false`.
 *   * action – The action to perform when the user triggers the button. The value must be a function that accepts no arguments and returns nothing.
 *
 * The button's role can be one of these constants:
 *   * CANCEL – Use this role for a button that cancels the current operation.
 *   * DESTRUCTIVE – Use this role for a button that deletes user data, or performs an irreversible operation.
 *                   A destructive button signals by its appearance that the user should carefully consider whether to click the button.
 *
 * @example
 * // Show confirmation that user has been registered
 * // Will show a dialog with a title and an "OK" button
 * showDialog("You are now registered")
 *
 * // Ask for confirmation before deleting a tutorial
 * // Will show a dialog with a title, message, and two buttons: "Cancel" and "Delete"
 * showDialog(
 *     "Are you sure you want to delete this tutorial?",
 *     "You cannot undo this action.",
 *     { title: "Delete", role: DESTRUCTIVE, action: () => console.log("Deleted tutorial") }
 * )
 *
 * // Asks user for input when leaving editing a project with unsaved changes
 * // Will show a dialog with a title, message, and three buttons: "Save" (default), "Don't Save" and "Continue Editing" (cancel)
 * showDialog(
 *     "Save changes made to this project?",
 *     "This project contains unsaved changes. These changes will be discarded if not saved.",
 *     { title: "Save Changes", isDefault: true, action: () => console.log("Saving changes") },
 *     { title: "Discard Changes", role: DESTRUCTIVE, action: () => console.log("Discarding changes") },
 *     { title: "Continue Editing", role: CANCEL }
 * )
 */
function showDialog(title, message = null, ...buttons) {
    const NORMAL = "normal"
    const DEFAULT = "default"

    const buttonRoles = [NORMAL, DESTRUCTIVE, CANCEL]

    // Add default OK button if no buttons are provided
    if (!buttons.length) {
        buttons.push({ title: "OK", role: CANCEL, isDefault: true})
    }

    // Register all the buttons

    let dialogButtonDescriptions = []

    let buttonCounts = {}
    buttonCounts[NORMAL] = 0
    buttonCounts[CANCEL] = 0
    buttonCounts[DESTRUCTIVE] = 0
    buttonCounts[DEFAULT] = 0

    for (const button of buttons) {
        // Maximum number of buttons is three: ignore the rest
        if (dialogButtonDescriptions.length >= 3) break

        // Fill in any missing values

        button.title ??= ""
        button.role ??= NORMAL
        button.isDefault ??= false
        button.action ??= (() => {})

        // Account for button role

        if (!buttonRoles.includes(button.role)) {
            button.role = NORMAL
        }

        buttonCounts[button.role]++

        dialogButtonDescriptions.push(button)
    }

    // Configure default buttons: only one default button allowed

    dialogButtonDescriptions
        .filter((e) => e.isDefault === true && e.role !== DESTRUCTIVE)
        .sort((a, b) => buttonRoles.indexOf(a.role) - buttonRoles.indexOf(b.role))
        .forEach((button) => {
            buttonCounts[button.role]--

            if (!buttonCounts[DEFAULT]) {
                if (button.role !== DESTRUCTIVE) {
                    button.role = DEFAULT
                }

                buttonCounts[DEFAULT]++
            } else {
                if (button.role !== DESTRUCTIVE) {
                    button.role = NORMAL
                    buttonCounts[NORMAL]++
                }
            }
        })

    // Configure cancel buttons: only one cancel button allowed

    buttonCounts[CANCEL] = 0

    dialogButtonDescriptions
        .filter((e) => e.role === CANCEL)
        .forEach((button) => {
            if (!buttonCounts[CANCEL]) {
                buttonCounts[CANCEL]++
            } else {
                button.role = NORMAL
                buttonCounts[NORMAL]++
            }
        })

    // Move cancel buttons to the end
    dialogButtonDescriptions.sort((a, b) => (a.role === CANCEL) - (b.role === CANCEL))

    // Add default cancel button, if necessary
    if (
        !buttonCounts[CANCEL] &&
        buttonCounts[DESTRUCTIVE] &&
        buttonCounts[NORMAL] + buttonCounts[DEFAULT] < 2
    ) {
        dialogButtonDescriptions.push({ title: "Cancel", role: CANCEL, action: () => {} })
        buttonCounts[CANCEL]++
    }

    // Make the first button the default, if necessary
    if (
        !buttonCounts[DEFAULT] &&
        dialogButtonDescriptions.at(0).role !== DESTRUCTIVE
    ) {
        dialogButtonDescriptions.at(0).role = DEFAULT
        buttonCounts[DEFAULT]++
    }

    // Make sure the following requirements are satisfied:
    // - There must be a default or cancel button
    // - There must be at most one default button
    // - There must be at most one cancel button
    // - The total number of normal, default and destructive buttons must be at most three
    if (
        !(buttonCounts[DEFAULT] || buttonCounts[CANCEL]) ||
        buttonCounts[DEFAULT] > 1 ||
        buttonCounts[CANCEL] > 1 ||
        buttonCounts[NORMAL] + buttonCounts[DEFAULT] + buttonCounts[DESTRUCTIVE] > 4
    ) {
        console.log(`Invalid number of buttons: ${buttonCounts}`)
        return
    }

    // Set dialog HTML elements

    $("#dialog").fadeIn(200, "swing")

    $("#dialog-card").removeClass("column-layout")

    // Set dialog's title and message

    const dialogTitle = $("#dialog-card .dialog-title")
    const dialogMessage = $("#dialog-card .dialog-message")

    dialogTitle.text(title)

    if (message) {
        dialogMessage.text(message)
        dialogMessage.show()
    } else {
        dialogMessage.hide()
    }

    // Clear any existing buttons
    $("#dialog-buttons").empty()

    // Add and configure the new buttons
    for (const button of dialogButtonDescriptions) {
        $("#dialog-buttons").append(`<button class="${button.role}">${button.title}</button>`)
        $("#dialog-buttons button").last().click(button.action)
    }

    const dialogButtons = $("#dialog-buttons button")

    // If there are more than two buttons or if they take up too much width (i.e. overflow),
    // then switch to a column layout

    let switchToColumn = false

    if (dialogButtonDescriptions.length > 2) {
        switchToColumn = true
    } else {
        let totalWidth = 0

        dialogButtons.each(function() {
            let width = $(this).outerWidth()
            totalWidth += width

            if (width >= 118 || totalWidth > 235) {
                switchToColumn = true
                return false
            }
        })
    }

    if (switchToColumn) {
        $("#dialog-card").addClass("column-layout")
    }

    dialogButtons.click(() => {
        $("#dialog").fadeOut(200, "swing")
        $(document).off("keyup.dismissDialog")
    })

    setTimeout(() => {
        // Add keyboard shortcuts to dismiss dialog
        $(document).on("keyup.dismissDialog", (e) => {
            switch (e.key) {
            case "Escape":
                $("#dialog-buttons button.cancel").click()
                break
            case "Enter":
                $("#dialog-buttons button.default").click()
                break
            }
        })
    }, 100)
}

/**
 * Shows a dialog asynchronously with a message to the user requesting they perform an action from a given list.
 *
 * This function returns a Promise object that can be ignored. If the dialog is to be used like the system `alert` function (pauses execution), then it must be called with the `await` keyword from within an asynchronous context (see example).
 *
 * @param {string} title A text string used as the title of the dialog.
 * @param {?string} message An optional text string used as the message for the dialog. The default value is `null`.
 * @param {...Object} buttons An array of objects describing the buttons for the dialog.
 *
 * @note This function is just an asynchronous version of `showDialog`.
 *
 * @example
 * // Ask for confirmation before deleting a tutorial
 * async function showDeleteConfirmation() {
 *     // Will show a dialog with a title, message, and two buttons: "Cancel" and "Delete"
 *     await showDialogAsync(
 *        "Are you sure you want to delete this tutorial?",
 *        "You cannot undo this action.",
 *        { title: "Delete", role: DESTRUCTIVE, action: () => console.log("Deleted tutorial") }
 *     )
 *
 *     console.log("Dialog dismissed")
 * }
 */
async function showDialogAsync(title, message = null, ...buttons) {
    return new Promise((resolve) => {
        showDialog(title, message, ...buttons)
        $("#dialog-buttons button").click(resolve)
    })
}
