<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();


if (isset($_POST['ID']) and isset($_POST['Nome'])) {
	$id = $_POST['ID'];
	$nome = $_POST['Nome'];
	$descr = $_POST['Descrizione'];
	$fonte = $_POST['Fonte'];
	$query="INSERT INTO Requisiti(ID,Nome,Descrizione,Fonte) VALUES('$id','$nome','$descr','$fonte')";
	$result=mysql_query($query,$conn)
		or die("Inserimento nella tabella requisiti fallito" . mysql_error($conn));
}

header("Location: requisiti.php");



?>