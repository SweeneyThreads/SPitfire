<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();
echo pageHeader('Requisiti');

echo <<<END

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
	<h2>Inserisci un nuovo requisito</h2>
	<form class="form-horizontal" role="form" method="POST" action="process_addrequirement.php">
		<div class="form-group">
			<label class="control-label col-sm-2" for="ID">ID:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="ID" name="ID"  value="R[][][]">
			</div>
		</div>
		<!--
		<div class="form-group">
			<label class="control-label col-sm-2" for="Versione">Versione:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="Versione" name="Versione"  placeholder="Inserisci la versione del requisito">
			</div>
		</div>
		-->
		<div class="form-group">
			<label class="control-label col-sm-2" for="Fonte">Fonte:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="Fonte" name="Fonte"  placeholder="Inserisci la fonte del requisito">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="Nome">Nome:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="Nome" name="Nome"  placeholder="Inserisci il nome del requisito">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="Descrizione">Descrizione:</label>
			<div class="col-sm-10">
				<textarea class="form-control" rows="5" id="Descrizione" name="Descrizione"></textarea>
			</div>
		</div>

		<!--
		<div class="form-group">
			<label class="control-label col-sm-2" for="Milestone">Milestone:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="Milestone" name="Milestone"  placeholder="Inserisci la milestone a cui il requisito è associato">
			</div>
		</div>
		-->
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Inserisci</button>
			</div>
		</div>
	</form>
</div>


END;
echo pageFooter();
?>