const searchResults = document.getElementsByName('follow-btn');

if (searchResults) {
    searchResults.forEach(node => {
        node.addEventListener('click', function () {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `/data/followuser`, true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (this.status == 200) {
                        const response = JSON.parse(this.response);
                        if (response.status == 'success') {
                            changeFollowState(node);
                            node.style.opacity = '100%';
                        }
                        node.dataset.token = response.token;
                    }
                };
                xhr.send(`action=${node.innerText}&followed=${node.dataset.target}&token=${node.dataset.token}`);
                node.style.opacity = '50%';
        });
    });
}

function changeFollowState(element) {
    if (element) {
        if (element.innerText == 'Последвай') {
            element.innerText = 'Отпоследвай';
        } else {
            element.innerText = 'Последвай';
        }
    }
}