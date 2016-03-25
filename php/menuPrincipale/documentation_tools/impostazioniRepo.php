<?php
/**
* questo file contiene le impostazni riguardanti i file da scansionare nel repository
*/

/**
* Questo array contiene la mappa chiave-valore dei documenti da mostrare nella tabella
* la chiave è il nome del documento
* il valore è il link in cui sono salvati (nella versione raw)
*/
$arrDocumentiRepo=array(
	'Analisi dei requisiti' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Analisi%20dei%20requisiti.tex',
	'Glossario' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Glossario.tex',
	'Lettera di presentazione' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Lettera%20di%20presentazione.tex',
	'Norme di progetto' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Norme%20di%20progetto.tex',
	'Piano di progetto' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Piano%20di%20progetto.tex',
	'Piano di qualifica' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Piano%20di%20qualifica.tex',
	'Studio di fattibilit&agrave' => 'https://raw.githubusercontent.com/SweeneyThreads/Actorbase/master/LaTeX/Studio%20di%20fattibilit%C3%A0.tex'
);


/**
* questi 2 array rappresentano i range di accettabilità e ottimalità per le metriche in tabella
* se 0 significa che non ci sono metriche
* altrimenti sono l'array contenente {valoreMin,valoreMax}
*/
$accettabilita=array(
	0,				//nome documento
	0,				//versione
	0,				//parole tot
	0,				//figure tot
	array(35,100),	//gulpease
	array(100,250)	//media parole per section
);

$ottimalita=array(
	0,				//nome documento
	0,				//versione
	0,				//parole tot
	0,				//figure tot
	array(45,100),	//gulpease
	array(15,100)	//media parole per section
);



?>