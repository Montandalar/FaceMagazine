<?php
require_once "fbl_common.php";
require_once 'Authenticator.php';

$auth = new Authenticator(null, null);
$authResult = $auth->do_login();
echo $authResult;

session_start();

var_dump($_POST);

$parent = $_POST["parent_post"];
$msg = $_POST["message"];

db_connect($conn);

$queryStr = 
"INSERT INTO POST(post_id, body, posted, poster_email_address, parent_post_id)
VALUES(seq_postid.nextval, :message, CURRENT_TIMESTAMP, :poster, :parent)";

$stmt = oci_parse($conn, $queryStr);

oci_bind_by_name($stmt, 'message', $msg);
oci_bind_by_name($stmt, 'poster', $_SESSION['email']);
oci_bind_by_name($stmt, 'parent', $parent);

$succ = oci_execute($stmt);

if (!$succ) {
    echo "There was an error and we could not process your reply";
    exit(1);
}
header("http/1.1 303 see other");
header('location: home.php');
?>
