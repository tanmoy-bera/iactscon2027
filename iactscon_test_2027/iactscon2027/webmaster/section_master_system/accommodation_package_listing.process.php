<?php
	include_once('includes/init.php');
	
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action)
	{
		/***************************************************************************/
		/*                    INSERT ACCOMMODATION PACKAGE PROCESS                 */
		/***************************************************************************/
		case'insert':
			
			$hotel_id               = addslashes(trim($_REQUEST['hotel_id_add']));
			$package_name           = addslashes(trim($_REQUEST['package_name']));
			
			// INSERTING PACKAGE DETAILS
			$insertPackage	=	array();
			$insertPackage['QUERY']	= "INSERT INTO "._DB_ACCOMMODATION_PACKAGE_."
											  SET  `hotel_id` 				= ?,
													`package_name` 			= ?,
													`status` 				= ?,
													`created_by` 		    = ?,
													`created_ip` 			= ?,
													`created_sessionid`		= ?,
													`created_datetime`		= ?";
													
			$insertPackage['PARAM'][]	=	array('FILD' => 'hotel_id' , 				       'DATA' => $hotel_id , 	            'TYP' => 's');
			$insertPackage['PARAM'][]	=	array('FILD' => 'package_name' , 			       'DATA' => $package_name , 	        'TYP' => 's');
			$insertPackage['PARAM'][]	=	array('FILD' => 'status' , 		        		   'DATA' => 'A' , 	        			'TYP' => 's');
			$insertPackage['PARAM'][]	=	array('FILD' => 'created_by' , 			           'DATA' => $loggedUserID ,        	'TYP' => 's');
			$insertPackage['PARAM'][]	=	array('FILD' => 'created_ip' , 	                   'DATA' => $_SERVER['REMOTE_ADDR'] ,  'TYP' => 's');
			$insertPackage['PARAM'][]	=	array('FILD' => 'created_sessionid' , 			   'DATA' => session_id() ,		        'TYP' => 's');
			$insertPackage['PARAM'][]	=	array('FILD' => 'created_datetime' , 			   'DATA' => date('Y-m-d H:i:s') , 	    'TYP' => 's');
			
			$mycms->sql_insert($insertPackage);
			
			$mycms->redirect("accommodation_package_listing.php?m=1");
			exit();
			break;
		
		/***************************************************************************/
		/*                    UPDATE ACCOMMODATION PACKAGE PROCESS                 */
		/***************************************************************************/
		case'update':
			
			$package_id				= addslashes(trim($_REQUEST['package_id']));
			
			$hotel_id               = addslashes(trim($_REQUEST['hotel_id_update']));
			$package_name        = addslashes(trim($_REQUEST['package_name']));
			
			// UPDATING PACKAGE DETAILS
			$sql		  =	array();
			$sql['QUERY'] = "UPDATE"._DB_ACCOMMODATION_PACKAGE_." 
								SET `hotel_id`				= ?,
									`package_name` 			= ?, 
								    `modified_by`			= ?,
									`modified_ip`			= ?,
									`modified_sessionId`	= ?,
									`modified_dateTime`		= ?
							  WHERE	`id`                	= ?";
							  
			$sql['PARAM'][]	=	array('FILD' => 'hotel_id' , 				    'DATA' => $hotel_id , 				           'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'package_name' , 			    'DATA' => $package_name , 					   'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'modified_by' , 				'DATA' => $loggedUserID , 					   'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'modified_ip' , 			    'DATA' => $_SERVER['REMOTE_ADDR'] , 		   'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'modified_sessionId' , 		    'DATA' => session_id() , 					   'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime' , 			'DATA' => date('Y-m-d') , 				       'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'id' , 						    'DATA' => $package_id , 					    'TYP' => 's');
			
			$mycms->sql_update($sql);
			
			$mycms->redirect("accommodation_package_listing.php?m=2");
			exit();
			break;	
	}
?>
