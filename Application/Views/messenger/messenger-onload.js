window.addEventListener('load', function () {
    const messages = document.querySelector('.messages');
    messages.scrollTo(0, messages.scrollHeight);
    
    document.querySelector('.selected').parentElement.scrollIntoView(true);
});
