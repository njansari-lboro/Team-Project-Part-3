$(() => {
    $("#searchInput").on("keyup", function() {
        const value = $(this).val().toLowerCase()

        $("#userTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        })
    })
// edit user ajax request / dom editing
    $(document).on("click", ".editBtn", function() {
        const row = $(this).closest("tr")
        const originalRowHtml = row.html()
        const userId = row.data("user-id")
        const name = row.find("td:eq(0)").text().split(" ")
        const email = row.find("td:eq(1)").text()
        const role = row.find("td:eq(2)").text()
        const technical = row.find("td:eq(3)").text() === "Yes"

        row.html(`
        <td><input type="text" name="first_name" value="${name[0]}" /><input type="text" name="last_name" value="${name[1]}" /></td>
        <td>${email}</td>
        <td>
            <select name="role">
                <option value="Employee"${role === "Employee" ? " selected" : ""}>Employee</option>
                <option value="Manager"${role === "Manager" ? " selected" : ""}>Manager</option>
                <option value="Admin"${role === "Admin" ? " selected" : ""}>Admin</option>
            </select>
        </td>
        <td><input type="checkbox" name="is_technical"${technical ? " checked" : ""} /></td>
        <td>
            <button class="saveBtn">Save</button>
            <button class="cancelBtn">Cancel</button>
        </td>
        `)

        $(".saveBtn").on("click", function() {
            const firstName = row.find("input[name='first_name']").val()
            const lastName = row.find("input[name='last_name']").val()
            const role = row.find("select[name='role']").val()
            const isTechnical = row.find("input[name='is_technical']").is(":checked") ? 1 : 0

            const userData = {
                user_id: userId,
                first_name: firstName,
                last_name: lastName,
                email: email,
                role: role,
                is_technical: isTechnical
            }
            console.log(userData)

            $.ajax({
                type: "POST",
                url: "users/users.php?task=modify",
                data: JSON.stringify(userData),
                dataType: "json",
                contentType: "application/json",
                success: function(response) {
                    if (response.success) {
                        row.html(`
                        <td>${firstName} ${lastName}</td>
                        <td>${email}</td> 
                        <td>${role}</td>
                        <td>${isTechnical ? "Yes" : "No"}</td>
                        <td>
                            <button class="editBtn">Edit</button>
                            <button class="deleteBtn">Delete</button>
                        </td>
                        `)
                    } else {
                        alert(`Error saving user: ${response.message}`)
                        row.html(originalRowHtml)
                    }
                },
                error: function(xhr, status, error) {
                    alert(`An error occurred: ${error}`)
                    row.html(originalRowHtml)
                }
            })
        })

        $(".cancelBtn").on("click", function() {
            row.html(originalRowHtml)
        })
    })
    //delete ajax request
    $(document).on("click", ".deleteBtn", function() {
        const userId = $(this).closest("tr").data("user-id")
        console.log(`Delete user with ID: ${userId}`)

        showDialog(
            "Confirm Delete",
            "Are you sure you want to delete this user? This action cannot be undone.",
            {
                title: "Cancel",
                role: CANCEL,
                action: () => console.log("Deletion cancelled")
            },
            {
                title: "Delete",
                role: DESTRUCTIVE,
                action: function() {
                    console.log("Delete user with ID: " + userId)
                    $.ajax({
                        type: "POST",
                        url: "users/users.php?task=delete",
                        data: {
                            task: "delete",
                            user_id: userId
                        },
                        success: function(response) {
                            const jsonResponse = JSON.parse(response)
                            if (jsonResponse.success) {
                                console.log("User successfully deleted")
                                $(`tr[data-user-id="${userId}"]`).remove()
                            } else {
                                console.error("Error deleting user: " + jsonResponse.message)
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error: " + error)
                        }
                    })
                }
            }
        )
    })
})

function validateForm() {
    const firstName = $("input[name='first_name']").val().trim()
    const lastName = $("input[name='last_name']").val().trim()
    const email = $("input[name='email']").val().trim()
    const role = $("select[name='role']").val()
    const isEmailValid = email.match(/^\w+@make-it-all\.co\.uk$/)

    const isFormValid = firstName && lastName && isEmailValid && role

    $("#add-user-btn").prop("disabled", !isFormValid)
}

validateForm()

$("#add-user-form input, #add-user-form select").on("input", validateForm)
//ajax request to save new user
$("#add-user-form").submit((e) => {
    e.preventDefault()

    console.log("ff")

    const formData = {
        first_name: $("input[name='first_name']").val().trim(),
        last_name: $("input[name='last_name']").val().trim(),
        email: $("input[name='email']").val().trim(),
        role: $("select[name='role']").val(),
        is_technical: $("input[name='is_technical']").is(":checked") ? 1 : 0
    }

    console.log(formData)

    $.ajax({
        type: "POST",
        url: "users/users.php?task=save_new_user",
        data: JSON.stringify(formData),
        dataType: "json",
        contentType: "application/json",
        success: (response) => {
            if (response.success) {
                addUserToDOM(response.userData)
                $("#add-user-form")[0].reset()
                validateForm()
            } else {
                console.error("Error: ", response.message)
            }
        },
        error: (xhr, status, error) => {
            console.log("Complete Response: ", xhr.responseText)
        }
    })
})
//adds new user to table after user is saved
function addUserToDOM(userData) {
    const isTechnicalText = userData.is_technical === 1 ? "Yes" : "No"
    const userHtml = `
    <tr data-user-id="${userData.id}">
        <td>${escapeHtml(userData.first_name + " " + userData.last_name)}</td>
        <td>${escapeHtml(userData.email)}</td>
        <td>${escapeHtml(userData.role)}</td>
        <td>${isTechnicalText}</td>
        <td>
            <button class="editBtn">Edit</button>
            <button class="deleteBtn">Delete</button>
        </td>
    </tr>
    `
    $("#userTable tbody").append(userHtml)
}

function escapeHtml(text) {
    return $("<div>").text(text).html()
}
