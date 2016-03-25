<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/htmlgenerator.php");
require_once("impostazioni.php");
require_once("../lib/phplib.php");
$conn=connect();


$Project = simplexml_load_file('uploads/Actorbase.xml');

$namespaces = $Project->getNamespaces(true);



#cerco l'ID massimo.
$maxID=0;
$startingUID=200001;
foreach ($Project->Resources->Resource as $Resource) {
	$ID=$Resource->ID;
	$ID=(int)$ID;
	if ($maxID<$ID) $maxID=$ID;
}

#COSTRUISCO L'ARRAY CON I NOSTRI UID E NOMI (nome=>UID)
$arrayNoi7=array();
foreach ($Project->Resources->Resource as $Resource) {
	$nome=$Resource->Name;
	$nome=(string)$nome;
	$UID=$Resource->UID;
	$UID=(int)$UID;
	$arrayNoi7[$nome]=$UID;
}

#genero l'array di tutte le possibili (nuove) risorse da inserire e relativo UID (ruolo(nome)=>UID)
$allReso=array();
#$ruoli e $nomi sono presi dai file esterno impostazioni.php
foreach ($ruoli as $ruolo) {
	foreach ($nomi as $nome) {
		$RuoloNome=$ruolo.'('.$nome.')';
		$allReso[$RuoloNome]=$startingUID;
		$startingUID+=1;
	}
}



/**
* INSERIMENTO NUOVI RUOLI
* @todo Padovan non ha capito molto bene alcuni valori, riguardare se qualcuno capisce questo xml
*/
foreach ($allReso as $RuoloNome => $UID) {
	$Project->Resources->addChild('Resource');
}

$i=$maxID;
foreach ($allReso as $RuoloNome => $UID) {
	$i+=1;	
	$Project->Resources->Resource[$i]->addChild('UID',$UID);
	$Project->Resources->Resource[$i]->addChild('ID',$i);
	$Project->Resources->Resource[$i]->addChild('Name',$RuoloNome);
	$Project->Resources->Resource[$i]->addChild('Type',1);
	$Project->Resources->Resource[$i]->addChild('IsNull',0);
	$Project->Resources->Resource[$i]->addChild('Initials',substr($RuoloNome, 0, 1));
	$Project->Resources->Resource[$i]->addChild('Group');
	$Project->Resources->Resource[$i]->addChild('EmailAddress');
	$Project->Resources->Resource[$i]->addChild('MaxUnits',1);
	$Project->Resources->Resource[$i]->addChild('PeakUnits',1);
	$Project->Resources->Resource[$i]->addChild('OverAllocated',0);
	$Project->Resources->Resource[$i]->addChild('Start','2016-01-13T08:00:00');
	$Project->Resources->Resource[$i]->addChild('Finish','2016-01-26T17:00:00');
	$Project->Resources->Resource[$i]->addChild('CanLevel',0);
	$Project->Resources->Resource[$i]->addChild('AccrueAt',3);

	#da $RuoloNome ricavo il ruolo (lo so che è bruttissimo ma è una modifica postuma e non ho voglia di fare diversamente)
	$ruolo=explode('(', $RuoloNome);
	$ruolo=$ruolo[0];
	$Project->Resources->Resource[$i]->addChild('StandardRate',$stdRate[$ruolo]);

	$Project->Resources->Resource[$i]->addChild('StandardRateFormat',3);
	$Project->Resources->Resource[$i]->addChild('OvertimeRateFormat',3);
	$Project->Resources->Resource[$i]->addChild('CalendarUID',16);
	$Project->Resources->Resource[$i]->addChild('IsGeneric',0);
	$Project->Resources->Resource[$i]->addChild('IsInactive',0);
	$Project->Resources->Resource[$i]->addChild('IsEnterprise',0);
	$Project->Resources->Resource[$i]->addChild('IsBudget',0);
	$Project->Resources->Resource[$i]->addChild('AvailabilityPeriods');
}







