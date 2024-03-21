$(document).ready(function() {
    //event handler for sorting dropdown change
    $("#sort").change(function() {
        const sort_by = $(this).val()
        sort_tasks(sort_by)
    })

    //event handler for filter change
    $("#filter").change(function() {
        const selected_assignee = $(this).val()
        filter_tasks(selected_assignee)
    })

    //event handler for search input
    $("#search").on("input", function() {
        const search_text = $(this).val().toLowerCase()
        search_tasks(search_text)
    })

    //edits a task
    $(document).on("click", ".task", function() {
        const edel = $(this)
        console.log(edel)
        edit_task(edel)
    })

    //deletes a task
    $(document).on("click", ".button-x", function() {
        const el = $(this)
        showDialogAsync(
            "Are you sure you want to delete this task?",
            "You cannot undo this action.",
            { title: "Delete", role: DESTRUCTIVE, action: () => delete_task(el) }
        )
    })

    if ($("#add_button").length) {
        $("#add_button").click(function(e) {
            e.preventDefault() //prevent default form submission behavior

            //gather form data
            const task_name = $("#task_name").val()
            const task_description = $("#task_description").val()
            const start_date = $("#start_date").val()
            const end_date = $("#end_date").val()
            const resource_hours = $("#resource_hours").val()
            const assignee = $("#assignee").val()
            const project_id = $("#project_id").val()
            const base_url = "projects/form-process.php"
            const new_url = `${base_url}?id=${project_id}`
            if (!task_name || !task_description || !start_date || !resource_hours || !end_date || !assignee) {
                showDialog("Please fill in all fields", null)
                return
            }

            console.log(new_url)
            console.log(project_id)
            //ajax request to send form data
            $.ajax({
                type: "POST",
                url: new_url,
                data: {
                    task_name: task_name,
                    task_description: task_description,
                    start_date: start_date,
                    end_date: end_date,
                    resource_hours: resource_hours,
                    assignee: assignee
                },
                success: function(response) {
                    //handle success response
                    console.log("Form submitted successfully")
                    const response_decode = JSON.parse(response)
                    //console.log(response_decode);
                    const task_id = response_decode.task_id
                    add_task(task_id)
                    $("#task_form")[0].reset() //reset the form
                    //console.log("ive reset form");
                },
                error: function(xhr, status, error) {
                    console.error("Form submission failed:", error)
                    alert("Form submission failed. Please try again later.")
                }
            })
        })
    }

    //initialize accordion
    $("#accordion").accordion({
        collapsible: true
    })
})

//function for adding tasks dynamically
function add_task(task_id) {
    //console.log("im in addtask");
    const task_name = $("#task_name").val()
    const task_description = $("#task_description").val()
    const start_date = $("#start_date").val()
    const resource_hours = $("#resource_hours").val()
    const end_date = $("#end_date").val() //not picking up
    const assignee = $("#assignee").val()
    //console.log(end_date);

    if (!task_name || !task_description || !start_date || !resource_hours || !end_date || !assignee) {
        alert("Please fill in all fields.")
        return
    }
    //template for displaying task cards
    const task = $("<div>").addClass("task").html(`
        <h3>Task: ${task_name}</h3>
        <p id="desc"><strong>Description:</strong> ${task_description}</p>
        <p id="date"><strong>Start Date:</strong> ${start_date}</p>
        <p id="hours"><strong>Resource Hours:</strong> ${resource_hours}</p>
        <p id="est"><strong>End Date:</strong> ${end_date}</p>
        <h4><strong>Assignee:</strong> ${assignee}</h4>
        <input type="hidden" id="task_id" value="${task_id}">
        <span class="button-x">&times;</span></button>
    `)

    $("#task_list").append(task)

}

