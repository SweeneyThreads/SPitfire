<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) {
	echo "non sei autorizzato";
	exit;
}

function pageHeader($title,$css="/stile.css") {
	$str='<!DOCTYPE html>
<html lang="it">
<head>
	<title>'.$title.'</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Documentazione prodotta per il corso Ingegneria del Software a.a 2015-2016">
	<meta name="keywords" content="Ingegneria del Software, Analisi dei requisiti, Progettazione, SweeneyThreads">
	<meta name="author" content="Bonato Paolo, Bortolazzo Matteo, Biggeri Mattia, Maino Elia, Nicoletti Luca, Padovan Tommaso, Tommasin Davide">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link href="http://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
	<link href="'.$css.'" rel="stylesheet" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">';
	return $str;
}

function pageFooter() {
	return "
</body>
</html>
	";
}


function navRuoli() {
	return '
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
				<li><a href="index.html" id="home">MENU PRINCIPALE</a></li>
				<li><a href="gest_ruoli.php" id="rotaz">ROTAZIONE RUOLI</a></li>
				<li><a href="parser.php" id="parser">PARSER</a></li>
			</ul>
		</div>
	</div>
</nav>
	';
}

function navRequisiti() {
	return '
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
				<li><a href="requisiti.php" id="rotaz">REQUISITI</a></li>
				<li><a href="milestones.php" id="parser">MILESTONES</a></li>
			</ul>
		</div>
	</div>
</nav>
	';
}


function navDocTools() {
	return '
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
				<li><a href="/menuPrincipale/documentation_tools/index.html" id="home">MENU PRINCIPALE</a></li>
				<li><a href="/menuPrincipale/documentation_tools/glossario.php" id="rotaz">TOOL GLOSSARIO</a></li>
				<li><a href="/menuPrincipale/documentation_tools/gulpease.php" id="parser">TOOL GULPEASE</a></li>
				<li><a href="/menuPrincipale/documentation_tools/sectionLength/form.php" id="parser">TOOL LUNGHEZZA SEZIONI</a></li>
				<li><a href="/menuPrincipale/documentation_tools/ourRepo/documenti_latex.php" id="parser">Tabella Repo</a></li>
			</ul>
		</div>
	</div>
</nav>
	';
}



function navDocTools2() {
	return '
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
				<li><a href="/menuPrincipale/" id="home">MENU PRINCIPALE</a></li>
				<li><a href="/menuPrincipale/documentation_tools/glossario.php" id="rotaz">TOOL GLOSSARIO</a></li>
				<li><a href="/menuPrincipale/documentation_tools/documenti_latex.php" id="parser">Tabella Repo</a></li>
				<li><a href="/menuPrincipale/documentation_tools/metric_calc.php" id="parser">Calcolatore Metriche</a></li>
			</ul>
		</div>
	</div>
</nav>
	';
}


##########################################################################################
function creaSelect($nome,$arrCoppie,$selected=NULL) {
	$str="<select name=\"$nome\">\n";
	foreach ($arrCoppie as $key => $value) {
		$str.="<option value=\"$key\"";
		if ($key==$selected) $str.=' selected="selected"';
		$str.=">$value</option>\n";
	}
	$str.="</select>";
	return $str;
}

function creaInputText($name,$text=NULL,$value=NULL) {
	$str= '<label>';
	if (isset($text)) $str.=$text;
	else $str.=$name;
	$str.='
		</label><br />
		<input type="text" class="form-control" name="'.$name.'" value="'.$value.'" /> 
	';
	return $str;
}
##########################################################################################
function tableStart($classeCss='table table-striped table-bordered') {
	return "<table class=\"$classeCss\">\n";
}

function tableHeading($arr) {
	$str="<tr>\n";
	foreach ($arr as $value) {
		$str.="<th>$value</th>\n";
	}
	$str.="</tr>\n";
	return $str;
}

function tableRow($arr) {
	$str="<tr>\n";
	foreach ($arr as $value) {
		$str.="<td>$value</td>\n";
	}
	$str.="</td>\n";
	return $str;
}

function tableEnd() {
	return "</table>\n";
}


?>