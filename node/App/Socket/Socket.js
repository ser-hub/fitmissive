import {Server} from 'socket.io'

export default class Socket {
    #io;
    constructor(server) {
        this.#io = new Server(server);
    }

    filter(filterFunc) {
        this.#io.use((socket, next) => {
            if (filterFunc && socket && next) {
                filterFunc(socket, next);
            }
        })
    }

    setUpResponse(messageString, response) {
        this.#io.on('connection', (socket) => {
            socket.on(messageString, (args) => {
                response(socket, args);
            })
        })
    }

    onConnection(toDo) {
        this.#io.on('connection', (socket) => {
            toDo(socket);
        })
    }

    getSockets() {
        return this.#io.of("/").sockets;
    }
}