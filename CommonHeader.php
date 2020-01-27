<?php
require_once 'Component.php';

class CommonHeader extends Component {
    function renderHTML() {
        echo<<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>FaceMagazine - {$this->attr['title']}</title>
    <link rel="stylesheet" type="text/css" href="styles.css" media="screen">
    <meta charset="utf-8">
</head>

EOT;
    }
}
?>
