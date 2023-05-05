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

window.addEventListener('resize', resize);
window.addEventListener('load', resize);