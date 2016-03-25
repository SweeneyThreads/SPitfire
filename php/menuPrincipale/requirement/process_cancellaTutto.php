<?php
session_start();
if (!isset($_SESSION['auth']) or $_SESSION['auth']==NULL) header("Location: ../login.php");
require_once("../lib/htmlgenerator.php");
require_once("../lib/phplib.php");
$conn=connect();
echo pageHeader('Cancellazione');


echo <<< END

<form method="GET">
	<p>scrivi DELETE per cancellare tutti i requisiti, tutti i loro stati di avanzamento e tutte le loro parentele</p>
	<input type="text" name="canc" />
	<input type="submit" value="gogogo" name="sub" />
</form>

END;



if (isset($_GET['sub'])) {
	if ($_GET['canc']=='DELETE') {
		$result=mysql_query('delete from derivazRequisiti where 1', $conn);
		$result=mysql_query('delete from Requisiti where 1', $conn);
		

		echo <<< END
Cancellato tutto. <br />
<a href="requisiti.php">vai ai requisiti</a>

END;
	} else {
		echo <<< END
Scrivi "DELETE" per cancellare tutto <br />
<a href="autoaddRequirement.php">oppure back</a>

END;
	}

}


?>