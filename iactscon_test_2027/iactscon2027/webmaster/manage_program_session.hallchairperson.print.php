<?php
include_once('includes/init.php');

?>
<html>
	<head>
		<title>:: Print ::</title>
	</head>
	
	<style>
		@font-face 
		{
		font-family: 'pathway_gothic_oneregular';
		src: url('./pathwaygothicone-regular-webfont.woff2') format('woff2'),
			 url('./pathwaygothicone-regular-webfont.woff') format('woff'),
			 url('./pathwaygothicone-regular-webfont.ttf') format('truetype');
		font-weight: normal;
		font-style: normal;
		}
		.scheduleTable {
			border: solid 1px #000;
			border-collapse:collapse;
		}
		.scheduleTable td, .scheduleTable th {
			border: solid 1px #000;
			padding:2px 10px 2px 10px;
			color:#000;
		}
		
		.scheduleTable .sessionTime{
			font-weight: bold;
		}
		
		.scheduleTable .sessionTitle{
			font-weight: bold;
		}
	</style>
	
	<body>
<?php 
		$dataArray = printSessionDetails($cfg, $mycms); 		
		anchorCards($dataArray);		
?>
	</body>
</html>

<?

function anchorCards($dataArray)
{
	$participantTypes = getPrintParticipationTypes();//array("chairperson","session coordinator","moderator","panelist","speaker", "paper presenter");
	
	$existingPrtId = array();
	
	$content = array();	
	
	if(isset($dataArray['HAS_CHAIRPERSON']) && $dataArray['HAS_CHAIRPERSON']=='YES')
	{
		$content = array();	
		$content['TYP'] = 'CHAIR';
		$content['DATA']['SESSION_TITLE'] 	= $dataArray['SESSION_TITLE'];
		$prtCount = 0;
		foreach($dataArray['CHAIRPERSON_CV'] as $kPt=>$rowParticipantType)
		{
			if(!in_array($kPt,$existingPrtId))
			{
				$existingPrtId[] 										= $kPt;
				$content['DATA']['PARTICIPANT'][$prtCount]['NAME'] 		= $rowParticipantType['NAME'];
				$content['DATA']['PARTICIPANT'][$prtCount]['CV'] 		= $rowParticipantType['CV']['participant_description'];
				$prtCount++;
			}
		}
		placeOnCard($content,false);
		echo '<h3 style="page-break-after: always;"></h3>';
	}
}


