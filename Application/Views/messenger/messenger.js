class MessageManager {
    constructor(parent, author, receiver, data) {
        this.$parent = parent
        this.$author = author
        this.$receiver = receiver
        this.$data = data
    }

    displayMessages() {
        this.$parent.innerHTML = ''
        let lastMessage = this.$data.messages[0];
        lastMessage.timestamp = new Date(lastMessage.timestamp)

        this.$parent.appendChild(this.$makeChatItem(lastMessage.author))

        this.$data.messages.forEach(element => {
            element.timestamp = new Date(element.timestamp)
            if (element.author == lastMessage.author && element.timestamp - lastMessage.timestamp <= 300000) {
                this.$addMessageToLastChatItem(element.text)
            } else {
                if (element.timestamp - lastMessage.timestamp > 300000) {
                    this.$addTimestampToParent(element.timestamp)
                }

                this.$parent.appendChild(this.$makeChatItem(element.author))
                this.$addMessageToLastChatItem(element.text)

                lastMessage = element
            }
        })
    }

    addMessage(author, message) { // make this add message to this.$data.messages and refresh
        this.$data.messages.push({
            author: author,
            timestamp: Date.now(),
            text:message
        })
        this.displayMessages()
        this.$parent.scrollTo(0, this.$parent.scrollHeight);
    }

    $makeChatItem(author) {
        let newChatItem = document.createElement('div')
        newChatItem.classList.add('chat-item')
        newChatItem.appendChild(document.createElement('img'))
        if (author == this.$author) {
            newChatItem.childNodes[0].src = this.$data.ownPicture
            newChatItem.style.marginLeft = 'auto'
        } else {
            newChatItem.childNodes[0].src = this.$data.targetPicture
        }
        newChatItem.childNodes[0].width = newChatItem.childNodes[0].height = 30
        newChatItem.childNodes[0].alt = 'Profile Picture'
        newChatItem.childNodes[0].style.marginRight = '10px'
        newChatItem.childNodes[0].classList.add('profile-pic')
        newChatItem.appendChild(document.createElement('div'))
        let authorDiv = document.createElement('div')
        authorDiv.classList.add('author')
        authorDiv.innerHTML = author
        newChatItem.childNodes[1].appendChild(authorDiv)
        
        return newChatItem;
    }

    $addMessageToLastChatItem( messageText) {
        let textDiv = document.createElement('div')
        textDiv.classList.add('message')
        textDiv.innerHTML = messageText
        this.$parent.childNodes[this.$parent.childNodes.length - 1].childNodes[1].appendChild(textDiv)
    }

    $addTimestampToParent(timestamp) {
        let timestampDiv = document.createElement('div')
        timestampDiv.classList.add('timestamp')
        timestampDiv.innerHTML = `${timestamp.getDay()}/${timestamp.getMonth()}/${timestamp.getYear() + 1900} ${timestamp.getHours()}:${timestamp.getMinutes()}`
        this.$parent.appendChild(timestampDiv)
    }
}

let messageManager = null;
let xhr = new XMLHttpRequest()
xhr.open('GET', `/messenger/messages/${recipient}`, true)
xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
xhr.onload = function () {
    if (this.status == 200) {
        console.log()
        let parsedResponse = JSON.parse(this.response)
        messageManager = new MessageManager(
            document.querySelector('.messages'),
            username,
            recipient,
            parsedResponse
        )

        messageManager.displayMessages()
    }
}
xhr.send()
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

window.addEventListener('load', function () {
    let messages = document.querySelector('.messages');
    messages.scrollTo(0, messages.scrollHeight);
});


function setStatus(status, otherwise) {
    const statusNode = document.querySelector('.user-status');
    if (statusNode.textContent !== status) {
        statusNode.textContent = status;
        setTimeout(function () {
            statusNode.textContent = otherwise;
        }, 2000);
    }
}

if (socket) {
    let input = document.querySelector('.message-field'),
        prompt = document.querySelector('.prompt');

    socket.emit('request status', recipient);

    input.addEventListener('keydown', (event) => {
        if (event.which === 13 && !event.shiftKey) {
            if (!/^\s*$/m.test(input.value)) {
                messageManager.addMessage(username, input.value)
                socket.emit('chat message', {
                    content: input.value,
                    to: recipient
                });
                input.value = '';
                if (prompt) prompt.style.display = 'none';
            }
            event.preventDefault();
        } else {
            socket.emit('chat status', {
                content: ' is typing...',
                to: recipient
            });
        }
    });

    socket.on('user status', function (data) {
        if (data) {
            document.querySelector('.user-status').textContent = 'online'
            document.querySelector('.user-status').style.color = 'green'
        } else {
            document.querySelector('.user-status').textContent = 'offline'
            document.querySelector('.user-status').style.color = 'gray'
        }
    })

    socket.on("connect_error", (err) => {
        if (err.message === "invalid username") {
            document.querySelector('.messages').textContent = "Something unexpected happened";
        }
    });

    socket.on('chat message', function ({
        content,
        from
    }) {
        if (from === recipient || from === username) {
            messageManager.addMessage(from, content)
        } else {
            let chats = document.querySelectorAll('.chat-button-text');
            let flag = false;
            chats.forEach(element => {
                if (element.textContent.trim() == from) {
                    flag = true;
                    element.parentElement.className += ' notification';
                }
            });

            if (!flag) {
                let chats = document.querySelector('.user-chats');
                let newChat = document.createElement('a');
                newChat.href = '/messenger/' + from;
                let newChatButton = document.createElement('div');
                newChatButton.className = 'chat-button notification';
                let newChatButtonText = document.createElement('div');
                newChatButtonText.className = 'chat-button-text';
                newChatButtonText.style = 'margin-left: 15px';
                newChatButtonText.textContent = from;
                newChatButton.appendChild(newChatButtonText);
                newChat.appendChild(newChatButton);
                chats.appendChild(newChat);
            }
        }
    });

    socket.on('chat status', function ({
        content,
        from
    }) {
        setStatus(content, 'online');
    });

    socket.onAny((event, ...args) => {
        console.log(event, args);
    });
}