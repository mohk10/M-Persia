<?php
// ===========================================================================================
//
// File: CDatabaseController.php
//
// Description: To ease database usage for pagecontroller. Supports MySQLi.
// All table names (stored procedures, functions, triggers) are stored in the config.php-file.
//
// Author: Mikael Roos
//

class CDatabaseController {

// ------------------------------------------------------------------------------------
//
// Internal variables
//
protected static $iInstance = NULL;
protected $iMysqli;
protected $iPc;

// For accessing the tables and procedures naming array from within the database controller
//public static $iTablesAndProcedures;
//public $_;


// ------------------------------------------------------------------------------------
//
// Constructor
//
public function __construct() {

//
// Store a pointer to the latest instance of the class.
// Sort of singleton design pattern but with a public constructor.
//
self::$iInstance =& $this;

$this->iMysqli = FALSE;

$this->iPc = new CPageController();
$this->iPc->LoadLanguage(__FILE__);

//
// Store a reference to the array containing names of tables, procedures, functions,
// triggers, etc in this class. The array is defined in the config.php-file.
// This makes it possible to use it as variable in HEREDOC strings.
// Reducing the need of converting constants/define to strings.
// The proper way of accessing a table name would then be:
// $db->_['tablename']
//
// http://www.php.net/manual/en/language.references.whatdo.php
//
//$this->lang = array_merge($this->lang, $lang);
require_once(TP_SQLPATH . 'config.php');

//self::$iTablesAndProcedures = &$DB_Tables_And_Procedures;
//$this->_ = &$DB_Tables_And_Procedures;
}


// ------------------------------------------------------------------------------------
//
// Destructor
//
public function __destruct() {
;
}


// ------------------------------------------------------------------------------------
//
// Get the instance of the latest created object or create a new one.
//
public static function GetInstance() {

if(self::$iInstance == NULL) {
$db = new CDatabaseController();
}
return self::$iInstance;
}


// ------------------------------------------------------------------------------------
//
// Connect to the database, return a database object.
//
public function Connect() {

$this->iMysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (mysqli_connect_error()) {
    die(sprintf($this->iPc->lang['CONNECT_TO_DATABASE_FAILED'], mysqli_connect_error()));
}

return $this->iMysqli;
}


// ------------------------------------------------------------------------------------
//
// Execute a database multi_query
//
public function MultiQuery($aQuery) {

$res = $this->iMysqli->multi_query($aQuery)
or die(sprintf($this->iPc->lang['COULD_NOT_QUERY_DATABASE'], $aQuery, $this->iMysqli->error));
            
return $res;
}


// ------------------------------------------------------------------------------------
//
// Retrieve and store results from multiquery in an array.
//
public function RetrieveAndStoreResultsFromMultiQuery(&$aResults) {

$mysqli = $this->iMysqli;

$i = 0;
do {
$aResults[$i++] = $mysqli->store_result();
} while($mysqli->next_result());

// Check if there is a database error
!$mysqli->errno
or die(sprintf($this->iPc->lang['FAILED_RETRIEVING_RESULTSET'], $this->iMysqli->errno, $this->iMysqli->error));
}


// ------------------------------------------------------------------------------------
//
// Execute multiquery, retrieve and store the resultset in an array.
// Return the resultset.
//
// Does the same as MultiQuery and RetrieveAndStoreResultsFromMultiQuery,
// just to ease usage from the pagecontrollers.
//
public function DoMultiQueryRetrieveAndStoreResultset($aQuery) {

$res = $this->iMysqli->multi_query($aQuery)
or die(sprintf($this->iPc->lang['COULD_NOT_QUERY_DATABASE'], $aQuery, $this->iMysqli->error));

$results = Array();
do {
$results[] = $this->iMysqli->store_result();
} while($this->iMysqli->next_result());

// Check if there is a database error
!$this->iMysqli->errno
or die(sprintf($this->iPc->lang['FAILED_RETRIEVING_RESULTSET'], $this->iMysqli->errno, $this->iMysqli->error));

return $results;
}


// ------------------------------------------------------------------------------------
//
// Retrieve and ignore results from multiquery, count number of successful statements
// Some succeed and some fail, must count to really know.
//
public function RetrieveAndIgnoreResultsFromMultiQuery() {

$mysqli = $this->iMysqli;

$statements = 0;
do {
$res = $mysqli->store_result();
$statements++;
} while($mysqli->next_result());

return $statements;
}


// ------------------------------------------------------------------------------------
//
// Load a database query from file in the directory TP_SQLPATH
//
public function LoadSQL($aFile) {

$mysqli = $this->iMysqli;
require(TP_SQLPATH . $aFile);
return $query;
}


// ------------------------------------------------------------------------------------
//
// Execute a database query
//
public function Query($aQuery) {

$res = $this->iMysqli->query($aQuery)
or die(sprintf($this->iPc->lang['COULD_NOT_QUERY_DATABASE'], $aQuery, $this->iMysqli->error));

return $res;
}

// ------------------------------------------------------------------------------------
//
// Execute a database query from file, check the number of rows affected.
// If aRowsAffected is 0, then skip checking.
//
/*
public function ConnectAndExecuteSingleSQLQueryFromFileCheckRowsAffected($aFile, $aRowsAffected=0) {

$this->Connect();
$query = $this->LoadSQL($aFile);
$res = $this->Query($query);

if($aRowsAffected != 0 && $this->iMysqli->affectedRows != $aRowsAffected) {
$this->iPc->SetSessionErrorMessage(sprintf($this->iPc->lang['NUMBER_OF_ROWS_AFFECTED_MISMATCH']), $aRowsAffected, $this->iMysqli->affectedRows);
}
$res->close();
$this->iMysqli->close();
}
*/


} // End of Of Class

?>