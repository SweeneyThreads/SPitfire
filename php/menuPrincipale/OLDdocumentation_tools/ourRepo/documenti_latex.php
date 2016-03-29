<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../../login.php");


require_once('../../lib/htmlgenerator.php');
require_once('../../lib/latexlib.php');
require_once('../class_gulpease.php');
require_once('../sectionLength/class_sectionLength.php');
require_once('metricheBase.php');

/**
* Questo array contiene la mappa chiave-valore dei documenti da mostrare nella tabella
* la chiave è il nome del documento
* il valore è il link in cui sono salvati (nella versione raw)
*/
$arrDocumentiRepo=array(
	'Analisi dei requisiti' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Analisi%20dei%20requisiti.tex',
	'Glossario' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Glossario.tex',
	'Lettera di presentazione' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Lettera%20di%20presentazione.tex',
	'Norme di progetto' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Norme%20di%20progetto.tex',
	'Piano di progetto' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Piano%20di%20progetto.tex',
	'Piano di qualifica' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Piano%20di%20qualifica.tex',
	'Studio di fattibilit&agrave' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Studio%20di%20fattibilit%C3%A0.tex'
);


/**
* questi 2 array rappresentano i range di accettabilità e ottimalità per le metriche in tabella
* se 0 significa che non ci sono metriche
* altrimenti sono l'array contenente {valoreMin,valoreMax}
*/
$accettabilita=array(
	0,				//nome documento
	0,				//versione
	0,				//parole tot
	0,				//figure tot
	array(35,100),	//gulpease
	array(100,250)	//media parole per section
);

$ottimalita=array(
	0,				//nome documento
	0,				//versione
	0,				//parole tot
	0,				//figure tot
	array(45,100),	//gulpease
	array(15,100)	//media parole per section
);


//disegno header e menu
echo pageHeader('Metriche repo');
echo navDocTools();



#disegno la tabella
echo '<br /><br /><br /><br />'.tableStart();

echo tableHeading(array(
	'Nome documento',
	'Versione',
	'Parole tot',
	'Figure tot',
	'Gulpease',
	'Lunghezza media sezioni',
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

	//calcolo il gulpease
	$gulp=GULP::calcola_gulpease($contenutoDoc);
	$gulp=$gulp['gulpease'];

	//creo un array con tutte le statistiche che servono (Nome documento, Versione, Parole tot, Figure tot, Gulpease, Lunghezza media sezioni)
	$arrRigaTabella=array(
		$nomeDoc,
		'v'.$doc->versione(),
		$doc->parole(),
		$doc->figure(),
		number_format($gulp,2,',',' '),
		number_format($doc->mediaParolePerSection(),2,',',' ')
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





echo pageFooter();

?>
