<?php



session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");

include_once("../lib/latexlib.php");
include_once("../../lib/latexlib.php");




class GULP {
	public static function calcola_gulpease($str=NULL) {
		if ($str==NULL) $text=utf8_decode(file_get_contents('uploads/Gulpease'));
		else $text=utf8_decode($str);
		$text=trimLatex($text);
		$Nfrasi=calculate_Nfrasi($text);
		$Nparole=calculate_Nparole($text);
		$Nlettere=calculate_Nlettere($text);
		$GULP=89+(300*$Nfrasi-10*$Nlettere)/$Nparole;
		return array(
			'frasi' => $Nfrasi,
			'parole' => $Nparole,
			'lettere' => $Nlettere,
			'gulpease' => $GULP
		);
	}
}



?>	