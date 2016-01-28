<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$username = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);
mysql_connect("localhost","username","password") or die(mysql_error());
mysql_select_db(test_db);
//$acct_list = mysql_query("SELECT * FROM user_acct") or die(mysql_error());
$usermatch = mysql_query("SELECT * FROM user_acct WHERE username = '$username' AND password = '$password'") or die(mysql_error());;
$usermatch = mysql_num_rows($usermatch);

if($usermatch > 0) {
session_start();
$_SESSION['username'] = $username;
$_SESSION['password'] = "1";
$_SESSION['sessionExpires'] = strtotime(strtotime("+30 minutes"));
header("Location:index.php");
}
else{
	header("location: login.php");
	mysql_close();
	exit;

}
}

else{ header("Location:login.php");}

?>