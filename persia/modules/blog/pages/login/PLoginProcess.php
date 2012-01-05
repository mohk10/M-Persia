<?php
// ===========================================================================================
//
// PLoginProcess.php
//
// Verify user and password. Create a session and store userinfo in.
//

// -------------------------------------------------------------------------------------------
//
// Page specific code
//
//Set the error reporting to on.
error_reporting(E_ALL);
// -------------------------------------------------------------------------------------------
//
// Destroy the current session (logout user), if it exists. 
//
require_once($currentDir . 'src/FDestroySession.php');

// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database.
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
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$user     = isset($_POST['accountAuthor']) ? $_POST['accountAuthor'] : '';
$password   = isset($_POST['passwordAuthor']) ? $_POST['passwordAuthor'] : '';

// Prevent SQL injections
$user     = $mysqli->real_escape_string($user);
$password   = $mysqli->real_escape_string($password);

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableAuthors       = DB_PREFIX . 'Authors';

$query = <<< EOD
SELECT 
  *
FROM {$tableAuthors} 
WHERE
  accountAuthor    = '{$user}' AND
  passwordAuthor   = md5('{$password}')
;
EOD;
//echo($query);
$res = $mysqli->query($query) 
                    or die("<p>Could not query database,</p><code>{$query}</code>");

// -------------------------------------------------------------------------------------------
//
// Use the results of the query to populate a session that shows we are logged in
//
session_start(); // Must call it since we destroyed it above.
session_regenerate_id(); // To avoid problems 

$row = $res->fetch_object();

// Must be one row in the resultset
if($res->num_rows === 1) {
  $_SESSION['idAuthor']       = $row->idAuthor;
  $_SESSION['accountAuthor']     = $row->accountAuthor; 
  $_SESSION['emailAuthor']     = $row->emailAuthor; 
  $_SESSION['nameBlog']     = $row->nameBlog;
  $_SESSION['errorMessage']  = "Inloggningen lyckades";
  $_POST['redirect']="visanamnbloggedit&idAuthor={$_SESSION['idAuthor']}";
  //echo($_SESSION['accountAuthor']);
  
} else {
  $_SESSION['errorMessage']  = "Inloggningen misslyckades";
  $_POST['redirect']       = 'login2';
  //echo($_SESSION['errorMessage']);
}

$res->close();

// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
$mysqli->close();

// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//

	$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'home';
header('Content-Type: text/html; charset=utf-8');
header('Location: ' . WS_SITELINK . "?m=blog&p={$redirect}");
exit;
?>
