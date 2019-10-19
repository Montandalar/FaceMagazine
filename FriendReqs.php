<?php
require_once 'Component.php';

class FriendReqs extends Component {
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
        $collection = $client->fbl->Members;
        $requests = $collection->find(
                ["friends" => [ '$elemMatch' =>
                    [ "person" => $_SESSION['email'],
                      'accepted' => [
                        '$exists' => false
                      ]
                    ]
                ]],
                ["id" => 1]
        );

        echo '<h2 id="manage-friend-reqs">Manage friend requests</h2>';
        $anyRequests = false;
        foreach ($requests as $request) {
            $this->renderRequest($request);
            $anyRequests = $anyRequests || true;
        }
        if (!$anyRequests) {
            echo "<span>No current friend requests</span>"; 
        }

    }

    function renderRequest($request) {
        ?><li class="friend-request"><?php
        echo '<span class="friend-request-name">',
             $request['screen_name'],'</span>';
        ?><form action="befriend.php" method="post"><?php
        echo '<input type="submit" value="Accept friend request"/>';
        // This is why it's not good to use the email as a primary key - we have
        // just exposed someone's email address
        echo '<input type="hidden" name="target" value="',$request['_id'],'"/>';
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
