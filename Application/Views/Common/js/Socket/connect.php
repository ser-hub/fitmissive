<script src='/node/node_modules/socket.io/client-dist/socket.io.js'></script>
<script>
    const recipient = <?php
                    if (isset($data['receiver'])) {
                        echo json_encode($data['receiver']);
                    } else {
                        echo json_encode('');
                    }
                    ?>;
    const username = <?php
                    if (isset($data['loggedUsername'])) {
                        echo json_encode($data['loggedUsername']);
                    } else {
                        echo json_encode('');
                    }
                    ?>;

    if (username != '') {
        try {
            var socket = io('http://fitmissive.localhost', {
                autoConnect: false
            });
            socket.auth = {
                sender: username,
                receiver: recipient
            };
            socket.connect();
        } catch (e) {
            console.log(e.message);
        }
    }

    if (socket) {
        socket.on('chat message', function({
            content,
            from
        }) {
            if (document.baseURI.split('/')[3] != "messenger") {
                const messageCount = document.querySelector('.notification');
                if (messageCount.textContent.length > 0) {
                    let count = parseInt(messageCountText.textContent);
                    if (count != 99) {
                        messageCount.textContent = ++count;
                    }
                } else {
                    messageCount.textContent = '1';
                }
            }
        });

    }
</script>