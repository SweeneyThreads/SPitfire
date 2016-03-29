<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) {
	header("Location: ../../login.php");
}

require_once("infoRepo.php");		//il file con le informazioni riguardanti le posizione dei file all'interno della repository





class RequirementsSystem {
	private $arrRequisiti;
	private $arrParentele;

	private function __construct($arrRequisiti,$arrParentele) {
		$this->arrRequisiti=$arrRequisiti;
		$this->arrParentele=$arrParentele;
	}


	public static function getInstanceFromDB($conn,$tabRequisiti='Requisiti',$tabParentele='derivazRequisiti') {
		$arrParentele=array();
		$result=mysql_query("SELECT * FROM $tabParentele",$conn);
		for ($i=0; $i<mysql_num_rows($result) ; $i++) {
			$row=mysql_fetch_assoc($result);
			$arrParentele[$row['padre']]=$row['figlio'];
		}

		$arrRequisiti=array();
		$result=mysql_query("SELECT * FROM $tabRequisiti",$conn);
		for ($i=0; $i<mysql_num_rows($result) ; $i++) {
			$row=mysql_fetch_assoc($result);
			$arrRequisiti[$row['ID']] = new Requisito($row);
		}

		return new RequirementsSystem($arrRequisiti,$arrParentele);
	}


	public static function getInstanceFromLatex($arrRepo) {

		$arrParentele=array();
		foreach ($arrRequisiti as $idPadre => $padre) {
			$codicePadre=$padre->GetCodice();
			$livelloRequisito=count($codicePadre);
			foreach ($arrRequisiti as $idFiglio => $figlio) {
				$codiceFiglio=$figlio->GetCodice();
				if (count($codiceFiglio)==$livelloRequisito+1) {
					$ok=true;
					for ($i=0; $i < $livelloRequisito; $i++) { 
						if ($codicePadre[$i]!=$codiceFiglio[$i]) $ok=false;
					}
					if ($ok) $arrParentele[$idPadre]=$idFiglio;
				}
			}
		}
		
		$arrRequisiti=array();
		foreach ($arrRepo as $nomeDoc => $urlDoc) {
			//carico il documento e lo salvo in $text
			$curlSession = curl_init();
			curl_setopt($curlSession, CURLOPT_URL, $urlDoc);
			curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
			curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
			$text = utf8_decode(curl_exec($curlSession));
			curl_close($curlSession);


			//tolgo testa e coda
			$text=explode('\endhead', $text);
			$text=$text[1];
			if (strpos($text, '\bottomrule') !== false) {
				$text=explode('\bottomrule', $text);
				$text=$text[0];
			}
			//tolgo tutta la formattazione e le altre cose inutili allo scopo
			$text=str_replace("\t", '', $text);

			$text=explode(PHP_EOL, $text);
			foreach ($text as $key => $value) {
				$value=trim($value);
				if ($value[0]=='%') unset($text[$key]);
			}
			$text=implode(PHP_EOL, $text);

			$text=str_replace(PHP_EOL, '', $text);
			$text=str_replace("\\\\", '', $text);
			$text=str_replace("'", "\\'", $text);

			//divido per ogni requisito e poi per ogni campo del requisito
			$text=explode('\hline', trim($text));
			foreach ($text as $value) {
				$value=trim($value);
				$arr=explode('&', $value);

				if (count($arr)==4){
					foreach ($arr as &$v) {
						$v=trim($v);
						$v=utf8_encode($v);
					}
					$arrRequisiti[$arr[0]]=Requisito::fromArguments($arr[0],$arr[1],$arr[3],$arr[2]);
				}
			}
		}


		return new RequirementsSystem($arrRequisiti,$arrParentele);
	}



	public function getRequisiti() {return $this->arrRequisiti;}

	public function getParentele() {return $this->arrParentele;}


	public function addRequirement($r,$conn) {
		if (!in_array($r->GetID(), array_keys($this->arrRequisiti))) {
			$this->arrRequisiti[$r->GetID()]=$r;
			$r=$r->GetAll();
			$id = $r['ID'];
			$nome = $r['Nome'];
			$descr = $r['Descrizione'];
			$fonte = $r['Fonte'];
			$note = $r['Note'];
			$query="INSERT INTO Requisiti(ID,Nome,Descrizione,Fonte,Note) VALUES('$id','$nome','$descr','$fonte','$note')";
			$result=mysql_query($query,$conn);
		}

	}


