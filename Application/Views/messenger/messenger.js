import MessageBox from "./messageBox/MessageBox.js"

const xhr = new XMLHttpRequest();
xhr.open('GET', `/messenger/messages/${recipient}`, true);

xhr.onload = function () {
    if (this.status == 200) {
        const parsedResponse = JSON.parse(this.response);
        const messageManager = new MessageBox(
            document.querySelector('.messages'),
            username,
            recipient,
            parsedResponse
        );

        messageManager.displayMessages();
    }
}

xhr.send();

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
                messageManager.addMessage(username, input.value);
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
            document.querySelector('.user-status').textContent = 'online';
            document.querySelector('.user-status').style.color = 'green';
        } else {
            document.querySelector('.user-status').textContent = 'offline';
            document.querySelector('.user-status').style.color = 'gray';
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
            messageManager.addMessage(from, content);
        } else {
            const chats = document.querySelectorAll('.chat-button-text');
            let flag = false;
            chats.forEach(element => {
                if (element.textContent.trim() == from) {
                    flag = true;
                    element.parentElement.className += ' notification';
                }
            });

            if (!flag) {
                const chats = document.querySelector('.user-chats');
                const newChat = document.createElement('a');
                newChat.href = '/messenger/' + from;
                const newChatButton = document.createElement('div');
                newChatButton.className = 'chat-button notification';
                const newChatButtonText = document.createElement('div');
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