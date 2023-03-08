var statusTimeout = 0;

function resize() {
    var home = document.getElementById('home');
    home.childNodes.forEach(function (child) {
        child.remove();
    });
    logo = document.createElement('img');
    logo.height = 60;
    logo.alt = 'fitmissive-logo';
    if (window.innerWidth < 1200) {
        logo.src = '/img/profiles/default.png';
        logo.width = 49;
    } else {
        logo.src = '/img/logo-transparent.png';
        logo.width = 400;
    }
    home.appendChild(logo);
}

function setStatus(status, otherwise) {
    var statusNode = document.querySelector('.online');
    if (statusNode.textContent !== status) {
        statusNode.textContent = status;
        statusTimeout = setTimeout(function () {
            statusNode.textContent = otherwise;
        }, 2000);
    }
}

var lastAuthor = '';

function displayMessage(author, msg) {
    if (author !== lastAuthor) {
        var chatItem = document.createElement('div');
        chatItem.className = 'chat-item';

        var authorDiv = document.createElement('div');
        authorDiv.style = 'margin-right: 10px;';
        chatItem.appendChild(authorDiv);

        var profilePic = document.createElement('img');
        if (author == username) {
            profilePic.src = document.querySelector('.sender').value;
        } else {
            profilePic.src = document.querySelector('.receiver').value;
        }
        profilePic.width = profilePic.height = '30';
        profilePic.alt = 'profile picture';
        authorDiv.appendChild(profilePic);

        textsDiv = document.createElement('div');
        chatItem.appendChild(textsDiv);
        var authorText = document.createElement('div');
        authorText.textContent = author
        authorText.className = 'author';
        textsDiv.appendChild(authorText);

        lastAuthor = author;
        messages.appendChild(chatItem);
    }

    var text = document.createElement('div');
    text.className = 'message';
    text.textContent = msg;
    textsDiv.appendChild(text);

    messages.scrollTo(0, messages.scrollHeight);
}

window.addEventListener('resize', resize);
window.addEventListener('load', resize);