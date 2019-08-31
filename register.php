<?php
require_once 'Component.php';
require_once 'AccountForm.php';
require_once 'CommonHeader.php';
require_once 'H1.php';

$email = isset($_POST["email"]) ? $_POST["email"] : "";
$pw = isset($_POST["pw"]) ? $_POST["pw"] : "";
$page = new Component([
            new CommonHeader([], ['title' => 'Registration']),
            new H1([], ['text' => 'Registration']),
            new AccountForm([], ['action' => 'register_done.php', 'email' =>
                $email, 'pw' => $pw, 'verb' => 'Register'])
        ]);
$page->renderHTML();
?>

