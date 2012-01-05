<?php
// ===========================================================================================
//
// File: SQLCoreFile.php
//
// Description: SQL statements for storing files.
//
// Author: Mikael Roos, mos@bth.se
//

// Get (or create) an instance of the database object.
//$db = CDatabaseController::GetInstance();
$tFile=DBT_File;
$tUser 			= DBT_User;
$tArticle=DBT_Article;
$cCSizeFileName=CSizeFileName;
$cCSizeFileNameUnique=CSizeFileNameUnique;
$cCSizePathToDisk=CSizePathToDisk;
$cCSizeMimetype=CSizeMimetype;
$cCDefaultCharacterSet=CDefaultCharacterSet;
$cCDefaultCollate=CDefaultCollate;
$SPInsertFile=DBSP_SPInsertFile;
$SPListFiles=DBSP_SPListFiles;
$SPListFiles2=DBSP_SPListFiles2;
$SPFileDetails=DBSP_SPFileDetails;
$SPFileDetailsUpdate=DBSP_SPFileDetailsUpdate;
$SPFileDetailsDeleted=DBSP_SPFileDetailsDeleted;
$udfFFileCheckPermission=DBUDF_FFileCheckPermission;
$udfFCheckUserIsOwnerOrAdmin2=DBUDF_FCheckUserIsOwnerOrAdmin2;
// Create the query
$query = <<<EOD

-- =============================================================================================
--
-- SQL for File
--
-- =============================================================================================


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for File
--
-- uniqueNameFile must be unique in combination with the userid.
--
DROP TABLE IF EXISTS {$tFile};
CREATE TABLE {$tFile} (

    -- Primary key(s)
    idFile INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    
    -- Foreign keys
    File_idUser INT NOT NULL,
    FOREIGN KEY (File_idUser) REFERENCES {$tUser}(idUser),
    File_idArticle INT NOT NULL,
    FOREIGN KEY (File_idArticle) REFERENCES {$tArticle}(idArticle),
    
    -- Attributes
    nameFile VARCHAR({$cCSizeFileName}) NOT NULL,
    uniqueNameFile VARCHAR({$cCSizeFileNameUnique}) NOT NULL,
    pathToDiskFile VARCHAR({$cCSizePathToDisk}) NOT NULL,
    sizeFile INT NOT NULL,
    mimetypeFile VARCHAR({$cCSizeMimetype}) NOT NULL,
    createdFile DATETIME NOT NULL,
    modifiedFile DATETIME NULL,
    deletedFile DATETIME NULL,

    -- Index
    INDEX (uniqueNameFile)

) ENGINE MyISAM CHARACTER SET {$cCDefaultCharacterSet} COLLATE {$cCDefaultCollate};


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to insert new file
--
DROP PROCEDURE IF EXISTS {$SPInsertFile};
CREATE PROCEDURE {$SPInsertFile}
(
    IN aUserId INT,
    IN aArticleId INT,
    IN aFilename VARCHAR({$cCSizeFileName}), 
    IN aUniqueFilename VARCHAR({$cCSizeFileNameUnique}), 
    IN aPathToDisk VARCHAR({$cCSizePathToDisk}), 
    IN aSize INT,
    IN aMimetype VARCHAR({$cCSizeMimetype})
)
BEGIN
    INSERT INTO {$tFile}    
        (File_idUser,File_idArticle, nameFile, uniqueNameFile, pathToDiskFile, sizeFile, mimetypeFile, createdFile) 
        VALUES 
        (aUserId,aArticleId, aFilename, aUniqueFilename, aPathToDisk, aSize, aMimetype, NOW());
END;

-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to list all files
--
DROP PROCEDURE IF EXISTS {$SPListFiles};
CREATE PROCEDURE {$SPListFiles}
(
    IN aUserId INT UNSIGNED
)
BEGIN
    SELECT 
        File_idUser AS owner,
        File_idArticle AS article,
        nameFile AS name, 
        uniqueNameFile AS uniquename,
        pathToDiskFile AS path, 
        sizeFile AS size, 
        mimetypeFile AS mimetype, 
        createdFile AS created,
        modifiedFile AS modified,
        deletedFile AS deleted
    FROM {$tFile}
    WHERE
        File_idUser = aUserId AND
        deletedFile IS NULL;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to list all files 2
--
DROP PROCEDURE IF EXISTS {$SPListFiles2};
CREATE PROCEDURE {$SPListFiles2}
(
    IN aArticleId INT
)
BEGIN
    SELECT 
        File_idUser AS owner,
        File_idArticle AS article,
        nameFile AS name, 
        uniqueNameFile AS uniquename,
        pathToDiskFile AS path, 
        sizeFile AS size, 
        mimetypeFile AS mimetype, 
        createdFile AS created,
        modifiedFile AS modified,
        deletedFile AS deleted
    FROM {$tFile}
    WHERE
        File_idArticle = aArticleId AND
        deletedFile IS NULL;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Function to check if file exists and if user has permissions to us it.
