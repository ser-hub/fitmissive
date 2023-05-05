<script>
    let messages = document.querySelector('.messages');

    window.addEventListener('load', function() {
        messages.scrollTo(0, messages.scrollHeight);
    });


    function setStatus(status, otherwise) {
        var statusNode = document.querySelector('.online');
        if (statusNode.textContent !== status) {
            statusNode.textContent = status;
            statusTimeout = setTimeout(function() {
                statusNode.textContent = otherwise;
            }, 2000);
        }
    }

    let lastAuthor = '';

    function displayMessage(author, msg) {
        if (author !== lastAuthor) {
            var chatItem = document.createElement('div');
            chatItem.className = 'chat-item';
            chatItem.style = 'margin-left: auto'

            var authorDiv = document.createElement('div');
            authorDiv.style = 'margin-right: 10px;';
            chatItem.appendChild(authorDiv);

            var profilePic = document.createElement('img');
            if (author == username) {
                profilePic.src = document.querySelector('.sender').value;
            } else {
                profilePic.src = document.querySelector('.receiver').value;
            }
            profilePic.classList.add('profile-pic')
            profilePic.width = profilePic.height = '30';
            profilePic.alt = 'profile picture';
            authorDiv.appendChild(profilePic);

            textsDiv = document.createElement('div');
            chatItem.appendChild(textsDiv);
            var authorText = document.createElement('div');
            authorText.textContent = author
            authorText.className = 'author';
            textsDiv.appendChild(authorText);

            lastAuthor = author;
            messages.appendChild(chatItem);
        }

        var text = document.createElement('div');
        text.className = 'message';
        text.textContent = msg;
        textsDiv.appendChild(text);

        messages.scrollTo(0, messages.scrollHeight);
    }

    if (socket) {
            input = document.querySelector('.message-field'),
            prompt = document.querySelector('.prompt');

        socket.emit('request status', recipient);

        input.addEventListener('keydown', (event) => {
            if (event.which === 13 && !event.shiftKey) {
                if (emitMessage(input, socket)) {
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


    function emitMessage(inputField, socket) {
        if (!/^\s*$/m.test(inputField.value)) {
            displayMessage(username, inputField.value);
            socket.emit('chat message', {
                content: inputField.value,
                to: recipient
            });
            return true;
        } else {
            return false;
        }
    }
</script>