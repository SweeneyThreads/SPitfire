<?php

function contains($a, $b) {
    return strpos($a, $b) !== false;
}

function mettiLeG($str, $arrTermini) {  
    foreach ($arrTermini as $termine) { 
        $match = array(); 
        $pattern = "/(\s|\\'|\(|\[|\\\emph{)(".$termine.")(\s|\.|,|!|\?|:|,|\\'|\)|\]|\})/i";             
        if(preg_match($pattern, $str, $match)) {  
            $str = str_replace($match[0], $match[1].$match[2]."\ped{\emph{G}}".$match[3], $str);
        }
    }
}

function creaParolaGlossario() {  
    $readme = $text=utf8_decode(file_get_contents('https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/README.md'));
    $termini = array();
    echo $readme;
    /*$text=explode(PHP_EOL, $text);
    $i = 0;
    while($i != count($text)) {*/
        //$patten = "/\*\s\*\*(.*?):\*\*/i";
        /*$match = $i.'<br/>';
        if(preg_match($pattern, $text[$i], $match)) {  
            echo $match[1].'<br/>';
            array_push($termini, $match[1]);
        }
        $i++;
    }*/
    return $termini;
}

session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();

$arrTermini = creaParolaGlossario();
/*$result=mysql_query('select * from TerminiGlossario',$conn);
for ($i=0; $i<mysql_num_rows($result) ; $i++) {
	$row=mysql_fetch_assoc($result);
	if ($row['nome']!='') array_push($arrTermini, $row['nome']);
}*/

//prendo il file caricato, lo salvi in utf8 dentro la variabile $text
$text=utf8_decode(file_get_contents('uploads/Tex'));
//elimino tutte le pedici
$text=str_replace('\ped{\emph{G}}', '', $text);

//splitto le singole parole
$text=explode(PHP_EOL, $text);
$i = 0;
while(!contains($text[$i], "\begin{document}")) {
    $i++;
}
$i++;
while($i != count($text)) {
    //Salta titlepage
    if(contains($text[$i], "\begin{titlepage}")) {
        while(!contains($text[$i], "\end{titlepage}"))
            $i++;
        $i++;
    }
    //Salta table
    else if(contains($text[$i], "\begin{table}")) {
        while(!contains($text[$i], "\end{table}"))
            $i++;
        $i++;
    }
    //Salta immagini
    else if(contains($text[$i], "\begin{figure}")) {
        while(!contains($text[$i], "\end{figure}"))
            $i++;
        $i++;
    }    
    else {  
        mettiLeG(&$text[$i], $arrTermini);     
        $i++;
    }
}
$text=implode(PHP_EOL, $text);

header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=texConPedici.tex");
$text=utf8_encode($text);
echo $text;

?>