-- Return values:
--  0 success
--  1 no permission to update file ({'FFileCheckPermissionMessages'][1]})
--  2 file does not exists  ({'FFileCheckPermissionMessages'][2]})
--
DROP FUNCTION IF EXISTS $udfFFileCheckPermission; 
CREATE FUNCTION $udfFFileCheckPermission
(
    aFileId INT,
    aUserId INT
)
RETURNS TINYINT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE i1 TINYINT;
    DECLARE i2 TINYINT;
    -- Does file exists?
    SELECT idFile INTO i1 FROM $tFile WHERE idFile = aFileId;
    IF i1 IS NOT NULL THEN
    BEGIN
    -- File exists and user have permissions to update file? 
        SELECT idFile INTO i2 FROM $tFile 
            WHERE 
                idFile=i1 AND (
                $udfFCheckUserIsOwnerOrAdmin2(0,aUserId) OR
                File_idUser = aUserId );
        IF i2 IS NOT NULL THEN
            RETURN 0;
        ELSE
        -- So, file exists but user has no permissions to use/update file.
            RETURN 1;
        END IF;
        END;
    ELSE 
        RETURN 2;
    END IF;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to show details of a file
--
DROP PROCEDURE IF EXISTS $SPFileDetails;
CREATE PROCEDURE $SPFileDetails
(
    IN aUserId INT,
    IN aUniqueFilename VARCHAR(13),
    OUT aSuccess TINYINT    
)
BEGIN
    DECLARE fileid INT;
    
    -- Get the id of the file
    SELECT idFile INTO fileid FROM $tFile
    WHERE
        uniqueNameFile = aUniqueFilename;

    -- Check permissions
    SELECT $udfFFileCheckPermission(fileid, aUserId) INTO aSuccess;
        
    -- Get details from file if aSuccess=0
    IF aSuccess=0 THEN
    SELECT 
        idFile AS fileid, 
        File_idUser AS owner,
        File_idArticle AS article,
        nameFile AS name, 
        uniqueNameFile AS uniquename,
        pathToDiskFile AS path, 
        sizeFile AS size, 
        mimetypeFile AS mimetype, 
        createdFile AS created,
        modifiedFile AS modified,
        deletedFile AS deleted
    FROM $tFile
    WHERE
        uniqueNameFile = aUniqueFilename;
        END IF;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to update details of a file
