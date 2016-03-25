<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/phplib.php");
$conn=connect();


foreach ($_POST['note'] as $key => $value) {
	$rowId=str_replace('(', '[', $key);
	$rowId=str_replace(')', ']', $rowId);
	$query="
			UPDATE Requisiti
			SET Note='$value'
			WHERE ID='$rowId'
		";
		$res=mysql_query($query,$conn);
}


/**
* 	@var tuttiID 	array 	array di tutti gli id di requisito che l'utente stava visualizzando nella pagina dove ha
* 	premuto su "completa selezionati"
*/
$tuttiID=array();
if (!isset($_POST['vecchioPadre']) || $_POST['vecchioPadre']==NULL)
	$query='select * from Requisiti where id not in (select figlio from derivazRequisiti)';
else
	$query='select * from Requisiti where id in (select figlio from derivazRequisiti where padre=\''.$_POST['vecchioPadre'].'\')';
$result=mysql_query($query,$conn);
for ($i=0; $i<mysql_num_rows($result) ; $i++) {
	$row=mysql_fetch_assoc($result);
	array_push($tuttiID, $row['ID']);
}



foreach ($tuttiID as $id) {
	//se l'id in questione è anche dentro l'array degli ID marcati come completati
	if (in_array($id, $_POST['completati'])) {
		$query="
			UPDATE Requisiti
			SET completato=1
			WHERE ID='$id'
		";
		$result=mysql_query($query,$conn);
	} else { //altrimenti...
		$query="
			UPDATE Requisiti
			SET completato=0
			WHERE ID='$id'
		";
		$result=mysql_query($query,$conn);
	}
}




//faccio l'update di tutta la tabella
$result=mysql_query('select * from Requisiti',$conn);
for ($i=0; $i<mysql_num_rows($result) ; $i++) {
	$row=mysql_fetch_assoc($result);
	if (checkIfDone($row['ID'],$conn)) {
		$query="
			UPDATE Requisiti
			SET completato=1
			WHERE ID='".$row['ID']."'
		";
		$res=mysql_query($query,$conn);
	} else {
		$query="
			UPDATE Requisiti
			SET completato=0
			WHERE ID='".$row['ID']."'
		";
		$res=mysql_query($query,$conn);
	}
}








/////////////////////////////////////////////////
function checkIfDone($idRequisito,$conn) {
	if (is_foglia($idRequisito,$conn)) {
		if (getIfDone($idRequisito,$conn)) return true;
		else return false;
	}
	
	$figli=getFigli($idRequisito,$conn);
	//var_dump($figli); echo '<br />';
	$done=true;
	foreach ($figli as $figlio)
		if (!getIfDone($figlio,$conn)) $done=false;

	return $done;
	
}



function getIfDone($idRequisito,$conn) {
	$result=mysql_query("select * from Requisiti where id='$idRequisito'",$conn);
	$row=mysql_fetch_assoc($result);
	if ($row['completato']==1) return true;
	else return false;
}


function getFigli($idRequisito,$conn) {
	$result=mysql_query("select * from derivazRequisiti where padre='$idRequisito'",$conn);
	$figli=array();
	for ($i=0; $i<mysql_num_rows($result) ; $i++) {
		$row=mysql_fetch_assoc($result);
		array_push($figli, $row['figlio']);
	}
	return $figli;
}




function is_foglia($requisito,$conn) {
	$query="select * from derivazRequisiti where padre='".$requisito."'";
	$result=mysql_query($query,$conn);
	if (mysql_num_rows($result)==0) return true;
	return false;
}
/////////////////////////////////////////////////


header("Location: requisiti.php?padre=".$_POST['vecchioPadre']);



?>