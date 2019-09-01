<?php
require_once "fbl_common.php";
require_once 'Authenticator.php';

session_start();
$auth = new Authenticator(null, null);
$authResult = $auth->do_login();

db_connect($conn);

$queryStr = "delete from friendship where
(member1 = :us and member2 = :them)
or (member2 = :us and member1 = :them)";

$stmt = oci_parse($conn, $queryStr);

oci_bind_by_name($stmt, 'us', $_SESSION['email']);
oci_bind_by_name($stmt, 'them', $_GET['target']);

$succ = oci_execute($stmt);

if (!$succ) {
    echo "There was an error and we could not process your friendship
        cancellation request.";
    exit(1);
}
header("http/1.1 303 see other");
header("location: search_people.php?person=".$_POST['searchTerm']);
?>



