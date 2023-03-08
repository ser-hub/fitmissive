let searchResults = document.getElementsByName('follow-btn')

if (searchResults) {
    searchResults.forEach(node => {
        node.addEventListener('click', function () {
                let xhr = new XMLHttpRequest()
                xhr.open('POST', `/search/follow`, true)
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
                xhr.onload = function() {
                    if (this.status == 200) {
                        changeFollowState(node)
                        node.dataset.token = this.responseText
                    }
                }
                xhr.send(`action=${node.innerText}&followed=${node.dataset.target}&token=${node.dataset.token}`)
        })
    })
    
}

function changeFollowState(element) {
    if (element) {
        if (element.innerText == 'Follow') {
            element.innerText = 'Unfollow'
        } else {
            element.innerText = 'Follow'
        }
    }
}