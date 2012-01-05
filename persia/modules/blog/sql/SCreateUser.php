<?php
// ===========================================================================================
//
// SCreateUser.php
//
// SQL to create user & usergroup-related tables.
//

$tUser 			= DBT_User; 
$tGroup 		= DBT_Group;
$tGroupMember 	= DBT_GroupMember;

$defaultCharsetAndCollate = "DEFAULT CHARACTER SET utf8 COLLATE utf8_bin";
$charCharsetAndCollate = ""; // Use for datatype CHAR to save space

$query .= <<<EOD

-- -------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS {$tUser};
DROP TABLE IF EXISTS {$tGroup};
DROP TABLE IF EXISTS {$tGroupMember};

--
-- Table for the User
--
CREATE TABLE {$tUser} (

  -- Primary key(s)
  idUser INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  -- Attributes
  accountUser CHAR(20) NOT NULL UNIQUE,
  emailUser CHAR(100) NOT NULL,
  passwordUser CHAR(32) NOT NULL
  
) {$defaultCharsetAndCollate};


--
-- Table for the Group
--
CREATE TABLE {$tGroup} (

  -- Primary key(s)
  idGroup CHAR(3) NOT NULL PRIMARY KEY,

  -- Attributes
  nameGroup CHAR(40) NOT NULL
  
) {$defaultCharsetAndCollate};


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

) {$defaultCharsetAndCollate};

--
-- Add default user(s) 
--
INSERT INTO {$tUser} (accountUser, emailUser, passwordUser)
VALUES ('mikael', 'mos@bth.se', md5('hemligt'));
INSERT INTO {$tUser} (accountUser, emailUser, passwordUser)
VALUES ('doe', 'doe@bth.se', md5('doe'));

--
-- Add default groups
--
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
INSERT INTO {$tGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');

--
-- Add default groupmembers
--
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'doe'), 'usr');
INSERT INTO {$tGroupMember} (GroupMember_idUser, GroupMember_idGroup) 
VALUES ((SELECT idUser FROM {$tUser} WHERE accountUser = 'mikael'), 'adm');

EOD;


?>