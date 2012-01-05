<?php
// ===========================================================================================
//
// SQLCreateUserAndGroupTables.php
//
// SQL statements to creta the tables for the User and group tables.
//
// WARNING: Do not forget to check input variables for SQL injections. 
//
// Author: Mikael Roos
//


// -------------------------------------------------------------------------------------------
//
// SQL for User & Group structure.
//
$tUser 			= DBT_User;
$tGroup 		= DBT_Group;
$tGroupMember 	= DBT_GroupMember;
$tStatistics    = DBT_Statistics;
$tArticle       =DBT_Article;
//$tTopic2Post    = DBT_Topic2Post;

// Get the SP/UDF/trigger names
$trInsertUser    = DBTR_TInsertUser;
// Get the SP names
$spSPGetAccountDetails= DBSP_SPGetAccountDetails;
$spSPChangeAccountInformation=DBSP_SPChangeAccountInformation;
$spSPCreateAccount=DBSP_SPCreateAccount;
$spSPCheckORAuthenticateAccount=DBSP_SPCheckORAuthenticateAccount;
$spSPGetMailAdressFromAccount=DBSP_SPGetMailAdressFromAccount;
$spSPPasswordResetGetKey=DBSP_SPPasswordResetGetKey;
$spSPPasswordResetActivate=DBSP_SPPasswordResetActivate;
// Get the UDF names
$udfFGetGravatarLinkFromEmail= DBUDF_FGetGravatarLinkFromEmail;
$udfFCreatePassword          = DBUDF_FCreatePassword;

$query = <<<EOD
-- SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS {$tGroupMember};
DROP TABLE IF EXISTS {$tUser};
DROP TABLE IF EXISTS {$tGroup};

--
-- Table for the User
--
CREATE TABLE {$tUser} (

  -- Primary key(s)
  idUser INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  accountUser CHAR(20) NOT NULL UNIQUE,
  emailUser CHAR(100) NULL UNIQUE,
  -- Attributes related to the password
  saltUser CHAR(10) NOT NULL,
  passwordUser CHAR(64) NOT NULL,
  methodUser CHAR(5) NOT NULL,
  -- Attributes related to resetting the password
  key3User CHAR(32) NULL UNIQUE,
  expireUser DATETIME NULL,
  -- Attributes for user profile info
  avatarUser CHAR(255) NULL,
  gravatarUser CHAR(100) NULL
);


--
-- Table for the Group
--
CREATE TABLE {$tGroup} (

  -- Primary key(s)
  idGroup CHAR(3) NOT NULL PRIMARY KEY,

  -- Attributes
  nameGroup CHAR(40) NOT NULL
);


--
-- Table for the GroupMember
--
CREATE TABLE {$tGroupMember} (

  -- Primary key(s)
  --
  -- The PK is the combination of the two foreign keys, see below.
  --
  
  -- Foreign keys
  GroupMember_idUser INT NOT NULL,
  GroupMember_idGroup CHAR(3) NOT NULL,
	
  FOREIGN KEY (GroupMember_idUser) REFERENCES {$tUser}(idUser),
  FOREIGN KEY (GroupMember_idGroup) REFERENCES {$tGroup}(idGroup),

  PRIMARY KEY (GroupMember_idUser, GroupMember_idGroup)
  
  -- Attributes

);
--
-- Table for the Statistics
--
DROP TABLE IF EXISTS {$tStatistics};
CREATE TABLE {$tStatistics} (

  -- Primary key(s)
  -- Foreign keys
  Statistics_idUser INT NOT NULL,
    
  FOREIGN KEY (Statistics_idUser) REFERENCES {$tUser}(idUser),
  PRIMARY KEY (Statistics_idUser),
  
  -- Attributes
  numOfArticlesStatistics INT NOT NULL DEFAULT 0
);
--
-- Create trigger for Statistics
-- Add row when new user is created
--
DROP TRIGGER IF EXISTS {$trInsertUser};
CREATE TRIGGER {$trInsertUser}
AFTER INSERT ON {$tUser}
FOR EACH ROW
BEGIN
  INSERT INTO {$tStatistics} (Statistics_idUser) VALUES (NEW.idUser);
