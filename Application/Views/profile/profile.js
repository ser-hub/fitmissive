const carouselInner = document.getElementsByClassName('carousel-inner')[0];
const colorPickers = document.getElementsByName('cp');

colorPickers.forEach(cp => {
    cp.onclick = function () {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/data/updateusercolor`, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (this.status == 200) {
                console.log(this.response)
                const response = JSON.parse(this.response);
                if (response.result) {
                    carouselInner.style.backgroundColor = `#${cp.dataset.value}`;
                    document.querySelector('.body').style.backgroundColor = `#${cp.dataset.value}`;
                }
                cp.parentNode.dataset.token = response.token;
                colorPickers.forEach(cp => {
                    cp.style.opacity = '100%';
                })
            }
        };

        xhr.send(`value=${cp.dataset.value}&token=${cp.parentNode.dataset.token}`);
        cp.style.opacity = '50%';
    };
});