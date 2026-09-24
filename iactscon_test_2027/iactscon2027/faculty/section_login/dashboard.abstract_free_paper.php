<?php
	include_once('includes/init.php');
	
	$sqlFetchAbstract['QUERY']		= "SELECT COUNT(*) AS totalAbstractRequestCount,
                                      SUM(totalReviewAttemptCount) AS grandReviewAttemptCount
	
	                             FROM (
										   SELECT abstractRequest.id AS abstractRequestId,
												  TIMESTAMPDIFF(DAY, abstractRequest.created_dateTime, '".date('Y-m-d H:i:s')."') AS dayPassedFromSubmit,
												  IFNULL(totalReviewAttemptCount, 0) AS totalReviewAttemptCount 
				
											 FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 
											 
								  LEFT OUTER JOIN (
												   
														SELECT COUNT(*) totalReviewAttemptCount,
															   abstract_id,
															   faculty_id 
														  
														  FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
														 WHERE `faculty_id` = '".$mycms->getLoggedUserId()."' 
														   AND `status` = 'A' 
														  
													  GROUP BY `abstract_id`     
											   
												  ) totalReviewAttempt 
											   ON totalReviewAttempt.abstract_id = abstractRequest.id
											   
											WHERE abstractRequest.status = 'A' 
											  AND abstractRequest.abstract_parent_type = 'FREE_PAPER_SESSION' 
								      ) mainTAB";
						   
	$resultAbstract         = $mycms->sql_select($sqlFetchAbstract);
	$rowAbstract            = $resultAbstract[0];
	
	$totalAbstractRequest   = ($rowAbstract['totalAbstractRequestCount']=="")?0:$rowAbstract['totalAbstractRequestCount'];
	$totalAbstractReviewed  = ($rowAbstract['grandReviewAttemptCount']=="")?0:$rowAbstract['grandReviewAttemptCount'];
	
	$reviewPercentage       = ($totalAbstractReviewed/$totalAbstractRequest)*100;
?>
	<html>
		<head>
			<title>:: Abstract :: Free Paper</title>
			<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/style.css" type="text/css" />
			<script src="<?=$cfg['DOMAIN_URL']?>js/jquery-1.10.2.min.js"></script>
		</head>
		<body style="background-color:#838280; margin: 0px; padding: 0px;">
			<div style="padding:20px 20px 0 20px;">
				<div style="color:#FFFFFF; font-size:14px;">
					<div style="font-size:24px; margin-bottom: 17px;">Free Paper</div>
					<div style="font-size:16px; margin-bottom: 2px;">&bull; Total Submission : <?=$rowAbstract['totalAbstractRequestCount']?></div>
					<div style="font-size:16px; margin-bottom: 2px;">&bull; Total Reviewed : <?=$rowAbstract['grandReviewAttemptCount']?></div>
				</div>
				<div id="container" data-dimension="170" data-info="Free Paper" data-width="16" data-percent="<?=$reviewPercentage?>" data-fgcolor="#FFFFFF" data-bgcolor="#898888" style="margin:0px auto;"></div>
				<script>
					$( document ).ready(function() {
						$('#container').circliful();
					});
				</script>
			</div>
			
			<script src="<?=_BASE_URL_?>js/adminPanel/all.js"></script>
			<script src="<?=_BASE_URL_?>js/graph/jquery.circliful.js"></script>
		</body>
	</html>