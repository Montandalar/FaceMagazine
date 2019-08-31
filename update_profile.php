<?php
require_once 'AuthenticatedPage.php';
require_once 'fbl_common.php';

class ProfileUpdate extends AuthenticatedPage {
    function doUpdate() {
        db_connect($conn);
        $stmt = oci_parse($conn,
                'UPDATE Member
                SET screen_name = :scr, location = :loc, status = :stat,
                visibility = :vis
                WHERE email_address = :email'
                );

        $email = $_SESSION['email'];
        $loc = $_POST['loc'];
        $stat = $_POST['stat'];
        $scr = $_POST['scr'];
        $vis = $_POST['vis'];

        oci_bind_by_name($stmt, "loc", $loc);
        oci_bind_by_name($stmt, "stat", $stat);
        oci_bind_by_name($stmt, "scr", $scr);
        oci_bind_by_name($stmt, "vis", $vis);
        oci_bind_by_name($stmt, "email", $email);

        $ret = oci_execute($stmt);
        oci_close($conn);
        return $ret;

    }
    
    function pageMain() {
        $this->doAuth();
        $succ = $this->doUpdate();
        header('HTTP/1.1 303 See Other');
        header('Location: manage.php?success=' . ($succ ? 1 : 0));
    }
}

$page = new ProfileUpdate();
$page->pageMain();

?>
class 
