<?php

function uidgen($name, $state, $date){
$teststring = bin2hex(hexdec(bin2hex($mysqldate)) + hexdec(bin2hex($name)) + hexdec(bin2hex($state)));
return $teststring;
}

?>