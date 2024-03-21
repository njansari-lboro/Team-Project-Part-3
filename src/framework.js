// DEALS WITH LOGIN LOGIC/SCREENS AND REDIRECTS TO MAIN INDEX, PAGE DASHBOARD

$(() => {
    if (inviteUser) {
        register(inviteUser.id)

        $("#register-first-name-input").val(inviteUser.first_name)
        $("#register-last-name-input").val(inviteUser.last_name)
    } else {
        restart()
    }

    $("#emailForm").show()
    $("#emailDisplay").hide()
    $("#passwordInput").hide()
    $("#forgotPassword").hide()
    $("#passwordField").hide()
    $("#resetPassword").hide()
    $("#tryAgain").hide()
    $("#mainBtn").show()
    $("#notRegistered").hide()

    $("#mainBtn").prop("disabled", $("#emailInput").val().length === 0)

    setTimeout(() => {
        $("#emailInput").focus()
        setTimeout(() => $("#emailInput").click(), 100)
    }, 100)

    $("#emailForm").submit((e) => {
        e.preventDefault()

        if ($("#passwordField").is(":hidden")) {
            validateAndDisplayEmail()
        } else {
            validatePasswordAndLogin()
        }
    })

    $(".form-control").on("input", function (e) {
        e.preventDefault()
        $("#mainBtn").prop("disabled", $(this).val().length === 0)
    })

    $("#changeEmail").click((e) => {
        e.preventDefault()
        restart()
    })

    $("#forgotPassword").click((e) => {
        e.preventDefault()
        showResetPassword()
    })

    $("#tryAgain").click((e) => {
        e.preventDefault()
        restart()
    })

    $("#register-button").click((event) => {
        event.preventDefault()

        const formData = {
            firstName: $("#register-first-name-input").val().trim(),
            lastName: $("#register-last-name-input").val().trim(),
            password: $("#register-password-input").val(),
            userID: $("#hiddenUserId").val()
        };

        $.ajax({
            type: "POST",
            url: "helpers/register.php",
            data: formData,
            dataType: "json",
            success: async (response) => {
                if (response.success) {
                    //successful registration
                    await showDialogAsync("You are now registered!")
                    restart();
                } else {
                    console.log("Registration failed")
                }
            },
            error: (xhr, status, error) => {
                console.log("AJAX Error: ", status, error);
            }
        });
    })
})

// checking email
async function validateAndDisplayEmail() {
    let email = $("#emailInput").val()

    if (!email.match(/^\w+@make-it-all\.co\.uk$/)) {
        $("#emailInput").blur()

        await showDialogAsync(
            "Invalid email address",
            "Please enter a valid @make-it-all.co.uk email address.",
            { title: "Try Again" }
        )

        $("#emailInput").focus()

        return false
    }

    $.ajax({
        type: "POST",
        url: "helpers/emailcheck.php",
        data: {
            email: email,
        },
        dataType: "json",
        success: (response) => {
            if (response.status === "registered") {
                console.log("success")
                $("#emailInput").hide()
                $("#displayedEmail").text(email)
                $("#passwordInput").show()
                $("#passwordField").show()
                $("#emailDisplay").show()
                $("#forgotPassword").show()
                $("#mainBtn").html("Login")

                $("#mainBtn").prop("disabled", $("#passwordInput").val().length === 0)

                setTimeout(() => {
                    $("#passwordInput").focus()
                    setTimeout(() => $("#passwordInput").click(), 100)
                }, 100)
                // } else if (response.status === "unregistered") {
                //     register(response.userID)
            } else {
                console.log("not registered")
                notRecognised(email)
            }
        },
        error: (jqXHR, textStatus, errorThrown) => {
            alert(`Error: ${textStatus}\n${errorThrown}`)
        }
    })
}

// checking password
function validatePasswordAndLogin() {
    let email = $("#emailInput").val()
    let password = $("#passwordInput").val()

    $.ajax({
        type: "POST",
        url: "helpers/login.php",
        data: {
            email: email,
            password: password
        },
        success: async (response) => {
            console.log(response)

            if (response === "true") {
                console.log("success")
                window.location = "pages/?page=dashboard"
            } else {
                $("#passwordInput").blur()

                await showDialogAsync(
                    "Login failed",
                    "Please check your email and password then try again.",
                    { title: "Try Again" }
                )

                $("#passwordInput").focus()
            }
        },
        error: (jqXHR, textStatus, errorThrown) => {
            alert(`Error: ${textStatus}\n${errorThrown}`)
        }
    })
}
// showing/hiding the correct elements
function restart() {
    $(".centered-content").show()
    $("#emailForm").show()
    $("#emailInput").show()
    $("#passwordField").hide()
    $("#mainBtn").html("Next")
    $("#emailDisplay").hide()
    $("#forgotPassword").hide()
    $("#resetPassword").hide()
    $("#tryAgain").hide()
    $("#mainBtn").show()
    $("#notRegistered").hide()
    $("#passwordInput").val("")
    $("#register-account-card").hide()

    $("#mainBtn").prop("disabled", $("#emailInput").val().length === 0)

    setTimeout(() => {
        $("#emailInput").focus()
        setTimeout(() => $("#emailInput").click(), 100)
    }, 100)
}

function notRecognised(email) {
    $("#notRegistered span").text(email)
    $("#emailForm").hide()
    $("#emailInput").hide()
    $("#passwordField").hide()
    $("#emailDisplay").hide()
    $("#forgotPassword").hide()
    $("#mainBtn").hide()
    $("#notRegistered").show()
    $("#tryAgain").show()
}

