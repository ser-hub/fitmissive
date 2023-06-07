import MessageService from './MessageService.js';
import Socket from '../Socket/Socket.js'

export default class ChatSocket
{   #socket;
    #messageService;
    constructor(server) {
        this.#socket = new Socket(server);
        this.#messageService = new MessageService();
    }

    setUp() {
        this.#socket.filter((socket, next) => {
            const username = socket.handshake.auth.sender;
            const receiver = socket.handshake.auth.receiver;
            if (!username) {
                console.log('error');
                return next(new Error("invalid username or receiver"));
            }
            console.log(username + " is connected to " + receiver);
            socket.receiver = receiver;
            socket.origin = username;
            next();
        })

        this.#socket.onConnection((socket) => {
            this.#setReceiverStatus(socket, true);
        });

        this.#socket.setUpResponse('disconnect', (socket) => {
            this.#setReceiverStatus(socket, false);
        });

        this.#socket.setUpResponse('request status', (socket, user) => {
            socket.emit('user status', this.#getOriginSocketId(user) != null);
        });

        this.#socket.setUpResponse('chat message', (socket, {content, to}) => {
            if (!/^\s*$/m.test(content)) {
                let receiver = this.#getOriginSocketId(to);
                if (this.#isOriginConnectedToReceiver(to, socket.origin)) {
                    this.#messageService.insertMessage(socket.origin, to, content, true);
                } else {
                    this.#messageService.insertMessage(socket.origin, to, content);
                }
    
                if (receiver != null) {
                    socket.to(receiver.id).emit("chat message", {
                        content,
                        from: socket.origin,
                    });
                }
            }
        })

        this.#socket.setUpResponse('chat status', (socket, { content, to }) => {
            let receiver = this.#getOriginSocketId(to);
                if (receiver != null && receiver.receiver == socket.origin) {
                    socket.to(receiver.id).emit("chat status", {
                        content,
                        from: socket.origin,
                    });
                }
        })
    }

    #setReceiverStatus(socket, status) {
        var openChat = this.#getReceiverSocketId(socket.origin);
        if (openChat) {
            socket.to(openChat.id).emit('user status', status);
        }
    }

    #getOriginSocketId(username) {
        for (let [id, socket] of this.#socket.getSockets()) {
            if (socket.origin === username) {
                return socket;
            }
        }
    }
    
    #getReceiverSocketId(username) {
        for (let [id, socket] of this.#socket.getSockets()) {
            if (socket.receiver === username) {
                return socket;
            }
        }
    }

    #isOriginConnectedToReceiver(origin, receiver) {
        for (let [id, socket] of this.#socket.getSockets()) {
            if (socket.receiver === receiver && socket.origin === origin) {
                return true;
            }
        }
        return false;
    }
}