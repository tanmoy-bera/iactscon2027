<?php
	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action)
	{
        case'insertSession':
    //    echo "<pre>";
    //         print_r($_REQUEST);
    //         die();
            $session_date 				= addslashes(trim($_REQUEST['session_date']));
            $session_title 				= addslashes(trim($_REQUEST['session_title']));

            $session_strating_time		= addslashes(trim($_REQUEST['session_strating_time']));
            $session_ending_time		= addslashes(trim($_REQUEST['session_ending']));

            $startTmExpld				= explode(":", $session_strating_time);
            $endTmExpld					= explode(":", $session_ending_time);

            $session_strating_time		= (($startTmExpld[0] < 10 && strlen($startTmExpld[0]) < 2) ? ("0" . $startTmExpld[0]) : $startTmExpld[0])
                . ":"
                . (($startTmExpld[1] < 10 && strlen($startTmExpld[1]) < 2) ? ("0" . $startTmExpld[1]) : $startTmExpld[1]);

            $session_ending_time		= (($endTmExpld[0] < 10 && strlen($endTmExpld[0]) < 2) ? ("0" . $endTmExpld[0]) : $endTmExpld[0])
                . ":"
                . (($endTmExpld[1] < 10 && strlen($endTmExpld[1]) < 2) ? ("0" . $endTmExpld[1]) : $endTmExpld[1]);

            $session_duration			= getTimeDiff($session_strating_time, $session_ending_time);

            $hall_id					= $_REQUEST['hall_id'];
            //$session_tag_Arr			= $_REQUEST['session_tag'];
            $session_color 				= $_REQUEST['sessioncolor'];
            $session_classifications_id	= $_REQUEST['session_classifications_id'];
            $parallel_timing = $_REQUEST['parallel_timing'] ?? 'N';
            // INSERTING SESSION DETAILS
            $sqlInsertSession = array();
            $sqlInsertSession['QUERY'] 			= "INSERT INTO " . _DB_PROGRAM_SCHEDULE_SESSION_ . " 
                                                            SET `session_title` 				= '" . $session_title . "', 
                                                                `session_date_id` 			= '" . $session_date . "', 
                                                                `session_color` 				= '" . $session_color . "',
                                                                `session_hall_id` 			= '" . $hall_id . "',
                                                                `session_start_time` 		= '" . $session_strating_time . "',
                                                                `session_end_time` 			= '" . $session_ending_time . "',
                                                                `session_duration` 			= '" . $session_duration . "',
                                                                `session_classifications_id` = '" . $session_classifications_id . "',
																`parallel_timing`            = '" . $parallel_timing . "',
                                                                `status` 					= 'A', 
                                                                `operationMode` 				= 'ORAL_PRESENTATION',
                                                                `created_by` 				= '" . $loggedUserID . "',
                                                                `created_ip` 				= '" . $_SERVER['REMOTE_ADDR'] . "',
                                                                `created_sessionId` 			= '" . session_id() . "',
                                                                `created_dateTime` 			= '" . date('Y-m-d H:i:s') . "'";


          
             $sessionId 							= $mycms->sql_insert($sqlInsertSession);
			 
             if($sessionId){
                $schedule_theme_title		= $_REQUEST['schedule_theme_title'];
                foreach ($schedule_theme_title as $group => $ValueGroup) {
                    $schedule_id					= $sessionId;
                    $date_id						= $session_date;
                    $hall_id						= $hall_id;
                    $venue_id						= addslashes(trim($_REQUEST['schedule_session_venue_id']));

                    $noTheme						= addslashes(trim($_REQUEST['noTheme']));

                    $theme_title					= addslashes(trim($_REQUEST['schedule_theme_title'][$group]));

                    $start_time						= addslashes(trim($_REQUEST['theme_start'][$group]));
                    $end_time						= addslashes(trim($_REQUEST['theme_end'][$group]));

                    $color							= addslashes(trim($_REQUEST['color'][$group]));

                    $start_time						= timeReComposer($start_time);
                    $end_time						= timeReComposer($end_time);

                    $theme_time_slot		    	= $start_time . '-' . $end_time;

                    if ($venue_id == "") $venue_id	= 0;
                    if ($hall_id == "")  $hall_id		= 0;

                    // COMPOSING PROGRAM SCHEDULE THEME
                    $sqlComposeTheme = array();
                    $sqlComposeTheme['QUERY']			= "INSERT INTO " . _DB_PROGRAM_SCHEDULE_THEME_ . "
                                                                    SET `theme_title` = '" . $theme_title . "', 
                                                                        `schedule_id` = '" . $schedule_id . "', 
                                                                        `theme_allocated_hall_id` = '" . $hall_id . "', 
                                                                        `date_id` = '" . $date_id . "',												   
                                                                        `venue_id` = '" . $venue_id . "',
                                                                        `theme_time_start` = '" . $start_time . "',
                                                                        `theme_time_end` = '" . $end_time . "',
                                                                        `theme_time_slot` = '" . $theme_time_slot . "', 				
                                                                        `theme_color` = '" . $color . "',
																		`noTheme` = '" . $noTheme . "', 												   
                                                                        `status` = 'A', 
                                                                        `operationMode` = 'GENERAL',
                                                                        `created_by` = '" . $loggedUserID . "',
                                                                        `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                                                                        `created_sessionId` = '" . session_id() . "',
                                                                        `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
          
                    $lastInsertedThemeId	    = $mycms->sql_insert($sqlComposeTheme);
					
                    $_REQUEST['schedule_theme_id'] 		= $lastInsertedThemeId;
                    $_REQUEST['schedule_session_id']	= $schedule_id;
                    $_REQUEST['schedule_allocated_hall_id']	= $hall_id;
                    $_REQUEST['schedule_session_date_id']	= $date_id;
                    $_REQUEST['schedule_session_venue_id']	= $venue_id;

                     insertTopic($mycms, $cfg,$group);
                    if ($noTheme == 'N') {
                        $participantSchudule = array();
                        $participantId 	 	 = $_REQUEST['schedule_theme_participant_id'][$group];
                        $participantName 	 = $_REQUEST['schedule_theme_participant_name'][$group];
                        $participatingAs 	 = $_REQUEST['schedule_theme_participant_as'][$group];
                        $isFaculty 	   		 = $_REQUEST['isFaculty'][$group];

                        if (is_array($participantId) && sizeof($participantId) > 0) {
                            foreach ($participantId as $Key => $Value) {
                                if ($Value != "") {
                                    $counter++;
                                    $participantSchudule[$counter]['participant_id'] = $Value;
                                    $participantSchudule[$counter]['session_id'] = $schedule_id;
                                    $participantSchudule[$counter]['theme_id'] = $lastInsertedThemeId;
                                    $participantSchudule[$counter]['topic_id'] = NULL;
                                    $participantSchudule[$counter]['participant_type'] = $participatingAs[$Key];
                                    $participantSchudule[$counter]['date_id'] = $date_id;
                                    $participantSchudule[$counter]['start_time'] = $start_time;
                                    $participantSchudule[$counter]['end_time'] = $end_time;
                                    $participantSchudule[$counter]['isFaculty'] 	   = $isFaculty[$Key];
                                }
                            }
                        }

                         composeParticipantSchedule($participantSchudule);
                    }
					if ($_REQUEST['participant_id_session'] != '') {
                        $participantSchudule = array();
                        $participantId 	 	 = $_REQUEST['participant_id_session'];
                        $participantName 	 = $_REQUEST['participant_name_session'];
                        $participatingAs 	 = $_REQUEST['participant_type_session'];
                        $isFaculty 	   		 = 'Faculty';

                        if (is_array($participantId) && sizeof($participantId) > 0) {
                            foreach ($participantId as $Key => $Value) {
                                if ($Value != "") {
                                    $counter++;
                                    $participantSchudule[$counter]['participant_id'] = $Value;
                                    $participantSchudule[$counter]['session_id'] = $schedule_id;
                                    $participantSchudule[$counter]['theme_id'] =NULL;
                                    $participantSchudule[$counter]['topic_id'] = NULL;
                                    $participantSchudule[$counter]['participant_type'] = $participatingAs[$Key];
                                    $participantSchudule[$counter]['date_id'] = $date_id;
                                    $participantSchudule[$counter]['start_time'] = $start_time;
                                    $participantSchudule[$counter]['end_time'] = $end_time;
                                    $participantSchudule[$counter]['isFaculty'] 	   = $isFaculty;
                                }
                            }
                        }

                         composeParticipantSchedule($participantSchudule);
                    }
                }
             }

           $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
         echo '<script>window.location.href="program_schedule.php?dateId=' . urlencode($session_date) . '&hallId=' . urlencode($hall_id) . '";</script>';
        exit();
        break;
		case 'updateSession':
			// echo "<pre>";
            // print_r($_REQUEST);
            // die();
			$session_id = addslashes(trim($_REQUEST['session_id']));
			$session_date = addslashes(trim($_REQUEST['session_date']));
			$session_title = addslashes(trim($_REQUEST['session_title']));
			$session_strating_time = addslashes(trim($_REQUEST['session_strating_time']));
			$session_ending_time = addslashes(trim($_REQUEST['session_ending']));
			$hall_id = addslashes(trim($_REQUEST['hall_id']));
			$session_color = addslashes(trim($_REQUEST['sessioncolor']));
			$session_classifications_id = addslashes(trim($_REQUEST['session_classifications_id']));
			$noTheme = addslashes(trim($_REQUEST['noTheme'] ?? 'N'));
		    $parallel_timing	=addslashes(trim($_REQUEST['parallel_timing'] ?? 'N'));

			// Format times
			$startTmExpld = explode(":", $session_strating_time);
			$endTmExpld = explode(":", $session_ending_time);
			
			$session_strating_time = (($startTmExpld[0] < 10 && strlen($startTmExpld[0]) < 2) ? ("0" . $startTmExpld[0]) : $startTmExpld[0])
				. ":"
				. (($startTmExpld[1] < 10 && strlen($startTmExpld[1]) < 2) ? ("0" . $startTmExpld[1]) : $startTmExpld[1]);
			
			$session_ending_time = (($endTmExpld[0] < 10 && strlen($endTmExpld[0]) < 2) ? ("0" . $endTmExpld[0]) : $endTmExpld[0])
				. ":"
				. (($endTmExpld[1] < 10 && strlen($endTmExpld[1]) < 2) ? ("0" . $endTmExpld[1]) : $endTmExpld[1]);
			
			$session_duration = getTimeDiff($session_strating_time, $session_ending_time);
			
			// UPDATE SESSION DETAILS
			$sqlUpdateSession = array();
			$sqlUpdateSession['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_SESSION_ . " 
				SET `session_title` = '" . $session_title . "', 
					`session_date_id` = '" . $session_date . "', 
					`session_color` = '" . $session_color . "',
					`session_hall_id` = '" . $hall_id . "',
					`session_start_time` = '" . $session_strating_time . "',
					`session_end_time` = '" . $session_ending_time . "',
					`session_duration` = '" . $session_duration . "',
					`session_classifications_id` = '" . $session_classifications_id . "',
					`parallel_timing`            = '" . $parallel_timing . "',
					`modified_by` = '" . $loggedUserID . "',
					`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
					`modified_sessionId` = '" . session_id() . "',
					`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
				WHERE `id` = '" . $session_id . "'";
			
			$mycms->sql_update($sqlUpdateSession);
			// ==========================================================
			$sqlDeleteSessionParticipants = array();
			$sqlDeleteSessionParticipants['QUERY'] = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
				WHERE `session_id` = '" . $session_id . "' 
				AND (`theme_id` IS NULL OR `theme_id` = '') 
				AND (`topic_id` IS NULL OR `topic_id` = '')";
			$mycms->sql_delete($sqlDeleteSessionParticipants);

			// Insert session-level participants with duplicate prevention
			if (isset($_REQUEST['participant_id_session']) && is_array($_REQUEST['participant_id_session'])) {
				$seenParticipants = array(); // Store combination of participant_id + participant_type
				$participantIds = $_REQUEST['participant_id_session'];
				$participantNames = $_REQUEST['participant_name_session'] ?? array();
				$participantTypes = $_REQUEST['participant_type_session'] ?? array();
				
				foreach ($participantIds as $key => $participantId) {
					// Skip empty participant IDs
					if (empty($participantId)) {
						continue;
					}
					
					$participantType = $participantTypes[$key] ?? '';
					
					// Create unique key combining participant_id and participant_type
					$uniqueKey = $participantId . '|' . $participantType;
					
					// Skip if this combination already exists
					if (in_array($uniqueKey, $seenParticipants)) {
						continue;
					}
					$seenParticipants[] = $uniqueKey;
					
					$participantName = $participantNames[$key] ?? '';
					$isFaculty = 'Faculty';
					
					// Use session start/end time for session-level participants
					$sqlInsertParticipant = array();
					$sqlInsertParticipant['QUERY'] = "INSERT INTO " . _DB_SP_PARTICIPANT_SCHEDULE_ . "
						SET `participant_id` = '" . addslashes($participantId) . "',
							`session_id` = '" . $session_id . "',
							`theme_id` = NULL,
							`topic_id` = NULL,
							`participant_type` = '" . addslashes($participantType) . "',
							`date_id` = '" . $session_date . "',
							`start_time` = '" . $session_strating_time . "',
							`end_time` = '" . $session_ending_time . "'";
					
					$mycms->sql_insert($sqlInsertParticipant);
					
					// Update participant details if needed
					if ($isFaculty != 'N' && $isFaculty != "") {
						$sqlUpdateParticipant = array();
						$sqlUpdateParticipant['QUERY'] = "UPDATE " . _DB_SP_PARTICIPANT_DETAILS_ . " 
							SET `participation_type` = '" . strtoupper($isFaculty) . "'
							WHERE `id` = '" . $participantId . "'";
						$mycms->sql_update($sqlUpdateParticipant);
					}
				}
			}
			// GET EXISTING THEME IDs TO COMPARE
			$existingThemes = array();
			$sqlGetThemes  = array();
			$sqlGetThemes['QUERY'] = "SELECT id FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " WHERE schedule_id = '" . $session_id . "'";
			$resultThemes = $mycms->sql_select($sqlGetThemes);
			foreach ($resultThemes as $theme) {
				$existingThemes[] = $theme['id'];
			}
			
			$updatedThemeIds = array();
			$venue_id = addslashes(trim($_REQUEST['schedule_session_venue_id'] ?? 0));
			
			// PROCESS EACH GROUP (THEME)
			if (isset($_REQUEST['schedule_theme_title']) && is_array($_REQUEST['schedule_theme_title'])) {
				foreach ($_REQUEST['schedule_theme_title'] as $group => $themeTitle) {
					// if ($noTheme == 'Y' && empty($themeTitle)) {
					// 	continue;
					// }
					
					$theme_title = addslashes(trim($themeTitle));
					$start_time = addslashes(trim($_REQUEST['theme_start'][$group] ?? ''));
					$end_time = addslashes(trim($_REQUEST['theme_end'][$group] ?? ''));
					$color = addslashes(trim($_REQUEST['Grpcolor'][$group] ?? '#DBDBDB'));
					
					$start_time = timeReComposer($start_time);
					$end_time = timeReComposer($end_time);
					$theme_time_slot = $start_time . '-' . $end_time;
					
					$existingThemeId = $_REQUEST['existing_theme_id'][$group] ?? 0;
					
					if ($existingThemeId > 0) {
						// UPDATE EXISTING THEME
						$sqlUpdateTheme =array();
						$sqlUpdateTheme['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_THEME_ . "
							SET `theme_title` = '" . $theme_title . "',
								`theme_allocated_hall_id` = '" . $hall_id . "',
								`date_id` = '" . $session_date . "',
								`venue_id` = '" . $venue_id . "',
								`theme_time_start` = '" . $start_time . "',
								`theme_time_end` = '" . $end_time . "',
								`theme_time_slot` = '" . $theme_time_slot . "',
								`theme_color` = '" . $color . "',
								`noTheme` = '" . $noTheme . "', 			
								`modified_by` = '" . $loggedUserID . "',
								`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
								`modified_sessionId` = '" . session_id() . "',
								`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
							WHERE `id` = '" . $existingThemeId . "'";
						
						$mycms->sql_update($sqlUpdateTheme);
						$lastInsertedThemeId = $existingThemeId;
						$updatedThemeIds[] = $existingThemeId;
					} else {
						// INSERT NEW THEME
						$sqlInsertTheme =array();
						$sqlInsertTheme['QUERY'] = "INSERT INTO " . _DB_PROGRAM_SCHEDULE_THEME_ . "
							SET `theme_title` = '" . $theme_title . "',
								`schedule_id` = '" . $session_id . "',
								`theme_allocated_hall_id` = '" . $hall_id . "',
								`date_id` = '" . $session_date . "',
								`venue_id` = '" . $venue_id . "',
								`theme_time_start` = '" . $start_time . "',
								`theme_time_end` = '" . $end_time . "',
								`theme_time_slot` = '" . $theme_time_slot . "',
								`theme_color` = '" . $color . "',
								`noTheme` = '" . $noTheme . "', 			
								`status` = 'A',
								`operationMode` = 'GENERAL',
								`created_by` = '" . $loggedUserID . "',
								`created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
								`created_sessionId` = '" . session_id() . "',
								`created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
						
						$lastInsertedThemeId = $mycms->sql_insert($sqlInsertTheme);
						$updatedThemeIds[] = $lastInsertedThemeId;
					}
					
					// UPDATE PARTICIPANTS FOR THIS THEME (Delete old, insert new)
					if ($noTheme == 'N') {
						// Delete existing participants for this theme
						$sqlDeleteThemeParticipants =array();
						$sqlDeleteThemeParticipants['QUERY'] = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
							WHERE `session_id` = '" . $session_id . "' 
							AND `theme_id` = '" . $lastInsertedThemeId . "' 
							AND (`topic_id` IS NULL OR `topic_id` = '')";
						$mycms->sql_delete($sqlDeleteThemeParticipants);
						
						// Insert new participants
						$participantIds = $_REQUEST['schedule_theme_participant_id'][$group] ?? [];
						$participantAs = $_REQUEST['schedule_theme_participant_as'][$group] ?? [];
						$isFaculties = $_REQUEST['isFaculty'][$group] ?? [];
						
						if (is_array($participantIds) && count($participantIds) > 0) {
							foreach ($participantIds as $key => $participantId) {
								if (!empty($participantId)) {
									$participantType = $participantAs[$key] ?? '';
									$isFaculty = $isFaculties[$key] ?? 'N';
									$sqlInsertParticipant = array();
									$sqlInsertParticipant['QUERY'] = "INSERT INTO " . _DB_SP_PARTICIPANT_SCHEDULE_ . "
										SET `participant_id` = '" . addslashes($participantId) . "',
											`session_id` = '" . $session_id . "',
											`theme_id` = '" . $lastInsertedThemeId . "',
											`topic_id` = NULL,
											`participant_type` = '" . addslashes($participantType) . "',
											`date_id` = '" . $session_date . "',
											`start_time` = '" . $start_time . "',
											`end_time` = '" . $end_time . "'";
									
									$mycms->sql_insert($sqlInsertParticipant);
									if($isFaculty!='N' && $isFaculty!="")
									{
										$sqlInsertSession  = array(); 
										$sqlInsertSession['QUERY'] 			= " UPDATE "._DB_SP_PARTICIPANT_DETAILS_." 
																				SET `participation_type` = '".strtoupper($isFaculty)."'
																				WHERE `id` = '".$participantId."'";
										$mycms->sql_update($sqlInsertSession);
									}
								}
							}
						}
					}else {
						// group mode turned off -> remove leftover group-level faculty
						$sqlDelLeft = array();
						$sqlDelLeft['QUERY'] = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . "
							WHERE `session_id` = '" . $session_id . "'
							AND `theme_id` = '" . $lastInsertedThemeId . "'
							AND (`topic_id` IS NULL OR `topic_id` = '')";
						$mycms->sql_delete($sqlDelLeft);
					}
					
					// UPDATE TOPICS FOR THIS THEME
					if (isset($_REQUEST['topic_id'][$group]) && is_array($_REQUEST['topic_id'][$group])) {
						// Delete existing topics for this theme
						// $sqlDeleteTopics = array();
						// $sqlDeleteTopics['QUERY'] = "DELETE FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
						// 	WHERE `schedule_theme_id` = '" . $lastInsertedThemeId . "'";
						// $mycms->sql_delete($sqlDeleteTopics);
						
						// Call the updateTopic function
						updateTopic($mycms, $cfg, $group, $session_id, $lastInsertedThemeId, $hall_id, $session_date, $start_time, $end_time);
					}
				}
			}
			
			// DELETE REMOVED THEMES
			$themesToDelete = array_diff($existingThemes, $updatedThemeIds);
			foreach ($themesToDelete as $themeIdToDelete) {
				// Delete topic participants first
				$sqlDeleteTopicParticipants = array();
				$sqlDeleteTopicParticipants['QUERY'] = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " WHERE theme_id = '" . $themeIdToDelete . "'";
				$mycms->sql_delete($sqlDeleteTopicParticipants);
				
				// Delete topics
				$sqlDeleteTopics = array();
				$sqlDeleteTopics['QUERY'] = "DELETE FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " WHERE schedule_theme_id = '" . $themeIdToDelete . "'";
				$mycms->sql_delete($sqlDeleteTopics);
				
				// Delete theme participants
				$sqlDeleteThemeParticipants = array();
				$sqlDeleteThemeParticipants['QUERY'] = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " WHERE theme_id = '" . $themeIdToDelete . "'";
				$mycms->sql_delete($sqlDeleteThemeParticipants);
				
				// Delete theme
				$sqlDeleteTheme = array();
				$sqlDeleteTheme['QUERY'] = "DELETE FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " WHERE id = '" . $themeIdToDelete . "'";
				$mycms->sql_delete($sqlDeleteTheme);
			}
			
			$_SESSION['toaster'] = [
				'type' => 'success',
				'message' => 'Session updated successfully!'
			];
			
           echo '<script>window.location.href="program_schedule.php?dateId=' . urlencode($session_date) . '&hallId=' . urlencode($hall_id) . '";</script>';
			exit();
		break;
		case 'getSessionRemoverWindow':
		getSessionRemoverWindow($mycms, $cfg);
		break;
		case 'searchParticipant':

			$data = [];

			if (!empty($_REQUEST['search'])) {

				$search = $_REQUEST['search'];
                $sqlParticipant = array();
				$sqlParticipant['QUERY'] = "
					SELECT id, participant_full_name
					FROM " . _DB_SP_PARTICIPANT_DETAILS_ . "
					WHERE status = 'A'
					AND participant_full_name LIKE '%" . addslashes($search) . "%'
					ORDER BY participant_full_name
					LIMIT 20
				";

				$resultParticipant = $mycms->sql_select($sqlParticipant);
               
				if (!empty($resultParticipant)) {

					foreach ($resultParticipant as $row) {

						$data[] = array(
							'ID'   => $row['id'],
							'NAME' => $row['participant_full_name']
						);
					}

				} else {
					$data = [];
				}

			}

			echo json_encode($data);

		break;
	case 'removeSession':
		removeSession($mycms, $cfg);
		break;
	case 'getThemeRemoverWindow':
		getThemeRemoverWindow($mycms, $cfg);
		break;

	case 'removeTheme':
		removeTheme($mycms, $cfg);
		break;
   case 'getTopicRemoverWindow':
		getTopicRemoverWindow($mycms, $cfg);
		break;

	case 'removeTopic':
		removeTopic($mycms, $cfg);
		break;
		
	case 'removeThemeParticipant':
		removeThemeParticipant($mycms, $cfg);
		break;
	case 'removeTopicParticipant':
		removeTopicParticipant($mycms, $cfg);
		break;
	case 'insertThemeParticipant':
		insertThemeParticipant($mycms, $cfg);
		break;
	case 'insertTopicParticipant':
		insertTopicParticipant($mycms, $cfg);
		break;
		
	case 'downloadScheduleExcel':
		downloadScheduleExcel($mycms, $cfg);
		break;

	case 'getPDFDisplay':
		getPDFDisplay($mycms, $cfg);
		break;

	case "printSession":
		printDisplayTemplate($mycms, $cfg);
		break;
    }

function getSessionDetails($sessionId)
{
	global $cfg, $mycms;
	$sqlSelectSession = array();
	$sqlSelectSession['QUERY']     = "SELECT session.*,
										   (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60) AS session_start_time_mins,
										   (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60) AS session_end_time_mins,
										   hall.hall_title,
										   venue.id AS venue_id,
										   venue.program_venue,
										   scheduleDate.conf_date AS session_date  
									  
									  FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
									   
								INNER JOIN "._DB_PROGRAM_SCHEDULE_DATE_." scheduleDate 
										ON session.session_date_id = scheduleDate.id
										
								INNER JOIN "._DB_MASTER_HALL_." hall 
										ON session.session_hall_id = hall.id
										
								INNER JOIN "._DB_PROGRAM_SCHEDULE_VENUE_." venue 
										ON hall.hall_venue = venue.id
										 
									 WHERE session.id = '".$sessionId."'";
										   
	$resultSession			 = $mycms->sql_select($sqlSelectSession);
	return $resultSession[0];
}

function getSessionDateId($sessionId)
{
	global $cfg, $mycms;
	$sqlSelectSession = array();
	$sqlSelectSession['QUERY']        = "    SELECT session.session_date_id  
									  FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
									 WHERE session.id = '".$sessionId."'";
										   
	$resultSession			 = $mycms->sql_select($sqlSelectSession);
	return $resultSession[0]['session_date_id'];
}

function getThemeDetails($themeId)
{
	global $cfg, $mycms;
	$sqlFetchTheme = array();
	$sqlFetchTheme['QUERY']           = "SELECT *, 
									   (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60) AS theme_time_start_mins,
									   (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60) AS theme_time_end_mins
								  FROM "._DB_PROGRAM_SCHEDULE_THEME_." 
								 WHERE id = '".$themeId."'";													 
	$resultTheme   = $mycms->sql_select($sqlFetchTheme);	
	return $resultTheme[0];
}

function getThemeParticipants($themeId)
{
	global $cfg, $mycms;
	
	$return			= array();
	$rowTheme		= getThemeDetails($themeId);
	$sqlParticipantTheme = array();	
	$sqlParticipantTheme['QUERY']		 = "SELECT sch.*
									  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
								INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
										ON sch.participant_id = prt.id
									 WHERE sch.`session_id` = '".$rowTheme['schedule_id']."'
									   AND sch.`theme_id` = '".$themeId."'
									   AND (sch.topic_id IS NULL OR sch.topic_id = '')";
	$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme); 
	
	$return['THEME']			 = $resultsParticipantTheme;
	$sqlFetchTopic  = array();
	$sqlFetchTopic['QUERY']           	 = "SELECT *
									  FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." 
									 WHERE schedule_session_id = '".$rowTheme['schedule_id']."'
									   AND schedule_theme_id = '".$themeId."'
									   AND schedule_hall_id = '".$rowTheme['theme_allocated_hall_id']."'";													 
	$resultTopic   			 	= $mycms->sql_select($sqlFetchTopic);	
	foreach($resultTopic as $ktp=>$rowTopic)
	{
		$sqlParticipantTheme = array();
		$sqlParticipantTheme['QUERY']		 = "SELECT sch.*
										  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
									INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
											ON sch.participant_id = prt.id
										 WHERE sch.`session_id` = '".$rowTheme['schedule_id']."'
										   AND sch.`theme_id` = '".$themeId."'
										   AND sch.topic_id = '".$rowTopic['id']."'";
		$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme); 
		$return['TOPIC'][$rowTopic['id']] = $resultsParticipantTheme;
	}
	return $return;
}

