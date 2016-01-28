<?php
mysql_connect("localhost","username","password") or die(mysql_error());

echo $_POST['date'];
mysql_select_db("test_db");
$name = $_POST['name'];
$time2 = date("Y-m-d H:i:s", strtotime($_POST['date']));
$statement = $_POST['state'];
mysql_query("UPDATE test SET stat_data = '$statement' WHERE name_data = '$name' AND time_data = '$time2'");
$search = mysql_query("SELECT * FROM test WHERE name_data = '$name' AND time_data = '$time2'") or die(mysql_error());
while($row = mysql_fetch_array($search))
{
echo "entry " . $row['name_data'] . "	" . $row['time_data'] . "	has been modified to:   " . $row['stat_data'];
echo "<br />";
}

mysql_close()

?>

<form action="mysqlentry.php" method="post">
Click here to go back: <input type="submit" />
</form> 
