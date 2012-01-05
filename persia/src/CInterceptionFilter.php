<?php
// ===========================================================================================
//
// Class CInterceptionFilter
//
// Used in each pagecontroller to check access, authority.
//
//
// Author: Mikael Roos
//


class CInterceptionFilter {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {
		;
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
	// Check if index.php (frontcontroller) is visited, disallow direct access to 
	// pagecontrollers
	//
	public function FrontControllerIsVisitedOrDie() {
		
		global $gPage; // Always defined in frontcontroller
		
		if(!isset($gPage)) {
			die('No direct access to pagecontroller is allowed.');
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if user has signed in or redirect user to sign in page
	//
	public function UserIsSignedInOrRedirectToSignIn() {
		
		if(!isset($_SESSION['accountUser'])) {
		
			 require(TP_PAGESPATH . 'login/PLogin.php');
		}
	}


	// ------------------------------------------------------------------------------------
	//
	// Check if index.php (frontcontroller) is visited, disallow direct access to 
	// pagecontrollers
	//
	public function UserIsMemberOfGroupAdminOrDie() {
		
		// User must be member of group adm or die
		if($_SESSION['groupMemberUser'] != 'adm') 
			die('You do not have the authourity to access this page');
	}
	// ------------------------------------------------------------------------------------
    //
    // Check if user belongs to the admin group or is a specific user.
    //
    public function IsUserMemberOfGroupAdminOrIsCurrentUser($aUserId) {
        
        $isAdmGroup         = (isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') ? TRUE : FALSE;
        $isCurrentUser    = (isset($_SESSION['idUser']) && $_SESSION['idUser'] == $aUserId) ? TRUE : FALSE;

        return $isAdmGroup || $isCurrentUser;
    }
// ------------------------------------------------------------------------------------
    //
    // Custom defined filter.
    // This method enables a custom filter by setting the $aLabel in the session.
    //
    // $aLabel: The label to set in the SESSION.
    // $aAction: check | set | unset
    //
    public static function CustomFilterIsSetOrDie($aLabel, $aAction='check') {

        switch($aAction) {

            case 'set': {
                $_SESSION[$aLabel] = $aLabel;            
            } break;

            case 'unset': {
                unset($_SESSION[$aLabel]);
            } break;
        
            case 'check':
            default: {
                isset($_SESSION[$aLabel]) 
                    or die('User defined filter not enabled. No access to this page.');
            } break;

        }
    }


} // End of Of Class

?>