<?php

session_start();
//if (!isset($_SESSION['userSession']['userID']) || !isset($_SESSION['userSession']['sessionExpiration']) || $_SESSION['userSession']['sessionExpiration'] < time() || !isset($_SESSION['userSession']['sessionLoggedIn']) || !($_SESSION['userSession']['sessionLoggedIn']))
if (!isset($_SESSION['username']['password']))
{
	header("location: login.php");
//your kickout code goes here
}

session_destroy();
header("location: login.php");
?>