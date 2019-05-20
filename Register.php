<?php
include("config.php");
include "phpFuncs.php";
error_reporting(0);
include("session.php");

$logUserAgent = $_SERVER['HTTP_USER_AGENT'];
$timeStamp = date("F j, Y, g:i a");
$logIP = $_SERVER['REMOTE_ADDR'];
date_default_timezone_set('GMT');

if($_SERVER["REQUEST_METHOD"] == "POST") // If the method is post continue
    {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['dob']))
        {
            $username = $_POST['username']; // setting the variable to the entered username
            $password = $_POST['password']; // setting the variable to the entered password
            $email = $_POST['email'];
            $dob = $_POST['dob']; 
            $countEmail = 0;
            $countUsername = 0;
            $query1 = "select COUNT(hashedUserAgentIP) AS Count FROM ip WHERE hashedUserAgentIP = '' AND `timestamp` > (now() - interval 5 minute) AND inActiveReg = True";
            $query2 = "INSERT INTO `ip` (`hashedUserAgentIP` ,`timestamp`, `inActive`) VALUES ('',CURRENT_TIMESTAMP, 'True')";
            $query3 = "INSERT INTO tester (Username,hashedPassword,Email,DOB) VALUES ('','','','')";
            $query4 = " ";

            $ip = $_SERVER['REMOTE_ADDR']; // gets the ip address of the user and assigns it to a variable
            $userAgent = $_SERVER['HTTP_USER_AGENT']; // gets the user agent details of the user and assigns it to a variable

            $hashOfUser = $ip . $userAgent; // joins the ip and user agent and assigns it to a vatiable
            $iterations = 1000; // number of iterations to use in the hashing algorithm

            $salt = "salty"; // salt for the hashing of the ip+useragent. Using the same salt as it does not matter if it is bo=roken
            $hash = hash_pbkdf2("sha256", $hashOfUser, $salt, $iterations, 32); // hashes the ip+useragent and assigns it to a variable

            $result = mysqli_query($db,"SELECT COUNT(hashedUserAgentIP) AS Count FROM ip WHERE hashedUserAgentIP = '$hash' AND `timestamp` > (now() - interval 5 minute) AND inActiveReg = True"); // query to check the database for matching useragent+ip and couunts them if they are still active
            if (!$result) {
                $label = "Query COUNT(hashedUserAgentIP) failed";
                $error = $sqlUsername->connect_error;
                $location = "ChangePassword.php";
                $test = SQLLog($label,$location,$error,$timeStamp);
            } 
            
            $row = mysqli_fetch_all($result,MYSQLI_ASSOC);

            if($row[0]['Count'] >= 3) // if the value returned by the query is 3 or more.
                {
                    $label = "Too many accounts created";
                    $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
									
                    echo "Your are allowed to create 3 Users in 5 minutes";
                }
            else
                {
                    $insertIp = mysqli_query($db, "INSERT INTO `ip` (`hashedUserAgentIP` ,`timestamp`, `inActive`) VALUES ('$hash',CURRENT_TIMESTAMP, 'True')"); // query to insert the hashed useragent+ip into the database
                    if (!$insertIp) {
                        $label = "Query insert into ip failed";
                        $error = $sqlUsername->connect_error;
                        $location = "ChangePassword.php";
                        $test = SQLLog($label,$location,$error,$timeStamp);
                    } 
                    
                    if (isset($_POST['username']) && isset($_POST['password'])) // if the 2 variables are set continue
                    {   
                        $sqlEmail = mysqli_query($db,"Select Email FROM tester");
                        if (!$sqlEmail) {
                            $label = "Query select email failed";
                            $error = $sqlUsername->connect_error;
                            $location = "ChangePassword.php";
                            $test = SQLLog($label,$location,$error,$timeStamp);
                        } 
                        
                        if($sqlEmail->num_rows>0)
                        {
                            while($row1 = $sqlEmail->fetch_assoc())
                            {
                                $returnEmail = $row1["Email"];
                                if (hash_equals($returnEmail, crypt($email, $returnEmail))) 
                                    {
                                        $countEmail++;
                                    }
                            }
                        }
                        
                        $sqlUsername = mysqli_query($db,"Select Username FROM tester");
                        if (!$sqlUsername) {
                            $label = "Query select username failed";
                            $error = $sqlUsername->connect_error;
                            $location = "ChangePassword.php";
                            $test = SQLLog($label,$location,$error,$timeStamp);
                        } 
                        
                        if($sqlUsername->num_rows>0)
                        {
                            while($row2 = $sqlUsername->fetch_assoc())
                            {
                                $returnUsername = $row2["Username"];
                                if(hash_equals($returnUsername, crypt($username, $returnUsername))) 
                                    {
                                        $countUsername++;
                                    }
                            }
                        }
                        
                        if ($countUsername > 0) // query to check if the username already exists
                        {
                            $query3 = " ";
                            $label = "Username already exists";
                            $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
					
                            echo "Username already exists";
                        }
                        elseif($countEmail > 0)
                        {
                            $query3 = " ";
                            $label = "Email already exists";
                            $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
							
                            echo "Email already exists";
                        }
                        elseif((!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password))) // query to check if the password is strong enough. >= 8 and <= 20 chars, contains a-z A-Z 0-9 and special chars(£,$ and % etc)
                        {
                            $query3 = " ";
                            $label = "Password Not Complex";
                            $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
							
                            echo "Password not complex enough, >= 8 and <= 20 chars, contains a-z A-Z 0-9 and special chars(£,$ and % etc)";
                            echo "eg Password14$";
                        }
                        else
                        {
                            $cryptPassword = crypt($password);
                            $cryptEmail = crypt($email);
                            $cryptDOB = crypt($dob);
                            $cryptUsername = crypt($username);
            
                            $result = mysqli_query($db,"INSERT INTO tester (Username,hashedPassword,Email,DOB) VALUES ('$cryptUsername', '$cryptPassword','$cryptEmail','$cryptDOB')");
                            if (!$result) {
                                $label = "Query insert into tester failed";
                                $error = $sqlUsername->connect_error;
                                $location = "ChangePassword.php";
                                $test = SQLLog($label,$location,$error,$timeStamp);
                            } 
                            $label = "Registered Successfully";
                            $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
							
							header("location:Login.php"); // query to insert the newly created user into the database
                        }
                    }
                }
        }  
    } 
?>
<html>

   <head>
      <title>Register User</title>

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
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Register</b></div>

            <div style = "margin:30px">

               <form action = "" method = "post" autocomplete = "off">
                  <label>UserName  :</label><input type = "text" name = "username" class = "box"/><br /><br />
                  <label>Password  :</label><input type = "password" name = "password" class = "box" /><br/><br />
                  <label>Email  :</label><input type = "email" name = "email" class = "box"/><br /><br />
                  <label>Date Of Birth  :</label><input type="date" name = "dob" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>

            </div>

         </div>

      </div>

   </body>
</html>
