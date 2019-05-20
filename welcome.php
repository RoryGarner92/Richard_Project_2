<?php
    include("session.php");
?>
<html>

   <head>
      <title>Welcome </title>
   </head>
   
   <body>
      <h1>Welcome <?php echo $login_session; ?></h1>
      <h2><a href = "ChangePassword.php">Password Change</a></h2>
      <h2><a href = "Log.php">Log File</a></h2>
      <h2><a href = "Logout.php">Sign Out</a></h2>
   </body>
   
</html>
