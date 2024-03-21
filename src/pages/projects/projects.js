$(() => {
    // on clicking edit task button
    $(".edit-task-button").click(function() {
        let row = $(this).closest("tr");
        row.find("td:not(.progress-bar, .edit-button-cell)").each(function() {
            let cell = $(this);
            if (cell.hasClass("assignee-cell") && !cell.hasClass("editing")){
                // store the current content
                cell.data("originalContent", cell.html());
                // replace content with a search box
                cell.html("<input class='assignee-search' type='text' value='" + cell.text() + "' placeholder='Search for a name'>");
            } else if (!cell.hasClass("editing")) {
                // store the current content
                cell.data("originalContent", cell.html());
                // replace content with a textarea
                cell.html("<textarea class='editable' type='text' placeholder='Type here'>" + cell.text() + "</textarea>");
            } else {
                // restore original content
                cell.html(cell.data("originalContent"));
            }

            // toggle the "editing" class
            cell.toggleClass("editing");
        });

        // replace edit w/ save & cancel
        $(this).parent().hide();
        row.find("#save-cancel-cell").show();
    });

    // array containing all names of employees in user table
    let names;

    // asynchronous request to initialise the names array
    $.ajax({
        url: "projects/fetch-names.php",
        method: "POST",
        data: { filter: "" },
        success: function (data) {
            names = JSON.parse(data).map((datum) => datum["label"]);
        }
    });

    // on typing in assignee search bar, whilst editing
    $(document).on("keydown", ".assignee-search", function() {
        $(this).autocomplete({
            // source of the autocomplete being names containing the filter data
            source: function (request, response) {
                $.ajax({
                    url: "projects/fetch-names.php",
                    method: "POST",
                    data: { filter: request.term },
                    success: function (data) {
                        response(JSON.parse(data));
                    }
                });
            },
            // on selection of a name save and display the chosen user's name and save the user's ID as an attribute
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                $(this).attr("value", ui.item.label);
                $(this).attr("assignee-id", ui.item.value);
            }
        });
    });

    // on clicking save button
    $(".save-task-button").click(function () {
        // identifying the row to modify and other necessary data
        let projectId = $(this).closest("div.dropdown-content").data("value");
        let row = $(this).closest("tr");
        let saveButton = $(this);
        let hours = row.find(".editable").eq(2).val();
        let name = row.find(".assignee-search").val();

        if (isNaN(hours) || hours < 0) {
            showDialog("Input Error", "Please input an appropriate estimated hours value.");
        } else if (names.indexOf(name) < 0) {
            showDialog("Input Error", "Please input an appropriate name value.");
        } else {
            showDialog(
                "Save changes to task?",
                "Confirm and save any changes made or cancel and continue editing.",
                { title: "Save", action: () => saveTaskChanges(row, saveButton, projectId) },
                { title: "Cancel", role: CANCEL }
            );
        }
    });

    // on clicking cancel button
    $(".cancel-task-button").click(function () {
        // restore original content and toggle editing state
        let row = $(this).closest("tr");
        row.find("td.editing").each(function () {
            let cell = $(this);
            cell.html(cell.data("originalContent"));
            cell.removeClass("editing");
        });

        // replace save & cancel w/ edit
        $(this).parent().hide();
        row.find("#edit-cell").show();
    });

    // on clicking a project title dropdown
    $(".dropdown").click(function () {
        // content = #project-n-content
        let buttonId = "#" + $(this).attr("id");
        let contentId = buttonId + "-content";

        $(buttonId).toggleClass("rotated");
        $(contentId).slideToggle();
    });

    // bringing the user to create-a-project page
    $("#new-project-button").click(() => {
        window.location.href = "?page=projects&task=new_project";
    });

    // inserting JQuery progressbar into <td>
    $(".progress-bar").each(function () {
        let cell = $(this).get(0);
        let progress = parseInt($(this).text(), 10);

        $(this).attr("data-value", progress + "%");

        cell.textContent = "";

        $(this).progressbar({
            value: progress,
            max: 100
        });
    });
});

function saveTaskChanges(row, saveButton, projectId) {
    // handle saving changes
    let assigneeId = row.find(".assignee-search").attr("assignee-id");
    let taskName = row.find(".editable").eq(0).val();
    let taskDescription = row.find(".editable").eq(1).val();
    let estimatedHours = parseFloat(row.find(".editable").eq(2).val());

    $.ajax({
        url: "projects/update-task.php",
        type: "POST",
        data: {
            project_id: projectId,
            task_id: row.data("value"),
            assignee: assigneeId,
            name: taskName,
            description: taskDescription,
            resource_hours: estimatedHours
        },
        success: (response) => {
            // refresh projects display
            // refresh progress bar
            let progressBar = row.find(".progress-bar").eq(0);
            let progress = parseInt(100 * (parseFloat(progressBar.attr("hours-spent")) / estimatedHours), 10);
            progressBar.progressbar({ value: progress, max: 100 });
            progressBar.attr("data-value", progress + "%");

            // update cells with new data
            row.find(".assignee-cell").text(row.find(".assignee-search").attr("value"));
            row.find(".editable").eq(0).text(taskName);
            row.find(".editable").eq(1).text(taskDescription);
            row.find(".editable").eq(2).text(estimatedHours);

            row.find("td.editing").each(function () {
                let cell = $(this);
                if (cell.hasClass("assignee-cell")){
                    cell.html(row.find(".assignee-search").attr("value"));
                }
                else {
                    cell.html(cell.find(".editable").text());
                }
                cell.removeClass("editing");
            });
        },
        error: (error) => {
            console.log("Error: ", error);
        }
    });

    // replace save & cancel w/ edit
    saveButton.parent().hide();
    row.find("#edit-cell").show();
}
