<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../../login.php");
require_once("../../lib/htmlgenerator.php");
require_once("../../lib/phplib.php");
require_once("../../lib/latexlib.php");
require_once("class_sectionLength.php");

$doc=utf8_decode($_POST['texFile']);






$indice=lunghezzaSezioni::calcola($doc);



echo 'Numero Parole Totali: '.$indice['parole'].'<br/ >';

echo 'Numero Sezioni: '.$indice['sezioni'].'<br/ >';

echo 'Parole per Sezione: '.$indice['parole']/$indice['sezioni'].'<br/ >';

echo '<a href="form.php">back</a>'


?>