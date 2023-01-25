<script>
    window.addEventListener('load', function() {
        var messages = document.querySelector('.messages');
        messages.scrollTo(0, messages.scrollHeight);
    });

    if (socket) {
        var messages = document.querySelector('.messages'),
            input = document.querySelector('.input'),
            prompt = document.querySelector('.prompt');

        socket.emit('request status', recipient);

        input.addEventListener('keydown', function(e) {
            if (e.which === 13 && !e.shiftKey) {
                if (!/^\s*$/m.test(input.value)) {
                    displayMessage(username, input.value);
                    socket.emit('chat message', {
                        content: input.value,
                        to: recipient
                    });
                }
                input.value = '';
                if (prompt) prompt.style.display = 'none';

                e.preventDefault();
            } else {
                socket.emit('chat status', {
                    content: ' is typing...',
                    to: recipient
                });
            }
        });

        socket.on('user status', function(data) {
            console.log('entered');
            var userStatus;
            if (data) {
                userStatus = 'online';
            } else {
                userStatus = 'offline';
            }
            document.querySelectorAll('.chat-button-text').forEach(element => {
                if (element.childNodes[1].textContent.trim() == recipient) {
                    console.log('changing');
                    element.childNodes[3].textContent = userStatus;
                    element.childNodes[3].className = userStatus;
                }
            });
        })

        socket.on("connect_error", (err) => {
            if (err.message === "invalid username") {
                document.querySelector('.messages').textContent = "Something unexpected happened";
            }
        });

        socket.on('chat message', function({
            content,
            from
        }) {
            if (from === recipient || from === username) {
                displayMessage(from, content);
            } else {
                var chats = document.querySelectorAll('.chat-button-text');
                var flag = false;
                chats.forEach(element => {
                    if (element.textContent.trim() == from) {
                        flag = true;
                        element.parentElement.className += ' notification';
                    }
                });

                if (!flag) {
                    var chats = document.querySelector('.user-chats');
                    var newChat = document.createElement('a');
                    newChat.href = '/messenger/' + from;
                    var newChatButton = document.createElement('div');
                    newChatButton.className = 'chat-button notification';
                    var newChatButtonText = document.createElement('div');
                    newChatButtonText.className = 'chat-button-text';
                    newChatButtonText.style = 'margin-left: 15px';
                    newChatButtonText.textContent = from;
                    newChatButton.appendChild(newChatButtonText);
                    newChat.appendChild(newChatButton);
                    chats.appendChild(newChat);
                }
            }
        });

        socket.on('chat status', function({
            content,
            from
        }) {
            setStatus(content, 'online');
        });

        socket.onAny((event, ...args) => {
            console.log(event, args);
        });
    }

    var chats = document.querySelector('.user-chats');
    var selectedChat = document.querySelector('.user-chats .selected').textContent;

    function scroll(item, index, arr) {
        if (item.textContent.trim() == selectedChat.trim() && index > 16) {
            item.scrollIntoView();
        }
    }
    chats.childNodes.forEach(scroll);
</script>