<?php
	include_once('includes/init.php');
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action){
		
		/****************************************************************************************/
		/*                                SEARCH FACULTY PROCESS                                */
		/****************************************************************************************/
		case'search_faculty':			
			pageRedirection("manage_faculty.php", 5, "");
			exit();
			break;
		
		/****************************************************************************************/
		/*                            FACULTY ACCOUNT ACTIVE PROCESS                            */
		/****************************************************************************************/
		case'Active':
			$sqlActive['QUERY']    = "UPDATE "._DB_FACULTY_ACCOUNT_." 
								SET `status` = 'A' 
							  WHERE `id` IN (".$_REQUEST['id'].")"; 
			$mycms->sql_update($sqlActive);
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("reviewer_listing.php");
			exit();
			break;
		
		/****************************************************************************************/
		/*                           FACULTY ACCOUNT INACTIVE PROCESS                           */
		/****************************************************************************************/	
		case'Inactive':
			$sqlInactive['QUERY']  = "UPDATE "._DB_FACULTY_ACCOUNT_." 
								SET `status` = 'I' 
							  WHERE `id` IN (".$_REQUEST['id'].")"; 
			$mycms->sql_update($sqlInactive);
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("reviewer_listing.php");
			exit();
			break;
		
		/****************************************************************************************/
		/*                            FACULTY ACCOUNT REMOVE PROCESS                            */
		/****************************************************************************************/	
		case'Remove':
			$sqlRemove['QUERY']       = "UPDATE "._DB_FACULTY_ACCOUNT_." 
								   SET `status` = 'D' 
								 WHERE `id` IN (".$_REQUEST['id'].")"; 
			$mycms->sql_update($sqlRemove);

				$sqlDelete = array();
		  $sqlDelete  = "  DELETE FROM "._DB_ABSTRACT_ALLOTMENT_." 
		                   WHERE `review_user_id` = '".$_REQUEST['id']."'";
		  $mycms->sql_query($sqlDelete);


		 $sqlFetchPrevReviewResult['QUERY']           = "  SELECT * 
		                                FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
		                                 WHERE `faculty_id` = '".$_REQUEST['id']."'";
		$resultFetchPrevReviewResult        = $mycms->sql_select($sqlFetchPrevReviewResult);    
		if($resultFetchPrevReviewResult)
		{
		    foreach($resultFetchPrevReviewResult as $keyPrevReviewResult=>$rowPrevReviewResult)
		    {
		      // MAKE EARLY REVIEW INACTIVE
		      $sqlUpdatePrevReviewResult['QUERY']  = "   UPDATE "._DB_ABSTRACT_REVIEW_RESULT_." 
		                              SET `status` = 'I' 
		                            WHERE `id` = '".$rowPrevReviewResult['id']."'";
		      
		      $mycms->sql_update($sqlUpdatePrevReviewResult);
		      
		      // MAKE EARLY REVIEW DETAILS INACTIVE
		      $sqlUpdatePrevReviewDetails['QUERY'] = "   UPDATE "._DB_ABSTRACT_REVIEW_RESULT_DETAILS_." 
		                              SET `status` = 'I'
		                            WHERE `review_result_id` = '".$rowPrevReviewResult['id']."'";
		                                 
		      $mycms->sql_update($sqlUpdatePrevReviewDetails); 
		    }
		}
		
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Removed successfully!' // dynamic message
	    	];
		    $mycms->redirect("reviewer_listing.php");
			exit();
			break;
		
		/****************************************************************************************/
		/*                            FACULTY ACCOUNT INSERT PROCESS                            */
		/****************************************************************************************/
		case'insert':			
			$faculty_username                     = addslashes(trim($_REQUEST['faculty_username_add']));
			$faculty_password                     = $_REQUEST['faculty_password_add'];
			$faculty_password_encoded             = $mycms->encoded($faculty_password);
			$faculty_access_role                  = addslashes(trim($_REQUEST['faculty_access_role_add']));
			$faculty_title                        = addslashes(trim(strtoupper($_REQUEST['faculty_title_add'])));
			$faculty_first_name                   = addslashes(trim(strtoupper($_REQUEST['faculty_first_name_add'])));
			$faculty_middle_name                  = addslashes(trim(strtoupper($_REQUEST['faculty_middle_name_add'])));
			$faculty_last_name                    = addslashes(trim(strtoupper($_REQUEST['faculty_last_name_add'])));
			$faculty_designation                  = addslashes(trim(strtoupper($_REQUEST['faculty_designation_add'])));
			$faculty_institution_name             = addslashes(trim(strtoupper($_REQUEST['faculty_institution_name_add'])));
			$faculty_specification                = addslashes(trim(strtoupper($_REQUEST['faculty_specification_add'])));
			
			// INSERT FACULTY DETAILS
			$sqlInsertFaculty['QUERY']                     = "INSERT INTO "._DB_FACULTY_ACCOUNT_." 
			                                                 SET `faculty_login_username` = '".$faculty_username."', 
															     `faculty_login_password` = '".$faculty_password_encoded."', 
																 `faculty_access_role_id` = '".$faculty_access_role."', 
																 `faculty_title` = '".$faculty_title."', 
																 `faculty_first_name` = '".$faculty_first_name."', 
																 `faculty_middle_name` = '".$faculty_middle_name."', 
																 `faculty_last_name` = '".$faculty_last_name."', 
															     `faculty_designation` = '".$faculty_designation."', 
																 `faculty_institution_name` = '".$faculty_institution_name."', 
																 `faculty_specification` = '".$faculty_specification."', 
																 `status` = 'A', 
																 `created_by` = '".$loggedUserID."', 
																 `created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
															     `created_sessionId` = '".session_id()."', 
																 `created_dateTime` = '".date('Y-m-d H:i:s')."'";
			$lastInsertedFaculty                  = $mycms->sql_insert($sqlInsertFaculty);
			
			// SETTING FACULTY ACCESS DETAILS
			facultyAccessDetailsProcess($lastInsertedFaculty, "add");
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];
		    $mycms->redirect("reviewer_listing.php");
			exit();
			break;
		
		/****************************************************************************************/
		/*                            FACULTY ACCOUNT UPDATE PROCESS                            */
		/****************************************************************************************/
		case'update':
			$faculty_id                           = addslashes(trim($_REQUEST['faculty_id']));
			$faculty_username                     = addslashes(trim($_REQUEST['faculty_username_edit']));
			$faculty_access_role                  = addslashes(trim($_REQUEST['faculty_access_role_edit']));
			$faculty_title                        = addslashes(trim(strtoupper($_REQUEST['faculty_title_edit'])));
			$faculty_first_name                   = addslashes(trim(strtoupper($_REQUEST['faculty_first_name_edit'])));
			$faculty_middle_name                  = addslashes(trim(strtoupper($_REQUEST['faculty_middle_name_edit'])));
			$faculty_last_name                    = addslashes(trim(strtoupper($_REQUEST['faculty_last_name_edit'])));
			$faculty_designation                  = addslashes(trim(strtoupper($_REQUEST['faculty_designation_edit'])));
			$faculty_institution_name             = addslashes(trim(strtoupper($_REQUEST['faculty_institution_name_edit'])));
			$faculty_specification                = addslashes(trim(strtoupper($_REQUEST['faculty_specification_edit'])));
			$faculty_login_username                =$_REQUEST['faculty_login_username'];
			// echo '<pre>'; print_r($_REQUEST);die;
			
			// UPDATE FACULTY DETAILS
			$sqlUpdateFaculty['QUERY']                     = "UPDATE "._DB_FACULTY_ACCOUNT_." 
																SET `faculty_access_role_id` = '".$faculty_access_role."', 
																	`faculty_title` = '".$faculty_title."', 
																	`faculty_first_name` = '".$faculty_first_name."', 
																	`faculty_middle_name` = '".$faculty_middle_name."', 
																	`faculty_last_name` = '".$faculty_last_name."', 
																	`faculty_designation` = '".$faculty_designation."', 
																	`faculty_institution_name` = '".$faculty_institution_name."', 
																	`faculty_specification` = '".$faculty_specification."', 
																	`faculty_login_username` = '".$faculty_login_username."', 
																	`modified_by` = '".$loggedUserID."', 
																	`modified_ip` = '".$_SERVER['REMOTE_ADDR']."', 
																	`modified_sessionId` = '".session_id()."', 
																	`modified_dateTime` = '".date('Y-m-d H:i:s')."' 
															  WHERE `id` = '".$faculty_id."'";
			$mycms->sql_update($sqlUpdateFaculty);
			
			// RESETTING FACULTY ACCESS DETAILS
			facultyAccessDetailsProcess($faculty_id, "edit");			
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("reviewer_listing.php");
			exit();
			break;
		
		case'allocateAbstract':
			allocateAbstract();
			pageRedirection("manage_faculty.php", 5, "");
			exit();
			break;
	
		case'allocateIndividualAbstract':
			allocateIndividualAbstract();
			exit();
			break;

		case'getMultiSubcat':
			//print_r($_REQUEST['catId']);
			$sqlCategory			  =	array();
			if( strpos($_REQUEST['catId'], ',') !== false ) {
			     //echo "Found";
			     $explodeData = explode(",",$_REQUEST['catId']);
				//print_r($explodeData);
				$array=array_map('intval', explode(',', $_REQUEST['catId']));
				$array = implode("','",$array);
				
				$sqlCategory['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_SUBMISSION_." 
												  WHERE `status` ='A' AND category IN ('".$array."')
											   ORDER BY `id` ASC";
				
				
				
			}
			else
			{

				$sqlCategory['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_SUBMISSION_." 
												  WHERE `status` ='A' AND category='".$_REQUEST['catId']."'
											   ORDER BY `id` ASC";
				//echo "not found";

			}

			//print_r($sqlCategory);

			$resultCategory = $mycms->sql_select($sqlCategory);

			//print_r($resultCategory);
			echo json_encode($resultCategory);
			exit();
			break;	
			case 'insertTopic':
			insertReviewCategory($cfg, $mycms);
			exit();
			break;
			case 'deleteReviewCategory':
			deleteReviewCategory($cfg, $mycms);
			exit();
			break;
			case 'InactivReviewCategory':
			InactivReviewCategory($cfg, $mycms);
			exit();
			break;
			case 'ActiveReviewCategory':
			ActiveReviewCategory($cfg, $mycms);
			exit();
			break;
			case 'updateTopic':
			updateAbstractCategory($cfg, $mycms);
			exit();
			break;
			case 'insertReviewList':
			insertReviewList($cfg, $mycms);
			exit();
			break;	
			case 'updateReviewList':
			updateReviewList($cfg, $mycms);
			exit();
			break;	
			case 'deleteReviewList':
			deleteReviewList($cfg, $mycms);
			exit();
			break;
		
	}
	
	function allocateIndividualAbstract()
	{	
		global $mycms, $cfg;
		
		$reviewerId 							 		= $_REQUEST['reviewerId'];	
		$abstract_id 							 		= $_REQUEST['abstractId'];
		$whatToDo 							 			= $_REQUEST['whatToDo'];
		
		
		$sqlDelete = array();
		$sqlDelete = "  DELETE FROM "._DB_ABSTRACT_ALLOTMENT_." 
										 WHERE `review_user_id` = '".$reviewerId."'
										   AND `abstract_id` = '".$abstract_id."'";
		$mycms->sql_query($sqlDelete); 
		

		if($whatToDo == 'DELETE')
		{
			// FETCHING EARLY REVIEW RESULTS
			$sqlFetchPrevReviewResult['QUERY']           = "  SELECT * 
																FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
															   WHERE `abstract_id` = '".$abstract_id."'
																 AND `faculty_id` = '".$reviewerId."'";
			$resultFetchPrevReviewResult        = $mycms->sql_select($sqlFetchPrevReviewResult);		
			if($resultFetchPrevReviewResult)
			{
				foreach($resultFetchPrevReviewResult as $keyPrevReviewResult=>$rowPrevReviewResult)
				{
					// MAKE EARLY REVIEW INACTIVE
					$sqlUpdatePrevReviewResult['QUERY']  = "   UPDATE "._DB_ABSTRACT_REVIEW_RESULT_." 
																  SET `status` = 'I' 
																WHERE `id` = '".$rowPrevReviewResult['id']."'";
					
					$mycms->sql_update($sqlUpdatePrevReviewResult);
					
					// MAKE EARLY REVIEW DETAILS INACTIVE
					$sqlUpdatePrevReviewDetails['QUERY'] = "   UPDATE "._DB_ABSTRACT_REVIEW_RESULT_DETAILS_." 
																  SET `status` = 'I'
																WHERE `review_result_id` = '".$rowPrevReviewResult['id']."'";
																	   
					$mycms->sql_update($sqlUpdatePrevReviewDetails); 
				}
			}
		}
		
		
		
		
		if($whatToDo == 'INSERT')
		{
			$sqlInsert = array();
			$sqlInsert['QUERY']         =  "   INSERT INTO "._DB_ABSTRACT_ALLOTMENT_." 
													   SET `abstract_id` = '".$abstract_id."' ,
														   `review_user_id` = '".$reviewerId."' ";		
			$mycms->sql_insert($sqlInsert, false);

			$sqlFetchPrevReviewResult['QUERY']           = "  SELECT * 
																FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
															   WHERE `abstract_id` = '".$abstract_id."'
																 AND `faculty_id` = '".$reviewerId."'";
			$resultFetchPrevReviewResult        = $mycms->sql_select($sqlFetchPrevReviewResult);		
			if($resultFetchPrevReviewResult)
			{
				foreach($resultFetchPrevReviewResult as $keyPrevReviewResult=>$rowPrevReviewResult)
				{
					// MAKE EARLY REVIEW INACTIVE
					$sqlUpdatePrevReviewResult['QUERY']  = "   UPDATE "._DB_ABSTRACT_REVIEW_RESULT_." 
																  SET `status` = 'A' 
																WHERE `id` = '".$rowPrevReviewResult['id']."'";
					
					$mycms->sql_update($sqlUpdatePrevReviewResult);
					
					// MAKE EARLY REVIEW DETAILS INACTIVE
					$sqlUpdatePrevReviewDetails['QUERY'] = "   UPDATE "._DB_ABSTRACT_REVIEW_RESULT_DETAILS_." 
																  SET `status` = 'A'
																WHERE `review_result_id` = '".$rowPrevReviewResult['id']."'";
																	   
					$mycms->sql_update($sqlUpdatePrevReviewDetails); 
				}
			}

		}		
	}
	
	function allocateAbstract()
	{	
		global $mycms, $cfg;
		
		$reviewerId 							 		= $_REQUEST['reviewerId'];	
		$abstract_ids 							 		= $_REQUEST['abstractId'];
		
		
		$sqlDelete = array();
		$sqlDelete  = "  DELETE FROM "._DB_ABSTRACT_ALLOTMENT_." 
										 WHERE `review_user_id` = '".$reviewerId."'";
		$mycms->sql_query($sqlDelete); 
		
		// FETCHING EARLY REVIEW RESULTS
		$sqlFetchPrevReviewResult['QUERY']           = "  SELECT * 
															FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
														   WHERE `abstract_id` = '".$abstract_id."'
															 AND `faculty_id` = '".$reviewerId."'";
		$resultFetchPrevReviewResult        = $mycms->sql_select($sqlFetchPrevReviewResult);		
		if($resultFetchPrevReviewResult)
		{
			foreach($resultFetchPrevReviewResult as $keyPrevReviewResult=>$rowPrevReviewResult)
			{
				// MAKE EARLY REVIEW INACTIVE
				$sqlUpdatePrevReviewResult['QUERY']  = "   UPDATE "._DB_ABSTRACT_REVIEW_RESULT_." 
															  SET `status` = 'I' 
															WHERE `id` = '".$rowPrevReviewResult['id']."'";
				
				$mycms->sql_update($sqlUpdatePrevReviewResult);
				
				// MAKE EARLY REVIEW DETAILS INACTIVE
				$sqlUpdatePrevReviewDetails['QUERY'] = "   UPDATE "._DB_ABSTRACT_REVIEW_RESULT_DETAILS_." 
															  SET `status` = 'I'
															WHERE `review_result_id` = '".$rowPrevReviewResult['id']."'";
																   
				$mycms->sql_update($sqlUpdatePrevReviewDetails); 
			}
		}
		
		foreach($abstract_ids as $kkl=>$abstract_id)
		{
			$sqlInsert = array();
			$sqlInsert['QUERY']         =  "   INSERT INTO "._DB_ABSTRACT_ALLOTMENT_." 
													   SET `abstract_id` = '".$abstract_id."' ,
														   `review_user_id` = '".$reviewerId."' ";		
			$mycms->sql_insert($sqlInsert, false);
		}		
	}
	
	/******************************************************************************/
	/*                                UTILITY METHOD                              */
	/******************************************************************************/
	function pageRedirection($fileName, $messageCode, $additionalString="")
	{
		global $mycms, $cfg;
		
		$pageKey                       		       = "_pgn1_";
		$pageKeyVal                    		       = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString                 		       = "";
		$searchArray                   		       = array();
		
		$searchArray[$pageKey]         		       = $pageKeyVal;
		$searchArray['src_faculty_username']       = trim($_REQUEST['src_faculty_username']);
		$searchArray['src_faculty_name']           = trim($_REQUEST['src_faculty_name']);
		$searchArray['src_faculty_access_role']    = trim($_REQUEST['src_faculty_access_role']);
		
		foreach($searchArray as $searchKey=>$searchVal)
		{
			if($searchVal!="")
			{
				$searchString .= "&".$searchKey."=".$searchVal;
			}
		}
		
		$mycms->redirect($fileName."?m=".$messageCode.$additionalString.$searchString);
	}
	
	/******************************************************************************/
	/*                         FACULTY ACCESS DETAILS PROCESS                     */
	/******************************************************************************/
	function facultyAccessDetailsProcess($facultyId, $formType)
	{
		global $cfg, $mycms;
		
		$loggedUserID                         = $mycms->getLoggedUserId();
		$faculty_access_role                  = addslashes(trim($_REQUEST['faculty_access_role_'.$formType]));
		
		// DELETING EXISTING FACULTY ACCESS DETAILS
		$sqlDeleteAccessDetails = "DELETE FROM "._DB_FACULTY_ACCESS_DETAILS_." 
													   WHERE `faculty_id` = '".$facultyId."'
														 AND `web_domain_id` = '2'";
		
		$mycms->sql_query($sqlDeleteAccessDetails); 
		
		// INSERTING FACULTY ACCESS DETAILS
		$sqlFetchAccessRoleDetails['QUERY']            = "SELECT accessRoleDetails.* 
												   
												   FROM "._DB_FACULTY_ACCESS_ROLE_." accessRole 
												   
											 INNER JOIN "._DB_FACULTY_ACCESS_ROLE_DETAILS_." accessRoleDetails 
													 ON accessRole.id = accessRoleDetails.faculty_role_id 
												  
												  WHERE accessRole.status = 'A' 
													AND accessRoleDetails.status = 'A' 
													AND accessRoleDetails.web_domain_id = '2' 
													AND accessRole.id = '".$faculty_access_role."' 
													AND accessRoleDetails.faculty_role_id = '".$faculty_access_role."'";
													
		$resultAccessRoleDetails              = $mycms->sql_select($sqlFetchAccessRoleDetails);
		
		if($resultAccessRoleDetails)
		{
			foreach($resultAccessRoleDetails as $key=>$rowAccessRoleDetails)
			{
				$sqlInsertRoleDetails['QUERY']         = "INSERT INTO "._DB_FACULTY_ACCESS_DETAILS_." 
														 SET `faculty_id` = '".$facultyId."', 
															 `web_page_id` = '".$rowAccessRoleDetails['web_page_id']."', 
															 `web_domain_id` = '".$rowAccessRoleDetails['web_domain_id']."', 
															 `status` = 'A', 
															 `created_by` = '".$loggedUserID."', 
															 `created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
															 `created_sessionId` = '".session_id()."', 
															 `created_dateTime` = '".date('Y-m-d H:i:s')."'";
															 
				$mycms->sql_insert($sqlInsertRoleDetails);
			}
		}
	}
	function insertReviewCategory($cfg, $mycms){
		$loggedUserId		= $mycms->getLoggedUserId();
		
		$category = (!empty(addslashes(trim($_POST['category']))))?	addslashes(trim($_POST['category'])) : '';
				$sqlInsertCoAuthor = array();
				$sqlInsertCoAuthor['QUERY']   = "INSERT INTO "._DB_ABSTRACT_REVIEW_SCORE_." 
												 	SET 
												 		`category` = '".$category."',
												 		`score` = '".addslashes(trim($_POST['score']))."',
														`status` = '".trim($_POST['status'])."',
														`created_by` = '".$loggedUserId."', 
														`created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
														`created_sessionId` = '".session_id()."', 
														`created_dateTime` = '".date('Y-m-d H:i:s')."',
														`modified_by` = '".$loggedUserId."',
														`modified_ip` = '".$_SERVER['REMOTE_ADDR']."',
														`modified_sessionId` = '".session_id()."',
														`modified_dateTime` = '".date('Y-m-d H:i:s')."'";
			$awardRequestId = $mycms->sql_insert($sqlInsertCoAuthor, false);
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];
		    $mycms->redirect("score_board.php");
			exit();
		// }

	}
	function deleteReviewCategory($cfg, $mycms)
	{
		$loggedUserId		= $mycms->getLoggedUserId();
		$modified_by = trim($_REQUEST['modified_by']);
		$category_id = trim($_REQUEST['ID']);
		if( isset($category_id) && !empty($category_id) ){
			$sqlUpdateRecord 			=	array();
			$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_REVIEW_SCORE_."
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";
									 
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' =>$loggedUserId?$loggedUserId:$modified_by,  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' =>$_SERVER['REMOTE_ADDR'],   'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' =>session_id(),  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' =>$category_id,   			 'TYP' => 's');								   
			$mycms->sql_update($sqlUpdateRecord);
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Deleted successfully!' // dynamic message
	    	];
		    $mycms->redirect("score_board.php");
			exit();
		}
		else{
			/*$mycms->redirect("review_score.category.php?show=editTopic&ID=<?=$category_id?>&m=Unable To Delete The Category.");
			exit();*/
		}
	}
	function InactivReviewCategory($cfg, $mycms)
	{
		$loggedUserId		= $mycms->getLoggedUserId();
		$modified_by = trim($_REQUEST['modified_by']);
		$category_id = trim($_REQUEST['id']);
		if( isset($category_id) && !empty($category_id) ){
			$sqlUpdateRecord 			=	array();
			$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_REVIEW_SCORE_."
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";
									 
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'I',  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' =>$loggedUserId?$loggedUserId:$modified_by,  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' =>$_SERVER['REMOTE_ADDR'],   'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' =>session_id(),  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' =>$category_id,   			 'TYP' => 's');								   
			$mycms->sql_update($sqlUpdateRecord);
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("score_board.php");
			exit();
		}
		else{
			/*$mycms->redirect("review_score.category.php?show=editTopic&ID=<?=$category_id?>&m=Unable To Delete The Category.");
			exit();*/
		}
	}
	function ActiveReviewCategory($cfg, $mycms)
	{
		$loggedUserId		= $mycms->getLoggedUserId();
		$modified_by = trim($_REQUEST['modified_by']);
		$category_id = trim($_REQUEST['id']);
		if( isset($category_id) && !empty($category_id) ){
			$sqlUpdateRecord 			=	array();
			$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_REVIEW_SCORE_."
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";
									 
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' =>$loggedUserId?$loggedUserId:$modified_by,  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' =>$_SERVER['REMOTE_ADDR'],   'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' =>session_id(),  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' =>$category_id,   			 'TYP' => 's');								   
			$mycms->sql_update($sqlUpdateRecord);
			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("score_board.php");
			exit();
		}
		else{
			/*$mycms->redirect("review_score.category.php?show=editTopic&ID=<?=$category_id?>&m=Unable To Delete The Category.");
			exit();*/
		}
	}
	function updateAbstractCategory($cfg, $mycms){
		$loggedUserId		= $mycms->getLoggedUserId();
		
		$abstract_topic_id = trim($_REQUEST['topic_id']);

		$category = (!empty(addslashes(trim($_POST['category']))))?	addslashes(trim($_POST['category'])) : '';

		$modified_by = trim($_REQUEST['modified_by']);
		if( isset($abstract_topic_id) && !empty($abstract_topic_id) ){
			$sqlUpdateRecord 			=	array();
			$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_REVIEW_SCORE_."
											   SET 
												   `category` = ?,
												    `score` = ?,
												   `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";
									 
			/*$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'abstract_topic',  'DATA' => addslashes(trim($_REQUEST['topicname'])),  	 'TYP' => 's');	*/
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category',  'DATA' => $category,  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'score',  'DATA' => $_REQUEST['score'],  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => trim($_REQUEST['status']),  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId?$loggedUserId:$modified_by,  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' =>$_SERVER['REMOTE_ADDR'],   'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' =>session_id(),  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' =>$abstract_topic_id,   			 'TYP' => 's');								   
		
			$mycms->sql_update($sqlUpdateRecord);

			$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("score_board.php");
			exit();
		}
		else{
			 $mycms->redirect("score_board.php");
			exit();
		}
	}
	function insertReviewList($cfg, $mycms){
		$loggedUserId		= $mycms->getLoggedUserId();
		
		//echo '<pre>'; print_r($_REQUEST);
		//echo json_encode($_REQUEST['review_category_id']);
				$sqlInsertCoAuthor = array();
				$sqlInsertCoAuthor['QUERY']   = "INSERT INTO "._DB_ABSTRACT_REVIEW_LIST_." 
												 	SET 
												 		`review_category_id` = '".json_encode($_REQUEST['review_category_id'])."',
												 		`category_id` = '".json_encode($_REQUEST['category_id'])."',
												 		`sub_category_id` = '".json_encode($_REQUEST['sub_category_id'])."',
												 		`abstract_name` = '".addslashes(trim($_POST['abstract_name']))."',
												 		`score_option` = '".addslashes(trim($_POST['score_option']))."',
														`full_marks` = '".addslashes(trim($_POST['full_marks']))."',
														`created_by` = '".$loggedUserId."', 
														`created_ip` = '".$_SERVER['REMOTE_ADDR']."', 
														`created_sessionId` = '".session_id()."', 
														`created_dateTime` = '".date('Y-m-d H:i:s')."',
														`modified_by` = '".$loggedUserId."',
														`modified_ip` = '".$_SERVER['REMOTE_ADDR']."',
														`modified_sessionId` = '".session_id()."',
														`modified_dateTime` = '".date('Y-m-d H:i:s')."'";
			$awardRequestId = $mycms->sql_insert($sqlInsertCoAuthor, false);
             $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];
		    $mycms->redirect("score_board.php");			
			exit();
		// }

	

	}
	function updateReviewList($cfg, $mycms){
		$loggedUserId		= $mycms->getLoggedUserId();

		//echo '<pre>'; print_r($_REQUEST['review_category_id']); die();
		
		$abstract_topic_id = trim($_REQUEST['topic_id']);
		//echo $abstract_topic_id;

		$modified_by = trim($_REQUEST['modified_by']);
		if( isset($abstract_topic_id) && !empty($abstract_topic_id) ){
			$sqlUpdateRecord 			=	array();
			$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_REVIEW_LIST_."
											   SET 
												   `review_category_id` = ?,
												   `category_id` = ?,
											 	   `sub_category_id` = ?,
												   `abstract_name` = ?,
												   `score_option` = ?,
												   `full_marks` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";
									 
			/*$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'abstract_topic',  'DATA' => addslashes(trim($_REQUEST['topicname'])),  	 'TYP' => 's');	*/
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category',  'DATA' => json_encode($_REQUEST['review_category_id']),  	 'TYP' => 's');

			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category_id',  'DATA' => json_encode($_REQUEST['category_id']),  	 'TYP' => 's');

			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'sub_category_id',  'DATA' => json_encode($_REQUEST['sub_category_id']),  	 'TYP' => 's');


			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'abstract_name',  'DATA' => trim($_REQUEST['abstract_name']),  	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'score_option',  'DATA' => trim($_REQUEST['score_option']),  	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'full_marks',  'DATA' => trim($_REQUEST['full_marks']),  	 'TYP' => 's');

			
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId?$loggedUserId:$modified_by,  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' =>$_SERVER['REMOTE_ADDR'],   'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' =>session_id(),  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' =>$abstract_topic_id,   			 'TYP' => 's');								   
			$mycms->sql_update($sqlUpdateRecord);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("score_board.php");		
			exit();
		}
		else{
		    $mycms->redirect("score_board.php");		
			exit();
		}
	}
	function deleteReviewList($cfg, $mycms)
	{
		$loggedUserId		= $mycms->getLoggedUserId();
		$modified_by = trim($_REQUEST['modified_by']);
		$category_id = trim($_REQUEST['ID']);
		if( isset($category_id) && !empty($category_id) ){
			$sqlUpdateRecord 			=	array();
			$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_REVIEW_LIST_."
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";
									 
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' =>$loggedUserId?$loggedUserId:$modified_by,  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' =>$_SERVER['REMOTE_ADDR'],   'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' =>session_id(),  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' =>$category_id,   			 'TYP' => 's');								   
			$mycms->sql_update($sqlUpdateRecord);
			 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Deleted successfully!' // dynamic message
	    	];
		    $mycms->redirect("score_board.php");		
			exit();
		}
		else{
			/*$mycms->redirect("review_score.category.php?show=editTopic&ID=<?=$category_id?>&m=Unable To Delete The Category.");
			exit();*/
		}
	}
?>