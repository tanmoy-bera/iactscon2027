<?php
	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action)
	{
		/************************************************************************/
		/*                             SEARCH HOTEL                             */
		/************************************************************************/
		case'search_hotel':
		
			pageRedirection(1,"accomodation_checkingdates.php",5);
			exit();
			break;
			
		/************************************************************************/
		/*                             ACTIVE HOTEL                             */
		/************************************************************************/	
		case'Active':
			$id=addslashes(trim($_REQUEST['id']));
			$sqlActiveHotel      = "UPDATE ".$cfg['DB.ACCOMMODATION.DATE']."
					                   SET `status` = 'A'
					                 WHERE `id` = '".$id."'"; 
					 
			$mycms->sql_update($sqlActiveHotel);
			
			pageRedirection(1,"accomodation_checkingdates.php",2);
			exit();
			break;

		/************************************************************************/
		/*                           INACTIVE HOTEL                             */
		/************************************************************************/	
		case'Inactive':
			 $id=addslashes(trim($_REQUEST['id']));
			 $sqlInactiveHotel    = "UPDATE ".$cfg['DB.ACCOMMODATION.DATE']."
					                   SET `status` = 'I'
					                 WHERE `id`= '".$id."'"; 
					 
			$mycms->sql_update($sqlInactiveHotel);
			
			pageRedirection(1,"accomodation_checkingdates.php",2);
			exit();
			break;

		/************************************************************************/
		/*                            REMOVE HOTEL                              */
		/************************************************************************/	
		case'Remove':
			
			$sqlRemoveHotel      = "UPDATE ".$cfg['DB.ACCOMMODATION.DATE']."
					                   SET `status` = 'D'
					                 WHERE `id` IN (".$_REQUEST['id'].")"; 
					 
			$mycms->sql_update($sqlRemoveHotel);
			
			pageRedirection(1,"hotel_listing.php",3,"");
			exit();
			break;
		
		case'insert':
			
			$hotel_id 			= addslashes(trim($_REQUEST['hotel_name_add']));
		
			$checkin_date 			= addslashes(trim($_REQUEST['checkin_date_add']));
			
			// INSERTING HOTEL DETAILS
			$cdate=addslashes(trim($_REQUEST['checkin_date_add']));
			$date = strtotime("+1 day", strtotime($cdate));
			 date("Y-m-d", $date);
			
			$sqlInsertHotel         = "INSERT INTO ".$cfg['DB.ACCOMMODATION.DATE']." 
			                                   SET `hotel_id` = '".$hotel_id."', 
											       `check_in_date` = '".$checkin_date."', 
												   `check_out_date` = '".date("Y-m-d", $date)."', 
												   `check_in_time` = '10:00am',
												   `check_out_time` = '9:55am', 
												    `status` = 'A'";
												   
			$mycms->sql_insert($sqlInsertHotel);
			
			pageRedirection(1, "accomodation_checkingdates.php", 1,"");
			exit();
			break;
			
		case'update':
			
			$accomodation_id        = addslashes(trim($_REQUEST['id']));
			
			//$hotel_name 			= addslashes(trim($_REQUEST['hotel_name_update']));
			$checkin_date_update 	= addslashes(trim($_REQUEST['checkin_date_update']));
			$cdate=addslashes(trim($_REQUEST['checkin_date_update']));
			$date = strtotime("+1 day", strtotime($cdate));
			 date("Y-m-d", $date);
			$checkout_date_update 	= addslashes(trim($_REQUEST['checkout_date_update']));
			
			
			// UPDATING HOTEL DETAILS
			$sqlUpdateHotel         = "UPDATE ".$cfg['DB.ACCOMMODATION.DATE']." 
									      SET  
										      `check_in_date` = '".$checkin_date_update."', 
											  `check_out_date` = '".date("Y-m-d", $date)."'
											WHERE `id` = '".$accomodation_id."'";
												   
			$mycms->sql_update($sqlUpdateHotel);
			
			pageRedirection(1, "accomodation_checkingdates.php", 2,"");
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

