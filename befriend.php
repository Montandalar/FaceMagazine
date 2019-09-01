<?php
/* Accept a friend request */
require_once "fbl_common.php";
require_once 'Authenticator.php';

session_start();
$auth = new Authenticator(null, null);
$authResult = $auth->do_login();

db_connect($conn);

$queryStr = "UPDATE friendship
            SET accepted = CURRENT_TIMESTAMP
            WHERE Member2 = :us and Member1 = :them";

$stmt = oci_parse($conn, $queryStr);

oci_bind_by_name($stmt, 'us', $_SESSION['email']);
oci_bind_by_name($stmt, 'them', $_POST['target']);

$succ = oci_execute($stmt);

if (!$succ) {
    echo "There was an error and we could not process your friendship
        acceptance request.";
    exit(1);
}
header("http/1.1 303 see other");
header("location: home.php");
?>




