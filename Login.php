<?php
date_default_timezone_set('GMT');
include("config.php");
error_reporting(0);
session_start(); // starts a session
$error = ''; // variable to hold error message 
include "phpFuncs.php";

if($_SERVER["REQUEST_METHOD"] == "POST") // If the method is post continue
    {
        $logUser = $_POST['username']; //getting user name from current session
        $logPassword = $_POST['password'];
        $logUserAgent = $_SERVER['HTTP_USER_AGENT'];
        $logIP = $_SERVER['REMOTE_ADDR'];
        $timeStamp = date("F j, Y, g:i a");

        $query1 = "SELECT COUNT(hashedUserAgentIP) AS Count FROM ip WHERE hashedUserAgentIP = '' AND `timestamp` > (now() - interval 5 minute) AND inActive = True";
        $query2 = "INSERT INTO `ip` (`hashedUserAgentIP` ,`timestamp`, `inActiveReg`) VALUES ('',CURRENT_TIMESTAMP,'False')";
        $query3 = "Select Username FROM tester";
        $query4 = "SELECT hashedPassword FROM tester WHERE Username = ''";

        $ip = $_SERVER['REMOTE_ADDR']; // gets the ip address of the user and assigns it to a variable
        $userAgent = $_SERVER['HTTP_USER_AGENT']; // gets the user agent details of the user and assigns it to a variable
        $foundUsername = "";
        $count = 0;

        $hashOfUser = $ip . $userAgent; // joins the ip and user agent and assigns it to a vatiable
        $iterations = 1000; // number of iterations to use in the hashing algorithm

        $salt = "salty"; // salt for the hashing of the ip+useragent. Using the same salt as it does not matter if it is bo=roken
        $hash = hash_pbkdf2("sha256", $hashOfUser, $salt, $iterations, 32); // hashes the ip+useragent and assigns it to a variable

        $result = mysqli_query($db,"SELECT COUNT(hashedUserAgentIP) AS Count FROM ip WHERE hashedUserAgentIP = '$hash' AND `timestamp` > (now() - interval 5 minute) AND inActive = True"); // query to check the database for matching useragent+ip and couunts them if they are still active
        if (!$result) {
            $label = "Query COUNT(hashedUserAgentIP) failed";
            $error = $sqlUsername->connect_error;
            $location = "ChangePassword.php";
            $test = SQLLog($label,$location,$error,$timeStamp);
        } 

        $row = mysqli_fetch_all($result,MYSQLI_ASSOC);

        if($row[0]['Count'] >= 10000) // if the value returned by the query is 3 or more.
            {
                echo "Your are allowed 5 attempts in 5 minutes";
            }
        else
            {
                $insertIp = mysqli_query($db, "INSERT INTO `ip` (`hashedUserAgentIP` ,`timestamp`, `inActiveReg`) VALUES ('$hash',CURRENT_TIMESTAMP, 'False')"); // query to insert the hashed useragent+ip into the database
                if (!$insertIp) {
                    $label = "Query insert ip failed";
                    $error = $sqlUsername->connect_error;
                    $location = "ChangePassword.php";
                    $test = SQLLog($label,$location,$error,$timeStamp);
                }
                
                $myusername = filter_var($_POST['username'],FILTER_SANITIZE_STRING); // takes the entered username and Strip tags thenand assigns it to a variable
                $mypassword = $_POST['password']; //filters out special characters in a string and assigns to a variable
            
                $sqlUsername = mysqli_query($db,"Select Username FROM tester");
                if (!$sqlUsername) {
                    $label = "Query select username failed";
                    $error = $sqlUsername->connect_error;
                    $location = "ChangePassword.php";
                    $test = SQLLog($label,$location,$error,$timeStamp);
                }
                        
                if($sqlUsername->num_rows>0)
                    {
                        while($row1 = $sqlUsername->fetch_assoc())
                            {
                                $returnUsername = $row1["Username"];
                                if (hash_equals($returnUsername, crypt($myusername, $returnUsername))) 
                                    {
                                        $foundUsername = $returnUsername;
                                    }
                            }
                    }      

                $salt = "SELECT hashedPassword FROM tester WHERE Username = '$foundUsername'"; // query to return the hashepassword and salt of the username entered
                $saltReturn = mysqli_query($db,$salt); // run the query
                if (!$saltReturn) {
                    $label = "Query select hashedPassword failed";
                    $error = $sqlUsername->connect_error;
                    $location = "ChangePassword.php";
                    $test = SQLLog($label,$location,$error,$timeStamp);
                }
                 
                $row = mysqli_fetch_all($saltReturn,MYSQLI_ASSOC); // mysqli_fetch_all returns a array and assigns it to the variable
                    
                $arr = (array)$row; // turns the variable into an array and assigns it to a variable
                if (empty($arr)) // checks if the array is empty
                    {
						$error = "Your Username($myusername) or Password is invalid";
                        $label = "Login Not Successful";
                        $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);	
                    }
                else // if the array is not empty
                    {
                        $returnedPassword = $row[0]['hashedPassword'];
                        if (hash_equals($returnedPassword, crypt($mypassword , $returnedPassword))) 
                            {
                                $count++;
                            }
                    
                        if($count == 1) 
                            {
                                $_SESSION['login_user'] = $logUser;
                                $label = "Login Successful";
                                $test = normalLog($label,$logUser,$logUserAgent,$timeStamp,$query1,$query2,$query3,$query4,$logIP);
                                
                                header("location: welcome.php"); // goes to the welcome page
                            }
                    }
            }
        }
?>
<html>

   <head>
      <title>Login Page</title>

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
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Login</b></div>

            <div style = "margin:30px">

               <form action = "" method = "post" autocomplete = "off">
                  <label>UserName  :</label><input type = "text" name = "username" class = "box"/><br /><br />
                  <label>Password  :</label><input type = "password" name = "password" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/>
                  <input type = "button" value = " Registration " onclick="window.location.href='Register.php'"/><br />
                  <input type = "button" value = " Forgot Password " onclick="window.location.href='ForgotPassword.php'"/><br />
               </form>

               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
            </div>
         </div>
      </div>
   </body>
</html>