/**
* A SECONDA DELLA DATA SCELGO COSA RIMPIAZZARE
*/
#costruisco un array del genere:
#data => {
#	nome1=>nomeruolo1
#	nome2=>nomeruolo2
# 	....
#}
# data => {}
# .......
$arrSostituzioni=array();
$result=mysql_query('select distinct dataInizio from Sostituzioni order by dataInizio',$conn);
for ($i=0; $i<mysql_num_rows($result) ; $i++) {
	$row=mysql_fetch_assoc($result);

	$query="select * from Sostituzioni where dataInizio='".$row['dataInizio']."'";
	$sqlSost=mysql_query($query,$conn);
	$arrTemp=array();
	for ($j=0; $j<mysql_num_rows($sqlSost) ; $j++) {
		$row2=mysql_fetch_assoc($sqlSost);
		$arrTemp[$row2['nome']]=$row2['sostituto'];
	}
	
	$arrSostituzioni[$row['dataInizio']] = $arrTemp;
}



#ora sostituisco tutti i nomi con i loro codici
$arrSostCodici=array();
foreach ($arrSostituzioni as $key => $value) {
	$arrSostCodici[$key]=array();
	foreach ($value as $nome => $nomeruolo) {
		$arrSostCodici[$key][$arrayNoi7[$corrispNomeBreveToCompleto[$nome]]]=$allReso[$nomeruolo];
	}
}

/*
stampa una merda di tabella con $arrSostituzioni e $arrSostCodici
echo '<table><tr><td>';
echo '<pre>';
var_dump($arrSostituzioni);
echo '</pre></td>';
echo '<td><pre>';
var_dump($arrSostCodici);
echo '</pre></td></tr></table>';
*/


#cerco il massimo Assignment


foreach ($Project->Assignments->Assignment as $Assignment) {
	$resUID=$Assignment->ResourceUID;
	$resUID=(int)$resUID;
	if (in_array($resUID, $arrayNoi7)) {	//il codice è valido, corrisponde ad un membro del gruppo
		$data=$Assignment->Start;	//ricavo la data di inizio, solo giorno e la salvo in $data
		$data=(string)$data;
		$data=explode('T', $data);
		$data=$data[0];
		//var_dump($data); echo '<br />';

		#cerco la data migliore all'interno di $arrSostCodici, quella immediatamente più piccola della data di start
		$dataMigliore=('0000-00-00');
		foreach ($arrSostCodici as $d => $arrSost) {
			if (is_maggiore($d,$dataMigliore) and is_maggiore($data,$d)) {
				$dataMigliore=$d;
			}
		}

		if ( isset($arrSostCodici[$dataMigliore][$resUID]) ) {
			$sostituto=$arrSostCodici[$dataMigliore][$resUID];
			$Assignment->ResourceUID=$sostituto;
		}
	}
}

function is_maggiore($a,$b) {
	$a=explode('-', $a);
	$b=explode('-', $b);
	if ($a[0]>$b[0]) return true;
	elseif ($a[0]<$b[0]) return false;
	else {
		if ($a[1]>$b[1]) return true;
		elseif ($a[1]<$b[1]) return false;
		else {
			if ($a[2]>=$b[2]) return true;
			else return false;
		}
	}
}


/*
$maxAS=0;
foreach ($Project->Assignments->Assignment as $Assignment) {
	$maxAS=$Assignment->TaskUID;
}
for ($i=0; $i <$maxAS; $i++) { 
	$resUID=$Project->Assignments->Assignment[$i]->ResourceUID;
	$resUID=(int)$resUID;
	if (in_array($resUID, $arrayNoi7)) {
		$data=$Project->Assignments->Assignment[$i]->Start;
		$data=(string)$data;
		$data=explode('T', $data);
		$data=$data[0];
	}

	#ora cerco la più grande data all'interno di $arrSostCodici minore di $data
	$dataUtile='0000-00-00';
	foreach ($arrSostCodici as $key => $value) {
		if (strtotime($key)<strtotime($data)) {
			$dataUtile=$key;
		}
	}
	$sostituto=$arrSostCodici[$dataUtile][$resUID];
	$Project->Assignments->Assignment[$i]->ResourceUID=$sostituto;
	

}*/






header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=actorbaseParsato.xml");
print $Project->asXML();

/*
$Project->asXML();
echo '<pre>';
print_r($Project);
echo '</pre>';
*/













//echo '</pre>';


/*
$Project->Name="porcodio e tutti i santi";
$Project->Resources->Resource[0]->Name='canedidio';

$culo=$Project->Resources->children();
print_r($culo->Resource[1]);
foreach ($culo as $key => $value) {
	echo $key->key();
}


echo '<br/><br/>';
$Project->asXML();
print_r($Project);
*/


/*
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=savethis.txt");

// do your Db stuff here to get the content into $content
print "This is some text...\n";
print $content;
*/



?>