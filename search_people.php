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
    private $client;
    private $friends;

    function renderHTML() {
        db_connect($this->client);

        $members = $this->client->fbl->Members;
        $person = isset($_GET["person"]) ? $_GET["person"] : "";
        $pattern = (new MongoDB\BSON\Regex($person, 'i'));

        $results = $members->find([
                    '$or' => [
                        ['full_name' => $pattern],
                        ['screen_name' => $pattern]
                    ]
                ],
                [ 'projection' => [
                    'screen_name' => 1,
                    'full_name' => 1,
                    'gender' => 1,
                    'status' => 1,
                    'location' => 1,
                    'friends' => 1
                ]]);

        $us = $members->findOne(['_id' => $_SESSION['email']]);
        $this->friends = isset($us['friends']) ? $us['friends'] : [];
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
        foreach ($results as $result){
            // Skip ourselves
            if ($result['_id'] == $_SESSION['email']) { continue; }
            $this->renderRow($result);
        }
    ?></tbody></table></div>
        <?php
    }

    function renderRow($result) {
        echo '<tr>';
        echo "<td>${result['screen_name']}</td>";
        echo "<td>${result['full_name']}</td>";
        echo "<td>${result['gender']}</td>";
        echo "<td>${result['status']}</td>";
        echo "<td>${result['location']}</td>";

        $friendsAlready = false;
        foreach ($this->friends as $friend) {
            if ($friend['accepted'] != null
                    && $friend['person'] == $result['_id'])
            {
                $friendsAlready = true;
                break;
            }
        }

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
