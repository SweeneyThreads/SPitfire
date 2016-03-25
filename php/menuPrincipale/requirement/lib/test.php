<?php
session_start();
var_dump($_SESSION);
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) {
	header("Location: ../login.php");
}
require_once("../../lib/htmlgenerator.php");
require_once("../../lib/phplib.php");
include "class.diagram.php";
$conn=connect();

$g = new Diagram();



$result=mysql_query("select ID from Requisiti where ID not in (select figlio from derivazRequisiti)",$conn);
$foresta=array();
for ($i=0; $i < mysql_num_rows($result); $i++) { 
	$row=mysql_fetch_assoc($result);
	$foresta[$row['ID']]=array();
}

foreach ($foresta as $radice => &$albero) {
	$albero=completaAlbero($radice,$conn);
}

	
$g->SetRectangleBorderColor(124, 128, 239);
$g->SetRectangleBackgroundColor(194, 194, 239);
$g->SetFontColor(255, 255, 255);
$g->SetBorderWidth(0);
$g->SetData($foresta);
$g->Draw();





function completaAlbero($root,$conn) {
	$result=mysql_query("select figlio from derivazRequisiti where padre='$root'");
	if (mysql_num_rows($result)==0)
		return;
	$a=array();
	for ($i=0; $i < mysql_num_rows($result); $i++) { 
		$row=mysql_fetch_assoc($result);
		$a[$row['figlio']]=completaAlbero($row['figlio'],$conn);
	}
	return $a;
}
?>
