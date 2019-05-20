<?php
include "phpFuncs.php";
date_default_timezone_set('GMT');
include("config.php");
include("session.php");
error_reporting(0);
$logUser = $_SESSION['login_user'];
$logUserAgent = $_SERVER['HTTP_USER_AGENT'];
$timeStamp = date("F j, Y, g:i a");
$logIP = $_SERVER['REMOTE_ADDR'];
$query1 = "Select Username FROM tester";
$query2 = "SELECT hashedPassword FROM tester WHERE Username = ''";
$query3 = "UPDATE tester SET hashedPassword = '' WHERE Username = '";
$query4 = " ";

if($_SERVER["REQUEST_METHOD"] == "POST") // If the method is post continue
    {
        if (isset($_POST['oldPassword']) && isset($_POST['newPassword'])) // if the 2 variables are set continue
            {
                $password1 = $_POST['oldPassword']; // setting the variable to the old password entered
                $password2 = $_POST['newPassword']; // setting the variable to the new password entered
                $password3 = $_POST['confirmPassword']; // setting the variable to the new password entered
                $username = $login_session; // setting the variable to the username of the currently logged in user

                $sqlUsername = mysqli_query($db,"Select Username FROM tester");
                if (!$sqlUsername) {
                    $label = "Query select Username failed";
                    $error = $sqlUsername->connect_error;
                    $location = "ChangePassword.php";
                    $test = SQLLog($label,$location,$error,$timeStamp);
                } 
                        
                if($sqlUsername->num_rows>0)
                    {
                        while($row1 = $sqlUsername->fetch_assoc())
                            {
                                $returnUsername = $row1["Username"];
                                if (hash_equals($returnUsername, crypt($username, $returnUsername))) 
                                    {
                                        $foundUsername = $returnUsername;
                                    }
                            }
                    }

                $passwordReturn = mysqli_query($db,"SELECT hashedPassword FROM tester WHERE Username = '$foundUsername'"); // query to return the hashepassword and salt of the logged in user
                $row = mysqli_fetch_all($passwordReturn,MYSQLI_ASSOC); // mysqli_fetch_all returns a array and assigns it to the variable
                $returned =  $row[0]['hashedPassword']; // position 0 holds the hashed password and assigns it to the variable
                if (!$passwordReturn) {
                    $label = "Query select haashedPassword failed";
                    $error = $sqlUsername->connect_error;
                    $location = "ChangePassword.php";
                    $test = SQLLog($label,$location,$error,$timeStamp);
                } 

                if (!hash_equals($returned, crypt($password1, $returned))) // if the hashedpassword stored in login1 does not matche the hashedpassword stored in salthash
                    {
                        $label = "Old Password Incorrect";
                        $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP); 
                    }
                elseif((!preg_match("#.*^(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password2))) // if old password is correct checks if entered new password is >= 8 and <= 20 chars, contains a-z A-Z 0-9 and special chars(£,$ and % etc)
                    {
                            echo "Password not complex enough, >= 8 and <= 20 chars, contains a-z A-Z 0-9 and special chars(£,$ and % etc)";
                            echo " eg Password14$";
                    }
                elseif($password2 === $password3)
                    {                    
                        $cryptPassword = crypt($password2);

                        $result = mysqli_query($db,"UPDATE tester SET hashedPassword = '$cryptPassword' WHERE Username = '$returnUsername'");
                        if (!$result) {
                            $label = "update tester failed";
                            $error = $sqlUsername->connect_error;
                            $location = "ChangePassword.php";
                            $test = SQLLog($label,$location,$error,$timeStamp);
                        } 
                        $label = "Changed Password Successful";
                        $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
				
                        header("location:Logout.php"); // query to update the users stored password
                    }
                else
                    {
                        $label = "Passwords not matching";
                        $query3 = " ";
                        $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
                        echo "Passwords do not match"; 
                    }
            }
   }
?>
<html>

   <head>
      <title>Change Password</title>

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
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Change Password</b></div>

            <div style = "margin:30px">

               <form action = "" method = "post" autocomplete = "off">
                  <label>Old Password  :</label><input type = "password" name = "oldPassword" class = "box" /><br/><br />
                  <label>New Password  :</label><input type = "password" name = "newPassword" class = "box" /><br/><br />
                  <label>Re-enter New Password  :</label><input type = "password" name = "confirmPassword" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>

            </div>

         </div>

      </div>

   </body>
</html>