-- The userid sent in must be owner or admin to be able to change the file details.
-- See funktion {FFileCheckPermission']} for return values.
--
DROP PROCEDURE IF EXISTS $SPFileDetailsUpdate;
CREATE PROCEDURE $SPFileDetailsUpdate
(
    IN aFileId INT,
    IN aUserId INT,
    IN aFilename VARCHAR(256), 
    IN aMimetype VARCHAR(127),
    OUT aSuccess TINYINT UNSIGNED
)
BEGIN
    -- Check permissions
    SELECT $udfFFileCheckPermission(aFileId, aUserId) INTO aSuccess;
    
    -- Do the update if aSuccess=0
    IF aSuccess=0 THEN
    UPDATE $tFile
    SET
        nameFile             = aFilename,
        mimetypeFile     = aMimetype,
        modifiedFile    = NOW()
    WHERE 
        idFile = aFileId;
        END IF;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to toggle delete/not deleted file
-- aDeleteOrRestore 1 (delete), 2 (no delete)
-- See funktion {FFileCheckPermission} for return values.
--
DROP PROCEDURE IF EXISTS $SPFileDetailsDeleted;
CREATE PROCEDURE $SPFileDetailsDeleted
(
    IN aFileId INT,
    IN aUserId INT,
    IN aDeleteOrRestore INT,
    OUT aSuccess TINYINT    
)
wrap: BEGIN
    DECLARE value DATETIME;
    
    -- Check permissions
    SELECT $udfFFileCheckPermission(aFileId, aUserId) INTO aSuccess;

    -- Set the value to be updated, depends on aDeleteOrRestore
    CASE aDeleteOrRestore 
        WHEN 1 THEN SET value = NOW();
        WHEN 2 THEN SET value = NULL;
        ELSE LEAVE wrap;
    END CASE;
    
    -- Do the update if aSuccess=0
    IF aSuccess=0 THEN
    UPDATE $tFile
    SET
        deletedFile    = value
    WHERE 
        idFile = aFileId;
        END IF;
END wrap;
EOD;

/*
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Function to check if file exists and if user has permissions to us it.
-- Return values:
--  0 success
--  1 no permission to update file ({$db->_['FFileCheckPermissionMessages'][1]})
--  2 file does not exists  ({$db->_['FFileCheckPermissionMessages'][2]})
--
DROP FUNCTION IF EXISTS {$db->_['FFileCheckPermission']};
CREATE FUNCTION {$db->_['FFileCheckPermission']}
(
    aFileId INT UNSIGNED,
    aUserId INT UNSIGNED
)
RETURNS TINYINT UNSIGNED
BEGIN
    DECLARE i TINYINT UNSIGNED;
    
    -- File exists and user have permissions to update file?
    SELECT idFile INTO i FROM {$db->_['File']} 
    WHERE 
        idFile = aFileId AND
        (
            {$db->_['FCheckUserIsAdmin']}(aUserId) OR
            File_idUser = aUserId
        );
    IF i IS NOT NULL THEN
        RETURN 0;
    END IF;    

    -- Does file exists?
    SELECT idFile INTO i FROM {$db->_['File']} WHERE idFile = aFileId;
    IF i IS NULL THEN
        RETURN 2;
    END IF;

    -- So, file exists but user has no permissions to use/update file.
    RETURN 1;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to show details of a file
--
DROP PROCEDURE IF EXISTS {$db->_['PFileDetails']};
CREATE PROCEDURE {$db->_['PFileDetails']}
(
    IN aUserId INT UNSIGNED,
    IN aUniqueFilename VARCHAR({$db->_['CSizeFileNameUnique']}),
    OUT aSuccess TINYINT UNSIGNED    
)
BEGIN
    DECLARE fileid INT UNSIGNED;
    
    -- Get the id of the file
    SELECT idFile INTO fileid FROM {$db->_['File']}
    WHERE
        uniqueNameFile = aUniqueFilename AND
        File_idUser = aUserId;

    -- Check permissions
    SELECT {$db->_['FFileCheckPermission']}(fileid, aUserId) INTO aSuccess;
        
    -- Get details from file
    SELECT 
        idFile AS fileid, 
        File_idUser AS owner, 
        nameFile AS name, 
        uniqueNameFile AS uniquename,
        pathToDiskFile AS path, 
        sizeFile AS size, 
        mimetypeFile AS mimetype, 
        createdFile AS created,
        modifiedFile AS modified,
        deletedFile AS deleted
    FROM {$db->_['File']}
    WHERE
        uniqueNameFile = aUniqueFilename AND
        File_idUser = aUserId;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to update details of a file
-- The userid sent in must be owner or admin to be able to change the file details.
-- See funktion {$db->_['FFileCheckPermission']} for return values.
--
DROP PROCEDURE IF EXISTS {$db->_['PFileDetailsUpdate']};
CREATE PROCEDURE {$db->_['PFileDetailsUpdate']}
(
    IN aFileId INT UNSIGNED,
    IN aUserId INT UNSIGNED,
    IN aFilename VARCHAR({$db->_['CSizeFileName']}), 
    IN aMimetype VARCHAR({$db->_['CSizeMimetype']}),
    OUT aSuccess TINYINT UNSIGNED
)
BEGIN
    -- Check permissions
    SELECT {$db->_['FFileCheckPermission']}(aFileId, aUserId) INTO aSuccess;
    
    -- Do the update
    UPDATE {$db->_['File']}
    SET
        nameFile             = aFilename,
        mimetypeFile     = aMimetype,
        modifiedFile    = NOW()
    WHERE 
        idFile = aFileId AND
        (
            {$db->_['FCheckUserIsAdmin']}(aUserId) OR
            File_idUser = aUserId
        );
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to toggle delete/not deleted file
-- aDeleteOrRestore 1 (delete), 2 (no delete)
-- See funktion {$db->_['FFileCheckPermission']} for return values.
--
DROP PROCEDURE IF EXISTS {$db->_['PFileDetailsDeleted']};
CREATE PROCEDURE {$db->_['PFileDetailsDeleted']}
(
    IN aFileId INT UNSIGNED,
    IN aUserId INT UNSIGNED,
    IN aDeleteOrRestore INT UNSIGNED,
    OUT aSuccess TINYINT UNSIGNED    
)
wrap: BEGIN
    DECLARE value DATETIME;
    
    -- Check permissions
    SELECT {$db->_['FFileCheckPermission']}(aFileId, aUserId) INTO aSuccess;

    -- Set the value to be updated, depends on aDeleteOrRestore
    CASE aDeleteOrRestore 
        WHEN 1 THEN SET value = NOW();
        WHEN 2 THEN SET value = NULL;
        ELSE LEAVE wrap;
    END CASE;
    
    -- Do the update
    UPDATE {$db->_['File']}
    SET
        deletedFile    = value
    WHERE 
        idFile = aFileId AND
        (
            {$db->_['FCheckUserIsAdmin']}(aUserId) OR
            File_idUser = aUserId
        );
END wrap;


EOD;*/

?>
