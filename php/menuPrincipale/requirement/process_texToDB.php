<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/phplib.php");
$conn=connect();


$text=utf8_decode($_POST['tex']);

//controllo se è documento intero o stralcio
if (strpos($text, '\endhead') !== false) { //se c'è un \endhead nel documento
	$text=explode('\endhead', $text);
	$text=$text[1];
	if (strpos($text, '\bottomrule') !== false) {
		$text=explode('\bottomrule', $text);
		$text=$text[0];
	}
}

//un po' di pulizia etnica
$text=str_replace("\t", '', $text);

$text=explode(PHP_EOL, $text);
foreach ($text as $key => $value) {
	$value=trim($value);
	if ($value[0]=='%') unset($text[$key]);
}
$text=implode(PHP_EOL, $text);

$text=str_replace(PHP_EOL, '', $text);
$text=str_replace("\\\\", '', $text);
$text=str_replace("'", "\\'", $text);



$text=explode('\hline', trim($text));

foreach ($text as $value) {
	$value=trim($value);
	$arr=explode('&', $value);
	
	foreach ($arr as &$v) {
		$v=trim($v);
		$v=utf8_encode($v);
	}

	if (count($arr)==4){
		$query="INSERT INTO Requisiti(ID,Nome,Fonte,Descrizione) VALUES(
			'".$arr[0]."',
			'".$arr[1]."',
			'".$arr[2]."',
			'".$arr[3]."'
		)";
	
		$result=mysql_query($query,$conn)
			or die("Inserimento nella tabella requisiti fallito" . mysql_error($conn));
	}

	header("Location: requisiti.php");
}