END;
--
-- Table for the Articles
--
DROP TABLE IF EXISTS {$tArticle};
CREATE TABLE {$tArticle} (

  -- Primary key(s)
  idArticle INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Foreign keys
  Article_idUser INT NOT NULL,
  FOREIGN KEY (Article_idUser) REFERENCES {$tUser}(idUser),
  
  -- Attributes
  titleArticle VARCHAR(100) NOT NULL,
  textArticle VARCHAR(500) NOT NULL,
  dateArticle DATETIME NOT NULL,
   -- Attributes to enable draft, publish and autosaves
  draftTitleArticle VARCHAR(100) NULL,
  draftTextArticle VARCHAR(500) NULL,
  draftModifiedArticle DATETIME NULL,
  publishedArticle DATETIME NULL
);
-- SET FOREIGN_KEY_CHECKS=1;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Function to create a link to gravatar.com from an emailadress.
--
-- http://en.gravatar.com/site/implement/url
--
DROP FUNCTION IF EXISTS {$udfFGetGravatarLinkFromEmail};
CREATE FUNCTION {$udfFGetGravatarLinkFromEmail}
(
    aEmail CHAR(100),
    aSize INT
)
RETURNS CHAR(255)
DETERMINISTIC
CONTAINS SQL
BEGIN
    DECLARE link CHAR(255);

    SELECT CONCAT('http://www.gravatar.com/avatar/', MD5(LOWER(aEmail)), '.jpg?s=', aSize)
        INTO link;
    
    RETURN link;        
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Function to create a password from a salt, password and method.
--
DROP FUNCTION IF EXISTS {$udfFCreatePassword};
CREATE FUNCTION {$udfFCreatePassword}
(
aSalt CHAR(10),
aPassword CHAR(32),
aMethod CHAR(5)
)
RETURNS CHAR(64)
DETERMINISTIC
CONTAINS SQL
BEGIN
DECLARE password CHAR(64);
--
-- Switch on the method to be used
--
CASE TRIM(aMethod)
WHEN 'MD5' THEN SELECT md5(CONCAT(aSalt, aPassword)) INTO password;
WHEN 'SHA-1' THEN SELECT sha1(CONCAT(aSalt, aPassword)) INTO password;
WHEN 'SHA-2' THEN SELECT sha2(CONCAT(aSalt, aPassword),256) INTO password;
WHEN 'PLAIN' THEN SELECT aPassword INTO password;
END CASE;
RETURN password;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to show/display details of an account/user.
--
DROP PROCEDURE IF EXISTS $spSPGetAccountDetails;
CREATE PROCEDURE $spSPGetAccountDetails
(
IN aUserId INT
)
BEGIN
SELECT
U.accountUser AS account,
U.emailUser AS email,
U.avatarUser AS avatar,
U.gravatarUser AS gravatar,
{$udfFGetGravatarLinkFromEmail}(U.gravatarUser, 105) AS gravatarsmall,
G.idGroup AS groupakronym,
G.nameGroup AS groupdesc
FROM $tUser AS U
INNER JOIN {$tGroupMember} AS Gm
ON U.idUser = Gm.GroupMember_idUser
INNER JOIN {$tGroup} AS G
ON G.idGroup = Gm.GroupMember_idGroup
WHERE
U.idUser = aUserId
;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to change password for an account/user.
--
DROP PROCEDURE IF EXISTS $spSPChangeAccountInformation;
CREATE PROCEDURE $spSPChangeAccountInformation(
IN infNumber INT,
IN aUserId INT,
IN aInf CHAR(255)
)
BEGIN
CASE infNumber
    WHEN 1 THEN 
        UPDATE $tUser SET saltUser = UNIX_TIMESTAMP(NOW()),
        passwordUser = {$udfFCreatePassword}(saltUser, aInf, methodUser) WHERE idUser = aUserId LIMIT 1;
    WHEN 2 THEN 
        UPDATE $tUser SET emailUser= TRIM(aInf) WHERE idUser = aUserId LIMIT 1;
    WHEN 3 THEN 
        UPDATE $tUser SET avatarUser=TRIM(aInf) WHERE idUser = aUserId LIMIT 1;
    WHEN 4 THEN 
        UPDATE $tUser SET gravatarUser=TRIM(aInf) WHERE idUser = aUserId LIMIT 1;    
