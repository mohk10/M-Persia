<?php
// ===========================================================================================
//
// PVisaLarareBetyg.php
//
// Show the information in a form and make it possible to edit the information.
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

$idLarare = isset($_GET['idLarare']) ? $_GET['idLarare'] : '';

if(!is_numeric($idLarare)) {
    die("idLarare måste vara ett integer. Försök igen.");
}


// -------------------------------------------------------------------------------------------
//
// Prepare and perform SQL query.
//
$tableProfessor = DB_PREFIX . 'Professor';
$tableGrade 	= DB_PREFIX . 'Grade';

$query = <<<EOD
SELECT 
	idProfessor,
	nameProfessor,
	infoProfessor,
	pictureProfessor
FROM {$tableProfessor} 
WHERE 
	idProfessor = {$idLarare}
;
EOD;


$query .= <<<EOD
SELECT 
  idGrade,
  valueGrade,
  commentGrade,
  dateGrade
FROM {$tableGrade} 
WHERE 
	Grade_idProfessor = {$idLarare}
ORDER BY
	dateGrade DESC
;
EOD;


$mysqli->multi_query($query) 
                   or die("Could not query database");

$html = <<<EOD
<h2>Visa lärare med kommentarer och betyg</h2>
EOD;

// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$res = $mysqli->store_result()
	or die("Failed to retrive result from query.");

$row = $res->fetch_object();

$namnLarare = $row->nameProfessor;

$html .= <<< EOD
<fieldset>
<legend><h3>{$row->nameProfessor}</h3></legend>
<table border='0'>
<tr>
<td>
<img src='{$row->pictureProfessor}' style='width: 100px'>
</td>
<td>
<textarea rows='6' cols='80' name='infoProfessor' readonly>
{$row->infoProfessor}
</textarea>
</td>
</tr>
<tr>
<td colspan='2' align='right'>
[ <a class='small' href='?p=kommentera&idLarare={$idLarare}'>Kommentera / betygsätt</a> ]
[ <a class='small' href='?p=visalarare'>Visa alla lärare</a> ]
[ <a class='small' href='?p=editlarare&idLarare={$idLarare}'>Uppdatera info</a> ]
[ <a class='small' href='?p=deletelarare&idLarare={$idLarare}'>Radera lärare</a> ]
</td>
</tr>
</table>
</fieldset>
EOD;

$res->close();


// -------------------------------------------------------------------------------------------
//
// Show the results of NEXT the query
//
($mysqli->next_result() && ($res = $mysqli->store_result())) 
	or die("Failed to retrive result from query.");

	$html .= <<< EOD
<p>
Det finns {$res->num_rows} kommentarer / betyg för denna läraren.
</p>
<p>
[ <a class='small' href='?p=kommentera&idLarare={$idLarare}'>Kommentera / betygsätt</a> ]
</p>
EOD;


while($row = $res->fetch_object()) {

	$html .= <<< EOD
<hr>
<p class='small'>
Betyg: {$row->valueGrade}, den {$row->dateGrade}
</p>
<p>
{$row->commentGrade}
</p>
EOD;
} // endwhile

$html .= "</table>";


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

$page->printHTMLHeader("{$namnLarare}, kommentarer och betyg");
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();

?>