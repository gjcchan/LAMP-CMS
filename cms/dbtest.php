<?php
require_once('headerauth.php');
include_once('publicuid.php');
$con = mysql_connect("localhost","username","password");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
if ($con)
{
echo "success!";
}
$mysqldate = date( 'Y-m-d H:i:s');
mysql_select_db("test_db", $con);
$value1 = $_POST['name'];
$value2 = $_POST['state'];
// use mysql_real_escape_string(); if you want to prevent SQL injection
$uid = uidgen($value1, $value2, $mysqldate);
$insertsql = "INSERT INTO test (name_data, time_data, stat_data, UID) VALUES ('$value1', '$mysqldate', '$value2', '$uid')";

mysql_query($insertsql) or die(mysql_error());
echo "no errors";

/*
if (!mysql_query($insertsql))
  {
  die('Error: ' . mysql_error());
  }
echo "1 record added";
*/

?> 

<script type="text/javascript">
window.location = "http://usernamec.us.to/mysqlentry.php"
</script>