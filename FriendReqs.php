<?php
require_once 'Component.php';

class FriendReqs extends Component {
    function renderHTML() {
        echo <<<EOT
<div id="manage-friends">
    <div id="manage-friend-reqs">
        <div class="friend-req">
        </div>
        <!--- ... n requests -->
    </div>

    <div id="add-friends">
        <form action="search_people.php">
        <input type="text" name="person" placeholder="Search people..."/>
        <input value="Search" type="submit"/>
        </form>
    </div>
</div>

EOT;
    }

}
