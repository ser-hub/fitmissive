import WorkoutManager from "./WorkoutManager/workoutManager.js"

let workoutContainers = document.getElementsByName('workout-container')

let xhr = new XMLHttpRequest()
xhr.open('GET', `/home/getInitalWorkoutData`, true)
xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
xhr.onload = function () {
    if (this.status == 200) {
        let parsedResponse = JSON.parse(this.response)
        workoutContainers.forEach(container => {
            let mainWM = new WorkoutManager(container)
            mainWM.setSentenceString(parsedResponse.splitData[container.dataset.day])
            mainWM.setExercises(parsedResponse.exercises)
            mainWM.initiateDefault()

            document.getElementsByName(container.dataset.day).forEach(child => {
                let childWM = new WorkoutManager(child)
                childWM.setSentenceString(child.innerHTML.trim())
                childWM.setExercises(parsedResponse.exercises)
                childWM.setMain(mainWM)
                childWM.initiateCopy()
            })
        })
    }
}
xhr.send()