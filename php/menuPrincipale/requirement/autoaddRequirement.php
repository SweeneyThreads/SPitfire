<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();
echo pageHeader('AutoAdd');



echo <<<THE_END

<nav class="navbar navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>                        
			</button>
		</div>
		<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="navbar-nav navbar-right">
				<li><a href=".." id="home">MENU PRINCIPALE</a></li>
				<li><a href="aggiungiRequisito.php" id="rotaz">Inserisci un Requisito</a></li>
				<li><a href="requisiti.php" id="rotaz">Requisiti</a></li>
				<li><a href="autoaddRequirement.php" id="parser">Latex to DB</a></li>
				<li><a href="autogenerateLatex.php" id="parser">DB to Latex</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container"><p>
</br></br></p>
	<h2>Inserisci la sezione di testo LaTeX contenente la specifica dei requisiti</h2>
	<div>
		Alcune semplici regole per fare in modo che tutto funga:
		<ul>
			<li>Si aspetta di ricevere in input il contenuto dei file nella cartella <code>Actorbase\\LaTeX\\tabelle_requisiti</code></li>
			<li>Inserire qui sotto il testo LaTeX intero, oppure solo la parte di esso coi requisiti che si vogliono inserire</li>
			<li><strong>Testo intero:</strong> i requisiti considerati devono stare <em>immediatamente</em> dopo <code>\endhead</code> ed <em>immediatamente</em> prima di <code>\bottomrule</code></li>
			<li><strong>Solo requisiti:</strong> va benissimo lo stesso. Basta che tutti i requisiti siano scritti secondo il template solito (ovvero con un <code>\hline</code> tra un requisito e l'altro)</li>
			<li>Remember: se uno solo dei requisiti che ci sono nel LaTeX ha codice uguale ad uno che è già presente in DB  <strong>va tutto a troie</strong> e si blocca (non &egrave una funzione di integrazione continua,
				ha il solo scopo di importare la prima volta tutti i requisiti e poi non toccare più)</li>
			<li>Il bottone in alto per cancellare, cancella tutto, <a href="https://www.youtube.com/watch?v=ElwersjlIxk">brutalmente.</a>
				Sia i requisiti salvati in DB (stato completato/non completato compreso) sia le loro parentele. Occhio.</li>
			<li>Il link sotto, autogenera le parentele tra requisiti partendo dal codice identificativo. Ogni volta che lo si preme cancella tutte le vecchie parentele e rifà da capo.</li>
		</ul>
	</div>
	<form action="process_cancellaTutto.php">
		<input type="submit" value="Cancellazione totale Brutal-Grindcore ------ OCCHIO">
	</form>

	<br />

	<form class="form-horizontal" role="form" method="POST" action="process_texToDB.php">
		<div class="form-group">
			<label class="control-label col-sm-2" for="Descrizione">Testo tex:</label>
			<div class="col-sm-10">
				<textarea class="form-control" rows="25" id="Descrizione" name="tex"></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Inserisci</button>
			</div>
		</div>
	</form>

	<p>Clicca questo link per reimpostare le dipendenze padre/figlio nell'albero dei requisiti (basandosi sul codice gerarchico nell'ID)</p>
	<a href="process_resetDependencies.php">Reset dependencies</a>
</div>

THE_END;



?>