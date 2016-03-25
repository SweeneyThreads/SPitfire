<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");

require_once("../lib/htmlgenerator.php");
require_once("impostazioni.php");
require_once("../lib/phplib.php");
$conn=connect();



if (isset($_POST['dataInizio'])) {
	foreach ($nomi as $componenteGruppo) {
		$query="insert into Sostituzioni values('".$_POST['dataInizio']."','$componenteGruppo','".$_POST[$componenteGruppo]."')";
		$result=mysql_query($query,$conn);
	}
}

header("Location: gest_ruoli.php");

?>