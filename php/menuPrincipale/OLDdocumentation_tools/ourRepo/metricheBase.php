<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../../../login.php");


require_once('../../lib/htmlgenerator.php');
require_once('../../lib/latexlib.php');
require_once('../class_gulpease.php');
require_once('../sectionLength/class_sectionLength.php');

/**
* classe che calcola alcue metriche di un documento latex
*
* @version 1.0
* @author Tommaso Padovan <tommaso.pado@gmail.com>
* @since 1.0
*/
class document {
	private $latex;
	private $plain;

	public function __construct($str) {
		$this->latex=$str;
		$this->plain=trimLatex($str);
	}

	/**
	* calcola il numero di parole del documento
	*
	* @return 	integer
	*/
	public function parole() {
		$arrParole=explode(' ', $this->plain);
		return count($arrParole);
	}

	/**
	* calcola il numero di pagine del documento
	*
	* @return 	integer
	* @todo 	trovare un modo di farlo
	*/
	public function pagine() {
		return 'devo ancora fare questa funzione';
	}

	/**
	* ritorna la versione del documento
	*
	* @return 	integer
	*/
	public function versione() {
		$arr=explode('\begin{center}', $this->latex); $arr=$arr[1];
		$arr=explode('Versione', $arr); $arr=$arr[1];
		$arr=explode('\end{center}', $arr); $arr=$arr[0];
		return $arr;
	}

	/**
	* calcola la media di parole per sezione del documento
	*
	* @uses 	$this->pagine()
	* @return 	float
	*/
	public function mediaParolePerSection() {
		$sectionStar=substr_count($this->latex, '\section*');
		$subs=substr_count($this->latex, '\subsection');
		$subsubs=substr_count($this->latex, '\subsubsection');
		return $this->parole()/($sectionStar+$subs+$subsubs);
	}

	/**
	* calcola il numero di figure del documento
	*
	* @return 	integer
	*/
	public function figure() {
		$numfigure=
			substr_count($this->latex, '\begin{figure}')+
			substr_count($this->latex, '\begin{table}')+
			substr_count($this->latex, '\includegraphics')+
			substr_count($this->latex, '\begin{tabularx}')+
			substr_count($this->latex, '\LTXtable');
		return $numfigure;
	}
}



?>