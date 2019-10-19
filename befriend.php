<?php
/* Accept a friend request */
require_once "fbl_common.php";
require_once 'Authenticator.php';

session_start();
$auth = new Authenticator(null, null);
$authResult = $auth->do_login();

db_connect($client);

$us = $_SESSION['email'];
$them = $_POST['target'];

$collection = $client->fbl->Members;
$result = $collection->updateMany(
	[
        '$or' => [ ["_id" => $us], ["_id" => $them] ],
        'friends' => [ '$elemMatch' => [ '$or' => [
            ['person' => $us], ['person' => $them]
         ]]]
	],
	[
		'$set' => [
            'friends.$.accepted' => (new MongoDB\BSON\UTCDateTime(time()*1000))
        ]
    ]
);

// Allow mongo to throw any exceptions, they should not be made except in
// exceptional circumstances

header("http/1.1 303 see other");
header("location: home.php");
?>
