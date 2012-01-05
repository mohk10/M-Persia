<?php
// -------------------------------------------------------------------------------------------
//
// PDeleteLarare.php
//
// 
//


// -------------------------------------------------------------------------------------------
//
// Page specific code
//


// -------------------------------------------------------------------------------------------
//
// Create a new database object, we are using the MySQLi-extension.
//
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (mysqli_connect_error()) {
   echo "Connect failed: ".mysqli_connect_error()."<br>";
   exit();
}
		

// -------------------------------------------------------------------------------------------
//
// Take care of _GET variables. Store them in a variable (if they are set).
// Then prepare the WHERE part of the DELETE-statement, but only if the _GET 
// variables has a value.
//

$idLarare = isset($_GET['idLarare']) ? $_GET['idLarare'] : '';

if(!is_numeric($idLarare)) {
	die("idLarare måste vara ett integer. Välj vilken lärare du vill radera och försök igen.");
}


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableProfessor = DB_PREFIX . 'Professor';
$tableGrade	= DB_PREFIX . 'Grade';

$query = <<<EOD
DELETE FROM {$tableProfessor}
WHERE idProfessor = {$idLarare}
LIMIT 1;
EOD;

$res = $mysqli->query($query) 
					or die("Could not query database");

$html = "<h2>Radera lärare</h2>";
$html .= "<p>Query={$query}</p><p>Rows affected: {$mysqli->affected_rows}</p>";
$html .= "<p><a href='?p=visalarare'>Visa alla lärare</a></p>";

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

$page = new CHTMLPage();

$page->printHTMLHeader('Radera en lärare');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();


?>
