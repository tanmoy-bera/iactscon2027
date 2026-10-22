<?php
	include_once('includes/init.php');
	include_once('../includes/backend.function.pages.php');
	$loggedUserID = $mycms->getLoggedUserId();
	switch($action)
	{		
		case'insert':	
		$ip     = $_REQUEST['newIp'];
	
		
		$sql	=	array();
		$sql['QUERY']	=	"INSERT INTO "._DB_IP_."
								SET 
									`ip` = ?,
									`created_ip` = ?,
									`created_sessionId` = ?,
									`status`= ?";
									
									
		$sql['PARAM'][]	=	array('FILD' => 'ip' , 				                'DATA' => $ip , 						                    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'created_ip' , 						'DATA' => $_SERVER['REMOTE_ADDR'] , 						'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'created_sessionId' , 				'DATA' => session_id() , 									'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'status' , 							'DATA' => 'A' , 											'TYP' => 's');
	
		$mycms->sql_insert($sql);			
		$mycms->redirect('validate_ip.php?m=1');
		break;	
		
		case'delFeatures':
			deletePageProcess($cfg, $mycms);
			break;		
	}
	
	
	/**************************************************************/
	/*                     WEB PAGE DELETE PROCESS                */
	/**************************************************************/
	function deletePageProcess($cfg, $mycms){
		$loggedUserID = $mycms->getLoggedUserId();
		
		$sql="DELETE  FROM " ._DB_IP_." WHERE `id` = ".$_REQUEST['id']."";
		
		$mycms->sql_query($sql);
		$mycms->redirect('validate_ip.php?m=3');
		exit();
	}
	
?>
