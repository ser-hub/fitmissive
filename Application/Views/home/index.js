const forgottenPassBtn = document.getElementById("forgotten-password");
forgottenPassBtn.onclick = function () {
    const parent = document.getElementsByClassName('page')[1]
    parent.innerHTML = '';

    const rowDiv = document.createElement('div');
    rowDiv.classList.add('row');

    const mainLegend = document.createElement('legend')
    mainLegend.innerHTML = 'Ще ви изпратим имейл с линк за смяна паролата ви.'

    const emailLabel = document.createElement('label');
    emailLabel.innerHTML = 'Въведете имейл:';
    emailLabel.for = 'pr-email-input';

    const emailInput = document.createElement('input');
    emailInput.type = 'text';
    emailInput.id = 'pr-email-input';
    emailInput.required = true;

    const btnRowDiv = document.createElement('div');
    btnRowDiv.classList.add('row');
    btnRowDiv.classList.add('row-btn');

    const messageDiv = document.createElement('div');

    const sendBtn = document.createElement('button');
    sendBtn.classList.add('btn-default');
    sendBtn.classList.add('email-btn');
    sendBtn.innerHTML = 'Изпрати имейл';
    sendBtn.onclick = function () {
        if (emailInput.value.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `/index/forgottenpassword`, true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (this.status == 200) {
                    messageDiv.innerHTML = this.responseText;
                    parent.appendChild(messageDiv);
                }
            }
            xhr.send(`pr-target=${emailInput.value}`);
        }
    }

    rowDiv.append(emailLabel);
    rowDiv.append(emailInput);
    btnRowDiv.appendChild(sendBtn);
    parent.appendChild(mainLegend);
    parent.appendChild(rowDiv);
    parent.appendChild(btnRowDiv);
    parent.style.flexDirection = 'column';
};