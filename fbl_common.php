<?php
require_once "vendor/autoload.php";

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

function db_connect(&$client) {
    $client = new MongoDB\Client("mongodb://localhost:27017");
}

function make_password($plaintext) {
    $salt = openssl_random_pseudo_bytes(8);
    $digest = openssl_digest($plaintext . $salt, "SHA256", TRUE);

    return [bin2hex($salt), bin2hex($digest)];
}

function check_password($plaintext, $salt, $digest) {
    $newdigest = openssl_digest($plaintext . $salt, "SHA256", TRUE); 
    return $digest == $newdigest; // subject to timing attack - better to use
                                  //constant-time comparison 
}

/* Attempt to add a user with the provided information to the database. Return an array of errors messages for any missing fields or a message for an existing user */

/* Attempt to add u user with the provided information to the databse. Return an
 * array of erro messages for any missing fields or a message for an existing
 * user; an empty array indicates no errors */
function add_user($client, $email, $fname, $scrname,
        $dob, $gender, $vis, $pw, $salt, $status, $location)
{
    $collection = $client->fbl->Members;

    try {
        $result = $collection->insertOne([
                '_id' => $email,
                'full_name' => $fname,
                'screen_name' => $scrname,
                'date_of_birth' => $dob,
                'gender' => $gender,
                'visibility' => $vis,
                'password_sum' => $pw,
                'password_salt' => $salt,
                'status' => $status,
                'location' => $location
        ]);

        return [];
    } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
        $code = $e->getCode();
        if ($code == "11000") {
            return ["A user with that email already exists!"];
        }
    }

}
?>