	public static function resetDependencies($conn) {
		$query="SELECT * FROM Requisiti";
		$result=mysql_query($query,$conn)
			or die("Inserimento nella tabella requisiti fallito" . mysql_error($conn));


		$arrRequisiti=array();
		for ($i=0; $i<mysql_num_rows($result) ; $i++) {
			$row=mysql_fetch_assoc($result);
			$arrRequisiti[$row['ID']]=$row['ID'];
		}



		foreach ($arrRequisiti as $key => &$requisito) {
			//ricavo il codice numerio del requisito
			$requisito=str_replace(' ', '', $requisito);
			$requisito=explode(']', $requisito);
			$requisito=$requisito[0];
			$requisito=trim($requisito);
			$requisito=explode('.', $requisito);
		}
		//svuoto la tabella
		$result=mysql_query("DELETE FROM derivazRequisiti WHERE 1");




		foreach ($arrRequisiti as $idPadre => $codicePadre) {
			$livelloRequisito=count($codicePadre);
			foreach ($arrRequisiti as $idFiglio => $codiceFiglio) {
				if (count($codiceFiglio)==$livelloRequisito+1) {
					$ok=true;
					for ($i=0; $i < $livelloRequisito; $i++) { 
						if ($codicePadre[$i]!=$codiceFiglio[$i]) $ok=false;
					}
					if ($ok) {
						$query="INSERT INTO derivazRequisiti(padre,figlio) VALUES('$idPadre','$idFiglio')";
						$result=mysql_query($query);
					}
				}
			}
		}

	}




	private static function contiene($needle,$haystack) {
		if (strpos($haystack, $needle) !== false && $haystack!=$needle) return true;
		return false;
	}
}








class Requisito {
	private $ID;
	private $Nome;
	private $Descrizione;
	private $Fonte;
	private $completato;
	private $Note;



	public function __construct($arr) {
		$this->ID=$arr['ID'];
		$this->Nome=$arr['Nome'];
		$this->Descrizione=$arr['Descrizione'];
		$this->Fonte=$arr['Fonte'];
		$this->completato=$arr['completato'];
		$this->Note=$arr['Note'];
	}

	public static function fromArguments($ID,$Nome,$Descrizione,$Fonte,$completato=0,$Note='') {
		$arr=array(
			'ID' => $ID,
			'Nome' => $Nome,
			'Descrizione' => $Descrizione,
			'Fonte' => $Fonte,
			'completato' => $completato,
			'Note'	 => $Note
		);
		return new Requisito($arr);
	}


	public function GetID() {return $this->ID;}
	public function GetNome() {return $this->Nome;}
	public function GetDescrizione() {return $this->Descrizione;}
	public function GetFonte() {return $this->Fonte;}
	public function Getcompletato() {return $this->completato;}
	public function GetNote() {return $this->Note;}
	public function GetAll() {
		return array(
			'ID' => $this->GetID(),
			'Nome' => $this->GetNome(),
			'Descrizione' => $this->GetDescrizione(),
			'Fonte' => $this->GetFonte(),
			'completato' => $this->Getcompletato(),
			'Note' => $this->GetNote()
		);
	}

	public function SetID($ID) {$this->ID=$ID;}
	public function SetNome($Nome) {$this->Nome=$Nome;}
	public function SetDescrizione($Descrizione) {$this->Descrizione=$Descrizione;}
	public function SetFonte($Fonte) {$this->Fonte=$Fonte;}
	public function Setcompletato($completato) {$this->completato=$completato;}
	public function SetNote($Note) {$this->Note=$Note;}
	public function SetAll($arr) {
		$this->ID=$arr['ID'];
		$this->Nome=$arr['Nome'];
		$this->Descrizione=$arr['Descrizione'];
		$this->Fonte=$arr['Fonte'];
		$this->completato=$arr['completato'];
		$this->Note	=$arr['Note'];
	}

	/**
	* Ritorna il codice numerico del requisito.
	*
	* Specificamente lo ritorna in un array ad una sola dimensione ordinato gerarchicamente: il primo numero dell'array è il numero del sul array
	* radice, il secondo del padre di secondo livello e così via
	*
	* @return array
	*/
	public function GetCodice() {
		$requisito=str_replace(' ', '', $this->ID);
		$requisito=explode(']', $requisito);
		$requisito=$requisito[0];
		$requisito=trim($requisito);
		$requisito=explode('.', $requisito);
		return $requisito;
	}
	

	public function toLatex() {
		$tex=utf8_decode($this->ID.' & '.$this->Nome.' & '.$this->Fonte.' & '.$this->Descrizione.'\\\\'."\n");
		$tex.='\hline'."\n";
		return utf8_encode($tex);
	}



	public function isEqual($r) {
		return (
			$this->GetID() == $r->GetID() &&
			$this->GetNome() == $r->GetNome() &&
			$this->GetDescrizione() == $r->GetDescrizione() &&
			$this->GetFonte() == $r->GetFonte()
		);
	}


	public function differenze($r) {
		if ($this->isEqual($r)) return NULL;
		$arrDiff=array(
			'ID' => '',
			'Nome' => '',
			'Descrizione' => '',
			'Fonte' => ''
		);
		$arrDiff['ID']=$this->GetID();
		if ($this->GetNome()!=$r->GetNome())
			$arrDiff['Nome']=$this->GetNome().'<br /> ----------- <br />'.$r->GetNome();
		if ($this->GetDescrizione()!=$r->GetDescrizione())
			$arrDiff['Descrizione']=$this->GetDescrizione().'<br /> ----------- <br />'.$r->GetDescrizione();
		if ($this->GetFonte()!=$r->GetFonte())
			$arrDiff['Fonte']=$this->GetFonte().'<br /> ----------- <br />'.$r->GetFonte();
		return $arrDiff;
	}



}


?>