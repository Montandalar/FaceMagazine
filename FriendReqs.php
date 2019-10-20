<?php
require_once 'Component.php';

class FriendReqs extends Component {
    private $client;

    function renderHTML() {
        ?> <div id="manage-friends"> <?php
        $this->renderRequests();
        $this->renderSearchPane();
        ?> </div> <?php
    }

    function renderRequests() {
        echo '<div id="manage-friend-reqs">';
        echo '<ul class="friend-request-list">';

        db_connect($client);
        $this->collection = $client->fbl->Members;
        $us = $_SESSION['email'];
        // Show requests for acceptance only when requester is NOT $us
        $requests = $this->collection->findOne(
                ["_id" => $us,
                "friends" => [ '$elemMatch' =>
                    [ "person" => [
                        '$not' => [ '$eq' =>  $us ]
                      ],
                      'accepted' => [
                        '$exists' => false
                      ],
                      'requester' => [
                        '$not' => [ '$eq' =>  $us ]
                      ]
                    ]
                ]],
                [
                    'projection' => [
                        "friends.$.person" => 1
                    ]
                ]
        );

        echo '<h2 id="manage-friend-reqs">Manage friend requests</h2>';
        $anyRequests = false;
        $requesters = $requests['friends'];
        if ($requesters == null) {
            echo "<span>No current friend requests</span>"; 
            return;
        }
        foreach ($requesters as $requester) {
            assert($requester['requester'] != $us);
            $this->renderRequest($requester['person']);
            $anyRequests = $anyRequests || true;
        }

    }

    function renderRequest($requester) {
        $result = $this->collection->findOne(
                [ "_id" => $requester],
                ["projection" => [
                    "screen_name" => 1
                ]]);
        ?><li class="friend-request"><?php
        echo '<span class="friend-request-name">',
             $result['screen_name'],'</span>';
        ?><form action="befriend.php" method="post"><?php
        echo '<input type="submit" value="Accept friend request"/>';
        // This is why it's not good to use the email as a primary key - we have
        // just exposed someone's email address
        echo '<input type="hidden" name="target" value="',$result['_id'],'"/>';
        ?></form></li><?php
    }

    function renderSearchPane() {
        ?>
       <div id="add-friends">
            <form action="search_people.php">
            <input type="text" name="person" placeholder="Search people..."/>
            <input value="Search" type="submit"/>
            </form>
        </div>
    </div>
    <?php
    }
}
