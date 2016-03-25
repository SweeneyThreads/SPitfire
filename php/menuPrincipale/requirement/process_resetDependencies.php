<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/phplib.php");
$conn=connect();



$query="SELECT * FROM Requisiti";
$result=mysql_query($query,$conn)
	or die("Inserimento nella tabella requisiti fallito" . mysql_error($conn));


$arrRequisiti=array();
for ($i=0; $i<mysql_num_rows($result) ; $i++) {
	$row=mysql_fetch_assoc($result);
	$arrRequisiti[$row['ID']]=$row['ID'];
}



foreach ($arrRequisiti as $key => &$requisito) {
	//ricavo il codice numerio del requisito
	$requisito=str_replace(' ', '', $requisito);
	$requisito=explode(']', $requisito);
	$requisito=$requisito[0];
	$requisito=trim($requisito);
	$requisito=explode('.', $requisito);
}


function contiene($needle,$haystack) {
	if (strpos($haystack, $needle) !== false && $haystack!=$needle) return true;
	return false;
}

//svuoto la tabella
$result=mysql_query("DELETE FROM derivazRequisiti WHERE 1");

/*
foreach ($arrRequisiti as $idPadre => $codicePadre) {
	foreach ($arrRequisiti as $idFiglio => $codiceFiglio) {
		if (contiene($codicePadre,$codiceFiglio)) {
			$query="INSERT INTO derivazRequisiti(padre,figlio) VALUES('$idPadre','$idFiglio')";
			$result=mysql_query($query);
		}
	}
}*/


foreach ($arrRequisiti as $idPadre => $codicePadre) {
	$livelloRequisito=count($codicePadre);
	foreach ($arrRequisiti as $idFiglio => $codiceFiglio) {
		if (count($codiceFiglio)==$livelloRequisito+1) {
			$ok=true;
			for ($i=0; $i < $livelloRequisito; $i++) { 
				if ($codicePadre[$i]!=$codiceFiglio[$i]) $ok=false;
			}
			if ($ok) {
				$query="INSERT INTO derivazRequisiti(padre,figlio) VALUES('$idPadre','$idFiglio')";
				$result=mysql_query($query);
			}
		}
	}
}


header("Location: requisiti.php");




?>