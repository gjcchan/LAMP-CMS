<?php

session_start();
if (!isset($_SESSION['password']) && $_SESSION['sessionExpires'] < time())
{
	header("location: login.php");
	exit;
//your kickout code goes here
}
else{
$_SESSION['sessionExpires'] = strtotime(strtotime("+30 minutes"));
//echo "you logged in!";
}



//if (!isset($_SESSION['userSession']['userID']) || !isset($_SESSION['userSession']['sessionExpiration']) || $_SESSION['userSession']['sessionExpiration'] < time() || !isset($_SESSION['userSession']['sessionLoggedIn']) || !($_SESSION['userSession']['sessionLoggedIn']))
//if (!isset($_SESSION['username']['password']))
//if (!isset($_SESSION['username']['password']) && $_SESSION['sessionExpires'] < time())
?>
