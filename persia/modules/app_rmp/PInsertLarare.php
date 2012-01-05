<?php
// -------------------------------------------------------------------------------------------
//
// PInsertLarare.php
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
// Prepare and perform a SQL query.
//
$tableProfessor = DB_PREFIX . 'Professor';
$tableGrade	= DB_PREFIX . 'Grade';

$query = <<<EOD
INSERT INTO {$tableProfessor} (
	nameProfessor, 
	infoProfessor, 
	pictureProfessor
)
VALUES (
	'John/Jane Doe', 
	'Ingen som sett han/henne ännu...', 
	'http://tekcp554.tek.bth.se/webdb/img/no.jpg'
);
EOD;

$res = $mysqli->query($query) 
					or die("Could not query database");

$html = <<<EOD
<h2>Ny lärare</h2>
<p>Query={$query}</p><p>Rows affected: {$mysqli->affected_rows}</p>
<p>Ny lärare fick id: {$mysqli->insert_id}</p>
<p>
<a href='?p=visalarare'>Visa alla lärare</a> |
<a href='?p=editlarare&amp;idLarare={$mysqli->insert_id}'>Uppdatera information om lärare</a>
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

$page->printHTMLHeader('lägg till ny lärare');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();


?>
