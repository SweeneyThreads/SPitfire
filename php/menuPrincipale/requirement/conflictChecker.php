<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("lib/class_requisiti.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();



$requisitiDB=RequirementsSystem::getInstanceFromDB($conn);
$arrRequisitiDB=$requisitiDB->getRequisiti();

$requisitiLatex=RequirementsSystem::getInstanceFromLatex($DEFAULT_REPO_LOCATION);
$arrRequisitiLatex=$requisitiLatex->getRequisiti();



$DBsiLatexNo=array();	//ha struttura requisitoDB => requisitoLatex e contiene tutti i requisiti che non ci sono da entrambe le parti
$LatexSiDBno=array();

foreach ($arrRequisitiDB as $idDB => $requisitoDB) {
	if (!in_array($idDB, array_keys($arrRequisitiLatex))) {	//se un requisito presente in database non c'è nel latex
		array_push($DBsiLatexNo, $idDB);
	}
}

foreach ($arrRequisitiLatex as $idLatex => $requisitoLatex) {
	if (!in_array($idLatex, array_keys($arrRequisitiDB))) {
		array_push($LatexSiDBno,$idLatex);
	}
}



echo <<<END
<!DOCTYPE html>
<html lang="it">
<head>
	<title>Conflitti</title>
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
  <a href="requisiti.php">Torna ai Requisiti</a><br />
  <h2>All paths lead to conflict.</h2>
  <p><a href="http://hydra-media.cursecdn.com/dota2.gamepedia.com/d/dc/Spir_move_19.mp3" title="Play" class="sm2_button">Spirit Breaker -- Dota2</a></p>
  <form method="POST" action="process_solveConflict.php">
  <p>Requisiti Presenti in DB ma non nel LaTeX</p>
  <table class="table table-striped table-bordered">
	<thead>
	  <tr>
		<th>ID</th>
		<th>Nome</th>
		<th>Descrizione</th>
		<th>Fonte</th>
		<th>Note</th>
		<th>...</th>
	  </tr>
	</thead>
	<tbody>

END;

foreach ($DBsiLatexNo as $id) {
	$row=$arrRequisitiDB[$id]->GetAll();
	echo "
		<tr>
			<td>".$row['ID']."</a></td>
			<td>".$row['Nome']."</td>
			<td>".$row['Descrizione']."</td>
			<td>".$row['Fonte']."</td>
			<td>".$row['Note']."</td>
			<td><input type=\"checkbox\" name=\"reqsel[]\" value=\"".$row['ID']."\" /></td>
		</tr>
	";
}


echo <<<END
	</tbody>
  </table>

  <input type="submit" value="Autogenera Latex" name="latex" />

</form>

	
  <br /><br /><br /><br />

  <form method="POST" action="process_solveConflict.php">
  <p>Requisiti Presenti nel Latex ma non nel DB</p>
  <table class="table table-striped table-bordered">
	<thead>
	  <tr>
		<th>ID</th>
		<th>Nome</th>
		<th>Descrizione</th>
		<th>Fonte</th>
		<th>Note</th>
		<th>...</th>
	  </tr>
	</thead>
	<tbody>

END;

foreach ($LatexSiDBno as $id) {
	$row=$arrRequisitiLatex[$id]->GetAll();
	echo "
		<tr>
			<td>".$row['ID']."</a></td>
			<td>".$row['Nome']."</td>
			<td>".$row['Descrizione']."</td>
			<td>".$row['Fonte']."</td>
			<td>".$row['Note']."</td>
			<td><input type=\"checkbox\" name=\"reqsel[]\" value=\"".$row['ID']."\" /></td>
		</tr>
	";
}








echo <<<END

</tbody>
  </table>
  <input type="checkbox" onclick="for(c in document.getElementsByName('rfile')) document.getElementsByName('reqsel[]').item(c).checked = this.checked">
  <input type="submit" value="Aggiungi a DB" name="DB" />

</form>

<hr />

  <p>Differenze di contenuto ---- non ancora implementato</p>
  <table class="table table-striped table-bordered">
	<thead>
	  <tr>
		<th>ID</th>
		<th>Nome</th>
		<th>Descrizione</th>
		<th>Fonte</th>
	  </tr>
	</thead>
	<tbody>

END;
######################################################################################################################################################
/*
$requisitiComuni=array();
foreach ($arrRequisitiDB as $idRequisito => $requisito) {
	if (in_array($idRequisito, array_keys($arrRequisitiLatex)))
		$requisitiComuni[$idRequisito]=$requisito;
}


foreach ($requisitiComuni as $idRequisito => $requisito) {
	if (!$requisito->isEqual($arrRequisitiLatex[$idRequisito])) {
		$row=$requisito->differenze($arrRequisitiLatex[$idRequisito]);
		echo "
			<tr>
				<td>".$row['ID']."</a></td>
				<td>".$row['Nome']."</td>
				<td>".$row['Descrizione']."</td>
				<td>".$row['Fonte']."</td>
			</tr>
		";
	}
}




*/




echo <<< END

  </tbody>
</table>


<body>
</html>
END;
?>