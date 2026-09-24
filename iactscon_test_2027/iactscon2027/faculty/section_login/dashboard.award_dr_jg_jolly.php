<?php
	include_once('includes/init.php');
	
	$sqlFetchAward['QUERY']		= "SELECT COUNT(*) AS totalAwardRequestCount,
                                      SUM(totalReviewAttemptCount) AS grandReviewAttemptCount
	
	                             FROM (
										   SELECT awardRequest.id AS awardRequestId,
												  IFNULL(totalReviewAttemptCount, 0) AS totalReviewAttemptCount 
				
											 FROM "._DB_AWARD_REQUEST_." awardRequest 
											 
								  LEFT OUTER JOIN (
												   
														SELECT COUNT(*) totalReviewAttemptCount,
															   award_id,
															   faculty_id 
														  
														  FROM "._DB_AWARD_REVIEW_RESULT_." 
														 WHERE `faculty_id` = '".$mycms->getLoggedUserId()."' 
														   AND `status` = 'A' 
														  
													  GROUP BY `award_id`     
											   
												  ) totalReviewAttempt 
											   ON totalReviewAttempt.award_id = awardRequest.id
											   
											WHERE awardRequest.status = 'A' 
											  AND awardRequest.award_category = 'AWARD_DR_JG_JOLLY'
								      ) mainTAB";
						   
	$resultAward        = $mycms->sql_select($sqlFetchAward);
	$rowAward           = $resultAward[0];
	
	$totalAwardRequest  = ($rowAward['totalAwardRequestCount']=="")?0:$rowAward['totalAwardRequestCount'];
	$totalAwardReviewed = ($rowAward['grandReviewAttemptCount']=="")?0:$rowAward['grandReviewAttemptCount'];
	
	$reviewPercentage   = ($totalAwardReviewed/$totalAwardRequest)*100;
?>
	<html>
		<head>
			<title>:: Award :: Dr. J. G. Jolly</title>
			<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/style.css" type="text/css" />
			<script src="<?=$cfg['DOMAIN_URL']?>js/jquery-1.10.2.min.js"></script>
		</head>
		<body style="background-color:#838280; margin: 0px; padding: 0px;">
			<div style="padding:20px 20px 0 20px;">
				<div style="color:#FFFFFF; font-size:14px;">
					<div style="font-size:24px; margin-bottom: 17px;">Dr. J. G. Jolly</div>
					<div style="font-size:16px; margin-bottom: 2px;">&bull; Total Submission : <?=$rowAward['totalAwardRequestCount']?></div>
				</div>
			</div>
			<div style="padding:8px 20px 0 20px;">
				<div style="color:#FFFFFF; font-size:14px;">
					<div style="font-size:14px; margin-bottom: 5px;">Reviewed By:</div>
					<div style="font-size:14px; margin-bottom: 2px;">&bull; President ISTM</div>
					<div style="font-size:14px; margin-bottom: 2px;">&bull; HOD Transfusion Medicine, <br /> &nbsp;&nbsp; PGIMER, Chandigarh</div>
					<div style="font-size:14px; margin-bottom: 2px;">&bull; HOD Transfusion Medicine, <br /> &nbsp;&nbsp; SGPGI, Lucknow</div>
					<div style="font-size:14px; margin-bottom: 2px;">&bull; Dr. Neeraj Jolly, USA</div>
				</div>
			</div>
		</body>
	</html>