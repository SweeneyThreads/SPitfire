<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) {
	echo "non sei autorizzato";
	exit;
}




function contains($a, $b) {
	return stripos($a, $b) !== false;
}

function rimuoviRiga($righe, $i) {
	$righe[$i] = "";
	$i++;
}

function rimuoviHeader($righe, $i) {
	//Rimuovo tutto quello prima di \begin{document}
	while(!contains($righe[$i], '\begin{document}'))
		rimuoviRiga(&$righe, &$i);
	//Rimuovo \begin{document}
	rimuoviRiga(&$righe, &$i); 
	$j = $i;
	//Scorro il documento
	while(!contains($righe[$j], '\end{document}')) {   
		$j++;
	}    
	//Rimuovo end document    
	rimuoviRiga(&$righe, &$j);
}

function rimuoviComandi($riga) {
	$match = array();  
	//rimuovo comando latex
	$riga = str_replace('\LaTeX', 'LaTeX', $riga);
	//rimuovo i commenti
	$pattern = '/^%.*/';
	if(preg_match($pattern, trim($riga))) {
		$riga = "";
	}  
	//rimuovo i verb mantenendo il contenuto
	$pattern = '/\\\\verb\|(.*?)\|/';
	while(preg_match($pattern, $riga, $match)) {
		$riga = str_replace($match[0], $match[1], $riga);            
	}
	$riga = str_replace('\verb', '', $riga);
	//rimuovo i comandi senza paramentro
	$comandiSenzaParametro = array('\newpage', '\tableofcontents', 
		'\cleardoublepage', '\listoffigures', '\cleardoublepage', 
		'\listoftables', '\centering', '\justify');
	foreach ($comandiSenzaParametro as $comando) {
		if(contains($riga, $comando))
			$riga = "";
	}
	//rimuovo il comando tentendo il contenuto
	$comandiTenere = array('emph', 'textbf', 'underline', 'textit', );
	foreach ($comandiTenere as $comando) {        
		$pattern = '/\\\\'.$comando.'{(.*?)}/';
		while(preg_match($pattern, $riga, $match)) {
			$riga = str_replace($match[0], $match[1], $riga);            
		}
	} 
	//se il comando è tra || non lo elimino altrimenti si
	$pattern = '/\\\\.*{.*?}/';
	while(preg_match($pattern, $riga, $match)) {        
		$riga = str_replace($match[0], "", $riga);            
	}             
	//rimuovo le doppie barre 
	$pattern = '/\\\\\\\\/';
	while(preg_match($pattern, $riga, $match)) {
		$riga = str_replace($match[0], "", $riga);            
	}       
}

function rimuoviContenutoBeginEnd($righe, $i) {
	$matches = array();
	$count = 0;
	//Se c'è un \begin ma non un \end nella stessa riga
	if(preg_match('/.*begin{(.+?)}.*/', $righe[$i], $matches) && !preg_match('/.*end{(.+?)}.*/', $righe[$i])) {
		rimuoviRiga(&$righe, &$i); 
		$count++;
		//prendo il valore all'interno delle parentesi
		$parametroComando = $matches[1]; 
		//gestisco i begin dello stesso tipo annidati
		while($count > 0) {
			$begin = '/.*begin{'.$parametroComando.'}.*/';
			$end = '/.*end{'.$parametroComando.'}.*/';
			if(preg_match($begin, $righe[$i])) 
				$count++;
			else if(preg_match($end, $righe[$i]))
				$count--;
			rimuoviRiga(&$righe, &$i);          
		}  
	}  
	else {
		rimuoviComandi(&$righe[$i]);         
		$i++;
   }  
}

