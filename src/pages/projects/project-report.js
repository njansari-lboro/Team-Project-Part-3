$(() => {
    // retrieving project ID from URL
    const projectId = new URLSearchParams(window.location.search).get('id');
    var projectHasIncompleteTasks; // boolean

    $.ajax({
        url: "projects/project-has-incomplete-tasks.php",
        method: "POST",
        data: { project_id: projectId },
        success: function (response) {
            // setting boolean value, determining whether a project can be completed or not
            projectHasIncompleteTasks = response;
        }
    });

    // on clicking exit, redirect to projects page
    $(".exit-btn").click(() => {
        window.location.href = "?page=projects"
    })

    // on clicking mark project as complete
    $("#complete-project").click(() => {
        // if there are still active tasks, fail to complete project
        if (projectHasIncompleteTasks){
            showDialog("Unable to mark project as completed", "Project has incomplete tasks.");
        } else {
            // prompt user to mark as complete
            showDialog(
                "Are you sure?", 
                "This action cannot be undone.",
                {title: "Mark as Completed", isDefault: true, action: () => completeProject(projectId, projectHasIncompleteTasks)},
                {title: "Cancel", role: CANCEL}
            );
        }
    });
});

// sets a given project to complete
function completeProject(projectId, projectHasIncompleteTasks){
    $.ajax({
        url: "projects/complete-project.php",
        method: "POST",
        data: { project_id: projectId },
        success: function (response) {
            // on success, redirect to projects page
            window.location.href = `?page=projects`;
        }
    });
}
