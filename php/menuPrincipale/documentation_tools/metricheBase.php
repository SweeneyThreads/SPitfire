<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../../../login.php");


require_once('../lib/htmlgenerator.php');
require_once('../lib/latexlib.php');
//require_once('class_gulpease.php');
//require_once('class_sectionLength.php');

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
	* calcola il numero di lettere del documento
	*
	* @uses 	$this->eliminaCaratteri()
	* @return 	integer
	*/
	public function lettere() {
		$arrEliminandi=array(' ',"\t",PHP_EOL);
		$str=$this->eliminaCaratteri($arrEliminandi,$this->plain);
		return strlen($str);
	}


	/**
	* calcola il numero di parole del documento
	*
	* @uses 	$this->multiExplode()
	* @uses 	$this->cancellaNull()
	* @return 	integer
	*/
	public function parole() {
		$arrParole=$this->multiExplode(array(' ',"\t"),trim($this->plain));
		$arrParole=$this->cancellaNull($arrParole);
		return count($arrParole);
	}

	/**
	* calcola il numero di frasi del documento
	*
	* @return 	integer
	*/
	public function frasi() {
		$arrFrasi=explode('.', trim($this->plain));
		$arrFrasi=$this->cancellaNull($arrFrasi);
		return count($arrFrasi);
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
	* @return 	int|float*
	*/
	public function mediaParolePerSection() {
		$sectionStar=substr_count($this->latex, '\section*');
		$subs=substr_count($this->latex, '\subsection');
		$subsubs=substr_count($this->latex, '\subsubsection');
		return array(
			'parole' => $this->parole(),
			'sections' => $sectionStar+$subs+$subsubs,
			'parolesections' => $this->parole()/($sectionStar+$subs+$subsubs)
		);
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

	/**
	* calcola l'indice gulpease del documento
	*
	* @uses 	$this->frasi()
	* @uses 	$this->parole()
	* @uses 	$this->lettere()
	* @return 	int|float*
	*/
	public function gulpease() {
		$Nfrasi=$this->frasi();
		$Nparole=$this->parole();
		$Nlettere=$this->lettere();
		$GULP=89+(300*$Nfrasi-10*$Nlettere)/$Nparole;
		return array(
			'frasi' => $Nfrasi,
			'parole' => $Nparole,
			'lettere' => $Nlettere,
			'gulp' => $GULP
		);
	}






	############################################################################################
	################################ ALCUNI METODI DI UTILITA' #################################
	############################################################################################



	private function multiExplode($delimiters,$string) {
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return  $launch;
	}

	private function cancellaNull($arr) {
		foreach ($arr as $key => $value) {
			if ($value==NULL or $value=='' or $value==' ' or $value=="\t" or $value==PHP_EOL) unset($arr[$key]);
		}
		return $arr;
	}

	private function eliminaCaratteri($arr,$str) {
		foreach ($arr as $value) {
			$str=str_replace($value, '', $str);
		}
		return $str;
	}
}



?>