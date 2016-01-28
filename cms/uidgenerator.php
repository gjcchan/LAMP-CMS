<?php


$name = $_POST['name'];
$statement = $_POST['state'];
$mysqldate = date( 'Y-m-d H:i:s');
$teststring = bin2hex(hexdec(bin2hex($mysqldate)) + hexdec(bin2hex($name)) + hexdec(bin2hex($state)));

echo $teststring;



?>