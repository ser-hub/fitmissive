const colorSection = document.querySelector('.color-section');
const controls = document.querySelector('.controls-color');
const redSection = document.querySelector('.red');
const redField = redSection.childNodes[1];
const greenSection = document.querySelector('.green');
const greenField = greenSection.childNodes[1];
const blueSection = document.querySelector('.blue');
const blueField = blueSection.childNodes[1];

const errorField = document.getElementById('error-field');
const resultField = document.querySelector('.result-value');
const visualisation = document.getElementById('color-select');

resultField.onchange = function () {
    if (resultField.value.length == 6) {
        let r = resultField.value[0] + resultField.value[1];
        let g = resultField.value[2] + resultField.value[3];
        let b = resultField.value[4] + resultField.value[5];

        if (parseInt(r, 16) < 0 && parseInt(r, 16) > 255) return;
        if (parseInt(g, 16) < 0 && parseInt(g, 16) > 255) return;
        if (parseInt(b, 16) < 0 && parseInt(b, 16) > 255) return;

        visualisation.style.backgroundColor = `#${resultField.value}`;
    }
}

redField.onchange = greenField.onchange = blueField.onchange = function () {
    setColor(redField.value, greenField.value, blueField.value);
}

function setControls(color) {
    document.getElementById('color-form').action = `/admin/updatecolor/${color.id}`;

    redField.value = parseInt(color.value[0] + color.value[1], 16);
    greenField.value = parseInt(color.value[2] + color.value[3], 16);
    blueField.value = parseInt(color.value[4] + color.value[5], 16);

    resultField.value = color.value;
    visualisation.style.backgroundColor = `#${color.value}`;
}

function setColor(r, g, b) {
    r = parseInt(r);
    if (r < 0 || r > 255) return;
    r = r.toString(16);
    let rString = `${r.length < 2 ? '0' + r : r}`;

    g = parseInt(g).toString(16);
    if (g < 0 || g > 255) return;
    g = g.toString(16);
    let gString = `${g.length < 2 ? '0' + g : g}`;

    b = parseInt(b).toString(16);
    if (b < 0 || b > 255) return;
    b = b.toString(16);
    let bString = `${b.length < 2 ? '0' + b : b}`;

    resultField.value = rString + gString + bString;
    visualisation.style.backgroundColor = `#${resultField.value}`;
}

function setUpColorDisplay(colors) {
    let selected = false;
    let index = 0;
    colorSection.textContent = 'Цветове:';
    colors.forEach(element => {
        const colorDiv = document.createElement('div');
        colorDiv.classList.add('color-select');
        colorDiv.style.backgroundColor = '#' + element.value;
        colorDiv.style.marginTop = `${index++ * 20}px`;
        colorDiv.onclick = function () {
            document.querySelectorAll('.color-select').forEach(color => {
                if (color.classList.contains('color-selected')) {
                    color.classList.remove('color-selected');
                }
            });

            colorDiv.classList.add('color-selected');

            setControls(element);
        }

        if (element.id == document.baseURI.split('/')[5]) {
            if (errorField.innerHTML.length == 0) {
                setControls(element);
            } else {
                document.getElementById('color-form').action = `/admin/updatecolor/${element.id}`;
            }
            colorDiv.classList.add('color-selected');
            selected = true;
        }
        colorSection.appendChild(colorDiv);
    });

    if (!selected) {
        document.querySelectorAll('.color-select')[0].onclick();
    }
}

const xhr = new XMLHttpRequest();
xhr.open('GET', `/data/allColorsHex`, true);
xhr.onload = function () {
    if (this.status == 200) {
        const data = JSON.parse(this.response);
        setUpColorDisplay(data);
    }
}
xhr.send();

for (let i = 0; i < 255; i++) {
    const cpItemRed = document.createElement('div');
    cpItemRed.classList.add('color-piece');
    cpItemRed.style.backgroundColor = `rgb(${i}, 0, 0)`;
    cpItemRed.onclick = cpItemRed.ondragover = function () {
        redField.value = i;
        setColor(redField.value, greenField.value, blueField.value);
    }
    redSection.appendChild(cpItemRed);

    const cpItemGreen = document.createElement('div');
    cpItemGreen.classList.add('color-piece');
    cpItemGreen.style.backgroundColor = `rgb(0, ${i}, 0)`;
    cpItemGreen.onclick = cpItemGreen.ondragover = function () {
        greenField.value = i;
        setColor(redField.value, greenField.value, blueField.value);
    }
    greenSection.appendChild(cpItemGreen);

    const cpItemBlue = document.createElement('div');
    cpItemBlue.classList.add('color-piece');
    cpItemBlue.style.backgroundColor = `rgb(0, 0, ${i})`;
    cpItemBlue.onclick = cpItemBlue.ondragover = function () {
        blueField.value = i;
        setColor(redField.value, greenField.value, blueField.value);
    }
    blueSection.appendChild(cpItemBlue);
}