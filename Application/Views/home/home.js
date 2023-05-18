import WorkoutManager from "../Common/js/WorkoutManager/workoutManager.js"

const workoutContainers = document.getElementsByName('workout-container');

const xhr = new XMLHttpRequest();
xhr.open('GET', `/home/getInitalWorkoutData`, true);
xhr.onload = function () {
    if (this.status == 200) {
        const parsedResponse = JSON.parse(this.response);
        workoutContainers.forEach(container => {
            let mainWM = new WorkoutManager(container);
            mainWM.setSentenceString(parsedResponse.splitData[container.dataset.day]);
            mainWM.setExercises(parsedResponse.exercises);
            mainWM.initiateDefault();

            document.getElementsByName(container.dataset.day).forEach(child => {
                let childWM = new WorkoutManager(child);
                childWM.setSentenceString(child.innerHTML.trim());
                childWM.setExercises(parsedResponse.exercises);
                childWM.setMain(mainWM);
                childWM.initiateCopy();
            });
        });
    }
}
xhr.send();