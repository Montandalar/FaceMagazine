<?php
require_once 'AuthenticatedPage.php';
require_once 'fbl_common.php';

class DeleteAction extends AuthenticatedPage {
    function doDelete() {
        db_connect($conn);
        $stmt = oci_parse($conn, 'DELETE FROM member where email_address = :email');
        $email = $_SESSION['email'];
        oci_bind_by_name($stmt, "email", $email);
        $succ = oci_execute($stmt);
        oci_close($conn);
        if ($succ) {
            session_destroy();
            echo "Your account is gone forever";
        } else {
            echo "There was an error and we couldn't delete your account.";
        }

    }
    
    function pageMain() {
        $this->doAuth();
        $this->doDelete();
    }
}

$page = new DeleteAction();
$page->pageMain();

?>
