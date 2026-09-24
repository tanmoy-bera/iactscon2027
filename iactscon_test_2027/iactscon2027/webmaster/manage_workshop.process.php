<?php

include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();

switch ($action) {
	case 'search_classification':

		pageRedirection('registration_tariff.php', 5, "");
		exit();
		break;

		/***************************************************************************/
		/*                      UPDATE TARIFF CLASSIFICATION                       */
		/***************************************************************************/
	case 'update':
		update($mycms, $cfg);

		$workshop_add 	  = addslashes($_POST['workshop_Edit']);
		$editseatlimit 	  = addslashes($_POST['seat_limit_Edit']);
		$editVenue 	  = addslashes($_POST['venueEdit']);
		$editworkshopdate = addslashes($_POST['workshop_date_Edit']);
		$workshop_type 	  = addslashes($_POST['workshop_type']);
		// $workshop_display = 'Y';
		// $icon_id = addslashes($_POST['icon_id']);
		$id 			  = addslashes($_POST['workshop_id']);
		$workshopstatus 	  = $_POST['workshopstatus'];
		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CLASSIFICATION_ . " 
				                SET  `classification_title`= ? , 
				                     `seat_limit`= ?, 
									 `venue`=?,
				                     `type`= ?,
									 `status`= ?,
								  	 `workshop_date`= ?,
								  	 `modified_by`= ?,
								  	 `modified_ip`= ?,
								  	 `modified_sessionId`= ?,
								  	 `modified_dateTime`= ?
				               WHERE `id`= ?";
		$sql['PARAM'][]	=	array('FILD' => 'classification_title', 	 'DATA' => $workshop_add, 						    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seat_limit', 			     'DATA' => $editseatlimit, 						'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'venue', 			     'DATA' => $editVenue, 						'TYP' => 's');
		// $sql['PARAM'][]	=	array('FILD' => 'display', 		         'DATA' => $workshop_display, 						'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'type', 					 'DATA' => $workshop_type, 				        'TYP' => 's');
	    $sql['PARAM'][]	=	array('FILD' => 'status', 			 'DATA' => $workshopstatus, 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'workshop_date', 			 'DATA' => $editworkshopdate, 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			 'DATA' => $loggedUserID, 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			 'DATA' => $_SERVER['REMOTE_ADDR'], 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_sessionId', 		  'DATA' => session_id(), 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 		  'DATA' => date('Y-m-d H:i:s'), 				        'TYP' => 's');

		$sql['PARAM'][]	=	array('FILD' => 'id', 						 'DATA' => $id, 									'TYP' => 's');

		$res         = $mycms->sql_update($sql);
      		//  echo '<pre>'; print_r($sql); echo '</pre>'; die();

	    echo '<script>window.location.href="system_master.php#workshop";</script>';
		break;
	case 'updateOnlyWorkshop':
		
		        $sql 	=	array();
				$sql['QUERY'] = "UPDATE "._DB_WORKSHOP_CLASSIFICATION_."
							SET `onlyWorkshop` = ?,
								`modified_by` = ?,
								`modified_ip` = ?,
								`modified_sessionId` = ?,
								`modified_dateTime` = ?
							WHERE `id` = ?";

			$sql['PARAM'][] = array(
				'FILD' => 'onlyWorkshop',
				'DATA' => $_REQUEST['onlyWorkshop'], // Y or N
				'TYP'  => 's'
			);

			$sql['PARAM'][] = array(
				'FILD' => 'modified_by',
				'DATA' => $loggedUserID,
				'TYP'  => 's'
			);

			$sql['PARAM'][] = array(
				'FILD' => 'modified_ip',
				'DATA' => $_SERVER['REMOTE_ADDR'],
				'TYP'  => 's'
			);

			$sql['PARAM'][] = array(
				'FILD' => 'modified_sessionId',
				'DATA' => session_id(),
				'TYP'  => 's'
			);

			$sql['PARAM'][] = array(
				'FILD' => 'modified_dateTime',
				'DATA' => date('Y-m-d H:i:s'),
				'TYP'  => 's'
			);

			$sql['PARAM'][] = array(
				'FILD' => 'id',
				'DATA' => $_REQUEST['id'],
				'TYP'  => 's'
			);

			$mycms->sql_update($sql);
      		//  echo '<pre>'; print_r($sql); echo '</pre>'; die();

	    echo '<script>window.location.href="system_master.php#workshop";</script>';
		break;
		case 'WorkshopAllow':

		    $sqlFetchTariffAmount = array();
			$sqlFetchTariffAmount['QUERY'] = "SELECT * 
											FROM "._DB_TARIFF_WORKSHOP_." 
											WHERE `workshop_id` = ? 
											AND `registration_classification_id`= ? 
										    AND `status` = ?";
		
			$sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'workshop_id', 'DATA'=>$_REQUEST['id'], 'TYP'=>'s');
			$sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'registration_classification_id', 'DATA'=>$_REQUEST['classificationId'], 'TYP'=>'s');
			$sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'status', 'DATA'=>'A', 'TYP'=>'s');

			 $resultFetchTariffAmount = $mycms->sql_select($sqlFetchTariffAmount);
            // echo "<pre>";
			// print_r($sqlFetchTariffAmount);
			// die();
			if($resultFetchTariffAmount){
				foreach($resultFetchTariffAmount as $key => $value){
					$sql 	=	array();
					$sql['QUERY'] = "UPDATE "._DB_TARIFF_WORKSHOP_."
								SET `OnlyWorkshopAllow` = ?,
									`modified_by` = ?,
									`modified_ip` = ?,
									`modified_sessionId` = ?,
									`modified_dateTime` = ?
								WHERE `id` = ? 
									";

					$sql['PARAM'][] = array(
						'FILD' => 'OnlyWorkshopAllow',
						'DATA' => $_REQUEST['WorkshopAllow'], // Y or N
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'modified_by',
						'DATA' => $loggedUserID,
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'modified_ip',
						'DATA' => $_SERVER['REMOTE_ADDR'],
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'modified_sessionId',
						'DATA' => session_id(),
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'modified_dateTime',
						'DATA' => date('Y-m-d H:i:s'),
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'id',
						'DATA' => $value['id'],
						'TYP'  => 's'
					);

					$mycms->sql_update($sql);
				}
			}else{
				
				$sql    =    array();
				$sql['QUERY'] = "SELECT cutoff.cutoff_title,cutoff.id 
								FROM " . _DB_WORKSHOP_CUTOFF_ . " cutoff
								WHERE status = ?";
				$sql['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
				$res = $mycms->sql_select($sql);

				foreach ($res as $key => $title) {
				
					$sqlInsertTariffAmount = array();
					$sqlInsertTariffAmount['QUERY']  = "INSERT INTO "._DB_TARIFF_WORKSHOP_."
														SET `workshop_id` = ? ,
															`registration_classification_id` = ? ,
															`tariff_cutoff_id` = ? ,
															`inr_amount` = ? , 
															`usd_amount` = ? ,
															`curency` = ? ,
															`OnlyWorkshopAllow` = ? ,
															`status` = ? ,
															`created_by` = ?,
															`created_ip` = ? ,
															`created_sessionId` = ?,
															`created_dateTime` = ? ";
					
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id', 		  		   'DATA' =>$_REQUEST['id'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id', 'DATA' => $_REQUEST['classificationId'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 		  	   'DATA' => $title['id'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'inr_amount', 		  			   'DATA' => 0.00,  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'usd_amount', 		  			   'DATA' => 0.00,  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'curency', 		  			   'DATA' => 'INR',  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshopAllow', 		  			   'DATA' =>$_REQUEST['WorkshopAllow'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'status', 		  			  	   'DATA' => 'A',  			'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_by', 		  			   'DATA' => $loggedUserID,  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_ip', 		  			   'DATA' => $_SERVER['REMOTE_ADDR'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_sessionId',  			   'DATA' => session_id(),  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_dateTime', 			   'DATA' => date('Y-m-d H:i:s'),  	'TYP' => 's');	
					$mycms->sql_insert($sqlInsertTariffAmount);		
				}
			}
		      
      		//  echo '<pre>'; print_r($sql); echo '</pre>'; die();

	    echo '<script>window.location.href="system_master.php#workshop";</script>';
		break;
		case 'OnlyWorkshopAllow':

		    $sqlFetchTariffAmount = array();
			$sqlFetchTariffAmount['QUERY'] = "SELECT * 
											FROM "._DB_TARIFF_WORKSHOP_." 
											WHERE `workshop_id` = ? 
											AND `registration_classification_id`= ? 
											AND `OnlyWorkshop_clssId`= ? 
										    AND `status` = ?";
		
			$sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'workshop_id', 'DATA'=>$_REQUEST['id'], 'TYP'=>'s');
			$sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'registration_classification_id', 'DATA'=>0, 'TYP'=>'s');
			$sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'OnlyWorkshop_clssId', 'DATA'=>$_REQUEST['classificationId'], 'TYP'=>'s');
			$sqlFetchTariffAmount['PARAM'][] = array('FILD'=>'status', 'DATA'=>'A', 'TYP'=>'s');

			 $resultFetchTariffAmount = $mycms->sql_select($sqlFetchTariffAmount);
            // echo "<pre>";
			// print_r($sqlFetchTariffAmount);
			// die();
			if($resultFetchTariffAmount){
				foreach($resultFetchTariffAmount as $key => $value){
					$sql 	=	array();
					$sql['QUERY'] = "UPDATE "._DB_TARIFF_WORKSHOP_."
								SET `OnlyWorkshopAllow` = ?,
									`modified_by` = ?,
									`modified_ip` = ?,
									`modified_sessionId` = ?,
									`modified_dateTime` = ?
								WHERE `id` = ? 
									";

					$sql['PARAM'][] = array(
						'FILD' => 'OnlyWorkshopAllow',
						'DATA' => $_REQUEST['OnlyWorkshopAllow'], // Y or N
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'modified_by',
						'DATA' => $loggedUserID,
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'modified_ip',
						'DATA' => $_SERVER['REMOTE_ADDR'],
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'modified_sessionId',
						'DATA' => session_id(),
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'modified_dateTime',
						'DATA' => date('Y-m-d H:i:s'),
						'TYP'  => 's'
					);

					$sql['PARAM'][] = array(
						'FILD' => 'id',
						'DATA' => $value['id'],
						'TYP'  => 's'
					);

					$mycms->sql_update($sql);
				}
			}else{
				
				$sql    =    array();
				$sql['QUERY'] = "SELECT cutoff.cutoff_title,cutoff.id 
								FROM " . _DB_WORKSHOP_CUTOFF_ . " cutoff
								WHERE status = ?";
				$sql['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
				$res = $mycms->sql_select($sql);

				foreach ($res as $key => $title) {
				
					$sqlInsertTariffAmount = array();
					$sqlInsertTariffAmount['QUERY']  = "INSERT INTO "._DB_TARIFF_WORKSHOP_."
														SET `workshop_id` = ? ,
															`registration_classification_id` = ? ,
															`tariff_cutoff_id` = ? ,
															`OnlyWorkshop_clssId`   = ?,
															`inr_amount` = ? , 
															`usd_amount` = ? ,
															`curency` = ? ,
															`OnlyWorkshopAllow` = ? ,
															`status` = ? ,
															`created_by` = ?,
															`created_ip` = ? ,
															`created_sessionId` = ?,
															`created_dateTime` = ? ";
					
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'workshop_id', 		  		   'DATA' =>$_REQUEST['id'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'registration_classification_id', 'DATA' => 0,  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'tariff_cutoff_id', 		  	   'DATA' => $title['id'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshop_clssId', 		  	   'DATA' => $_REQUEST['classificationId'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'inr_amount', 		  			   'DATA' => 0.00,  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'usd_amount', 		  			   'DATA' => 0.00,  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'curency', 		  			   'DATA' => 'INR',  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'OnlyWorkshopAllow', 		  			   'DATA' =>$_REQUEST['OnlyWorkshopAllow'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'status', 		  			  	   'DATA' => 'A',  			'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_by', 		  			   'DATA' => $loggedUserID,  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_ip', 		  			   'DATA' => $_SERVER['REMOTE_ADDR'],  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_sessionId',  			   'DATA' => session_id(),  	'TYP' => 's');	
					$sqlInsertTariffAmount['PARAM'][]  = array('FILD' => 'created_dateTime', 			   'DATA' => date('Y-m-d H:i:s'),  	'TYP' => 's');	
					$mycms->sql_insert($sqlInsertTariffAmount);		
				}
			}
		      
      		//  echo '<pre>'; print_r($sql); echo '</pre>'; die();

	    echo '<script>window.location.href="system_master.php#workshop";</script>';
		break;
		/***************************************************************************/
		/*                      Insert TARIFF CLASSIFICATION                       */
		/***************************************************************************/
	case 'insert':
		insert($mycms, $cfg);
		// pageRedirection('manage_workshop.php', 2, "");
		break;
		/************************ACTIVE**********************/
	case 'Active':
		Active($mycms, $cfg);
		pageRedirection('manage_workshop.php', 2, "");
		break;
		/************************ACTIVE**********************/
	case 'Inactive':
		Inactive($mycms, $cfg);
		pageRedirection('manage_workshop.php', 2, "");
		break;
	case 'delete':
		delete($mycms, $cfg);
		pageRedirection('manage_cutoff.php',2,"");
		break;
}


function update($mycms, $cfg)
{
	global $loggedUserID;



	$classification_id                 	= addslashes($_REQUEST['classification_id']);
	$tariff_inr_cutoff_id_edit          = $_REQUEST['tariff_inr_cutoff_id_edit'];
	$tariff_usd_cutoff_id_edit          = $_REQUEST['tariff_usd_cutoff_id_edit'];

	// TARIFF CLASSIFICATION AMOUNT UPDATION
	$tariff_cutoff_id               = $_REQUEST['tariff_cutoff_id_edit'];

	foreach ($tariff_inr_cutoff_id_edit as $key => $inr_amount) {
		$inr_amount;
		$usd_amount = $tariff_usd_cutoff_id_edit[$key];
		if ($inr_amount != "" && $usd_amount != "") {
			$sqlFetchTariffAmount['QUERY']     = "SELECT * FROM " . _DB_TARIFF_WORKSHOP_ . " 
															  WHERE `workshop_id` = ? 
															    AND `tariff_cutoff_id` = ? ";
			$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'workshop_id',   			'DATA' => $classification_id, 	    'TYP' => 's');
			$sqlFetchTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id', 		'DATA' => $key, 		   			 'TYP' => 's');

			$resultFetchTariffAmount  = $mycms->sql_select($sqlFetchTariffAmount);
			$maxRowsTariffAmount      = $mycms->sql_numrows($resultFetchTariffAmount);

			if ($maxRowsTariffAmount > 0) {

				$sql 	=	array();
				$sql['QUERY']  = "UPDATE " . _DB_TARIFF_WORKSHOP_ . "
									  SET `inr_amount`			= ? ,
									  	  `usd_amount`			= ? ,
										  `modified_by`		 	= ? ,
										  `modified_ip` 		= ? ,
										  `modified_sessionId`  = ? ,
										  `modified_dateTime`   = ? 
									WHERE `workshop_id` 		= ?  
									  AND `tariff_cutoff_id` 	= ? ";
				$sql['PARAM'][]	=	array('FILD' => 'inr_amount', 	 		'DATA' => $inr_amount, 					    'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'usd_amount', 			'DATA' => $usd_amount, 						'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'modified_by', 		'DATA' => $loggedUserID, 						'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 	    'DATA' => $_SERVER['REMOTE_ADDR'], 	        'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'modified_sessionId',  'DATA' => session_id(), 				        'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime',   'DATA' => date('Y-m-d H:i:s'), 			    'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'workshop_id', 		'DATA' => $classification_id, 				    'TYP' => 's');
				$sql['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id', 	'DATA' => $key, 								'TYP' => 's');
				$mycms->sql_update($sql);
			} else {
				$sqlInsertTariffAmount 	= 	array();

				$sqlInsertTariffAmount['QUERY']  = "INSERT INTO " . _DB_TARIFF_WORKSHOP_ . "
															   SET `workshop_id` = ? ,
																   `tariff_cutoff_id` = ? ,
																   `inr_amount` = ? ,
																   `usd_amount` = ? , 
																   `status` = ? ,
																   `created_by` = ? ,
																   `created_ip` = ? ,
																   `created_sessionId` = ? ,
																   `created_dateTime` = ? ";
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'workshop_id', 		  'DATA' => $classification_id, 						        'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id', 	  'DATA' => $key, 						    'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'inr_amount', 		      'DATA' => $inr_amount, 			    'TYP' => 's');

				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'usd_amount', 		      'DATA' => $usd_amount, 			    'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'status', 		          'DATA' => 'A', 			    'TYP' => 's');


				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_by', 			  'DATA' => $loggedUserID, 		            'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_ip', 	          'DATA' => $_SERVER['REMOTE_ADDR'], 					        'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_sessionId', 	  'DATA' => session_id(), 							        'TYP' => 's');
				$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_dateTime', 	  'DATA' => date('Y-m-d H:i:s'), 							    'TYP' => 's');

				$mycms->sql_insert($sqlInsertTariffAmount);
			}
		}
	}
}
/*================ ACTIVE WORKSHOP ==================*/
function Active($mycms, $cfg)
{
	global $loggedUserID;
	$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CLASSIFICATION_ . " 
						    SET `status`	 = ?,
							  	`modified_by`= ?,
							  	`modified_ip`= ?,
							  	`modified_sessionId`= ?,
							  	`modified_dateTime`= ?
						  WHERE `id`		 = ?";
	$sql['PARAM'][]	=	array('FILD' => 'status', 					 'DATA' => 'A', 							'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			 'DATA' => $loggedUserID, 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			 'DATA' => $_SERVER['REMOTE_ADDR'], 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_sessionId', 		  'DATA' => session_id(), 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 		  'DATA' => date('Y-m-d H:i:s'), 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						 'DATA' => $_REQUEST['id'], 				'TYP' => 's');
	$mycms->sql_update($sql);
	echo '<script>window.location.href="system_master.php#workshop";</script>';

	exit();
	
}
/*================ INACTIVE WORKSHOP =================*/
function Inactive($mycms, $cfg)
{
	global $loggedUserID;
	$sql['QUERY'] = "UPDATE " . _DB_WORKSHOP_CLASSIFICATION_ . " 
						    SET `status`	 = ?,
						    	`modified_by`= ?,
							  	`modified_ip`= ?,
							  	`modified_sessionId`= ?,
							  	`modified_dateTime`= ? 
						  WHERE `id`		 = ?";
	$sql['PARAM'][]	=	array('FILD' => 'status', 					 'DATA' => 'I', 							'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			 'DATA' => $loggedUserID, 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			 'DATA' => $_SERVER['REMOTE_ADDR'], 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_sessionId', 		  'DATA' => session_id(), 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime', 		  'DATA' => date('Y-m-d H:i:s'), 				        'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'id', 						 'DATA' => $_REQUEST['id'], 				'TYP' => 's');
	$mycms->sql_update($sql);
	echo '<script>window.location.href="system_master.php#workshop";</script>';

	exit();
	
}
/******************************************************************************/
/*                                 UTILITY METHOD                             */
/******************************************************************************/
function insert($mycms, $cfg)
{
	global $loggedUserID;
	$workshop 			=			addslashes($_REQUEST['workshop_add']);
	$venue 			=			addslashes($_REQUEST['venue']);
	$seatlimit 			=			addslashes($_REQUEST['seat_limit_add']);
	$workshop_date_add  =			addslashes($_REQUEST['workshop_date_add']);
	$workshop_type 		=			addslashes($_REQUEST['workshop_type']);
	$workshop_display   =           addslashes($_REQUEST['workshop_display']);
	$workshopstatus 	  = $_REQUEST['workshopstatus'];

	$sqlInsertTariffAmount	=	array();
	$sqlInsertTariffAmount['QUERY']  = "INSERT INTO " . _DB_WORKSHOP_CLASSIFICATION_ . "
												   SET `classification_title`   = ?,
												   	   `type`				 	= ?,
													   `venue`=?,
													   `seat_limit` 			= ?,
													   `workshop_date`			= ?,
													   `display`	   			= ?,
													   `status`					= ?,
													   `created_by` 			= ?,
													   `created_ip`				= ?,
													   `created_sessionId` 		= ?,
													   `created_dateTime` 		= ?";

	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'classification_title',       'DATA' => $workshop, 	        'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'type', 			           'DATA' => $workshop_type,       'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'venue', 			           'DATA' => $venue,       'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'seat_limit', 		           'DATA' => $seatlimit, 	        'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'workshop_date', 		       'DATA' => $workshop_date_add,   'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'display', 		           'DATA' => 'Y',    'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'status', 		               'DATA' => $workshopstatus, 				    'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_by', 			       'DATA' => $loggedUserID, 	     'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_ip', 	               'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_sessionId', 		   'DATA' => session_id(),          'TYP' => 's');
	$sqlInsertTariffAmount['PARAM'][]	=	array('FILD' => 'created_dateTime', 		   'DATA' => date('Y-m-d H:i:s'),   'TYP' => 's');
//     echo "<pre>";
// 	print_r($sqlInsertTariffAmount);
// die();
     $mycms->setDisplayMessage("Workshop added successfully.");
    $_SESSION['DISPLAY_MESSAGE'] = $mycms->getDisplayMessage();

	$mycms->sql_insert($sqlInsertTariffAmount);
	echo '<script>window.location.href="system_master.php#workshop";</script>';

	exit();
	
}

function delete($mycms,$cfg)
	{
		global $loggedUserID ;
		$sql['QUERY'] = "UPDATE "._DB_WORKSHOP_CLASSIFICATION_." 
						    SET `status`	 = ?,
						    	`modified_by`= ?,
							  	`modified_ip`= ?,
							  	`modified_sessionId`= ?,
							  	`modified_dateTime`= ? 
						  WHERE `id`		 = ?"; 
		$sql['PARAM'][]	=	array('FILD' => 'status' , 					 'DATA' => 'D' , 							'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_by' , 			 'DATA' => $loggedUserID , 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_ip' , 			 'DATA' => $_SERVER['REMOTE_ADDR'] , 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_sessionId' , 		  'DATA' => session_id() , 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modified_dateTime' , 		  'DATA' => date('Y-m-d H:i:s') , 				        'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id' , 						 'DATA' => $_REQUEST['id'] , 				'TYP' => 's');
		$mycms->sql_update($sql);
        echo '<script>window.location.href="system_master.php#workshop";</script>';
		exit();
		
	}	
/******************************************************************************/
/*                                 UTILITY METHOD                             */
/******************************************************************************/
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey                       		       = "_pgn_";
	$pageKeyVal                    		       = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString                 		       = "";
	$searchArray                   		       = array();

	$searchArray[$pageKey]         		       = $pageKeyVal;
	$searchArray['src_tariff_classification']  = trim($_REQUEST['src_tariff_classification']);

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}
