<?php
// ===========================================================================================
//
// PProfileShow.php
//
// An implementation of a PHP pagecontroller for a web-site.
//
//
// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPageController.php');

$pc = new CPagecontroller();


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
require_once(TP_SOURCEPATH . 'CInterceptionFilter.php');

$intFilter = new CInterceptionFilter();

$intFilter->frontcontrollerIsVisitedOrDie();
//$intFilter->userIsSignedInOrRecirectToSign_in();
//$intFilter->userIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of global pageController settings, can exist for several pagecontrollers.
// Decide how page is displayed, review CHTMLPage for supported types.
//
$displayAs = $pc->GETisSetOrSetDefault('pc_display', '');


// -------------------------------------------------------------------------------------------
//
//  Allow only access to pagecontrollers through frontcontroller
//
//if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');

// -------------------------------------------------------------------------------------------
//
// Create a new database object, we are using the MySQLi-extension.
//
require_once(TP_SQLPATH."config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);




if (mysqli_connect_error()) {
   echo "Connect failed: ".mysqli_connect_error()."<br>";
   exit();
}

$mysqli->set_charset("utf8");

 

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableAuthors      = DB_PREFIX . 'Authors';

$query = <<< EOD
SELECT 
  *  
FROM {$tableAuthors} 
WHERE
  idAuthor    = '{$_SESSION['idAuthor']}' 
;
EOD;


$res = $mysqli->query($query) or die("Could not query database");
$html = <<<EOD
<h2>MITT KONTO</h2>
EOD;

// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$row = $res->fetch_object();

$html .= <<< EOD
<fieldset>
<legend><strong>{$row->accountAuthor}</strong></legend>
<form action='?p=visaprofil' method='POST'>
<input type='hidden' name='redirect' value='visalarare'>
<table>
<tr>
<th>Id</th>
<td><input type='text' name='idAuthor' size='80' readonly value='{$row->idAuthor}'></td>
</tr>
<tr>
<th>Namn</th>
<td><input type='text' name='nameAuthor' size='80' readonly value='{$row->nameAuthor}'></td>
</tr>
<tr>
<th>Account</th>
<td><input type='text' name='accountAuthor' size='80' readonly value='{$row->accountAuthor}'></td>
</tr>
<tr>
<th>Email</th>
<td><input type='text' name='emailAuthor' size='80' readonly value='{$row->emailAuthor}'></td>
</tr>
<tr>
<th>Ansvarsområde</th>
<td><input type='text' name='sphereAuthor' size='80' readonly value='{$row->sphereAuthor}'></td>
</tr>
<tr>
<th>Min bloggs namn</th>
<td><input type='text' name='nameBlog' size='80' readonly value='{$row->nameBlog}'></td>
</tr>
</table>
</form>
</fieldset>
EOD;

$res->close();

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
//require_once(TP_SOURCEPATH.'CHTMLPage.php');

//$page = new CHTMLPage();
require_once($currentDir .'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);
$page->printHTMLHeader('Visa användarens profil');
$page->printPageHeader('Fogglers blogg');
$page->printPageBody($html);
$page->printPageFooter();
?>
