<?php
require_once 'Component.php';
require_once 'AuthenticatedPage.php';
require_once 'CommonHeader.php';
require_once 'FriendReqs.php';
require_once 'UserActions.php';
require_once 'Authenticator.php';
require_once 'FrameworkRoot.php';
require_once 'ManageAccount.php';
require_once 'AccountForm.php';
require_once 'fbl_common.php';

class UpdateStatus extends Component {
    function renderHTML() {
        echo '<div class="update-status">';
        if ($this->attr['success']) {
            echo '<p>Your profile has been updated</p>';
        } else {
            echo "<p>Your profile couldn't be updated</p>";
        }
        echo '</div>';
    }
}
class ManageAccountPage extends AuthenticatedPage {
    private $udata;

    function pageMain() {
        $this->doAuth();
        $this->udata = $this->queryUserData();
        $this->renderHTML();
    }

    function queryUserData() {
        db_connect($conn);
        $stmt = oci_parse($conn,
                'SELECT screen_name, status, location, visibility
                FROM Member WHERE email_address = :email');

        $email = $_SESSION['email'];
        oci_bind_by_name($stmt, "email", $email);
        oci_execute($stmt);
        $results = oci_fetch_array($stmt, OCI_NUM | OCI_RETURN_LOBS);
        if (!$results) {
            echo "Fatal database error retrieving your profile!";
            exit;
        }
        oci_close($conn);
        return [
            'inline' => 1,
            'scrname' => $results[0],
            'stat' => $results[1],
            'loc' => $results[2],
            'vis' => $results[3],
            'omit' => [
                'email' => 1,
                'pw' => 1,
                'fname' => 1,
                'dob' => 1,
                'gender' => 1,
            ],
            'verb' => 'Update my profile',
            'action' => 'update_profile.php'
        ];
    }

    function renderHTML() {
        if (isset($_GET['success'])) {
            $notice = new UpdateStatus([], ['success' => $_GET['success']]);
        } else {
            $notice = new Component();
        }
        $this->children = [
            new CommonHeader([], ["title" => "Manage account"]),
            new FrameworkRoot([
                'accMgmt' => new UserActions(),
                'mgAcc' => new ManageAccount([$notice, new AccountForm([],
                        $this->udata)]),
                'friendReqs' => new FriendReqs(),
            ])
        ];

        parent::renderHTML();
    }
}


$page = new ManageAccountPage();
$page->pageMain();
?>
