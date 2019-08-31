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
    <form action="make_post.php" method="post">
    <p>What are you up to, {$this->attr['name']}</p>
    <textarea name="body"></textarea>
    <input value="Make post" type="submit"/>
    </form>
  </div>
EOT;

        $this->renderPosts();
        oci_close($this->conn);
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
        } else {
            echo "Success retrieving posts. Number of results: ",
                 oci_num_rows($stmt);
        }

        echo "Your posts: [";
        while ($row = oci_fetch_row($stmt)) {
            $this->renderPost($row[0]);
        }
        echo "]";
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

        $level = 0;
        while ($row = oci_fetch_assoc($stmt)) {
            //var_dump($row);
            echo '<p>',$row['POST_ID'], $row['BODY']->load(), $row['POSTED'],
                 $row['SCREEN_NAME'], $row['PARENT_POST_ID'], $row['L'],'</p>';
        }


        /*
        echo '<div class="post">
            <span class="post-id">#</span>
            */
        echo $postId, ",";
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
