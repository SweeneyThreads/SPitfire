<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php&thenbackto=roleparser%parser.php");
require_once 'htmlgenerator.php';
require_once 'impostazioni.php';
require_once 'phplib.php';
$conn=connect();


echo pageHeader('Post Burner | Rouli e Parser');
echo navRuoli();


$target_dir = "uploads/";
$target_file = $target_dir . "Actorbase.xml";
$uploadOk = 1;

if(isset($_POST["submit"])) {
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
			header("Location: process_downloadpased.php");
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
}



//$xml=simplexml_load_file("uploads/Actorbase.xml") or die ("sbubba");




echo <<<END
		<div id="page-wrap">
			<div class="main">
				<section class="article">
					<h3>Post Burner</h3>
					<div class="row clearfix">
						<div class="col2">
							<p>
								Post Burner &egrave un tool sviluppato da Tommaso Padovan
								per parsare l'xml generato da Teamwork e risistemare ruoi e
								assegnazione degli stessi.
							</p>
						</div>
					</div>
				</section>
			</div>
			
			<hr />

			<form action="parser.php" method="post" enctype="multipart/form-data">
				Select file to upload:
				<input type="file" name="fileToUpload" id="fileToUpload">
				<input type="submit" value="BURRRRRRN" name="submit">
			</form>

		</div>
END;




echo pageFooter();
?>