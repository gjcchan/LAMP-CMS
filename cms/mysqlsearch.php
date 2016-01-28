<?php

mysql_connect("localhost","username","password") or die(mysql_error());
mysql_select_db(test_db);


$name = mysql_real_escape_string($_POST['name']);
$search = mysql_query("SELECT * FROM test WHERE name_data = '$name'");
while($row = mysql_fetch_array($search))
{
echo $row['name_data'] . "	" . $row['time_data'] . "	" . $row['stat_data'];
echo "<br />";
}

mysql_close()

?>
