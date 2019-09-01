<?php
require_once 'Component.php';
require_once 'fbl_common.php';

class PostList extends Component {

    private $conn;

    function renderHTML() {
        db_connect($this->conn);
        $stmt = oci_parse($this->conn,
                "SELECT Screen_name FROM Member WHERE email_address = :email");
        oci_bind_by_name($stmt, "email", $_SESSION['email']);
        $succ = oci_execute($stmt);
        if (!$succ) {
            $this->attr['name'] = "FacebookLite user";
        } else {
            $this->attr['name'] = oci_fetch_row($stmt)[0];
        }
        oci_free_statement($stmt);
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
        oci_close($this->conn);
        echo "</div>\n";
    }

    function renderPosts() {
        /* Retrieve the root posts of this user - ones which are not a reply */
        $queryStr = <<<EOT
-- All root posts that should appear on the home page for a user
select post_id from (
(
-- All public posts
select post_id, posted from (
select post_id, posted, poster_email_address from post where parent_post_id is null)
join (select email_address from member where visibility = 0)
on poster_email_address = email_address
)
UNION
-- Posts of friends set to friends only
(
select post_id, posted from post
where parent_post_id is null and poster_email_address in (
select member from
    (select email_address from member where visibility = 1)
    JOIN (
        -- Friends who have accepted requests
        SELECT member2 as member from friendship where member1 = :email
        and accepted is not null
        union
        select member1 as member from friendship where member2 = :email
        and accepted is not null
    ) on email_address = member
)
UNION
-- Our own posts
(
SELECT POST_ID, POSTED FROM POST
WHERE PARENT_POST_ID IS NULL AND POSTER_EMAIL_ADDRESS = :email
)
)
order by posted desc --newest first
)
EOT;
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
            $queryStr = "
                select screen_name, email_address from ((
                    select MEMBER_EMAIL_ADDRESS from likes 
                    where post_id = :postid and rownum = 1
                ) join member on member_email_address = email_address)";
            $statementNames = oci_parse($this->conn, $queryStr);
            oci_bind_by_name($statementNames, "postid", $postId);
            $succ = oci_execute($statementNames);
            if (!$succ) {
                echo "Somebody likes this";
            }
            $row = oci_fetch_row($statementNames);
            $name = $row[0];
            $email = $row[1];
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
            $result = oci_fetch_row($stmt)[0];
            oci_free_statement($stmt);
            return $result;
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
