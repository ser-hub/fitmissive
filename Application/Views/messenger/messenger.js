import MessageBox from "./messageBox/MessageBox.js"
import "./messenger-onload.js"

let notification = null;
let messageManager = null;
const input = document.querySelector('.message-field');
const chats = document.querySelectorAll('.chat-button-text');
const prompt = document.querySelector('.prompt');

input.addEventListener('keydown', (event) => {
    if (event.which === 13 && !event.shiftKey) {
        if (!/^\s*$/m.test(input.value)) {
            sendMsg(input.value);
            input.value = '';
            if (prompt) prompt.style.display = 'none';
        }
        event.preventDefault();
    } else {
        if (socket) {
            socket.emit('chat status', {
                content: ' is typing...',
                to: recipient
            });
        }
    }
});

const sendBtn = document.querySelector('.message-send-btn');
sendBtn.onclick = function () {
    sendMsg(input.value);
    input.value = '';
}


const xhr = new XMLHttpRequest();
xhr.open('GET', `/data/messages/${recipient}`, true);

xhr.onload = function () {
    if (this.status == 200) {
        const parsedResponse = JSON.parse(this.response);
        messageManager = new MessageBox(
            document.querySelector('.messages'),
            username,
            recipient,
            parsedResponse
        );

        messageManager.displayMessages();
    }
}

xhr.send();

function sendMsg(text) {
    if (!/^\s*$/m.test(text)) {
        if (socket) {
            messageManager.addMessage(username, text);
            socket.emit('chat message', {
                content: text,
                to: recipient
            });
        }
    }
}

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
    socket.emit('request status', recipient);

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
            let flag = false;
            chats.forEach(element => {
                if (element.childNodes[1].textContent.trim() == from) {
                    flag = true;
                    notification = element.querySelector('.messages-notify');
                    if (notification.textContent.trim().length > 0) {
                        console.log(notification.textContent);
                        let count = parseInt(notification.textContent.trim().split(' ')[0]);
                        if (count != 99) {
                            notification.textContent = ++count + ' нови cъобщения';
                        }
                    } else {
                        notification.textContent = '1 новo cъобщение';
                    }
                }
            });
            
            if (!flag) {
                const chatNotify = document.querySelector('.chat-notify');
                if (chatNotify.textContent.length == 0) {
                    chatNotify.style.padding = '.5rem';
                    chatNotify.textContent = 'Имате нови чатове';
                }
            }
        }
    });

    socket.on('chat status', function ({
        content,
        from
    }) {
        setStatus(content, 'online');
    });
}