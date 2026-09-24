<?php
include_once('includes/init.php');
$act = @$_REQUEST['act'];

switch ($act) {
    case 'insertTopic':
        insertAbstractTopic($cfg, $mycms);
        exit();
	break;
	case 'deleteTopic':
        deleteAbstractTopic($cfg, $mycms);
        exit();
    break;
    case 'Inactive':
        InactiveAbstractTopic($cfg, $mycms);
        exit();
    break;
    case 'Active':
         ActiveAbstractTopic($cfg, $mycms);
        exit();
    break;
	case 'updateTopic':
		updateAbstractTopic($cfg, $mycms);
		exit();
	break;
	case 'insertCategory':
		insertAbstractCategory($cfg, $mycms);
		exit();
	case 'updateCategory':
		updateAbstractCategory($cfg, $mycms);
		exit();
		break;
	case 'insertSubmission':
		insertAbstractSubmission($cfg, $mycms);
		exit();
		break;
	case 'updateSubmission':
		updateAbstractSubmission($cfg, $mycms);
		exit();
		break;
	case 'deleteSubmission':
		deleteAbstractSubmission($cfg, $mycms);
		exit();
		break;
    case 'InactivesubCat':
		InactivesubCat($cfg, $mycms);
		exit();
		break;
	 case 'ActivesubCat':
		ActivesubCat($cfg, $mycms);
		exit();
		break;
	case 'deleteCategory':
		deleteAbstractCategory($cfg, $mycms);
		exit();
		break;
    case 'InactiveCat':
		InactiveCat($cfg, $mycms);
		exit();
		break;
	 case 'ActiveCat':
		ActiveCat($cfg, $mycms);
		exit();
		break;
	case 'insertPresentation':
		insertAbstractPresentation($cfg, $mycms);
		exit();
		break;
	case 'updatePresentation':
		updateAbstractPresentation($cfg, $mycms);
		exit();
		break;
	case 'deletePresentation':
		deleteAbstractPresentation($cfg, $mycms);
		exit();
		break;
    case 'InactivePresentation':
		InactivePresentation($cfg, $mycms);
		exit();
		break;
	 case 'ActivePresentation':
		ActivePresentation($cfg, $mycms);
		exit();
		break;
     case 'updateFields':
			upsateAbstractFields($cfg, $mycms);
			break;
     case 'InactiveField':
			InactiveField($cfg, $mycms);
			break;
	 case 'ActiveField':
			ActiveField($cfg, $mycms);
			break;
}