END CASE;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get mail adress of an account/user.
--
-- aAccountOrMail: Be the account name or a mailadress.
-- aAccount: The accountname that matched.
-- aMail: The resulting mail, if found(sucess), else empty(failed).
-- 
DROP PROCEDURE IF EXISTS $spSPGetMailAdressFromAccount;
CREATE PROCEDURE $spSPGetMailAdressFromAccount
(
    IN aAccountOrMail CHAR(100),
    OUT aAccount CHAR(32),
    OUT aMail CHAR(100)
)
BEGIN
    -- Is it an accountname or mail?
    SELECT
        accountUser, emailUser INTO aAccount, aMail 
    FROM pe_User 
    WHERE 
        accountUser = aAccountOrMail OR
        emailUser = aAccountOrMail;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to initiate password reset by saving a key with the user.
-- By sending this same key to pe_SPPasswordResetActivate will allow to
-- reset the password.
--
-- aKey should have a value initiated by the caller (key1).
-- The procedure creates a new key (key2) and uses these two keys to generate a third key (key3). 
-- Key3 is stored in the user table.
-- Key2 is put in the aKey OUT variable. Both key1 and key2 are later needed to carry out the 
-- password reset action using pe_SPPasswordResetActivate.
--
-- A procedure could work like this:
-- Create key1 in PHP using some random value and create a MD5 hash from it.
-- The procedure creates key2 using similare techniques.
-- Key3 is created by hashing a combination of key1 and key2.
-- Key3 is stored in the database.
-- Key2 is sent to the user via mail.
-- Key1 is stored in the webbapplications session.
-- The user takes key2 from the mail and inputs it in a form. 
-- The webbapplikation takes key2 from the form and key1 from the session and sends it as 
-- input to the procedure pe_SPPasswordResetActivate which resets the password.
--
-- I'm not really sure on the advantages with this but if feels better than just using 1 key
-- and sending it in plain text to the user. This could, of course, be further evaluated. 
--
DROP PROCEDURE IF EXISTS $spSPPasswordResetGetKey;
CREATE PROCEDURE $spSPPasswordResetGetKey
(
    IN aAccountUser CHAR(32),
    INOUT aKey CHAR(32)
)
BEGIN
    DECLARE key1 CHAR(32);
    DECLARE key2 CHAR(32);
    DECLARE key3 CHAR(32);
    
    SET key1 = aKey;
    SET key2 = MD5(UNIX_TIMESTAMP(NOW()));
    SET key3 = MD5(CONCAT(key1,key2));
    SET aKey = key2;
    
    UPDATE 
        pe_User
    SET 
        key3User         = key3,
        expireUser     = ADDTIME(NOW(), '01:00:00')
    WHERE
        accountUser = aAccountUser
    LIMIT 1;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to carry out a password reset request. It is really a alternative way of loggin in
-- and authenticating a user based on a token.
--
-- aAccountId number = Success
-- aAccountId null = Failed
--
DROP PROCEDURE IF EXISTS $spSPPasswordResetActivate;
CREATE PROCEDURE $spSPPasswordResetActivate
(
    OUT aAccountId INT,
    OUT aAccountName CHAR(32),
    IN aKey1 CHAR(32),
    IN aKey2 CHAR(32)
)
BEGIN
    DECLARE key1 CHAR(32);
    DECLARE key2 CHAR(32);
    DECLARE key3 CHAR(32);
    
    SET key1 = aKey1;
    SET key2 = aKey2;
    SET key3 = MD5(CONCAT(key1,key2));

    -- Find the key
    SELECT 
        idUser, accountUser INTO aAccountId, aAccountName
    FROM 
        pe_User
    WHERE
        key3User         = key3 AND
        expireUser     > NOW();
        
    -- Clean up and set correct error messages
    IF aAccountId IS NOT NULL THEN
    BEGIN
        -- Reset the key
        UPDATE 
            pe_User
        SET 
            key3User         = NULL,
            expireUser     = NULL
        WHERE
            idUser = aAccountId
        LIMIT 1;
    END;
    END IF;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to create an account/user.
