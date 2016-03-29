<?php
/**
* assume che le parole di gloassario siano tutte e sole quelle
* successive a \mychapter{2}
* e argomenti di \textbf{}
*/
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();

$text=utf8_decode(file_get_contents('uploads/Glossario'));

$text=explode('\section*{A}', $text);
$text=$text[1];

$text=explode('\textbf{', $text);
$arrParoleGlossario=array();


$i=0;
while (isset($text[$i])) {
	$i++;
	$newGlosWord=explode('}',$text[$i]);
	$newGlosWord=$newGlosWord[0];
	$newGlosWord=explode(':', $newGlosWord);
	$newGlosWord=$newGlosWord[0];

	array_push($arrParoleGlossario, $newGlosWord);
}


$result=mysql_query('delete from TerminiGlossario where 1',$conn); //cancello tutti i dati preesistenti
foreach ($arrParoleGlossario as $value) {
	$query="INSERT INTO TerminiGlossario VALUES ('".$value."')";
	$result=mysql_query($query,$conn);
}

header("Location: glossario.php");



?>