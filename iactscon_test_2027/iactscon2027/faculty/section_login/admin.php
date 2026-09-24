<?php
	include_once('includes/init.php');

	dashboard_page_header("Reviewer Panel");
	
	$loggedUserID = $mycms->getLoggedUserId();
	
	// FETCH LOGGED USER DETAILS
	$sqlFacultyDetails['QUERY']		= "SELECT * 
								 FROM "._DB_FACULTY_ACCOUNT_."  
	                            WHERE `id` = '".$mycms->getLoggedUserId()."'";
	
	$resultFacultyDetails   = $mycms->sql_select($sqlFacultyDetails);
	$rowFacultyDetails      = $resultFacultyDetails[0];
	// print_r($rowFacultyDetails);
?>

	<div class="container" style="margin:0px; padding:0px;">
		
		<!-- <table width="100%" border="1">
			<tr>
				<td width="50%" align="center" bgcolor="#DEDEDC" colspan="2">
					<div style="color:#000000; font-size:18px; margin:10px;">REVIEW HISTORY</div>
				</td>
				
			</tr>
		</table> -->
		
		<table width="100%" border="1">
			<!-- <tr>
				<td width="50%" height="279" align="left" bgcolor="#4e4e4e" valign="top">
					<div style="color:#FFFFFF; font-size:14px; margin:100px 20px;">
					 <img src="images/abstract.png" style="margin-top:5px;">
						<div style="font-size:28px; margin-bottom: 17px;">Abstract </div>
						<?php
							$sqlAbstractTopic['QUERY']    = "SELECT * 
													  FROM "._DB_ABSTRACT_TOPIC_." 
												     WHERE `status` = 'A' 
												  ORDER BY `abstract_topic` ASC";
							
							$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
							if($resultAbstractTopic)
							{
								foreach($resultAbstractTopic as $keyAbstractTopic=>$rowAbstractTopic)
								{
							?>
									<?php /*?><div style="font-size:16px; margin-bottom: 2px;">&bull; <?=$rowAbstractTopic['abstract_topic']?></div><?php */?>
							<?php
								}
							}
							
							$ReviewCount['QUERY'] = "SELECT COUNT(*) AS totalReviewAttemptCount, abstract.tags, registeredDelegates.status
													  FROM "._DB_ABSTRACT_REVIEW_RESULT_." review
												
												INNER JOIN "._DB_ABSTRACT_REQUEST_." abstract 
														ON review.abstract_id = abstract.id
														 
												INNER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
														ON abstract.applicant_id = registeredDelegates.id 	
													
													 WHERE abstract.status = 'A' 
													   AND abstract.abstract_parent_type = 'ABSTRACT'
													   AND review.status = 'A'
													   AND registeredDelegates.status = 'A'
													   AND review.faculty_id = '".$loggedUserID."'"; 
									 
							$resultReviewCount     = $mycms->sql_select($ReviewCount);
							$rowReviewCount        = $resultReviewCount[0];
							
							$totalCount['QUERY'] 		   = " SELECT *, COUNT(abstractRequest.id) AS totalAbstract
														 FROM "._DB_ABSTRACT_REQUEST_." abstractRequest
												   INNER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
														   ON abstractRequest.applicant_id = registeredDelegates.id 
														WHERE abstractRequest.status = 'A' 
														  AND abstractRequest.abstract_parent_type = 'ABSTRACT'
														  AND registeredDelegates.status = 'A'
											  			  AND abstractRequest.id IN (SELECT abstract_id FROM "._DB_ABSTRACT_ALLOTMENT_." WHERE review_user_id = '".$loggedUserID."')"; 
									 
							$resulttotalCount      = $mycms->sql_select($totalCount);
							$rowtotalCount         = $resulttotalCount[0];
							?>
							<br />
							<div style="font-size:20px; margin-bottom: 17px;">Total Alloted Abstract Paper :  <?=$rowtotalCount['totalAbstract']?></div>
							<div style="font-size:20px; margin-bottom: 17px; ">Total Reviewed Abstract Paper :  <?=$rowReviewCount['totalReviewAttemptCount']?></div>
							<div style="font-size:20px; margin-bottom: 17px;">Pending Abstract Paper to Review:  <?=($rowtotalCount['totalAbstract'])-($rowReviewCount['totalReviewAttemptCount'])?></div>	
							
					</div>					
				</td>
				<td width="50%" height="279" align="left" bgcolor="#e2000f" valign="top" >
					<div style="color:#FFFFFF; font-size:14px; margin:100px 20px;">
					 <img src="images/abstract.png" style="margin-top:5px;">
						<div style="font-size:28px; margin-bottom: 17px;">Case Report </div>
						<?php
							$sqlAbstractTopic['QUERY']    = "SELECT * 
													  FROM "._DB_ABSTRACT_TOPIC_." 
													 WHERE `status` = 'A' 
												  ORDER BY `abstract_topic` ASC";
							
							$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
							if($resultAbstractTopic)
							{
								foreach($resultAbstractTopic as $keyAbstractTopic=>$rowAbstractTopic)
								{
							?>
									<?php /*?><div style="font-size:16px; margin-bottom: 2px;">&bull; <?=$rowAbstractTopic['abstract_topic']?></div><?php */?>
							<?php
								}
							}
							
							$ReviewCount['QUERY'] = " SELECT COUNT(*) AS totalReviewAttemptCount,abstract.tags,registeredDelegates.status
											   FROM "._DB_ABSTRACT_REVIEW_RESULT_." review
										 INNER JOIN "._DB_ABSTRACT_REQUEST_." abstract 
												 ON review.abstract_id = abstract.id
										 INNER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
												 ON abstract.applicant_id = registeredDelegates.id 
											  WHERE abstract.status = 'A' 
												AND abstract.abstract_parent_type = 'CASEREPORT'
												AND review.status = 'A'
												AND registeredDelegates.status = 'A'
												AND review.faculty_id = '".$loggedUserID."'"; 
									 
							$resultReviewCount         = $mycms->sql_select($ReviewCount);
							$rowReviewCount            = $resultReviewCount[0];
							
							$totalCount['QUERY'] = "SELECT *, COUNT(abstractRequest.id) AS totalAbstract
											 FROM "._DB_ABSTRACT_REQUEST_." abstractRequest
									   INNER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
											   ON abstractRequest.applicant_id = registeredDelegates.id 
											WHERE abstractRequest.status = 'A' 
											  AND abstractRequest.abstract_parent_type = 'CASEREPORT'
											  AND registeredDelegates.status = 'A'	
											  AND abstractRequest.id IN (SELECT abstract_id FROM "._DB_ABSTRACT_ALLOTMENT_." WHERE review_user_id = '".$loggedUserID."')"; 
									 
							$resulttotalCount         = $mycms->sql_select($totalCount);
							$rowtotalCount           = $resulttotalCount[0];
							?>
							<br />
							<div style="font-size:20px; margin-bottom: 17px;">Total Submitted Case Report :  <?=$rowtotalCount['totalAbstract']?></div>
							<div style="font-size:20px; margin-bottom: 17px; ">Total Reviewed Case Report :  <?=$rowReviewCount['totalReviewAttemptCount']?></div>
							<div style="font-size:20px; margin-bottom: 17px;">Pending Case Report to Review:  <?=$rowtotalCount['totalAbstract']-$rowReviewCount['totalReviewAttemptCount']?></div>	
							
					</div>
					
				</td>
			</tr> -->
			
			<tr>
				<td width="50%" align="center" bgcolor="#DEDEDC" colspan="2">
					<div style="color:#000000; font-size:18px; margin:10px;">
					Reviewed By
					</div>
				</td>
			</tr>
			<tr>
				<!--<td  height="279" align="left" bgcolor="#e2000f" valign="center" colspan="2">
				<div style="font-size: 28px; margin-top: 44px; margin-left: 40%; color: white;">Faculty List </div>
				<div style="color:#FFFFFF; font-size:20px; margin:63px 39%;">-->	
				<?
					/*$facultyCounter          = 0;
					$loggedUserID 			 = $mycms->getLoggedUserId();
					$sqlFacultyDetails['QUERY']		 = "SELECT faculty.*,
															   
													   IFNULL(faculty.faculty_title, '') AS facultyTitle,
													   IFNULL(faculty.faculty_first_name, '') AS facultyFirstName,
													   IFNULL(faculty.faculty_middle_name, '') AS facultyMiddleName,
													   IFNULL(faculty.faculty_last_name, '') AS facultyLastName,
													   
													   abstractReview.id AS abstractReviewId,
													   abstractReview.marks_obtained,
													   abstractReview.created_dateTime AS review_dateTime  
												  
												 FROM "._DB_FACULTY_ACCOUNT_." faculty  
												   
									  LEFT OUTER JOIN "._DB_ABSTRACT_REVIEW_RESULT_." abstractReview 
												   ON faculty.id = abstractReview.faculty_id
												  AND abstractReview.status = 'A' 
												  AND abstractReview.abstract_id = '".$rowAbstractDetails['id']."'
												  
												WHERE faculty.id != '1'
												  AND faculty.status = 'A' ";
					
					$resultFacultyDetails    = $mycms->sql_select($sqlFacultyDetails);
					?>
					<?php
					if($resultFacultyDetails)
					{
						foreach($resultFacultyDetails as $keyFacultyDetails=>$rowFacultyDetails)
						{
							$facultyCounter++;
					?>
							<span style="padding-left:10px;">
							&bull;&nbsp;<?=$rowFacultyDetails['faculty_title']?>. 
							<?=$rowFacultyDetails['faculty_first_name']?> 
							<?=$rowFacultyDetails['faculty_middle_name']?> 
							<?=$rowFacultyDetails['faculty_last_name']?>
							</span> <br><br>
						<?php
						}
					}*/
					?>	
		<!--</div>
			
		</td>-->
				<td width="50%" height="279" align="center" bgcolor="#666666" valign="top" colspan="2">
					<div style="color:#FFFFFF; font-size:28px; margin:75px 20px;">
					<img src="images/blank_image.png" style="width: 30%; style="margin-top:5px;">
					<br><?=$rowFacultyDetails['faculty_title']?> 
					<?=$rowFacultyDetails['faculty_first_name']?> 
					<?=$rowFacultyDetails['faculty_middle_name']?> 
					<?=$rowFacultyDetails['faculty_last_name']?> <br>
					</div>
					
				</td>
			</tr>
			<tr class="tfooter">

					<td colspan="2">
					<span style="color: red;"> For any query contact : +91 81005 69558 (11:00 - 20:00)</span>
						
					</td>
				</tr>	
		</table>
		
	</div>
<?php
	page_footer();
?>