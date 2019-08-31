<?php
require_once 'Component.php';

class UserActions extends Component {
    function renderHTML() {
        echo <<<EOT
<div id='account-management'>
  <ul class="plain">
  <li><a href="home.php">Home</a></li>
  <li><a href="manage.php">Update your profile</a></li>
  <li><a href="delete.php">Delete your account</a></li>
  <li><a href="logout.php">Log out</a><li>
  </ul>
</div>
EOT;
    }
}
