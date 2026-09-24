<?
include_once('includes/init.php');
$action = $_REQUEST['act'];
switch($action){
	case 'add':
	    $sql1 =array();
		$sql1['QUERY'] = "INSERT INTO "._DB_PROGRAM_SCHEDULE_TOPIC_."
					SET `topic_title` 			= '".addslashes($_REQUEST['title'])."',
						`reference_tag`			= '".addslashes($_REQUEST['reference_tag'])."'";
		$mycms->sql_insert($sql1);
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Added successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="manage_programtopic.php";</script>';
		exit();
		break;
			
	case 'edit':
	    $up = array();
		$up['QUERY'] = "  UPDATE "._DB_PROGRAM_SCHEDULE_TOPIC_."
					SET `topic_title` 		= '".addslashes($_REQUEST['title'])."',
						`reference_tag` 	= '".addslashes($_REQUEST['reference_tag'])."'
				  WHERE `id`				= '".$_REQUEST['id']."'";
		$mycms->sql_update($up);	
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Updated successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="manage_programtopic.php";</script>';
		exit();
		break;
		
	case 'remove':
		$topicId	= $_REQUEST['id'];
		$sqlDelete = array();
		$sqlDelete['QUERY']	= "DELETE FROM "._DB_SP_PARTICIPANT_SCHEDULE_."
							 WHERE `topic_id` = '".$topicId."'";
		$mycms->sql_delete($sqlDelete);
		$sqlDelete = array();
		$sqlDelete['QUERY']	= "DELETE FROM "._DB_SP_PARTICIPANT_COMMENTS_."
							 WHERE `topic_id` = '".$topicId."'";
		$mycms->sql_delete($sqlDelete);
		$sqlDelete = array();
		$sqlDelete['QUERY']	="DELETE FROM "._DB_PROGRAM_SCHEDULE_TOPIC_."
			   	   			WHERE `id`      = '".$topicId."'";
		$mycms->sql_delete($sqlDelete);
		
		 $_SESSION['toaster'] = [
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data Removed successfully!' // dynamic message
	    	];

		   // pageRedirection(1, "hotel_listing.php", 1);
		   echo '<script>window.location.href="manage_programtopic.php";</script>';
		exit();	
		break;
	
	case 'downloadTopicExcel':
		downloadTopicExcel($cfg, $mycms);
		exit();	
		break;
}

function downloadTopicExcel($cfg, $mycms)
{
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=topics".time().".xls");
	
	$loggedUserID     = $mycms->getLoggedUserId();
	?>		
	<table border="1">	
		<tr>
			<td colspan="2" align="left"><h4 style="color:#000000;">SCIENTIFIC PROGRAM TOPIC REPORT</h4></td>
		</tr>					
		<tr align="center" valign="middle" class="tcat">
			<th align="left" style="font-size:14px; padding:2px 5px 2px 5px;">Title</th>
			<th align="left" style="font-size:14px; padding:2px 5px 2px 5px;">Allocation</th>
		</tr>					
		<?php						
		$sqlFetchTopic['QUERY'] = "  SELECT tpk.*,
								   (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
								   (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins,
								   thm.theme_title, thm.theme_time_start, thm.theme_time_end,
								   session.session_title, session.session_start_time, session.session_end_time, session.session_date_id,
								   hall.hall_title,session.session_hall_id,
								   venue.id AS venue_id,
								   venue.program_venue,
								   scheduleDate.conf_date AS session_date  
								   
							  FROM ".$cfg['DB.PROGRAM.SCHEDULE.TOPIC']." tpk
						
				   LEFT OUTER JOIN ".$cfg['DB.PROGRAM.SCHEDULE.THEME']." thm
								ON thm.id = tpk.schedule_theme_id
							  
				   LEFT OUTER JOIN ".$cfg['DB.PROGRAM.SCHEDULE.SESSION']." session
								ON session.id = thm.schedule_id
								   
				   LEFT OUTER JOIN ".$cfg['DB.PROGRAM.SCHEDULE.DATE']." scheduleDate 
								ON session.session_date_id = scheduleDate.id
								
				   LEFT OUTER JOIN ".$cfg['DB.MASTER.HALL']." hall 
								ON session.session_hall_id = hall.id
								
				   LEFT OUTER JOIN ".$cfg['DB.PROGRAM.SCHEDULE.VENUE']." venue 
								ON hall.hall_venue = venue.id
							 
							 WHERE tpk.status = 'A'
						  ORDER BY tpk.topic_title";
						  
		$resultContent   = $mycms->sql_select($sqlFetchTopic);
		if($resultContent)
		{
			$i=0;
			foreach($resultContent as $keyContent=>$rowContent)
			{
				$i++;
				$allocation	= array();
				if($rowContent['session_date']!='') $allocation[] = $rowContent['session_date'].' '.$rowContent['hall_title'];
				if($rowContent['session_title']!='') $allocation[] = $rowContent['session_title'];
				if($rowContent['theme_title']!='') $allocation[] = $rowContent['theme_title'];
				if($rowContent['topic_time_duration']!='') $allocation[] = $rowContent['topic_time_duration'].' mins.';
		?>
		<tr class="tlisting">
			<td valign="top" style="font-size:12px; padding:2px 5px 2px 5px;"><?=$rowContent['topic_title']?></td>
			<td valign="top" style="font-size:12px; padding:2px 5px 2px 5px;"><?=implode('<br>',$allocation)?></td>
		</tr>
		<?php
			}
		}
	?>
		<tr>
			<td align="left" colspan="2">
				<h3>Excel Download Date and Time : <?=date('d/m/Y h:i A')?>	</h3>											
			</td>
		</tr> 	
	</table>	
	<?php
	
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
	$searchArray['src_title']                  = addslashes(trim($_REQUEST['src_title']));
	$searchArray['src_reference']        	   = addslashes(trim($_REQUEST['src_reference']));	
	
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