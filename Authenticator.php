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
        db_connect($conn);
        $query = 
            'SELECT password_sum, password_salt
            FROM Member
            WHERE email_address = :email';

        $email = $this->username;
        $password = $this->password;

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, "email", $email);

        oci_execute($stmt);

        $row = oci_fetch_array($stmt);
        oci_close($conn);
        if (!$row) {
            return "No such user";
        }
        if (!check_password($password, hex2bin($row["PASSWORD_SALT"]),
                    hex2bin($row["PASSWORD_SUM"])))
        {
            return "No such password";
        }


        // success
        return "Auth successful";
    }
}

?>
