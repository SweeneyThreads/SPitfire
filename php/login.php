<?php
session_start();

if (isset($_POST['usr'])) {
	if ($_POST['usr']=="sweeneythreads" and md5($_POST['psw'])=="2267068a9eb99f57752dbc5409e433f6") {
		$_SESSION['auth']=1;
		$_SESSION['host']='localhost';
		$_SESSION['user']='sweeneytreadaas';
		$_SESSION['psw']='';
		$_SESSION['dbname']='my_sweeneytreadaas';
		if (isset($_GET['thenbackto'])) header("Location: ".$_GET['thenbackto']); 
		else header("Location: menuPrincipale");
	}
}


echo <<<END
<!DOCTYPE html>
<html lang="it">
<head>
	<title>SweeneyThreads</title>
	<meta charset='utf-8'>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Documentazione prodotta per il corso Ingegneria del Software a.a 2015-2016">
	<meta name="keywords" content="Ingegneria del Software, Analisi dei requisiti, Progettazione, SweeneyThreads">
	<meta name="author" content="Bonato Paolo, Bortolazzo Matteo, Biggeri Mattia, Maino Elia, Nicoletti Luca, Padovan Tommaso, Tommasin Davide">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link href="http://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
	<link href="stile.css" rel="stylesheet" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">

<div class="jumbotron text-center">
  <h1>SPitFire</h1> 
  <h3>Support Platform for IT.. with fire</h3>
</div>

<div id="login" class="container-fluid ">
  <div class="row text-center">
    <div class="col-sm-12">
    <h2>LOGIN</h2>
	<form method="POST">
		<span>Username<br></span><input type="text" name="usr" /><br />
		<span>Password<br></span><input type="password" name="psw" /><br />
		<br>
		<button type="submit" class="btn btn-default" value="submit" >Accedi</button>
	</form>
	</div>
</div>
</div>

<div id="githubio" class="container-fluid bg-gray">
  <div class="row text-center">
    <div class="col-sm-12">
	<a href="http://sweeneythreads.github.io">Torna a sweeneythreads.github.io</a>
	</div>
</div>
</div>
<body>
</html>
END;



?>