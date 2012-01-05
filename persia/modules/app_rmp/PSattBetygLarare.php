<?php
// ===========================================================================================
//
// PSattBetygLarare.php
//
// Show details about the professor and enable making comments and gradings.
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
</table>
</fieldset>
EOD;

$res->close();


// -------------------------------------------------------------------------------------------
//
// Form to create the comment and grade
//
$html .= <<<EOD
<fieldset>
<legend><h3>Kommentera och sätt betyg!</h3></legend>
<form action='?p=kommenterap' method='POST'>
<input type='hidden' name='Grade_idProfessor' value='{$row->idProfessor}'>
<input type='hidden' name='redirect' value='visalararebetyg&idLarare={$idLarare}'>
<table border='0'>
<tr>
<td style='width: 50px;'>
Betyg: 
</td>
<td>
<select name='valueGrade'>
<option value='1'>1</option>
<option value='2'>2</option>
<option value='3'>3</option>
<option value='4'>4</option>
<option value='5' selected>5</option>
<option value='6'>6</option>
<option value='7'>7</option>
<option value='8'>8</option>
<option value='9'>9</option>
<option value='10'>10</option>
</select>
</td>
</tr>

<tr>
<td colspan='2'>
<textarea rows='6' cols='80' name='commentGrade'>
</textarea>
</td>
</tr>

<tr>
<td colspan='2' align='right'>
<button name='back' tab='15' value='undo' type='button' onClick='history.back();'>Tillbaka</button>
<button name='undo' tab='16' value='undo' type='reset'>Återställ</button>
<button name='save' tab='14' value='save' type='submit'>Spara</button>
</td>
</tr>

</table>
</form>
</fieldset>

EOD;


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