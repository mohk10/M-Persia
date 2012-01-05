<?php
// ===========================================================================================
//
// SUserLogin.php
//
// Authenticate a user
//

$tUser 			= DBT_User; 
$tGroup 		= DBT_Group;
$tGroupMember 	= DBT_GroupMember;

$query .= <<< EOD
SELECT 
	idUser, 
	accountUser,
	GroupMember_idGroup
FROM {$tUser} AS U
	INNER JOIN {$tGroupMember} AS GM
		ON U.idUser = GM.GroupMember_idUser
WHERE
	accountUser		= '{$user}' AND
	passwordUser 	= md5('{$password}')
;
EOD;

?>