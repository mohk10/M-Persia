<?php
// -------------------------------------------------------------------------------------------
//
// PUsersList.php
//
// Show all users in a list.
//


// -------------------------------------------------------------------------------------------
//
// Interception Filter, access, authorithy and other checks.
//
if(!isset($indexIsVisited)) die('No direct access to pagecontroller is allowed.');

// User must be logged in
if(!isset($_SESSION['accountUser'])) require(TP_PAGESPATH . 'login/PLogin.php');

// User must be member of group adm
if($_SESSION['groupMemberUser'] != 'adm') die('You do not have the authourity to access this page');


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
// Prepare the order by ref, can you figure out how it works?
//

$ascOrDesc = $orderOrder == 'ASC' ? 'DESC' : 'ASC';
$httpRef = "?p=admin&amp;order={$ascOrDesc}&orderby=";


// -------------------------------------------------------------------------------------------
//
// Create a new database object, we are using the MySQLi-extension.
//
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (mysqli_connect_error()) {
   echo "Connect failed: ".mysqli_connect_error()."<br>";
   exit();
}

// Prevent SQL injections
$orderStr = $mysqli->real_escape_string($orderStr);


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableUser		 	= DB_PREFIX . 'User';
$tableGroup			= DB_PREFIX . 'Group';
$tableGroupMember	= DB_PREFIX . 'GroupMember';

$query = <<< EOD
SELECT 
	idUser, 
	accountUser,
	emailUser,
	idGroup,
	nameGroup
FROM {$tableUser} AS U
	INNER JOIN {$tableGroupMember} AS GM
		ON U.idUser = GM.GroupMember_idUser
	INNER JOIN {$tableGroup} AS G
		ON G.idGroup = GM.GroupMember_idGroup
{$orderStr}
EOD;

$res = $mysqli->query($query) 
                    or die("<p>Could not query database,</p><code>{$query}</code>");

$html = "<h2>Admin: Visa användarkonton</h2>";


// -------------------------------------------------------------------------------------------
//
// Show the results of the query
//

$html .= <<< EOD
<table border='1'>
<tr>
<th><a href='{$httpRef}idUser'>Id</a></th>
<th><a href='{$httpRef}accountUser'>Account</a></th>
<th><a href='{$httpRef}emailUser'>Email</a></th>
<th><a href='{$httpRef}idGroup'>Grupp</a></th>
<th><a href='{$httpRef}nameGroup'>Grupp description</a></th>
</tr>
EOD;

while($row = $res->fetch_object()) {
	$html .= <<< EOD
<tr>
<td>{$row->idUser}</td>
<td>{$row->accountUser}</td>
<td>{$row->emailUser}</td>
<td>{$row->idGroup}</td>
<td>{$row->nameGroup}</td>
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

$page = new CHTMLPage();

$page->printHTMLHeader('Visa samtliga användarkonton');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();
exit;


?>
