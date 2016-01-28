<?php
mysql_connect("localhost","username","password") or die(mysql_error());


mysql_select_db("test_db");
$uid = $_POST['uid'];
mysql_query("DELETE FROM test WHERE UID = '$uid'");
mysql_close()

?>

<form action="mysqlentry.php" method="post">
Click here to go back: <input type="submit" />
</form> 
