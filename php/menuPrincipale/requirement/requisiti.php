<?php
session_start();
var_dump($_SESSION);
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) {
	header("Location: ../login.php");
}
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();



$result=mysql_query('select count(*) as NumRequisiti from Requisiti',$conn);
$num=mysql_fetch_assoc($result);
$num=$num['NumRequisiti'];


$linkPadre=getPadre($_GET['padre'],$conn);


echo <<<END
<!DOCTYPE html>
<html lang="it">
<head>
	<title>Tool requisiti</title>
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

<nav class="navbar navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>                        
			</button>
		</div>
		<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="navbar-nav navbar-right">
				<li><a href=".." id="home">MENU PRINCIPALE</a></li>
				<li><a href="aggiungiRequisito.php" id="rotaz">Inserisci un Requisito</a></li>
				<li><a href="requisiti.php" id="rotaz">Requisiti</a></li>
				<li><a href="autoaddRequirement.php" id="parser">Latex to DB</a></li>
				<li><a href="autogenerateLatex.php" id="parser">DB to Latex</a></li>
			</ul>
		</div>
	</div>
</nav>



<div class="container" style="padding-top: 60px;">
  <form method="POST" action="process_setDone.php">
  <h2>Requisiti</h2>
  <p>La tabella sottostante contiene tutti i requisiti individuati:  (tot $num)</p>
  <a href="requisiti.php?padre=$linkPadre">Torna al Padre</a>
  <table class="table table-striped table-bordered">
	<thead>
	  <tr>
		<th>ID</th>
		<th>Nome</th>
		<th>Descrizione</th>
		<th>Fonte</th>
		<th>Completato</th>
		<th>Note</th>
	  </tr>
	</thead>
	<tbody>

END;

// seleziono quali sono i requisiti da far vedere
if (!isset($_GET['padre']) || $_GET['padre']==NULL)
	$query='select * from Requisiti where id not in (select figlio from derivazRequisiti) ORDER BY LENGTH(ID), ID';
else
	$query='select * from Requisiti where id in (select figlio from derivazRequisiti where padre=\''.$_GET['padre'].'\') ORDER BY LENGTH(ID), ID';

//echo $query;

$result=mysql_query($query,$conn);


$arrRighe=array();
for ($i=0; $i<mysql_num_rows($result) ; $i++) {
	$row=mysql_fetch_assoc($result);
	


	$checked='';
	$done='';
	if ($row['completato']==1) {
		$checked="checked";
		$done='OK';
	}


	$rowIdSafe=str_replace('[', '(', $row['ID']);
	$rowIdSafe=str_replace(']', ')', $rowIdSafe);


	if (is_foglia($row['ID'],$conn))
		echo "
			<tr>
				<td>".$row['ID']."</a></td>
				<td>".$row['Nome']."</td>
				<td>".$row['Descrizione']."</td>
				<td>".$row['Fonte']."</td>
				<td><input type=\"checkbox\" name=\"completati[]\" value=\"".$row['ID']."\" ".$checked." />$done</td>
				<td><textarea name=\"note[$rowIdSafe]\" rows=\"3\" cols=\"35\">".$row['Note']."</textarea></td>
			</tr>
		";
	else
		echo "
			<tr>
				<td><a href=\"requisiti.php?padre=".$row['ID']."\">".$row['ID']."</a></td>
				<td>".$row['Nome']."</td>
				<td>".$row['Descrizione']."</td>
				<td>".$row['Fonte']."</td>
				<td>".$done."</td>
				<td><textarea name=\"note[$rowIdSafe]\" rows=\"3\" cols=\"35\">".$row['Note']."</textarea></td>
			</tr>
		";

}

$padre=$_GET['padre'];

echo <<<END
	</tbody>
  </table>

  <input type="hidden" name="vecchioPadre" value="$padre" /> 
  <input type="submit" value="Applica cambiamenti" />

</form>


<body>
</html>

END;








/////////////////////////////////////////////////
function is_foglia($requisito,$conn) {
	$query="select * from derivazRequisiti where padre='".$requisito."'";
	$result=mysql_query($query,$conn);
	if (mysql_num_rows($result)==0) return true;
	return false;
}

function getPadre($requisito,$conn) {
	$query="select padre from derivazRequisiti where figlio='".$requisito."'";
	$result=mysql_query($query,$conn);
	$row=mysql_fetch_assoc($result);
	return $row['padre'];
}



?>
