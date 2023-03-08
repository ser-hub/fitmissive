let ratingSections = document.getElementsByName('rating-section')
ratingSections.forEach(parentNode => {
    let likeBtn = {
        btn: parentNode.getElementsByClassName('fa-thumbs-up')[0],
        state: false
    }
    let dislikeBtn = {
        btn: parentNode.getElementsByClassName('fa-thumbs-down')[0],
        state: false
    }

    let likesCounter = parentNode.getElementsByClassName('likes-count')[0]
    let dislikesCounter = parentNode.getElementsByClassName('dislikes-count')[0]

    if (!likeBtn.btn.classList.contains('fa-solid')) {
        activate(likeBtn)
    }

    if (!dislikeBtn.btn.classList.contains('fa-solid')) {
        activate(dislikeBtn)
    }

    likeBtn.btn.addEventListener('click', function () {
        if (likeBtn.state) {
            let xhr = new XMLHttpRequest()
            xhr.open('POST', `/profile/rate/${parentNode.dataset.user}`, true)
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
            xhr.onload = likePressed
            xhr.send(`rating=1&token=${ratingSections[0].dataset.token}`)
        }
    })

    dislikeBtn.btn.addEventListener('click', function () {
        if (dislikeBtn.state) {
            let xhr = new XMLHttpRequest()
            xhr.open('POST', `/profile/rate/${parentNode.dataset.user}`, true)
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
            xhr.onload = dislikePressed
            xhr.send(`rating=0&token=${ratingSections[0].dataset.token}`)
        }
    })

    function activate(node) {
        node.btn.addEventListener('mouseover', mouseoverlistener)
        node.btn.addEventListener('mouseleave', mouseleavelistener)
        node.state = true
    }

    function deactivate(node) {
        node.btn.removeEventListener('mouseover', mouseoverlistener)
        node.btn.removeEventListener('mouseleave', mouseleavelistener)
        node.state = false
    }

    function increaseCounter(counterElement) {
        counterElement.innerText = parseInt(counterElement.innerText) + 1
    }

    function decreaseCounter(counterElement) {
        counterElement.innerText = parseInt(counterElement.innerText) - 1
    }

    function mouseoverlistener() {
        this.classList.replace('fa-regular', 'fa-solid')
    }

    function mouseleavelistener() {
        this.classList.replace('fa-solid', 'fa-regular')
    }

    function likePressed() {
        if (this.status == 200) {
            let response = JSON.parse(this.response)
            if (response.result == 'Rated' || response.result == 'Updated') {
                likeBtn.btn.classList.replace('fa-regular', 'fa-solid')
                deactivate(likeBtn)
                increaseCounter(likesCounter)
            }
            if (response.result == 'Updated') {
                dislikeBtn.btn.classList.replace('fa-solid', 'fa-regular')
                activate(dislikeBtn)
                decreaseCounter(dislikesCounter)
            }
            ratingSections.forEach(node => {
                node.dataset.token = response.token
            })
        }
    }

    function dislikePressed() {
        if (this.status == 200) {
            let response = JSON.parse(this.response)
            if (response.result == 'Rated' || response.result == 'Updated') {
                dislikeBtn.btn.classList.replace('fa-regular', 'fa-solid')
                deactivate(dislikeBtn)
                increaseCounter(dislikesCounter)
            }
            if (response.result == 'Updated') {
                likeBtn.btn.classList.replace('fa-solid', 'fa-regular')
                activate(likeBtn)
                decreaseCounter(likesCounter)
            }
            ratingSections.forEach(node => {
                node.dataset.token = response.token
            })
        }
    }
})