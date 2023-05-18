export default class WorkoutManager {
    constructor(parentNode) {
        if (parentNode != null) {
            this.$parentConatiner = parentNode;
            this.$CSRF = this.$parentConatiner.dataset.token;

            this.$workoutContainer = document.createElement('div');
            this.$workoutContainer.classList.add('workout-container');
            this.$workoutContainer.parentClass = this;

            this.$sentencesDiv = document.createElement('div');
            this.$sentencesDiv.classList.add('sentences');

            this.$controlsDiv = document.createElement('div');
            this.$controlsDiv.classList.add('controls');

            this.$prepareButtons();
        }
    }

    setSentenceString(sentences) {
        this.$sentences = [];
        if (sentences != null && sentences.length > 0) {
            this.$sentences = sentences.split('\n');
            this.$cleanSentences();
        }
    }

    setSentences(sentences) {
        this.$sentences = [];
        if (sentences != null && sentences.length > 0) {
            this.$sentences = sentences;
            this.$cleanSentences();
        }
    }

    getSentences() {
        return join(this.$sentences);
    }

    setExercises(exercises) {
        this.$exerciseData = exercises;
    }

    setUser(user) {
        this.$user = user;
    }

    setCSRF(csrf) {
        this.$CSRF = csrf;
    }

    setError(error) {
        this.$sentencesDiv.childNodes[this.$sentencesDiv.childNodes.length - 1].innerHTML = error;
    }

    setMain(mainReference) {
        this.$mainWM = mainReference;
    }

    copy(sentences) {
        this.$tempSentences = this.$sentences.slice();
        this.setSentences(sentences);
        this.$initiateEdit();
        document.getElementsByClassName('feed-wrapper')[0].scrollTo(0, 0);
    }

    initiateDefault() {
        this.$setWMState();
        this.$display();
    }

    initiateCopy() {
        this.$setWMState('copy');
        this.$display();
    }

    initiateView() {
        this.$setWMState('view');
        this.$display();
    }

    $initiateEdit() {
        this.$setWMState('edit');
        this.$display();
    }

    $setSentences(mode) {
        this.$sentencesDiv.innerHTML = '';
        if (this.$sentences) {
            this.$sentences.forEach(sentence => {
                const sentenceDiv = document.createElement('div');
                sentenceDiv.classList.add('sentence');
                if (mode == 'edit') {
                    this.$prepareSentenceEdit(sentence, sentenceDiv);
                    let xBtn = this.$makeXBtn();
                    xBtn.sentence = sentence;
                    sentenceDiv.appendChild(xBtn);
                } else {
                    this.$prepareSentenceDefault(sentence, sentenceDiv);
                }
                this.$sentencesDiv.appendChild(sentenceDiv);
            });
        }
        if (this.$sentencesDiv.childNodes.length < 1) {
            const restDiv = document.createElement('div');
            restDiv.classList.add('rest-div');
            restDiv.innerHTML = 'Почивен ден';
            this.$sentencesDiv.appendChild(restDiv);
        }
        const errorsDiv = document.createElement('div');
        errorsDiv.classList.add('workout-edit-errors');
        this.$sentencesDiv.appendChild(errorsDiv);
    }

    $prepareSentenceDefault(sentence, parent) {
        const words = sentence.split(' ');
        parent.innerHTML = `<div class='sets inline-pill'>${words[0]}</div>`;
        parent.innerHTML += ` x <div class='reps inline-pill'>${words[2]} ${words[3]}</div>`;

        let commentIndex = sentence.length;
        if (sentence.indexOf('#') != -1) {
            commentIndex = sentence.indexOf('#');
        }

        parent.innerHTML += ` <div class='exercise inline-pill'>${sentence.substring(sentence.indexOf(words[4]), commentIndex)}</div>`;
        if (sentence.indexOf('#') != -1) {
            const comment = document.createElement('div');
            comment.classList.add('inline-pill');
            comment.classList.add('sentence-comment');
            comment.textContent = sentence.substring(commentIndex + 1);
            parent.appendChild(comment);
        }
    }

    $prepareSentenceEdit(sentence, parent) {
        const words = sentence.split(' ');
        this.$addNumericDropdown(parent, 'sets', 1, 20, words[0]);
        parent.append(` x `);
        this.$addNumericDropdown(parent, 'reps', 1, 100, words[2]);
        this.$addStringDropdown(parent, 'metric', words[3], ['повторения', 'секунди', 'минути']);

        let commentIndex = sentence.length + 1;
        if (sentence.indexOf('#') != -1) {
            commentIndex = sentence.indexOf('#');
        }

        this.$addExerciseDropdowns(parent, sentence.substring(sentence.indexOf(words[4]), commentIndex - 1), this.$exerciseData);
        this.$addCommentField(parent, 'sentence-comment', sentence.substring(commentIndex + 2));
    }

    $cleanSentences() {
        const sentenceFormat = /^\d+ x \d+ (повторения)|(минути)|(секунди) [а-яА-Я- ]+( #)?/;
        let rawSentences = this.$sentences;
        this.$sentences = [];
        if (rawSentences) {
            rawSentences.forEach(sentence => {
                if (sentenceFormat.test(sentence)) {
                    this.$sentences.push(sentence);
                }
            });
        }
    }

    $setTitle(title) {
        const carouselItem = this.$parentConatiner.parentNode.nextSibling;
        if (carouselItem && title) {
            const titleNode = carouselItem.nextSibling.childNodes[1];
            if (titleNode && titleNode.innerHTML != title) {
                titleNode.innerHTML = title;
            }
        }
    }

    $addCommentField(parent, className, content) {
        const commentField = document.createElement('input');
        commentField.type = 'text';
        commentField.classList.add('inline-pill');
        commentField.classList.add(className);
        commentField.maxLength = 80;
        commentField.value = content;
        commentField.placeholder = 'Коментар...';

        parent.appendChild(commentField);
    }

    $addNumericDropdown(parent, specificClass = '', min = 1, max = 1, selected = 1) {
        const numericDropdown = document.createElement('select');
        numericDropdown.classList.add('inline-pill');
        numericDropdown.classList.add(specificClass);

        if (min < max) {
            for (let i = min; i <= max; i++) {
                numericDropdown.options[numericDropdown.options.length] = new Option(i, i);
            }
        }
        numericDropdown.value = selected;

        parent.appendChild(numericDropdown);
    }

    $addStringDropdown(parent, specificClass = '', selected = '', data = []) {
        const stringDropdown = document.createElement('select');
        stringDropdown.classList.add('inline-pill');
        stringDropdown.classList.add(specificClass);

        data.forEach(element => {
            stringDropdown.options[stringDropdown.options.length] = new Option(element, element);
        })
        stringDropdown.value = selected;

        parent.appendChild(stringDropdown);
    }

    $addExerciseDropdowns(parent = document.createElement('div'), selected = '', data) {
        const categorySelect = document.createElement('select');
        categorySelect.classList.add('category');
        categorySelect.classList.add('inline-pill');

        const exerciseSelect = document.createElement('select');
        exerciseSelect.classList.add('exercise');
        exerciseSelect.classList.add('inline-pill');

        if (data) {
            for (let option in data) {
                categorySelect.options[categorySelect.options.length] = new Option(option, option);
            }
        }

        categorySelect.onchange = function () {
            exerciseSelect.length = 0;
            data[this.value].forEach(option => {
                exerciseSelect.options[exerciseSelect.options.length] = new Option(option, option);
            });
        };

        let category = '';
        for (let item in data) {
            if (data[item].includes(selected)) {
                category = item;
                break;
            }
        }

        if (category.length > 0) {
            exerciseSelect.length = 0;
            data[category].forEach(option => {
                exerciseSelect.options[exerciseSelect.options.length] = new Option(option, option);
            });
            categorySelect.value = category;
        } else {
            categorySelect.value = data[0];
        }

        if (selected.length > 0) {
            exerciseSelect.value = selected;
        } else {
            exerciseSelect.value = Object.values(data)[0][0];
        }

        parent.appendChild(categorySelect);
        parent.appendChild(exerciseSelect);
    }

    $prepareButtons() {
        this.$plusBtn = document.createElement('button');
        this.$plusBtn.innerHTML = "<i class='fa-regular fa-plus'></i>";
        this.$plusBtn.classList.add('plus');
        this.$plusBtn.parent = this;
        this.$plusBtn.onclick = this.$onPlusClick;

        this.$saveBtn = document.createElement('button');
        this.$saveBtn.innerHTML = "<i class='fa-solid fa-check'></i>";
        this.$saveBtn.classList.add('save');
        this.$saveBtn.parent = this;
        this.$saveBtn.onclick = this.$onSaveClick;

        this.$editBtn = document.createElement('button');
        this.$editBtn.innerHTML = 'Редактирай';
        this.$editBtn.classList.add('edit');
        this.$editBtn.parent = this;
        this.$editBtn.onclick = this.$onEditClick;

        this.$cancelBtn = document.createElement('button');
        this.$cancelBtn.innerHTML = "<i class='fa-solid fa-arrow-left'></i>";
        this.$cancelBtn.classList.add('cancel');
        this.$cancelBtn.parent = this;
        this.$cancelBtn.onclick = this.$onCancelClick;

        this.$copyBtn = document.createElement('button');
        this.$copyBtn.innerHTML = 'Копирай';
        this.$copyBtn.classList.add('edit');
        this.$copyBtn.parent = this;
        this.$copyBtn.onclick = this.$onCopyClick;
    }

    $makeXBtn() {
        const xBtn = document.createElement('button');
        xBtn.innerHTML = `<i class="fa-solid fa-xmark"></i>`;
        xBtn.classList.add('xmark');
        xBtn.parent = this;
        xBtn.onclick = this.$onXClick;
        return xBtn;
    }

    $addNewSentence() {
        this.$sentences.push(`1 x 1 повторения ${Object.values(this.$exerciseData)[0][0]}`);
        this.$initiateEdit();
        this.$parentConatiner.scrollTo(0, document.body.scrollHeight);
    }

    $removeSentence(sentence) {
        this.$sentences.splice(this.$sentences.indexOf(sentence), 1);
        this.$initiateEdit();
        this.$parentConatiner.scrollTo(0, document.body.scrollHeight);
    }

    $onXClick() {
        this.parent.$persistSentences();
        this.parent.$removeSentence(this.sentence);
    }

    $onPlusClick() {
        this.parent.$persistSentences();
        this.parent.$addNewSentence();
    }

    $onSaveClick() {
        this.parent.$persistSentences();
        this.parent.$sendData();
    }

    $onEditClick() {
        this.parent.$tempSentences = this.parent.$sentences.slice();
        console.log(this.parent.$sentences);
        this.parent.$initiateEdit('edit');
    }

    $onCancelClick() {
        this.parent.$sentences = this.parent.$tempSentences.slice();
        this.parent.initiateDefault();
    }

    $onCopyClick() {
        this.parent.$mainWM.copy(this.parent.$sentences);
    }

    $setWMState(state) {
        this.$workoutContainer.innerHTML = '';
        this.$sentencesDiv.innerHTML = '';
        this.$controlsDiv.innerHTML = '';
        if (state == null) {
            this.$setSentences();
            this.$controlsDiv.appendChild(this.$editBtn);
            if (this.$controlsDiv.classList.contains('edit-mode')) {
                this.$controlsDiv.classList.remove('edit-mode');
            }
        } else if (state == 'edit') {
            this.$setSentences('edit');
            if (!this.$controlsDiv.classList.contains('edit-mode')) {
                this.$controlsDiv.classList.add('edit-mode');
            }
            this.$controlsDiv.appendChild(this.$cancelBtn);
            this.$controlsDiv.appendChild(this.$plusBtn);
            this.$controlsDiv.appendChild(this.$saveBtn);
        } else if (state == 'copy') {
            this.$setSentences();
            this.$controlsDiv.appendChild(this.$copyBtn);
        } else if (state =='view') {
            this.$setSentences();
        }
    }

    $display() {
        this.$parentConatiner.innerHTML = '';
        this.$workoutContainer.appendChild(this.$sentencesDiv);
        this.$workoutContainer.appendChild(this.$controlsDiv);
        this.$parentConatiner.appendChild(this.$workoutContainer);
    }

    $persistSentences() {
        let data = [];
        let sentence = '';
        this.$sentencesDiv.childNodes.forEach(node => {
            if (node.classList[0] == 'sentence' && node.childNodes[2].value.length > 0) {
                sentence = `${node.childNodes[0].value} x ${node.childNodes[2].value} ${node.childNodes[3].value}`;
                sentence += ` ${node.childNodes[5].value}`;
                if (node.childNodes[6].value.trim().length > 0) {
                    sentence += ` # ${node.childNodes[6].value.trim()}`;
                }
                data.push(sentence);
            }
        })
        
        this.setSentences(data);
    }

    $sendData() {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/home/update/${this.$parentConatiner.dataset.day}`, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.sender = this;

        xhr.onload = function () {
            if (this.status == 200) {
                let responseParsed = JSON.parse(this.response);

                if (responseParsed.error.length > 0) {
                    this.sender.setError(responseParsed.error);
                    this.sender.setSentences(responseParsed.savedSentences);
                    this.sender.initiateDefault();
                    this.sender.setError(responseParsed.error);
                } else {
                    this.sender.initiateDefault();
                }
                this.sender.setCSRF(responseParsed.token);
                this.sender.$setTitle(responseParsed.title);
            }
        };

        const postStringDefault = `data=${this.$sentences.join('\n')}&token=${this.$CSRF}`;
        if (this.$user != undefined) {
            xhr.send(postStringDefault + `&user=${this.$user}`);
        } else {
            xhr.send(postStringDefault);
        }
    }
}