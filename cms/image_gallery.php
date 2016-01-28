<?php
require_once('headerauth.php');
?>
<html>
	<body>
		<link rel="stylesheet" type="text/css" href="/css/image_gallery.css" />
		<table BORDER="0" CELLPADDING="3" CELLSPACING="3" ALIGN="left">
		<?php
		
		mysql_connect("localhost","username","password") or die(mysql_error());
mysql_select_db(test_db);
$data1 = mysql_query("SELECT * FROM image_db") or die(mysql_error());
echo '<tr>';
echo '<td>' . "FileName" . '</td><td>' . "Image" . '</td><td>' . "Date of upload" . '</td><td>'  . "PRIMARY_KEY" . '</td>';
echo '</tr>';
while($row = mysql_fetch_array($data1))
{
echo '<tr>';
echo '<td>' ."<p align='center'>". $row[img] ."</p>". '</td><td>' . "<img src='upload/" . $row[img] . "'>" . '</td><td>' ."<p align='center'>" . $row['ts'] ."</p>" . '</td><td>'. "<p align='center'>" . $row['imgid'] . "</p>" . '</td>';
echo '</tr>';
}		
		?>
	</table>
	</body>
</html>