function placeOnCard($content,$triggerbreak=false,$breakBefore=array())
{
	$participantTypes = getPrintParticipationTypes();//array("chairperson","session coordinator","moderator","panelist","speaker", "paper presenter");
	
	
?>
<div style="width:1200px; height:auto;  position:relative; background-repeat: no-repeat; padding:100px;
			background-size: 100%; background-position: left top;">
<?
	$typ = $content['TYP'];
	$data = $content['DATA'];
	switch($typ)
	{
		case "FIRST_CARD":
?>
			<p style="margin: 0 auto ; padding-top:320px; font-size: 52px; text-align: center; font-weight: bold; color: rgb(0, 0, 0); font-family: 'pathway_gothic_oneregular';">
				<?=$data?>
			</p>
<?
			break;
		case "SCHEDULE":
?>
			<table align="center" width="95%" class="scheduleTable" style="font-size:22px;">
<?
			$time = "";
			foreach($data['THEME'] as $i => $rowData)
			{
				$title = $rowData['TITLE'];
				if($title=='')
				{
					$title = $data['SESSION_TITLE'];
				}
				
				if($time == $rowData['TIME'])
				{
?>
				</table>
				<h3 style="page-break-after: always;"></h3>
				<table align="center" width="95%" class="scheduleTable">
<?
				}
				
				$time = $rowData['TIME'];
?>
				<tr>
					<td colspan="2" align="center" valign="middle" class="sessionTime"><?=$rowData['TIME']?></td>
					<td colspan="2" align="left" valign="middle" class="sessionTitle">
						<?=$title?>
						<br>
						<?
							foreach($rowData['PARTICIPANT'] as $role => $prtDetail)
							{
								echo "<b>".ucwords($role)." - </b>".combineTheParticipants($prtDetail).'<br>';
							}
						?>
					</td>
				</tr>
<?
				$lclCounter = 1;
				foreach($rowData['TOPIC'] as $id => $tpcDetail)
				{
?>
				<tr>
					<td width="50px"><?=$data['SESSION_TAG'].'.'.$lclCounter?></td>
					<td width="60px"><?=$tpcDetail['DURATION']?> m</td>
					<td width="50%"><?=$tpcDetail['TITLE']?></td>
					<td>
					<?
										
						foreach($participantTypes as $kPt=>$rowParticipantType)
						{
							$prtt = array();		
							foreach($tpcDetail['PARTICIPANT'][$rowParticipantType] as $role => $prtDetail)
							{
								$prtt[] = $prtDetail['NAME'];
							}
							if(sizeof($prtt)>0)
							{
								echo "<b>".ucwords($rowParticipantType)." </b> - ".implode(', ',$prtt).'<br>';
							}
						}
						
						
					?>
					</td>
				</tr>
<?
					$lclCounter++;
				}
			}
?>
			</table>
<?
			break;
			
		case "ACTIVITY":
?>
			<p style="font-size: 28px; text-align: left; font-weight: normal; color: rgb(0, 0, 0); font-family: 'pathway_gothic_oneregular'; margin-top:10px; ">
				<?=$data['SESSION_TITLE']?>
			</p>
			
			<p style="font-size: 40px; text-align: left; font-weight: normal; color: rgb(0, 0, 0); font-family: 'pathway_gothic_oneregular';  margin-top:10px; margin-bottom:10px;">
				<?=$data['SESSION_TAG'].' '.$data['TITLE']?>
			</p>
<?
			foreach($data['PARTICIPANT'] as $iii=>$prtc)
			{
				echo '<!--'; echo $triggerbreak; print_r($breakBefore); echo '>'.(in_array(trim($prtc['NAME']),$breakBefore)).'<'; echo '-->';
				
?>	
			<p style="font-size: 28px; text-align: left; font-weight: bold; color: rgb(0, 0, 0); font-family: 'pathway_gothic_oneregular'; margin-top:10px; margin-bottom:10px;">
				<?=$prtc['NAME']?>
			</p>
<?
				if($prtc['CV']!='')
				{
?>
			<p style="font-size: 24px; text-align: left; font-weight: normal; color: rgb(0, 0, 0); font-family: 'pathway_gothic_oneregular'; margin-top:10px; margin-bottom:10px;">
				<?=$prtc['CV']?>
			</p>
<?
				}
			}
			break;
			
		case "CHAIR":
?>
			<!--<p style="font-size: 40px; text-align: left; font-weight: normal; color: rgb(0, 0, 0); font-family: 'pathway_gothic_oneregular'; margin-top:10px;">
			<?=$data['SESSION_TITLE']?>
			</p>		-->	
<?
			$cc=0;
			foreach($data['PARTICIPANT'] as $iii=>$prtc)
			{
				$cc++;
				echo '<!--##'; echo $triggerbreak; print_r($breakBefore); echo '>'.(in_array(trim($prtc['NAME']),$breakBefore)).'<'; echo '-->';
				if($triggerbreak && $cc > 1)// && !empty($breakBefore) && in_array(trim($prtc['NAME']),$breakBefore)
				{
?>
			<h3 style="page-break-after: always;"></h3>
<?
				}
?>	
			<p style="font-size: 28px; text-align: left; font-weight: bold; color: rgb(0, 0, 0); font-family: 'pathway_gothic_oneregular'; margin-top:10px; margin-bottom:10px;">
			<?=$prtc['NAME']?>
			</p>
<?
				if($prtc['CV']!='')
				{
?>
			<p style="font-size: 24px; text-align: left; font-weight: normal; color: rgb(0, 0, 0); font-family: 'pathway_gothic_oneregular'; margin-top:10px; margin-bottom:10px;">
				<?=$prtc['CV']?>
			</p>
<?
				}
			}
			break;
	}
?>
</div>
<?
}

