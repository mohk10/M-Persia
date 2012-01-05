<?php
// ===========================================================================================
//
// PProfileShow.php
//
// Show the users profile information in a form and make it possible to edit the information.
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
require_once(TP_SOURCEPATH . 'CPagecontroller.php');

$pc = new CPagecontroller();
$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
require_once(TP_SOURCEPATH . 'CInterceptionFilter.php');

$intFilter = new CInterceptionFilter();

$intFilter->frontcontrollerIsVisitedOrDie();
$intFilter->userIsSignedInOrRecirectToSign_in();
//$intFilter->userIsMemberOfGroupAdminOrDie();


// -------------------------------------------------------------------------------------------
//
// Take care of global pageController settings, can exist for several pagecontrollers.
// Decide how page is displayed, review CHTMLPage for supported types.
//
$displayAs = $pc->GETisSetOrSetDefault('pc_display', '');


// -------------------------------------------------------------------------------------------
//
// Page specific code
//

$settingsMenu = $pc->GetSidebarMenu(unserialize(MENU_SETTINGSBAR));

$htmlLeft = <<<EOD
<div class='sidebox'>
<fieldset>
<div id='settingsbar'>
<h4>Inställningar</h4>
{$settingsMenu}
</div>
</fieldset>
</div>
EOD;

$headerMenu = $pc->GetSidebarMenu(unserialize(MENU_ACCOUNTBAR));

$htmlMain = <<<EOD
<h1>Konto</h1>
<div id='settingsmenu'>
{$headerMenu}
</div>
EOD;

$htmlRight = "";


// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
//
$mysqli = $pc->ConnectToDatabase();


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$query = "";

$user = $_SESSION['accountUser'];

require_once(TP_SQLPATH . "SUserDetails.php");

$res = $pc->Query($query);


// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//
$row = $res->fetch_object();

$htmlMain .= <<< EOD
<fieldset>
<table border='0'>
<tr>
<th>Id</th>
<td><input type='text' tab='10' name='idUser' size='80' readonly value='{$row->idUser}'></td>
</tr>
<tr>
<th>Account</th>
<td><input type='text' tab='11' name='accountUser' readonly size='80' value='{$row->accountUser}'></td>
</tr>
<tr>
<th>Email</th>
<td><input type='text' tab='12' name='emailUser' readonly size='80' value='{$row->emailUser}'></td>
</tr>
<tr>
<th>Group</th>
<td><input type='text' tab='13' name='idGroup' readonly size='80' value='{$row->idGroup}'></td>
</tr>
<tr>
<th>Group description</th>
<td><input type='text' tab='13' name='nameGroup' readonly size='80' value='{$row->nameGroup}'></td>
</tr>
</table>
</fieldset>
EOD;


// -------------------------------------------------------------------------------------------
//
// Use the results of the query 
//

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

$page = new CHTMLPage(WS_STYLESHEET);

$page->printPage($htmlLeft, $htmlMain, $htmlRight, 'Inställningar Konto', $displayAs);
exit;

?>