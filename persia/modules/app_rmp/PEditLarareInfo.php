<?php
// ===========================================================================================
//
// PEditLarareInfo.php
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
// Prepare and perform a SQL query.
//
$tableProfessor = DB_PREFIX . 'Professor';

$query = <<<EOD
SELECT 
	* 
FROM {$tableProfessor} 
WHERE 
	idProfessor = {$idLarare};
EOD;

$res = $mysqli->query($query) 
                    or die("Could not query database");

$html = <<<EOD
<h2>Uppdatera information om lärare</h2>
EOD;

// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$row = $res->fetch_object();

$html .= <<< EOD
<fieldset>
<legend><img src='{$row->pictureProfessor}' width='100'></legend>
<form action='?p=editlararep' method='POST'>
<!-- <input type='hidden' name='redirect' value='editlararep&idProfessor={$idLarare}'> -->
<table border='0'>
<tr>
<th>Id</th>
<td><input type='text' tab='10' name='idProfessor' size='80' readonly value='{$row->idProfessor}'></td>
</tr>
<tr>
<th>Namn</th>
<td><input type='text' tab='11' name='nameProfessor' size='80' value='{$row->nameProfessor}'></td>
</tr>
<tr>
<th>Bild</th>
<td><input type='text' tab='12' name='pictureProfessor' size='80' value='{$row->pictureProfessor}'></td>
</tr>
<tr>
<th>Info</th>
<td><input type='text' tab='13' name='infoProfessor' size='80' value='{$row->infoProfessor}'></td>
</tr>
<tr>
<td colspan='2' align='right'>
[ <a class='small' href='?p=deletelarare&idLarare={$idLarare}'>Radera lärare</a> ]
<button name='undo' tab='15' value='undo' type='reset'>Återställ</button>
<button name='save' tab='14' value='save' type='submit'>Spara</button>
</td>
</tr>
</table>
</form>
</fieldset>
EOD;

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

$page->printHTMLHeader('Uppdatera information om lärare');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();

?>