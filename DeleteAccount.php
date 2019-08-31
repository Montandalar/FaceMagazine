<?php
require_once 'AuthenticatedPage.php';

class DeleteAccount extends Component {
    function renderHTML() {
        echo <<<EOT
        <div id="main-content-container">
        <h1>Delete your account</h1>
        <p><strong>Warning</strong>: If you delete your account, it is gone
        forever.</p>

        <form action="do_delete.php" method="post">
            <input type="submit" value="Delete my account forever"/>
        </form>
        </div>
EOT;
    }
}
