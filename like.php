<?php
require_once "fbl_common.php";
require_once 'Authenticator.php';

$auth = new Authenticator(null, null);
$authResult = $auth->do_login();

session_start();

db_connect($client);
$collection = $client->fbl->Posts;

$operator = '$addToSet';
if (isset($_POST["unlike"])) {
    $operator = '$pull';
}   
$result = $collection->updateOne(
    ["_id" => (new MongoDB\BSON\ObjectId($_POST['like_target']))],
    [$operator => ['liked' => $_SESSION['email']]]
);

// If something exceptional happens, Mongo will throw an exception.

header("http/1.1 303 see other");
header("location: home.php#postno$likeTarget");
?>

