<?php
function connect() {
	$c=mysql_connect($_SESSION['host'],$_SESSION['user'],$_SESSION['psw']);
	mysql_select_db($_SESSION['dbname']);
	return $c;
}



?>