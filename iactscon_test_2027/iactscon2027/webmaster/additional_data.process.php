<?php
include_once('includes/init.php');

$loggedUserID     = $mycms->getLoggedUserId();
$loggedUserType   = $mycms->getLoggedUserType();
switch ($action) {
	case 'insertconfDate':
		$session_id				= addslashes(trim($_REQUEST['session_id']));
		$conf_title 			= addslashes(trim($_REQUEST['conf_title']));
		$conf_desc 				= addslashes(trim($_REQUEST['conf_desc']));
		$conf_highlight 		= addslashes(trim($_REQUEST['conf_highlight']));
		$conf_date 				= addslashes(trim($_REQUEST['session_date_add']));
		$conf_time			   =  addslashes(trim($_REQUEST['session_time_add']));
		if (!empty($conf_time)) {
			$confDate = $conf_date . ' ' . $conf_time . ':00';
		} else {
			$confDate = $conf_date;
		}
		//echo $_FILES['conf_image']['name']; die;		

		//echo '<pre>'; print_r($_REQUEST); die;	

		// INSERTING SESSION DETAILS
		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "INSERT INTO " . _DB_PROGRAM_SCHEDULE_DATE_ . " 
											  	SET   `conf_title` = ?,
											  		  `conf_desc` = ?,
											  		  `conf_highlight` = ?,
											  		  `conf_date` = ?,

													  `status` = ? ";

		$sqlInsertSession['PARAM'][]  = array('FILD' => 'conf_title',  'DATA' => $conf_title,  'TYP' => 's');
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'conf_desc',  'DATA' => $conf_desc,  'TYP' => 's');
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'conf_highlight',  'DATA' => $conf_highlight,  'TYP' => 's');
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'conf_date',  'DATA' => $confDate,  'TYP' => 's');
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');


		$lastInsertedId = $mycms->sql_insert($sqlInsertSession, false);





		participateImageUpload($lastInsertedId, $_FILES['conf_image']);


		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#date_master";</script>';	exit();
		break;

	case 'removeConferenceDate':
		$id 				= addslashes(trim($_REQUEST['id']));

		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "UPDATE " . _DB_PROGRAM_SCHEDULE_DATE_ . " 
													  SET   `status` ='D'  WHERE `id`=?";

		$sqlInsertSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');
		// print_r($sqlInsertSession);
		// die;

		$mycms->sql_update($sqlInsertSession);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#date_master";</script>';	exit();
		break;

	case 'insertSpeaker':

		$session_id				= addslashes(trim($_REQUEST['session_id']));
		$speaker_name 			= addslashes(trim($_REQUEST['speaker_name']));
		$hall_name 				= addslashes(trim($_REQUEST['hall_name']));
		$designation 			= addslashes(trim($_REQUEST['designation']));

		$conf_datetime 			= addslashes(trim($_REQUEST['conf_datetime']));


		//echo $_FILES['conf_image']['name']; die;		

		//echo '<pre>'; print_r($_REQUEST); die;	

		// INSERTING SESSION DETAILS
		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "INSERT INTO " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . " 
											  	SET   `speaker_name` = ?,
											  		  `hall_name` = ?,
											  		  `designation` = ?,
											  		  `conf_datetime` = ?,
											  		

													  `status` = ? ";

		$sqlInsertSession['PARAM'][]  = array('FILD' => 'speaker_name',  'DATA' => $speaker_name,  'TYP' => 's');
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'hall_name',  'DATA' => $hall_name,  'TYP' => 's');
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'designation',  'DATA' => $designation,  'TYP' => 's');
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'conf_datetime',  'DATA' => $conf_datetime,  'TYP' => 's');

		$sqlInsertSession['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');


		$lastInsertedId = $mycms->sql_insert($sqlInsertSession, false);





		speakerImageUpload($lastInsertedId, $_FILES['image']);


		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#highlight_speakers";</script>';	exit();
		break;

	case 'updateConfDate':


		if (!empty($_REQUEST['session_id'])) {

			$session_id				= addslashes(trim($_REQUEST['session_id']));
			$conf_title 			= addslashes(trim($_REQUEST['conf_title']));
			$conf_desc 				= addslashes(trim($_REQUEST['conf_desc']));
			$conf_highlight 		= addslashes(trim($_REQUEST['conf_highlight']));
			$conf_date 				= addslashes(trim($_REQUEST['session_date_add']));
			$conf_time			   =  addslashes(trim($_REQUEST['session_time_add']));
			if (!empty($conf_time)) {
				$confDate = $conf_date . ' ' . $conf_time . ':00';
			} else {
				$confDate = $conf_date;
			}

			$sqlFetchSession = array();
			$sqlFetchSession['QUERY'] 			= "SELECT conf_image FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " 
												
												WHERE `id` = ?";

			$sqlFetchSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $session_id,  'TYP' => 's');

			$rowFetchSession = $mycms->sql_select($sqlFetchSession);
			//echo $_FILES['conf_image']['name']; die;				


			// UPDATING SESSION DETAILS
			$sqlUpdateSession = array();
			$sqlUpdateSession['QUERY'] 			= "UPDATE " . _DB_PROGRAM_SCHEDULE_DATE_ . " 
												  SET `conf_title` = '" . $conf_title . "',
												   `conf_desc` = '" . $conf_desc . "',
												   `conf_date` = '" . $confDate . "',
												  
												   `conf_highlight` = '" . $conf_highlight . "'
												WHERE `id` = ?";

			$sqlUpdateSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $session_id,  'TYP' => 's');

			$mycms->sql_update($sqlUpdateSession);

			if (!empty($_FILES['conf_image']['name']) && $_FILES['conf_image']['name'] != '') {
				participateImageUpload($session_id, $_FILES['conf_image']);
				$userParticipateImagePath     				= '../../' . $cfg['SP.PARTICIPANT.DOC'] . $rowFetchSession[0]['conf_image'];
				unlink($userParticipateImagePath);
			}
		}



			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#date_master";</script>';	exit();
		exit();
		break;

	case 'updateSpeaker':


		if (!empty($_REQUEST['session_id'])) {

			$session_id				= addslashes(trim($_REQUEST['session_id']));
			$speaker_name 			= addslashes(trim($_REQUEST['speaker_name']));
			$hall_name 				= addslashes(trim($_REQUEST['hall_name']));
			$designation 				= addslashes(trim($_REQUEST['designation']));
			$conf_datetime 		= addslashes(trim($_REQUEST['conf_datetime']));


			$sqlFetchSession = array();
			$sqlFetchSession['QUERY'] 			= "SELECT image FROM " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . " 
												
												WHERE `id` = ?";

			$sqlFetchSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $session_id,  'TYP' => 's');

			$rowFetchSession = $mycms->sql_select($sqlFetchSession);
			//echo $_FILES['conf_image']['name']; die;				


			// UPDATING SESSION DETAILS
			$sqlUpdateSession = array();
			$sqlUpdateSession['QUERY'] 			= "UPDATE " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . " 
												  SET `speaker_name` = '" . $speaker_name . "',
												   `hall_name` = '" . $hall_name . "',
												   `designation` = '" . $designation . "',
												   `conf_datetime` = '" . $conf_datetime . "'
												WHERE `id` = ?";

			$sqlUpdateSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $session_id,  'TYP' => 's');

			$mycms->sql_update($sqlUpdateSession);

			if (!empty($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
				speakerImageUpload($session_id, $_FILES['image']);
				$userParticipateImagePath     				= '../../' . $cfg['SP.PARTICIPANT.DOC'] . $rowFetchSession[0]['image'];
				unlink($userParticipateImagePath);
			}
		}



		
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#highlight_speakers";</script>';	exit();
		exit();
		break;

	case 'DateInactive':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_DATE_ . "
					   SET `status`='I'
					 WHERE id = ? ";

		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
		
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#date_master";</script>';	exit();
		break;

	case 'SpeakerInactive':
		$id = $_REQUEST['id'];

		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . "
					   SET `status`='I'
					 WHERE id = ? ";

		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#highlight_speakers";</script>';	exit();
		break;

	case 'DateActive':

		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_DATE_ . "
					   SET `status`='A'
					 WHERE id = ?";

		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#date_master";</script>';	exit();
		break;


	case 'SpeakerActive':

		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . "
					   SET `status`='A'
					 WHERE id = ?";

		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#highlight_speakers";</script>';	exit();
		break;

	//////////////////////////

	case 'insertVenue':

		$venue	 					= addslashes(trim($_REQUEST['session_venue_add']));
		// INSERTING SESSION DETAILS
		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']			= "INSERT INTO " . _DB_PROGRAM_SCHEDULE_VENUE_ . " 
											  	   SET `program_venue` = '" . $venue . "', 
													   `status` = 'A'";

		$mycms->sql_insert($sqlInsertSession);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#venue_master";</script>';	exit();
		break;

	case 'updateVenue':
		$session_id					= addslashes(trim($_REQUEST['venue_id']));
		$program_venue 				= addslashes(trim($_REQUEST['venuetitle']));
		// UPDATING SESSION DETAILS
		$sqlUpdateSession = array();
		$sqlUpdateSession['QUERY'] 			= "UPDATE " . _DB_PROGRAM_SCHEDULE_VENUE_ . "
											  SET `program_venue` = '" . $program_venue . "' 
											WHERE `id` = ?";

		$sqlUpdateSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $session_id,  'TYP' => 's');

		$mycms->sql_update($sqlUpdateSession);

			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#venue_master";</script>';	exit();
		exit();
		break;

	case 'InactiveVenue':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_VENUE_ . "
					   SET `status`='I'
					 WHERE id = ?";

		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#venue_master";</script>';	exit();
		break;

	case 'ActiveVenue':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_VENUE_ . "
					   SET `status`='A'
					 WHERE id = ? ";
		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');
		$mycms->sql_update($sql);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#venue_master";</script>';	exit();
		break;
	case 'removeVenue':
		$id 				= addslashes(trim($_REQUEST['id']));

		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "UPDATE " . _DB_PROGRAM_SCHEDULE_VENUE_ . " 
													  SET   `status` ='D'  WHERE `id`=?";

		$sqlInsertSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sqlInsertSession);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#venue_master";</script>';	exit();
		break;

	////////////////////////////////////////////////////////////

	case 'HallInsert':
		$session_id				= addslashes(trim($_REQUEST['session_id']));
		$hall_title 			= addslashes(trim($_REQUEST['session_title_add']));
		$hall_name 				= addslashes(trim($_REQUEST['session_tag_add']));
		$hall_venue				= addslashes(trim($_REQUEST['session_venue_add']));

		$hall_temp_name 		= $_REQUEST['hall_temp_name'];

		// INSERTING SESSION DETAILS
		$sqlInsertSession = array();
		$sqlInsertSession['QUERY'] 			= "INSERT INTO " . _DB_MASTER_HALL_ . " 
											  	   SET `hall_title` = '" . $hall_title . "', 
												  	   `tag_name` = '" . $hall_name . "',
													   `hall_venue` = '" . $hall_venue . "',
													   `status` = 'A'";
		$lastId = $mycms->sql_insert($sqlInsertSession);
		foreach ($_REQUEST['session_type'] as $key => $value) {
			$sqlInsertMaping['QUERY']			 = "INSERT INTO " . $cfg['DB.SP.MAPING.SC.TO.HALL'] . " 
														SET `session_classification_id` = '" . $value . "', 
															`hall_id` = '" . $lastId . "'";
			$mycms->sql_insert($sqlInsertMaping);
		}

		foreach ($hall_temp_name as $dateId => $tempName) {

			$sqlInsertSession = array();
			$sqlInsertSession['QUERY'] 			 = "INSERT INTO " . _DB_MASTER_HALL_NAME_ . " 
											  	   			    SET `hall_name` = '" . $tempName . "', 
																    `date_id` = '" . $dateId . "',
																    `hall_id` = '" . $lastId . "'";
			$mycms->sql_insert($sqlInsertSession);
		}

			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#hall_master";</script>';	exit();
		break;

	case 'HallUpdate':
		$hall_id				= addslashes(trim($_REQUEST['session_id']));
		$hall_title 			= addslashes(trim($_REQUEST['halltitle']));
		$hall_name 				= addslashes(trim($_REQUEST['halltag']));
		$hall_venue				= addslashes(trim($_REQUEST['session_venue_add']));

		$hall_temp_name 		= $_REQUEST['hall_temp_name'];

		// UPDATING SESSION DETAILS
		$sqlUpdateSession = array();
		$sqlUpdateSession['QUERY'] 	= "UPDATE " . _DB_MASTER_HALL_ . " 
											  SET `hall_title` = '" . $hall_title . "', 
												  `tag_name` = '" . $hall_name . "',
												  `hall_venue` = '" . $hall_venue . "'
											WHERE `id` = ?";

		$sqlUpdateSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $hall_id,  'TYP' => 's');
		$mycms->sql_update($sqlUpdateSession);



		$sqlDelete = array();
		$sqlDelete['QUERY'] = "DELETE FROM " . _DB_MASTER_HALL_NAME_ . "
										 WHERE `hall_id` = '" . $hall_id . "'";
		$mycms->sql_delete($sqlDelete);



		foreach ($hall_temp_name as $dateId => $tempName) {

			$sqlInsertSession = array();
			$sqlInsertSession['QUERY'] 			 = "INSERT INTO " . _DB_MASTER_HALL_NAME_ . " 
											  	   			    SET `hall_name` = '" . $tempName . "', 
																    `date_id` = '" . $dateId . "',
																    `hall_id` = '" . $hall_id . "'";
			$mycms->sql_insert($sqlInsertSession);
		}



		/*
			$sqlTrancate['QUERY']              = "DELETE FROM ".$cfg['DB.SP.MAPING.SC.TO.HALL']." 
		                                   WHERE `hall_id` = '".$hall_id."'";
			$mycms->sql_query($sqlTrancate);
			
			foreach($_REQUEST['session_type'] as $key => $value)
			{
				$sqlInsertMaping['QUERY']			 = "INSERT INTO ".$cfg['DB.SP.MAPING.SC.TO.HALL']." 
														SET `session_classification_id` = '".$value."', 
															`hall_id` = '".$hall_id ."'";
				$mycms->sql_insert($sqlInsertMaping);
			}			
			*/
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#hall_master";</script>';	exit();
		break;

	case 'HallInactive':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_MASTER_HALL_ . "
					   SET `status`='I'
					 WHERE id = ? ";
		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#hall_master";</script>';	exit();
		break;

	case 'HallActive':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_MASTER_HALL_ . "
					   SET `status`='A'
					 WHERE id = ? ";
		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#hall_master";</script>';	exit();
		break;

	case 'removeHall':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_MASTER_HALL_ . "
					   SET `status`='D'
					 WHERE id = ? ";
		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#hall_master";</script>';	exit();
		break;


	/////////////////////////////////////////

	case 'insertParticipantType':
		$type_name 				= addslashes(trim($_REQUEST['type_name']));

		// INSERTING SESSION DETAILS
		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "INSERT INTO " . _DB_SP_PARTICIPANT_TYPE_ . " 
											  	SET   `type_name` = ?";
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'conf_date',  'DATA' => $type_name,  'TYP' => 's');

		$mycms->sql_insert($sqlInsertSession);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#participant_master";</script>';	exit();
		break;

	case 'editParticipantType':
		$type_name 				= addslashes(trim($_REQUEST['type_name']));
		$id 				= addslashes(trim($_REQUEST['id']));

		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "UPDATE " . _DB_SP_PARTICIPANT_TYPE_ . " 
													  SET   `type_name` = ? WHERE `id`=?";
		$sqlInsertSession['PARAM'][]  = array(
			'FILD' => 'conf_date',
			'DATA' => $type_name,
			'TYP' => 's'
		);
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sqlInsertSession);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#participant_master";</script>';	exit();
		break;

	case 'removeParticipantType':
		$id 				= addslashes(trim($_REQUEST['id']));

		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "UPDATE " . _DB_SP_PARTICIPANT_TYPE_ . " 
													  SET   `status` ='D'  WHERE `id`=?";

		$sqlInsertSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');
		// print_r($sqlInsertSession);
		// die;

		$mycms->sql_update($sqlInsertSession);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#participant_master";</script>';	exit();
		break;

	/////////////////////////////////////////

	case 'insertSessionType':
		$type_name 				= addslashes(trim($_REQUEST['session_type_add']));

		// INSERTING SESSION DETAILS
		$sqlInsertSession = array();
		$sqlInsertSession['QUERY']	= "INSERT INTO " . _DB_SESSION_CLASSIFICATION . " 
											  	SET   `session_classifications` = ?, `status`='A'";
		$sqlInsertSession['PARAM'][]  = array('FILD' => 'session_classifications',  'DATA' => $type_name,  'TYP' => 's');

		$mycms->sql_insert($sqlInsertSession);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#session_master";</script>';	exit();
		break;

	case 'editSession':
		$type_name 				= addslashes(trim($_REQUEST['type_name']));
		$id 				= addslashes(trim($_REQUEST['id']));

		$sqlUpdateSession = array();
		$sqlUpdateSession['QUERY']	= "UPDATE " . _DB_SESSION_CLASSIFICATION . " 
													  SET   `session_classifications` = ? WHERE `id`=?";
		$sqlUpdateSession['PARAM'][]  = array(
			'FILD' => 'session_classifications',
			'DATA' => $type_name,
			'TYP' => 's'
		);
		$sqlUpdateSession['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sqlUpdateSession);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#session_master";</script>';	exit();
		break;

	case 'sessionInactive':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_SESSION_CLASSIFICATION . "
						   SET `status`='I'
						 WHERE id = ? ";
		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#session_master";</script>';	exit();
		break;

	case 'sessionActive':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_SESSION_CLASSIFICATION . "
						   SET `status`='A'
						 WHERE id = ? ";
		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#session_master";</script>';	exit();
		break;

	case 'removeSession':
		$id = $_REQUEST['id'];
		$sql = array();
		$sql['QUERY'] = "UPDATE " . _DB_SESSION_CLASSIFICATION . "
							   SET `status`='D'
							 WHERE id = ? ";
		$sql['PARAM'][]  = array('FILD' => 'id',  'DATA' => $id,  'TYP' => 's');

		$mycms->sql_update($sql);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="scientific_masterdata.php#session_master";</script>';	exit();
		break;



	/// Deprecated //

	case 'deleteDocument':
		$uniqueDocName = $_REQUEST['docUniqName'];
		$sql['QUERY'] = "DELETE FROM " . $cfg['DB.SP.PARTICIPANT.DOCUMENT'] . "
					WHERE `doc_unique_name` = '" . $uniqueDocName . "' ";

		$mycms->sql_delete($sql);
		$file_delete = "documents/" . $uniqueDocName;
		unlink($file_delete);
		echo "SUCESS";
		break;

	case 'insertClassification':
		$participant_classifications = addslashes(trim($_REQUEST['Participant_Classifications']));
		//echo "<pre>";print_r($_REQUEST);echo "</pre>";die();
		// INSERTING SESSION DETAILS
		$sqlInsertSession['QUERY'] 			 = "INSERT INTO " . $cfg['DB.SP.PARTICIPANT.CLASSIFICATION'] . " 
											  	    SET `participant_classifications` = '" . $participant_classifications . "', 
													    `status` = 'A'";
		$lastId = $mycms->sql_insert($sqlInsertSession);
		foreach ($_REQUEST['session_type'] as $key => $value) {
			$sqlInsertMaping['QUERY']			 = "INSERT INTO " . $cfg['DB.SP.MAPING.SC.TO.PC'] . " 
														SET `session_classification_id` = '" . $value . "', 
															`participation_classification_id` = '" . $lastId . "'";
			$mycms->sql_insert($sqlInsertMaping);
		}
		pageRedirection("additional_data.php", 1, "&data=participantClassification");
		break;

	case 'updateClassification':
		$classifications_id				= addslashes(trim($_REQUEST['classifications_id']));
		$participant_classifications 	= addslashes(trim($_REQUEST['participantClassifications']));

		// UPDATING SESSION DETAILS
		$sqlUpdateSession['QUERY']  = "UPDATE " . $cfg['DB.SP.PARTICIPANT.CLASSIFICATION'] . "
											  SET `participant_classifications` = '" . $participant_classifications . "' 
											WHERE `id` = '" . $classifications_id . "'";

		$mycms->sql_update($sqlUpdateSession);

		$sqlTrancate['QUERY']      = "DELETE FROM " . $cfg['DB.SP.MAPING.SC.TO.PC'] . " 
		                                   WHERE `participation_classification_id` = '" . $classifications_id . "'";
		$mycms->sql_delete($sqlTrancate);

		foreach ($_REQUEST['session_type'] as $key => $value) {
			$sqlInsertMaping['QUERY']			 = "INSERT INTO " . $cfg['DB.SP.MAPING.SC.TO.PC'] . " 
														SET `session_classification_id` = '" . $value . "', 
															`participation_classification_id` = '" . $classifications_id . "'";
			$mycms->sql_insert($sqlInsertMaping);
		}

		pageRedirection("additional_data.php", 2, "&data=participantClassification");
		exit();
		break;


	case 'InactiveClassification':
		$id = $_REQUEST['id'];
		$sql['QUERY'] = "UPDATE " . $cfg['DB.SP.PARTICIPANT.CLASSIFICATION'] . "
							   SET `status`='I'
							 WHERE id = '" . $id . "' ";
		$mycms->sql_update($sql);
		pageRedirection("additional_data.php", 2, "&data=participantClassification");
		break;

	case 'ActiveClassification':
		$id = $_REQUEST['id'];
		$sql['QUERY'] = "UPDATE " . $cfg['DB.SP.PARTICIPANT.CLASSIFICATION'] . "
							   SET `status`='A'
							 WHERE id = '" . $id . "' ";
		$mycms->sql_update($sql);
		pageRedirection("additional_data.php", 2, "&data=participantClassification");
		break;

	case 'insertSessionClassification':
		$session_classifications = addslashes(trim($_REQUEST['Session_Classifications']));
		$session_type			 = addslashes(trim($_REQUEST['sessionType']));

		// INSERTING SESSION DETAILS
		$sqlInsertSession['QUERY'] 			 = "INSERT INTO " . $cfg['DB.SP.SESSION.CLASSIFICATION'] . " 
											  	    SET `session_classifications` = '" . $session_classifications . "',
														`session_type` = '" . $session_type . "', 
													    `status` = 'A'";
		$mycms->sql_insert($sqlInsertSession);
		pageRedirection("additional_data.php", 1, "&data=sessionClassification");
		break;

	case 'updateSessionClassification':
		$classifications_id				= addslashes(trim($_REQUEST['classifications_id']));
		$participant_classifications 	= addslashes(trim($_REQUEST['Session_Classifications']));
		$session_type			  	 	= addslashes(trim($_REQUEST['sessionType']));

		// UPDATING SESSION DETAILS
		$sqlUpdateSession['QUERY']	= "UPDATE " . $cfg['DB.SP.SESSION.CLASSIFICATION'] . "
											  SET `session_classifications` = '" . $participant_classifications . "',
											  	  `session_type` = '" . $session_type . "'  
											WHERE `id` = '" . $classifications_id . "'";

		$mycms->sql_update($sqlUpdateSession);

		pageRedirection("additional_data.php", 2, "&data=sessionClassification");
		exit();
		break;

	case 'InactiveSessionClassification':
		$id = $_REQUEST['id'];
		$sql['QUERY'] = "UPDATE " . $cfg['DB.SP.SESSION.CLASSIFICATION'] . "
							   SET `status`='I'
							 WHERE id = '" . $id . "' ";
		$mycms->sql_update($sql);
		pageRedirection("additional_data.php", 2, "&data=sessionClassification");
		break;

	case 'ActiveSessionClassification':
		$id = $_REQUEST['id'];
		$sql['QUERY'] = "UPDATE " . $cfg['DB.SP.SESSION.CLASSIFICATION'] . "
							   SET `status`='A'
							 WHERE id = '" . $id . "' ";
		$mycms->sql_update($sql);
		pageRedirection("additional_data.php", 2, "&data=sessionClassification");
		break;



	case 'regIdvalidation':

		$sql['QUERY'] = "SELECT * FROM " . _DB_USER_REGISTRATION_ . " WHERE `user_registration_id` = '" . $_REQUEST['regId'] . "' AND `status` = 'A'";
		$results = $mycms->sql_select($sql);

		$dataString                 = '{';
		if ($results) {
			$row	 = $results[0];

			$dataString                .= '"ID": "' . $row['id'] . '",';
			$dataString                .= '"COLOR": "green",';
			$dataString                .= '"NAME": "' . $row['user_full_name'] . '",';
			$dataString                .= '"INSTITUTE": "' . $row['user_institute_name'] . '",';
			$dataString                .= '"EMAIL_ID": "' . $row['user_email_id'] . '",';
			$dataString                .= '"MOBILE_NO": "' . $row['user_mobile_no'] . '",';
			$dataString                .= '"MESSAGE": "Available"';
		} else {
			$dataString                .= '"ID": "",';
			$dataString                .= '"COLOR": "red",';
			$dataString                .= '"MESSAGE": "Wrong Registration Id"';
		}
		$dataString                .= '}';
		echo $dataString;
		break;
}



