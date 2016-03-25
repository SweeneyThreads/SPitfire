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
echo pageHeader('Calcolatore Metriche Documenti');
echo navDocTools2();


echo <<< END
<div id="descrizioneTool" class="container-fluid">
	<div class="row text-center">
		<div class="col-sm-12">
			<h2>Camel Calculator</h2>
			<p>
				Questo tool permette di caricare il documento in diversi modi ed ottenere tutti i dettagli
				delle metriche di quel documento.
			</p>
		</div>
	</div>

	<form method="POST">
		<p>Direct input</p>
		<div>
			<textarea rows="5" cols="75" name="texFile"></textarea>
		</div>
		<input type="submit" value="Calcola" name="direct_input" />
	</form>

	<br />

	<form method="POST">
		<p>Dall'url della versione raw del documento LaTeX</p>
		<input type="url" name="texFile" />
		<input type="submit" value="Calcola" name="url" />
	</form>

	<br />

	<form method="POST">
		<p>Da un documento in Repository</p>
		<select name="texFile">
END;

echo "\n";
foreach ($arrDocumentiRepo as $key => $value)
	echo "\t\t\t<option value=\"$value\">$key</option>\n";

echo <<< END

		</select>
		<input type="submit" value="Calcola" name="url" />
	</form>

	<br />

	<form method="post" enctype="multipart/form-data">
		Da file (anche drag and drop)
		<input type="file" name="fileToUpload" id="fileToUpload">
		<input type="submit" name="file" value="Calcola">
	</form>

	<hr />

END;



###############################################################################
###############################################################################
###############################################################################




if (isset($_POST['direct_input'])) {
	$doc=new document(utf8_decode($_POST['texFile']));
} else if (isset($_POST['url'])) {
	$curlSession = curl_init();
	curl_setopt($curlSession, CURLOPT_URL, $_POST['texFile']);
	curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	$contenutoDoc = utf8_decode(curl_exec($curlSession));
	curl_close($curlSession);
	$doc=new document($contenutoDoc);
} else if (isset($_POST['file'])) {
	$contenutoDoc=file_get_contents($_FILES['fileToUpload']['tmp_name']);
	$contenutoDoc=utf8_decode($contenutoDoc);
	$doc=new document($contenutoDoc);
}

if (isset($doc)) {

	echo '<br />'.tableStart();

	echo tableHeading(array(
		'Versione',
		'Parole tot',
		'Figure tot',
		'Gulpease',
		'Lunghezza media sezioni',
	));


	$gulp=$doc->gulpease();
	$gulp=
		'<table class="table table-striped table-bordered">'.
		'<tr><td>Frasi</td><td>'.$gulp['frasi'].'</td></tr>'.
		'<tr><td>Parole</td><td>'.$gulp['parole'].'</td></tr>'.
		'<tr><td>Lettere</td><td>'.$gulp['lettere'].'</td></tr>'.
		'<tr><td>Indice Gulpease</td><td>'.number_format($gulp['gulp'],2,',',' ').'</td></tr>'.
		'</table>';

	$mediasection=$doc->mediaParolePerSection();
	$mediasection=
		'<table class="table table-striped table-bordered">'.
		'<tr><td>Parole</td><td>'.$mediasection['parole'].'</td></tr>'.
		'<tr><td>Sezioni</td><td>'.$mediasection['sections'].'</td></tr>'.
		'<tr><td>Parole su Sezioni</td><td>'.number_format($mediasection['parolesections'],2,',',' ').'</td></tr>'.
		'</table>';

	//creo un array con tutte le statistiche che servono (Nome documento, Versione, Parole tot, Figure tot, Gulpease, Lunghezza media sezioni)
	$arrRigaTabella=array(
		'v'.$doc->versione(),
		$doc->parole(),
		$doc->figure(),
		$gulp,
		$mediasection
	);


	echo tableRow($arrRigaTabella);

	echo tableEnd();
}


echo '</div>';
echo pageFooter();

?>