function getTopicDetails($topicId)
{
	global $cfg, $mycms;
	$sqlFetchTopic = array();
	$sqlFetchTopic['QUERY'] = "SELECT *, 
							 topic_time_duration AS topic_time_duration_mins,
							 (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
							 (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60) AS topic_time_end_mins
						FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." 
					   WHERE id = '".$topicId."'";													 
	$resultTopic 	= $mycms->sql_select($sqlFetchTopic);	
	return $resultTopic[0];
} 

function getTopicParticipants($topicId)
{
	global $cfg, $mycms;
	
	$return			= array();
	$rowTopic		= getTopicDetails($topicId);
	$sqlParticipantTheme = array();
	$sqlParticipantTheme['QUERY']		 = "SELECT sch.*
									  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
								INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
										ON sch.participant_id = prt.id
									 WHERE sch.`session_id` = '".$rowTopic['schedule_session_id']."'
									   AND sch.`theme_id` = '".$rowTopic['schedule_theme_id']."'
									   AND sch.topic_id = '".$topicId."'";
	$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme); 
	$return['TOPIC']			 = $resultsParticipantTheme;
	return $return;
}

function getDateTheme($themeId) // GET TIME OF THEME
{
	global $cfg, $mycms;
	$sql = array();
	$sql['QUERY']		 = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_THEME_." WHERE status = 'A' AND id = '".$themeId."'";
	$result      = $mycms->sql_select($sql);
	return $result[0]['date_id'];
}

function getTimeTheme($themeId) // GET TIME ARRAY OF THEME
{
	global $cfg, $mycms;
	$sql = array();
	$sql['QUERY']		 = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_THEME_." WHERE status = 'A' AND id = '".$themeId."'";
	$result      = $mycms->sql_select($sql);
	$timeArr = explode("-",$result[0]['theme_time_slot']);
	return $timeArr;
}

function getTimeTopic($topicId) // GET TIME ARRAY OF TOPIC
{
	global $cfg, $mycms;
	$sql = array();
	$sql['QUERY']		 = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." WHERE status = 'A' AND id = '".$topicId."'";
	$result      = $mycms->sql_select($sql);
	$timeArr = explode("-",$result[0]['topic_time_slot']);
	return $timeArr;
}

function composeParticipantSchedule($dtlsArr) // INSERTING PROCESS OF SCHEDULE
{
	global $cfg, $mycms;
	if($dtlsArr)
	{	
		foreach($dtlsArr as $Key=>$Value)
		{
			if($Value['theme_id']==NULL)
			{
				$themeId = "NULL";
			}
			else
			{
				$themeId = "'".$Value['theme_id']."'";
			}
			
			if($Value['topic_id']==NULL)
			{
				$topicId = "NULL";
			}
			else
			{
				$topicId = "'".$Value['topic_id']."'";
			}
			
			$sqlDelete = array();
			$sqlDelete['QUERY']				 = "DELETE FROM "._DB_SP_PARTICIPANT_SCHEDULE_."
												 WHERE `participant_id` = '".$Value['participant_id']."'
												   AND `session_id` = '".$Value['session_id']."'
												   AND `theme_id` = ".$themeId."
												   AND `topic_id` = ".$topicId."
												   AND `date_id` = '".$Value['date_id']."'";
			$mycms->sql_query($sqlDelete['QUERY']);
			
			$start_time				= timeReComposer($Value['start_time']);
			$end_time				= timeReComposer($Value['end_time']);
			
			$sqlCompose = array();
			$sqlCompose['QUERY']		 = "INSERT INTO "._DB_SP_PARTICIPANT_SCHEDULE_."
											   SET `participant_id` = '".$Value['participant_id']."', 
												   `session_id` = '".$Value['session_id']."', 
												   `theme_id` = ".$themeId.",
												   `topic_id` = ".$topicId.",
												   `participant_type` = '".$Value['participant_type']."',
												   `date_id` = '".$Value['date_id']."', 
												   `start_time` = '".$start_time."', 
												   `end_time` = '".$end_time."'";	   
			$mycms->sql_insert($sqlCompose);
			
			if($Value['isFaculty']!='N' && $Value['isFaculty']!="")
			{
			    $sqlInsertSession  = array(); 
				$sqlInsertSession['QUERY'] 			= " UPDATE "._DB_SP_PARTICIPANT_DETAILS_." 
														   SET `participation_type` = '".strtoupper($Value['isFaculty'])."'
														 WHERE `id` = '".$Value['participant_id']."'";
				$mycms->sql_update($sqlInsertSession);
			}
			
		}
	}
}

function insertTopicAgainstTheme($hallId, $sessionId, $themeId, $formType,$startTime) // INSERTING PROCESS OF TOPIC
{
	global $cfg, $mycms;
	
	$loggedUserID 				= $mycms->getLoggedUserId();
	$isAbstractArr					= $_REQUEST['topic_isBreak_'.$formType];
	//echo "<pre>"; print_r($_REQUEST); echo "<pre>";
	$start = $startTime;	
	$counter		 		= 0;
	$participantSchudule = array();
	foreach($isAbstractArr as $Key=>$Val)
	{
		$topic_hour_val			= addslashes(trim($_REQUEST['topic_duration_hh_'.$formType][$Key]));
		$topic_min_val			= addslashes(trim($_REQUEST['topic_duration_min_'.$formType][$Key]));
		
		$topic_hour				= $topic_hour_val;
		$topic_min				= $topic_min_val;
		
		$topic_isAbstract_val	= addslashes(trim($_REQUEST['topic_isAbstract_'.$formType][$Key]));
		$topic_isBreak_val		= addslashes(trim($_REQUEST['topic_isBreak_'.$formType][$Key]));
		$topic_isHighlight_val	= addslashes(trim($_REQUEST['topic_isHighlight_'.$formType][$Key]));
		$topic_title_val		= addslashes(trim($_REQUEST['topic_title_'.$formType][$Key]));
		$topic_contant_val		= addslashes(trim($_REQUEST['topic_contant_'.$formType][$Key]));
		
		$topic_speaker_val		= addslashes(trim($_REQUEST['topic_speaker_'.$formType][$Key]));
		$no_of_speaker			= addslashes(trim($_REQUEST['no_of_speaker_'.$formType][$Key]));
		$topic_time_val			= addslashes(trim($_REQUEST['topic_timeslot'][$Key]));
		//echo "<br>---------><br>".$topic_hour."---->".$topic_min."<-------->".$hallId."<-------->".$sessionId."<-------->".$themeId."<-------->".$formType."<--------><br>";
		
		if(trim($topic_hour) !=""
		   && trim($topic_min) !=""
		   && trim($hallId) !=""  
		   && trim($sessionId) !=""  
		   && trim($themeId) !="")
		{
			
			$topic_time_duration = $topic_hour.":".$topic_min;
			
			$timeArr 	= explode("-",$topic_time_val);
			$start		= $timeArr[0];
			$end		= $timeArr[1];
			//print_r ($timeArr);
			$sqlInsertTopic	 = array();
			$sqlInsertTopic['QUERY'] = "INSERT INTO "._DB_PROGRAM_SCHEDULE_TOPIC_." 
										   SET `schedule_session_id` = '".$sessionId."', 
											   `schedule_theme_id` = '".$themeId."', 
											   `schedule_hall_id` = '".$hallId."',
											   `topic_title` = '".$topic_title_val."',
											   `topic_content` = '".$topic_contant_val."', 
											   `topic_time_duration` = '".$topic_time_duration."', 
											   `topic_no_of_speaker` = '0', 
											   `topic_time_slot` = '".$topic_time_val."', 
											   `topic_speaker` = '".$topic_speaker_val."', 
											   `isAbstract` = 'NO', 
											   `isBreak` = '".$topic_isBreak_val."', 
											   `isHighlight` = '".$topic_isHighlight_val."',
											   `topic_abstract_id` = '0', 
											   `topic_award_id` = '0', 
											   `status` = 'A', 
											   `operationMode` = 'ORAL_PRESENTATION',
											   `created_by` = '".$loggedUserID."',
											   `created_ip` = '".$_SERVER['REMOTE_ADDR']."',
											   `created_sessionId` = '".session_id()."',
											   `created_dateTime` = '".date('Y-m-d H:i:s')."'";
											   
			$topicId = $mycms->sql_insert($sqlInsertTopic);
			
			
			$speakersStr = addslashes(trim($_REQUEST['id_of_speakers'.$Key]));
			
			$speakersArr = explode(",",$speakersStr);
			
			if($speakersArr)
			{	
				$key="";$Value='';
				foreach($speakersArr as $Key=>$Value)
				{
					$counter++;
					if($Value !="")
					{
						$participantSchudule[$counter]['participant_id'] = $Value;
						$participantSchudule[$counter]['session_id'] = $sessionId;
						$participantSchudule[$counter]['theme_id'] = $themeId;
						$participantSchudule[$counter]['topic_id'] = $topicId;
						$participantSchudule[$counter]['participant_type'] = 4;
						$participantSchudule[$counter]['date_id'] = getDateTheme($themeId);
						$participantSchudule[$counter]['start_time'] = $start;
						$participantSchudule[$counter]['end_time'] = $end;
					}
				}
			}
		}
	}
	
	composeParticipantSchedule($participantSchudule);
}

