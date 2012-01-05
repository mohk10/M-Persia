<?php
// ===========================================================================================
//
// SUserDetails.php
//
// Show details about a user account
//

$tUser 			= DBT_User; 
$tGroup 		= DBT_Group;
$tGroupMember 	= DBT_GroupMember;

$query .= <<< EOD
SELECT 
	idUser, 
	accountUser,
	emailUser,
	idGroup,
	nameGroup
FROM {$tUser} AS U
	INNER JOIN {$tGroupMember} AS GM
		ON U.idUser = GM.GroupMember_idUser
	INNER JOIN {$tGroup} AS G
		ON G.idGroup = GM.GroupMember_idGroup
WHERE
	accountUser	= '{$user}'
;
EOD;

?>