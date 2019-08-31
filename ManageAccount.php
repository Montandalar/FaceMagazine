<?php 
require_once 'Component.php';

class ManageAccount extends Component {
    function renderHTML() {
        echo '<div id="main-content-container">';
        echo '<h1>Update your profile</h1>';
        parent::renderHTML();
        echo '</div>';
    }
}
