<?php
require_once('headerauth.php');
?>
<?php include 'Header.php'; ?>


<html>
	<body bgcolor="gray">
<link rel="stylesheet" type="text/css" href="/css/index.css" />
<div id='body'>
<?php
mysql_connect("localhost","username","password") or die(mysql_error());
mysql_select_db(test_db);
$data1 = mysql_query("SELECT * FROM test") or die(mysql_error());
while($row = mysql_fetch_array($data1))
{
echo "<div id='poster_name'>";
echo "<div id='name'>";
echo "<div class='alignLeft'>Posted by: &nbsp" . $row['name_data'] . "</div>";
//echo "<div id='date'>";
echo "<div class='alignRight'>" . $row['time_data'] . "</div>";
//echo "</div>";
echo "</div>";

echo "</div>";

echo "<div id='statement'>";
echo  $row['stat_data'];
echo "</div>";
}

mysql_close()


?>
</div>

<?php include 'footer.php'; ?>
	</body>
</html>