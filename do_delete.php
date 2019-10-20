<?php
require_once 'AuthenticatedPage.php';
require_once 'fbl_common.php';

class DeleteAction extends AuthenticatedPage {
    function doDelete() {
        $email = $_SESSION['email'];

        db_connect($client);
        $collection = $client->fbl->Members;
        $us = $_SESSION['email'];
        $result = $collection-> deleteOne(['_id' => $us]);
        $collection->updateMany([
                "friends" => [
                    '$elemMatch' => [
                        'person' => $us
                    ]
                ]],
                [
                '$pull' => [
                    'friends' => [
                        'person' => $us
                    ]
                ]]
            );
        $collection->updateMany(
            [
                'liked' => [
                    '$elemMatch' => [
                        '$eq' => $us
                    ]
                ]

            ],
            [
                '$pull' => [
                    'liked' => $us
                ]
            ]
            );

        if ($result->getDeletedCount() > 0) {
            session_destroy();
            echo "Your account is gone forever";
        } else {
            echo "There was an error and we couldn't delete your account.";
        }

    }
    
    function pageMain() {
        $this->doAuth();
        $this->doDelete();
    }
}

$page = new DeleteAction();
$page->pageMain();

?>
