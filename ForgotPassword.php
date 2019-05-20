<?php
    date_default_timezone_set('GMT');
    include("config.php");
    error_reporting(0);
    include "phpFuncs.php";
    session_start();
  
    $countEmail = 0;
    
    $query1 = "Select Email FROM tester";
    $query2 = " ";
    $query3 = " ";
    $query4 = " ";

    function generateRandomString() 
    {
        $randomString = random_bytes(50);
        $randomString = bin2hex($randomString);
        return $randomString;
    }
                
    if (isset($_POST['emailAddress'])) // if the variable are set continue
        {   
			$logEmail = $_POST['emailAddress'];
			$logUserAgent = $_SERVER['HTTP_USER_AGENT'];
			$timeStamp = date("F j, Y, g:i a");
			$logIP = $_SERVER['REMOTE_ADDR'];
            
            $sqlEmail = mysqli_query($db,"Select Email FROM tester");
            if (!$sqlEmail) {
                $label = "Query select Email failed";
                $error = $sqlUsername->connect_error;
                $location = "ChangePassword.php";
                $test = SQLLog($label,$location,$error,$timeStamp);
            } 

            if($sqlEmail->num_rows>0)
                {
                    while($row1 = $sqlEmail->fetch_assoc())
                        {
                            $returnEmail = $row1["Email"];
                            if (hash_equals($returnEmail, crypt($logEmail, $returnEmail))) 
                                {
                                    $countEmail++;
                                    $userEmail = $returnEmail;

                                    $_SESSION['token'] = generateRandomString();
                                    $resetToken = $_SESSION['token'];
                                    $result = mysqli_query($db,"UPDATE tester SET resetToken = '$resetToken',timestamp = CURRENT_TIMESTAMP WHERE Email = '$userEmail'");
                                    if (!$result) {
                                        $label = "Query update tester failed";
                                        $error = $sqlUsername->connect_error;
                                        $location = "ChangePassword.php";
                                        $test = SQLLog($label,$location,$error,$timeStamp);
                                    } 

                                    $label = "Email Valid";
                                    $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
                                    
									header("location:resetPassword.php");
                                }
								else{
                                    $label = "Email Not Valid";
                                    $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
								}
                        }                                                                                          
                }
        } 
?>
<html>

   <head>
      <title>Reset Password</title>

      <style type = "text/css">
         body {
            font-family:Arial, Helvetica, sans-serif;
            font-size:14px;
         }

         label {
            font-weight:bold;
            width:100px;
            font-size:14px;
         }

         .box {
            border:#666666 solid 1px;
         }
      </style>

   </head>

   <body bgcolor = "#FFFFFF">

      <div align = "center">
         <div style = "width:300px; border: solid 1px #333333; " align = "left">
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Forgotten Password</b></div>

            <div style = "margin:30px">

               <form action = "" method = "post" autocomplete = "off">
                  <label>Enter Email:</label><input type = "Email" name = "emailAddress" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>

            </div>

         </div>

      </div>

   </body>
</html>
