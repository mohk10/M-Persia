<?php
// -------------------------------------------------------------------------------------------
//
// PVisaLarare.php
//
// 
//


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

// -------------------------------------------------------------------------------------------
//
// Take care of _GET variables. Store them in a variable (if they are set).
// Then prepare the ORDER BY SQL-statement, but only if the _GET variables has a value.
//

$orderBy 	= isset($_GET['orderby']) 	? $_GET['orderby'] 	: '';
$orderOrder = isset($_GET['order'])		? $_GET['order']	: '';

$orderStr = "";
if(!empty($orderBy) && !empty($orderOrder)) {
	$orderStr = " ORDER BY {$orderBy} {$orderOrder}";
}


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

$query =<<<EOD
SELECT 
	* 
FROM {$tableProfessor} 
{$orderStr}
;
EOD;

$res = $mysqli->query($query) 
					or die("Could not query database");

$html = "<h2>Visa lärare</h2>";
$html .= "<p><a href='?p=insertlarare'>Lägg till en Ny lärare</a></p>";


// -------------------------------------------------------------------------------------------
//
// Prepare the order by ref, can you figure out how it works?
//

$ascOrDesc = $orderOrder == 'ASC' ? 'DESC' : 'ASC';
$httpRef = "?p=visalarare&amp;order={$ascOrDesc}&orderby=";


// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//

$html .= <<< EOD
<table border='1'>
<tr>
<th><a href='{$httpRef}pictureProfessor'>Bild</a></th>
<th><a href='{$httpRef}nameProfessor'>Namn</a></th>
<th><a href='{$httpRef}infoProfessor'>Info</a></th>
<th></th>
</tr>
EOD;

while($row = $res->fetch_object()) {
	$html .= <<< EOD
<tr>
<td><img src='{$row->pictureProfessor}' style='width: 100px'></td>
<td>
<a href='?p=visalararebetyg&amp;idLarare={$row->idProfessor}' title='Granska denna lärare och sätt ditt eget betyg'>{$row->nameProfessor}</a>
</td>
<td>{$row->infoProfessor}</td>
<td>
<a href='?p=editlarare&amp;idLarare={$row->idProfessor}' title='Updatera detaljer om {$row->nameProfessor} med id={$row->idProfessor}'>edit</a>
<a href='?p=deletelarare&amp;idLarare={$row->idProfessor}' title='Radera {$row->nameProfessor} med id={$row->idProfessor}'>&otimes;</a>
</td>
</tr>
EOD;
}

$html .= "</table>";
$html .= "<p>Query={$query}</p><p>Antal rader i resultset: {$res->num_rows}</p>";

$res->close();


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

$page->printHTMLHeader('Visa samtliga lärare');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();


?>
