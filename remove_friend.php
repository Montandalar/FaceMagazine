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

// Update us
$collection->updateOne(
    ['_id' => $us,
    'friends' => [
        '$elemMatch' => [
            'person' => $them
        ]
    ]],
    ['$pull' => ['friends' => ['person' => $them]]]
);

// Update them
$collection->updateOne(
    ['_id' => $them,
    'friends' => [
        '$elemMatch' => [
            'person' => $them
        ]
    ]],
    ['$pull' => ['friends' => ['person' => $us]]]
);

// Allow mongo to throw any exceptions, they should not be made except in
// exceptional circumstances

header("http/1.1 303 see other");
header("location: search_people.php?person=".$_POST['searchTerm']);
?>



