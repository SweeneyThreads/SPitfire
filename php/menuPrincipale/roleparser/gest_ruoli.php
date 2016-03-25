<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php&thenbackto=roleparser%2Fgest_ruoli.php");
require_once 'htmlgenerator.php';
require_once 'impostazioni.php';
require_once 'phplib.php';
$conn=connect();


echo pageHeader('Rotazione Ruoli | Rouli e Parser');
echo navRuoli();





#form per impostare il nuovo set di ruoli
function getNomixRuoli($nome,$arrRouli) {
	$aux=array();
	foreach ($arrRouli as $value) {
		$aux["$value($nome)"]="$value($nome)";
	}
	return $aux;
}


$result=mysql_query('select distinct dataInizio from Sostituzioni order by dataInizio',$conn);
$arrRighe=array();

$lastData='';
$lastRuoli=array();
for ($i=0; $i<mysql_num_rows($result) ; $i++) {
	array_push($arrRighe, array()); 
	$row=mysql_fetch_assoc($result);
	array_push($arrRighe[$i], $row['dataInizio']);
	$lastData=$row['dataInizio'];

	$query="select * from Sostituzioni where dataInizio='".$row['dataInizio']."'";
	$sqlSost=mysql_query($query,$conn);
	$str='';
	for ($j=0; $j<mysql_num_rows($sqlSost) ; $j++) {
		$row2=mysql_fetch_assoc($sqlSost);
		$str.=$row2['nome'].' => '.$row2['sostituto'].'<br />';
		$lastRuoli[$row2['nome']]=$row2['sostituto'];
	}
	array_push($arrRighe[$i], $str);
}





echo <<<END
	<div id="page-wrap">
		<div class="main">
			<section class="article">
				<h3>Nuova rotazione</h3>
			</section>
		</div>
	</div>


	<form action="process_addrotation.php" method="POST">
		data inizio:<br />
		<input type="date" name="dataInizio" value=$lastData min="$lastData"/>
		<table>
			<tr>
				<th>$nomi[0]</th>
				<th>$nomi[1]</th>
				<th>$nomi[2]</th>
				<th>$nomi[3]</th>
				<th>$nomi[4]</th>
				<th>$nomi[5]</th>
				<th>$nomi[6]</th>
			</tr><tr>
END;


foreach ($nomi as $value) {
	$strSelect=creaSelect($value,getNomixRuoli($value,$ruoli),$lastRuoli[$value]);
	echo "<td>$strSelect</td>\n";
}

echo <<<END
			</tr>
		</table>
		<input type="submit" value="submit" />
	</form>
	<hr />

END;




echo <<<END
	<div id="page-wrap">
		<div class="main">
			<section class="article">
				<h3>Tabella rotazioni</h3>
			</section>
		</div>
	</div>
END;

echo '<div class="CSS_Table_Example" style="width:600px;height:150px;">';
echo tableStart();

echo tableHeading(array('Data Inizio','Sost.'));

foreach ($arrRighe as $value) {
	echo tableRow($value);
}

echo tableEnd();
echo '</div>';

echo pageFooter();
?>