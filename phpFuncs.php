<?php


function normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP) 
{
    $k = file_get_contents('k.txt');
    $sesID = session_id();

    $message = "Action: ".$label."?????".
    "Session ID: ".$sesID."?????".
    "Username: ".$logUser."?????".
    "UserAgent: ".$logUserAgent."?????".
    "Timestamp: ".$timeStamp."?????".
    "Query Used 1: ".$query1."?????".
    "Query Used 2: ".$query2 ."?????".
    "Query Used 3: ".$query3."\r\n"."?????".
    "Query Used 4: ".$query4."\r\n"."?????".
    "IP Address: ".$logIP."?????";
    $sanitizedLogVariable = filter_var($message, FILTER_SANITIZE_STRING);

    $cipher = "aes-128-gcm";
    $key = $k;
    $ivlen = openssl_cipher_iv_length($cipher);
    
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($sanitizedLogVariable, $cipher, $key, $options=0, $iv, $tag);
    //store $cipher, $iv, and $tag for decryption later
    $encrypted = $ciphertext. "@@@@@" . $iv . "@@@@@" . $tag . "#####";
    
    $myfile = fopen("errorLog.txt", "a") or die("Unable to open file!");
    fwrite($myfile, $encrypted);
    fclose($myfile);
    return $randomString;
}

function SQLLog($label,$location,$error,$timeStamp) 
{
    $sesID = session_id();
    $k = file_get_contents('k.txt');
    $message = "Action: ".$label."?????".
    "Session ID: ".$sesID."?????".
    "Location: ".$location."?????".
    "Error: ".$error."?????".
    "Timestamp: ".$timeStamp."?????";

    $sanitizedLogVariable = filter_var($message, FILTER_SANITIZE_STRING);

    $cipher = "aes-128-gcm";
    $key = $k;
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