function showResetPassword() {
    $("#emailForm").hide()
    $("#emailInput").hide()
    $("#passwordField").hide()
    $("#emailDisplay").hide()
    $("#forgotPassword").hide()
    $("#mainBtn").hide()
    $("#resetPassword").show()
    $("#tryAgain").show()
}

function register(id) {
    $("#hiddenUserId").val(id);

    $(".centered-content").hide()
    $("#register-account-card").show()

    $("#register-first-name-input").change(checkIfAccountProfileCanRegister)
    $("#register-last-name-input").change(checkIfAccountProfileCanRegister)
    // $("#register-password-input").change(checkIfAccountProfileCanRegister)
    // $("#register-confirm-password-input").change(checkIfAccountProfileCanRegister)

    $("#register-password-input").attr("type", "password")

    $("#register-password-input").css("background-color", "rgba(255, 255, 255, 0.85)")
    $("#register-confirm-password-input").css("background-color", "rgba(255, 255, 255, 0.85)")

    $("#register-password-input").on("input", () => validateNewPassword())

    $("#register-password-input").change(() => {
        const newInput = $("#register-password-input")
        const confirmInput = $("#register-confirm-password-input")

        const isValidPassword = validateNewPassword()

        if (isValidPassword && confirmInput.val() === newInput.val()) {
            newInput.css("background-color", "rgba(255, 255, 255, 0.85)")
            confirmInput.css("background-color", "rgba(255, 255, 255, 0.85)")

            checkIfAccountProfileCanRegister()
        } else if (isValidPassword) {
            newInput.css("background-color", "rgba(255, 255, 255, 0.85)")

            confirmInput.focus()
        } else if (!confirmInput.val()) {
            newInput.css("background-color", "rgba(255, 59, 48, 0.2)")

            $("#register-button").prop("disabled", true)
        } else {
            newInput.css("background-color", "rgba(255, 59, 48, 0.2)")
            confirmInput.css("background-color", "rgba(255, 59, 48, 0.2)")

            $("#register-button").prop("disabled", true)
        }
    })

    $("#register-confirm-password-input").change(() => {
        const newInput = $("#register-password-input")
        const confirmInput = $("#register-confirm-password-input")

        const isValidPassword = validateNewPassword()

        if (isValidPassword && confirmInput.val() === newInput.val()) {
            newInput.css("background-color", "rgba(255, 255, 255, 0.85)")
            confirmInput.css("background-color", "rgba(255, 255, 255, 0.85)")

            checkIfAccountProfileCanRegister()
        } else {
            newInput.css("background-color", "rgba(255, 59, 48, 0.2)")
            confirmInput.css("background-color", "rgba(255, 59, 48, 0.2)")

            $("#register-button").prop("disabled", true)
        }
    })

    $("#register-password-input-container").mouseleave(() => {
        $("#register-password-input").attr("type", "password")
        $("#show-password-icon").show()
        $("#hide-password-icon").hide()
    })

    $("#show-hide-password-button").click(() => {
        $("#show-password-icon").toggle()
        $("#hide-password-icon").toggle()

        if ($("#show-password-icon").is(":visible")) {
            $("#register-password-input").attr("type", "password")
        } else {
            $("#register-password-input").attr("type", "text")
        }
    })

    $("#show-password-icon").show()
    $("#hide-password-icon").hide()
}

function checkIfAccountProfileCanRegister() {
    const firstName = $("#register-first-name-input").val().trim()
    const lastName = $("#register-last-name-input").val().trim()
    const password = $("#register-password-input").val().trim()
    const confirmPassword = $("#register-confirm-password-input").val().trim()

    let registerIsDisabled = firstName.length === 0 || lastName.length === 0 || password.length === 0 || confirmPassword.length === 0

    $("#register-button").prop("disabled", registerIsDisabled)
}

function validateNewPassword() {
    const password = $("#register-password-input").val()

    const minLengthRegex = /.{12,}/
    const uppercaseRegex = /[A-Z]/
    const lowercaseRegex = /[a-z]/
    const numberRegex = /[0-9]/
    const symbolRegex = /[\W_]/

    let isValid = true

    if (minLengthRegex.test(password)) {
        $("#register-password-reqs-min-chars").css("color", "var(--green-color)")
    } else {
        $("#register-password-reqs-min-chars").css("color", "var(--red-color)")
        isValid = false
    }

    if (uppercaseRegex.test(password)) {
        $("#register-password-reqs-uppercase").css("color", "var(--green-color)")
    } else {
        $("#register-password-reqs-uppercase").css("color", "var(--red-color)")
        isValid = false
    }

    if (lowercaseRegex.test(password)) {
        $("#register-password-reqs-lowercase").css("color", "var(--green-color)")
    } else {
        $("#register-password-reqs-lowercase").css("color", "var(--red-color)")
        isValid = false
    }

    if (numberRegex.test(password)) {
        $("#register-password-reqs-number").css("color", "var(--green-color)")
    } else {
        $("#register-password-reqs-number").css("color", "var(--red-color)")
        isValid = false
    }

    if (symbolRegex.test(password)) {
        $("#register-password-reqs-symbol").css("color", "var(--green-color)")
    } else {
        $("#register-password-reqs-symbol").css("color", "var(--red-color)")
        isValid = false
    }

    return isValid
}
