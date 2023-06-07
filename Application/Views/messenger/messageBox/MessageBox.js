export default class MessageBox {
    #parentContainer;
    #author;
    #receiver;
    #data;

    constructor(parentNode, author, receiver, data) {
        this.#parentContainer = parentNode;
        this.#author = author;
        this.#receiver = receiver;
        this.#data = data;
    }

    displayMessages() {
        this.#parentContainer.innerHTML = '';

        if (this.#data.messages.length == 0) {
            const promptDiv = document.createElement('div');
            promptDiv.classList.add('prompt');
            promptDiv.innerHTML = `Кажи здрасти на ${this.#receiver}`;
            this.#parentContainer.appendChild(promptDiv);
        } else {
            let lastMessage = this.#data.messages[0];
            lastMessage.timestamp = new Date(lastMessage.timestamp);
            let lastTimestamp = lastMessage.timestamp;
    
            this.#addTimestampToParentContainer(lastMessage.timestamp)
            this.#parentContainer.appendChild(this.#makeChatItem(lastMessage.author, lastMessage.timestamp));
    
            this.#data.messages.forEach(element => {
                element.timestamp = new Date(element.timestamp);

                if (element.timestamp - lastTimestamp > 300000) {
                    this.#addTimestampToParentContainer(element.timestamp);
                    this.#parentContainer.appendChild(this.#makeChatItem(element.author, element.timestamp));
                    lastTimestamp = element.timestamp;
                } else if (element.author != lastMessage.author) {
                    this.#parentContainer.appendChild(this.#makeChatItem(element.author, element.timestamp));
                }

                this.#addMessageToLastChatItem(element.text);
                
                lastMessage = element;
            });
        } 

        this.#parentContainer.scrollTo(0, this.#parentContainer.scrollHeight);
    }

    addMessage(author, message) { 
        this.#data.messages.push({
            author: author,
            timestamp: Date.now(),
            text:message
        });
        this.displayMessages();
        this.#parentContainer.scrollTo(0, this.#parentContainer.scrollHeight);
    }

    #makeChatItem(author, timestamp) {
        const newChatItem = document.createElement('div');
        newChatItem.classList.add('chat-item');
        newChatItem.appendChild(document.createElement('img'));

        if (author == this.#author) {
            newChatItem.childNodes[0].src = this.#data.ownPicture;
            newChatItem.style.marginLeft = 'auto';
        } else {
            newChatItem.childNodes[0].src = this.#data.targetPicture;
        }
        
        newChatItem.childNodes[0].width = newChatItem.childNodes[0].height = 30;
        newChatItem.childNodes[0].alt = 'Profile Picture';
        newChatItem.childNodes[0].style.marginRight = '10px';
        newChatItem.childNodes[0].classList.add('profile-pic');
        newChatItem.appendChild(document.createElement('div'));

        const authorDiv = document.createElement('div');
        authorDiv.classList.add('author');
        authorDiv.textContent = author;

        const messageTimestampDiv = document.createElement('div');
        messageTimestampDiv.classList.add('message-timestamp');
        messageTimestampDiv.innerHTML += `${timestamp.getHours()}:${timestamp.getMinutes() < 10 ? '0' + timestamp.getMinutes(): timestamp.getMinutes()}`;

        authorDiv.appendChild(messageTimestampDiv);
        newChatItem.childNodes[1].appendChild(authorDiv);
        
        return newChatItem;
    }

    #addMessageToLastChatItem( messageText) {
        const textDiv = document.createElement('div');
        textDiv.classList.add('message');
        textDiv.textContent = messageText;
        this.#parentContainer.childNodes[this.#parentContainer.childNodes.length - 1].childNodes[1].appendChild(textDiv);
    }

    #addTimestampToParentContainer(timestamp) {
        const timestampDiv = document.createElement('div');
        timestampDiv.classList.add('timestamp');
        timestampDiv.innerHTML = `${timestamp.getDay()}.${timestamp.getMonth()}.${timestamp.getYear() + 1900}`;
        timestampDiv.innerHTML += ` ${timestamp.getHours()}:${timestamp.getMinutes() < 10 ? '0' + timestamp.getMinutes(): timestamp.getMinutes()}`;
        this.#parentContainer.appendChild(timestampDiv);
    }
}