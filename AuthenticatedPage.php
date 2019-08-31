<?php 
require_once 'Component.php';
require_once 'Authenticator.php';

class AuthenticatedPage extends Component {
    function doAuth() {
        // Security issue: do not use unless the $_SESSION data sent to the
        // client is only the session ID.
        session_start();
        if (!isset($_SESSION["email"])) {
            $authenticator = new Authenticator($_POST["email"], $_POST["pw"]);
            $succ = $authenticator->do_login();
            if ($succ != "Auth successful") {
                $authenticator->fail_redirect();
            }
            $_SESSION["email"] = $_POST["email"];
        }
    }

    function pageMain() {
        $this->doAuth();
        $this->renderHTML();
    }
}
