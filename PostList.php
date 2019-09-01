<?php
require_once 'Component.php';
require_once 'fbl_common.php';

class PostList extends Component {

    private $conn;

    function renderHTML() {
        $this->attr['name'] = $_SESSION['email'];
        db_connect($this->conn);
        echo <<<EOT
<div id='main-content-container'>
  <div class="make-post-container">
    <form action="post_reply.php" method="post">
    <p>What are you up to, {$this->attr['name']}</p>
    <textarea name="message"></textarea>
    <input value="Make post" type="submit"/>
    </form>
  </div>\n
EOT;

        $this->renderPosts();
        oci_close($this->conn);
        echo "</div>\n";
    }

    function renderPosts() {
        /* Retrieve the root posts of this user - ones which are not a reply */
        $queryStr = "SELECT POST_ID FROM POST WHERE PARENT_POST_ID IS NULL AND
            POSTER_EMAIL_ADDRESS = :email";
        $stmt = oci_parse($this->conn, $queryStr);
        $email = $_SESSION['email'];
        oci_bind_by_name($stmt, 'email', $email);
        $succ = oci_execute($stmt);

        if (!$succ) {
            echo '<div class="post"><p>Error retrieving your posts</p></div>';
            oci_close($conn);
        }

        while ($row = oci_fetch_row($stmt)) {
            $this->renderPost($row[0]);
        }
        oci_free_statement($stmt);
    }

    function renderPost($postId) {
        /* Select all posts under this with a hierarchical query then render
           them recursively */

        $queryStr = "select post_id, body, posted, screen_name, parent_post_id, l
            from (
                select post_id, body, posted, poster_email_address,
                parent_post_id, level as l from post
                connect by prior post_id = parent_post_id
                start with post_id = :post_id
            ) join member on member.email_address = poster_email_address";

        $stmt = oci_parse($this->conn, $queryStr);
        oci_bind_by_name($stmt, 'post_id', $postId);
        $succ = oci_execute($stmt);

        if (!$succ) {
            echo "Couldn't retrieve post: ", $postId;
        }

        $oldLevel = 0;
        while ($row = oci_fetch_assoc($stmt)) {
            $newLevel = $row['L'];   
            $row['PARENT_POST_ID'];
            //var_dump($row);
            echo "<div class=\"post\" style=\"margin-left:${row['L']}em\">","\n";
            echo '<a class="post-id" id="postno', $row['POST_ID'], '">#',
                 $row['POST_ID'], '</a>',"\n";
            echo '<time class="post-time" datetime="', $row['POSTED'],
                 "\"> ${row['POSTED']}</time>\n";
            echo '<a class="post-name">', $row['SCREEN_NAME'], ' said:</a>';
            echo '<pre class="post-body">',
                 htmlspecialchars($row['BODY']->load()), '</pre>';
            $this->renderLikes($row['POST_ID']);
            echo '<form method="post" class="reply-form" action="post_reply.php">';
            echo '<textarea name="message" placeholder="Write a reply"></textarea>';
            echo '<input type="hidden" name="parent_post" value="',
                 $row['POST_ID'],'"/>';
            echo '<input type="submit" value="Reply"></input>';
            echo '</form>';
            echo "</div>\n";
        }
    }

    function renderLikes($postId) {
        $likes = $this->getNumberLikes($postId);
        $userLikes = $this->getUserLikes($postId, $_SESSION['email']);
        $flavour = $this->getFlavour($postId, $likes);
        /* Choose a flavour text based on how many people have liked and the
           name of someone who has like it */

        

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

    function getFlavour($postId, $likes) {
        $flavour = null;
        if ($likes > 0) {
            $queryStr = "
                select screen_name from ((
                    select MEMBER_EMAIL_ADDRESS from likes 
                    where post_id = :postid and rownum = 1
                ) join member on member_email_address = email_address)";
            $statementNames = oci_parse($this->conn, $queryStr);
            oci_bind_by_name($statementNames, "postid", $postId);
            $succ = oci_execute($statementNames);
            if (!$succ) {
                echo "Somebody likes this";
            }
            $name = oci_fetch_row($statementNames)[0];
            if ($likes == 1) {
                $flavour = "$name likes this";
            } elseif ($likes == 2) {
                $flavour = "$name and 1 other like this";
            } else {
                $flavour = "$name and ".($likes-1)." others like this";
            }
            oci_free_statement($statementNames);
        } else {
            $flavour = "Be the first to like this";
        }
        return $flavour;
    }

    function getUserLikes($postId, $user) {
        $queryStr = "select count(*) from likes
            where post_id = :postid and MEMBER_EMAIL_ADDRESS = :email";
        $stmt = oci_parse($this->conn, $queryStr);
        oci_bind_by_name($stmt, "postid", $postId);
        oci_bind_by_name($stmt, "email", $user);
        $succ = oci_execute($stmt);
        if (!$succ) {
            return null;
        } else {
            return oci_fetch_row($stmt)[0] > 0;
        }
    }

    function getNumberLikes($postId) {
       /* Get the total number of likes */
        $stmtCount = oci_parse($this->conn, "select count(*) from likes where
                post_id = :postid");
        oci_bind_by_name($stmtCount, "postid", $postId);
        $succ = oci_execute($stmtCount);

        $likes = null;
        if (!$succ) {
            echo "Error retrieving likes";
        } else {
            $likes = oci_fetch_row($stmtCount)[0];
        }
        oci_free_statement($stmtCount);
        return $likes;
 }
}
/*
  <div class="post">
  <p>Body</p>
  <!-- ... n posts -->
  </div>
</div>
EOT;
*/
