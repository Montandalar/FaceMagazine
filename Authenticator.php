<?php
require_once 'fbl_common.php';

class Authenticator {
    private $username;
    private $password;

    function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    function success_redirect() {
        $_SESSION["email"] = $_POST["email"];
        header("HTTP/1.1 303 See Other");
        header('Location: home.php');
    }

    function fail_redirect() {
        header("HTTP/1.1 307 Temporary Redirect");
        header('Location: login.php?failed=1');
    }

    function do_login() {
        if (!isset($_SESSION['email'])) { // absolutely not secure, use a token
            $result = $this->authenticate();
        }
        else {
            // Already logged in
            $result = "Auth successful";
        }
        return $result;
    }

    private function authenticate() {
        db_connect($client);

        $collection = $client->fbl->Members;
        $doc = $collection->findOne(["_id" => $this->username],
                ["projection" => [
                    "password_sum" => 1,
                    "password_salt" => 1   
                ]]
        );

        if ($doc == null) {
            return "No such user";
        }
        if (!check_password($this->password, hex2bin($doc["password_salt"]),
                    hex2bin($doc["password_sum"])))
        {
            return "No such password";
        }

        // success
        return "Auth successful";
    }
}

?>
