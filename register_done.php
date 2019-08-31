<?php
include "fbl_common.php";

$fields = ["email", "pw", "fname", "scrname", "dob", "gender", "vis",
"status", "location"];

function validate_fields($fields) {
  $missing = [];
  foreach ($fields as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] == "") {
      array_push($missing, $field);
    }
  }
  return $missing;
}

$err = validate_fields($fields);
if ($err == []) {
    db_connect($conn);
    $creds = make_password($_POST["pw"]);
    $salt = $creds[0];
    $pw = $creds[1];

    add_user($conn, $_POST["email"], $_POST["fname"], $_POST["scrname"],
            $_POST["dob"], $_POST["gender"], $_POST["vis"], $pw,
            $salt, $_POST["status"], $_POST["location"]);

    oci_close($conn);
}

?>
<!DOCTYPE html>
<html>
<head>
<title>FacebookLite: Registering...</title>
</head>

<body>
<?php if ($err == []): ?>
  <h1>Congratulations! </h1>
  <p>You are now registered. Your details:</p>
  <?php 
  foreach ($fields as $field) {
    echo $field . "=" . $_POST[$field];
    echo isset($_POST[$field]) ? "//set" : "//unset";
    echo $_POST[$field] == "" ? "(blank)" : "(not blank)";
    echo "<br/>";
  }
  ?>
  <p><a href="login.php">Return to login now</a></p>
<?php else: ?>
  <p>Sorry, you didn't submit all the fields in your registration, or another
  error occurred:</p><ul>
  <?php
  foreach ($err as $msg) {
    echo "<li>" . $msg . "\n";
  }
  ?></ul>
<?php endif ?>
</body>
