<?php
$user = 'root';
$pass = '';

$conn = new mysqli('localhost', $user, $pass) or die("Unable to connect");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
// Create database
$sql = "CREATE DATABASE Project2";
$createTesterTable = "CREATE TABLE `tester` (
  `id` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `hashedPassword` varchar(500) CHARACTER SET utf8mb4 NOT NULL,
  `Email` varchar(255) NOT NULL,
  `DOB` varchar(255) NOT NULL,
  `resetToken` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	 
$createIpTable = "CREATE TABLE `ip` (
  `id` int(11) NOT NULL,
  `hashedUserAgentIP` varchar(200) CHARACTER SET utf8 NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `inActive` tinyint(1) NOT NULL DEFAULT '1',
  `inActiveReg` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1";

$InsertIntoIP = "
INSERT INTO `ip` (`id`, `hashedUserAgentIP`, `timestamp`, `inActive`, `inActiveReg`) VALUES
(1, '9c7355ae2a6f6d9d07d4e87fea45d55a', '2018-01-25 17:26:08', 0, 0),
(2, '9c7355ae2a6f6d9d07d4e87fea45d55a', '2018-01-25 17:26:11', 0, 0),
(3, '9c7355ae2a6f6d9d07d4e87fea45d55a', '2018-01-25 17:29:43', 0, 0),
(4, '9c7355ae2a6f6d9d07d4e87fea45d55a', '2018-01-25 17:49:46', 1, 0);";

$alterIP = "ALTER TABLE `ip`
  ADD PRIMARY KEY (`id`);";
  
$alterTester = "ALTER TABLE `tester`
  ADD PRIMARY KEY (`id`);";
  
$ModifyIP = "ALTER TABLE `ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;";

$ModifyTester = "ALTER TABLE `tester`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;";

if ($conn->query($sql) === TRUE) {
    $database = "Project2";
    $db = new mysqli('localhost',$user,$pass,$database) or die('Cannot Connect');
    $ut = mysqli_query($db,$createTesterTable);
    $ip = mysqli_query($db,$createIpTable);
	$insertIP = mysqli_query($db,$InsertIntoIP);
	$aTester = mysqli_query($db,$alterTester);
	$aIP = mysqli_query($db,$alterIP);
	$mTester = mysqli_query($db,$ModifyTester);
	$mIP = mysqli_query($db,$ModifyIP);
    echo "Database and tables created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}
}
// extra ask not in spec  php function to create tables and db if not there onClick
$conn->close();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="" method="post">
<button type="submit" value="Create">Create</button>
<input type= "button"  value= "Login" onclick="window.location.href='Login.php'"/>

</form>
</body>
</html>
