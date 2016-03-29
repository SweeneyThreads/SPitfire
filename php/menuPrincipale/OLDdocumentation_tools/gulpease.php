<?php 
session_start();
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();



$target_dir = "uploads/";
$target_file = $target_dir . "Gulpease";
$uploadOk = 1;
if(isset($_POST["action"]) and $_POST['action']=="Calcola Gulpease") {
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
			header("Location: process_gulpease.php");
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
}

echo pageHeader('Tool Glossario');
echo navDocTools();
echo <<< END
<div id="descrizioneTool" class="container-fluid ">
	<div class="row text-center">
		<div class="col-sm-12">
			<h2>TOOL GULPEASE</h2>
			<p>
				Gulpease
			</p>
		</div>
	</div>
	<div class="row text-center">
	<div class="col-sm-12">
		<h3>Aggiorna Glossario</h3>
		<p>Carica il file contenente il documento di cui calcolare il gulpease</p>
		<form method="post" enctype="multipart/form-data">
			Select file to upload: <input type="file" name="fileToUpload" id="fileToUpload">
			<input type="submit" name="action" value="Calcola Gulpease">
		</form>
		</div>
	</div>	
</div>
END;
echo pageFooter();


?>