<?php
// ===========================================================================================
//
// PInstallProcess.php
//
// Creates new tables in the database. Redirects to a response-page using _GET['r'].
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
// Prepare SQL for User & Group structure.
//
$tableUser 			= DB_PREFIX . 'User';
$tableGroup			= DB_PREFIX . 'Group';
$tableGroupMember	= DB_PREFIX . 'GroupMember';

$query = <<<EOD
DROP TABLE IF EXISTS {$tableUser};
DROP TABLE IF EXISTS {$tableGroup};
DROP TABLE IF EXISTS {$tableGroupMember};

--
-- Table for the User
--
CREATE TABLE {$tableUser} (

  -- Primary key(s)
  idUser INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  accountUser CHAR(20) NOT NULL UNIQUE,
  emailUser CHAR(100) NOT NULL,
  passwordUser CHAR(32) NOT NULL
);


--
-- Table for the Group
--
CREATE TABLE {$tableGroup} (

  -- Primary key(s)
  idGroup CHAR(3) NOT NULL PRIMARY KEY,

  -- Attributes
  nameGroup CHAR(40) NOT NULL
);


--
-- Table for the GroupMember
--
CREATE TABLE {$tableGroupMember} (

  -- Primary key(s)
  --
  -- The PK is the combination of the two foreign keys, see below.
  --
  
  -- Foreign keys
  GroupMember_idUser INT NOT NULL,
  GroupMember_idGroup CHAR(3) NOT NULL,
	
  FOREIGN KEY (GroupMember_idUser) REFERENCES {$tableUser}(idUser),
  FOREIGN KEY (GroupMember_idGroup) REFERENCES {$tableGroup}(idGroup),

  PRIMARY KEY (GroupMember_idUser, GroupMember_idGroup)
  
  -- Attributes

);

--
-- Add default user(s) 
--
INSERT INTO {$tableUser} (accountUser, emailUser, passwordUser)
VALUES ('mikael', 'mos@bth.se', md5('hemligt'));
INSERT INTO {$tableUser} (accountUser, emailUser, passwordUser)
VALUES ('doe', 'doe@bth.se', md5('doe'));

--
-- Add default groups
--
INSERT INTO {$tableGroup} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
INSERT INTO {$tableGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');

--
-- Add default groupmembers
--
INSERT INTO {$tableGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
VALUES ((SELECT idUser FROM {$tableUser} WHERE accountUser = 'doe'), 'usr');
INSERT INTO {$tableGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
VALUES ((SELECT idUser FROM {$tableUser} WHERE accountUser = 'mikael'), 'adm');

EOD;


// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableProfessor = DB_PREFIX . 'Professor';
$tableGrade	= DB_PREFIX . 'Grade';

$query .= <<<EOD
DROP TABLE IF EXISTS {$tableGrade};
DROP TABLE IF EXISTS {$tableProfessor};

--
-- Table for the professors
--
CREATE TABLE {$tableProfessor} (

  -- Primary key(s)
  idProfessor INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  nameProfessor CHAR(40),
  infoProfessor CHAR(100),
  pictureProfessor CHAR(100)
);

--
-- Table to store the grades and the comments
--
CREATE TABLE {$tableGrade} (

  -- Primary key(s)
  idGrade INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Foreign key(s)
  Grade_idProfessor INT,
  FOREIGN KEY (Grade_idProfessor) REFERENCES {$tableProfessor}(idProfessor),

  -- Attributes
  valueGrade INT,
  commentGrade VARCHAR(400),
  dateGrade DATETIME
);

--
-- Insert some dummy values to start with
--
INSERT INTO {$tableProfessor}(nameProfessor, infoProfessor, pictureProfessor)
VALUES ('MegaMic Mos', 'En lysande kille!', 'http://tekcp554.tek.bth.se/webdb/img/megamic.jpg');
INSERT INTO {$tableProfessor}(nameProfessor, infoProfessor, pictureProfessor)
VALUES ('Glada grisen', 'Trevlig och glad.', 'http://tekcp554.tek.bth.se/webdb/img/happy-pig.jpg');
INSERT INTO {$tableProfessor}(nameProfessor, infoProfessor, pictureProfessor)
VALUES ('Gandalf', 'Hänsynslös och tuff, kan det mesta', 'http://tekcp554.tek.bth.se/webdb/img/gandalf.jpg');
INSERT INTO {$tableProfessor}(nameProfessor, infoProfessor, pictureProfessor)
VALUES ('Dumbeldore', 'Ett otroligt minne, har hemligheter', 'http://tekcp554.tek.bth.se/webdb/img/dumbeldore.jpg');
EOD;

$res = $mysqli->multi_query($query) 
                    or die("Could not query database");

//
// Retrieve and ignore the results from the above query
// Some may succed and some may fail. Must count the number of succeded 
// statements to really know.
//
$statements = 0;
do {
	$res = $mysqli->store_result();
	$statements++;
} while($mysqli->next_result());


//
// Prepare the text
//
$html = "<h2>Installera databas</h2>";
$html .= "<p>Query=<br/><pre>{$query}</pre></p>";
$html .= "<p>Antal lyckade statements: {$statements}</p>";
$html .= "<p><a href='?p=hem'>Hem</a></p>";


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

$page->printHTMLHeader('Installation av databas');
$page->printPageHeader();
$page->printPageBody($html);
$page->printPageFooter();



?>
