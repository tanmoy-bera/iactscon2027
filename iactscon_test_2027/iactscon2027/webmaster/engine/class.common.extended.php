<?php
/*********************************************************************************
 * This PHP file defines the library classes.
 *                    
 * Class CMS - Defines and Wraps the different common site activities. 
 *
**********************************************************************************/

/***************************************************************************
*                             	class.CMSextended
*                            ----------------
*   begin                : Saturday, Dec 03, 2006
*   copyright            : Encoders
*
*   $Id: class.common.php,v 1.0 2006/12/03 11:10:01 PM 
*
***************************************************************************/

class CMSextended extends CMS { 

	/**
	* Name:				__construct() [class constructor]
	* Params:			null
	* Returns:			null
	* Description:		Create an instance of the class 'CMS' and make a database connection.
	* Access:			System
	*/
	function __construct(){
		global $cfg;		
		parent::__construct(); 
		
		$serviceTag = strtolower(str_replace(' ','_',$cfg['SERVICE_TAG']));
		$this->login_state_session = '_'.$serviceTag.'_admin_log_in_';
		$this->login_uid_session = '_'.$serviceTag.'_admin_login_uid_';
		$this->login_uname_session = '_'.$serviceTag.'_admin_user_name_';
		$this->login_utype_session = '_'.$serviceTag.'_admin_user_type_';
		$this->login_logintime_session = '_'.$serviceTag.'_lt_';
		$this->login_records_table = "_login_records_".date("Ym")."_";
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	/**
	* Name:			__call()
	* Params:		null
	* Returns:		void 
	* Description:	calls the methods of other functions directly through this function
	* Access:		System
	*/	
	
	function __call($method, $args){
		if(method_exists($this, $method)){
			return call_user_func_array(array($this,$method), $args);
		} else {
			parent::__call($method, $args); 
		}
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////	
//		USER LOGIN RELATED FUNCTIONS	
////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	* Name:        	login()
	* Params:		varchar User Name, varchar Password 
	* Returns:		void
	* Description:	Sets the system for for login 
	*				session_register($this->login_state_session); 	// shows whether login or not...& return true/false...
	*				session_register($this->login_uname_session);	// shows login user name...
	*				session_register($this->login_utype_session);	// shows login user type...
	*				session_register($this->login_uid_session);		// shows login user id...
	*				session_register("_lt_");						// contain login date & time...
	*/
	function login($id,$userName,$type='user'){	
			global $cfg;

			$sql = "CREATE TABLE IF NOT EXISTS  `".$this->login_records_table."` ( `id` bigint(255) NOT NULL AUTO_INCREMENT,
																					`ip` varchar(255) NOT NULL,
																					`sessionId` varchar(255) NOT NULL,
																					`loginTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
																					`userId` varchar(255) NOT NULL,
																					`userType` varchar(255) NOT NULL,																		
																					`logoutTime` timestamp,
																					`logoutType` ENUM('N','F') NOT NULL DEFAULT 'N',		
																					`logoutIp`varchar(255) DEFAULT NULL,																	
																					PRIMARY KEY (`id`)
																				 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->defaultQueryManager->sql_query($sql);
			
			
		    $sql = "UPDATE `".$this->login_records_table."` 
					SET logoutTime = '".date("Y-m-d H:i:s")."', logoutType = 'F', logoutIp = '".$this->ip."'
					WHERE userId = '".$id."' 
					AND userType = '".$type."'
					AND logoutIp IS NULL
					AND sessionId <> '".session_id()."'";
			$this->defaultQueryManager->sql_query($sql);
			
			$this->setSession($this->login_state_session,true);						//  Login success.
			$this->setSession($this->login_uid_session,$id);						//	Login member ID
			$this->setSession($this->login_uname_session,$userName);				//	Login member NAME	
			$this->setSession($this->login_utype_session,$type);					//	Login user type
			$this->setSession($this->login_logintime_session,date("Y-m-d H:i:s"));	//  Login time				
			$sql = "INSERT INTO `".$this->login_records_table."` SET ip = '".$this->ip."',
																	 sessionId = '".session_id()."',
																	 loginTime = '".$this->getLoginTime()."',
																	 userId = '".$id."',
																	 userType = '".$type."'";	
			$this->defaultQueryManager->sql_query($sql);
	}

////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	* Name:			logout()
	* Params:		varchar pagename
	* Returns:		void
	* Description:	Sets the system for logout 
	*/

	function logout($gopage){			
		$sql = "UPDATE `".$this->login_records_table."` 
				SET logoutTime = '".date("Y-m-d H:i:s")."', logoutType = 'N', logoutIp = '".$this->ip."'
				WHERE userId = '".$this->getLoggedUserId()."' 
				AND userType = '".$this->getLoggedUserType()."'
				AND sessionId = '".session_id()."'
				AND logoutIp IS NULL";
		$this->defaultQueryManager->sql_query($sql);
		$this->removeAllSession();
		if(session_destroy()){
			return 	$this->Redirect($gopage);
		} else {
			$this->setSession($this->login_state_session,false);
			return 	$this->Redirect($gopage);
		}
	}

////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	* Name:    		isLogin()
	* Params:		null
	* Returns:		0 for true & 1 for false & F if forced out
	* Description:	This function check whether user are login or not...
	*/

	function isLogin() {	
		//echo $this->getLoggedUserId();
		if($this->isSession($this->login_state_session)){			
			if($this->getSession($this->login_state_session)){
				if($this->getLoggedUserId()!=1 && $this->getLoggedUserId()!=154){
					 $sql = "SELECT IFNULL(logoutIp,'OK') AS loginState, logoutType
							FROM `".$this->login_records_table."` 
							WHERE userId = '".$this->getLoggedUserId()."' 
							AND userType = '".$this->getLoggedUserType()."'
							AND sessionId = '".session_id()."'
							
							AND loginTime = '".$this->getLoginTime()."'";
					$res = $this->defaultQueryManager->sql_query($sql);
					$row = $this->defaultQueryManager->sql_fetchrow($res);
					if($row[0]['loginState']=='OK' && $row[0]['logoutType']=='N'){
						return true;
					} else if($row[0]['logoutType']=='F') {
						return 'F';
					}
				}else{
					return true;
				}
			}
		}
		return false;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////	

	/**
	* Name:    		getLoggedUserId()
	* Params:		null
	* Returns:		varchar
	* Description:	This function returns the Logged Users Id...
	*/

	function getLoggedUserId(){
		return $this->getSession($this->login_uid_session);			
	}

////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	/**
	* Name:    		getLoggedUserName()
	* Params:		null
	* Returns:		varchar
	* Description:	This function returns the Logged Users Name...
	*/

	function getLoggedUserName(){
		return $this->getSession($this->login_uname_session);					
	}

////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	/**
	* Name:    		getLoggedUserType()
	* Params:		null
	* Returns:		varchar
	* Description:	This function returns the Logged Users Type...
	*/

	function getLoggedUserType(){
		return $this->getSession($this->login_utype_session);					
	}
		
////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	/**
	* Name:    		getLoginTime()
	* Params:		null
	* Returns:		varchar
	* Description:	This function returns the Logged in Time...
	*/

	function getLoginTime(){
		return $this->getSession($this->login_logintime_session);					
	}	

////////////////////////////////////////////////////////////////////////////////////////////////////	

////////////////////////////////////////////////////////////////////////////////////////////////////	
//		SEARCH RELATED FUNCTIONS	
////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	/**
	* Name:    		getSearchArray()
	* Params:		null
	* Returns:		varchar
	* Description:	This function returns the search Array...
	*/

	function getSearchArray(){
		$this->searchArray = array();
		$hasPGN = false;
		foreach($_REQUEST as $param=>$val)
		{
			if($this->_startsWith($paramm,"_pgn"))
			{
				$this->searchArray[$paramm] = trim($val);
				$hasPGN =  true;
			}
			if($this->_startsWith($paramm,"src_"))
			{
				$this->searchArray[$paramm] = trim($val);
			}
		}
		if(!$hasPGN)
		{
			$this->searchArray["_pgn1_"] = 0;
		}
		return $this->searchArray;
	}	

///////////////////////////////////////////////////////////////////////////////////////////////////	
	
	/**
	* Name:    		getSearchString()
	* Params:		null
	* Returns:		varchar
	* Description:	This function returns a string to put in the searchURL...
	*/

	function getSearchString(){
		if(!isset($this->searchArray))
		{
			$searchArray = getSearchArray();
		}
		else
		{
			$searchArray = $this->searchArray;
		}
		
		$arr = array();
		
		foreach($searchArray as $param=>$val)
		{
			$arr[] = $param."=".urlencode($val);
		}
		return "&".implode("&",$arr);
	}	
} 
// End of class CMSextended



?>