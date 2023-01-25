import Database from '../Database/DB.js'

export default class MessageService {
    #db
    constructor() {
        this.#db = new Database()
    }

    insertMessage(sender, recipient, content, seen = false) {
        let senderId
        let db = this.#db
        let time = new Date().toISOString().slice(0, 19).replace('T', ' ')
        
        db.query("SELECT username, user_id FROM users WHERE username = ? OR username = ?", [sender, recipient], function (result) {
            if (result) {
                result.forEach(element => {
                    if (element.username == sender) {
                        senderId = element.user_id
                    }
                })
                
                db.query(
                    "SELECT chat_id FROM chats WHERE (user_a = ? AND user_b = ?) OR (user_b = ? AND user_a = ?)",
                    [result[0].user_id, result[1].user_id, result[0].user_id, result[1].user_id], function (result) {
                        db.query(
                            "CALL insert_message(?)",
                            [[result[0].chat_id, senderId, content, seen == true ? time : null]])
                    })
            }
        })
    }
}