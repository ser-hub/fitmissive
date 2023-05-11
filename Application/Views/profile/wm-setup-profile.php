<script type="module">
    import WorkoutManager from "/Application/Views/Common/js/WorkoutManager/workoutManager.js"
    let workoutContainers = document.getElementsByName('workout-container')

    let xhr = new XMLHttpRequest()
    xhr.open('GET', `/home/getInitalWorkoutData`, true)
    xhr.onload = function() {
        if (this.status == 200) {
            let parsedResponse = JSON.parse(this.response)
            workoutContainers.forEach(container => {
                let userWM = new WorkoutManager(container)
                userWM.setSentenceString(container.innerHTML.trim())
                userWM.setExercises(parsedResponse.exercises)

                <?php if ($isAdmin) { ?>
                    userWM.setUser('<?= $username ?>')
                    userWM.initiateDefault()
                <?php } elseif ($user->user_id == $data['loggedUser']) { ?>
                    userWM.initiateDefault()
                <?php } else { ?>
                    userWM.initiateView()
                <?php } ?>
            })
        }
    }
    xhr.send()
</script>