export default class MessageBox {
    constructor(parent, author, receiver, data) {
        this.$parent = parent
        this.$author = author
        this.$receiver = receiver
        this.$data = data
    }

    displayMessages() {
        this.$parent.innerHTML = ''

        if (this.$data.messages.length == 0) {
            let promptDiv = document.createElement('div')
            promptDiv.classList.add('prompt')
            promptDiv.innerHTML = `Кажи здрасти на ${this.$receiver}`
            this.$parent.appendChild(promptDiv)
        } else {
            let lastMessage = this.$data.messages[0];
            lastMessage.timestamp = new Date(lastMessage.timestamp)
    
            this.$parent.appendChild(this.$makeChatItem(lastMessage.author))
    
            this.$data.messages.forEach(element => {
                element.timestamp = new Date(element.timestamp)
                if (element.author == lastMessage.author && element.timestamp - lastMessage.timestamp <= 300000) {
                    this.$addMessageToLastChatItem(element.text)
                } else {
                    if (element.timestamp - lastMessage.timestamp > 300000) {
                        this.$addTimestampToParent(element.timestamp)
                    }
    
                    this.$parent.appendChild(this.$makeChatItem(element.author))
                    this.$addMessageToLastChatItem(element.text)
    
                    lastMessage = element
                }
            })
        } 

        this.$parent.scrollTo(0, this.$parent.scrollHeight)
    }

    addMessage(author, message) { // make this add message to this.$data.messages and refresh
        this.$data.messages.push({
            author: author,
            timestamp: Date.now(),
            text:message
        })
        this.displayMessages()
        this.$parent.scrollTo(0, this.$parent.scrollHeight);
    }

    $makeChatItem(author) {
        let newChatItem = document.createElement('div')
        newChatItem.classList.add('chat-item')
        newChatItem.appendChild(document.createElement('img'))
        if (author == this.$author) {
            newChatItem.childNodes[0].src = this.$data.ownPicture
            newChatItem.style.marginLeft = 'auto'
        } else {
            newChatItem.childNodes[0].src = this.$data.targetPicture
        }
        newChatItem.childNodes[0].width = newChatItem.childNodes[0].height = 30
        newChatItem.childNodes[0].alt = 'Profile Picture'
        newChatItem.childNodes[0].style.marginRight = '10px'
        newChatItem.childNodes[0].classList.add('profile-pic')
        newChatItem.appendChild(document.createElement('div'))
        let authorDiv = document.createElement('div')
        authorDiv.classList.add('author')
        authorDiv.innerHTML = author
        newChatItem.childNodes[1].appendChild(authorDiv)
        
        return newChatItem;
    }

    $addMessageToLastChatItem( messageText) {
        let textDiv = document.createElement('div')
        textDiv.classList.add('message')
        textDiv.innerHTML = messageText
        this.$parent.childNodes[this.$parent.childNodes.length - 1].childNodes[1].appendChild(textDiv)
    }

    $addTimestampToParent(timestamp) {
        let timestampDiv = document.createElement('div')
        timestampDiv.classList.add('timestamp')
        timestampDiv.innerHTML = `${timestamp.getDay()}.${timestamp.getMonth()}.${timestamp.getYear() + 1900}`
        timestampDiv.innerHTML += ` ${timestamp.getHours()}:${timestamp.getMinutes() < 10 ? '0' + timestamp.getMinutes(): timestamp.getMinutes()}`
        this.$parent.appendChild(timestampDiv)
    }
}