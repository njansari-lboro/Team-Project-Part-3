async function fetchChats(filterText) {
    try {
        let url = "/api/chats"

        if (filterText) {
            url += `?filter_text=${filterText}`
        }

        const response = await fetch(url)

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}

async function fetchChat(chatID) {
    try {
        const response = await fetch(`/api/chats/${chatID}`)

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}

async function fetchChatIcon(chatIconName) {
    try {
        const formData = new FormData()
        formData.append("icon_name", chatIconName)

        const response = await fetch("chat/chat-functions.php?task=get_chat_icon", {
            method: "POST",
            body: formData
        })

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}

async function uploadChatIcon(iconFile) {
    try {
        const formData = new FormData()
        formData.append("upload_chat_icon", iconFile)

        const response = await fetch("chat/chat-functions.php?task=upload_chat_icon", {
            method: "POST",
            body: formData
        })

        const data = await response.json()

        if (data.success) {
            return data.file_name
        } else {
            throw new Error(data.message)
        }
    } catch (error) {
        console.error("Error uploading image:", error)
        return false
    }
}

async function addChat(name, isPrivate, iconName) {
    try {
        const formData = new FormData()

        if (name) {
            formData.append("name", name)
        }

        formData.append("is_private", isPrivate)

        if (iconName) {
            formData.append("icon_name", iconName)
        }

        const response = await fetch("/api/chats", {
            method: "POST",
            body: formData
        })

        return response.ok
    } catch (error) {
        console.error("Error adding chat:", error)
        return false
    }
}

async function updateChat(chatID, name, iconName) {
    try {
        const putData = {}

        if (name) {
            putData.name = name
        }

        if (iconName) {
            putData.icon_name = iconName
        }

        const response = await fetch(`/api/chats/${chatID}`, {
            method: "PUT",
            body: JSON.stringify(putData)
        })

        return response.ok
    } catch (error) {
        console.error("Error updating chat:", error)
        return false
    }
}

async function fetchMembersForChat(chatID) {
    try {
        const response = await fetch(`/api/chats/${chatID}/users`)

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}

async function addUserToChat(userID, chatID) {
    try {
        const formData = new FormData()
        formData.append("user_id", userID)

        const response = await fetch(`/api/chats/${chatID}/users`, {
            method: "POST",
            body: formData
        })

        return response.ok
    } catch (error) {
        console.error("Error adding user to chat:", error)
        return false
    }
}

async function removeUserFromChat(userID, chatID) {
    try {
        const response = await fetch(`/api/chats/${chatID}/users/${userID}`, { method: "DELETE" })
        return response.ok
    } catch (error) {
        console.error("Error removing user from chat:", error)
        return false
    }
}

async function fetchMessagesForChat(chatID) {
    try {
        const response = await fetch(`/api/chats/${chatID}/messages`)

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}

async function deleteMessage(chatID, messageID) {
    try {
        const response = await fetch(`/api/chats/${chatID}/messages/${messageID}`, { method: "DELETE" })
        return response.ok
    } catch (error) {
        console.error("Error deleting message:", error)
        return false
    }
}

async function fetchUser(userID) {
    try {
        const formData = new FormData()
        formData.append("user_id", userID)

        const response = await fetch("chat/chat-functions.php?task=get_user", {
            method: "POST",
            body: formData
        })

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}

async function fetchUserProfileImage(userID) {
    try {
        const formData = new FormData()
        formData.append("user_id", userID)

        const response = await fetch("chat/chat-functions.php?task=get_user_profile_image", {
            method: "POST",
            body: formData
        })

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}

async function fetchAllUsers() {
    try {
        const response = await fetch("chat/chat-functions.php?task=fetch_users")

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
    }
}

const chatInfoHTML = async (chat, iconSize) => {
    const chatMembers = await fetchMembersForChat(chat.id)

    let icon
    let iconPath = null
    let chatName

    if (chat.is_private) {
        if (chatMembers.length !== 2) {
            console.error(`Private chat (${chat.id}) has invalid number of users (${chatMembers.length})`)
        }

        const otherUserID = chatMembers.filter(member => member.user_id !== user.id)[0].user_id
        const otherUser = await fetchUser(otherUserID)

        chatName = otherUser.full_name
        iconPath = otherUser.profile_image_path
    } else {
        chatName = chat.name

        if (chat.icon_name) {
            iconPath = await fetchChatIcon(chat.icon_name)
        }
    }

    if (iconPath) {
        icon = `<img class="chat-icon" src="${iconPath}" alt="Chat icon">`
    } else {
        icon = `
            <load-svg class="chat-icon" src="../assets/${chat.is_private ? "profile-icon" : "chat-icon"}.svg">
                <style shadowRoot>
                    svg {
                        width: ${iconSize}px;
                        height: ${iconSize}px;
                    }

                    .fill {
                        fill: var(--fill-color);
                    }
                </style>
            </load-svg>
            `
    }

    return { icon: icon, name: chatName }
}

const messageGroupTimestampHTML = (date) => {
    const dateTime = formatMessageGroupTimestamp(date)
    return `
    <div class="message-group-timestamp">
        <strong>${dateTime.date}</strong> ${dateTime.time}
    </div>
    `
}

const messageHTML = (body, canDelete) => {
    const deleteButton = canDelete ? `
    <button class="message-delete-button">
        <load-svg class="message-delete-icon" src="../assets/close-icon.svg">
            <style shadowRoot>
                svg {
                    width: 0.6em;
                    height: 0.6em;
                }

                .fill {
                    fill: var(--secondary-label-color)
                }
            </style>
        </load-svg>
    </button>` : ""

    return `
    <div class="message">
        <p>${body}</p>
        ${deleteButton}
    </div>
    `
}

const sentMessagesContainerHTML = (messages) => {
    return `
    <div class="messages-container sent">
        ${messages.join("")}
    </div>
    `
}

const arrivedMessagesContainerHTML = (messages) => {
    return `
    <div class="messages-container arrived">
        ${messages.join("")}
    </div>
    `
}

const arrivedUserMessagesContainerHTML = (user, messages) => {
    let icon
    const profileImagePath = user.profile_image_path

    if (profileImagePath) {
        icon = `<img class="message-user-profile-icon" src="${profileImagePath}" alt="User profile image">`
    } else {
        icon = `
        <picture>
            <source class="message-user-profile-icon-dark" srcset="../img/default-user-profile-image-dark.png" media="(prefers-color-scheme: dark)">
            <img class="message-user-profile-icon" src="../img/default-user-profile-image.png" alt="User profile image">
        </picture>
        `
    }

    return `
    <div class="message-user-container">
        ${icon}

        <div class="messages-container arrived">
            <div class="message-user-name">${user.full_name}</div>
            ${messages.join("")}
        </div>
    </div>
    `
}

const getSelectedChatID = () => localStorage.getItem("selectedChat")

async function displayChatsList(chats) {
    const chatsList = document.getElementById("chats-list")

    chatsList.innerHTML = ""

    for (const chat of chats) {
        const chatMembers = await fetchMembersForChat(chat.id)

        const memberCount = chatMembers.length
        const memberCountText = (() => {
            switch (memberCount) {
            case 0:
                return "No members"
            case 1:
                return "1 member"
            default:
                return `${memberCount} members`
            }
        })()

        let icon = ""
        let iconPath = null
        let chatName = ""

        if (chat.is_private) {
            if (chatMembers.length !== 2) {
                console.error(`Private chat (${chat.id}) has invalid number of users (${chatMembers.length})`)
            }

            const otherUserID = chatMembers.filter(member => member.user_id !== user.id)[0].user_id
            const otherUser = await fetchUser(otherUserID)

            chatName = otherUser.full_name
            iconPath = otherUser.profile_image_path
        } else {
            chatName = chat.name

            if (chat.icon_name) {
                iconPath = await fetchChatIcon(chat.icon_name)
            }
        }

        if (iconPath) {
            icon = `<img class="chat-icon" src="${iconPath}" alt="Chat icon">`
        } else {
            icon = `
            <load-svg class="chat-icon" src="../assets/${chat.is_private ? "profile-icon" : "chat-icon"}.svg">
                <style shadowRoot>
                    svg {
                        width: 30px;
                        height: 30px;
                    }

                    .fill {
                        fill: var(--fill-color);
                    }
                </style>
            </load-svg>
            `
        }

        if (chat.is_private) {
            icon = `
            <div class="chat-icon-container">
                ${icon}
            
                <load-svg class="chat-private-icon" src="../assets/private-icon.svg">
                    <style shadowRoot>
                        svg {
                            width: 15px;
                            height: 15px;
                        }

                        .fill {
                            fill: var(--fill-color);
                        }
                    </style>
                </load-svg>
            </div>
            `
        }

        const chatRowHTML = `
        <div class="chat-row ${getSelectedChatID() == chat.id ? "selected" : ""}">
            ${icon}

            <div class="chat-row-content">
                <div class="chat-row-header">
                    <div class="chat-name">${chatName}</div>
                    <div class="chat-last-updated">${formatChatLastUpdated(new Date(chat.last_updated))}</div>
                </div>

                <div class="chat-member-count">${memberCountText}</div>
            </div>
        </div>
        `

        const chatRowElement = document.createElement("div")
        chatRowElement.innerHTML = chatRowHTML

        chatRowElement.querySelector(".chat-row").addEventListener("click", async function() {
            document.querySelectorAll(".chat-row").forEach(row => {
                row.classList.remove("selected")
            })

            this.classList.add("selected")

            localStorage.setItem("selectedChat", chat.id)

            await displayConversationMessages()
        })

        chatsList.appendChild(chatRowElement)
    }

    const chatRows = document.querySelectorAll(".chat-row")

    const isSelected = Array.from(chatRows).some(e => e.classList.contains("selected"))

    if (!isSelected && chatRows.length > 0) {
        chatRows[0].classList.add("selected")
        localStorage.setItem("selectedChat", chats[0].id)
    }
}

async function parseMessagesForChat(chat) {
    const messages = await fetchMessagesForChat(chat.id)

    let messageGroups = []
    let currentGroup = null
    let currentContainer = null

    for (let i = 0; i < messages.length; i++) {
        const message = messages[i]
        const messageDate = new Date(message.date_posted)

        if (!currentGroup || Math.abs(messageDate - currentGroup.date) > 1000 * 60 * 60) {
            currentGroup = {
                date: messageDate,
                containers: []
            }

            messageGroups.push(currentGroup)
        }

        if (
            !currentContainer
            || Math.abs(messageDate - currentContainer.date) > 1000 * 60
            || message.author_id !== currentContainer.author_id
        ) {
            if (!chat.is_private && message.author_id !== user.id) {
                const user = await fetchUser(message.author_id)

                currentContainer = {
                    type: "arrivedUser",
                    user: user,
                    messages: [message.body]
                }
            } else {
                currentContainer = {
                    type: message.author_id === user.id ? "sent" : "arrived",
                    messages: [message.body]
                }
            }

            currentGroup.containers.push(currentContainer)
        } else {
            currentContainer.messages.push(message.body)
        }

        currentContainer.date = messageDate
        currentContainer.author_id = message.author_id
    }

    return messageGroups
}

async function displayConversationMessages() {
    const conversation = document.getElementById("conversation-messages")

    const chat = await fetchChat(getSelectedChatID())

    if (chat.is_private) {
        conversation.classList.remove("group")
    } else {
        conversation.classList.add("group")
    }

    conversation.innerHTML = ""

    const messages = await parseMessagesForChat(chat)

    for (const messageGroup of messages) {
        conversation.innerHTML += messageGroupTimestampHTML(messageGroup.date)

        for (const messageContainer of messageGroup.containers) {
            const messages = messageContainer.messages.map(messageHTML)

            switch (messageContainer.type) {
            case "sent":
                conversation.innerHTML += sentMessagesContainerHTML(messages)
                break
            case "arrived":
                conversation.innerHTML += arrivedMessagesContainerHTML(messages)
                break
            case "arrivedUser":
                conversation.innerHTML += arrivedUserMessagesContainerHTML(messageContainer.user, messages)
                break
            }
        }
    }

    resetConversationScrollPosition()
}

fetchChats().then(async (chats) => {
    await displayChatsList(chats)
    await displayConversationMessages()
})

document.querySelectorAll(".toggle-chat-list").forEach(toggle => {
    toggle.addEventListener("click", () => {
        document.getElementById("left-panel").classList.toggle("expanded")
    })
})

document.querySelector(".sidebar-input").addEventListener("input", async function() {
    let chats

    if (this.value.trim().length === 0) {
        chats = await fetchChats()
    } else {
        chats = await fetchChats(this.value.trim())
    }

    await displayChatsList(chats)
})

function resetConversationScrollPosition() {
    const conversationMessages = document.getElementById("conversation-messages")
    conversationMessages.scrollTop = conversationMessages.scrollHeight
}

document.getElementById("compose-message-input").addEventListener("input", function() {
    const submitButton = document.getElementById("compose-message-submit")

    if (this.value.trim().length === 0) {
        if (!submitButton.hasAttribute("disabled")) {
            submitButton.setAttribute("disabled", "")
        }
    } else {
        if (submitButton.hasAttribute("disabled")) {
            submitButton.removeAttribute("disabled")
        }
    }
})

document.getElementById("compose-message-input").addEventListener("change", submitMessage)
document.getElementById("compose-message-submit").addEventListener("click", submitMessage)

async function submitMessage(event) {
    event.preventDefault()

    const messageInput = document.getElementById("compose-message-input")

    if (messageInput.value.trim().length === 0) return

    try {
        const formData = new FormData()
        formData.append("body", messageInput.value)

        const response = await fetch(`/api/chats/${getSelectedChatID()}/messages`, {
            method: "POST",
            body: formData
        })

        if (!response.ok) throw new Error("Failed to fetch data")

        await displayConversationMessages()
    } catch (error) {
        console.error("Error fetching data:", error)
    } finally {
        messageInput.value = ""
        document.getElementById("compose-message-submit").setAttribute("disabled", "")
    }
}

function formatChatLastUpdated(date) {
    const components = getRelativeDateComponents(date)

    const locale = navigator.language

    const time = new Intl.DateTimeFormat(locale, { hour: "numeric", minute: "numeric" })
    const day = new Intl.DateTimeFormat(locale, { weekday: "long" })
    const fullDate = new Intl.DateTimeFormat(locale, { year: "numeric", month: "numeric", day: "numeric" })

    if (components.seconds < 60) {
        // Within a minute
        return "Now"
    } else if (components.date.getDate() === components.now.getDate() - 1) {
        // Within yesterday
        return "Yesterday"
    } else if (components.hours < 24 && components.date.getDate() === components.now.getDate()) {
        // Within today
        return time.format(components.date)
    } else if (components.days < 7) {
        // Within a week
        return day.format(components.date)
    } else {
        // Over a week ago
        return fullDate.format(components.date)
    }
}

function formatMessageGroupTimestamp(date) {
    const components = getRelativeDateComponents(date)

    const locale = navigator.language

    const time = new Intl.DateTimeFormat(locale, { hour: "numeric", minute: "numeric" })
    const day = new Intl.DateTimeFormat(locale, { weekday: "long" })
    const shortDate = new Intl.DateTimeFormat(locale, { month: "short", weekday: "short", day: "numeric" })

    let dateString
    let timeString

    if (components.date.getDate() === components.now.getDate() - 1) {
        // Within yesterday
        dateString = "Yesterday"
        timeString = time.format(components.date)
    } else if (components.hours < 24 && components.date.getDate() === components.now.getDate()) {
        // Within today
        dateString = "Today"
        timeString = time.format(components.date)
    } else if (components.days < 7) {
        // Within a week
        dateString = day.format(components.date)
        timeString = time.format(components.date)
    } else {
        // Over a week ago
        dateString = shortDate.format(components.date)
        timeString = `at ${time.format(components.date)}`
    }

    return { date: dateString, time: timeString }
}
