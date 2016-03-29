<?php



session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
require_once("../lib/latexlib.php");
require_once("class_gulpease.php");

$conn=connect();

/*
$text=utf8_decode(file_get_contents('uploads/Gulpease'));
$text=trimLatex($text);
$Nfrasi=calculate_Nfrasi($text);
$Nparole=calculate_Nparole($text);
$Nlettere=calculate_Nlettere($text);
$GULP=89+(300*$Nfrasi-10*$Nlettere)/$Nparole;*/
//echo utf8_encode("$text");    //<---------------------------------------------------- decomennta qua se vuoi vedere il testo


$indice=GULP::calcola_gulpease();

echo '<br />';
echo 'n frasi= '.$indice['frasi'].'<br />';
echo 'n parole='.$indice['parole'].'<br />';
echo 'n lettere= '.$indice['lettere'].'<br />';
echo 'indice Gulpease= '.$indice['gulpease'].'<br />';
echo '<a href="gulpease.php">back</a>'





?>