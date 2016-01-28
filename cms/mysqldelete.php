<?php
mysql_connect("localhost","username","password") or die(mysql_error());

//echo $_POST['date'];
mysql_select_db("test_db");
$name = $_POST['name'];
$time2 = date("Y-m-d H:i:s", strtotime($_POST['date']));
$statement = $_POST['state'];
$search = mysql_query("SELECT * FROM test WHERE name_data = '$name' AND time_data = '$time2'") or die(mysql_error());
mysql_query("DELETE FROM test WHERE name_data = '$name' AND time_data = '$time2'");
while($row = mysql_fetch_array($search))
{
echo "entry " . $row['name_data'] . "	" . $row['time_data'] . "	has been deleted ";
echo "<br />";
}

mysql_close()

?>

<form action="mysqlentry.php" method="post">
Click here to go back: <input type="submit" />
</form> 
