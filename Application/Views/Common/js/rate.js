const ratingSections = document.getElementsByName('rating-section');
ratingSections.forEach(parentNode => {
    const likeBtn = {
        btn: parentNode.getElementsByClassName('fa-arrow-up')[0],
        state: false
    };

    const dislikeBtn = {
        btn: parentNode.getElementsByClassName('fa-arrow-down')[0],
        state: false
    };

    const likesCounter = parentNode.getElementsByClassName('likes-count')[0];
    const dislikesCounter = parentNode.getElementsByClassName('dislikes-count')[0];

    if (!likeBtn.btn.classList.contains('fa-xl')) {
        activate(likeBtn);
    }

    if (!dislikeBtn.btn.classList.contains('fa-xl')) {
        activate(dislikeBtn);
    }

    likeBtn.btn.addEventListener('click', function () {
        if (likeBtn.state) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `/data/rateworkout/${parentNode.dataset.user}`, true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = likePressed;
            xhr.send(`rating=1&token=${ratingSections[0].dataset.token}`);
        }
    });

    dislikeBtn.btn.addEventListener('click', function () {
        if (dislikeBtn.state) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `/data/rateworkout/${parentNode.dataset.user}`, true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = dislikePressed;
            xhr.send(`rating=0&token=${ratingSections[0].dataset.token}`);
        }
    });

    function activate(node) {
        node.btn.classList.replace('fa-xl', 'fa-lg');
        node.btn.classList.add('rating-btn');
        node.btn.addEventListener('mouseover', mouseoverlistener);
        node.btn.addEventListener('mouseleave', mouseleavelistener);
        node.state = true;
    }

    function deactivate(node) {
        node.btn.classList.replace('fa-lg', 'fa-xl');
        node.btn.classList.remove('rating-btn');
        node.btn.removeEventListener('mouseover', mouseoverlistener);
        node.btn.removeEventListener('mouseleave', mouseleavelistener);
        node.state = false;
    }

    function increaseCounter(counterElement) {
        counterElement.innerText = parseInt(counterElement.innerText) + 1;
    }

    function decreaseCounter(counterElement) {
        counterElement.innerText = parseInt(counterElement.innerText) - 1;
    }

    function mouseoverlistener() {
        if (!this.classList.contains('fa-bounce')) {
            this.classList.add('fa-bounce');
        }
    }

    function mouseleavelistener() {
        if (this.classList.contains('fa-bounce')) {
            this.classList.remove('fa-bounce');
        }
    }

    function likePressed() {
        if (this.status == 200) {
            console.log(this.response)
            let response = JSON.parse(this.response);
            if (response.result == 'Rated' || response.result == 'Updated') {
                likeBtn.btn.classList.replace('fa-lg', 'fa-xl');
                deactivate(likeBtn);
                increaseCounter(likesCounter);
            }
            if (response.result == 'Updated') {
                dislikeBtn.btn.classList.replace('fa-xl', 'fa-lg');
                activate(dislikeBtn);
                decreaseCounter(dislikesCounter);
            }
            ratingSections[0].dataset.token = response.token;
        }
    }

    function dislikePressed() {
        if (this.status == 200) {
            let response = JSON.parse(this.response);
            if (response.result == 'Rated' || response.result == 'Updated') {
                dislikeBtn.btn.classList.replace('fa-lg', 'fa-xl');
                deactivate(dislikeBtn);
                increaseCounter(dislikesCounter);
            }
            if (response.result == 'Updated') {
                likeBtn.btn.classList.replace('fa-xl', 'fa-lg');
                activate(likeBtn);
                decreaseCounter(likesCounter);
            }
            ratingSections[0].dataset.token = response.token;
        }
    }
})