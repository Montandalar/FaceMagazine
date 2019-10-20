<?php
require_once "fbl_common.php";
require_once 'Authenticator.php';

session_start();
$auth = new Authenticator(null, null);
$authResult = $auth->do_login();

db_connect($client);

$us = $_SESSION['email'];
$them = $_GET['target'];

$collection = $client->fbl->Members;

function remove_friend($memA, $memB, $collection) {
    $collection->updateOne(
        ['_id' => $memA,
        'friends' => [
            '$elemMatch' => [
                'person' => $memB
            ]
        ]],
        ['$pull' => ['friends' => ['person' => $memB]]]
    );
}

remove_friend($us, $them, $collection);
remove_friend($them, $us, $collection);

// Allow mongo to throw any exceptions, they should not be made except in
// exceptional circumstances

header("http/1.1 303 see other");
header("location: search_people.php?person=".$_POST['searchTerm']);
?>



