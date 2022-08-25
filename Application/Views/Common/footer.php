    <footer class="footer-container">
        <div class="infos">
            <div class="row">
                <div class="footer-items">
                    <a href="/home">Home</a>
                    <?php
                    if ($data['info']) {
                        foreach ($data['info'] as $info) {
                            echo ' | <a href="/info/' . $info->slug . '">' . $info->title . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="footer-items">
                    All rights reserved 2022
                </div>
            </div>
        </div>
    </footer>
    </body>

    </html>