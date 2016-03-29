<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php&thenbackto=roleparser%2Fgest_ruoli.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();

echo pageHeader('Tool Glossario');
echo navDocTools();


/**
* parte per l'upload dei file
*/
$target_dir = "uploads/";
$target_file = $target_dir . "Glossario";
$uploadOk = 1;
if(isset($_POST["action"]) and $_POST['action']=="Aggiorna il Glossario") {
	if ($_FILES["fileToUpload"]["size"] > 500000) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			header("Location: process_up_glossario.php");
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
}
$target_dir = "uploads/";
$target_file = $target_dir . "Tex";
$uploadOk = 1;
if(isset($_POST["action"]) and $_POST['action']=="Carica Tex") {
	if ($_FILES["fileToUpload"]["size"] > 500000) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			header("Location: process_mettileg.php");
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
}	
echo <<< END
<div id="descrizioneTool" class="container-fluid ">
	<div class="row text-center">
		<div class="col-sm-12">
			<h2>TOOL GLOSSARIO</h2>
			<p>
				Questo tool permette di aggiungere le G a pedice di tutti i documenti in Latex.<br />
				Il form in alto ("Aggiorna Glossario") permette di caricare direttamente il file
				Latex contente l.ultima versione del glossario, e genera in automatico una tabella
				di DB contenente tutti i termini presenti. [Sarebbe utile inserire una tabellina che mostri
				quali sono le parole correntemente memorizzate in DB, lo farò alla prossima versione.]<br />
				Il form in basso ("Glossy Buddy") permette di accedere al tool vero e proprio. Caricando il file
				a cui si vuole aggiungere la notazione di glossario, premendo su "Carica Tex" si avvierà il download
				di un file contente lo stesso codice Latex ma con le G a pedice al posto giusto. [nelle prossime
				versioni si potrà fare in modo che il file scaricato mantenga il nome del file originale]
			</p>
		</div>
	</div>
	<div class="row text-center">
	<div class="col-sm-12">
		<h3>Aggiorna Glossario</h3>
		<p>Carica il file contenente il glossario più aggiornato</p>
		<form method="post" enctype="multipart/form-data">
			Select file to upload: <input type="file" name="fileToUpload" id="fileToUpload">
			<input type="submit" name="action" value="Aggiorna il Glossario">
		</form>
		</div>
	</div>
	
	<div class="row text-center">
	<div class="col-sm-12">
		<h3>Glossy Buddy</h3>
		<p>Carica il file tex a cui vuoi aggiungere le pedici</p>
		<form method="post" enctype="multipart/form-data">
			Select file to upload: <input type="file" name="fileToUpload" id="fileToUpload">
			<input type="submit" name="action" value="Carica Tex">
		</form>
		</div>
	</div>
</div>
END;
echo pageFooter();
?>