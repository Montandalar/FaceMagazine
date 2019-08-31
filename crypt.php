<?php
include "fbl_common.php";
echo "<pre>";
$ds = make_password("wew lad");
$same = check_password($_POST["pw"], $ds[0], $ds[1]);
echo "\n\n";
echo $same ? "equal" : "not equal";
echo "</pre>";
?>

<form action="crypt.php" method="post">
  <input type="password" name="pw"/>
  <input type="submit"/>
</form>