function insertAbstractTopic($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	if ((isset($_POST['topicname']) && !empty($_POST['topicname']))  && (isset($_POST['status']) && !empty($_POST['status']))) {
		// INSERT PROCESS
		$sub_category = !empty($_POST['sub_category_id']) ? $_POST['sub_category'] : '0';
		$sub_sub_cat_id = !empty($_POST['sub_sub_category']) ? $_POST['sub_sub_category'] : '0';
		$categoryIds = $_REQUEST['category'];
		$subCategoryIds = $_REQUEST['sub_category_id'];
		foreach ($categoryIds as $id) {
			if (!empty($subCategoryIds)) {
				// to Get all Sub Category of this Category
				$sqlSubCatList = array();
				$sqlSubCatList['QUERY'] = "SELECT `id` FROM " . _DB_ABSTRACT_SUBMISSION_ . " WHERE `category`=" . $id . " AND `status`='A' ";
				$resultSubCatList = $mycms->sql_select($sqlSubCatList);
				$allSubCategoryArr = array();
				foreach ($resultSubCatList as $key => $value) {
					array_push($allSubCategoryArr, $value['id']);
				}
				// echo "<pre>";print_r($allSubCategoryArr);
				// echo "</pre><pre>";print_r($subCategoryIds);die;

				foreach ($subCategoryIds as $subCatId) {

					//to check among all inputSubCatId which belongs to present category's subCategory
					if (in_array($subCatId, $allSubCategoryArr)) {
						$sqlInsertTopic = array();
						$sqlInsertTopic['QUERY']   = "INSERT INTO " . _DB_ABSTRACT_TOPIC_ . " 
												 	SET `abstract_topic` = '" . addslashes(trim($_POST['topicname'])) . "',
												 		`category` = '" . $id . "',
												 		`sub_category` = '" . $subCatId . "',
												 		`sub_sub_category` = '" . addslashes(trim($sub_sub_cat_id)) . "',
														`status` = '" . trim($_POST['status']) . "',
														`created_by` = '" . $loggedUserId . "', 
														`created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														`created_sessionId` = '" . session_id() . "', 
														`created_dateTime` = '" . date('Y-m-d H:i:s') . "',
														`modified_by` = '" . $loggedUserId . "',
														`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
														`modified_sessionId` = '" . session_id() . "',
														`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'";
						$awardRequestId = $mycms->sql_insert($sqlInsertTopic, false);
					}
				}
			} //if 
			else {
				$sqlInsertTopic = array();
				$sqlInsertTopic['QUERY']   = "INSERT INTO " . _DB_ABSTRACT_TOPIC_ . " 
												 	SET `abstract_topic` = '" . addslashes(trim($_POST['topicname'])) . "',
													 `category` = '" . $id . "',
												 		
												 		
												 		`sub_sub_category` = '" . addslashes(trim($sub_sub_cat_id)) . "',
														`status` = '" . trim($_POST['status']) . "',
														`created_by` = '" . $loggedUserId . "', 
														`created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														`created_sessionId` = '" . session_id() . "', 
														`created_dateTime` = '" . date('Y-m-d H:i:s') . "',
														`modified_by` = '" . $loggedUserId . "',
														`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
														`modified_sessionId` = '" . session_id() . "',
														`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'";
				$awardRequestId = $mycms->sql_insert($sqlInsertTopic, false);
			}
		}
         $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		$mycms->redirect("abstract_master.php#abstopic");
		exit();
	}
}


function deleteAbstractTopic($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$abstract_topic_id = trim($_REQUEST['ID']);
  
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_TOPIC_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		$mycms->redirect("abstract_master.php#abstopic");
		exit();
	} else {
         $mycms->redirect("abstract_master.php#abstopic");
		exit();
	}

}

function InactiveAbstractTopic($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = $loggedUserId;
	$abstract_topic_id = trim($_REQUEST['id']);
  
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_TOPIC_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'I',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		$mycms->redirect("abstract_master.php#abstopic");
		exit();
	} else {
         $mycms->redirect("abstract_master.php#abstopic");
		exit();
	}
}


function ActiveAbstractTopic($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = $loggedUserId;
	$abstract_topic_id = trim($_REQUEST['id']);

	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_TOPIC_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];

		$mycms->redirect("abstract_master.php#abstopic");
		exit();
	} else {
         $mycms->redirect("abstract_master.php#abstopic");
		exit();
	}
}


