<?php 
	include_once('includes/init.php');
	
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action){
			
		/*******************************************************************************/
		/*                              UPDATE PERSONAL DETAILS                        */
		/*******************************************************************************/
		case'updatePersonalDetails':
			
			$facultyId                            = $mycms->getLoggedUserId();
			
			$faculty_title                        = addslashes(trim(strtoupper($_REQUEST['faculty_title_edit'])));
			$faculty_first_name                   = addslashes(trim(strtoupper($_REQUEST['faculty_first_name_edit'])));
			$faculty_middle_name                  = addslashes(trim(strtoupper($_REQUEST['faculty_middle_name_edit'])));
			$faculty_last_name                    = addslashes(trim(strtoupper($_REQUEST['faculty_last_name_edit'])));
			$faculty_designation                  = addslashes(trim(strtoupper($_REQUEST['faculty_designation_edit'])));
			$faculty_institution_name             = addslashes(trim(strtoupper($_REQUEST['faculty_institution_name_edit'])));
			$faculty_specification                = addslashes(trim(strtoupper($_REQUEST['faculty_specification_edit'])));
			
			// UPDATE FACULTY DETAILS
			$sqlUpdateFaculty['QUERY']                     = "UPDATE "._DB_FACULTY_ACCOUNT_." 
													    SET `faculty_title` = '".$faculty_title."', 
														    `faculty_first_name` = '".$faculty_first_name."', 
														    `faculty_middle_name` = '".$faculty_middle_name."', 
														    `faculty_last_name` = '".$faculty_last_name."', 
														    `faculty_designation` = '".$faculty_designation."', 
														    `faculty_institution_name` = '".$faculty_institution_name."', 
														    `faculty_specification` = '".$faculty_specification."', 
														 	`created_by` = '".$loggedUserID."', 
														 	`created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
														 	`created_sessionId` = '".session_id()."', 
														 	`created_dateTime` = '".date('Y-m-d H:i:s')."' 
													  WHERE `id` = '".$facultyId."'";
																 
			$mycms->sql_update($sqlUpdateFaculty);
			
			$mycms->redirect("userProfile.php");
			exit();
			break;
		
		/*******************************************************************************/
		/*                                 CHANGE USER IMAGE                           */
		/*******************************************************************************/
		case'changeUserImage':
			
			$loggedFacultyID                   = $mycms->getLoggedUserId(); 
			$facultyImage                      = $_FILES['faculty_image']['name'];
			$facultyImageTempFile              = $_FILES['faculty_image']['tmp_name'];
			$facultyImageFileName              = $loggedFacultyID.time().strstr($facultyImage, '.');
			
			if($facultyImageTempFile!="")
			{
				$facultyImagePath              = "../../".$cfg['FACULTY.PROFILE.IMAGE'].$facultyImageFileName;
				
				chmod($facultyImagePath, 0777);
				copy($facultyImageTempFile, $facultyImagePath);
				chmod($facultyImagePath, 0777);
		
				$sqlFacultyImage['QUERY']               = "UPDATE "._DB_FACULTY_ACCOUNT_." 
									                 SET `faculty_profile_image` = '".$facultyImageFileName."' 
								                   WHERE `id` = '".$loggedFacultyID."'";
				
				$mycms->sql_update($sqlFacultyImage);
			}
			
			$mycms->redirect("userProfile.php");	
			exit();
			break;
	}
?>