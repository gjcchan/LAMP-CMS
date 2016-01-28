<html>
<body>


<div class ="leftmenu">
<?php include 'sidebar.php'; ?>
</div>

<form action="test2.php" method="post">
Insert First number here: <input type="number" name="data1" />
Insert Second number here: <input type="number" name="data2" />
Click to calculate: <input type="submit" />
</form> 



<form action="upload.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" />
<br />
<input type="submit" name="submit" value="Submit" />
</form>

<?php
echo "Hello World";
$test=25;

function myTest ($test, $test2)
{
$result = $test + $test2;
echo $result;

if ($result ==25){
echo "result is 25";
}
}

?>

</body>
</html>