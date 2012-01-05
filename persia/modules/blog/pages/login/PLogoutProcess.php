<?php
// ===========================================================================================
//
// PLogoutProcess.php
//
// Logout by destroying the session.
//

// -------------------------------------------------------------------------------------------
//
// Page specific code
//

// -------------------------------------------------------------------------------------------
//
// Destroy the current session (logout user), if it exists. 
//
require_once($currentDir . 'src/FDestroySession.php');

// -------------------------------------------------------------------------------------------
//
// Redirect to another page
//
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'login2';
header('Location: ' . WS_SITELINK . "?m=blog&p={$redirect}");
exit;
?>
