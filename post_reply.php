<?php
require_once "fbl_common.php";
require_once 'Authenticator.php';

$auth = new Authenticator(null, null);
$authResult = $auth->do_login();

session_start();

$parent = isset($_POST["parent_post"]) ? $_POST['parent_post'] : null;
$msg = $_POST["message"];

db_connect($client);
$collection = $client->fbl->Posts;

try {
    $result = $collection->insertOne([
            "body" => $_POST['message'],
            "posted" => (new MongoDB\BSON\UTCDateTime(time()*1000)),
            "poster" => $_SESSION['email'],
            "parent" => $parent
            /* Liked will be empty at time of post - any likes added will be upserted */
    ]);
} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
    echo "There was an error and we could not process your reply";
    exit(1);
}

header("http/1.1 303 see other");
header("location: home.php#postno$parent");
?>
