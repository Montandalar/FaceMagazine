<?php
require_once 'AuthenticatedPage.php';
require_once 'fbl_common.php';

class ProfileUpdate extends AuthenticatedPage {
    function doUpdate() {
        db_connect($client);
        $collection = $client->fbl->Members;
        $result = $collection->updateOne(['_id' => $_SESSION['email']],
                ['$set' => ['visibility' => $_POST['vis'],
                'location' => $_POST['loc'],
                'status' => $_POST['stat'],
                'screen_name' => $_POST['scr']]
                ]);

        // If no exception happened, either the profile was already correct or
        // has been updately appropriately.
        return true;
    }
    
    function pageMain() {
        $this->doAuth();
        $succ = $this->doUpdate();
        header('HTTP/1.1 303 See Other');
        header('Location: manage.php?success=' . ($succ ? 1 : 0));
    }
}

$page = new ProfileUpdate();
$page->pageMain();

?>
class 