function updateAbstractTopic($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();

	$abstract_topic_id = trim($_REQUEST['topic_id']);
	$modified_by = trim($_REQUEST['modified_by']);

	$sub_category = !empty($_POST['sub_category']) ? $_POST['sub_category'] : '0';
	$sub_sub_cat_id = !empty($_POST['sub_sub_category']) ? $_POST['sub_sub_category'] : '0';
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_TOPIC_ . "
											   SET `abstract_topic` = ?, 
												   
												   `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'abstract_topic',  'DATA' => addslashes(trim($_REQUEST['topicname'])),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => trim($_REQUEST['status']),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		
		$mycms->sql_update($sqlUpdateRecord);
		
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Topic Updated successfully!' // dynamic message
	    	];
		$mycms->redirect("abstract_master.php#abstopic");
		exit();
	} else {
		$mycms->redirect("abstract_master.php#abstopic");
		exit();
	}
}

function insertAbstractCategory($cfg, $mycms){
		$loggedUserId		= $mycms->getLoggedUserId();
		// if( (isset($_POST['topicname']) && !empty($_POST['topicname']))  && (isset($_POST['status']) && !empty($_POST['status'])) ){
				// INSERT PROCESS
		$category_fields_input = isset($_POST['category_fields']) ? $_POST['category_fields'] : '';
		$fields_array = explode(',', $category_fields_input);
		
				 $upload_doc = isset($_POST['doc_type_cat']) ? 'yes' : 'no';
				$sqlInsertCoAuthor = array();
				$sqlInsertCoAuthor['QUERY']   = "INSERT INTO "._DB_ABSTRACT_TOPIC_CATEGORY_." 
												 	SET 
												 		`category` = '".addslashes(trim($_POST['category']))."',
												 		`category_fields` = '".json_encode($fields_array)."',
														`doc_upload` =   '".$upload_doc."',
														`suporting_document_name` ='".$_REQUEST['doc_type_cat']."',
														`suporting_document_type` = '".json_encode($_POST['category_files'])."',
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
			'message' => 'Data Submitted successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abscategory");
			exit();
		// }

	}



	function updateAbstractCategory($cfg, $mycms){
		$loggedUserId		= $mycms->getLoggedUserId();
		
		$abstract_topic_id = trim($_REQUEST['topic_id']);
		$category_fields_input = isset($_POST['category_fields']) ? $_POST['category_fields'] : '';
		$fields_array = explode(',', $category_fields_input);
		
        $upload_doc = isset($_POST['upload_cat_doc']) ? 'yes' : 'no';
		$modified_by = trim($_REQUEST['modified_by']);
		if( isset($abstract_topic_id) && !empty($abstract_topic_id) ){
			$sqlUpdateRecord 			=	array();
			$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_TOPIC_CATEGORY_."
											   SET 
												   `category` = ?,
												   `category_fields` = ?,
												   `doc_upload`   =     ?,
													`suporting_document_name`= ?,
													`suporting_document_type`=  ?,
												   `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";
									 
			/*$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'abstract_topic',  'DATA' => addslashes(trim($_REQUEST['topicname'])),  	 'TYP' => 's');	*/
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category',  'DATA' => trim($_REQUEST['category']),  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category_fields',  'DATA' => json_encode($fields_array),  	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][] =	array('FILD' => 'doc_upload',    	 'DATA' => $upload_doc, 		 'TYP' => 's');
	        $sqlUpdateRecord['PARAM'][] =	array('FILD' => 'suporting_document_name',    	 'DATA' =>$_REQUEST['doc_type_cat'], 		 'TYP' => 's');
	        $sqlUpdateRecord['PARAM'][] =	array('FILD' => 'suporting_document_type',    	 'DATA' => json_encode($_REQUEST['category_files']), 		 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => trim($_REQUEST['status']),  	 'TYP' => 's');	
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId?$loggedUserId:$modified_by,  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' =>$_SERVER['REMOTE_ADDR'],   'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' =>session_id(),  			 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
			$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' =>$abstract_topic_id,   			 'TYP' => 's');								   
				
			$mycms->sql_update($sqlUpdateRecord);
		
            $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Submitted successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abscategory");
			exit();			
		}
		else{
		    $mycms->redirect("abstract_master.php#abscategory");
			exit();
		}
	}

