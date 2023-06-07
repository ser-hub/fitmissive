import WorkoutManager from "../Common/js/WorkoutManager/workoutManager.js"

const workoutContainers = document.getElementsByName('workout-container');

const xhr = new XMLHttpRequest();
xhr.open('GET', `/data/initalWorkoutData`, true);
xhr.onload = function () {
    if (this.status == 200) {
        const parsedResponse = JSON.parse(this.response);
        workoutContainers.forEach(container => {
            let mainWM = new WorkoutManager(container);
            mainWM.setExercises(parsedResponse.exercises);
            mainWM.setSentenceString(parsedResponse.splitData[container.dataset.day]);
            mainWM.initiateDefault();

            document.getElementsByName(container.dataset.day).forEach(child => {
                let childWM = new WorkoutManager(child);
                childWM.setExercises(parsedResponse.exercises);
                childWM.setSentenceString(child.textContent.trim());
                childWM.setMain(mainWM);
                childWM.initiateCopy();
            });
        });
    }
}
xhr.send();