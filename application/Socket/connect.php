<script>
    window.addEventListener('resize', resize);
    window.addEventListener('load', resize);

    var recipient = <?php
                    if (isset($data['receiver'])) {
                        echo json_encode($data['receiver']);
                    } else {
                        echo json_encode('');
                    }
                    ?>;
    var username = <?php
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
            if (recipient == "") {
                var menuItems = document.querySelector('.items');
                menuItems.childNodes.forEach(function(menuItem) {
                    if (menuItem.textContent == 'Messenger' && !menuItem.classList.contains('notification')) {
                        menuItem.classList.add('notification');
                    }
                });
            }
        });

    }
</script>