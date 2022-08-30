var server = require('http').createServer();
var io = require('socket.io')(server);

function insertMessage(con, sender, recipient, content, seen = false) {
    var senderId;
    con.query("SELECT * FROM users WHERE username = ? OR username = ?", [sender, recipient], function (err, result, fields) {
        if (err) throw err;
        result.forEach(element => {
            if (element.username == sender) {
                senderId = element.user_id;
            }
        });
        con.query(
            "SELECT * FROM chats WHERE (user_a = ? AND user_b = ?) OR (user_b = ? AND user_a = ?)",
            [result[0].user_id, result[1].user_id, result[0].user_id, result[1].user_id], function (err, result, fields) {
                if (err) throw err;
                var time = null;
                if (seen) {
                    time = new Date().toISOString().slice(0, 19).replace('T', ' ');
                }
                con.query(
                    "CALL insert_message(?)",
                    [[result[0].chat_id, senderId, content, time]], function (err, result, fields) {
                        if (err) throw err;
                    });
            });
    });
}

function getOriginSocketId(username) {
    for (let [id, socket] of io.of("/").sockets) {
        if (socket.origin === username) {
            return socket;
        }
    }
}

function getReceiverSocketId(username) {
    for (let [id, socket] of io.of("/").sockets) {
        if (socket.receiver === username) {
            return socket;
        }
    }
}

module.exports = {
    insertMessage,
    getOriginSocketId,
    getReceiverSocketId,
    io,
    server,
}