function addMoreTopicTemplate($formType) // TEMPLATE OF TOPIC
{
	global $cfg, $mycms;
?>
	<table id="addMoreTopicTemplate" style="display:none;">
		<tr class="tlisting" forType="parent" operationMode="newTopicRow" sequenceBy="#COUNTER">
			<td align="center" valign="top" use='priority'>#COUNTER</td>
			<td align="center" valign="top">
				<span rel="timeChooser">
					<?php /*?><input type="text" name="topic_time_<?=$formType?>[#COUNTER]" id="topic_time_<?=$formType?>_#COUNTER" style="width:90%;"/><?php */?>
					
					<input type="text" name="topic_duration_hh_<?=$formType?>[#COUNTER]" id="topic_duration_hh_<?=$formType?>_#COUNTER" 
						   specific="hour" use="hour" onblur="timeCalculate();"
						   style="width:20px; text-align:right;" placeholder="HH"/> :
						   
					 <input type="text" name="topic_duration_min_<?=$formType?>[#COUNTER]" id="topic_duration_min_<?=$formType?>_#COUNTER" 
							specific="min" use="min" onblur="timeCalculate();"
							style="width:20px;" placeholder="MM"/>
				<span use="time"></span>
				 <input type="hidden" use="timeSlot" value="" id="topic_timeslot_#COUNTER" name="topic_timeslot[#COUNTER]" />
				</span>
				
			</td>
			<td align="left" valign="top">
				<input type="radio" name="topic_isBreak_<?=$formType?>[#COUNTER]" id="topic_isBreak_yes_<?=$formType?>_#COUNTER" value="YES" /> Yes 
				<br />
				<input type="radio" name="topic_isBreak_<?=$formType?>[#COUNTER]" id="topic_isBreak_no_<?=$formType?>_#COUNTER" checked="checked" value="NO" /> No
			</td>
			<td align="left"  valign="top">
				<input type="radio" name="topic_isHighlight_<?=$formType?>[#COUNTER]" id="topic_isHighlight_yes_<?=$formType?>_#COUNTER" value="YES" /> Yes 
				<br />
				<input type="radio" name="topic_isHighlight_<?=$formType?>[#COUNTER]" id="topic_isHighlight_no_<?=$formType?>_#COUNTER" checked="checked" value="NO" /> No
			</td>
			<td align="left"  valign="top">
				<textarea name="topic_title_<?=$formType?>[#COUNTER]" id="topic_title_<?=$formType?>_#COUNTER" style="width:97%;"></textarea>
			</td>
			<td align="left"  valign="top">
				<textarea name="topic_contant_<?=$formType?>[#COUNTER]" id="topic_contant_<?=$formType?>_#COUNTER" style="width:97%;" ></textarea>
			</td>
			<td align="left"  valign="top">
				<input type="hidden" name="id_of_speakers#COUNTER" id="id_of_speakers#COUNTER"  value=""  />
				<textarea name="name_of_speakers_<?=$formType?>[#COUNTER]" id="name_of_speakers#COUNTER" style="width:97%;" readonly="readonly" onclick="openParticipantDiv(4,#COUNTER);" onkeypress="keypressDitect(1,#COUNTER);"></textarea>
			</td>
			<td align="center"  valign="top">
				
			<?php /*?>	<a operationMode="newTopicRowRemover" sequenceBy="#COUNTER">
				<span alt="Remove" title="Remove" class="icon-trash-stroke" /></a><?php */?>
				
			</td>
		</tr>
	</table>
<?php
}

function getParticipantDetails($participantId) // GET PARCITIPANT DETAILS
{
	global $cfg, $mycms;
	$sqlListing	 = array();
	$sqlListing['QUERY']		 = "SELECT * FROM "._DB_SP_PARTICIPANT_DETAILS_." WHERE `status` = 'A' AND `id` = '".$participantId."'";
	$resultsListing	 = $mycms->sql_select($sqlListing); 
	$rowDetails		 = $resultsListing[0];
	return $rowDetails;
}

function getParticipantListName($sessionId,$themeId,$topicId,$type) // GET PARCITIPANT NAME OF PARTICULAR THEME OR TOPIC
{
	global $cfg, $mycms;
	$sql = array();
	$sql['QUERY'] = "	SELECT * 
						  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." 
						 WHERE session_id = '".$sessionId."' 
						   AND theme_id = '".$themeId."' 
						   AND topic_id = '".$topicId."' 
						   AND participant_type = '".$type."'";
	$result      = $mycms->sql_select($sql);
	$speaker = array();
	foreach($result as $i=>$value)
	{
		$participantDtls = getParticipantDetails($value['participant_id']);
		$speaker[] = $participantDtls['participant_full_name'];
	}
	return $speaker;
}

function getProgramDate($id) // GET PROGRAM DATE
{
	global $cfg, $mycms;
	$sqlListing	 = array();
	$sqlListing['QUERY']		 = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_DATE_." WHERE `status` = 'A' AND `id` = '".$id."'";
	$resultsListing	 = $mycms->sql_select($sqlListing); 
	$rowDetails		 = $resultsListing[0]['conf_date'];
	$date=date_create($rowDetails);
	return date_format($date,"d M, Y");
}

function getSessionTitle($id) // GET SESSION TITLE
{
	global $cfg, $mycms;
	$sqlListing	 = array();
	$sqlListing['QUERY']		 = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_SESSION_." WHERE `status` = 'A' AND `id` = '".$id."'";
	$resultsListing	 = $mycms->sql_select($sqlListing); 
	$rowDetails		 = $resultsListing[0]['session_title'];
	return $rowDetails;
}

function getThemeTitle($id) //GET THEME TITLE
{
	global $cfg, $mycms;
	$sqlListing	 = array();
	$sqlListing['QUERY']  = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_THEME_." WHERE `status` = 'A' AND `id` = '".$id."'";
	$resultsListing	 = $mycms->sql_select($sqlListing); 
	$rowDetails		 = $resultsListing[0]['theme_title'];
	return $rowDetails;
}

function getTopicTitle($id) //GET THEME TOPIC
{
	global $cfg, $mycms;
	$sqlListing	 = array();
	$sqlListing['QUERY']  = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." WHERE `status` = 'A' AND `id` = '".$id."'";
	$resultsListing	 = $mycms->sql_select($sqlListing); 
	$rowDetails		 = $resultsListing[0]['topic_title'];
	return $rowDetails;
}

function getSpeakerType($id) //GET SPEAKER TYPE
{
	global $cfg, $mycms;
	$sqlListing	 = array();
	$sqlListing['QUERY'] = "SELECT * FROM "._DB_SP_PARTICIPANT_CLASSIFICATION_." WHERE `status` = 'A' AND `id` = '".$id."'";
	$resultsListing	 = $mycms->sql_select($sqlListing); 
	$rowDetails		 = $resultsListing[0]['participant_classifications'];
	return $rowDetails;
}

function allMastersData()
{
?>
	<table width="100%">
		<tr>
			<td valign="top"><? venueDetails();?></td>
		</tr>
		<tr>
			<td valign="top"><? hallDetails();?></td>
		</tr>
		<tr>
			<td valign="top"><? sessionClassificationDetails();?></td>
		</tr>
		<tr>
			<td valign="top"><? participantClassificationDetails();?></td>
		</tr>
	</table>
<?
}

function sessionClassificationDetails() // LIST OF SESSION CLASSIFICATION MASTER
{
	global $cfg, $mycms;
?>
	<table width="100%" style="border:solid #dddddd 1px;">
		<td colspan="5" class="tcat">
			<span style="float:left">Session Classifications</span>
			<a href="additional_data.php?show=addSessionClasf"><span style="float:right">Add Session Classifications</span></a>
		</td>
		<tr class="theader">
			<td width="13%">Sl. No.</td>
			<td>Session Classifications Tittle</td>
			<td align="center" width="20%">Status</td>
			<td align="center" width="10%">Action</td>
		</tr>
	<?php
	    $sqlHallListing = array();
		$sqlHallListing['QUERY']		 = "SELECT * FROM "._DB_SP_SESSION_CLASSIFICATION_." WHERE `status` != 'D'";
		$resultHallListing   = $mycms->sql_select($sqlHallListing);
		$Counter			 = 0;
		if($resultHallListing)
		{
			foreach($resultHallListing as $keyHallListing=>$rowHallListing)
			{
				$Counter++;
			?>
				<tr>
					<td align="center"><?=$Counter?></td>
					<td><?=$rowHallListing['session_classifications']?></td>
					<td align="center"><a href="additional_data.process.php?act=<?=($rowHallListing['status']=='A'?'InactiveSessionClassification':'ActiveSessionClassification')?>&id=<?=$rowHallListing['id']?>" class="<?=($rowHallListing['status']=='A'?'ticket ticket-success':'ticket ticket-important')?>"><?=($rowHallListing['status']=='A'?'Active':'Inactive')?></a></td>
					<td align="center"><a href="additional_data.php?show=editSessionClasf&id=<?=$rowHallListing['id']?>">
								<span alt="Edit" title="Edit Record" class="icon-pen-alt2" /></a>
					</td>
				</tr>
			<?	
			}
		}
	?>
	</table>
<?
}

function participantClassificationDetails() // LIST OF PARTICIPANT CLASIFICATION MASTER
{
	global $cfg, $mycms;
?>
	<table width="100%" style="border:solid #dddddd 1px;">
		<td colspan="5" class="tcat">
			<span style="float:left">Participant Classifications</span>
			<a href="additional_data.php?show=addPartClasf"><span style="float:right">Add Participant Classifications</span></a>
		</td>
		<tr class="theader">
			<td width="13%">Sl. No.</td>
			<td>Classifications Tittle</td>
			<td align="center" width="20%">Status</td>
			<td align="center" width="10%">Action</td>
		</tr>
	<?php
	    $sqlHallListing = array();
		$sqlHallListing['QUERY']		 = "SELECT * FROM "._DB_SP_PARTICIPANT_CLASSIFICATION_." WHERE `status` != 'D'";
		$resultHallListing   = $mycms->sql_select($sqlHallListing);
		$Counter			 = 0;
		if($resultHallListing)
		{
			foreach($resultHallListing as $keyHallListing=>$rowHallListing)
			{
				$Counter++;
			?>
				<tr>
					<td align="center"><?=$Counter?></td>
					<td><?=$rowHallListing['participant_classifications']?></td>
					<td align="center"><a href="additional_data.process.php?act=<?=($rowHallListing['status']=='A'?'InactiveClassification':'ActiveClassification')?>&id=<?=$rowHallListing['id']?>" class="<?=($rowHallListing['status']=='A'?'ticket ticket-success':'ticket ticket-important')?>"><?=($rowHallListing['status']=='A'?'Active':'Inactive')?></a></td>
					<td align="center"><a href="additional_data.php?show=editClassification&id=<?=$rowHallListing['id']?>">
								<span alt="Edit" title="Edit Record" class="icon-pen-alt2" /></a>
					</td>
				</tr>
			<?	
			}
		}
	?>
	</table>
<?
}

function participantAvalableStatus($participantId,$sessionId)
{
	global $cfg, $mycms;
	$sqlSession = array();
	$sqlSession['QUERY'] = "SELECT * FROM "._DB_PROGRAM_SCHEDULE_THEME_." WHERE `status` = 'A' AND `id` = '".$sessionId."'";
	$resultsSession	 	 = $mycms->sql_select($sqlSession);
	$rowSession		 	 = $resultsSession[0];
	$timeSlot		 	 = explode("-",$rowSession['theme_time_slot']);
	$startTime		 	 = strtotime($timeSlot[0]);
	$endTime		 	 = strtotime($timeSlot[1]);
	$date_id		 	 = $rowSession['date_id'];
	
	$sqlListing 			= array();
	$sqlListing	['QUERY']	= "SELECT * FROM "._DB_SP_PARTICIPANT_DETAILS_." WHERE `status` = 'A' AND `id` = '".$participantId."'";
	$resultsListing	 		= $mycms->sql_select($sqlListing);
	$rowDetails		 		= $resultsListing[0];
	
	$sqlAvabality 			= array();
	$sqlAvabality['QUERY']	= "SELECT * FROM "._DB_SP_PARTICIPANT_AVAILABILITY_." WHERE `participant_id` = '".$participantId."' AND `available_date_id` = '".$date_id."'";
	$resultsAvabal	 		= $mycms->sql_select($sqlAvabality);
	$rowAvabality	 		= $resultsAvabal[0];
	
	$avalableStart	 		= strtotime($rowAvabality['available_start_time']);
	$avalableEnd	 		= strtotime($rowAvabality['available_end_time']);
	
	$sqlSet	= array();
	$sqlSet['QUERY'] = "SELECT * 
						  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." 
						 WHERE `participant_id` = '".$participantId."' 
						   AND `date_id` = '".$date_id."'
						   AND `start_time` >= '".$timeSlot[0]."'
						   AND `start_time` <= '".$timeSlot[1]."'
						   AND `end_time` >= '".$timeSlot[0]."'
						   AND `end_time` <= '".$timeSlot[1]."'";
	$resultsSet		 = $mycms->sql_select($sqlSet);
	$rowSet			 = $resultsSet[0];
	
	if($resultsSet)
	{
		$scheduleStatus = false;
	}
	else
	{
		$scheduleStatus = true;
	}
	
	
	if($avalableStart <= $startTime && $startTime <= $avalableEnd
		&& $avalableStart <= $endTime && $endTime <= $avalableEnd
		&& $scheduleStatus)
	{
		$msg = 'Available';
	}
	else
	{
		$msg = 'Not Available';
	}
	
	
	return $msg;
	
}

function allocatedQuery($theme_id=0,$date_id=0, $participant_type=NULL,$topic_id="")
{
	global $cfg, $mycms;
	
	$topicCondition = "";
	if($topic_id !="")
	{
		$topicCondition = "AND schedule.topic_id = '".$topic_id."'";
	}
	$sql = array();
	$sql['QUERY']		 	 = "SELECT schedule.*,
									   participant.participant_full_name 
								  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." schedule
							INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." participant
									ON schedule.participant_id = participant.id
								 WHERE schedule.theme_id = '".$theme_id."'  
								   AND schedule.date_id = '".$date_id."'
								   AND schedule.participant_type = '".$participant_type."' ".$topicCondition."";
						   
	$result			 = $mycms->sql_select($sql); 
	return $result;
}

function avalableParticapantQuery($topic_id,$minLimit,$maxLimit,$orderCondition,$themeId)
{
	global $cfg, $mycms;
	$sqlTopic = array();
 	 $sqlTopic['QUERY']	= " SELECT topic.*,
								   session.session_date_id AS dateId,
								   session.session_classifications_id
							  FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
						INNER JOIN "._DB_PROGRAM_SCHEDULE_TOPIC_." topic
								ON session.id = topic.schedule_session_id 
							 WHERE session.status = 'A' 
							   AND topic.status = 'A'
							   AND topic.id = '".$topic_id."'";
	$resultsTopic	 = $mycms->sql_select($sqlTopic);
	$rowTopic		 = $resultsTopic[0];

	$timeSlot		 = explode("-",$rowTopic['topic_time_slot']);
	$strating		 = $timeSlot[0];
	$ending			 = $timeSlot[1];
	$startTime		 = strtotime($timeSlot[0]);
	$endTime		 = strtotime($timeSlot[1]);
	$date_id		 = $rowTopic['dateId'];
	
	$participantTypeForSession    = array();
	
	$sql = array();
	$sql['QUERY']   			  = "SELECT *  FROM "._DB_SP_MAPING_SC_TO_PC_."  WHERE `session_classification_id` = '".$rowTopic['session_classifications_id']."'";
	$results		  = $mycms->sql_select($sql);
	if($results)
	{
		foreach($results as $key=>$row)
		{
			$participantTypeForSession[] = $row['participation_classification_id'];
		}
	}
	
	$avalableDelegateArray    = array();
	$notAvalableDelegateArray = array();
	$counter		 = 0;
	$sqlListing = array();
	$sqlListing['QUERY']	  = "   SELECT * 
									  FROM "._DB_SP_PARTICIPANT_DETAILS_." 
									 WHERE `status` = 'A'
									   AND `id` NOT IN ('".implode("','",$_SESSION['USED_PARTICIPANT_ID'])."')";
						 
	$resultsListing	 = $mycms->sql_select($sqlListing);
	
	if($resultsListing)
	{
		foreach($resultsListing as $key=>$rowParticipant)
		{
			$participantId   = $rowParticipant['id'];
			
			$sqlAvabality = array();
			$sqlAvabality['QUERY']	 = "SELECT * FROM "._DB_SP_PARTICIPANT_AVAILABILITY_." WHERE `participant_id` = '".$participantId."' AND `available_date_id` = '".$date_id."'";
			$resultsAvabal	 = $mycms->sql_select($sqlAvabality);
			$rowAvabality	 = $resultsAvabal[0];
			
			$sql = array();
			$sql['QUERY']			 = "SELECT * FROM "._DB_SP_MAPING_PC_TO_PARTICIPANT_." WHERE `participant_id` = '".$participantId."'";
			$results		 = $mycms->sql_select($sql);
			$participantStatus = false;
			if($results)
			{
				foreach($results as $key=>$row)
				{
					if(in_array($row['participation_classification_id'], $participantTypeForSession))
					{
						$participantStatus = true;
					}
				}
			}
			
			$avalableStart	 = strtotime($rowAvabality['available_start_time']);
			$avalableEnd	 = strtotime($rowAvabality['available_end_time']);
						
			$sqlSet	= array();
			$sqlSet['QUERY'] = "SELECT * 
								  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." 
								 WHERE `participant_id` = '".$participantId."' 
								   AND `date_id` = '".$date_id."'
								   AND `start_time` >= '".$strating."'
								   AND `start_time` <= '".$ending."'
								   AND `end_time` >= '".$strating."'
								   AND `end_time` <= '".$ending."'";
								   
				
								   
			$resultsSet		 = $mycms->sql_select($sqlSet);
			
			$sqlSet2 = array();
			$sqlSet2['QUERY'] = "   SELECT * 
									  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." 
									 WHERE `participant_id` = '".$participantId."' 
									   AND `date_id` = '".$date_id."'
									   AND `theme_id` = '".$themeId."'";
			$resultsSet2	  = $mycms->sql_select($sqlSet2);
			
			if($resultsSet || $resultsSet2)
			{
				$scheduleStatus = false;
			}
			else
			{
				$scheduleStatus = true;
			}
			
			// && $key >= $minLimit && $key <= $maxLimit
			if($avalableStart <= $startTime && $startTime <= $avalableEnd
				&& $avalableStart <= $endTime && $endTime <= $avalableEnd
				&& $scheduleStatus && $participantStatus && $counter < $maxLimit)
			{
				//$msg = 'Avalable';
				
				$_SESSION['USED_PARTICIPANT_ID'][]  = $participantId;
				
				$avalableDelegateArray[$topic_id][$counter]['ID'] 	= $participantId;
				$avalableDelegateArray[$topic_id][$counter]['NAME'] = $rowParticipant['participant_full_name']; 
				$avalableDelegateArray[$topic_id][$counter]['STATUS'] = 'Available';
				
				$counter		 = $counter + 1;
			}
			else
			{
				$msg = 'Not Available';
			}
		}		
	}

	return $avalableDelegateArray;
}
 
function getTimeDiff($dtime,$atime) // GET TIME DIFFARANCE BETWEEN TWO TIME
{
	$nextDay=$dtime>$atime?1:0;
	$dep=explode(':',$dtime);
	$arr=explode(':',$atime);
	$diff=abs(mktime($dep[0],$dep[1],0,date('n'),date('j'),date('y'))-mktime($arr[0],$arr[1],0,date('n'),date('j')+$nextDay,date('y')));
	$hours=floor($diff/(60*60));
	$mins=floor(($diff-($hours*60*60))/(60));
	$secs=floor(($diff-(($hours*60*60)+($mins*60))));
	if(strlen($hours)<2){$hours="0".$hours;}
	if(strlen($mins)<2){$mins="0".$mins;}
	if(strlen($secs)<2){$secs="0".$secs;}
	//return $hours.':'.$mins.':'.$secs;
	return timeReComposer($hours.':'.$mins);
}

