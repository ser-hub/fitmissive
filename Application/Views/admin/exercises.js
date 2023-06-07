const mainForm = document.querySelector('.selector-section').getElementsByTagName('form')[0];
const formTitle = document.querySelector('.form-title');
const titleInput = document.getElementsByName('exercise-title')[0];
const descriptionInput = document.getElementsByName('exercise-description')[0];
const sumbitBtn = document.getElementsByClassName('save-btn')[0];
const deleteBtn = document.getElementsByClassName('save-btn')[1];

if (document.querySelector('.plus-btn') != null) {
    document.querySelector('.plus-btn').onclick = resetControls;
}

function resetControls() {
    formTitle.innerHTML = 'Добавяне на упражнение';
    mainForm.action = '/admin/newexercise';
    titleInput.value = '';
    descriptionInput.value = '';
    sumbitBtn.value = 'Добави';
    document.getElementsByClassName('save-btn')[1].style.display = 'none';
}

function setControls(exercise) {
    if (formTitle != null) {
        formTitle.innerHTML = 'Редактиране на упражнение';
        mainForm.action = '/admin/updateexercise/' + exercise.id;
        deleteBtn.href = '/admin/deleteexercise/' + exercise.id;
        titleInput.value = exercise.name;
        descriptionInput.value = exercise.description;
        sumbitBtn.value = 'Обнови';
        document.getElementsByClassName('save-btn')[1].style.display = 'block';
    } else {
        document.querySelector('.controls').innerHTML = exercise.description;
    }
}

function setOptionSelected(optionValue) {
    document.querySelector('.options').childNodes.forEach(option => {
        if (option.classList.contains('option-selected')) {
            option.classList.remove('option-selected');
        }

        if (option.innerHTML == optionValue) {
            option.classList.add('option-selected');
        }
    });
}

function setOptionsDiv(optionsDiv, exercises) {
    optionsDiv.innerHTML = '';

    exercises.forEach(option => {
        const optionDiv = document.createElement('div');
        optionDiv.classList.add('option');
        optionDiv.innerHTML = option.name;
        optionDiv.onclick = function () {
            if (document.getElementById('error-field') != null && document.getElementById('error-field').innerHTML.length != 0) {
                document.getElementById('error-field').innerHTML = '';
            }
            setOptionSelected(this.innerHTML);
            setControls(option);
        }
        optionsDiv.appendChild(optionDiv);

        if (option.id == document.baseURI.split('/')[5]) {
            setOptionSelected(option.name);
            setControls(option);
        }
    });
}

function setExercises(exerciseData) {
    const categorySelect = document.querySelector('.category-select');
    if (exerciseData) {
        let selectedCategory = null;
        if (categorySelect.dataset.selected.length != 0) {
            selectedCategory = categorySelect.dataset.selected;
        }
        for (let option in exerciseData) {
            categorySelect.options[categorySelect.options.length] = new Option(option);
            if (exerciseData[option].map((element) => element.id).includes(parseInt(document.baseURI.split('/')[5]))) {
                selectedCategory = option;
            }
        }

        categorySelect.onchange = function () {
            setOptionsDiv(document.querySelector('.options'), exerciseData[this.value]);
            setControls({
                id: '',
                name: '',
                description: ''
            });
        };

        if (selectedCategory != null) {
            categorySelect.value = selectedCategory;
            setOptionsDiv(document.querySelector('.options'), exerciseData[selectedCategory]);
        } else {
            categorySelect.value = categorySelect.options[0].value;
            setOptionsDiv(document.querySelector('.options'), exerciseData[categorySelect.options[0].value]);
        }
    }
}

const xhr = new XMLHttpRequest();
xhr.open('GET', `/data/completeExerciseData`, true);
xhr.onload = function () {
    if (this.status == 200) {
        setExercises(JSON.parse(this.response));
    }
}
xhr.send();