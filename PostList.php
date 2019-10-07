<?php
require_once 'Component.php';
require_once 'fbl_common.php';

class PostList extends Component {

    private $client;

    function renderHTML() {
        db_connect($this->client);
        $collection = $this->client->fbl->Members;
        try {
            $result = $collection->findOne(["_id" => $_SESSION['email']],
                ['screen_name' => 1]);
            $this->attr['name'] = $result['screen_name'];
        } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
            $this->attr['name'] = "FacebookLite User";
        }
        echo <<<EOT
<div id='main-content-container'>
  <div class="make-post-container">
    <form action="post_reply.php" method="post">
    <p>What are you up to, {$this->attr['name']}?</p>
    <textarea name="message"></textarea>
    <input value="Make post" type="submit"/>
    </form>
  </div>\n
EOT;

        $this->renderPosts();
        echo "</div>\n";
    }

    function renderPosts() {
        /* Retrieve the root posts of this user - ones which are not a reply
        This includes -
        - All root posts that should appear on the home page for a user
        - All public posts
        UNION
        - Posts of friends set to friends only
        UNION
        - Our own posts
        */

        $collection = $this->client->fbl->Posts;
        $documents = $collection->find(
                ["poster" => $_SESSION["email"], "parent" => null],
                ['projection' => ['_id' => 1]]
                );

        foreach ($documents as $post) {
            $this->renderPost($post, 0);
        }

    }

    function renderPost($post, $level) {
        $collection = $this->client->fbl->Posts;

        /* Select direct descendents of this post with an aggregation,
           rendering them them recursively. Retrieve them in posted order. */
		$post = $collection->aggregate([
            ['$match' => [
                "_id" => (new MongoDB\BSON\ObjectId($post['_id']))
            ]],
            ['$graphLookup' => [
                "from" => "Posts",
                "startWith" => (new MongoDB\BSON\ObjectId($post["_id"])),
                "connectFromField" => "_id",
                "connectToField" => "parent",
                "as" => "children",
                "depthField" => "depth",
                "maxDepth" => 0
            ]],
            ['$sort' => [
                'posted' => 1
            ]]
        ]);

        // Result of the aggregation is only one document
        $post = $post->toArray()[0];

        // Render the parent
        $this->renderSinglePost($post['_id'], $post['body'],
                date(DateTimeInterface::ISO8601,
                    $post['posted']->__toString()/1000),
                $post['poster'], $level);

        $children = $post["children"];

        foreach ($children as $child) {
            $this->renderPost($child, $level+1);
        }
    }

    private function renderSinglePost($post_id, $body, $posted, $poster, $level) {
        echo "<div class=\"post\" style=\"margin-left:${level}em\">","\n";
        echo '<a class="post-id" id="postno', $post_id, '">#',
             $post_id, '</a>',"\n";
        echo '<time class="post-time" datetime="', $posted,
        "\"> $posted</time>\n";
        echo '<a class="post-name">', $poster, ' said:</a>';
        echo '<pre class="post-body">',
             htmlspecialchars($body), '</pre>';
        $this->renderLikes($post_id);
        echo '<form method="post" class="reply-form" action="post_reply.php">';
        echo '<textarea name="message" placeholder="Write a reply"></textarea>';
        echo '<input type="hidden" name="parent_post" value="',
             $post_id,'"/>';
        echo '<input type="submit" value="Reply"></input>';
        echo '</form>';
        echo "</div>\n";
    }

    function renderLikes($postId) {
        $likes = $this->getNumberLikes($postId);
        $userLikes = $this->getUserLikes($postId, $_SESSION['email']);
        $flavour = $this->getFlavour($postId, $_SESSION['email'], $likes);

        echo '<form method="post" action="like.php">';
        echo "<span class=\"like-flavour\">$flavour</span>";
        if ($userLikes) {
            echo '<input type="submit" value="Unlike" />';
            echo '<input type="hidden" name="unlike" value="1" />';
        } else {
            echo '<input type="submit" value="Like" />';
        }
        echo '<input type="hidden" name="like_target" value="',$postId,'"/>';
        echo '</form>';
    }

    function getFlavour($postId, $user, $likes) {
        $flavour = null;
        if ($likes > 0) {
            $collection = $this->client->fbl->Members;
            $result = $collection->findOne(['_id' => $user], ['_id' => 1,
                    'screen_name' => 1]);
            if ($result == null) {
                echo "Somebody likes this";
            }
            $name = $result['screen_name'];
            $email = $result['_id'];
            if ($email == $user) {
                $name = 'You';
            }
            if ($likes == 1) {
                $flavour = "$name liked this";
            } elseif ($likes == 2) {
                $flavour = "$name and 1 other like this";
            } else {
                $flavour = "$name and ".($likes-1)." others like this";
            }
        } else {
            $flavour = "Be the first to like this";
        }

        return $flavour;
    }

    function getUserLikes($postId, $user) {
        $collection = $this->client->fbl->Posts;
        $result = $collection->findOne(['liked' => $user], ['_id' => 1]);
        return ($result == null);
    }

    function getNumberLikes($postId) {
        /* Get the total number of likes */
        $collection = $this->client->fbl->Posts;
        $result = $collection->aggregate([
                    ['$match' => [ "_id" => $postId ]],
                    ['$project' => [
                        'likes' => [ '$cond' => [
                            'if' => [ 'isArray' => '$liked' ],
                            'then' => [ '$size' => ['$ifNull' => ['$liked', []]] ],
                            'else' => 0
                        ]]
                    ]]
                ]);

        $result = $result->toArray();
        if (count($result) == 0) {
            return 0;
        } else {
            return $result[0]['likes'];
        }
 }
}
