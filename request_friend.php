<?php
require_once "fbl_common.php";
require_once 'Authenticator.php';

session_start();
$auth = new Authenticator(null, null);
$authResult = $auth->do_login();

db_connect($conn);

$us = $_SESSION['email'];
$them = $_GET['target'];

// Make the friend request idempotently - if we already friends, do nothing
$collection = $conn->fbl->Members;

function addRequest($memA, $memB, $requester, $collection) {
    $collection->updateOne(
        ['_id' => $memA,
         'friends' => [
             '$not' => [
                 '$elemMatch' => [
                    'person' => $memB
                    ]
                ]
            ]
        ],
        ['$addToSet' => [
            'friends' => [
                'person' => $memB,
                'requester' => $requester
            ]
        ]]
    );
}

addRequest($us, $them, $us, $collection);
addRequest($them, $us, $us, $collection);

// Allow mongo to throw any exceptions, they should not be made except in // exceptional circumstances

header("http/1.1 303 see other");
header("location: search_people.php?person=".$_POST['searchTerm']);
?>

