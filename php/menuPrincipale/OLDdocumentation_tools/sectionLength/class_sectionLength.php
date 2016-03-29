<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../../login.php");
require_once("../../lib/htmlgenerator.php");
require_once("../../lib/phplib.php");
require_once("../../lib/latexlib.php");



class lunghezzaSezioni {
	public static function calcola($doc) {
		//prendo la parte di documento tra inizio e fine
		$doc=explode('\begin{document}', $doc);
		$doc=$doc[1];
		
		//vado al primo section
		$arrDoc=array();
		$arrTmp=explode('\subsection', $doc);
		foreach ($arrTmp as $value) {
			$arr=explode('\subsubsection', $value);
			foreach ($arr as $v) {
				array_push($arrDoc, $v);
			}
		}

		
		/**
		* 
		*/
		foreach ($arrDoc as &$value) {
			$value=trimLatex('aaa'.PHP_EOL.'\begin{document}'.PHP_EOL.'\subsection'.$value.PHP_EOL.'\end{document}');
		}
		
		$numeroSezioni=count($arrDoc);
		
		$numeroParole=0;
		foreach ($arrDoc as $value) {
			$value=explode(' ', $value);
			$parole=count($value);
			$numeroParole+=$parole;
		}

		return array(
			'sezioni' => $numeroSezioni,
			'parole' => $numeroParole
		);
	}
}