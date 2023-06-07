import ChatSocket from './App/Services/ChatSocket.js'
import * as httpServer from 'http'
import {__dirname} from './pathname.js'

import * as dotenv from 'dotenv'
dotenv.config({
    path: __dirname + '/../Application/.env'
});

let server = httpServer.createServer();
let chatSocket = new ChatSocket(server);
chatSocket.setUp();

server.listen(process.env.SOCKET_PORT, () => {
    console.log('listening * on: ' + process.env.SOCKET_PORT);
});