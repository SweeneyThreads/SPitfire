<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../../login.php");

require_once("../../lib/htmlgenerator.php");
require_once("../../lib/phplib.php");
//$conn=connect(); 						<------------------------------ i think we dont need connection here

echo pageHeader('Tool Lunghezza Sezioni','/stile.css');
echo navDocTools();


echo <<< END
<div id="descrizioneTool" class="container-fluid ">
	<div class="row text-center">
		<div class="col-sm-12">
			<h2>TOOL GLOSSARIO</h2>
			<p>
				Questo tool calcola la lunghezza media dei paragrafi del file latex inserito
			</p>
		</div>
	</div>
	<div class="row text-center">
	<div class="col-sm-12">
		<h3>Copia qui il testo latex</h3>
		<p>Inserisci il testo latex di cui vuoi calcolare la lunghezza media dei paragrafi</p>
		<form method="post" action="process_elaborate.php">
			<textarea rows="20" cols="150" name="texFile">

			</textarea>
			<input type="submit" value="Calcola">
		</form>
		</div>
	</div>

</div>

END;



echo pageFooter();
?>
