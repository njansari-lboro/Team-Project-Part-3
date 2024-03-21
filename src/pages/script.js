$(() => {
    // Toggle the side navigation
    const sidebarToggle = $("#sidebar-toggle")

    sidebarToggle.click((event) => {
        event.preventDefault()
        toggleSidebar()
    })

    // Add keyboard shortcuts for navigation
    $(document).keyup((e) => {
        if (e.altKey) {
            if (e.code === "KeyS") {
                toggleSidebar()
            } else {
                const match = e.code.match(/Digit([0-9])/)
                if (match && match[1]) {
                    $(`#sidebar-links`).children().toArray()[match[1] - 1].click()
                }
            }
        }
    })

    $("#sidebar-dim.dimmed-overlay").click(toggleSidebar)

    $("#profile-menu-button").click((event) => {
        event.preventDefault()

        if ($("#profile-menu-items").is(":visible")) {
            dismissProfileMenu()
        } else {
            $("#profile-menu-items").show()
            $("#profile-menu-button").css("background-color", "var(--unemphasised-selected-content-background-color)")
            $("#profile-menu-button .notification-badge").hide()
            $("#profile-menu-dim.dimmed-overlay").show()
        }
    })

    // Configure edit profile modal

    $("#edit-profile-button").click(() => {
        dismissProfileMenu()
        $("#edit-profile-modal").fadeIn(500, "swing")

        function uploadedImage(uploader) {
            if (uploader.files && uploader.files[0]) {
                const url = window.URL.createObjectURL(uploader.files[0])
                $("#edit-profile-user-image").attr("src", url)
                $("#edit-profile-user-image-dark").attr("srcset", url)
            }
        }

        $("#edit-profile-image .image-upload").on("change", function() {
            $("#edit-profile-image .image-upload").attr("changed", "")
            uploadedImage(this)
            checkIfEditProfileCanSave()
        })

        $("#edit-profile-image").click(() => {
            if ($("#edit-profile-upload-image").is(":hidden")) {
                $("#edit-profile-form").animate({ scrollTop: 0 }, 100)
            }
        })

        $("#edit-profile-upload-image").on("click", () => $("#edit-profile-image .image-upload").click())

        $("#edit-profile-user-image").attr("src", user.profile_image_path ?? "../img/default-user-profile-image.png")
        $("#edit-profile-user-image-dark").attr("srcset", user.profile_image_path ?? "../img/default-user-profile-image-dark.png")

        $("#edit-profile-form").scroll(function() {
            if (this.scrollTop > 50) {
                $("#edit-profile-upload-image").fadeOut(100)
                $("#edit-profile-user-image").css("width", "8em")
                $("#edit-profile-user-image").css("height", "8em")
                $("#edit-profile-form").css("height", "calc(100% - 215px)")
            } else {
                $("#edit-profile-upload-image").fadeIn(100)
                $("#edit-profile-user-image").css("width", "10em")
                $("#edit-profile-user-image").css("height", "10em")
                $("#edit-profile-form").css("height", "calc(100% - 235px)")
            }
        })

        $("#edit-profile-image .image-upload").removeAttr("changed")
        $("#edit-first-name-input").removeAttr("changed")
        $("#edit-last-name-input").removeAttr("changed")
        $("#edit-new-password-input").removeAttr("changed")

        $("#edit-first-name-input").val(user.first_name)
        $("#edit-last-name-input").val(user.last_name)
        $("#edit-email-input").val(user.email)
        $("#edit-password-input").val(user.password)

        $("#edit-first-name-input").change(() => {
            $("#edit-first-name-input").attr("changed", "")
            checkIfEditProfileCanSave()
        })

        $("#edit-last-name-input").change(() => {
            $("#edit-last-name-input").attr("changed", "")
            checkIfEditProfileCanSave()
        })

        $("#edit-email-input").change(checkIfEditProfileCanSave)

        $("#edit-profile-change-password-button").show()
        $("#edit-profile-change-password").hide()

        $("#edit-current-password-input").prop("disabled", false)
        $("#edit-new-password-label").css("color", "var(--disabled-control-text-color)")
        $("#edit-confirm-password-label").css("color", "var(--disabled-control-text-color)")
        $("#edit-new-password-requirements, span.edit-password-req").css("color", "var(--disabled-control-text-color)")
        $("#edit-new-password-input").prop("disabled", true)
        $("#edit-confirm-password-input").prop("disabled", true)
        $("#edit-new-password-input").css("background-color", "var(--tertiary-fill-color)")
        $("#edit-confirm-password-input").css("background-color", "var(--tertiary-fill-color)")
        $("#show-hide-password-button").hide()

        $("#save-button").prop("disabled", true)

        $("#edit-profile-change-password-button").click(() => {
            $("#save-button").prop("disabled", true)

            $("#edit-profile-change-password-button").hide()
            $("#edit-profile-change-password").show()
            $("#edit-current-password-input").focus()

            setTimeout(() => {
                const form = $("#edit-profile-form")
                form.animate({ scrollTop: form[0].scrollHeight }, 200)
            }, 100)
        })

        $("#edit-current-password-input").change(() => {
            $.ajax({
                url: "?action=validate_password",
                type: "POST",
                data: {
                    current_password: $("#edit-current-password-input").val()
                },
                success: (response) => {
                    if (response === "true") {
                        $("#edit-current-password-input").css("background-color", "var(--tertiary-fill-color)")

                        $("#edit-new-password-label").css("color", "var(--label-color)")
                        $("#edit-confirm-password-label").css("color", "var(--label-color)")
                        $("#edit-new-password-requirements, span.edit-password-req").css("color", "var(--secondary-label-color)")

                        $("#edit-current-password-input").prop("disabled", true)
                        $("#edit-new-password-input").prop("disabled", false)
                        $("#edit-confirm-password-input").prop("disabled", false)
                        $("#show-hide-password-button").show()

                        $("#edit-new-password-input").focus()
                    } else {
                        $("#edit-current-password-input").css("background-color", "var(--reduced-red-color)")
                    }
                }
            })
        })

        $("#edit-new-password-input").on("input", () => validateNewPassword())

        $("#edit-new-password-input").change(() => {
            $("#edit-new-password-input").attr("changed", "")

            const newInput = $("#edit-new-password-input")
            const confirmInput = $("#edit-confirm-password-input")

            const isValidPassword = validateNewPassword()

            if (isValidPassword && confirmInput.val() === newInput.val()) {
                newInput.css("background-color", "var(--tertiary-fill-color)")
                confirmInput.css("background-color", "var(--tertiary-fill-color)")

                checkIfEditProfileCanSave()
            } else if (isValidPassword) {
                newInput.css("background-color", "var(--tertiary-fill-color)")

                confirmInput.focus()
            } else if (!confirmInput.val()) {
                newInput.css("background-color", "var(--reduced-red-color)")

                $("#save-button").prop("disabled", true)
            } else {
                newInput.css("background-color", "var(--reduced-red-color)")
                confirmInput.css("background-color", "var(--reduced-red-color)")

                $("#save-button").prop("disabled", true)
            }
        })

        $("#edit-confirm-password-input").change(() => {
            const newInput = $("#edit-new-password-input")
            const confirmInput = $("#edit-confirm-password-input")

            const isValidPassword = validateNewPassword()

            if (isValidPassword && confirmInput.val() === newInput.val()) {
                newInput.css("background-color", "var(--tertiary-fill-color)")
                confirmInput.css("background-color", "var(--tertiary-fill-color)")

                checkIfEditProfileCanSave()
            } else {
                newInput.css("background-color", "var(--reduced-red-color)")
                confirmInput.css("background-color", "var(--reduced-red-color)")

                $("#save-button").prop("disabled", true)
            }
        })

        $("#edit-new-password-input-container").mouseleave(() => {
            $("#edit-new-password-input").attr("type", "password")
            $("#show-password-icon").show()
            $("#hide-password-icon").hide()
        })

        $("#show-hide-password-button").click(() => {
            $("#show-password-icon").toggle()
            $("#hide-password-icon").toggle()

            if ($("#show-password-icon").is(":visible")) {
                $("#edit-new-password-input").attr("type", "password")
            } else {
                $("#edit-new-password-input").attr("type", "text")
            }
        })

        $("#cancel-button").click(() => {
            if ($("#edit-profile-card [changed]").length) {
                showDialog(
                    "Discard Changes?",
                    "You have unsaved changes that will be discarded. This action cannot be undone.",
                    { title: "Discard", role: DESTRUCTIVE, action: dismissEditProfileModal }
                )
            } else {
                dismissEditProfileModal()
            }
        })

        $("#save-button").click(() => {
            dismissEditProfileModal()

            if ($("#edit-profile-image .image-upload").is("[changed]")) {
                window.URL.revokeObjectURL($("#edit-profile-user-image").attr("src"))

                const profileImageUpload = document.querySelector("#edit-profile-image .image-upload")

                if (profileImageUpload.files && profileImageUpload.files[0]) {
                    const data = new FormData()

                    data.append("upload_profile_image", profileImageUpload.files[0])

                    $.ajax({
                        url: "",
                        type: "POST",
                        data: data,
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: (response) => {
                            if (response.success) {
                                saveUser(response.file_name)
                            } else {
                                console.log(response.message)
                            }
                        },
                        error: (xhr, status, error) => {
                            console.log("AJAX Error: ", status, error)
                        }
                    })
                }
            } else {
                saveUser()
            }
        })
    })

    // Configgure notifications modal

    $("#notifications-button").click(() => {
        dismissProfileMenu()

        $("#notifications-modal").fadeIn(500, "swing")

        $("#notifications-list").empty()

        const notificationCardHtml = ({ title, body, date_posted }) => `
        <div class="notification-card">
            <button class="remove-notification-button">
                <load-svg class="remove-notification-icon" src="../assets/close-icon.svg">
                    <style shadowRoot>
                        svg {
                            width: 0.8em;
                            height: 0.8em;
                        }

                        .fill {
                            fill: var(--secondary-label-color)
                        }
                    </style>
                </load-svg>
            </button>

            <div>
                <span class="notification-title">${title}</span>
                <span class="notification-date-posted">${formatNotificationDate(new Date(date_posted))}</span>
            </div>

            <span class="notification-body">${body}</span>
        </div>
        `

        $.ajax({
            url: "?action=update_user_notifications",
            type: "POST",
            data: {},
            dataType: "json",
            success: (notifications) => {
                if (notifications.length === 0) {
                    $("#notifications-list").hide()
                } else {
                    $("#no-notifications-placeholder").hide()

                    for (const notification of notifications) {
                        $("#notifications-list").append(notificationCardHtml(notification))

                        $("#notifications-list > :last-child").click(() => {
                            $.ajax({
                                url: "?action=update_user_notifications",
                                type: "POST",
                                data: { notification_id: notification.id },
                                success: () => {
                                    switch (notification.type) {
                                    case "favourited_tutorials":
                                        window.location.href = `?page=tutorials&task=view&id=${notification.data_id}`
                                        break;
                                    case "favourited_forum_posts":
                                    case "created_forum_posts":
                                        window.location.href = `?page=forums&task=view&id=${notification.data_id}`
                                        break;
                                    case "project_tasks":
                                        switch (user.role) {
                                        case "Admin":
                                        case "Manager":
                                            window.location.href = "?page=projects"
                                            break;
                                        case "Employee":
                                            window.location.href = "?page=tasks"
                                            break;
                                        }

                                        break;
                                    }
                                }
                            })
                        })

                        $("#notifications-list > :last-child .remove-notification-button").click(function(event) {
                            event.stopPropagation()

                            const card = $(this).closest(".notification-card")
                            card.addClass("removed-item")

                            $.ajax({
                                url: "?action=update_user_notifications",
                                type: "POST",
                                data: { notification_id: notification.id },
                                success: () => {
                                    setTimeout(() => {
                                        card.remove()
                                        removeNotification()
                                    }, 500)
                                }
                            })
                        })
                    }
                }
            },
            error: (xhr, status, error) => {
                console.log("AJAX Error: ", status, error)
            }
        })

        $.ajax({
            url: "?action=update_notification_preferences",
            type: "POST",
            data: {},
            dataType: "json",
            success: (response) => {
                $("#favourited-tutorial-notifications-checkbox")[0].checked = response.favourited_tutorials
                $("#favourited-tutorial-notifications-checkbox").change(function() {
                    $.ajax({
                        url: "?action=update_notification_preferences",
                        type: "POST",
                        data: { favourited_tutorial_notifications_preference: this.checked ? 1 : 0 }
                    })
                })

                $("#favourited-forum-post-notifications-checkbox")[0].checked = response.favourited_forum_posts
                $("#favourited-forum-post-notifications-checkbox").change(function() {
                    $.ajax({
                        url: "?action=update_notification_preferences",
                        type: "POST",
                        data: { favourited_forum_post_notifications_preference: this.checked ? 1 : 0 }
                    })
                })

                $("#created-forum-post-notifications-checkbox")[0].checked = response.created_forum_posts
                $("#created-forum-post-notifications-checkbox").change(function() {
                    $.ajax({
                        url: "?action=update_notification_preferences",
                        type: "POST",
                        data: { created_forum_post_notifications_preference: this.checked ? 1 : 0 }
                    })
                })

                $("#project-tasks-notifications-checkbox")[0].checked = response.project_tasks
                $("#project-tasks-notifications-checkbox").change(function() {
                    $.ajax({
                        url: "?action=update_notification_preferences",
                        type: "POST",
                        data: { project_tasks_notifications_preference: this.checked ? 1 : 0 }
                    })
                })
            },
            error: (xhr, status, error) => {
                console.log("AJAX Error: ", status, error)
            }
        })

        $("#close-notifications-modal-button").click(() => {
            $("#notifications-modal").fadeOut()
        })
    })

    // Configure invite member modal

    $("#invite-button").click((event) => {
        event.preventDefault()

        $("#invite-member-modal").fadeIn(500, "swing")

        $("#invite-member-card span:last-child").hide()

        $("#invite-member-email").on("input", () => {
            const email = $("#invite-member-email").val()
            const emailMatch = /^(.+)@make-it-all\.co\.uk$/

            $("#invite-member-button").prop("disabled", true)

            if (email.trim().length > 0 && email.match(emailMatch)) {
                $.ajax({
                    type: "POST",
                    url: "../helpers/emailcheck.php",
                    data: { email: email },
                    dataType: "json",
                    success: (response) => {
                        if (response.status === "unregistered") {
                            $("#invite-member-button").prop("disabled", false)
                        }
                    }
                })
            }

            $("#invite-link").val("")
            $("#copy-invite-link-button").prop("disabled", true)
            $("#invite-member-card span:last-child").hide()
        })

        $("#invite-member-email").change(function() {
            $(this).blur()
            if (!$("#invite-member-button").prop("disabled")) {
                $("#invite-member-button").click()
            }
        })

        $("#invite-member-button").click(() => {
            const email = $("#invite-member-email").val()

            $.ajax({
                url: "?action=invite_user",
                type: "POST",
                data: { email: email },
                success: (response) => {
                    if (response !== false) {
                        $("#invite-member-button").prop("disabled", true)

                        let encodedInviteCode = btoa(response).replace(/=+$/, "")
                        $("#invite-link").val(`${window.location.origin}/?invite_code=${encodedInviteCode}`)

                        $("#copy-invite-link-button").prop("disabled", false)
                        $("#invite-member-card span:last-child").show()
                    }
                }
            })
        })

        $("#copy-invite-link-button").click(async () => {
            $("#invite-link").focus().select()

            if (navigator.clipboard) {
                try {
                    await navigator.clipboard.writeText($("#invite-link").val())
                    console.log("Copied")
                } catch (error) {
                    console.log(`Copy failed: ${error}`)
                }
            } else {
                document.execCommand("copy")
            }
        })

        $("#close-invite-member-modal-button").click(() => {
            $("#invite-member-modal").fadeOut(() => {
                $("#invite-member-email").val("")
                $("#invite-member-button").prop("disabled", true)
                $("#invite-link").val("")
                $("#copy-invite-link-button").prop("disabled", true)
                $("#invite-member-card span:last-child").hide()
            })
        })
    })

    $("#profile-menu-dim.dimmed-overlay").click((event) => {
        if (!event.target.closest("#profile-menu")) {
            dismissProfileMenu()
        }
    })

    $(".modal").each((_, element) => {
        const options = { root: document.documentElement }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.intersectionRatio > 0) {
                    $(document).on("keyup.dismissModal", (e) => {
                        switch (e.key) {
                        case "Escape":
                            $(".modal .modal-dismiss-button").click()
                            break
                        }
                    })
                } else {
                    $(document).off("keyup.dismissModal")
                }
            })
        }, options)

        observer.observe(element)
    })
})

