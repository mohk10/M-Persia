<?php
// ===========================================================================================
//
// PInstallProcess.php
//
// Creates new tables in the database. 
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
// Take care of _GET variables. Store them in a variable (if they are set).
//
$idAuthor = isset($_GET['idAuthor']) ? $_GET['idAuthor'] : '';

if(!is_numeric($idAuthor)) {
    die("idAuthor måste vara ett integer. Försök igen. Du måste vara inloggad.");
}
// -------------------------------------------------------------------------------------------
//
// interception filter
//
if(isset($_SESSION['idAuthor'])&&($idAuthor!=$_SESSION['idAuthor'])){
	die("Du har inte behörighet till denna sida");
}

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
// Prepare and perform a SQL query.
//
$tableAuthors= DB_PREFIX . 'Authors';
$tableComments = DB_PREFIX . 'Comments';
$tableNotes = DB_PREFIX . 'Notes';

$query = <<<EOD
DROP TABLE IF EXISTS {$tableAuthors};
DROP TABLE IF EXISTS {$tableComments};
DROP TABLE IF EXISTS {$tableNotes};

--
-- Table for the authors
--
CREATE TABLE {$tableAuthors} (

  -- Primary key(s)
  idAuthor INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  nameAuthor CHAR(50) NOT NULL,
  accountAuthor CHAR(25) NOT NULL UNIQUE,
  passwordAuthor CHAR(32) NOT NULL,
  emailAuthor CHAR(100) NOT NULL,
  sphereAuthor CHAR(50) NOT NULL,
  nameBlog CHAR(50) NOT NULL
) ENGINE=MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Table to store the comments
--
CREATE TABLE {$tableComments} (

  -- Primary key(s)
  idComment INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Foreign key(s)
  Comment_idAuthor INT,
  FOREIGN KEY (Comment_idAuthor) REFERENCES {$tableAuthors}(idAuthor),

  -- Attributes
  titleComment VARCHAR(50),
  textComment VARCHAR(500),
  imageComment VARCHAR(200),
  tagsComment VARCHAR(100),
  dateComment DATETIME
) ENGINE=MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;
--
-- Table to store the notes
--
CREATE TABLE {$tableNotes} (

  -- Primary key(s)
  idNote INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Foreign key(s)
  Note_idComment INT,
  FOREIGN KEY (Note_idComment) REFERENCES {$tableComments}(idComment),

  -- Attributes
  titleNote VARCHAR(50),
  textNote VARCHAR(500),
  emailNote VARCHAR(100),
  dateNote DATETIME
) ENGINE=MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Insert some dummy values to start with
--
INSERT INTO {$tableAuthors}(nameAuthor, accountAuthor,passwordAuthor,emailAuthor,sphereAuthor,nameBlog)
VALUES ('Henrik R', 'henke', md5('henke'),'henke@hotmail.com','Europa','Henriks blogg');
INSERT INTO {$tableAuthors}(nameAuthor,accountAuthor,passwordAuthor,emailAuthor,sphereAuthor,nameBlog)
VALUES ('Petter W', 'petter', md5('petter'),'petter@hotmail.com','Nordamerika','Petters blogg - Uncle Sam');
INSERT INTO {$tableAuthors}(nameAuthor,accountAuthor,passwordAuthor,emailAuthor,sphereAuthor,nameBlog)
VALUES ('Tobias C', 'tobbe', md5('tobbe'),'tobbe@hotmail.com','Asien','Tobias blogg - Mittens rike');
INSERT INTO {$tableAuthors}(nameAuthor,accountAuthor,passwordAuthor,emailAuthor,sphereAuthor,nameBlog)
VALUES ('Daniel S', 'danne', md5('danne'),'danne@hotmail.com','Australien','Daniels blogg - Down under');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (1, 'Fotboll förr och då', 'Det spelades bättre boll på Gunnar Nordahls tid? Det spelades bättre boll på gräsplanen hemmavid?','modules/blog/images/zlatan-ibrahimovic.jpg','fotboll,konst,religion,mat','2010-01-01 10:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (1, 'Henkes andra inlägg', 'andra inlägget,andra inlägget,andra inlägget',NULL,'konst,politik,fotboll,ekonomi','2010-03-02 10:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (1, 'Henkes tredje inlägg', 'tredje inlägget,tredje inlägget,tredje inlägget',NULL,'fotboll,musik,politik,religion','2010-06-03 10:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (1, 'Henriks fjärde inlägg','Idre är ett toppenställe,Idre är ett toppenställe,Idre är ett toppenställe','http://www.idrefjall.se/bilder/342/fredrik_skidakning.jpg','sport,politik,mat,fotboll','2010-11-01 17:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (2, 'Petters första inlägg', 'första inlägget,första inlägget,första inlägget',NULL,NULL,'2010-10-04 10:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (2, 'Petters andra inlägg', 'andra inlägget,andra inlägget,andra inlägget',NULL,'fotboll','2010-11-04 14:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (2, 'Petters tredje inlägg', 'tredje inlägget,tredje inlägget,tredje inlägget',NULL,'konst,ekonomi,fotboll','2011-05-08 11:00');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (3, 'Tobias första inlägg', 'första inlägget,första inlägget,första inlägget',NULL,'politik,mat,religion,fotboll','2011-01-05 10:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (3, 'Tobias andra inlägg', 'andra inlägget,andra inlägget,andra inlägget',NULL,'fotboll,vänner,sport','2011-04-05 15:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (3, 'Tobias tredje inlägg','tredje inlägget,tredje inlägget,tredje inlägget,tredje inlägget',NULL,'politik,mat,fotboll','2011-04-15 17:10');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (4, 'Daniels första inlägg', 'första inlägget,första inlägget,första inlägget',NULL,'sport,konst,vänner,fotboll','2011-04-23 10:20');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (4, 'Daniels andra inlägg', 'andra inlägget,andra inlägget,andra inlägget',NULL,'religion,musik,fotboll','2011-04-26 18:18');
INSERT INTO {$tableComments}(Comment_idAuthor,titleComment,textComment,imageComment,tagsComment,dateComment)
VALUES (4, 'Daniels tredje inlägg', 'tredje inlägget,tredje inlägget,tredje inlägget','modules/blog/images/pennor.jpg','mat,politik,fotboll','2011-05-08 10:10');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (1, 'Pers första kommentar', 'Det var ju mer lek då,det var ju mer lek då','pelle@hotmail.com','2010-01-01 12:18');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (1, 'Pers andra kommentar', 'Var är spontan fotbollen,var är spontan fotbollen,var är spontan fotbollen','pelle@hotmail.com','2010-01-01 13:18');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (1, 'Nisses  kommentar', 'Zlatan var den förste svensk att vinna italenska skytteliga sedan Gunnar Nordahl.','nisse@hotmail.com','2010-01-01 13:28');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (3, 'Lars första kommentar', 'första kommentar,första kommentar,första kommentar,första kommentar','lasse@hotmail.com','2010-06-03 12:18');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (3, 'Lars andra kommentar', 'andra kommentaren,andra kommentaren,andra kommentaren','lasse@hotmail.com','2010-06-03 13:18');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (3, 'Pelles  kommentar', 'pelles kommentar,pelles kommentar','pelle@hotmail.com','2010-06-03 13:28');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (13, 'Lars  kommentar', 'lars kommentaren,lars kommentaren,lars kommentaren','lasse@hotmail.com','2011-05-08 18:19');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (13, 'Pelles  kommentar', 'pelles kommentar,pelles kommentar','pelle@hotmail.com','2011-05-08 20:28');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (7, 'Lars  kommentar', 'lars kommentaren,lars kommentaren,lars kommentaren','lasse@hotmail.com','2011-05-08 18:19');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (7, 'Pelles  kommentar', 'pelles kommentar,pelles kommentar','pelle@hotmail.com','2011-05-08 20:28');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (8, 'Pelles  kommentar', 'pelles kommentar,pelles kommentar','pelle@hotmail.com','2011-01-05 20:28');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (9, 'Pelles  kommentar', 'pelles kommentar,pelles kommentar','pelle@hotmail.com','2011-04-05 20:38');
INSERT INTO {$tableNotes}(Note_idComment,titleNote,textNote,emailNote,dateNote)
VALUES (11, 'Pelles  kommentar', 'pelles kommentar,pelles kommentar','pelle@hotmail.com','2011-04-23 10:28');
EOD;
//echo($query);
//die("stop execution");
$res = $mysqli->multi_query($query) or die("Could not query database");

// -------------------------------------------------------------------------------------------
//
// Retrieve and ignore the results from the above query
// Some may succed and some may fail. Lets count the number of succeded 
// statements to really know.
//
$statements = 0;
do {
  $res = $mysqli->store_result();
  $statements++;
} while($mysqli->more_results() && $mysqli->next_result());

// -------------------------------------------------------------------------------------------
//
// Prepare the text
//
$html = "<article><h2>Installera databas</h2>";
$html .= "<p>Query=<br/><pre>{$query}</pre>";
$html .= "<p>Antal lyckade statements: {$statements}</p>";
$html .= "<p>Error code: {$mysqli->errno} ({$mysqli->error})</p>";
$html .= "<p><a href='?p=visabloggar'>Hem</a></p></article>";

// -------------------------------------------------------------------------------------------
//
// Close the connection to the database
//
$mysqli->close();

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
//require_once(TP_SOURCEPATH.'CHTMLPage.php'); 

//$page = new CHTMLPage();
require_once(TP_MODULESBLOG.'src/CHTMLPage2.php');
//require_once(TP_MODULESBLOG.'stylesheet2.css');
$stylesheet ="modules/blog/stylesheet2.css" ;

$page = new CHTMLPage2($stylesheet);


$page->printHTMLHeader('Installation av databas');
$page->printPageHeader();
$page->printStartSection('commentandnotes');
$page->printPageBody($html);
$page->printCloseSection();
$page->printPageFooter();
?>
