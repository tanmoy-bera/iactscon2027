<?php
	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action)
	{
		/************************************************************************/
		/*                             SEARCH HOTEL                             */
		/************************************************************************/
		case'search_hotel':
		
			pageRedirection(1,"dinner_classificaton.php",5);
			exit();
			break;
			
		
		/************************************************************************/
		/*                            REMOVE HOTEL                              */
		/************************************************************************/	
		case'Remove':
			$dinner_id              		= addslashes(trim($_REQUEST['id']));
			$sqlUpdateHotel	=	array();
			$sqlUpdateHotel['QUERY']= "UPDATE "._DB_DINNER_CLASSIFICATION_." 
								      SET `status` = ? 
									WHERE `id` = ?";
			
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status' , 	  'DATA' => 'D' ,             'TYP' => 's');
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'id' ,  'DATA' => $dinner_id ,       	  'TYP' => 's');	
			$mycms->sql_update($sqlUpdateHotel);
			
			$sqlUpdateHotel	=	array();
			$sqlUpdateHotel['QUERY']= "UPDATE "._DB_DINNER_TARIFF_." 
								      SET `status` = ? 
									WHERE `dinner_classification_id` = ?";
			
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status' , 	  'DATA' => 'D' ,             'TYP' => 's');
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'id' ,  'DATA' => $dinner_id ,       	  'TYP' => 's');	
			$mycms->sql_update($sqlUpdateHotel);
			
			pageRedirection(1,"dinner_classificaton.php",3);
			exit();
			break;
		
		case'insert':
			
			$dinner_class_name 			    = addslashes(trim($_REQUEST['dinner_class_name']));
			$service_type 			   		= addslashes(trim($_REQUEST['service_type']));
			$dinner_date 		    		= addslashes(trim($_REQUEST['dinner_date']));
			$dinner_hotel_name 		    		= addslashes(trim($_REQUEST['dinner_hotel_name']));
			$link 		    		= addslashes(trim($_REQUEST['link']));

			$sqlInsertDinner		= array();
			$sqlInsertDinner['QUERY']= "INSERT INTO "._DB_DINNER_CLASSIFICATION_." 
			                                   SET `dinner_classification_name` = ?,
			                                   		`date` 				= ?, 
			                                   		`dinner_hotel_name` 				= ?, 
			                                   		`link` 				= ?, 
											       `service_type` 			= ?, 
												   
												   `status` 				= ?,  
												   `created_by` 		    = ?,
												   `created_ip` 			= ?,
												   `created_sessionId`		= ?,
												   `created_dateTime`		= ?";

			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'dinner_classification_name' , 	   'DATA' => $dinner_class_name ,      'TYP' => 's'); 
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'date' ,  		   	  			   'DATA' => $dinner_date ,       	    'TYP' => 's');	
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'dinner_hotel_name' ,  		   	  			   'DATA' => $dinner_hotel_name ,       	    'TYP' => 's');	
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'link' ,  		   	  			   'DATA' => $link ,       	    'TYP' => 's');	
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'service_type' , 	  	  		   'DATA' => $service_type ,            'TYP' => 's');
			
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'status' , 		   	   			   'DATA' => 'A',        				'TYP' => 's');
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'created_by' , 		   			   'DATA' => $loggedUserID ,        	'TYP' => 's');
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'created_ip' , 	      			   'DATA' => $_SERVER['REMOTE_ADDR'] ,  'TYP' => 's');
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'created_sessionId' ,  		  	   'DATA' => session_id() ,		        'TYP' => 's');
			$sqlInsertDinner['PARAM'][]	=	array('FILD' => 'created_dateTime' ,   		       'DATA' => date('Y-m-d H:i:s') ,	    'TYP' => 's');

			$mycms->sql_insert($sqlInsertDinner);
			
				
			pageRedirection(1, "dinner_classificaton.php", 1);
			exit();
			break;
			
		case'update':
			
			$dinner_id               		= addslashes(trim($_REQUEST['id']));
			$dinner_class_name 			    = addslashes(trim($_REQUEST['dinner_class_name']));
			$service_type 			   		= addslashes(trim($_REQUEST['service_type']));
			$dinner_date 		    		= addslashes(trim($_REQUEST['dinner_date']));
			$dinner_hotel_name 		    		= addslashes(trim($_REQUEST['dinner_hotel_name']));
			$link 		    		= addslashes(trim($_REQUEST['link']));
			
			$sqlUpdateHotel	=	array();
			$sqlUpdateHotel['QUERY']= "UPDATE "._DB_DINNER_CLASSIFICATION_." 
								      SET `dinner_classification_name` = ?,
								      		`date` 			= ?, 
								      		`dinner_hotel_name` 			= ?, 
								      		`link` 			= ?, 
									       `service_type` 			= ?, 
										  
								      	   `status`				    = ?,
								      	   `modified_by`			= ?,
										   `modified_ip`			= ?,
										   `modified_sessionId`		= ?,
										   `modified_dateTime`		= ?
									WHERE `id` 						= ?";
			
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'dinner_classification_name' , 	'DATA' => $dinner_class_name ,             			   'TYP' => 's');
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'date' ,  						'DATA' => $dinner_date ,      					'TYP' => 's');	
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'dinner_hotel_name' ,  			'DATA' => $dinner_hotel_name ,      'TYP' => 's');	
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'link' ,  						'DATA' => $link ,      					'TYP' => 's');	
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'service_type' ,  				'DATA' => $service_type ,      					'TYP' => 's');		
				
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'status' ,  					'DATA' => 'A' ,      					'TYP' => 's');		
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'modified_by' , 				'DATA' => $loggedUserID , 					 'TYP' => 's');
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'modified_ip' , 			    'DATA' => $_SERVER['REMOTE_ADDR'] , 		   'TYP' => 's');
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'modified_sessionId' , 		    'DATA' => session_id() , 					 'TYP' => 's');
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'modified_dateTime' , 			'DATA' => date('Y-m-d') , 				     'TYP' => 's');	
			$sqlUpdateHotel['PARAM'][]	=	array('FILD' => 'id' , 						    'DATA' => $dinner_id , 					    'TYP' => 's');						   
			$mycms->sql_update($sqlUpdateHotel);
			
			pageRedirection(1,"dinner_classificaton.php",3);
			exit();
			break;
	}
	
	/******************************************************************************/
	/*                               PAGE REDIRECTION METHOD                      */
	/******************************************************************************/
	function pageRedirection($indexVal,$fileName,$messageCode,$additionalString="")
	{
		global $mycms, $cfg;
		
		$pageKey         = "_pgn1_";
		$pageKeyVal      = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString   = "";
		$searchArray     = array();
		
		$searchArray[$pageKey]                = $pageKeyVal;
		$searchArray['src_hotel_name']        = addslashes(trim($_REQUEST['src_hotel_name']));
		
		foreach($searchArray as $searchKey=>$searchVal)
		{
			if($searchVal!="")
			{
				$searchString .= "&".$searchKey."=".$searchVal;
			}
		}
		
		$mycms->redirect($fileName."?m=".$messageCode.$additionalString.$searchString);
	}
?>
