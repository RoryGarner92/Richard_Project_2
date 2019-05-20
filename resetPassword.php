<?php
error_reporting(0);
    include("config.php");
    include("session.php");
    date_default_timezone_set('GMT');
	$countEmail = 0;                    
    $tokenValue = $_SESSION['token'];
    error_reporting(0);
	include "phpFuncs.php";
	
	$query1 = "Select Email FROM tester";
	$query2 = "SELECT resetToken,DOB FROM tester WHERE Email = '' AND (now() - interval 1 minute)  < `timestamp`";
	$query3 = "UPDATE tester SET hashedPassword = '' WHERE Email = ''";
	$query4 = "UPDATE tester SET resetToken = '' AND timestamp = '' WHERE Email = ''";

    if($_SERVER["REQUEST_METHOD"] == "POST") 
        {			
			$logEmail = $_POST['emailAddress'];
			$logUserAgent = $_SERVER['HTTP_USER_AGENT'];
			$timeStamp = date("F j, Y, g:i a");
			$logIP = $_SERVER['REMOTE_ADDR'];
			$returned = "";
			$returned2 = "";
			
            $newPassword = $_POST['newPassword'];
			$newPassword1 = $_POST['newPassword1'];
            $enteredToken = $_POST['token'];
            $dob = $_POST['dob'];
     
			$sqlEmail = mysqli_query($db,"Select Email FROM tester");
            if (!$sqlEmail) {
                $label = "Query email from tester failed";
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
                            $tokenResult = mysqli_query($db,"SELECT resetToken,DOB FROM tester WHERE Email = '$userEmail' AND (now() - interval 5 minute)  < `timestamp`");
                            if (!$tokenResult) {
                                $label = "Query select resetToken failed";
                                $error = $sqlUsername->connect_error;
                                $location = "ChangePassword.php";
                                $test = SQLLog($label,$location,$error,$timeStamp);
                            } 
							
							$row = mysqli_fetch_all($tokenResult,MYSQLI_ASSOC);
                            if($row == null)
                                {
									echo "error";
									
                                }
                            else
                                {
                                    $returned = $row[0]['resetToken'];  
                                    $returned2 = $row[0]['DOB'];  
                                }
                        }
                }
			}

			if($newPassword != $newPassword1){
				$query1 = " ";
				$query2 = " ";
				$query3 = " ";
				$query4 = " ";
				$label = "Passwords not matching";
				$test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);

                echo "Passwords entered do not match";
				echo "<br>";
				echo "<br>";
			}
            else if($countEmail > 0 && $returned == $enteredToken  && $returned2 === crypt($dob,$returned2))
            {
                $reset = '';
                $cryptPassword = crypt($newPassword);
                $result = mysqli_query($db,"UPDATE tester SET hashedPassword = '$cryptPassword' WHERE Email = '$userEmail'");
                if (!$result) {
                    $label = "Query update tester failed";
                    $error = $sqlUsername->connect_error;
                    $location = "ChangePassword.php";
                    $test = SQLLog($label,$location,$error,$timeStamp);
                } 
                $result = mysqli_query($db,"UPDATE tester SET resetToken = '$reset' AND timestamp = '$reset' WHERE Email = '$userEmail'");
                if (!$result) {
                    $label = "Query update tester failed";
                    $error = $sqlUsername->connect_error;
                    $location = "ChangePassword.php";
                    $test = SQLLog($label,$location,$error,$timeStamp);
                } 
				$label = "Token Valid Successful Password Change";
				$test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
		
                header("location: Login.php");
            }
            else if($countEmail < 1)
            {
				$query2 = " ";
				$query3 = " ";
				$query4 = " ";
				$label = "Invalid Email";
				$test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);

                echo "Invalid Input1";
				echo "<br>";
				echo "<br>";
            }
			else if($countEmail > 0 && $returned != $enteredToken  && $returned2 === crypt($dob,$returned2))
            {
				$query3 = " ";
				$query4 = " ";
				$label = "Invalid Token";
				$test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
				
                echo "Invalid Input2";
				echo "<br>";
				echo "<br>";
			}
			else if($countEmail > 0 && $returned == $enteredToken  && $returned2 !== crypt($dob,$returned2))
            {
				$query3 = " ";
				$query4 = " ";
				$label = "Invalid DOB";
				$test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
				
                echo "Invalid Input3";
				echo "<br>";
				echo "<br>";
			}
        }
        echo $tokenValue;
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
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Reset Password</b></div>

            <div style = "margin:30px">

               <form action = "" method = "post" autocomplete = "off">
                  <label>Enter Email:</label><input type = "Email" name = "emailAddress" class = "box" /><br/><br />
                  <label>Enter New Password:</label><input type = "Password" name = "newPassword" class = "box" /><br/><br />
                  <label>Re-enter New Password:</label><input type = "Password" name = "newPassword1" class = "box" /><br/><br />
                  <label>Enter DOB:</label><input type = "date" name = "dob" class = "box" /><br/><br />
                  <label>Enter Password Token:</label><input type = "Text" name = "token" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>

            </div>

         </div>

      </div>

   </body>
</html>