function printSessionDetails($cfg, $mycms)
{
	$participantTypes = getPrintParticipationTypes();//array("chairperson","moderator","panellist","speaker");
	
	$dataArray	   	  = array();
	
	$hallId   		  = $_REQUEST['hallId'];
	$dateId   		  = $_REQUEST['dateId'];
	
	$sqlSelectSession['QUERY'] 	= " SELECT session.id																	  
									  FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
									 WHERE session.status = 'A' 
									   AND session.session_hall_id = '".$hallId."'
									   AND session.session_date_id = '".$dateId."'
								  ORDER BY session_start_time ASC";
	$resultSession			 	= $mycms->sql_select($sqlSelectSession);
	
	foreach($resultSession as $lk=>$rowSchs)
	{
		$schedule_id   = $rowSchs['id'];
		
		$sqlSelectSession['QUERY'] 	= " SELECT session.*																	  
										  FROM "._DB_PROGRAM_SCHEDULE_SESSION_." session
										 WHERE session.status = 'A' 
										   AND session.id = '".$schedule_id."'";
		$resultSession			 	= $mycms->sql_select($sqlSelectSession);
		$rowSession 			 	= $resultSession[0];
		
		$dataArray['SESSION_TITLE'] = $rowSession['session_title'];
		$dataArray['SESSION_TIME']  = $rowSession['session_start_time'];//.' - '.$rowSession['session_end_time'];
		$dataArray['SESSION_TAG'] 	= breakTheTitle($rowSession['session_title']);
		
		$sqlFetchTheme['QUERY'] 	= "SELECT *, 
											 (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60) AS theme_time_start_mins,
											 (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)-1 AS theme_time_end_mins
										FROM "._DB_PROGRAM_SCHEDULE_THEME_." 
									   WHERE schedule_id = '".$schedule_id."'
										 AND status = 'A'  
									ORDER BY (TIME_TO_SEC(CONCAT(theme_time_start,':00'))/60), (TIME_TO_SEC(CONCAT(theme_time_end,':00'))/60)";													 
		$resultTheme   				= $mycms->sql_select($sqlFetchTheme);	
		
		if($resultTheme)
		{
			foreach($resultTheme as $keyTheme=>$rowtheme)
			{
				$dataArray['THEME'][$rowtheme['id']]['TITLE'] 	= $rowtheme['theme_title'];
				$dataArray['THEME'][$rowtheme['id']]['TIME'] 	= $rowtheme['theme_time_start'];//.' - '.$rowtheme['theme_time_end'];
				
				$sqlParticipantTheme['QUERY']		 = "SELECT prt.*, sch.participant_type
														  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
													INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
															ON sch.participant_id = prt.id
														 WHERE sch.`session_id` = '".$schedule_id."'
														   AND sch.`theme_id` = '".$rowtheme['id']."'
														   AND (sch.topic_id IS NULL OR sch.topic_id = '')";
				$resultsParticipantTheme	 		= $mycms->sql_select($sqlParticipantTheme); 
				$themePartArr	= array();	
				if($resultsParticipantTheme)
				{
					$participantArr = array();
					foreach($resultsParticipantTheme as $kPt=>$rowParticipant)
					{
						if(trim($rowParticipant['participant_type'])=='')
						{
							$dataArray['THEME'][$rowtheme['id']]['PARTICIPANT']['PARTICIPANT'][$rowParticipant['id']]['NAME'] = $rowParticipant['participant_full_name'];
							$dataArray['THEME'][$rowtheme['id']]['PARTICIPANT']['PARTICIPANT'][$rowParticipant['id']]['CV'] = getParticipantCV($rowParticipant['id']);
						}
						else
						{
							$dataArray['THEME'][$rowtheme['id']]['PARTICIPANT'][strtolower(trim($rowParticipant['participant_type']))][$rowParticipant['id']]['NAME'] = $rowParticipant['participant_full_name'];
							$dataArray['THEME'][$rowtheme['id']]['PARTICIPANT'][strtolower(trim($rowParticipant['participant_type']))][$rowParticipant['id']]['CV'] = getParticipantCV($rowParticipant['id']);
							if(strtolower(trim($rowParticipant['participant_type']))=='chairperson' || strtolower(trim($rowParticipant['participant_type']))=='moderator' || strtolower(trim($rowParticipant['participant_type']))=='panellist' || strtolower(trim($rowParticipant['participant_type']))=='panelist')
							{
								$dataArray['HAS_CHAIRPERSON'] = 'YES';
								$dataArray['CHAIRPERSON_IDS'][] = $rowParticipant['id'];
								$dataArray['CHAIRPERSON_CV'][$rowParticipant['id']]['NAME'] = $rowParticipant['participant_full_name'];
								$dataArray['CHAIRPERSON_CV'][$rowParticipant['id']]['CV'] = getParticipantCV($rowParticipant['id']);
							}				
						}																					
					}		
				}																
										
				$sqlFetchTopic['QUERY']  = "SELECT *, 
												   topic_time_duration AS topic_time_duration_mins,
												   (TIME_TO_SEC(CONCAT(topic_time_start,':00'))/60) AS topic_time_start_mins,
												   (TIME_TO_SEC(CONCAT(topic_time_end,':00'))/60)-1 AS topic_time_end_mins
											  FROM "._DB_PROGRAM_SCHEDULE_TOPIC_." 
											 WHERE schedule_session_id = '".$schedule_id."'
											   AND schedule_theme_id = '".$rowtheme['id']."'
											   AND status = 'A'
										  ORDER BY sequence, id";													 
				$resultTopic   			 = $mycms->sql_select($sqlFetchTopic);	
					
				if($resultTopic)
				{	
					
					foreach($resultTopic as $keyTopic=>$rowTopic)
					{
						$dataArray['THEME'][$rowtheme['id']]['TOPIC'][$rowTopic['id']]['TITLE'] 	= $rowTopic['topic_title'];
						$dataArray['THEME'][$rowtheme['id']]['TOPIC'][$rowTopic['id']]['DURATION'] 	= $rowTopic['topic_time_duration'];
						
						$sqlParticipantTheme['QUERY']= "SELECT prt.*, sch.participant_type
														  FROM "._DB_SP_PARTICIPANT_SCHEDULE_." sch
													INNER JOIN "._DB_SP_PARTICIPANT_DETAILS_." prt
															ON sch.participant_id = prt.id
														 WHERE sch.`session_id` = '".$rowTopic['schedule_session_id']."'
														   AND sch.`theme_id` = '".$rowTopic['schedule_theme_id']."'
														   AND sch.`topic_id` = '".$rowTopic['id']."'
													  ORDER BY prt.participant_full_name";
						$resultsParticipantTheme	 = $mycms->sql_select($sqlParticipantTheme); 
						$topicPartArr = array();
						if($resultsParticipantTheme)
						{
							$participantArr = array();	
							foreach($resultsParticipantTheme as $kPt=>$rowParticipant)
							{
								if(trim($rowParticipant['participant_type'])=='')
								{
									$dataArray['THEME'][$rowtheme['id']]['TOPIC'][$rowTopic['id']]['PARTICIPANT']['PARTICIPANT'][$rowParticipant['id']]['NAME'] = $rowParticipant['participant_full_name'];
									$dataArray['THEME'][$rowtheme['id']]['TOPIC'][$rowTopic['id']]['PARTICIPANT']['PARTICIPANT'][$rowParticipant['id']]['CV'] = getParticipantCV($rowParticipant['id']);
								}
								else
								{
									$dataArray['THEME'][$rowtheme['id']]['TOPIC'][$rowTopic['id']]['PARTICIPANT'][strtolower(trim($rowParticipant['participant_type']))][$rowParticipant['id']]['NAME'] = $rowParticipant['participant_full_name'];
									$dataArray['THEME'][$rowtheme['id']]['TOPIC'][$rowTopic['id']]['PARTICIPANT'][strtolower(trim($rowParticipant['participant_type']))][$rowParticipant['id']]['CV'] = getParticipantCV($rowParticipant['id']);
								}																					
							}	
						}	
					}
				}
			}
		}	
	}
	
	return $dataArray;
}

