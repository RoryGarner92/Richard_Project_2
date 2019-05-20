<?php
$timeStamp = date("F j, Y, g:i a");
$user = 'root';
$pass = '';
$db = 'Project2';
$db = new mysqli('localhost', $user, $pass, $db);

if ($db->connect_errno) {

    $message = "Action: "."Failed to connect to Database"."?????".
    "Location: "."config.php" ."?????".
    "Timestamp: ".$timeStamp ."?????".
    "Action: ".$db->connect_error ."?????";

    $sanitizedLogVariable = filter_var($message, FILTER_SANITIZE_STRING);

    $cipher = "aes-128-gcm";
    $key = "keykeykeykeykeyk";
    $ivlen = openssl_cipher_iv_length($cipher);
    
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($sanitizedLogVariable, $cipher, $key, $options=0, $iv, $tag);
    //store $cipher, $iv, and $tag for decryption later
    $encrypted = $ciphertext. "@@@@@" . $iv . "@@@@@" . $tag . "#####";

    $myfile = fopen("errorLog.txt", "a");
    fwrite($myfile, $encrypted);
    fclose($myfile);
}
?>
