utils = require("./utilities");
require('dotenv').config({
    path: '${__dirname}/../Application/.env'
});

utils.con.connect(function (err) {
    if (err) throw err;
    console.log("Database connected!");
});

utils.io.use((socket, next) => {
    const username = socket.handshake.auth.sender;
    const receiver = socket.handshake.auth.receiver;
    if (!username) {
        console.log('error');
        return next(new Error("invalid username or recipient"));
    }
    console.log(username + " is connected to " + receiver);
    socket.receiver = receiver;
    socket.origin = username;
    next();
});

utils.io.on('connection', (socket) => {

    var openChat = utils.getReceiverSocketId(socket.origin);
    if (openChat) {
        socket.to(openChat.id).emit('user status', true);
    }

    socket.on('disconnect', () => {
        var openChat = utils.getReceiverSocketId(socket.origin);
        if (openChat) {
            socket.to(openChat.id).emit('user status', false);
        }
    });

    socket.on('request status', (data) => {
        if (utils.getOriginSocketId(data) != null) {
            socket.emit('user status', true);
        } else {
            socket.emit('user status', false);
        }
    });

    socket.on('chat message', ({ content, to }) => {
        if (!/^\s*$/m.test(content)) {
            receiver = utils.getOriginSocketId(to);
            if (receiver != null && receiver.receiver != "") {
                utils.insertMessage(socket.origin, to, content, true);
            } else {
                utils.insertMessage(socket.origin, to, content);
            }

            if (receiver != null) {
                socket.to(receiver.id).emit("chat message", {
                    content,
                    from: socket.origin,
                });
            }
        }
    });

    socket.on('chat status', ({ content, to }) => {
        var receiver = utils.getOriginSocketId(to);
        if (receiver != null && receiver.receiver == socket.origin) socket.to(receiver.id).emit("status", {
            content,
            from: socket.origin,
        });
    });
});

utils.server.listen(process.env.SOCKET_PORT, () => {
    console.log('listening on *:' + process.env.SOCKET_PORT);
});