/******************************************************************************/
/*                                 UTILITY METHOD                             */
/******************************************************************************/
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey                       		       		 = "_pgn_";
	$pageKeyVal                    		       		 = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString                 		       		 = "";
	$searchArray                   		       		 = array();

	$searchArray[$pageKey]         		       		 = $pageKeyVal;
	$searchArray['src_applicant_first_name']  		 = trim($_REQUEST['src_applicant_first_name']);
	$searchArray['src_applicant_middle_name']  		 = trim($_REQUEST['src_applicant_middle_name']);
	$searchArray['src_applicant_last_name']  		 = trim($_REQUEST['src_applicant_last_name']);
	$searchArray['src_applicant_access_key']  		 = trim($_REQUEST['src_applicant_access_key']);
	$searchArray['src_abstract_submission_code']  	 = trim($_REQUEST['src_abstract_submission_code']);
	$searchArray['src_applicant_email_id']  		 = trim($_REQUEST['src_applicant_email_id']);
	$searchArray['src_paper_presentation_category']  = trim($_REQUEST['src_paper_presentation_category']);
	$searchArray['src_apply_from_date']  			 = trim($_REQUEST['src_apply_from_date']);
	$searchArray['src_apply_to_date']  				 = trim($_REQUEST['src_apply_to_date']);

	foreach ($searchArray as $searchKey => $searchVal) {
		if ($searchVal != "") {
			$searchString .= "&" . $searchKey . "=" . $searchVal;
		}
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}


function participateImageUpload($participantId, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'PART_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;
     	$uploadDir = realpath(__DIR__ . "/../" . $cfg['SP.PARTICIPANT.DOC']); // resolves parent folder
		$userImagePath = $uploadDir . "/" . $userImageFileName; // append filename	

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_PROGRAM_SCHEDULE_DATE_ . "
														  SET `conf_image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function speakerImageUpload($participantId, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'PART_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

        $uploadDir = realpath(__DIR__ . "/../" . $cfg['SP.PARTICIPANT.DOC']); // resolves parent folder
		$userImagePath = $uploadDir . "/" . $userImageFileName; // append filename	

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . "
														  SET `image` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}
