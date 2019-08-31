<?php
include "fbl_common.php";
commonHeader("Login");
?>

<body>

<h1>FacebookLite: Login/Register</h1>
<form class="form-box" action="home.php" method="post">
  <?php
  if (isset($_GET['failed'])) {
      echo '<div class="error">Username and password entered were invalid.</div>';
  }
  $email = isset($_POST["email"]) ? $_POST["email"] : "";
  formLayout([
      ["friendly" => "Email Address", "name" => "email", "type" => "text",
      "value" => $email],
      ["friendly" => "Password", "name" => "pw", "type" => "password"]
      ]); ?>
  <input value="Login" type="submit"/>
  <input value="Register" type="submit" formaction="register.php"/>
</form>
</body>
</html>