function getParticipantCV($participantId)
{
	global $mycms, $cfg;
	
	$return = array();
	
	$sqlListing['QUERY']	= " SELECT *
								  FROM "._DB_SP_PARTICIPANT_DETAILS_." 
								 WHERE `status` = 'A' 
								   AND `id` = '".$participantId."'";
	$resultsListing	 		= $mycms->sql_select($sqlListing); 
	$rowParticipant	 		= $resultsListing[0];
	
	$cv = $rowParticipant['participant_description'];
	if(trim($cv)!='')
	{
		$cvList = explode(PHP_EOL, $cv);
		
		$cvText = '<ul style="font-size:20px;">';
		foreach($cvList as $cc=>$cvTxt)
		{
			$cvText .= '<li style="font-size:20px;">'.$cvTxt.'</li>';
		}
		$cvText .= '</ul>';
		
		$rowParticipant['participant_description'] = $cvText;
	}
	return $rowParticipant;
}

function breakTheTitle($title)
{
	$tag = "";
	
	$s1 = explode('-',$title);
	$p = trim($s1[0]);
	$prt = explode(' ',$p);
	
	$szPrt = sizeof($prt);
	if($szPrt>2)
	{
		$tag .= 	trim($prt[0])[0];
		$tag .= 	trim($prt[1])[0];
		$tag .= 	trim($prt[$szPrt-1])[0];
	}
	elseif($szPrt>1)
	{
		$tag .= 	trim($prt[0])[0];
		$tag .= 	trim($prt[1])[0];
	}
	else
	{
		$tag .= 	trim($prt[0])[0];
		$tag .= 	trim($prt[0])[1];
	}
	return strtoupper($tag);
}

function combineTheParticipants($prtDetail)
{
	$comb = array();
	
	foreach($prtDetail as $id=>$det)
	{
		$comb[] = $det['NAME'];
	}
	
	return implode(", ",$comb);
}

function getPrintParticipationTypes()
{
	global $cfg, $mycms;
	
	$sql['QUERY']		 			= "SELECT DISTINCT participant_type
												  FROM "._DB_SP_PARTICIPANT_SCHEDULE_."
											  ORDER BY (CASE WHEN LOWER(participant_type) = 'chairperson' THEN 1
															 WHEN LOWER(participant_type) = 'session coordinator' THEN 2
															 WHEN LOWER(participant_type) = 'moderator' THEN 3
															 WHEN LOWER(participant_type) = 'panelist' THEN 4
															 WHEN LOWER(participant_type) = 'speaker' THEN 5
															 WHEN LOWER(participant_type) = 'paper presenter' THEN 6
															 ELSE 999
														END) ASC";
	$resultsParticipantType	 		= $mycms->sql_select($sql);
	$return 						= array();
	foreach($resultsParticipantType as $kk=>$rowTyp)
	{
		$return[] = strtolower($rowTyp['participant_type']);
	}
	
	return $return;
}
?>


