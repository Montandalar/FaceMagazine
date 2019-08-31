<?php
session_start();
session_destroy();
header('HTTP/1.1 307 Temporary Redirect');
header('Location: login.php');
?>
