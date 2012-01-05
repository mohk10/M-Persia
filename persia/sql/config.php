<?php
// ===========================================================================================
//
// config.php
//
// Config-file for database and SQL related issues. All SQL-statements are usually stored in this
// directory (TP_SQLPATH). This files contains global definitions for table names and so.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Define the names for the database (tables, views, procedures, functions, triggers)
//
define('DBT_User', 			DB_PREFIX . 'User');
define('DBT_Group', 		DB_PREFIX . 'Group');
define('DBT_GroupMember',	DB_PREFIX . 'GroupMember');
define('DBT_Statistics',    DB_PREFIX . 'Statistics');
define('DBT_Article',        DB_PREFIX . 'Article');
define('DBT_Topic',                DB_PREFIX . 'Topic');
define('DBT_Topic2Post',    DB_PREFIX . 'Topic2Post');

// Stored procedures
define('DBSP_SPCreateNewArticle',    DB_PREFIX . 'SPCreateNewArticle');
define('DBSP_SPDisplayArticle',                DB_PREFIX . 'SPDisplayArticle');
define('DBSP_SPUpdateArticle',            DB_PREFIX . 'SPUpdateArticle');
define('DBSP_SPListArticles',                DB_PREFIX . 'SPListArticles');
define('DBSP_SPGetTopicList',                                        DB_PREFIX . 'SPGetTopicList');
define('DBSP_SPGetTopicDetails',                                    DB_PREFIX . 'SPGetTopicDetails');
define('DBSP_SPGetTopicDetailsAndPosts',                    DB_PREFIX . 'SPGetTopicDetailsAndPosts');
define('DBSP_SPGetPostDetails',                                    DB_PREFIX . 'SPGetPostDetails');
define('DBSP_SPInitialPostPublish',                                    DB_PREFIX . 'SPInitialPostPublish');
define('DBSP_SPInsertOrUpdatePost',                            DB_PREFIX . 'SPInsertOrUpdatePost');
define('DBSP_SPGetAccountDetails', DB_PREFIX . 'SPGetAccountDetails');
define('DBSP_SPChangeAccountInformation', DB_PREFIX . 'SPChangeAccountInformation');
define('DBSP_SPCreateAccount', DB_PREFIX . 'SPCreateAccount');
define('DBSP_SPCheckORAuthenticateAccount',  DB_PREFIX . 'SPCheckORAuthenticateAccount');
define('DBSP_SPGetMailAdressFromAccount',    DB_PREFIX . 'SPGetMailAdressFromAccount');
define('DBSP_SPPasswordResetGetKey'     ,    DB_PREFIX . 'SPPasswordResetGetKey');
define('DBSP_SPPasswordResetActivate'   ,    DB_PREFIX . 'SPPasswordResetActivate');
// $spSPGetAccountDetails= DBSP_SPGetAccountDetails;
//$spSPChangeAccountInformation=DBSP_SPChangeAccountInformation;
//$spSPCreateAccount=DBSP_SPCreateAccount;
//$spSPCheckORAuthenticateAccount=DBSP_SPCheckORAuthenticateAccount;

// User Defined Functions UDF
define('DBUDF_FCheckUserIsOwnerOrAdmin2',    DB_PREFIX . 'FCheckUserIsOwnerOrAdmin2');
define('DBUDF_FGetGravatarLinkFromEmail',    DB_PREFIX . 'FGetGravatarLinkFromEmail');
define('DBUDF_FCreatePassword'          ,    DB_PREFIX . 'FCreatePassword');
// Triggers
define('DBTR_TInsertUser',        DB_PREFIX . 'TInsertUser');
define('DBTR_TAddArticle',        DB_PREFIX . 'TAddArticle');
define('DBTR_TSubArticle',        DB_PREFIX . 'TSubArticle');
// -------------------------------------------------------------------------------------------
//
// Module Core
//
define('CDefaultCharacterSet', 'utf8');
define('CDefaultCollate','utf8_unicode_ci');
// Module Core: File
//
define('CSizeFileName'         , 256);
define('CSizeFileNameUnique'         ,13); // Smallest size of PHP uniq().
define('CSizePathToDisk'                 ,256);
    
// Max 127 chars according http://tools.ietf.org/html/rfc4288#section-4.2
define('CSizeMimetype'                 , 127);
    
define('DBT_File', DB_PREFIX . 'File');
define('DBSP_SPInsertFile', DB_PREFIX . 'SPInsertFile');
define('DBSP_SPFileDetails', DB_PREFIX . 'SPFileDetails');
define('DBSP_SPFileDetailsUpdate',DB_PREFIX . 'SPFileDetailsUpdate');
define('DBSP_SPListFiles', DB_PREFIX . 'SPListFiles');
define('DBSP_SPListFiles2', DB_PREFIX . 'SPListFiles2');
define('DBSP_SPFileDetailsDeleted',DB_PREFIX . 'SPFileDetailsDeleted');

    // Check permissions and success values
define('DBUDF_FFileCheckPermission', DB_PREFIX . 'FFileCheckPermission');
//define('FFileCheckPermissionMessages1', $pc->lang['FILE_NO_PERMISSION']);
//define('FFileCheckPermissionMessages2',$pc->lang['FILE_DOES_NOT_EXISTS']);

?>