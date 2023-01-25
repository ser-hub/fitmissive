import * as mysql2 from 'mysql2'

export default class DB {
    #connection
    constructor(host, username, password, dbName) {
        if (host != null && username != null && password != null && dbName != null) {
            this.#connection = mysql2.createConnection({
                host: host,
                user: username,
                password: password,
                database: dbName
            })
        } else {
            this.#connection = mysql2.createConnection({
                host: process.env.DB_HOST,
                user: process.env.DB_USERNAME,
                password: process.env.DB_PASSWORD,
                database: process.env.DB_NAME
            })
        }
        this.#connect()
    }

    #connect() {
        this.#connection.connect(function (err) {
            if (err) throw err;
            console.log("Database connected!");
        });
    }

    query(queryString, parameters = [], callback) {
        this.#connection.query(queryString, parameters, function (err, result, fields) {
            if (err) throw err
            if (callback) callback(result)
        })
    }
}