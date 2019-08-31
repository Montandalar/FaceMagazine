<?php
function commonHeader($pageName) {
    echo <<<EOT
    <!DOCTYPE html>
    <html>
    <head>
    <title>FacebookLite - $pageName</title>
    <link rel="stylesheet" type="text/css" href="styles.css" media="screen">
    </head>
EOT;
}

function formLayout($fields) {
    foreach ($fields as $field) {
        foreach (["name", "friendly", "type", "required", "value"] as $key) {
            if (!array_key_exists($key, $field)) { 
                $field[$key] = "";
            }
        }
        echo <<<EOT
<div>
  <label class="formLabel" for="$field[name]">$field[friendly]</label>
  <input type="$field[type]" name="$field[name]" value="$field[value]" required="$field[required]"/>
</div>

EOT;
    }
}

function db_connect(&$handle) {
    $handle = oci_connect("S3606501", "Oracle4G",
            "talsprddb01.int.its.rmit.edu.au/CSAMPR1.ITS.RMIT.EDU.AU");
    if(!$handle) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    $stmt = oci_parse($handle, 
        "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");

    if (!oci_execute($stmt)) {
        echo "Couldn't set database date format!";
    }

    $stmt = oci_parse($handle,
            "ALTER SESSION SET NLS_TIMESTAMP_FORMAT =
            'YYYY-MM-DD HH24:MI'"
            );

    if (!oci_execute($stmt)) {
        echo "Couldn't set the database timestamp format!";
    }
}

function make_password($plaintext) {
    $salt = openssl_random_pseudo_bytes(8);
    $digest = openssl_digest($plaintext . $salt, "SHA256", TRUE);

    return [$salt, $digest];
}

function check_password($plaintext, $salt, $digest) {
    $newdigest = openssl_digest($plaintext . $salt, "SHA256", TRUE); 
    return $digest == $newdigest; // subject to timing attack - better to use
                                  //constant-time comparison 
}

function add_user($conn, $email, $fname, $scrname,
        $dob, $gender, $vis, $pw, $salt, $status, $location)
{
    $querystr = <<<EOT
INSERT INTO MEMBER(email_address, full_name, screen_name, date_of_birth, gender,
        visibility, password_sum, password_salt, status, location)
VALUES(:email, :fname, :scrname, :dob, :gender, :vis, :pw, :salt, :status, :location)
EOT;

    $pw = bin2hex($pw);
    $salt = bin2hex($salt);

    $stmt = oci_parse($conn, $querystr);
    oci_bind_by_name($stmt, "email", $email);
    oci_bind_by_name($stmt, "fname", $fname);
    oci_bind_by_name($stmt, "scrname", $scrname);
    oci_bind_by_name($stmt, "dob", $dob);
    oci_bind_by_name($stmt, "gender", $gender);
    oci_bind_by_name($stmt, "vis", $vis);
    oci_bind_by_name($stmt, "pw", $pw);
    oci_bind_by_name($stmt, "salt", $salt);
    oci_bind_by_name($stmt, "status", $status);
    oci_bind_by_name($stmt, "location", $location);

    return oci_execute($stmt);
}
?>
