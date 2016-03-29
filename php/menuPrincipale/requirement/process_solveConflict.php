<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("lib/class_requisiti.php");
require_once("../lib/phplib.php");
$conn=connect();


$requisitiDB=RequirementsSystem::getInstanceFromDB($conn);
$arrRequisitiDB=$requisitiDB->getRequisiti();

$requisitiLatex=RequirementsSystem::getInstanceFromLatex($DEFAULT_REPO_LOCATION);
$arrRequisitiLatex=$requisitiLatex->getRequisiti();


if (isset($_POST['DB'])) { 
	$requisitiDaAggiungere=$_POST['reqsel'];

	foreach ($requisitiDaAggiungere as $idRequisito) {
		$requisitiDB->addRequirement($arrRequisitiLatex[$idRequisito],$conn);
	}

	RequirementsSystem::resetDependencies($conn);
	header("Location: conflictChecker.php");
}




if (isset($_POST['latex'])) {
	$requisitiDaGenerare=$_POST['reqsel'];
	$tex='';
	foreach ($requisitiDaGenerare as $idRequisito) {
		$tex.=$arrRequisitiDB[$idRequisito]->toLatex();
	}
echo <<< END
<!DOCTYPE html>
<html lang="it">
<head>
	<title>Latex autogenerato</title>
	<meta charset='utf-8'>
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Documentazione prodotta per il corso Ingegneria del Software a.a 2015-2016">
	<meta name="keywords" content="Ingegneria del Software, Analisi dei requisiti, Progettazione, SweeneyThreads">
	<meta name="author" content="Bonato Paolo, Bortolazzo Matteo, Biggeri Mattia, Maino Elia, Nicoletti Luca, Padovan Tommaso, Tommasin Davide">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link href="http://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
	<link href="../../stile.css" rel="stylesheet" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">
<div class="container" style="padding-top: 60px;">
  <a href="conflictChecker.php">Torna ai Conflitti</a><br />
  <h2>Latex Da Aggiungere</h2>


<body id="myPage" data-spy="scroll" data-offset="60"><div id="descrizioneTool" class="container-fluid">
<pre>
$tex
</pre>
</div></body>


END;
}


?>