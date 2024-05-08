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

async function fetchMembersForChat(chatID) {
    try {
        const response = await fetch(`/api/chats/${chatID}/users`)

        if (!response.ok) throw new Error("Failed to fetch data")

        return await response.json()
    } catch (error) {
        console.error("Error fetching data:", error)
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

const messageGroupTimestampHTML = (date) => {
    const dateTime = formatMessageGroupTimestamp(date)
    return `
    <div class="message-group-timestamp">
        <strong>${dateTime.date}</strong> ${dateTime.time}
    </div>
    `
}

const messageHTML = (body) => {
    return `
    <div class="message">
        <p>${body}</p>
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

/*
//fetch("http://35.189.103.73/api/chats")
//	.then(function(response){ return response.json()})
//	.then(function(data) {
//        console.log(data);
//    });


// GET chats
chatsJSON = "[{\"id\":\"1\",\"name\":\"Test Chat\",\"is_private\":\"0\",\"icon_name\":null,\"last_updated\":\"2024-04-25 11:34:56\"}," +
    "{\"id\":\"2\",\"name\":\"private1\",\"is_private\":\"1\",\"icon_name\":null,\"last_updated\":\"2024-04-25 15:01:45\"}," +
    "{\"id\":\"3\",\"name\":\"private2\",\"is_private\":\"1\",\"icon_name\":null,\"last_updated\":\"2024-04-26 9:54:02\"}]"

//chatsJSON='[]';


chat1usersJSON = "[{\"user_id\":\"1\"},{\"user_id\":\"2\"},{\"user_id\":\"3\"}]"

chat2usersJSON = "[{\"user_id\":\"1\"},{\"user_id\":\"2\"}]"

chat3usersJSON = "[{\"user_id\":\"1\"},{\"user_id\":\"3\"}]"


chat1messagesJSON = "[{\"id\":\"1\",\"chat_id\":\"1\",\"author_id\":\"1\",\"body\":\"hi\",\"date_posted\":\"2024-04-25 9:00:02\"}," +
    "{\"id\":\"2\",\"chat_id\":\"1\",\"author_id\":\"2\",\"body\":\"hello\",\"date_posted\":\"2024-04-25 9:54:02\"}," +
    "{\"id\":\"3\",\"chat_id\":\"1\",\"author_id\":\"3\",\"body\":\"whats up\",\"date_posted\":\"2024-04-25 11:34:56\"}]"

chat2messagesJSON = "[{\"id\":\"1\",\"chat_id\":\"2\",\"author_id\":\"1\",\"body\":\"how r u\",\"date_posted\":\"2024-04-25 15:00:01\"}," +
    "{\"id\":\"2\",\"chat_id\":\"2\",\"author_id\":\"2\",\"body\":\"very bad\",\"date_posted\":\"2024-04-25 15:01:45\"}]"

chat3messagesJSON = "[{\"id\":\"1\",\"chat_id\":\"3\",\"author_id\":\"1\",\"body\":\"im bored\",\"date_posted\":\"2024-04-25 9:50:02\"}," +
    "{\"id\":\"2\",\"chat_id\":\"3\",\"author_id\":\"3\",\"body\":\"same\",\"date_posted\":\"2024-04-26 9:54:02\"}]"


let chatsJS = JSON.parse(chatsJSON)

let chat1usersJS = JSON.parse(chat1usersJSON)
let chat2usersJS = JSON.parse(chat2usersJSON)
let chat3usersJS = JSON.parse(chat3usersJSON)

let chat1messagesJS = JSON.parse(chat1messagesJSON)
let chat2messagesJS = JSON.parse(chat2messagesJSON)
let chat3messagesJS = JSON.parse(chat3messagesJSON)


function getMessages(chat_id) {

    if (chat_id == "1") {
        return chat1messagesJS
    } else if (chat_id == "2") {
        return chat2messagesJS
    } else if (chat_id == "3") {
        return chat3messagesJS
    } else {
        return ""
    }

}

function getUsers(chat_id) {

    if (chat_id == "1") {
        return chat1usersJS
    } else if (chat_id == "2") {
        return chat2usersJS
    } else if (chat_id == "3") {
        return chat3usersJS
    } else {
        return ""
    }

}


//console.log(document.querySelector("#mlist").innerHTML);

//let text="<li class='message-title' style='font-size:18px;'>Recent</li><br>";
let text = "<div>"
for (i = 0; i < chatsJS.length; i++) {

//text+=                            '<li>'+
    //                               '<a href="#">'+
    //                           '<span class="message-info">'+
    //                              '<span class="message-name">Angela</span>'+
    //                            '<span class="message-text">Thank you, I received your email!</span>'+
    //                      '</span>'+
    //                    '<span>'+
    //                      '<span class="message-unread">1</span>'+
    //                     '<span class="message-time">11:40</span>'+
    //                '</span>'+
    //                  '</a>'+
    //             '</li>';

    console.log(chatsJS[i].id)
    text += "<div class=\"chats\" id=\"" + chatsJS[i].id + "\">"
    text += chatsJS[i].name + " " + chatsJS[i].last_updated
    text += "</div>"


}
text += "</div>"
document.querySelector("#mlist").innerHTML = text
//console.log(document.querySelector("#mlist").innerHTML);


searchInput = document.querySelector(".search")
Container = document.getElementById("mlist")
//gets all the topics so they will all come back when search removed
originalMessages = Array.from(Container.querySelectorAll(".chats"))
searchInput.addEventListener("input", performSearch)

//performSearch();
function performSearch() {
    const searchValue = searchInput.value.toLowerCase()
//shows or hides topics based on the search value inputted by user
    originalMessages.forEach(message => {
        const Name = message.textContent.toLowerCase()


        if (Name.includes(searchValue) || searchValue === "") {
            message.style.display = "block"
        } else {
            message.style.display = "none"
        }
    })
}

originalMessages.forEach(message => {
    message.addEventListener("click", showChat)
})


//showChat()
let topChat = document.getElementById("1")
if (topChat != null) {
    document.getElementById("1").click()
}


function showChat() {
    let chat = ""
    if (chatsJS.length == 0) {
        return
    } else if (this.id == undefined) {
        chat = document.querySelector(".chats")
    } else {
        chat = this
    }
    document.querySelector("#bottom").style.display = "block"
    let users = getUsers(chat.id)
    let userStr = ""
    for (let i = 0; i < users.length; i++) {
        userStr += "user" + users[i].user_id + " "
    }
    document.querySelector("#header").innerHTML = chat.textContent + "<br>" + userStr
    let currentChat = getMessages(chat.id)
    document.querySelector("#container").innerHTML = displayMessages(currentChat)
}


function displayMessages(messages) {
    let text = ""
    for (let i = 0; i < messages.length; i++) {
        text += "<div class=\"message\" id=\"message " + messages[i].id + "\">user" + messages[i].author_id + "<br>" + messages[i].body + "<br>" + messages[i].date_posted + "</div>"
    }

    return text

}


document.querySelector("#sendMessage").addEventListener("click", function() {
    let textBox = document.querySelector("#typeMessage")
    let message = textBox.value
    console.log(message)
    textBox.value = ""
    console.log(chatsJS)

})

document.querySelector("#typeMessage").addEventListener("keyup", function() {
    if (event.key == "Enter" && document.querySelector("#typeMessage").value != "") {
        document.querySelector("#sendMessage").click()
    }
})
*/
