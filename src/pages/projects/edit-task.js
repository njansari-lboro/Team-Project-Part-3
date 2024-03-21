$(document).ready(function() {
    //updates a task
    $("#update").on("click", function(event) {
        event.preventDefault()
            console.log("update clicked!!!")
            update_task()
        })
})
//function which prepares a new ajax with new data to update database
function update_task() {
    //get form data
    const task_id = $("#task_id").val()
    const task_name = $("#task_name").val() || $("#task_name").attr("placeholder")
    const task_description = $("#task_description").val() || $("#task_description").attr("placeholder")
    const start_date = $("#start_date").val()
    const end_date = $("#end_date").val()
    const resource_hours = $("#resource_hours").val()
    const assignee = $("#assignee").val() || $("#assignee option:selected").text()
    // console.log(task_name);
    // console.log(task_description);
    // console.log(start_date);
    // console.log(end_date);
    // console.log(resource_hours);
    // console.log(assignee);
    // console.log(task_id);
    if (!task_name || !task_description || !start_date || !resource_hours || !end_date || !assignee) {
        showDialog("Please fill in all fields", null);
        return;
    }
    //ajax request
    $.ajax({
        type: "POST",
        url: "projects/update-process.php",
        data: {
            task_id: task_id,
            task_name: task_name,
            task_description: task_description,
            start_date: start_date,
            end_date: end_date,
            resource_hours: resource_hours,
            assignee: assignee
        },
        success: async function(response) {
            $("#up_form")[0].reset();
            const project_id = $("#project_id").val();
            window.location.href = `?page=projects&task=new_project_tasks&id=${project_id}&task_id=${task_id}`;
        },
        error: function(xhr, status, error) {
            console.error("Form submission failed:", error)
            console.error("XHR:", xhr)
            alert("Form submission failed. Please try again later.")
        }
    })
}

//function to predict end date based on resource hours
function predictend_date() {
    const start_date_input = document.getElementById("start_date")
    const end_date_input = document.getElementById("end_date")
    const resource_hours_input = document.getElementById("resource_hours")
    const start_date = new Date(start_date_input.value)
    const resource_hours = parseFloat(resource_hours_input.value)

    if (!isNaN(start_date.getTime()) && !isNaN(resource_hours)) {
        const end_date = new Date(start_date)
        const work_days = Math.ceil(resource_hours / 8) //assuming 8 hours per workday
        end_date.setDate(end_date.getDate() + work_days)
        end_date_input.value = end_date.toISOString().split("T")[0]
    } else {
        end_date_input.value = ""
    }
}

window.addEventListener("DOMContentLoaded", (event) => {
    //if cancel clicked go back to add-task page
    $("#cancel").on("click", function(event) {
        event.preventDefault()
        console.log("cancel clicked")
        const project_id = $("#project_id").val()
        const task_id = $("#task_id").val()
        window.location.href = `?page=projects&task=new_project_tasks&id=${project_id}&task_id=${task_id}`
    })

    //event listener for resource hours to predict end date
    const resource_hours_input = document.getElementById("resource_hours")
    if (resource_hours_input) {
        resource_hours_input.addEventListener("input", function() {
            predictend_date()
        })
    }
})
