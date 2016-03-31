<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
//require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
require_once("lib/class_requisiti.php");
$conn=connect();



$requisitiDB=RequirementsSystem::getInstanceFromDB($conn);
$arrRequisitiDB=$requisitiDB->getRequisiti();

$requisitoFonte=array();
foreach ($arrRequisitiDB as $idRequisito => $requisito) {
	$idRequisito=str_replace("\n", '', trim($idRequisito));
	$req=$requisito->GetFonte();
	$req=str_replace("\n", '', trim($req));
	$requisitoFonte[$idRequisito]=$req;
}
uksort($requisitoFonte, 'compareLenght');


//tex del tracciamento requisiti fonti
$texReqFont='';
foreach ($requisitoFonte as $requisito => $fonte) {
	$fonte=str_replace('ewline', '\newline', $fonte);
	$texReqFont.=utf8_decode($requisito.' & '.$fonte.' \\\\'."\n");
	$texReqFont.='\hline'."\n";
}
$texReqFont=utf8_encode($texReqFont);






// genero l'array contenente tutte le fonti
$arrFonti=array();
foreach ($requisitoFonte as $fonte)
	if (!in_array($fonte, $arrFonti)) array_push($arrFonti, $fonte);



//genero l'array con il tracciamento da fonti a requisiti
$fonteRequisiti=array();
foreach ($arrFonti as $fonte)
	$fonteRequisiti[$fonte]=array();


foreach ($requisitoFonte as $requisito => $fonte) {
	array_push($fonteRequisiti[$fonte],$requisito);
}





//cosa molto stramba, invece di scrivere \newline interpreta lo \n come return, quindi devo controllare se ci sono "ewline"
foreach ($fonteRequisiti as $fonte => $requisito) {
	if (strpos($fonte, 'ewline')!==false) {								//se dentro la key dell'array c'è un "ewline"
		$arrFontiTemp=explode('ewline', $fonte);							//lo splitto e lo metto in un array $arrFontiTemp
		foreach ($arrFontiTemp as $fonteTemp) {								//scorro l'array $arrFontiTemp
			$fonteTemp=trim($fonteTemp);
			if ( !in_array($fonteTemp, array_keys($fonteRequisiti)) )			//se un elemento dell'array $arrFontiTemp non c'è in $fontiRequisiti..
				$fonteRequisiti[$fonteTemp]=array();								//..allora vuol dire che non avevo inserito la fonte, e quindi inserisco una nuova entry nella mappa
			foreach($requisito as $req) array_push($fonteRequisiti[$fonteTemp], $req);
		}
		unset($fonteRequisiti[$fonte]);
	}
}


//sort
function toArrCodice($a) {
	$a=explode('[', $a);
	$a=$a[1];
	$a=explode(']', $a);
	$a=$a[0];
	$a=explode('.', trim($a));
	return $a;
}


function compareLenght($a, $b) {
	$a=toArrCodice($a);
	$b=toArrCodice($b);
	$lunghezzaMinima=min(array(count($a),count($b)));
	for ($i=0; $i < $lunghezzaMinima; $i++) { 
		if ($a[$i]<$b[$i]) return -1;
		if ($a[$i]>$b[$i]) return 1;
	}
	if (count($a)==count($b)) return 0;
	return (count($a) < count($b)) ? -1 : 1;
}

function toNumRequisito($a) {
	return explode('.',trim(str_replace('UC', '', trim($a))));
}
function contiene($needle,$haystack) {
	return strpos($haystack, $needle)!==false;
}

function compareUC($a,$b) {
	if (contiene('Capitolato',$a) or contiene('Decisione',$a)) {
		if (!(contiene('Capitolato',$b) or contiene('Decisione',$b))) return -1;
		else return strcmp(trim($a), trim($b));
	}
	if (contiene('Capitolato',$b) or contiene('Decisione',$b)) {
		if (!(contiene('Capitolato',$a) or contiene('Decisione',$a))) return 1;
		else return strcmp(trim($a), trim($b));
	}

	$a=toNumRequisito($a);
	$b=toNumRequisito($b);
	$lunghezzaMinima=min(array(count($a),count($b)));
	for ($i=0; $i < $lunghezzaMinima; $i++) { 
		if ($a[$i]<$b[$i]) return -1;
		if ($a[$i]>$b[$i]) return 1;
	}
	if (count($a)==count($b)) return 0;
	return (count($a) < count($b)) ? -1 : 1;
}


uksort($fonteRequisiti, 'compareUC');

foreach ($fonteRequisiti as $fonte => &$requisiti) {
	uasort($requisiti, 'compareLenght');
}


//tex del tracciamento fonti requisiti
$texFontReq='';
foreach ($fonteRequisiti as $fonte => $requisito) {
	$listaRequisiti='';
	foreach ($requisito as $val) {
		$listaRequisiti.=utf8_decode($val.' \newline'."\n");
	}
	$listaRequisiti=substr($listaRequisiti, 0,-9);
	$listaRequisiti=utf8_encode($listaRequisiti);

	$texFontReq.=utf8_decode($fonte.' & '.$listaRequisiti.' \\\\'."\n");
	$texFontReq.='\hline'."\n";
	$texFontReq=utf8_encode($texFontReq);
}




echo <<<END
<!DOCTYPE html>
<html lang="it">
<head>
	<title>Tracciamento</title>
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
  <a href="#fonreq">Fonti-Requisiti</a><br />
  <a href="#reqfon">Requisiti-Fonti</a><br />

  <a name="fonreq"></a> 
  <h2>Fonti - Requisiti</h2>
  <pre>  
$texFontReq
  </pre>

  <a name="reqfon"></a> 
  <h2>Requisiti - Fonti</h2>
  <pre>  
$texReqFont
  </pre>
</div>
</body>
</html>



END;


?>