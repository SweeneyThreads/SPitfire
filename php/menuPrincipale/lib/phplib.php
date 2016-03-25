<?php
function connect() {
	$c=mysql_connect($_SESSION['host'],$_SESSION['user'],$_SESSION['psw'])
	or die($_SERVER['PHP_SELF'] . "Connessione fallita!");
	mysql_select_db($_SESSION['dbname']);
	return $c;
}



?>