function dismissProfileMenu() {
    $("#profile-menu-items").hide()
    $("#profile-menu-button").css("background-color", "transparent")
    $("#profile-menu-button .notification-badge").show()
    $("#profile-menu-dim.dimmed-overlay").hide()
}

function dismissEditProfileModal() {
    $("#edit-profile-modal").fadeOut(() => {
        $("#edit-new-password-input").attr("type", "password")
        $("#show-password-icon").show()
        $("#hide-password-icon").hide()

        $("#edit-profile-image .image-upload").val("")
        $("#edit-current-password-input").val("")
        $("#edit-new-password-input").val("")
        $("#edit-confirm-password-input").val("")
    })
}

function validateNewPassword() {
    const password = $("#edit-new-password-input").val()

    const minLengthRegex = /.{12,}/
    const uppercaseRegex = /[A-Z]/
    const lowercaseRegex = /[a-z]/
    const numberRegex = /[0-9]/
    const symbolRegex = /[\W_]/

    let isValid = true

    if (minLengthRegex.test(password)) {
        $("#edit-password-reqs-min-chars").css("color", "var(--green-color)")
    } else {
        $("#edit-password-reqs-min-chars").css("color", "var(--red-color)")
        isValid = false
    }

    if (uppercaseRegex.test(password)) {
        $("#edit-password-reqs-uppercase").css("color", "var(--green-color)")
    } else {
        $("#edit-password-reqs-uppercase").css("color", "var(--red-color)")
        isValid = false
    }

    if (lowercaseRegex.test(password)) {
        $("#edit-password-reqs-lowercase").css("color", "var(--green-color)")
    } else {
        $("#edit-password-reqs-lowercase").css("color", "var(--red-color)")
        isValid = false
    }

    if (numberRegex.test(password)) {
        $("#edit-password-reqs-number").css("color", "var(--green-color)")
    } else {
        $("#edit-password-reqs-number").css("color", "var(--red-color)")
        isValid = false
    }

    if (symbolRegex.test(password)) {
        $("#edit-password-reqs-symbol").css("color", "var(--green-color)")
    } else {
        $("#edit-password-reqs-symbol").css("color", "var(--red-color)")
        isValid = false
    }

    return isValid
}

