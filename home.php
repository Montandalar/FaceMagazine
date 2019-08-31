<?php
require_once 'Component.php';
require_once 'AuthenticatedPage.php';
require_once 'CommonHeader.php';
require_once 'FriendReqs.php';
require_once 'UserActions.php';
require_once 'PostList.php';
require_once 'Authenticator.php';
require_once 'FrameworkRoot.php';

$hp = new AuthenticatedPage([
        new CommonHeader([], ["title" => "Home"]),
        new FrameworkRoot([
            'accMgmt' => new UserActions(),
            'postList' => new PostList([], ["name" => "wew lad"]),
            'friendReqs' => new FriendReqs(),
        ])
    ]);

$hp->pageMain();
?>
