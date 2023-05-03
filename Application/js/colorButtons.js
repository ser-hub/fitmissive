let carouselInner = document.getElementsByClassName('carousel-inner')[0];
const colorPickers = document.getElementsByName('cp');

colorPickers.forEach(cp => {
    cp.onclick = function () {
        let xhr = new XMLHttpRequest()
        xhr.open('POST', `/profile/updateColor`, true)
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
        xhr.onload = function() {
            if (this.status == 200) {
                let response = JSON.parse(this.response)
                if (response.result) {
                    carouselInner.style = 'background-color: #' + cp.dataset.value
                }
                cp.parentNode.dataset.token = response.token
            }
        }

        xhr.send(`value=${cp.dataset.value}&token=${cp.parentNode.dataset.token}`)
    } 
})