<?php
    include("session.php");
    $k = file_get_contents('k.txt');
    $array = explode("#####", file_get_contents('errorLog.txt'));
    $cipher = "aes-128-gcm";
    $key = $k;
    for($i = 0;$i < count($array)-1;$i++)
    {
        $parts = explode('@@@@@', $array[$i]);
        $decrypted = openssl_decrypt($parts[0], $cipher, $key, $options=0, $parts[1], $parts[2]);
        $parts1 = explode('?????', $decrypted );
        for($j = 0;$j < count($parts1)-1;$j++)
        {
            echo $parts1[$j];
            echo "<br>";
        }
        echo "<br>";
    }
?>

<html>
   
   <head>
      <title>Log </title>
   </head>
   
   <body>
      <h2><a href = "welcome.php">Back</a></h2>
   </body>
   
</html>