function timeReComposer($time,$ampm=false)
{
	$timeExpld				= explode(":",$time);		
	$returnTime				= number_pad(intval(trim($timeExpld[0])),2).":".number_pad(intval(trim($timeExpld[1])),2);
	if($ampm)
	{
		if(intval(trim($timeExpld[0]))>=12)
		{
			if(intval(trim($timeExpld[0]))>12)
			{
				$t = intval(trim($timeExpld[0]))-12;
				$returnTime				= number_pad($t,2).":".number_pad(intval(trim($timeExpld[1])),2)." PM";
			}
			else
			{
				$returnTime				= number_pad(intval(trim($timeExpld[0])),2).":".number_pad(intval(trim($timeExpld[1])),2)." PM";
			}
		}
		else
		{
			$returnTime				= number_pad(intval(trim($timeExpld[0])),2).":".number_pad(intval(trim($timeExpld[1])),2)." AM";
		}
	}
	return $returnTime;
}
function insertTopic($mycms, $cfg,$index)
	{
		global $cfg, $mycms;

		$loggedUserID 			= $mycms->getLoggedUserId();
            // echo "<pre>";
            // print_r($_REQUEST);
            // die();
        foreach($_REQUEST['topic_id'][$index] as $topicKey =>$row){

            $schedule_id			= addslashes(trim($_REQUEST['schedule_session_id']));
            $hall_id				= addslashes(trim($_REQUEST['schedule_allocated_hall_id']));
            $date_id				= addslashes(trim($_REQUEST['schedule_session_date_id']));
            $venue_id				= addslashes(trim($_REQUEST['schedule_session_venue_id']));
            $theme_id	   			= addslashes(trim($_REQUEST['schedule_theme_id']));

            $topic_min_val			= addslashes(trim($_REQUEST['topic_duration_min'][$index][$topicKey]));

            $color					= addslashes(trim($_REQUEST['topicColor'][$index][$topicKey]));

            $topic_time_duration 	= $topic_min_val;

            $is_duration_permanent	= addslashes(trim($_REQUEST['is_duration_permanent'][$index][$topicKey]));
            if ($is_duration_permanent == 'Y') {
                $is_duration_permanent = 'Y';
            } else {
                $is_duration_permanent = 'N';
            }

            $sequence				= addslashes(trim($_REQUEST['sequence'][$index][$topicKey]));

            $reference_tag			= addslashes(trim($_REQUEST['reference_tag'][$index][$topicKey]));

            if ($_REQUEST['importFromAbstract'][$index][$topicKey] === 'Y') {
                $abstract_id			= addslashes(trim($_REQUEST['abstract_id'][$index][$topicKey]));
                $abstract_title			= addslashes(trim($_REQUEST['abstract_title'][$index][$topicKey]));
                $abstract_topic			= addslashes(trim($_REQUEST['abstract_topic'][$index][$topicKey]));
                $presenter				= addslashes(trim($_REQUEST['presenter'][$index][$topicKey]));
                $regId					= addslashes(trim($_REQUEST['regId'][$index][$topicKey]));

                $sqlAbstract = array();
                $sqlAbstract['QUERY'] = "   SELECT abstarct.*, topic.id AS topic_id,
                                                applicant.user_full_name, applicant.user_email_id, applicant.user_mobile_no,
                                                applicant.id AS userId, applicant.user_registration_id, applicant.user_unique_sequence,
                                                particpnt.id AS participantId, particpnt.participant_full_name AS participant_name
                                            FROM " . _DB_ABSTRACT_REQUEST_ . " abstarct
                                LEFT OUTER JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic 
                                                ON abstarct.abstract_topic_id = abstractTopic.id 						  
                                LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " topic
                                                ON topic.topic_abstract_id = abstarct.id AND topic.status = 'A'
                                LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " applicant 
                                                ON applicant.id = abstarct.applicant_id
                                LEFT OUTER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " particpnt 
                                                ON ( applicant.id = particpnt.participant_delegate_id OR particpnt.participant_email_id = applicant.user_email_id )
                                            WHERE abstarct.id = '" . $abstract_id . "'";
                $resAbstract = $mycms->sql_select($sqlAbstract);
                $rowAbstract = $resAbstract[0];
                if ($rowAbstract['topic_id'] == '') {
                    $sqlInsertTopic	 = array();
                    $sqlInsertTopic['QUERY']		= "INSERT INTO " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                                                            SET `schedule_session_id` = '" . $schedule_id . "', 
                                                                `schedule_theme_id` = '" . $theme_id . "', 
                                                                `schedule_hall_id` = '" . $hall_id . "',
                                                                `topic_title` = '" . ($abstract_title) . "', 
                                                                `topic_content` = '" . ($abstract_topic) . "', 
                                                                `isAbstract` = 'YES', 
                                                                `topic_abstract_id` = '" . $abstract_id . "',
                                                                `topic_time_duration` = '" . $topic_time_duration . "', 
                                                                `is_duration_permanent` = '" . $is_duration_permanent . "',
                                                                `sequence` = '" . $sequence . "',
                                                                `topic_color` = '" . $color . "',
                                                                                                            
                                                                `status` = 'A', 
                                                                `operationMode` = 'ORAL_PRESENTATION',
                                                                `created_by` = '" . $loggedUserID . "',
                                                                `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                                                                `created_sessionId` = '" . session_id() . "',
                                                                `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
                    $topic_id = $mycms->sql_insert($sqlInsertTopic);
                } else {
                    $topic_id			= $rowAbstract['topic_id'];

                    $sqlInsertTopic	= array();
                    $sqlInsertTopic['QUERY']		= "     UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                                                            SET `schedule_session_id` = '" . $schedule_id . "', 
                                                                `schedule_theme_id` = '" . $theme_id . "', 
                                                                `schedule_hall_id` = '" . $hall_id . "',
                                                                `topic_title` = '" . ($abstract_title) . "', 
                                                                `topic_content` = '" . strtoupper($abstract_topic) . "', 
                                                                `isAbstract` = 'YES', 
                                                                `topic_abstract_id` = '" . $abstract_id . "',
                                                                `topic_time_duration` = '" . $topic_time_duration . "', 
                                                                `is_duration_permanent` = '" . $is_duration_permanent . "',
                                                                `sequence` = '" . $sequence . "',	
                                                                `topic_color` = '" . $color . "',														   
                                                                `status` = 'A', 
                                                                `operationMode` = 'ORAL_PRESENTATION',
                                                                `created_by` = '" . $loggedUserID . "',
                                                                `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                                                                `created_sessionId` = '" . session_id() . "',
                                                                `created_dateTime` = '" . date('Y-m-d H:i:s') . "'
                                                            WHERE `id` = '" . $topic_id . "'";
                                                            
                    $mycms->sql_update($sqlInsertTopic);
                }

                if ($rowAbstract['participantId'] == '') {
                    $sqlParticipant = array();
                    $sqlParticipant['QUERY']		= "INSERT INTO " . _DB_SP_PARTICIPANT_DETAILS_ . " 
                                                            SET `participant_email_id`				= '" . $rowAbstract['user_email_id'] . "', 
                                                                `participant_full_name`				= '" . $rowAbstract['user_full_name'] . "', 
                                                                `participant_mobile_no`				= '" . $rowAbstract['user_mobile_no'] . "', 
                                                                `participant_delegate_id`			= '" . $rowAbstract['userId'] . "', 
                                                                `participant_unique_sequence`		= '" . $rowAbstract['user_unique_sequence'] . "', 
                                                                `participant_registration_id`		= '" . $rowAbstract['user_registration_id'] . "', 
                                                                `participation_type`					= 'ABSTRACT_PRESENTER', 
                                                                `status` 							= 'A',
                                                                `created_ip` 						= '" . $_SERVER['REMOTE_ADDR'] . "', 
                                                                `created_sessionId` 					= '" . session_id() . "',
                                                                `created_browser` 					= '" . $_SERVER['HTTP_USER_AGENT'] . "',
                                                                `created_dateTime` 					= '" . date('Y-m-d H:i:s') . "'";
                    $participantId[0] 	 = $mycms->sql_insert($sqlParticipant);
                    $participantName[0]  = $rowAbstract['user_full_name'];
                    $participatingAs[0]  = trim($rowAbstract['tags'] . ' ' . $rowAbstract['abstract_parent_type'] . ' ' . 'PRESENTER');
                    $isFaculty[0] 		 = 'N';
                } else {
                    $participantId[0] 	 = $rowAbstract['participantId'];
                    $participantName[0]  = $rowAbstract['participant_name'];
                    $participatingAs[0]  = trim($rowAbstract['tags'] . ' ' . $rowAbstract['abstract_parent_type'] . ' ' . 'PRESENTER');
                    $isFaculty[0] 		 = 'N';
                }
            } else {
                $topic_id				= addslashes(trim($_REQUEST['topic_id'][$index][$topicKey]));
                $topic_title			= addslashes(trim($_REQUEST['topic_title'][$index][$topicKey]));
                $topic_contant			= addslashes(trim($_REQUEST['topic_contant'][$index][$topicKey]));
                $sqlFetchTopic = array();
				$sqlFetchTopic['QUERY']           = "   SELECT `id` 
														FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." 
														WHERE status = 'A'
														AND id = '".$topic_id."'";		
																								 
				$resultTopic   			 = $mycms->sql_select($sqlFetchTopic);	
                $rowTopic  = $resultTopic[0];
                if ($rowTopic['id'] != '' && $topic_id!='') {
                    $sqlInsertTopic = array();
                    $sqlInsertTopic['QUERY']		= " UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                                                        SET `schedule_session_id` = '" . $schedule_id . "', 
                                                            `schedule_theme_id` = '" . $theme_id . "', 
                                                            `schedule_hall_id` = '" . $hall_id . "',
                                                            `topic_title` = '" . $topic_title . "', 
                                                            `topic_content` = '" . $topic_contant . "', 
                                                            `topic_time_duration` = '" . $topic_time_duration . "', 
                                                            `is_duration_permanent` = '" . $is_duration_permanent . "',
                                                            `sequence` = '" . $sequence . "',		
                                                            `topic_color` = '" . $color . "',				
                                                            `reference_tag` = '" . $reference_tag . "',														   
                                                            `status` = 'A', 
                                                            `operationMode` = 'ORAL_PRESENTATION',
                                                            `created_by` = '" . $loggedUserID . "',
                                                            `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                                                            `created_sessionId` = '" . session_id() . "',
                                                            `created_dateTime` = '" . date('Y-m-d H:i:s') . "'
                                                        WHERE `id` = '" . $topic_id . "'";
                    $mycms->sql_update($sqlInsertTopic);
                } else {
                    $sqlInsertTopic = array();
                    $sqlInsertTopic['QUERY']		= " INSERT INTO " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                                                        SET `schedule_session_id` = '" . $schedule_id . "', 
                                                            `schedule_theme_id` = '" . $theme_id . "', 
                                                            `schedule_hall_id` = '" . $hall_id . "',
                                                            `topic_title` = '" . ($topic_title) . "', 
                                                            `topic_content` = '" . $topic_contant . "', 
                                                            `topic_time_duration` = '" . $topic_time_duration . "', 
                                                            `is_duration_permanent` = '" . $is_duration_permanent . "',
                                                            `sequence` = '" . $sequence . "',
                                                            `topic_color` = '" . $color . "',				
                                                            `reference_tag` = '" . $reference_tag . "',																													   
                                                            `status` = 'A', 
                                                            `operationMode` = 'ORAL_PRESENTATION',
                                                            `created_by` = '" . $loggedUserID . "',
                                                            `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                                                            `created_sessionId` = '" . session_id() . "',
                                                            `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
                    $topic_id = $mycms->sql_insert($sqlInsertTopic);
                }

                $participantId 	 = $_REQUEST['topic_theme_participant_id'][$index][$topicKey];
                $participantName = $_REQUEST['topic_participant_name'][$index][$topicKey];
                $participatingAs = $_REQUEST['topic_theme_participant_as'][$index][$topicKey];
                $isFaculty 		 = $_REQUEST['topic_isFaculty'][$index][$topicKey];
            }

            adjustTopicDownSequence($topic_id);
            adjustTopicTime($theme_id);
            setTopicSchedulesTimes($theme_id);

            $rowUpdatedTopic = getTopicDetails($topic_id);

            $participantSchudule 	 = array();
            $counter 				 = 0;
            if (is_array($participantId) && sizeof($participantId) > 0) {
                foreach ($participantId as $Key => $Value) {
                    if ($Value != "") {
                        $participantSchudule[$counter]['participant_id']   = $Value;
                        $participantSchudule[$counter]['session_id'] 	   = $rowUpdatedTopic['schedule_session_id'];
                        $participantSchudule[$counter]['theme_id'] 		   = $rowUpdatedTopic['schedule_theme_id'];
                        $participantSchudule[$counter]['topic_id'] 		   = $topic_id;
                        $participantSchudule[$counter]['participant_type'] = $participatingAs[$Key];
                        $participantSchudule[$counter]['date_id'] 		   = $date_id;
                        $participantSchudule[$counter]['start_time'] 	   = $rowUpdatedTopic['topic_time_start'];
                        $participantSchudule[$counter]['end_time'] 		   = $rowUpdatedTopic['topic_time_end'];
                        $participantSchudule[$counter]['isFaculty'] 	   = $isFaculty[$Key];
                        $counter++;
                    }
                }
            }

            composeParticipantSchedule($participantSchudule);
        }
	}
    
	function adjustTopicUpSequence($topic_id)
	{
		global $cfg, $mycms;

		$rowTopic 		= getTopicDetails($topic_id);
		$sqlSchdTopic = array();
		$sqlSchdTopic['QUERY']	= "UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
									  SET sequence = sequence-1
									WHERE status = 'A'
									  AND schedule_theme_id = '" . $rowTopic['schedule_theme_id'] . "'
									  AND id != '" . $topic_id . "'
									  AND sequence >= '" . $rowTopic['sequence'] . "'";
		$mycms->sql_update($sqlSchdTopic);
	}

	function adjustTopicDownSequence($topic_id)
	{
		global $cfg, $mycms;

		$rowTopic 		= getTopicDetails($topic_id);
		$sqlSchdTopic = array();
		$sqlSchdTopic['QUERY']	= "UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
									  SET sequence = sequence+1
									WHERE status = 'A'
									  AND schedule_theme_id = '" . $rowTopic['schedule_theme_id'] . "'
									  AND id != '" . $topic_id . "'
									  AND sequence >= '" . $rowTopic['sequence'] . "'";
		$mycms->sql_update($sqlSchdTopic);
	}

	function adjustTopicTime($themeId)
	{
		global $cfg, $mycms;

		$rowTheme		= getThemeDetails($themeId);
		$rowSession		= getSessionDetails($rowTheme['schedule_id']);

		$themeDurtion   = intval($rowTheme['theme_time_end_mins']) - intval($rowTheme['theme_time_start_mins']);

		$sqlPermDur	 = array();
		$sqlPermDur['QUERY']		= "SELECT SUM(CAST(topic_time_duration AS SIGNED)) AS permanent_duration_mins
										 FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
										WHERE status = 'A'
										  AND schedule_theme_id = '" . $themeId . "'
										  AND is_duration_permanent = 'Y'";
		$resultPermDur 	= $mycms->sql_select($sqlPermDur);
		$permDuraion	= intval($resultPermDur[0]['permanent_duration_mins']);

		$sqlNonPermItm = array();
		$sqlNonPermItm['QUERY']	= "SELECT COUNT(*) AS nonPermanentCount
									 FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
									WHERE status = 'A'
									  AND schedule_theme_id = '" . $themeId . "'
									  AND is_duration_permanent = 'N'";
		$resNonPermItm 	= $mycms->sql_select($sqlNonPermItm);
		$NonPermItems	= intval($resNonPermItm[0]['nonPermanentCount']);

		$avlblSecsPostPermTopics = ($themeDurtion * 60) - ($permDuraion * 60);
		$nonPermTimeDurDistSec 	 = floor($avlblSecsPostPermTopics / $NonPermItems);
		$nonPermTimeDurDistMin 	 = floor($nonPermTimeDurDistSec / 60);

		$totalCalculatedDuration = $permDuraion + ($nonPermTimeDurDistMin * $NonPermItems);

		if (($nonPermTimeDurDistMin < 1 && $NonPermItems > 0)
			|| $totalCalculatedDuration > $themeDurtion
		) {
			$nonPermTimeDurDistMin = 0;
			$topic_has_problem_with_time = 'Y';
			$sqlFetchTheme = array();
			$sqlFetchTheme['QUERY']  = "   UPDATE " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
											  SET has_problem_with_time = 'Y'
											WHERE id = '" . $themeId . "'";
			$mycms->sql_update($sqlFetchTheme);
		} else {
			$topic_has_problem_with_time = 'N';
			$sqlFetchTheme = array();
			$sqlFetchTheme['QUERY']  = "   UPDATE " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
											  SET has_problem_with_time = 'N'
											WHERE id = '" . $themeId . "'";
			$mycms->sql_update($sqlFetchTheme);
		}

		$nonPerm_topic_time_duration = $nonPermTimeDurDistMin;
		$sqlSchdNonPermItm = array();
		$sqlSchdNonPermItm['QUERY']	= "UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
										  SET topic_time_duration = '" . $nonPerm_topic_time_duration . "',
											  has_problem_with_time = '" . $topic_has_problem_with_time . "'
										WHERE status = 'A'
										  AND schedule_theme_id = '" . $themeId . "'
										  AND is_duration_permanent = 'N'";
		$mycms->sql_update($sqlSchdNonPermItm);
	}

	function setTopicSchedulesTimes($themeId)
	{
		global $cfg, $mycms;

		$rowTheme				= getThemeDetails($themeId);
		$theme_time_start_mins  = intval($rowTheme['theme_time_start_mins']);
		$theme_time_end_mins   	= intval($rowTheme['theme_time_start_mins']);
		$topic_start_mins		= $theme_time_start_mins;

		$sqlFetchTopic = array();
		$sqlFetchTopic['QUERY'] = "   SELECT *, 
											 topic_time_duration AS topic_time_duration_mins,
											 (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
											 (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60) AS topic_time_end_mins
										FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
									   WHERE schedule_theme_id = '" . $themeId . "'
									ORDER BY sequence ASC";
		$resultTopic 	= $mycms->sql_select($sqlFetchTopic);

		foreach ($resultTopic as $k => $rowTopic) {
			$topic_end_mins	= $topic_start_mins + intval($rowTopic['topic_time_duration']);

			$topic_time_start 	= timeReComposer(floor($topic_start_mins / 60) . ':' . ($topic_start_mins % 60));
			$topic_time_end   	= timeReComposer(floor($topic_end_mins / 60) . ':' . ($topic_end_mins % 60));
			$topic_time_slot  	= $topic_time_start . ' - ' . $topic_time_end;
			$sqlSchdNonPermItm 	= array();
			$sqlSchdNonPermItm['QUERY']	= "UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
									  SET topic_time_slot = '" . $topic_time_slot . "',
										  topic_time_start = '" . $topic_time_start . "',
										  topic_time_end = '" . $topic_time_end . "'
									WHERE status = 'A'
									  AND id = '" . $rowTopic['id'] . "'";
			$mycms->sql_update($sqlSchdNonPermItm);

			$topic_start_mins = $topic_end_mins;
		}
	}
	


function removeSession($mycms, $cfg)
{
	global $loggedUserID;

	$sessionId	= addslashes(trim($_REQUEST['sessionId']));
	$rowSession	= getSessionDetails($sessionId);

	$sqlFetchTheme = array();
	$sqlFetchTheme['QUERY'] = " SELECT *
									  FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
									 WHERE schedule_id = '" . $sessionId . "'";
	$resultTheme   = $mycms->sql_select($sqlFetchTheme);

	foreach ($resultTheme as $ky => $rowTh) {
		$themeId = $rowTh['id'];

		/// release participants
		$sqlDelete = array();
		$sqlDelete['QUERY'] = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . "
										 WHERE `theme_id` = '" . $themeId . "'
										   AND ( `topic_id` IS NULL OR `topic_id` = '')";
		$mycms->sql_delete($sqlDelete);

		$sqlFetchTopic = array();
		$sqlFetchTopic['QUERY'] = " SELECT *
										  FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
										 WHERE schedule_session_id = '" . $rowTheme['schedule_id'] . "'
										   AND schedule_theme_id = '" . $themeId . "'";
		$resultTopic   			 = $mycms->sql_select($sqlFetchTopic);
		foreach ($resultTopic as $keyTopic => $rowTopic) {
			// release topic participants
			$rowExistingParticipant	= getTopicParticipants($rowTopic['id']);
			foreach ($rowExistingParticipant['TOPIC'] as $kth => $topicParticipant) {
				$sqlParticipantDelete = array();
				$sqlParticipantDelete['QUERY']   = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
														 WHERE `participant_id` = '" . $topicParticipant['participant_id'] . "'
														   AND `session_id` = '" . $topicParticipant['session_id'] . "'
														   AND `theme_id` = '" . $topicParticipant['theme_id'] . "'
														   AND `topic_id` = '" . $topicParticipant['topic_id'] . "'
														   AND `participant_type` = '" . $topicParticipant['participant_type'] . "'
														   AND `date_id` = '" . $topicParticipant['date_id'] . "'
														   AND `start_time` = '" . $topicParticipant['start_time'] . "'
														   AND `end_time` = '" . $topicParticipant['end_time'] . "'";
				$mycms->sql_delete($sqlParticipantDelete);
			}

			// release topics
			$sqlInsertTopic = array();
			$sqlInsertTopic['QUERY']		= "     UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
														   SET `schedule_session_id` = NULL,
															   `schedule_theme_id` = NULL,
															   `schedule_hall_id` = NULL,
															   `schedule_hall_id` = NULL,
															   `topic_time_duration` = NULL, 
															   `is_duration_permanent` = 'N',
															   `sequence` = NULL,	
															   `topic_time_slot` = NULL,			
															   `has_problem_with_time` = 'N',		
															   `topic_time_start` = NULL,
															   `topic_time_end` = NULL,					   
															   `modified_by` = '" . $loggedUserID . "',
															   `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
															   `modified_sessionId` = '" . session_id() . "',
															   `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
														 WHERE `id` = '" . $rowTopic['id'] . "'";
			$mycms->sql_update($sqlInsertTopic);
		}

		//hard delete theme
		$sqlDelTheme = array();
		$sqlDelTheme['QUERY'] = "DELETE FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
										   WHERE id = '" . $themeId . "'";
		$mycms->sql_delete($sqlDelTheme);
	}

	$sqlDeleteSession = array();
	$sqlDeleteSession['QUERY']        = "DELETE FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . "
									 		  WHERE id = '" . $sessionId . "'";
	$mycms->sql_delete($sqlDeleteSession);

   $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Session Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="program_schedule.php";</script>';
}


	function removeTheme($mycms, $cfg)
	{
		global $loggedUserID;

		$themeId  		= $_REQUEST['themeId'];
		$rowTheme 		= getThemeDetails($themeId);
		$ExistingDateId = $rowTheme['date_id'];

		/// release participants
		$sqlDelete = array();
		$sqlDelete['QUERY']			 = " DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . "
										  WHERE `theme_id` = '" . $themeId . "'
											AND ( `topic_id` IS NULL OR `topic_id` = '')";
		$mycms->sql_delete($sqlDelete);
		$sqlFetchTopic = array();
		$sqlFetchTopic['QUERY'] = " SELECT *
									  FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
									 WHERE schedule_session_id = '" . $rowTheme['schedule_id'] . "'
									   AND schedule_theme_id = '" . $themeId . "'";
		$resultTopic   			 = $mycms->sql_select($sqlFetchTopic);
		foreach ($resultTopic as $keyTopic => $rowTopic) {
			// release topic participants
			$rowExistingParticipant	= getTopicParticipants($rowTopic['id']);
			foreach ($rowExistingParticipant['TOPIC'] as $kth => $topicParticipant) {
				$sqlParticipantDelete = array();
				$sqlParticipantDelete['QUERY']   = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
													 WHERE `participant_id` = '" . $topicParticipant['participant_id'] . "'
													   AND `session_id` = '" . $topicParticipant['session_id'] . "'
													   AND `theme_id` = '" . $topicParticipant['theme_id'] . "'
													   AND `topic_id` = '" . $topicParticipant['topic_id'] . "'
													   AND `participant_type` = '" . $topicParticipant['participant_type'] . "'
													   AND `date_id` = '" . $topicParticipant['date_id'] . "'
													   AND `start_time` = '" . $topicParticipant['start_time'] . "'
													   AND `end_time` = '" . $topicParticipant['end_time'] . "'";
				$mycms->sql_delete($sqlParticipantDelete);
			}

			// release topics
			$sqlInsertTopic	 = array();
			$sqlInsertTopic['QUERY']		= " UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
												   SET `schedule_session_id` = NULL,
													   `schedule_theme_id` = NULL,
													   `schedule_hall_id` = NULL,
													   `schedule_hall_id` = NULL,
													   `topic_time_duration` = NULL, 
													   `is_duration_permanent` = 'N',
													   `sequence` = NULL,	
													   `topic_time_slot` = NULL,			
													   `has_problem_with_time` = 'N',		
													   `topic_time_start` = NULL,
													   `topic_time_end` = NULL,					   
													   `modified_by` = '" . $loggedUserID . "',
													   `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
													   `modified_sessionId` = '" . session_id() . "',
													   `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
												 WHERE `id` = '" . $rowTopic['id'] . "'";
			$mycms->sql_update($sqlInsertTopic);
		}

		//hard delete theme
		$sqlDelTheme = array();
		$sqlDelTheme['QUERY']           = "DELETE FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
									 		WHERE id = '" . $themeId . "'";
		$mycms->sql_delete($sqlDelTheme);

		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Group Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="program_schedule.php";</script>';
		exit();
	}

	function getTopicRemoverWindow($mycms, $cfg)
	{
		global  $buttonSpPrgAccessArray, $buttonAccessArray, $loggedUserID;

		$topicId = $_REQUEST['topicId'];
		$sqlFetchTopic = array();
		$sqlFetchTopic['QUERY'] = "  SELECT tpk.*,
										   (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
										   (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins,
										   thm.theme_title, thm.theme_time_start, thm.theme_time_end,
										   session.session_title, session.session_start_time, session.session_end_time, session.session_date_id,
										   hall.hall_title,session.session_hall_id,
										   venue.id AS venue_id,
										   venue.program_venue,
										   scheduleDate.conf_date AS session_date  
										   
									  FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " tpk
								
								INNER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " thm
										ON thm.id = tpk.schedule_theme_id
									  
								INNER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " session
										ON session.id = thm.schedule_id
										   
								INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " scheduleDate 
										ON session.session_date_id = scheduleDate.id
										
								INNER JOIN " . _DB_MASTER_HALL_ . " hall 
										ON session.session_hall_id = hall.id
										
								INNER JOIN " . _DB_PROGRAM_SCHEDULE_VENUE_ . " venue 
										ON hall.hall_venue = venue.id
									 WHERE tpk.id = '" . $topicId . "'";
		$resultTopic   = $mycms->sql_select($sqlFetchTopic);
		$rowTopic 	   = $resultTopic[0];
		?>
			<div style="overflow-y: scroll;max-height: 650px;">
				<form name="frmComposeProgramSchedule" id="topicRemoverForm" action="full_program_schedule.process.php" method="post">
					<input type="hidden" name="act" value="removeTopic" />
					<input type="hidden" name="topic_id" id="topic_id" value="<?= $topicId ?>" />
					<table width="100%" class="tborder">
						<tr>
							<td colspan="2" class="tcat">
								Remove Topic
								<a onClick="$('#defaultOverLay').fadeOut();$('#TopicRemover').fadeOut();"><span style="float:right"><span alt="Edit Topic" title="Edit Topic" class="icon12b-x" /></span></a>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="margin:0px; padding:0px;" use="topicAddDetailContainer">
								<table width="100%">
									<tr>
										<td colspan="4" class="thighlight" align="left">Session Details</td>
									</tr>
									<tr>
										<td width="20%" align="left">
											Session Title
										</td>
										<td width="30%" align="left" dataDisp="valSessionTitle"><?= $rowTopic['session_title'] ?></td>
										<td align="left">
											Session Date
										</td>
										<td align="left" dataDisp="valSessionDate"><?= $rowTopic['session_date'] ?></td>
									</tr>
									<tr>
										<td align="left">
											Hall
										</td>
										<td align="left" dataDisp="valHall"><?= $rowTopic['hall_title'] ?></td>
										<td align="left">Time Slot</td>
										<td align="left" dataDisp="valTime"><?= $rowTopic['session_start_time'] . '-' . $rowTopic['session_end_time'] ?></td>
									</tr>
								</table>
								<table width="100%">
									<tr>
										<td colspan="4" class="thighlight" align="left">Group Details</td>
									</tr>
									<tr>
										<td align="left">
											Group Title
										</td>
										<td align="left" dataDisp="valThemeTitle">
											<?= $rowTopic['theme_title'] ?>
										</td>
										<td align="left" valign="top">
											Time Slot
										</td>
										<td align="left" valign="top" dataDisp="valThemeTimeSlot">
											<?= $rowTopic['theme_time_start'] . '-' . $rowTopic['theme_time_end'] ?>
										</td>
									</tr>
								</table>
								<table width="100%">
									<tr>
										<td colspan="4" class="thighlight" align="left">Topic Details</td>
									</tr>
									<tr>
										<td align="left" valign="top">
											Topic
										</td>
										<td align="left" valign="top">
											<?= $rowTopic['topic_title'] ?>
										</td>
										<td align="left" valign="top">Content</td>
										<td align="left" valign="top">
											<?= $rowTopic['topic_content'] ?>
										</td>
									</tr>
									<tr>
										<td align="left" valign="top">
											Duration
										</td>
										<td align="left" valign="top">
											<?= $rowTopic['topic_time_duration'] ?> min
											<?= ($rowTopic['is_duration_permanent'] == 'Y') ? "[Permanent]" : "" ?>
										</td>
										<td align="left" valign="top">
											Sequence
										</td>
										<td align="left" valign="top">
											<?= $rowTopic['sequence'] ?>
										</td>
									</tr>
								</table>
								<?
								$sqlParticipantTheme = array();
								$sqlParticipantTheme['QUERY'] = "   SELECT prt.*, sch.participant_type
															  FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
														INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
																ON sch.participant_id = prt.id
															 WHERE sch.`session_id` = '" . $rowTopic['schedule_session_id'] . "'
															   AND sch.`theme_id` = '" . $rowTopic['schedule_theme_id'] . "'
															   AND sch.topic_id = '" . $topicId . "'";
								$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme);
								if ($resultsParticipantTheme) {
								?>
									<table width="100%">
										<tr>
											<td colspan="4" class="thighlight" align="left">
												Participants
											</td>
										</tr>
									</table>
									<?
									foreach ($resultsParticipantTheme as $kPt => $rowParticipant) {
									?>
										<table width="100%" use="topicParticipantAdd">
											<tr>
												<td align="left" width="20%" style="background:#CCCCCC;">Name</td>
												<td align="left"><?= $rowParticipant['participant_full_name'] ?></td>
												<td align="left" width="20%" style="background:#CCCCCC;">Participating As</td>
												<td align="left" width="30%"><?= $rowParticipant['participant_type'] ?></td>
											</tr>
										</table>
								<?
									}
								}
								?>
							</td>
						</tr>
						<tr>
							<td width="20%"></td>
							<td align="right">
								<input type="submit" name="bttnSubmit" id="bttnSubmit" value="Remove" class="btn btn-small btn-blue" />
							</td>
						</tr>
						<tr>
							<td colspan="2" class="tfooter">&nbsp;</td>
						</tr>
					</table>
				</form>
				<script>
					// Auto-submit the form when popup loads
					document.getElementById('topicRemoverForm').submit();
				</script>
			</div>
		<?php
	}

	function removeTopic($mycms, $cfg)
	{
		global $cfg, $mycms;

		$loggedUserID 			= $mycms->getLoggedUserId();

		$topic_id				= addslashes(trim($_REQUEST['topic_id']));

		$rowTopic 				= getTopicDetails($topic_id);
		$rowSession				= getSessionDetails($rowTopic['schedule_session_id']);
		$rowTheme				= getThemeDetails($rowTopic['schedule_theme_id']);
		$rowExistingParticipant	= getTopicParticipants($topic_id);

		adjustTopicUpSequence($topic_id);
		$sqlInsertTopic = array();
		$sqlInsertTopic['QUERY']		= "     UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
												   SET `schedule_session_id` = NULL,
													   `schedule_theme_id` = NULL,
													   `schedule_hall_id` = NULL,
													   `schedule_hall_id` = NULL,
													   `topic_time_duration` = NULL, 
													   `is_duration_permanent` = 'N',
													   `sequence` = NULL,	
													   `topic_time_slot` = NULL,			
													   `has_problem_with_time` = 'N',		
													   `topic_time_start` = NULL,
													   `topic_time_end` = NULL,					   
													   `modified_by` = '" . $loggedUserID . "',
													   `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
													   `modified_sessionId` = '" . session_id() . "',
													   `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
												 WHERE `id` = '" . $topic_id . "'";
		$mycms->sql_update($sqlInsertTopic);

		adjustTopicTime($rowTopic['schedule_theme_id']);
		setTopicSchedulesTimes($rowTopic['schedule_theme_id']);

		foreach ($rowExistingParticipant['TOPIC'] as $kth => $topicParticipant) {
			$sqlParticipantDelete = array();
			$sqlParticipantDelete['QUERY']   = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
												 WHERE `participant_id` = '" . $topicParticipant['participant_id'] . "'
												   AND `session_id` = '" . $topicParticipant['session_id'] . "'
												   AND `theme_id` = '" . $topicParticipant['theme_id'] . "'
												   AND `topic_id` = '" . $topicParticipant['topic_id'] . "'
												   AND `participant_type` = '" . $topicParticipant['participant_type'] . "'
												   AND `date_id` = '" . $topicParticipant['date_id'] . "'
												   AND `start_time` = '" . $topicParticipant['start_time'] . "'
												   AND `end_time` = '" . $topicParticipant['end_time'] . "'";
			$mycms->sql_delete($sqlParticipantDelete);
		}

	 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Topic Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="program_schedule.php";</script>';
}
function removeThemeParticipant($mycms, $cfg)
	{
		global $loggedUserID;
		$themeId 			 = $_REQUEST['themeId'];
		$participantId 		 = $_REQUEST['participantId'];
		$sqlDelete = array();
		$sqlDelete['QUERY']			 = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . "
									  WHERE `participant_id` = '" . $participantId . "'
									    AND `theme_id` = '" . $themeId . "'
									    AND ( `topic_id` IS NULL OR `topic_id` = '')";
		$mycms->sql_delete($sqlDelete);
		exit();
	}

	function removeTopicParticipant($mycms, $cfg) ///
	{
		global $loggedUserID;
		$topicId 			 = $_REQUEST['topicId'];
		$participantId 		 = $_REQUEST['participantId'];
		$sqlDelete = array();
		$sqlDelete['QUERY']			 = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . "
											  WHERE `participant_id` = '" . $participantId . "'
												AND `topic_id` = '" . $topicId . "'";
		$mycms->sql_delete($sqlDelete);
		exit();
	}
	
	function insertThemeParticipant($mycms, $cfg)
	{
		global $loggedUserID;
		//  echo "<pre>"; print_r($_REQUEST);die;
		$themeId 			 = $_REQUEST['themeId'];

		$rowTheme 			 = getThemeDetails($themeId);
		$ExistingDateId 	 = $rowTheme['date_id'];

		$participantSchudule = array();
		$participantId 	 	 = $_REQUEST['schedule_theme_participant_id'];
		$participantName 	 = $_REQUEST['schedule_theme_participant_name'];
		$participatingAs 	 = $_REQUEST['schedule_theme_participant_as'];
		$isFaculty 	   		 = $_REQUEST['isFaculty'];

		if (is_array($participantId) && sizeof($participantId) > 0) {
			foreach ($participantId as $Key => $Value) {
				if ($Value != "") {
					$counter++;
					$participantSchudule[$counter]['participant_id'] 	= $Value;
					$participantSchudule[$counter]['session_id'] 		= $rowTheme['schedule_id'];
					$participantSchudule[$counter]['theme_id'] 			= $themeId;
					$participantSchudule[$counter]['topic_id'] 			= NULL;
					$participantSchudule[$counter]['participant_type']  = $participatingAs[$Key];
					$participantSchudule[$counter]['date_id'] 			= $rowTheme['date_id'];
					$participantSchudule[$counter]['start_time'] 		= $rowTheme['theme_time_start'];
					$participantSchudule[$counter]['end_time'] 			= $rowTheme['theme_time_end'];
					$participantSchudule[$counter]['isFaculty'] 	    = $isFaculty[$Key];
				}
			}
		}

		composeParticipantSchedule($participantSchudule);

		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="program_schedule.php";</script>';

		exit();
	}
	
	function insertTopicParticipant($mycms, $cfg) ///
	{
		global $loggedUserID;
		$topicId 			 = $_REQUEST['topicId'];
		$rowTopic 			 = getTopicDetails($topicId);
		$rowTheme 			 = getThemeDetails($rowTopic['schedule_theme_id']);

		$participantSchudule = array();
		$participantId 	 	 = $_REQUEST['topic_participant_id'];
		$participantName 	 = $_REQUEST['topic_participant_name'];
		$participatingAs 	 = $_REQUEST['topic_participant_as'];
		$isFaculty 	   		 = $_REQUEST['topic_isFaculty'];

		$participantSchudule 	 = array();
		$counter 				 = 0;
		if (is_array($participantId) && sizeof($participantId) > 0) {
			foreach ($participantId as $Key => $Value) {
				if ($Value != "") {
					$participantSchudule[$counter]['participant_id']   = $Value;
					$participantSchudule[$counter]['session_id'] 	   = $rowTopic['schedule_session_id'];
					$participantSchudule[$counter]['theme_id'] 		   = $rowTopic['schedule_theme_id'];
					$participantSchudule[$counter]['topic_id'] 		   = $topicId;
					$participantSchudule[$counter]['participant_type'] = $participatingAs[$Key];
					$participantSchudule[$counter]['date_id'] 		   = $rowTheme['date_id'];
					$participantSchudule[$counter]['start_time'] 	   = $rowTopic['topic_time_start'];
					$participantSchudule[$counter]['end_time'] 		   = $rowTopic['topic_time_end'];
					$participantSchudule[$counter]['isFaculty'] 	   = $isFaculty[$Key];
					$counter++;
				}
			}
		}

		composeParticipantSchedule($participantSchudule);

		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="program_schedule.php";</script>';

		exit();
	}
function updateTopic($mycms, $cfg, $index, $session_id, $theme_id, $hall_id, $session_date, $start_time, $end_time)
{
    global $cfg, $mycms;
    
    $loggedUserID = $mycms->getLoggedUserId();
    
    if (!isset($_REQUEST['topic_id'][$index]) || !is_array($_REQUEST['topic_id'][$index])) {
        return;
    }
    
    // Get existing topic IDs for this theme to delete removed ones
    $sqlGetExisting = array();
    $sqlGetExisting['QUERY'] = "SELECT id FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                                WHERE `schedule_theme_id` = '" . $theme_id . "'";
    $existingTopics = $mycms->sql_select($sqlGetExisting);
    $existingTopicIds = array();
    foreach ($existingTopics as $topic) {
        $existingTopicIds[] = $topic['id'];
    }
    
    $updatedTopicIds = array();
    
    foreach ($_REQUEST['topic_id'][$index] as $topicKey => $topicIdValue) {
        
        $topic_min_val = addslashes(trim($_REQUEST['topic_duration_min'][$index][$topicKey] ?? 0));
        $color = addslashes(trim($_REQUEST['topicColor'][$index][$topicKey] ?? '#DBDBDB'));
        $topic_time_duration = $topic_min_val;
        
        $is_duration_permanent = addslashes(trim($_REQUEST['is_duration_permanent'][$index][$topicKey] ?? 'N'));
        $is_duration_permanent = ($is_duration_permanent == 'Y') ? 'Y' : 'N';
        
        $sequence = addslashes(trim($_REQUEST['sequence'][$index][$topicKey] ?? 0));
        $reference_tag = addslashes(trim($_REQUEST['reference_tag'][$index][$topicKey] ?? ''));
        
        $participantId = array();
        $participantName = array();
        $participatingAs = array();
        $isFaculty = array();
        
        $importFromAbstract = $_REQUEST['importFromAbstract'][$index][$topicKey] ?? 'N';
        
        if ($importFromAbstract === 'Y') {
            // Handle Abstract Import
            $abstract_id = addslashes(trim($_REQUEST['abstract_id'][$index][$topicKey] ?? 0));
            $abstract_title = addslashes(trim($_REQUEST['abstract_title'][$index][$topicKey] ?? ''));
            $abstract_topic = addslashes(trim($_REQUEST['abstract_topic'][$index][$topicKey] ?? ''));
            $presenter = addslashes(trim($_REQUEST['presenter'][$index][$topicKey] ?? ''));
            $regId = addslashes(trim($_REQUEST['regId'][$index][$topicKey] ?? ''));
            
            if (!empty($abstract_id)) {
                $sqlAbstract = array();
                $sqlAbstract['QUERY'] = "SELECT abstarct.*, topic.id AS topic_id,
                                        applicant.user_full_name, applicant.user_email_id, applicant.user_mobile_no,
                                        applicant.id AS userId, applicant.user_registration_id, applicant.user_unique_sequence,
                                        particpnt.id AS participantId, particpnt.participant_full_name AS participant_name
                                FROM " . _DB_ABSTRACT_REQUEST_ . " abstarct
                                LEFT OUTER JOIN " . _DB_ABSTRACT_TOPIC_ . " abstractTopic 
                                    ON abstarct.abstract_topic_id = abstractTopic.id
                                LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " topic
                                    ON topic.topic_abstract_id = abstarct.id AND topic.status = 'A'
                                LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " applicant 
                                    ON applicant.id = abstarct.applicant_id
                                LEFT OUTER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " particpnt 
                                    ON (applicant.id = particpnt.participant_delegate_id OR particpnt.participant_email_id = applicant.user_email_id)
                                WHERE abstarct.id = '" . $abstract_id . "'";
                
                $resAbstract = $mycms->sql_select($sqlAbstract);
                $rowAbstract = $resAbstract[0] ?? null;
                
                if ($rowAbstract) {
                    // Check if topic exists
                    $checkTopic = array();
                    $checkTopic['QUERY'] = "SELECT id FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                                   WHERE topic_abstract_id = '" . $abstract_id . "'";
                    $existingTopic = $mycms->sql_select($checkTopic);
                    
                    if (empty($existingTopic)) {
                        $sqlInsertTopic = array();
                        $sqlInsertTopic['QUERY'] = "INSERT INTO " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                            SET `schedule_session_id` = '" . $session_id . "', 
                                `schedule_theme_id` = '" . $theme_id . "', 
                                `schedule_hall_id` = '" . $hall_id . "',
                                `topic_title` = '" . ($abstract_title) . "', 
                                `topic_content` = '" . ($abstract_topic) . "', 
                                `isAbstract` = 'YES', 
                                `topic_abstract_id` = '" . $abstract_id . "',
                                `topic_time_duration` = '" . $topic_time_duration . "', 
                                `is_duration_permanent` = '" . $is_duration_permanent . "',
                                `sequence` = '" . $sequence . "',
                                `topic_color` = '" . $color . "',
                                `status` = 'A', 
                                `operationMode` = 'ORAL_PRESENTATION',
                                `created_by` = '" . $loggedUserID . "',
                                `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                                `created_sessionId` = '" . session_id() . "',
                                `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
                        $topic_id = $mycms->sql_insert($sqlInsertTopic);
                    } else {
                        $topic_id = $existingTopic[0]['id'];
                        $sqlUpdateTopic = array();
                        $sqlUpdateTopic['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                            SET `schedule_session_id` = '" . $session_id . "', 
                                `schedule_theme_id` = '" . $theme_id . "', 
                                `schedule_hall_id` = '" . $hall_id . "',
                                `topic_title` = '" . ($abstract_title) . "', 
                                `topic_content` = '" . strtoupper($abstract_topic) . "', 
                                `topic_time_duration` = '" . $topic_time_duration . "', 
                                `is_duration_permanent` = '" . $is_duration_permanent . "',
                                `sequence` = '" . $sequence . "',	
                                `topic_color` = '" . $color . "',
                                `status` = 'A', 
                                `modified_by` = '" . $loggedUserID . "',
                                `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                                `modified_sessionId` = '" . session_id() . "',
                                `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
                            WHERE `id` = '" . $topic_id . "'";
                        $mycms->sql_update($sqlUpdateTopic);
                    }
                    $updatedTopicIds[] = $topic_id;
                    
                    // Create/Update participant from abstract
                    if (empty($rowAbstract['participantId'])) {
                        $sqlParticipant = array();
                        $sqlParticipant['QUERY'] = "INSERT INTO " . _DB_SP_PARTICIPANT_DETAILS_ . " 
                            SET `participant_email_id` = '" . $rowAbstract['user_email_id'] . "', 
                                `participant_full_name` = '" . $rowAbstract['user_full_name'] . "', 
                                `participant_mobile_no` = '" . $rowAbstract['user_mobile_no'] . "', 
                                `participant_delegate_id` = '" . $rowAbstract['userId'] . "', 
                                `participant_unique_sequence` = '" . $rowAbstract['user_unique_sequence'] . "', 
                                `participant_registration_id` = '" . $rowAbstract['user_registration_id'] . "', 
                                `participation_type` = 'ABSTRACT_PRESENTER', 
                                `status` = 'A',
                                `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
                                `created_sessionId` = '" . session_id() . "',
                                `created_browser` = '" . $_SERVER['HTTP_USER_AGENT'] . "',
                                `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
                        $participantId[0] = $mycms->sql_insert($sqlParticipant);
                        $participantName[0] = $rowAbstract['user_full_name'];
                        $participatingAs[0] = trim($rowAbstract['tags'] . ' ' . $rowAbstract['abstract_parent_type'] . ' PRESENTER');
                        $isFaculty[0] = 'N';
                    } else {
                        $participantId[0] = $rowAbstract['participantId'];
                        $participantName[0] = $rowAbstract['participant_name'];
                        $participatingAs[0] = trim($rowAbstract['tags'] . ' ' . $rowAbstract['abstract_parent_type'] . ' PRESENTER');
                        $isFaculty[0] = 'N';
                    }
                }
            }
        } else {
            // Regular Topic (not from abstract)
            $topic_id = addslashes(trim($_REQUEST['topic_id'][$index][$topicKey]));
            $topic_title = addslashes(trim($_REQUEST['topic_title'][$index][$topicKey] ?? ''));
            $topic_content = addslashes(trim($_REQUEST['topic_contant'][$index][$topicKey] ?? ''));
            $sqlFetchTopic = array();
			$sqlFetchTopic['QUERY']           = "   SELECT `id` 
													FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." 
													WHERE status = 'A'
													AND id = '".$topic_id."'";		
																								
			$resultTopic   			 = $mycms->sql_select($sqlFetchTopic);	
			$rowTopic  = $resultTopic[0];
			if ($rowTopic['id'] != '' && $topic_id!='') {
                // Update existing topic
                $sqlUpdateTopic = array();
                $sqlUpdateTopic['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                    SET `schedule_session_id` = '" . $session_id . "', 
                        `schedule_theme_id` = '" . $theme_id . "', 
                        `schedule_hall_id` = '" . $hall_id . "',
                        `topic_title` = '" . $topic_title . "', 
                        `topic_content` = '" . $topic_content . "', 
                        `topic_time_duration` = '" . $topic_time_duration . "', 
                        `is_duration_permanent` = '" . $is_duration_permanent . "',
                        `sequence` = '" . $sequence . "',		
                        `topic_color` = '" . $color . "',				
                        `reference_tag` = '" . $reference_tag . "',
                        `modified_by` = '" . $loggedUserID . "',
                        `modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                        `modified_sessionId` = '" . session_id() . "',
                        `modified_dateTime` = '" . date('Y-m-d H:i:s') . "'
                    WHERE `id` = '" . $topic_id . "'";
                $mycms->sql_update($sqlUpdateTopic);
                $updatedTopicIds[] = $topic_id;
            } else {
                // Insert new topic
                $sqlInsertTopic = array();
                $sqlInsertTopic['QUERY'] = "INSERT INTO " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
                    SET `schedule_session_id` = '" . $session_id . "', 
                        `schedule_theme_id` = '" . $theme_id . "', 
                        `schedule_hall_id` = '" . $hall_id . "',
                        `topic_title` = '" . ($topic_title) . "', 
                        `topic_content` = '" . $topic_content . "', 
                        `topic_time_duration` = '" . $topic_time_duration . "', 
                        `is_duration_permanent` = '" . $is_duration_permanent . "',
                        `sequence` = '" . $sequence . "',
                        `topic_color` = '" . $color . "',				
                        `reference_tag` = '" . $reference_tag . "',
                        `status` = 'A', 
                        `operationMode` = 'ORAL_PRESENTATION',
                        `created_by` = '" . $loggedUserID . "',
                        `created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
                        `created_sessionId` = '" . session_id() . "',
                        `created_dateTime` = '" . date('Y-m-d H:i:s') . "'";
                $topic_id = $mycms->sql_insert($sqlInsertTopic);
                $updatedTopicIds[] = $topic_id;
            }
            
            // Get topic participants
            $participantId = $_REQUEST['topic_theme_participant_id'][$index][$topicKey] ?? [];
            $participantName = $_REQUEST['topic_participant_name'][$index][$topicKey] ?? [];
            $participatingAs = $_REQUEST['topic_theme_participant_as'][$index][$topicKey] ?? [];
            $isFaculty = $_REQUEST['topic_isFaculty'][$index][$topicKey] ?? [];
        }
        
        // Delete existing participants for this topic before inserting new ones
        if (!empty($topic_id)) {
            $sqlDeleteTopicParticipants = array();
            $sqlDeleteTopicParticipants['QUERY'] = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
                WHERE `topic_id` = '" . $topic_id . "'";
            $mycms->sql_delete($sqlDeleteTopicParticipants);
        }
        
        // Adjust topic sequence and times
        if (function_exists('adjustTopicDownSequence')) {
            adjustTopicDownSequence($topic_id);
        }
        if (function_exists('adjustTopicTime')) {
            adjustTopicTime($theme_id);
        }
        if (function_exists('setTopicSchedulesTimes')) {
            setTopicSchedulesTimes($theme_id);
        }
        
        // Get updated topic details
        $rowUpdatedTopic = array();
        if (function_exists('getTopicDetails')) {
            $rowUpdatedTopic = getTopicDetails($topic_id);
        } else {
            $sqlTopic = array();
            $sqlTopic['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " WHERE id = '" . $topic_id . "'";
            $topicResult = $mycms->sql_select($sqlTopic);
            $rowUpdatedTopic = $topicResult[0] ?? [];
        }
        
        // Prepare participant schedule
        $participantSchedule = array();
        $counter = 0;
        
        if (is_array($participantId) && count($participantId) > 0) {
            foreach ($participantId as $Key => $Value) {
                if ($Value != "") {
                    $participantSchedule[$counter]['participant_id'] = $Value;
                    $participantSchedule[$counter]['session_id'] = $session_id;
                    $participantSchedule[$counter]['theme_id'] = $theme_id;
                    $participantSchedule[$counter]['topic_id'] = $topic_id;
                    $participantSchedule[$counter]['participant_type'] = $participatingAs[$Key] ?? '';
                    $participantSchedule[$counter]['date_id'] = $session_date;
                    $participantSchedule[$counter]['start_time'] = $rowUpdatedTopic['topic_time_start'] ?? $start_time;
                    $participantSchedule[$counter]['end_time'] = $rowUpdatedTopic['topic_time_end'] ?? $end_time;
                    $participantSchedule[$counter]['isFaculty'] = $isFaculty[$Key] ?? 'N';
                    $counter++;
                }
            }
        }
        
        // Compose participant schedule
        if (function_exists('composeParticipantSchedule')) {
            composeParticipantSchedule($participantSchedule);
        }
    }
    
    // DELETE TOPICS THAT WERE REMOVED (not in the updated list)
    $topicsToDelete = array_diff($existingTopicIds, $updatedTopicIds);
    foreach ($topicsToDelete as $topicIdToDelete) {
        // Delete topic participants
        $sqlDeleteTopicParticipants = array();
        $sqlDeleteTopicParticipants['QUERY'] = "DELETE FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " 
            WHERE `topic_id` = '" . $topicIdToDelete . "'";
        $mycms->sql_delete($sqlDeleteTopicParticipants);
        
        // Delete topic
        $sqlDeleteTopic = array();
        $sqlDeleteTopic['QUERY'] = "DELETE FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
            WHERE `id` = '" . $topicIdToDelete . "'";
        $mycms->sql_delete($sqlDeleteTopic);
    }
}
function downloadScheduleExcel($mycms, $cfg)
{
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=Schedule" . time() . ".xls");

	$showNumber = false;
	if ($_REQUEST['showMobile'] == 'Y') {
		$showNumber = true;
	}
	$showNumber = true;

	$participantTypes = array("chairperson", "moderator", "panellist", "speaker");

	if (trim($_REQUEST['dateId']) != '' && $_REQUEST['dateId'] > 0) {
		$searchCond		 = " AND id = '" . $_REQUEST['dateId'] . "'";
	}
	$sqlDateisting	= array();
	$sqlDateisting['QUERY']		 = "SELECT *, DATE_FORMAT(conf_date,'%D %M, %Y') AS formattedDate,  DATE_FORMAT(conf_date,'%d%b') AS dateIndex
							FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " 
							WHERE `status` = 'A' " . $searchCond . "
						ORDER BY conf_date";
	$resultDateListing   = $mycms->sql_select($sqlDateisting);
	if ($resultDateListing) {
		$dateRecCounter = 0;
		foreach ($resultDateListing as $keyDateListing => $rowDate) {
	?>
			<table cellpadding="0" cellspacing="0" border="1">
				<tr>
					<td valign="top">Reference Tag</td>
					<td valign="top">Duration</td>
					<td valign="top">Title</td>
					<td valign="top">Participant Name</td>
					<td valign="top">Participant Mobile</td>
					<td valign="top">Partiipation Status</td>
					<td valign="top">Last Comments</td>
					<td valign="top">All Comments</td>
				</tr>
				<tr align="left" valign="middle">
					<td colspan=8 valign="top">
						<h3><?= $rowDate['formattedDate'] ?></h3>
					</td>
				</tr>
				<?
				$sql = array();
				$sql['QUERY']		 = "SELECT hall.*, CONCAT('Hall',hall.id) AS hallIndex, 
										IFNULL(hallTempname.hall_name, hall.hall_title) AS hall_temp_title
									FROM " . _DB_MASTER_HALL_ . "  hall
						LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " hallTempname
									ON hall.id = hallTempname.hall_id  
									AND hallTempname.date_id =  '" . $rowDate['id'] . "'
									WHERE hall.status = 'A'  
									AND hall.id IN (SELECT session_hall_id FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . " WHERE session_date_id = '" . $rowDate['id'] . "')
								ORDER BY hall.hall_title";
				$resultHall  		= $mycms->sql_select($sql);
				if ($resultHall) {
					$hallCounter = 0;
					foreach ($resultHall as $key => $rowHall) {
				?>
						<tr align="left" valign="middle" style="background:#FFFBB0;">
							<td colspan=8 valign="top"><b><?= $rowHall['hall_temp_title'] ?></b></td>
						</tr>
						<?

						$sqlSelectSession = array();
						$sqlSelectSession['QUERY'] 	= " SELECT session.*,
														(TIME_TO_SEC(CONCAT(session_start_time,':00'))/60) AS session_start_time_mins,
														(TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)-1 AS session_end_time_mins,
														hall.hall_title, CONCAT('Hall',hall.id) AS hallIndex,
														venue.id AS venue_id,
														venue.program_venue,
														scheduleDate.conf_date AS session_date  
													
													FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . " session
													
											INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " scheduleDate 
													ON session.session_date_id = scheduleDate.id
													
											INNER JOIN " . _DB_MASTER_HALL_ . " hall 
													ON session.session_hall_id = hall.id
													
											INNER JOIN " . _DB_PROGRAM_SCHEDULE_VENUE_ . " venue 
													ON hall.hall_venue = venue.id
														
													WHERE session.status = 'A' 
													AND session.session_date_id = '" . $rowDate['id'] . "'
													AND session.session_hall_id = '" . $rowHall['id'] . "'
												ORDER BY (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60), (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)";
						$resultSession			 	= $mycms->sql_select($sqlSelectSession);
						if ($resultSession) {
							$sessionCounter = 0;
							foreach ($resultSession as $keySchedule => $rowSession) {
								$sessSt 		= $rowSession['session_start_time'];
								$sessEnd 		= $rowSession['session_end_time'];

								$sessionStartTime = timeReComposer($sessSt, true);
								$sessionEndTime = timeReComposer($sessEnd, true);

								switch ($rowSession['session_color']) {
									case "#FFA042":
										$sessionClass = "color:red";
										break;
									case "#FF71FF":
										$sessionClass = "color:red";
										break;
									default:
										$sessionClass = "color:blck";
								}
						?>
								<tr align="left" valign="middle" style=" <?= $sessionClass ?>; background:#FFC6C6;" typ='sesssion'>
									<td valign="top">&nbsp;</td>
									<td width="150px" valign="top"><b><?= $sessionStartTime ?> - <?= $sessionEndTime ?></b></td>
									<td width="300px" valign="top"><b><?= $rowSession['session_title'] ?></b></td>
									<?
									$thePaticipanIds 			 		 = array('0');
									$sqlParticipantSess 		 		 = array();
									$sqlParticipantSess['QUERY']		 = "SELECT prt.*, sch.participant_type
																	FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
															INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
																	ON sch.participant_id = prt.id
																	WHERE sch.`session_id` = '" . $rowSession['id'] . "'
																	AND (sch.theme_id IS NULL OR sch.theme_id = '')
																	AND (sch.topic_id IS NULL OR sch.topic_id = '')
																ORDER BY prt.participant_full_name, prt.created_dateTime";
									$resultsParticipantSess	 			= $mycms->sql_select($sqlParticipantSess);
									$sessPartArr						= array();
									$sessPartPhArr 						= array();
									$sessPartStstAr 					= array();
									if ($resultsParticipantSess) {
										$participantArr = array();
										$cntt = 0;
										foreach ($resultsParticipantSess as $kPt => $rowParticipant) {
											$thePaticipanIds[] = $rowParticipant['id'];
											if (trim($rowParticipant['participant_type']) == '') {
												$participantArr['none'][$cntt]['name'] = $rowParticipant['participant_full_name'];
											} else {
												$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['name'] 					= $rowParticipant['participant_full_name'];
												$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['phone'] 					= ($showNumber ? ($rowParticipant['participant_mobile_no']) : "");
												$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['participation_status'] 	= $rowParticipant['participation_status'];
											}
											$cntt++;
										}



										$keys = array_keys($participantArr);

										$typeset = false;
										$prevPrtTyp = '';
										foreach ($participantTypes as $kPt => $rowParticipantType) {
											$rowParticipant = $participantArr[$rowParticipantType];
											if (!empty($rowParticipant)) {
												if (!$typeset || $prevPrtTyp != $rowParticipantType) {
													$sessPartArr[] 		= "<b>" . ucwords($rowParticipantType) . "</b>";
													$sessPartPhArr[] 	= "";
													$sessPartStstAr[] 	= "";
													$typeset 			= true;
												}

												foreach ($participantArr[$rowParticipantType] as $k => $val) {
													$sessPartArr[] 		= $val['name'];
													$sessPartPhArr[] 	= $val['phone'];
													$sessPartStstAr[] 	= $val['participation_status'];
												}

												$prevPrtTyp = $rowParticipantType;
											}
										}

										$typeset = false;
										$prevPrtTyp = '';
										foreach ($participantArr as $prtTyp => $rowParticipant) {
											if (!in_array($prtTyp, $participantTypes) && $prtTyp != 'none') {
												if (!$typeset || $prevPrtTyp != $prtTyp) {
													$sessPartArr[] 		= "<b>" . ucwords($prtTyp) . "</b>:";
													$sessPartPhArr[] 	= "";
													$sessPartStstAr[] 	= "";
													$typeset 			= true;
												}

												foreach ($rowParticipant as $k => $val) {
													$sessPartArr[] 		= $val['name'];
													$sessPartPhArr[] 	= $val['phone'];
													$sessPartStstAr[] 	= $val['participation_status'];
												}

												$prevPrtTyp = $prtTyp;
											}
										}

										if (!empty($participantArr['none'])) {
											$sessPartArr[] 		= "<b>Participant</b>:";
											$sessPartPhArr[] 	= "";
											$sessPartStstAr[] 	= "";
											foreach ($participantArr['none'] as $k => $val) {
												$sessPartArr[] 		= $val['name'];
												$sessPartPhArr[] 	= $val['phone'];
												$sessPartStstAr[] 	= $val['participation_status'];
											}
										}
									}
									?>
									<td align="left" valign="top"><?= implode("<br/>", $sessPartArr); ?></td>
									<td align="left" valign="top"><?= implode("<br/>`", $sessPartPhArr); ?></td>
									<td align="left" valign="top"><?= implode("<br/>", $sessPartStstAr); ?></td>
									<td align="left" valign="top">
										<?
										$lastComment   			   = array();
										$coments 	   			   = array();
										$sqlComent	   			   = array();
										$sqlComent['QUERY'] 	   = "    SELECT participant_comment.*, participant_details.participant_full_name, participant_details.participation_status,
																	DATE_FORMAT(participant_comment.created_dateTime, '%Y-%m-%d') AS created_dateTime, DATE_FORMAT(participant_comment.completionDate, '%Y-%m-%d') AS completionDate,
																	recordingUser.name AS recoderName, completingUser.name As completerName
															FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
														INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
																ON participant_comment.participant_id = participant_details.id
													LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
																ON participant_comment.recordedBy = recordingUser.a_id
													LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
																ON participant_comment.completedBy 	= completingUser.a_id
															WHERE participant_comment.session_id 	= '" . $rowSession['id'] . "'
																AND (participant_comment.theme_id IS NULL OR participant_comment.theme_id = '')
																AND (participant_comment.topic_id IS NULL OR participant_comment.topic_id = '') 
														ORDER BY participant_details.participant_full_name, participant_comment.created_dateTime";
										$resComent		= $mycms->sql_select($sqlComent);
										if ($resComent) {
											foreach ($resComent as $k => $rowComments) {
												$participants[] = '<b>' . $rowComments['participant_full_name'] . '</b>';
												$lastComment[$rowComments['participant_full_name']] = '<b>' . $rowComments['participant_full_name'] . ' : </b>' . $rowComments['comment'] . '<i>' . (($rowComments['completionRemarks'] != '') ? ("(" . $rowComments['completionRemarks'] . ")") : "") . '</i>';
												$coments[] = $lastComment[$rowComments['participant_full_name']];
											}
										}
										?>
										<?= implode("<br/>", $lastComment); ?></td>
									<td align="left" valign="top"><?= implode("<br/>", $coments); ?></td>
								</tr>
								<?
								$sqlFetchTheme = array();
								$sqlFetchTheme['QUERY']           = "   SELECT *, 
																		(TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60) AS theme_time_start_mins,
																		(TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)-1 AS theme_time_end_mins
																	FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
																	WHERE schedule_id = '" . $rowSession['id'] . "'
																	AND status = 'A'  
																ORDER BY (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60), (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)";
								$resultTheme   = $mycms->sql_select($sqlFetchTheme);
								if ($resultTheme) {
									foreach ($resultTheme as $keyTheme => $rowtheme) {
										if (true) //trim($rowtheme['theme_title'])!=''
										{
											$thmSt 			= $rowtheme['theme_time_start'];
											$thmEnd 		= $rowtheme['theme_time_end'];

											$themeStartTime = timeReComposer($thmSt, true);
											$themeEndTime = timeReComposer($thmEnd, true);
								?>
											<tr align="left" valign="middle" class="session" style=" background:#D7D7FF;" typ='theme'>
												<td valign="top">&nbsp;</td>
												<td valign="top"><?= $themeStartTime ?> - <?= $themeEndTime ?></td>
												<td valign="top"><?= $rowtheme['theme_title'] ?></td>
												<?
												$thePaticipanIds 			 		 = array('0');
												$sqlParticipantTheme 		 		 = array();
												$sqlParticipantTheme['QUERY']		 = "SELECT prt.*, sch.participant_type
																				FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
																		INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
																				ON sch.participant_id = prt.id
																				WHERE sch.`session_id` = '" . $rowtheme['schedule_id'] . "'
																				AND sch.`theme_id` = '" . $rowtheme['id'] . "'
																				AND (sch.topic_id IS NULL OR sch.topic_id = '')";
												$resultsParticipantTheme	 		= $mycms->sql_select($sqlParticipantTheme);
												$themePartArr						= array();
												$themePartPhArr 					= array();
												$themePartStstAr 					= array();
												if ($resultsParticipantTheme) {
													$participantArr = array();
													$cntt = 0;
													foreach ($resultsParticipantTheme as $kPt => $rowParticipant) {
														$thePaticipanIds[] = $rowParticipant['id'];
														if (trim($rowParticipant['participant_type']) == '') {
															$participantArr['none'][$cntt]['name'] = $rowParticipant['participant_full_name'];
														} else {
															$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['name'] 					= $rowParticipant['participant_full_name'];
															$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['phone'] 					= ($showNumber ? ($rowParticipant['participant_mobile_no']) : "");
															$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['participation_status'] 	= $rowParticipant['participation_status'];
														}
														$cntt++;
													}



													$keys = array_keys($participantArr);

													$typeset = false;
													$prevPrtTyp = '';
													foreach ($participantTypes as $kPt => $rowParticipantType) {
														$rowParticipant = $participantArr[$rowParticipantType];
														if (!empty($rowParticipant)) {
															if (!$typeset || $prevPrtTyp != $rowParticipantType) {
																$themePartArr[] 	= "<b>" . ucwords($rowParticipantType) . "</b>";
																$themePartPhArr[] 	= ".";
																$themePartStstAr[] 	= ".";
																$typeset = true;
															}

															foreach ($participantArr[$rowParticipantType] as $k => $val) {
																$themePartArr[] 	= $val['name'];
																$themePartPhArr[] 	= $val['phone'];
																$themePartStstAr[] 	= $val['participation_status'];
															}

															$prevPrtTyp = $rowParticipantType;
														}
													}

													$typeset = false;
													$prevPrtTyp = '';
													foreach ($participantArr as $prtTyp => $rowParticipant) {
														if (!in_array($prtTyp, $participantTypes) && $prtTyp != 'none') {
															if (!$typeset || $prevPrtTyp != $prtTyp) {
																$themePartArr[] 	= "<b>" . ucwords($prtTyp) . "</b>:";
																$themePartPhArr[] 	= ".";
																$themePartStstAr[] 	= ".";
																$typeset = true;
															}

															foreach ($rowParticipant as $k => $val) {
																$themePartArr[] 	= $val['name'];
																$themePartPhArr[] 	= $val['phone'];
																$themePartStstAr[] 	= $val['participation_status'];
															}

															$prevPrtTyp = $prtTyp;
														}
													}

													if (!empty($participantArr['none'])) {
														$themePartArr[] 	= "<b>Participant</b>:";
														$themePartPhArr[] 	= "<span style='color:white'>.</span>";
														$themePartStstAr[] 	= "<span style='color:white'>.</span>";
														foreach ($participantArr['none'] as $k => $val) {
															$themePartArr[] 	= $val['name'];
															$themePartPhArr[] 	= $val['phone'];
															$themePartStstAr[] 	= $val['participation_status'];
														}
													}
												}
												?>
												<td align="left" valign="top"><?= implode("<br/>", $themePartArr); ?></td>
												<td align="left" valign="top"><?= implode("<br/>`", $themePartPhArr); ?></td>
												<td align="left" valign="top"><?= implode("<br/>", $themePartStstAr); ?></td>
												<td align="left" valign="top">
													<?
													$lastComment 	   		   = array();
													$coments 	   			   = array();
													$sqlComent 				   = array();
													$sqlComent['QUERY'] 	   = "    SELECT participant_comment.*, participant_details.participant_full_name,  participant_details.participation_status,
																				DATE_FORMAT(participant_comment.created_dateTime, '%Y-%m-%d') AS created_dateTime, DATE_FORMAT(participant_comment.completionDate, '%Y-%m-%d') AS completionDate,
																				recordingUser.name AS recoderName, completingUser.name As completerName
																		FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
																	INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
																			ON participant_comment.participant_id = participant_details.id
																LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
																			ON participant_comment.recordedBy = recordingUser.a_id
																LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
																			ON participant_comment.completedBy 	= completingUser.a_id
																		WHERE participant_comment.session_id 	= '" . $rowSession['id'] . "'
																			AND participant_comment.theme_id 		= '" . $rowtheme['id'] . "'																	 
																			AND (participant_comment.topic_id IS NULL OR participant_comment.topic_id = '') 
																			AND participant_comment.participant_id IN (" . implode(",", $thePaticipanIds) . ")
																	ORDER BY participant_details.participant_full_name, participant_comment.created_dateTime";
													$resComent		= $mycms->sql_select($sqlComent);
													$lastComment	= '';
													if ($resComent) {
														foreach ($resComent as $k => $rowComments) {
															$lastComment[$rowComments['participant_full_name']] = '<b>' . $rowComments['participant_full_name'] . ' : </b>' . $rowComments['comment'] . '<i>' . (($rowComments['completionRemarks'] != '') ? ("(" . $rowComments['completionRemarks'] . ")") : "") . '</i>';
															$coments[] = $lastComment[$rowComments['participant_full_name']];
														}
													}
													echo  implode("<br/>", $lastComment);
													?>
												</td>
												<td align="left" valign="top">
													<? echo implode("<br/>", $coments); ?>
												</td>
											</tr>
											<?
										}

										$sqlFetchTopic = array();
										$sqlFetchTopic['QUERY']   = "   SELECT *, 
																		topic_time_duration AS topic_time_duration_mins,
																		(TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
																		(TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins
																	FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
																	WHERE schedule_session_id = '" . $rowSession['id'] . "'
																	AND schedule_theme_id = '" . $rowtheme['id'] . "'
																	AND status = 'A'
																ORDER BY sequence, id";
										$resultTopic   			 = $mycms->sql_select($sqlFetchTopic);
										if ($resultTopic) {
											foreach ($resultTopic as $keyTopic => $rowTopic) {
												$tpcSt 			= $rowTopic['topic_time_start'];
												$tpcEnd 		= $rowTopic['topic_time_end'];

												$topicStartTime = timeReComposer($tpcSt, true);
												$topicEndTime = timeReComposer($tpcEnd, true);
											?>
												<tr align="left" valign="middle" typ='topic'>
													<td valign="top"><?= $rowTopic['reference_tag'] ?></td>
													<td valign="top"><?= $rowTopic['topic_time_duration_mins'] ?> Mins</td><!--<?= $topicStartTime ?> - <?= $topicEndTime ?>-->
													<td valign="top"><?= $rowTopic['topic_title'] ?></td>
													<?
													$thePaticipanIds 			 	= array('0');
													$sqlParticipantTheme 			= array();
													$sqlParticipantTheme['QUERY'] 	= " SELECT prt.*, sch.participant_type
																				FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
																		INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
																				ON sch.participant_id = prt.id
																				WHERE sch.`session_id` = '" . $rowTopic['schedule_session_id'] . "'
																				AND sch.`theme_id` = '" . $rowTopic['schedule_theme_id'] . "'
																				AND sch.`topic_id` = '" . $rowTopic['id'] . "'
																			ORDER BY prt.participant_full_name";
													$resultsParticipantTheme	= $mycms->sql_select($sqlParticipantTheme);
													$topicPartArr 				= array();
													$topicPartPhArr 			= array();
													$topicPartStstAr 			= array();
													if ($resultsParticipantTheme) {
														$participantArr = array();
														$cntt = 0;
														foreach ($resultsParticipantTheme as $kPt => $rowParticipant) {
															$thePaticipanIds[] = $rowParticipant['id'];
															if (trim($rowParticipant['participant_type']) == '') {
																$participantArr['none'][$cntt]['name'] = $rowParticipant['participant_full_name'];
															} else {
																$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['name'] 					= $rowParticipant['participant_full_name'];
																$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['phone'] 					= ($showNumber ? ($rowParticipant['participant_mobile_no']) : "");
																$participantArr[strtolower(trim($rowParticipant['participant_type']))][$cntt]['participation_status'] 	= $rowParticipant['participation_status'];
															}
															$cntt++;
														}

														$keys = array_keys($participantArr);

														$typeset = false;
														$prevPrtTyp = '';
														foreach ($participantTypes as $kPt => $rowParticipantType) {
															$rowParticipant = $participantArr[$rowParticipantType];
															if (!empty($rowParticipant)) {
																if (!$typeset || $prevPrtTyp != $rowParticipantType) {
																	$topicPartArr[] 	= "<b>" . ucwords($rowParticipantType) . "</b>";
																	$topicPartPhArr[] 	= ".";
																	$topicPartStstAr[] 	= ".";
																	$typeset 			= true;
																}

																foreach ($rowParticipant as $k => $val) {
																	$topicPartArr[] 	= $val['name'];
																	$topicPartPhArr[] 	= $val['phone'];
																	$topicPartStstAr[] 	= $val['participation_status'];
																}

																$prevPrtTyp = $rowParticipantType;
															}
														}

														$typeset = false;
														$prevPrtTyp = '';
														foreach ($participantArr as $prtTyp => $rowParticipant) {
															if (!in_array($prtTyp, $participantTypes) && $prtTyp != 'none') {
																if (!$typeset || $prevPrtTyp != $prtTyp) {
																	$topicPartArr[] 	= "<b>" . ucwords($prtTyp) . "</b>:";
																	$topicPartPhArr[] 	= "<span style='color:white'>.</span>";
																	$topicPartStstAr[] 	= "<span style='color:white'>.</span>";
																	$typeset 			= true;
																}

																foreach ($rowParticipant as $k => $val) {
																	$topicPartArr[] 	= $val['name'];
																	$topicPartPhArr[] 	= $val['phone'];
																	$topicPartStstAr[] 	= $val['participation_status'];
																}
															}

															$prevPrtTyp = $prtTyp;
														}

														if ($participantArr['none'] != '') {
															$topicPartArr[] 	= "<b>Participant</b>:";
															$topicPartPhArr[] 	= ".";
															$topicPartStstAr[] 	= ".";
															foreach ($participantArr['none'] as $k => $val) {
																$topicPartArr[] 	= $val['name'];
																$topicPartPhArr[] 	= $val['phone'];
																$topicPartStstAr[] 	= $val['participation_status'];
															}
														}
													}
													?>
													<td align="left" valign="top"><?= implode("<br/>", $topicPartArr); ?></td>
													<td align="left" valign="top"><?= implode("<br/>`", $topicPartPhArr); ?></td>
													<td align="left" valign="top"><?= implode("<br/>", $topicPartStstAr); ?></td>
													<td align="left" valign="top">
														<?
														$coments 	   = array();
														$lastComment   = array();
														$sqlComent = array();
														$sqlComent['QUERY'] 	   = "SELECT participant_comment.*, participant_details.participant_full_name,
																				DATE_FORMAT(participant_comment.created_dateTime, '%Y-%m-%d') AS created_dateTime, DATE_FORMAT(participant_comment.completionDate, '%Y-%m-%d') AS completionDate,
																				recordingUser.name AS recoderName, completingUser.name As completerName
																		FROM " . _DB_SP_PARTICIPANT_COMMENTS_ . " participant_comment
																	INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
																			ON participant_comment.participant_id = participant_details.id
																LEFT OUTER JOIN " . _DB_CONF_USER_ . " recordingUser
																			ON participant_comment.recordedBy = recordingUser.a_id
																LEFT OUTER JOIN " . _DB_CONF_USER_ . " completingUser
																			ON participant_comment.completedBy 	= completingUser.a_id
																		WHERE participant_comment.session_id 	= '" . $rowSession['id'] . "'
																			AND participant_comment.theme_id 		= '" . $rowtheme['id'] . "'
																			AND participant_comment.topic_id 		= '" . $rowTopic['id'] . "' 
																			AND participant_comment.participant_id IN (" . implode(",", $thePaticipanIds) . ")
																	ORDER BY participant_details.participant_full_name, participant_comment.created_dateTime";
														$resComent				= $mycms->sql_select($sqlComent);
														$lastComment			= '';
														if ($resComent) {
															foreach ($resComent as $k => $rowComments) {
																$lastComment[$rowComments['participant_full_name']]	= '<b>' . $rowComments['participant_full_name'] . ' : </b>' . $rowComments['comment'] . '<i>' . (($rowComments['completionRemarks'] != '') ? ("(" . $rowComments['completionRemarks'] . ")") : "") . '</i>';
																$coments[] 		= $lastComment[$rowComments['participant_full_name']];
															}
														}
														echo implode("<br/>", $lastComment)
														?>
													</td>
													<td align="left" valign="top">
														<? echo implode("<br/>", $coments); ?>
													</td>
												</tr>
				<?
											}
										}
									}
								}
								$sessionCounter++;
							}
						}
					}
				}
				$dateRecCounter++;
				?>
			</table>
	<?
		}
	}
	exit();
}
function getPDFDisplay($mycms, $cfg)
{
	$submission   	= trim($_REQUEST['submission']);
	$baseURLpath 	= 'http://imscon2019.com/';
	$cssPath 		= _BASE_URL_ . 'webmaster/pdfcss/';
	$pgHiet			= 0;
	$pdfString 		= '';

	$sql 	=	array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
											WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
	$result = $mycms->sql_select($sql);
	$row    		 = $result[0];

	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
	$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
	if ($row['header_image'] != '') {
		$emailHeader  = $header_image;
	}

	$pdfString .= '	
<link rel="stylesheet" href="' . $cssPath . '/bootstrap.min.css" />
<link rel="stylesheet" href="' . $cssPath . '/normalize.css" />
<link rel="stylesheet" href="' . $cssPath . '/font-awesome.min.css" />
<link rel="stylesheet" href="' . $cssPath . '/icomoon.css" />
<link rel="stylesheet" href="' . $cssPath . '/owl.carousel.min.css" />
<link rel="stylesheet" href="' . $cssPath . '/customScrollbar.css" />
<link rel="stylesheet" href="' . $cssPath . '/photoswipe.css" />
<link rel="stylesheet" href="' . $cssPath . '/default-skin.css" />
<link rel="stylesheet" href="' . $cssPath . '/prettyPhoto.css" />
<link rel="stylesheet" href="' . $cssPath . '/animate.css" />
<link rel="stylesheet" href="' . $cssPath . '/transitions.css" />
<link rel="stylesheet" href="' . $cssPath . '/main.css" />
<link rel="stylesheet" href="' . $cssPath . '/color.css" />
<link rel="stylesheet" href="' . $cssPath . '/style.css" />
<link rel="stylesheet" href="' . $cssPath . '/responsive.css" />
<link rel="stylesheet" href="' . $cssPath . '/videopopup.css" />
<section id="program" class="tg-haslayout">
	<div class="tg-sectionspace container" style="padding: 40px 0 !important">
		<div class="row">
			<div class="tg-eventvenueregistration">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-bottom:20px;">
						<img src="' . $emailHeader . '" width="100%" alt="header" />
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="tg-headholder">
						<div class="tg-sectionhead tg-textalignleft">
							<div class="tg-sectionheading">
								<h2>Scientific Program</h2>
							</div>
						</div>
					</div>
					<div class="tg-locationregister no-gutters program_body">
						<div class="row">';

	$pgHiet += 50;

	// Main ProgramBody
	$participantTypes = array("chairperson", "moderator", "panellist", "speaker");
	$sqlDateisting = array();
	$sqlDateisting['QUERY'] = "SELECT *, DATE_FORMAT(conf_date,'%M <span>%e</span>') AS formattedDate,  DATE_FORMAT(conf_date,'%d%b') AS dateIndex,  DATE_FORMAT(conf_date,'%b%d') AS dateIndex2
								FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " WHERE `status` = 'A'";
	$resultDateListing   	= $mycms->sql_select($sqlDateisting);

	$pdfString .= '	<div class="col-md-12">
					<div class="sc_program">
						<div class="row">';

	if ($resultDateListing) {
		$dateRecCounter = 0;
		foreach ($resultDateListing as $keyDateListing => $rowDate) {
			ini_set('max_execution_time', 300);

			$pdfString .= '		<div class="col-md-2 col-lg-2 pr0">
								<div class="tab_list program_main_tab">
									<ul class="nav">
										<li use="sessdateLis" id="' . $rowDate['dateIndex'] . 'Tab" class="nav-item slow active">
											<img src="https://www.ruedakolkata.com/imscon2019/webmaster/section_scientific_program/images/' . $rowDate['dateIndex'] . '.png" />
										</li>
									</ul>
								</div>
							</div>
							<br><br>
							<div class="col-md-10 col-lg-10">										
								<div id="' . $rowDate['dateIndex'] . 'Box" class="tab_details_body program_main_tab_details" style="display:block;">
									<div class="program_content">
										<div class="program_sub_content">';

			$sql = array();
			$sql['QUERY'] = "   SELECT *, CONCAT('Hall',id) AS hallIndex 
								FROM " . _DB_MASTER_HALL_ . " 
								WHERE status = 'A'  
								AND id IN (SELECT session_hall_id FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . " WHERE session_date_id = '" . $rowDate['id'] . "')
							ORDER BY hall_title";
			$resultHall  = $mycms->sql_select($sql);
			if ($resultHall) {
				$hallCounter = 0;
				foreach ($resultHall as $key => $rowHall) {
					ini_set('max_execution_time', 300);

					$pdfString .= '			<div class="tab_list program_sub_tab_' . $rowDate['dateIndex'] . 'Hall">
											<ul class="nav">
												<li id="' . $rowDate['dateIndex'] . $rowHall['hallIndex'] . 'Tab"  class="nav-item slow active">' . $rowHall['hall_title'] . '</li>
											</ul>
										</div>	
											<div id="' . $rowDate['dateIndex'] . $rowHall['hallIndex'] . 'Box" class="tab_details_body program_sub_tab_' . $rowDate['dateIndex'] . 'Hall_details" style="display:block;font-size:17px !important;">';

					$pgHiet += 20;

					$sqlSelectSession = array();
					$sqlSelectSession['QUERY'] = "  SELECT session.*,
														(TIME_TO_SEC(CONCAT(session_start_time,':00'))/60) AS session_start_time_mins,
														(TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)-1 AS session_end_time_mins,
														hall.hall_title, CONCAT('Hall',hall.id) AS hallIndex,
														venue.id AS venue_id,
														venue.program_venue,
														scheduleDate.conf_date AS session_date  
													
													FROM " . _DB_PROGRAM_SCHEDULE_SESSION_ . " session
													
											INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " scheduleDate 
													ON session.session_date_id = scheduleDate.id
													
											INNER JOIN " . _DB_MASTER_HALL_ . " hall 
													ON session.session_hall_id = hall.id
													
											INNER JOIN " . _DB_PROGRAM_SCHEDULE_VENUE_ . " venue 
													ON hall.hall_venue = venue.id
														
													WHERE session.status = 'A' 
													AND session.session_date_id = '" . $rowDate['id'] . "'
													AND session.session_hall_id = '" . $rowHall['id'] . "'
												ORDER BY (TIME_TO_SEC(CONCAT(session_start_time,':00'))/60), (TIME_TO_SEC(CONCAT(session_end_time,':00'))/60)";
					$resultSession			   = $mycms->sql_select($sqlSelectSession);
					if ($resultSession) {
						$sessionCounter = 0;
						foreach ($resultSession as $keySchedule => $rowSession) {
							ini_set('max_execution_time', 300);

							$sessSt 		= $rowSession['session_start_time'];
							$sessEnd 		= $rowSession['session_end_time'];

							$sessionStartTime = timeReComposer($sessSt, true);
							$sessionEndTime = timeReComposer($sessEnd, true);

							$pdfString .= '			<div class="session_heading">
													<h3>' . $rowSession['session_title'] . '<span style="float:right;">' . $sessionStartTime . '-' . $sessionEndTime . '</span></h3>
												</div>';

							$pgHiet += 10;

							$sqlFetchTheme = array();
							$sqlFetchTheme['QUERY'] = " SELECT *, 
															(TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60) AS theme_time_start_mins,
															(TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)-1 AS theme_time_end_mins
														FROM " . _DB_PROGRAM_SCHEDULE_THEME_ . " 
														WHERE schedule_id = '" . $rowSession['id'] . "'
														AND status = 'A'  
													ORDER BY (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60), (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)";
							$resultTheme   = $mycms->sql_select($sqlFetchTheme);
							if ($resultTheme) {
								foreach ($resultTheme as $keyTheme => $rowtheme) {
									ini_set('max_execution_time', 300);

									$thmSt 			= $rowtheme['theme_time_start'];
									$thmEnd 		= $rowtheme['theme_time_end'];

									$sessionStartTime 	= timeReComposer($thmSt);
									$sessionEndTime 	= timeReComposer($thmEnd);

									$themeParticipants = "";
									$sqlParticipantTheme = array();
									$sqlParticipantTheme['QUERY'] = "   SELECT prt.*, sch.participant_type
																		FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
																INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
																		ON sch.participant_id = prt.id
																		WHERE sch.`session_id` = '" . $rowtheme['schedule_id'] . "'
																		AND sch.`theme_id` = '" . $rowtheme['id'] . "'
																		AND (sch.topic_id IS NULL OR sch.topic_id = '')";
									$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme);
									$themePartArr	= array();
									if ($resultsParticipantTheme) {
										$participantArr = array();
										foreach ($resultsParticipantTheme as $kPt => $rowParticipant) {
											if (trim($rowParticipant['participant_type']) == '') {
												$participantArr['none'][] = $rowParticipant['participant_full_name'];
											} else {
												$participantArr[strtolower(trim($rowParticipant['participant_type']))][] = $rowParticipant['participant_full_name'];
											}
										}

										$keys = array_keys($participantArr);

										foreach ($participantTypes as $kPt => $rowParticipantType) {
											$rowParticipant = implode(', ', $participantArr[$rowParticipantType]);
											if (trim($rowParticipant) != '') {
												$themePartArr[] = "<b>" . ucwords($rowParticipantType) . "</b>:" . $rowParticipant;
											}
										}

										foreach ($participantArr as $prtTyp => $rowParticipant) {
											if (!in_array($prtTyp, $participantTypes) && $prtTyp != 'none') {
												$themePartArr[] = "<b>" . ucwords($prtTyp) . "</b>:" . implode(', ', $rowParticipant);
											}
										}

										if ($participantArr['none'] != '') {
											$themePartArr[] = "<b>Participant</b>:" . implode(', ', $participantArr['none']);
										}
										$themeParticipants = implode(', ', $themePartArr);
									}

									$pdfString .= '	<div class="theme_heading">
													<h3>' . $rowtheme['theme_title'] . '</h3>';
									foreach ($themePartArr as $k => $themeParticipants) {
										$pdfString .= '	<p>' . $themeParticipants . '</p>';
									}

									$pdfString .= '	</div>';

									$pgHiet += 20;

									$sqlFetchTopic = array();
									$sqlFetchTopic['QUERY'] = " SELECT *, 
																	topic_time_duration AS topic_time_duration_mins,
																	(TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
																	(TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins
																FROM " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " 
																WHERE schedule_session_id = '" . $rowSession['id'] . "'
																AND schedule_theme_id = '" . $rowtheme['id'] . "'
																AND status = 'A'
															ORDER BY sequence, id";
									$resultTopic   			 = $mycms->sql_select($sqlFetchTopic);
									if ($resultTopic) {
										foreach ($resultTopic as $keyTopic => $rowTopic) {
											$tpcSt 			= $rowTopic['topic_time_start'];
											$tpcEnd 		= $rowTopic['topic_time_end'];

											$topicStartTime 	= timeReComposer($tpcSt, true);
											$topicEndTime 		= timeReComposer($tpcEnd, true);

											$topicParticipants = "";
											$sqlParticipantTheme = array();
											$sqlParticipantTheme['QUERY'] = "   SELECT prt.*, sch.participant_type
																				FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " sch
																		INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " prt
																				ON sch.participant_id = prt.id
																				WHERE sch.`session_id` = '" . $rowTopic['schedule_session_id'] . "'
																				AND sch.`theme_id` = '" . $rowTopic['schedule_theme_id'] . "'
																				AND sch.`topic_id` = '" . $rowTopic['id'] . "'
																			ORDER BY prt.participant_full_name";
											$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme);
											$topicPartArr = array();
											if ($resultsParticipantTheme) {
												$participantArr = array();
												foreach ($resultsParticipantTheme as $kPt => $rowParticipant) {
													if (trim($rowParticipant['participant_type']) == '') {
														$participantArr['none'][] = $rowParticipant['participant_full_name'];
													} else {
														$participantArr[strtolower(trim($rowParticipant['participant_type']))][] = $rowParticipant['participant_full_name'];
													}
												}

												$keys = array_keys($participantArr);

												foreach ($participantTypes as $kPt => $rowParticipantType) {
													$rowParticipant = implode(', ', $participantArr[$rowParticipantType]);
													if (trim($rowParticipant) != '') {
														$topicPartArr[] = $rowParticipant; //"<b>".ucwords($rowParticipantType)."</b>:".
													}
												}

												foreach ($participantArr as $prtTyp => $rowParticipant) {
													if (!in_array($prtTyp, $participantTypes) && $prtTyp != 'none') {
														$topicPartArr[] = implode(', ', $rowParticipant); //"<b>".ucwords($prtTyp)."</b>:".
													}
												}

												if ($participantArr['none'] != '') {
													$topicPartArr[] = implode(', ', $participantArr['none']); //"<b>Participant</b>:".
												}
												$topicParticipants = implode(', ', $topicPartArr);
											}
											if (trim($rowTopic['topic_title']) != '') {
												$pdfString .= '	<div class="session">
																<!--<div class="icon"><img alt="" src="' . $baseURLpath . 'images/icon-session.png"></div>-->
																<article class="session_details" style="width:90% !important; margin-left:30px;">
																	<div class="left" style="width:150px !important;">
																		<div class="time"><i class="fa fa-clock-o"></i> <span>' . $topicStartTime . '-' . $topicEndTime . '</span></div>';
												$pdfString .= '			</div>
																	<div class="right">	
																		<h3>' . $rowTopic['topic_title'] . '</h3>';

												if (trim($topicParticipants) != '') {
													$pdfString .= '			<p><i class="fa fa-user-md"></i> &nbsp;&nbsp;' . $topicParticipants . '</p>';
												}

												$pdfString .= '			</div>
																</article>
															</div>';
												$pgHiet += 30;
											}
										}
									}
								}
							}
							$sessionCounter++;
						}
					}

					$pdfString .= '				</div>';

					$pgHiet += 10;

					$hallCounter++;
				}
			}
			$dateRecCounter++;

			$pdfString .= '					</div>
									</div>
								</div>
							</div>
							<div class="clear"></div>
							';
			$pgHiet += 10;
		}
	}

	$pdfString .= '			</div>
					</div>
				</div>';
	// Main ProgramBody END

	$pdfString .= '			</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-bottom:20px;">
						<img src="' . $footer_image . '" width="100%" alt="footer" />
				</div>
	</div>
	
	
	
</section>';


	if ($submission === 'SHOW') {
		echo $pdfString;
	} else {

		try {
			if ($_REQUEST['USE'] == 'PNG') {
				// include_once('../../lib/pdfcrowd.php');
				include_once(__DIR__ . "/../lib/pdfcrowd.php");

				$client = new \Pdfcrowd\HtmlToImageClient($cfg['CROWD.PDF.USERNAME'], '509c38ff7f042c440a9e7c2c98f07c13');
				$client->setOutputFormat("png");
				$pdf = $client->convertString($pdfString);
				header("Content-Type: image/png");
				header("Cache-Control: no-cache");
				header("Accept-Ranges: none");
				header("Content-Disposition: attachment; filename=\"programme_schedule_trauma_update2018.png\"");
				echo $pdf;
			} else {
				include_once(__DIR__ . "/../lib/pdfcrowd.php");

				$h = $pgHiet . 'mm';
				$client = new Pdfcrowd($cfg['CROWD.PDF.USERNAME'], $cfg['CROWD.PDF.API.KEY']);
				$client->enableImages(true);
				$client->setPageWidth("210mm");
				$client->setPageHeight("330mm");
				$pdf = $client->convertHtml($pdfString);
				header("Content-Type: application/pdf");
				header("Cache-Control: no-cache");
				header("Accept-Ranges: none");
				header("Content-Disposition: attachment; filename=\"Schedule.pdf\"");
				echo $pdf;
			}
		} catch (Exception $e) {
			echo $pdfString;
		?>
			<script>
				window.print();
			</script>
	<?
		}
	}
	exit();
}