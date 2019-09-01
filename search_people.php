<?php
require_once "Authenticator.php";
require_once 'AuthenticatedPage.php';
require_once "Component.php";
require_once "fbl_common.php";
require_once 'CommonHeader.php';
require_once 'FrameworkRoot.php';
require_once 'UserActions.php';
require_once 'FriendReqs.php';

class SearchResults extends Component {
    private $conn;

    function renderHTML() {
        db_connect($this->conn);

        $queryStr = "
            select screen_name, full_name, gender, status, location,
                   email_address
            from member where upper(screen_name) like '%' || :person || '%'
            or upper(full_name) like '%' || :person || '%'";

        $person = isset($_GET["person"]) ? $_GET["person"] : "";
        $person = strtoupper($person);
        $stmt = oci_parse($this->conn, $queryStr);
        oci_bind_by_name($stmt, "person", $person);

        $succ = oci_execute($stmt);

        if (!$succ) {
            echo "Error searching";
            return;
        }

        ?>
<div id="main-content-container">
<h1>Friend search results</h1>
<?php echo "<p>Friends like: $person</p>"; ?>
<table id='search-results'>
<thead><tr>
    <th>Screen name</th>
    <th>Full name</th>
    <th>Gender</th>
    <th>Status</th>
    <th>Location</th>
    <th>Friendship</th>
</tr></thead>
<tbody>
        <?php
        while ($row = oci_fetch_row($stmt)) {
            // Skip ourselves
            if ($row[5] == $_SESSION['email']) { continue; }
            $this->renderRow($row);
        }
    ?></tbody></table></div>
        <?php
    }

    function renderRow($row) {
        echo '<tr>';
        for ($col = 0; $col < 5; ++$col) {
            if ($col == 3) { //status
                echo '<td>',$row[$col]->load(),'</td>';
                continue;
            }
            echo "<td>",$row[$col],'</td>';
        }
        $targetEmail = $row[5];
        $queryStr = "select COUNT(*) from friendship
            where (member1 = :us and member2 = :them)
            or (member2 = :us and member1 = :them)";
        $stmt = oci_parse($this->conn, $queryStr);
        oci_bind_by_name($stmt, "us", $_SESSION['email']);
        oci_bind_by_name($stmt, "them", $targetEmail);
        $succ = oci_execute($stmt);
        $friendsAlready = oci_fetch_row($stmt)[0] > 0;
        if ($friendsAlready) {
            ?><td>
                <form action="remove_friend.php">
                <input type="submit" value="Remove friend/Cancel request"/><?php
        } else {
            ?><td>
                <form action="request_friend.php">
                <input type="submit" value="Send friend request"/><?php
        }
        // Common form elements
        echo '<input type="hidden" name="target" value="',
             $targetEmail,'"/>';
        echo '<input type="hidden" name="search_term" value="',
             $_GET['person'],'"/>';
        echo "</form></td>\n";
        echo "</tr>\n";
    }
}

class SearchPage extends AuthenticatedPage {
    private $conn;

    function renderHTML() {
        $this->children = [
            new CommonHeader([], ["title" => "Find friends"]),
            new FrameworkRoot([
                'accMgmt' => new UserActions(),
                'mgAcc' => new SearchResults(),
                'friendReqs' => new FriendReqs(),
            ])
        ];

        parent::renderHTML();
    }
}

$page = new SearchPage();
$page->pageMain();
