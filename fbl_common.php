<?php
require_once "vendor/autoload.php";

function commonHeader($pageName) {
    echo <<<EOT
    <!DOCTYPE html>
    <html>
    <head>
    <title>FaceMagazine - $pageName</title>
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

function add_user($client, $email, $fname, $scrname,
        $dob, $gender, $vis, $pw, $salt, $status, $location)
{
    $collection = $client->fbl->Members;
    print("collection=$collection");

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

}
?>