//function to display edit-task page based on given id
function edit_task() {
    //get all task elements
    const tasks = document.querySelectorAll(".task")
    // add double-click event listener to each task element
    tasks.forEach(task => {
        task.addEventListener("click", function(event) {
            event.preventDefault()
            const task_id = this.querySelector("#task_id").value
            const url_params = new URLSearchParams(window.location.search)
            const project_id = url_params.get("id")
            //redirect to the edit task page with the task ID appended to the URL
            window.location.href = `?page=projects&task=edit_task&id=${project_id}&task_id=${task_id}`
        })
    })
}
//function which sends relevant data using ajax to delete task from db
function delete_task(el) {
    //gather form data
    const task_id = $(el).closest(".task").find("input[type='hidden']").val()
    //const task_name = $(el).parent().find("h3").text().substring(5)
    //const task_description = $(el).parent().find("#desc").text().substring(12)
    //const start_date = $(el).parent().find("#date").text().substring(11)
    //const resource_hours = $(el).parent().find("#hours").text().substring(15)
    //var end_date = $(el).siblings('.end_date').text();
    //console.log("End Date:", end_date);
    //const assignee = $(el).parent().find("h4").text().substring(10)

    //ajax request to send form data
    $.ajax({
        type: "POST",
        url: "projects/delete-process.php",
        data: {
            task_id: task_id
        },
        success: function(response) {
            console.log("Form submitted successfully")
            $(el).parent().fadeOut(150, function() {
                $(el).parent().remove()
            })
        },
        error: function(xhr, status, error) {
            //handle error response
            console.error("Form submission failed:", error)
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
        const workDays = Math.ceil(resource_hours / 8) // assuming 8 hours per workday
        end_date.setDate(end_date.getDate() + workDays)
        end_date_input.value = end_date.toISOString().split("T")[0]
        //console.log("End Date:", end_date_input.value)
    } else {
        end_date_input.value = ""
    }
}

//function to sort tasks based on selected option
function sort_tasks(sort_by) {
    const $task_list = $("#task_list")
    //get all task items
    const $tasks = $task_list.children(".task")
    //sort tasks based on selected option
    switch (sort_by) {
    case "1": // A-Z task
        $tasks.sort(function(a, b) {
            const textA = $(a).find("h3").text().toUpperCase()
            const textB = $(b).find("h3").text().toUpperCase()
            return (textA < textB) ? -1 : (textA > textB) ? 1 : 0
        })
        break
    case "2": // Z-A task
        $tasks.sort(function(a, b) {
            const textA = $(a).find("h3").text().toUpperCase()
            const textB = $(b).find("h3").text().toUpperCase()
            return (textA > textB) ? -1 : (textA < textB) ? 1 : 0
        })
        break
    case "3": // A-Z assignee
        $tasks.sort(function(a, b) {
            const textA = $(a).find("h4").text().toUpperCase()
            const textB = $(b).find("h4").text().toUpperCase()
            return (textA < textB) ? -1 : (textA > textB) ? 1 : 0
        })
        break
    case "4": // Z-A assignee
        $tasks.sort(function(a, b) {
            const textA = $(a).find("h4").text().toUpperCase()
            const textB = $(b).find("h4").text().toUpperCase()
            return (textA > textB) ? -1 : (textA < textB) ? 1 : 0
        })
        break
    case "5": //Ascending
        $tasks.sort(function(a, b) {
            const dateA = new Date($(a).find("#date").text())
            const dateB = new Date($(b).find("#date").text())
            return dateA - dateB
        })
        break
    case "6": //Descending
        $tasks.sort(function(a, b) {
            const dateA = new Date($(a).find("#date").text())
            const dateB = new Date($(b).find("#date").text())
            return dateB - dateA
        })
        break
    default:
        //do nothing for default option
        break
    }

    //re-append sorted tasks to task list
    $task_list.empty().append($tasks)
}

//function to filter tasks based on search text
function search_tasks(search_text) {
    const $task_list = $("#task_list")
    const $tasks = $task_list.children(".task")

    $tasks.each(function() {
        const task_name = $(this).find("h3").text().toLowerCase()
        const assignee = $(this).find("h4").text().toLowerCase()
        if (task_name.includes(search_text) || assignee.includes(search_text)) {
            $(this).show()
        } else {
            $(this).hide()
        }
    })
}

//function to filter tasks based on the selected assignee
function filter_tasks(selected_assignee) {
    const $task_list = $("#task_list")
    const $tasks = $task_list.children(".task")
    $tasks.each(function() {
        const $task = $(this)
        const task_assignee = $task.find("h4").text().trim()
        //remove any additional text or formatting
        const assignee_name = task_assignee.replace("Assignee:", "").trim()
        //convert both assignees to lowercase
        if (selected_assignee === "" || assignee_name.toLowerCase() === selected_assignee.toLowerCase()) {
            //console.log("Showing task for:", assignee_name);
            $task.show()
        } else {
            //console.log("Hiding task for:", assignee_name);
            $task.hide()
        }
    })
}

window.addEventListener("DOMContentLoaded", (event) => {
    //adds an event listener to resoursce hours to predict end date when it is changed
    const resource_hours_input = document.getElementById("resource_hours")
    if (resource_hours_input) {
        resource_hours_input.addEventListener("input", function() {
            predictend_date()
        })
    }

    //redirect to appropriate page when back clicked
    document.getElementById("back_button").addEventListener("click", function() {
        //console.log("back clicked")
        const project_id = $("#project_id").val()
        window.location.href = `?page=projects&task=new_project&id=${project_id}`
    })
    //redirect to appropriate page when submit clicked
    document.getElementById("submit_button").addEventListener("click", function() {
        //console.log("submit clicked")
        const project_id = $("#project_id").val()
        window.location.href = `?page=projects&id=${project_id}`
    })
})
