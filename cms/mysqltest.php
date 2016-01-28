<?php
$con = mysql_connect("localhost","username","password");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
if ($con)
{
echo "success!";
}


mysql_close($con);
// some code
?> 