

<?php

include_once('publicuid.php');


	
$name = $_POST['name'];
$state = $_POST['state'];
$mysqldate = date( 'Y-m-d H:i:s');
echo uidgen($name, $state, $mysqldate);

?>