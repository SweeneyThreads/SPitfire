<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("class_requisiti.php");
require_once("infoRepo.php");

class Conflitti {
	public static function detectMissing($conn,$repoLocation) {
		$requisitiDB=RequirementsSystem::getInstanceFromDB($conn);
		$arrRequisitiDB=$requisitiDB->getRequisiti();

		$requisitiLatex=RequirementsSystem::getInstanceFromLatex($repoLocation);
		$arrRequisitiLatex=$requisitiLatex->getRequisiti();


		$DBsiLatexNo=array();
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


		if (count($DBsiLatexNo)==0 && count($LatexSiDBno)==0) return false;
		else return true;
	}

}


?>