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

        db_connect($conn);
        $queryStr = "select screen_name, email_address from (
                        select member1 from friendship
                        where accepted is null and
                        member2 = :email)
                    join MEMBER on MEMBER1 = EMAIL_ADDRESS";
        $stmt = oci_parse($conn, $queryStr);
        oci_bind_by_name($stmt, "email", $_SESSION['email']);
        $succ = oci_execute($stmt);
        if (!$succ) {
            echo "<span>Error retrieving friend requests</span></div>";
            goto cleanup;
        }

        $counter = 0;
        echo '<h2 id="manage-friend-reqs">Manage friend requests</h2>';
        while ($row = oci_fetch_row($stmt)) {
            $this->renderRequest($row);
            $counter++;
        }
        if ($counter == 0) {
            echo "<span>No current friend requests</span>"; 
        }

        /*echo "Rendered $counter reqs<Br/>";
        echo "email: ",$_SESSION['email'];*/
        echo '</div>';
        cleanup: {
            oci_free_statement($stmt);
            oci_close($conn);
        }
    }

    function renderRequest($row) {
        ?><li class="friend-request"><?php
        echo '<span class="friend-request-name">',$row[0],'</span>';
        ?><form action="befriend.php" method="post"><?php
        echo '<input type="submit" value="Accept friend request"/>';
        // This is why it's not good to use the email as a primary key - we have
        // just exposed someone's email address
        echo '<input type="hidden" name="target" value="',$row[1],'"/>';
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
