<?php
// ===========================================================================================
//
// PEditLarareInfoProcess.php
//
// Save changes to a edited professor.
//


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (mysqli_connect_error()) {
   echo "Connect failed: ".mysqli_connect_error()."<br>";
   exit();
}


// -------------------------------------------------------------------------------------------
//
// Take care of _GET variables. Store them in a variable (if they are set).
//
//print_r($_POST);
$idProfessor 		= isset($_POST['idProfessor']) 		? $_POST['idProfessor'] 		: '';
$nameProfessor 		= isset($_POST['nameProfessor']) 	? $_POST['nameProfessor'] 		: '';
$pictureProfessor 	= isset($_POST['pictureProfessor']) ? $_POST['pictureProfessor'] 	: '';
$infoProfessor 		= isset($_POST['infoProfessor']) 	? $_POST['infoProfessor'] 		: '';

if(!is_numeric($idProfessor)) {
    die("idProfessor måste vara ett integer. Försök igen.");
}


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableProfessor = DB_PREFIX . 'Professor';

$query = <<< EOD
UPDATE {$tableProfessor} 
SET
	nameProfessor 		= '{$nameProfessor}',
	pictureProfessor 	= '{$pictureProfessor}',
	infoProfessor 		= '{$infoProfessor}'	
WHERE 
	idProfessor = {$idProfessor}
;
EOD;

$res = $mysqli->query($query) 
                    or die("Could not query database");

$html = <<<EOD
<h2>Uppdatera info om lärare</h2>
<p>Query={$query}</p><p>Rows affected: {$mysqli->affected_rows}</p>
<p>
<a href='?p=visalarare'>Visa alla lärare</a> |
<a href='?p=editlarare&idLarare={$idProfessor}'>Uppdatera info igen</a> |
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//

$mysqli->close();


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once(TP_SOURCEPATH . 'CHTMLPage.php');

$stylesheet = WS_STYLESHEET;

$page = new CHTMLPage($stylesheet);

$page->printHTMLHeader('Uppdatera info om lärare');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();


?>