function tieniContenutoBeginEnd($righe, $i, $array) {
	$matches = array();
	foreach ($array as $comando) {
		$j = $i;
		$count = 0;
		//Se c'è un \begin 
		if(preg_match('/.*begin{'.$comando.'}.*/', $righe[$j], $matches)) {
			rimuoviRiga(&$righe, &$j); 
			$count++;
			//gestisco i begin dello stesso tipo annidati
			while($count > 0) {
				$begin = '/.*begin{'.$comando.'}.*/';
				$end = '/.*end{'.$comando.'}.*/';
				if(preg_match($begin, $righe[$j])) {
					$count++;                
					rimuoviRiga(&$righe, &$j); 
				}
				else if(preg_match($end, $righe[$j])) {
					$count--;  
					rimuoviRiga(&$righe, &$j);  
				}
				else $j++;               
			}          
		}
	}    
	$i++;
}

function rimuoviItemize($righe, $i) {
	$count = 0;
	//Se c'è un \begin ma non un \end nella stessa riga
	if(preg_match('/.*begin{itemize}.*/', $righe[$i]) && !preg_match('/.*end{itemize}.*/', $righe[$i])) {
		rimuoviRiga(&$righe, &$i); 
		$count++;
		//gestisco i begin dello stesso tipo annidati
		while($count > 0) {
			$begin = '/.*begin{itemize}.*/';
			$end = '/.*end{itemize}.*/';
			if(preg_match($begin, $righe[$i])) {
				$count++;
				rimuoviRiga(&$righe, &$i); 
			}
			else if(preg_match($end, $righe[$i])) {
				$count--;  
				rimuoviRiga(&$righe, &$i);  
			}
			else {
				$righe[$i] = str_replace('\item', '', $righe[$i]);
				$i++;
			}      
		}  
	}  
	else { 
		$i++;
	}  
}

function rimuoviBegin($righe, $i) {    
	//tiene il contenuto dei begin
	$j = $i;
	while($j < count($righe)) {
		$beginEndDaTenere = array('center', 'flushright', 'flushleft');
		tieniContenutoBeginEnd(&$righe, &$j, $beginEndDaTenere);
	}
	//tiene gli item dell'itemize
	$j = $i;
	while($j < count($righe)) {
		rimuoviItemize(&$righe, &$j);
	}
	//elimina il resto dei begin e dei comandi
	$j = $i;
	while($j < count($righe)) {
		rimuoviContenutoBeginEnd(&$righe, &$j);   
	}
}

function trimLatex($str) {         
	$i = 0;
	//rimuovo i pedici del glossario
	$str=str_replace('\ped{\emph{G}}', '', $str);
	$righe=explode(PHP_EOL, $str);
	//rimuovo la parte iniziale
	rimuoviHeader(&$righe, &$i);	    
	//rimuovo i begin-end e i comandi
	rimuoviBegin(&$righe, &$i);
	$righe=implode(PHP_EOL, $righe); 
	return $righe;
}



/**
* questa funzione toglie i comandi latex, di una parte del documento, insomma, ignora il togliere la \begin
*/
function trimLatexPartial($str) {         
	$i = 0;
	//rimuovo i pedici del glossario
	$str=str_replace('\ped{\emph{G}}', '', $str);
	$righe=explode(PHP_EOL, $str);
	//rimuovo i begin-end e i comandi
	foreach ($righe as &$value) {
		$value=rimuoviComandi($value);
	}
	$righe=implode(PHP_EOL, $righe); 
	return $righe;
}

function calculate_Nfrasi($str) {
	$str=explode('.', $str);
	return count($str);
}
function calculate_Nparole($str) {
	$str=explode(' ', $str);
	return count($str);
}
function calculate_Nlettere($str) {
	$str=str_replace(' ', '', $str);
	$str=str_replace('.', '', $str);
	$str=str_replace(',', '', $str);
	$str=str_replace(';', '', $str);
	$str=str_replace(':', '', $str);
	$str=str_replace('(', '', $str);
	$str=str_replace(')', '', $str);
	return strlen($str);
}



function delete_all_between($beginning, $end, $string) {
  $beginningPos = strpos($string, $beginning);
  $endPos = strpos($string, $end);
  if ($beginningPos === false || $endPos === false) {
	return $string;
  }

  $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)+1) - $beginningPos);

  return str_replace($textToDelete, '', $string);
}



function stripFirstLine($text)
{        
  return substr( $text, strpos($text, "\n")+1 );
}


?>