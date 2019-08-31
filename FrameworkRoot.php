<?php
require_once 'Component.php';

class FrameworkRoot extends Component {
    function renderHTML() {
        echo '<body>';
        echo '<div id="framework-root">';
        parent::renderHTML();
        echo '</div>';
        echo '</body></html>';
    }
}
