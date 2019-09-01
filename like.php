<?php
require_once "fbl_common.php";
require_once 'Authenticator.php';

$auth = new Authenticator(null, null);
$authResult = $auth->do_login();

session_start();

$likeTarget = $_POST["like_target"];

db_connect($conn);

$queryStr = "";
if (isset($_POST["unlike"])) {
    $queryStr =
        "delete from likes
        where member_email_address = :email
        and post_id = :postid";
}
else {
    $queryStr = 
        "insert into likes(member_email_address, post_id)
        values (:email, :postid)";
}
$stmt = oci_parse($conn, $queryStr);

oci_bind_by_name($stmt, 'email', $_SESSION['email']);
oci_bind_by_name($stmt, 'postid', $likeTarget);

$succ = oci_execute($stmt);

if (!$succ) {
    echo "There was an error and we could not process your like";
    exit(1);
}
header("http/1.1 303 see other");
header("location: home.php#postno$likeTarget");
?>

