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
	<form class="form-horizontal" role="form" method="POST" action="autogenerateLatex.php?gee=cool">

		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">GO</button>
			</div>
		</div>
	</form>
</div>

THE_END;

if (isset($_GET['gee'])) {
	echo '<body id="myPage" data-spy="scroll" data-offset="60"><div id="descrizioneTool" class="container-fluid">';
	echo '<pre>';
	
	$query="SELECT * FROM Requisiti";
	$result=mysql_query($query,$conn)
		or die("Inserimento nella tabella requisiti fallito" . mysql_error($conn));


	$tex='';
	for ($i=0; $i<mysql_num_rows($result) ; $i++) {
		$row=mysql_fetch_assoc($result);
		$tex.=utf8_decode($row['ID'].' & '.$row['Nome'].' & '.$row['Fonte'].' & '.$row['Descrizione'].'\\\\'."\n");
		$tex.='\hline'."\n";
	}

	echo utf8_encode($tex);


	echo '</pre>';
	echo '</div></body>';
}


?>