function checkIfEditProfileCanSave() {
    const firstName = $("#edit-first-name-input").val()
    const lastName = $("#edit-last-name-input").val()

    let saveIsDisabled = true
    setTimeout(() => $("#save-button").prop("disabled", saveIsDisabled), 0)

    if (firstName.trim().length === 0) {
        return
    }

    if (lastName.trim().length === 0) {
        return
    }

    saveIsDisabled = false
}

function saveUser(profileImageURL = null) {
    const data = {}

    if (profileImageURL) {
        data.profile_image_url = profileImageURL
    }

    if ($("#edit-first-name-input").is("[changed]")) {
        data.first_name = $("#edit-first-name-input").val().trim()
    }

    if ($("#edit-last-name-input").is("[changed]")) {
        data.last_name = $("#edit-last-name-input").val().trim()
    }

    if ($("#edit-new-password-input").is("[changed]")) {
        data.password = $("#edit-new-password-input").val()
    }

    $.ajax({
        url: "?action=save_user",
        type: "POST",
        data: data,
        dataType: "json",
        success: () => window.location.reload()
    })
}

function removeNotification() {
    const notificationCount = $("#notifications-list").children().length

    if (notificationCount === 0) {
        $(".notification-badge").hide()
        $("#notifications-card h1").text("Notifications")
        $("#notifications-list").hide()
        $("#no-notifications-placeholder").show()
    } else {
        $(".notification-badge").text(notificationCount)
        $("#notifications-card h1").text(`Notifications (${notificationCount})`)
    }
}

