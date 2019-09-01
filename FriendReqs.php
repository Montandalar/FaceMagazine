<?php
require_once 'Component.php';

class FriendReqs extends Component {
    function renderHTML() {
        $this->renderRequests();
        $this->renderSearchPane();
    }

    function renderRequests() {
        echo '<div id="manage-friend-reqs">';
        db_connect($conn);
        $queryStr = "select screen_name from (
                        select member1 from friendship
                        where accepted is null and
                        member2 = :us)
                    join MEMBER on MEMBER1 = EMAIL_ADDRESS";
        $stmt = oci_parse($conn, $queryStr);
        oci_bind_by_name($stmt, "us", $_SESSION['email']);
        $succ = oci_execute($stmt);
        if (!$succ) {
            echo "<span>Error retrieving friend requests</span></div>";
            return;
        }
        while ($row = oci_fetch_row($stmt)) {
            $this->renderRequest($row);
        }
        echo '</div>';
    }

    function renderRequest($row) {
        echo $row[0], ", ";
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
