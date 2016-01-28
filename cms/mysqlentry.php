<?php
require_once('headerauth.php');
?>
<link rel="stylesheet" type="text/css" href="/css/mysqlentry.css" />
<div class = "left-menu" style="left: 123px; top: 355px">
<?php include 'sidebar.php'; ?>
</div>

<div id="entry_box_heading" style="font-weight: bold; font-size: 22; font-family: Arial Black ">
Entry
</div>

<div id = "Entry_box">
<form action="dbtest.php" method="post">
name: <input type="text" name="name" />
<br />
<textarea cols="50" rows="4" name="state"></textarea>
<br />
Insert data into table: <input type="submit"; value = "submit" />
</form> 
</div>

<?php
echo '<br />';
?>

<div id="Main_table" >
<table>
<?php
mysql_connect("localhost","username","password") or die(mysql_error());
mysql_select_db(test_db);
$data1 = mysql_query("SELECT * FROM test") or die(mysql_error());
echo '<tr>';
echo '<td>' . "Name" . '</td><td>' . "Statement" . '</td><td>' . "Date of creation" . '</td><td>'  . "UID" . '</td>';
echo '</tr>';
while($row = mysql_fetch_array($data1))
{
echo '<tr>';
echo '<td>' . $row['name_data'] . '</td><td>' . $row['stat_data'] . '</td><td>' . $row['time_data'] . '</td><td>' . $row['UID'] . '</td>';
echo '</tr>';
}

mysql_close()

?>
</table>
</div>

<form action="mysqlsearch.php" method="post">
Search name: <input type="text" name="name" />
User Lookup: <input type="submit" />
</form> 

<form action="mysqledit.php" method="post">
Type in name you would like to modify : <input type="text" name="name" />
Type in date you would like to modify: <input type="text" name="date" />
Type in statement you would like to modify: <input type="text" name="state" />
User Lookup: <input type="submit" />
</form> 


<form action="mysqldelete.php" method="post">
Type in name you would like to delete : <input type="text" name="name" />
Type in date you would like to delete: <input type="text" name="date" />
User Lookup: <input type="submit" />
</form> 


<form action="mysqluiddelete.php" method="post">
Type in the UID you would like to delete : <input type="text" name="uid" />
User Lookup: <input type="submit" />
</form> 

<?php
	
echo '<br />';
echo '<br />';
echo "UID generator";	
?>
	
<form action="publicfunc.php" method="post">
name: <input type="text" name="name" />
statement: <input type="text" name="state" />
Insert data into table: <input type="submit" />
</form> 


<form action="upload_file.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" />
<br />
<input type="submit" name="submit" value="Submit" />
</form>