--
DROP PROCEDURE IF EXISTS $spSPCreateAccount;
CREATE PROCEDURE $spSPCreateAccount
(
OUT aUserId INT,
IN aUserAccount CHAR(20),
IN aPassword CHAR(32),
IN aMethod CHAR(5),
OUT aStatus INT
)
BEGIN
DECLARE salt CHAR(10);
--
-- Insert the user account
--
SELECT UNIX_TIMESTAMP(NOW()) INTO salt;
INSERT INTO {$tUser}
(accountUser, saltUser, passwordUser, methodUser, avatarUser)
VALUES
(aUserAccount, salt, {$udfFCreatePassword}(salt, aPassword, aMethod), aMethod, 'img/man_60x60.png')
;




SET aUserId = LAST_INSERT_ID();
--
-- Insert default group memberships
--
INSERT INTO {$tGroupMember}
(GroupMember_idUser, GroupMember_idGroup)
VALUES
(aUserId, 'usr')
;
SET aStatus = 0; -- SUCCESS
END;
--
-- SP to authenticate an account/user.
--
DROP PROCEDURE IF EXISTS $spSPCheckORAuthenticateAccount;
CREATE PROCEDURE $spSPCheckORAuthenticateAccount
(
OUT aUserId INT,
IN  aAuthNr INT,
IN aUserAccountOrEmail CHAR(100),
IN aPassword CHAR(32),
OUT aStatus INT
)
BEGIN
IF aAuthNr=1 THEN
--
-- Check that account
--
SELECT
idUser INTO aUserId
FROM $tUser
WHERE
accountUser = aUserAccountOrEmail;
ELSEIF aAuthNr=2 THEN
--
-- Check that account and passwords match
--
SELECT
idUser INTO aUserId
FROM $tUser
WHERE
 (accountUser        = aUserAccountOrEmail
 OR
  emailUser            = aUserAccountOrEmail)AND
        passwordUser	= {$udfFCreatePassword}(saltUser, aPassword, methodUser);
END IF;
IF aUserId IS NULL THEN
SET aStatus = 1; -- FAILED, the account does not exists or passwords does not match.
ELSE
SET aStatus = 0; -- SUCCESS
END IF;
END;
--
-- Add default groups
--
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');
EOD;
$account = 'morgan';
$password = 'hemligt';
$mail = "morhag89@hotmail.com";
$avatar = "http://www.student.bth.se/~mohk10/dbwebb2/persia/img/larssonavatar.jpg";
$gravatar="morhag89@hotmail.com";
$usemail=2;
$useavatar=3;
$usegravatar=4;
$passwordhashing=DB_APASSWORDHASHING;

$query .= <<<EOD
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default user(s)
--
CALL $spSPCreateAccount(@aUserId, '{$account}', '{$password}','{$passwordhashing}', @aStatus);
CALL $spSPChangeAccountInformation($usemail,@aUserId, '{$mail}');
CALL $spSPChangeAccountInformation($useavatar,@aUserId, '{$avatar}');
CALL $spSPChangeAccountInformation($usegravatar,@aUserId, '{$gravatar}');
UPDATE {$tGroupMember} SET GroupMember_idGroup='adm' WHERE GroupMember_idUser=1;
EOD;

$account = 'doe';
$password = 'doe';
$mail = "doe@bth.se";
$avatar = "http://www.student.bth.se/~mohk10/dbwebb2/persia/img/larssonavatar.jpg";

$query .= <<<EOD

-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Add default user(s)
--
CALL $spSPCreateAccount(@aUserId, '{$account}', '{$password}','{$passwordhashing}', @aStatus);
CALL $spSPChangeAccountInformation($usemail,@aUserId, '{$mail}');
CALL $spSPChangeAccountInformation($useavatar,@aUserId, '{$avatar}');
EOD;
?>