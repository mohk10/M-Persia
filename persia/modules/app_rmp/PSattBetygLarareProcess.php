<?php
// ===========================================================================================
//
// PVisaLarareBetygProcess.php
//
// Save changes for comments and grading.
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
$Grade_idProfessor 	= isset($_POST['Grade_idProfessor']) 	? $_POST['Grade_idProfessor'] 	: '';
$valueGrade 		= isset($_POST['valueGrade']) 			? $_POST['valueGrade'] 			: '';
$commentGrade 		= isset($_POST['commentGrade']) 		? $_POST['commentGrade'] 		: '';

if(!is_numeric($Grade_idProfessor)) {
    die("Grade_idProfessor måste vara ett integer. Försök igen.");
}


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableGrade = DB_PREFIX . 'Grade';

$query = <<< EOD
INSERT INTO {$tableGrade} 
	(Grade_idProfessor, valueGrade, commentGrade, dateGrade)
VALUES
	({$Grade_idProfessor}, {$valueGrade}, '{$commentGrade}', NOW())
;
EOD;

$res = $mysqli->query($query) 
                    or die("Could not query database");

$html = <<<EOD
<h2>Lägga till kommentar och betyg för lärare</h2>
<p>Query={$query}</p><p>Rows affected: {$mysqli->affected_rows}</p>
<p>
[ <a href='?p=visalarare'>Visa alla lärare</a> ]
[ <a href='?p=visalararebetyg&idLarare={$Grade_idProfessor}'>Visa läraren med kommentarer igen</a> ]
</p>
EOD;


// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//

$mysqli->close();


/*
// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
require_once('CHTMLPage.php');

$stylesheet = WS_STYLESHEET;

$page = new CHTMLPage($stylesheet);

$page->printHTMLHeader('Lägg till kommentar och betyg om lärare');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();
*/

// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'home';
header('Location: ' . WS_SITELINK . "?p={$redirect}");
exit;


?>