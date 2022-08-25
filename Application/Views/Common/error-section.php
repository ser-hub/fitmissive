<div class="error-section">
    <?php

    use Application\Utilities\Session;

    if (isset($errors)) {
        foreach ($errors as $error) {
            echo '<div class="error">' . $error . '</div>';
        }
    } else if (isset($data[$errorsLabel])) {
        foreach ($data[$errorsLabel] as $error) {
            echo '<p class="error">' . $error . '</p>';
        }
    } else if (Session::exists('success')) {
        echo '<p class="success">' . Session::flash('success') . '</p>';
    } else if (Session::exists('error')) {
        echo '<p class="error">' . Session::flash('error') . '</p>';
    }
    ?>
</div>