function insertAbstractSubmission($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$category_fields_input = isset($_POST['category_fields']) ? $_POST['category_fields'] : '';
		$fields_array = explode(',', $category_fields_input);
		
	if ((isset($_REQUEST['topicname']) && !empty($_REQUEST['topicname']))  && (isset($_REQUEST['status']))) {
		// INSERT PROCESS
		$sqlInsertCoAuthor = array();
		$sqlInsertCoAuthor['QUERY']   = "INSERT INTO " . _DB_ABSTRACT_SUBMISSION_ . " 
												 	SET `abstract_submission` = '" . addslashes(trim($_REQUEST['topicname'])) . "',
												 		`category` = '" . addslashes(trim($_REQUEST['category'])) . "',
														`category_fields` = '".json_encode($fields_array)."',
														`description` = '" . addslashes(trim($_REQUEST['description'])) . "',
														`status` = '" . trim($_REQUEST['status']) . "',
														`created_by` = '" . $loggedUserId . "', 
														`created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														`created_sessionId` = '" . session_id() . "', 
														`created_dateTime` = '" . date('Y-m-d H:i:s') . "',
														`modified_by` = '" . $loggedUserId . "',
														`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
														`modified_sessionId` = '" . session_id() . "',
														`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'";
		$awardRequestId = $mycms->sql_insert($sqlInsertCoAuthor, false);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Submitted successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	}
}

function insertAbstractPresentation($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$category_fields_input = isset($_POST['category_fields']) ? $_POST['category_fields'] : '';
		$fields_array = explode(',', $category_fields_input);
		
	if ((isset($_REQUEST['topicname']) && !empty($_REQUEST['topicname']))  && (isset($_REQUEST['status']) && !empty($_REQUEST['status']))) {
		// INSERT PROCESS

		$explodeCat = explode("-", trim($_POST['category']));
		//print_r($explodeCat);
		$category_id = $explodeCat[0];
		$submission_id = $explodeCat[1];

		$sqlInsertCoAuthor = array();
		$sqlInsertCoAuthor['QUERY']   = "INSERT INTO " . _DB_ABSTRACT_PRESENTATION_ . " 
												 	SET `abstract_presentation` = '" . addslashes(trim($_REQUEST['topicname'])) . "',
												 		`category_id` = '" . addslashes(trim($category_id)) . "',
														`category_fields` = '".json_encode($fields_array)."',
												 		`submission_id` = '" . addslashes(trim($submission_id)) . "',
														`description` = '" . addslashes(trim($_REQUEST['description'])) . "',
														`status` = '" . trim($_REQUEST['status']) . "',
														`created_by` = '" . $loggedUserId . "', 
														`created_ip` = '" . $_SERVER['REMOTE_ADDR'] . "', 
														`created_sessionId` = '" . session_id() . "', 
														`created_dateTime` = '" . date('Y-m-d H:i:s') . "',
														`modified_by` = '" . $loggedUserId . "',
														`modified_ip` = '" . $_SERVER['REMOTE_ADDR'] . "',
														`modified_sessionId` = '" . session_id() . "',
														`modified_dateTime` = '" . date('Y-m-d H:i:s') . "'";
		$awardRequestId = $mycms->sql_insert($sqlInsertCoAuthor, false);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Submitted successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	}
}

function updateAbstractSubmission($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();

	$abstract_topic_id = trim($_REQUEST['topic_id']);
	$modified_by = trim($_REQUEST['modified_by']);
	$category_fields_input = isset($_POST['category_fields']) ? $_POST['category_fields'] : '';
		$fields_array = explode(',', $category_fields_input);
		
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_SUBMISSION_ . "
											   SET `abstract_submission` = ?, 
												   `category` = ?,
												   `category_fields` = ?,
												   `description` =?,
												   `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'abstract_topic',  'DATA' => addslashes(trim($_REQUEST['topicname'])),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category',  'DATA' => trim($_REQUEST['category']),  	 'TYP' => 's');
	    $sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category_fields',  'DATA' => json_encode($fields_array),  	 'TYP' => 's');
	    $sqlUpdateRecord['PARAM'][]   = array('FILD' => 'description',  'DATA' => trim($_REQUEST['description']),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => trim($_REQUEST['status']),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	} else {
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	}
}

function updateAbstractPresentation($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();

	$abstract_topic_id = trim($_REQUEST['topic_id']);
	$explodeCat = explode("-", trim($_REQUEST['category']));
	$category_id = $explodeCat[0];
	$submission_id = $explodeCat[1];
        $category_fields_input = isset($_POST['category_fields']) ? $_POST['category_fields'] : '';
		$fields_array = explode(',', $category_fields_input);
		
	$modified_by = trim($_REQUEST['modified_by']);
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_PRESENTATION_ . "
											   SET `abstract_presentation` = ?, 
												   `category_id` = ?,
												   `submission_id` = ?,
												   `category_fields` = ?,
												   `description` =?,
												   `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'abstract_topic',  'DATA' => addslashes(trim($_REQUEST['topicname'])),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category_id',  'DATA' => trim($category_id),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'submission_id',  'DATA' => trim($submission_id),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category_fields',  'DATA' => json_encode($fields_array),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'description',  'DATA' => trim($_REQUEST['description']),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => trim($_REQUEST['status']),  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	} else {
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	}
}

function deleteAbstractSubmission($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$abstract_topic_id = trim($_REQUEST['ID']);
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_SUBMISSION_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	} else {
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	}
}

function InactivesubCat($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$abstract_topic_id = trim($_REQUEST['id']);
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_SUBMISSION_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'I',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	} else {
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	}
}
function ActivesubCat($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$abstract_topic_id = trim($_REQUEST['id']);
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_SUBMISSION_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status Updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	} else {
		    $mycms->redirect("abstract_master.php#abssubmission");
		exit();
	}
}
function deleteAbstractPresentation($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$abstract_topic_id = trim($_REQUEST['ID']);
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_PRESENTATION_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	} else {
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	}
}
function InactivePresentation($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$abstract_topic_id = trim($_REQUEST['id']);
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_PRESENTATION_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'I',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	} else {
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	}
}
function ActivePresentation($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$abstract_topic_id = trim($_REQUEST['id']);
	if (isset($abstract_topic_id) && !empty($abstract_topic_id)) {
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE " . _DB_ABSTRACT_PRESENTATION_ . "
											   SET `status` = ?,
												   `modified_by` = ?,
												   `modified_ip` = ?,
												   `modified_sessionId` = ?,
												   `modified_dateTime` = ?
											 WHERE `id` = ?";

		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId ? $loggedUserId : $modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' => $_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' => session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' => date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' => $abstract_topic_id,   			 'TYP' => 's');
		$mycms->sql_update($sqlUpdateRecord);
		$_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Status updated successfully!' // dynamic message
	    	];
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	} else {
		    $mycms->redirect("abstract_master.php#abspresentation");
		exit();
	}
}

function deleteAbstractCategory($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$category_id = trim($_REQUEST['ID']);
	if( isset($category_id) && !empty($category_id) ){
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_TOPIC_CATEGORY_."
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
		    $mycms->redirect("abstract_master.php#abscategory");
		exit();
	}
	else{
       $mycms->redirect("abstract_master.php#abscategory");
 		exit();
	}
}
function InactiveCat($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$category_id = trim($_REQUEST['id']);
	if( isset($category_id) && !empty($category_id) ){
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_TOPIC_CATEGORY_."
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
		    $mycms->redirect("abstract_master.php#abscategory");
		exit();
	}
	else{
       $mycms->redirect("abstract_master.php#abscategory");
 		exit();
	}
}
function ActiveCat($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$category_id = trim($_REQUEST['id']);
	if( isset($category_id) && !empty($category_id) ){
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_TOPIC_CATEGORY_."
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
		    $mycms->redirect("abstract_master.php#abscategory");
		exit();
	}
	else{
       $mycms->redirect("abstract_master.php#abscategory");
 		exit();
	}
}

function upsateAbstractFields($cfg, $mycms){
	$loggedUserId		= $mycms->getLoggedUserId();
	
	$fields_id = trim($_REQUEST['fields_id']);
	//echo $abstract_topic_id;

	$modified_by = trim($_REQUEST['modified_by']);
	if( isset($fields_id) && !empty($fields_id) ){
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_FIELDS_."
											SET 
												`display_name` = ?,
												`status` = ?,
												`modified_by` = ?,
												`modified_ip` = ?,
												`modified_sessionId` = ?,
												`modified_dateTime` = ?
											WHERE `id` = ?";
									
		/*$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'abstract_topic',  'DATA' => addslashes(trim($_REQUEST['topicname'])),  	 'TYP' => 's');	*/
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'category',  'DATA' => trim($_REQUEST['display_name']),  	 'TYP' => 's');	
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'status',  'DATA' => trim($_REQUEST['status']),  	 'TYP' => 's');	
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_by',            		 	'DATA' => $loggedUserId?$loggedUserId:$modified_by,  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_ip',             		 	'DATA' =>$_SERVER['REMOTE_ADDR'],   'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_sessionId',            	'DATA' =>session_id(),  			 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'modified_dateTime',             	'DATA' =>date('Y-m-d H:i:s'),   	 'TYP' => 's');
		$sqlUpdateRecord['PARAM'][]   = array('FILD' => 'id',             				 	'DATA' =>$fields_id,   			 'TYP' => 's');								   
		$mycms->sql_update($sqlUpdateRecord);
			$_SESSION['toaster'] = [
		'type' => 'success', // 'success' or 'error'
		'message' => 'Data Updated successfully!' // dynamic message
		];
		$mycms->redirect("abstract_master.php#absfields");
		exit();
	}
	else{
		$mycms->redirect("abstract_master.php#absfields");
		exit();
	}
}
function InactiveField($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$category_id = trim($_REQUEST['id']);
	if( isset($category_id) && !empty($category_id) ){
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_FIELDS_."
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
		    $mycms->redirect("abstract_master.php#absfields");
		exit();
	}
	else{
       $mycms->redirect("abstract_master.php#absfields");
 		exit();
	}
}
function ActiveField($cfg, $mycms)
{
	$loggedUserId		= $mycms->getLoggedUserId();
	$modified_by = trim($_REQUEST['modified_by']);
	$category_id = trim($_REQUEST['id']);
	if( isset($category_id) && !empty($category_id) ){
		$sqlUpdateRecord 			=	array();
		$sqlUpdateRecord['QUERY']	 = "UPDATE "._DB_ABSTRACT_FIELDS_."
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
		    $mycms->redirect("abstract_master.php#absfields");
		exit();
	}
	else{
       $mycms->redirect("abstract_master.php#absfields");
 		exit();
	}
}
