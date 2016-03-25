<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../../login.php");


require_once('../lib/htmlgenerator.php');
require_once('../lib/latexlib.php');
require_once('metricheBase.php');
/**
* questo file contiene le informazioni sui documenti e sulle metriche da riportare nella tabella
* @see impostazioniRepo.php
*/
require_once('impostazioniRepo.php');



//disegno header e menu
echo pageHeader('Metriche repo');
echo navDocTools2();

echo <<< END
<div id="descrizioneTool" class="container-fluid ">
	<div class="row text-center">
		<div class="col-sm-12">
			<h2>Repo's Outpost</h2>
			<p>
				Questa &egrave la tabella riassuntiva delle metriche riguardanti i file
				nel nostro repository, per modificare quali sono i file interessati
				agire direttamente sul file <code>impostazioniRepo.php</code>
			</p><p>
				Per uteriori dettagli riguardo ad un determinato documento cliccare su <button type="button"> -> </button>
		</div>
	</div>

END;


#disegno la tabella
echo tableStart();

echo tableHeading(array(
	'Nome documento',
	'Versione',
	'Parole tot',
	'Figure tot',
	'Gulpease',
	'Lunghezza media sezioni'
));


foreach ($arrDocumentiRepo as $nomeDoc => $urlDoc) { 		//per ogni documento da mettere in tabella

	//lo scarico e lo salvo in $contenutoDoc
	$curlSession = curl_init();
	curl_setopt($curlSession, CURLOPT_URL, $urlDoc);
	curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	$contenutoDoc = utf8_decode(curl_exec($curlSession));
	curl_close($curlSession);

	/**
	* @var 	$doc 	document 	@see metricheBase.php
	* inizializzo $doc al tipo document definito in metricheBase.php, questa classe espone diversi metodi
	* per calcolare alcune statistiche
	*/
	$doc=new document($contenutoDoc);

	$nome='<p>'.$nomeDoc.'
		<form action="metric_calc.php" method="POST">
			<input type="hidden" name="texFile" value="'.$urlDoc.'" />
			<input type="submit" name="url" value="->" />
		</form></p>
	';
	$gulp=$doc->gulpease();
	$mediasection=$doc->mediaParolePerSection();

	//creo un array con tutte le statistiche che servono (Nome documento, Versione, Parole tot, Figure tot, Gulpease, Lunghezza media sezioni)
	$arrRigaTabella=array(
		$nome,
		'v'.$doc->versione(),
		$doc->parole(),
		$doc->figure(),
		number_format($gulp['gulp'],2,',',' '),
		number_format($mediasection['parolesections'],2,',',' ')
	);

	//scorro l'array e coloro le metriche che hanno un range di accettazione
	//verde		->	ottimale
	//arancione	->	accettabile
	//rosso 	->	non accettabile
	for ($i=0; $i < count($arrRigaTabella); $i++) { 
		if ($accettabilita[$i]!=0 && $ottimalita[$i]!=0) {
			if ($arrRigaTabella[$i]>=$ottimalita[$i][0] && $arrRigaTabella[$i]<=$ottimalita[$i][1])
				$arrRigaTabella[$i]='<p style="color:limegreen">'.$arrRigaTabella[$i].'</p>';
			else if ($arrRigaTabella[$i]>=$accettabilita[$i][0] && $arrRigaTabella[$i]<=$accettabilita[$i][1])
				$arrRigaTabella[$i]='<p style="color:orange">'.$arrRigaTabella[$i].'</p>';
			else
				$arrRigaTabella[$i]='<p style="color:red">'.$arrRigaTabella[$i].'</p>';
		}
	}

	//scrivo l'array appena calcolato come riga della tabella
	echo tableRow($arrRigaTabella);
}



echo tableEnd();

echo '</div>';



echo pageFooter();

?>