function formatNotificationDate(date) {
    const locale = navigator.language

    const now = new Date()
    const diff = now - date

    const seconds = Math.floor(diff / 1000)
    const minutes = Math.floor(seconds / 60)
    const hours = Math.floor(minutes / 60)
    const days = Math.floor(hours / 24)

    const relative = new Intl.RelativeTimeFormat(locale, { style: "narrow" })
    const time = new Intl.DateTimeFormat(locale, { hour: "numeric", minute: "numeric" })
    const dayTime = new Intl.DateTimeFormat(locale, { weekday: "short", hour: "numeric", minute: "numeric" })
    const dateTime = new Intl.DateTimeFormat(locale, { month: "numeric", day: "numeric", hour: "numeric", minute: "numeric" })
    const fullDate = new Intl.DateTimeFormat(locale, { year: "numeric", month: "numeric", day: "numeric" })

    if (seconds < 60) {
        // Within a minute
        return "now"
    } else if (minutes < 60) {
        // Within an hour
        return relative.format(-minutes, "minutes").replace(" min", "m")
    } else if (date.getDate() === now.getDate() - 1) {
        // Within yesterday
        return `Yesterday, ${time.format(date)}`
    } else if (hours < 4) {
        // Within 4 hours
        return relative.format(-hours, "hours").replace(" hr", "h")
    } else if (hours < 24 && date.getDate() === now.getDate()) {
        // Within today
        return time.format(date)
    } else if (days < 7) {
        // Within a week
        return dayTime.format(date)
    } else if (days < 365) {
        // Within a year
        return dateTime.format(date).replace(",", "")
    } else {
        // Over a year ago
        return fullDate.format(date)
    }
}
