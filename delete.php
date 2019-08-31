<?php
require_once 'Component.php';
require_once 'CommonHeader.php';
require_once 'FrameworkRoot.php';
require_once 'UserActions.php';
require_once 'DeleteAccount.php';
require_once 'FriendReqs.php';

$page = new AuthenticatedPage([
            new CommonHeader([], ["title" => "Delete account"]),
            new FrameworkRoot([
                'accMgmt' => new UserActions(),
                'del' => new DeleteAccount(),
                'friendReqs' => new FriendReqs(),
            ])
        ]);

$page->pageMain();
