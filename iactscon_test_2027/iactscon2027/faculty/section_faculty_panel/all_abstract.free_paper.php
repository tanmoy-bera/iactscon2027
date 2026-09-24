<?php
	include_once('includes/init.php');
	page_header("Free Paper");
	
	$pageKey                       		       		 = "_pgn1_";
	$pageKeyVal                    		       		 = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString                 		       		 = "";
	$searchArray                   		       		 = array();
	
	$searchArray[$pageKey]         		       		 = $pageKeyVal;
	$searchArray['src_abstract_submission_code']  	 = trim($_REQUEST['src_abstract_submission_code']);
	$searchArray['src_abstract_topic_id']  		 	 = trim($_REQUEST['src_abstract_topic_id']);
	$searchArray['src_paper_presentation_category']  = trim($_REQUEST['src_paper_presentation_category']);
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
?>
	<script language="javascript" src="scripts/abstract.free_paper.js"></script>
	<div class="container">
		<?php 
		switch($show){
			
			// ABSTRACT VIEW DETAILS WINDOW
			case'view':
			
				abstractReviewDetailsWindow($cfg, $mycms);
				break;	
														
			// ABSTRACT LISTING DETAILS WINDOW
			default:
				
				abstractListingDisplayWindow($cfg, $mycms); 
				break;				
		
		}
		?>
	</div>
<?php

	page_footer();

	/****************************************************************************/
	/*                      ABSTRACT LISTING DETAILS WINDOW                     */
	/****************************************************************************/
	function abstractListingDisplayWindow($cfg, $mycms)
	{
		global $searchString, $searchArray;
	?>
		<form name="frmSearch" id="frmSearch" action="all_abstract.free_paper.process.php" method="post">
			<input type="hidden" name="act" value="search_abstract" />
			<table width="100%" class="tborder">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Submission List</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<div class="tsearch">
							<table width="100%">
								<tr>
									<td align="left" width="150">Submission Code:</td>
									<td align="left" width="250">
										<input type="text" name="src_abstract_submission_code" id="src_abstract_submission_code" 
										 style="width:90%; text-transform:uppercase;" value="<?=$_REQUEST['src_abstract_submission_code']?>" />
									</td>
									<td align="left" width="150"></td>
									<td align="left" width="250"></td>
									<td align="right" rowspan="2">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
									</td>
								</tr>
								<tr>
									<td align="left">Select Preference:</td>
									<td align="left">
										<select name="src_paper_presentation_category" id="src_paper_presentation_category" style="width:90%;">
											<option value="">-- Select Category --</option>
											<option value="ORAL" <?=($_REQUEST['src_paper_presentation_category']=="ORAL")?'selected="selected"':''?>>Oral</option>
											<option value="POSTER" <?=($_REQUEST['src_paper_presentation_category']=="POSTER")?'selected="selected"':''?>>Poster</option>
											<option value="NONE" <?=($_REQUEST['src_paper_presentation_category']=="NONE")?'selected="selected"':''?>>No Choice</option>
										</select>
									</td>
									<td align="left">Abstract Topic:</td>
									<td align="left">
										<select name="src_abstract_topic_id" id="src_abstract_topic_id" style="text-transform:uppercase; width:90%;">
											<option value="">-- Select Topic --</option>
											<?php
											$sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_TOPIC_." 
																			WHERE `status` = 'A' 
																		 ORDER BY `abstract_topic` ASC";
											
											$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);
											if($resultAbstractTopic)
											{
												foreach($resultAbstractTopic as $keyAbstractTopic=>$rowAbstractTopic)
												{
											?>
													<option value="<?=$rowAbstractTopic['id']?>" <?=($rowAbstractTopic['id']==$_REQUEST['src_abstract_topic_id'])?'selected="selected"':''?>><?=$rowAbstractTopic['abstract_topic']?></option>
											<?php
												}
											}
											?>
										</select>
									</td>
								</tr>
							</table>
						</div>
						
						<table width="100%">
							<tr class="theader">
								<td width="50" align="center">Sl No</td>
								<td width="100" align="center">Submission Code</td>
								<td width="150" align="center">Abstract Topic</td>
								<td align="left">Abstract Title</td>
								<td width="100" align="center">Review Result</td>
								<td width="85" align="center">Action</td>
							</tr>
							<?php
							$counter                       = 0;
							
							$searchCondition               = "";
							$searchCondition              .= " AND grandTAB.abstract_parent_type = !'VIDEO_PRESENTATION'";
							
							
							if($_REQUEST['src_abstract_submission_code']!="")
							{
								$searchCondition          .= " AND grandTAB.abstract_submition_code LIKE '%".addslashes(trim($_REQUEST['src_abstract_submission_code']))."%'";
							}
							if($_REQUEST['src_paper_presentation_category']!="")
							{
								$searchCondition          .= " AND grandTAB.abstract_child_type = '".addslashes(trim($_REQUEST['src_paper_presentation_category']))."'";
							}
							if($_REQUEST['src_abstract_topic_id']!="")
							{
								$searchCondition          .= " AND grandTAB.abstract_topic_id = '".addslashes(trim($_REQUEST['src_abstract_topic_id']))."'";
							}
							
							$searchCondition              .= " ORDER BY grandTAB.id ASC";
							
							$sqlAbstractDetails['QUERY']            ="SELECT grandTAB.*,
												   REPLACE(CONCAT(applicantFirstName, ' ', applicantMiddleName, ' ', applicantLastName), '  ', ' ') AS applicant_full_name,
												   REPLACE(CONCAT(user_title, ' ', user_first_name, ' ', user_middle_name, ' ', user_last_name), '  ', ' ') AS user_full_name 
											  
											  FROM (
							                            SELECT abstractRequest.*,
																   abstractTopic.abstract_topic,
																   
																   registeredDelegates.user_email_id,
																   registeredDelegates.user_mobile_no,
																   registeredDelegates.user_unique_sequence,
																   registeredDelegates.user_registration_id,
																   
																   IFNULL(registeredDelegates.user_title, '') AS user_title,
																   IFNULL(registeredDelegates.user_first_name, '') AS user_first_name,
																   IFNULL(registeredDelegates.user_middle_name, '') AS user_middle_name,
																   IFNULL(registeredDelegates.user_last_name, '') AS user_last_name,
																
																   registeredDelegates.isRegistration,
																   registeredDelegates.isWorkshop,
																  
																   
																   registeredDelegates.registration_payment_status,
																   registeredDelegates.workshop_payment_status,
																   
																   country.country_name AS author_country_name,
																   state.state_name AS author_state_name,
																   
																   IFNULL(abstractRequest.applicant_first_name, '') AS applicantFirstName,
																   IFNULL(abstractRequest.applicant_middle_name, '') AS applicantMiddleName,
																   IFNULL(abstractRequest.applicant_last_name, '') AS applicantLastName,
																   
																   IFNULL(abstractReview.totalMarksObtained, 0) AS totalMarksObtained,
																   IFNULL(abstractReview.totalReviewCount, 0) AS totalReviewCount
																 
																   
																   
								
															  FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 
															  
												   		INNER JOIN "._DB_ABSTRACT_TOPIC_." abstractTopic 
																ON abstractRequest.abstract_topic_id = abstractTopic.id 
												   
												   		INNER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
																ON abstractRequest.applicant_id = registeredDelegates.id 
															   AND registeredDelegates.status = 'A'
												   
												   LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
																ON abstractRequest.abstract_author_country_id = country.country_id
															 
												   LEFT OUTER JOIN "._DB_COMN_STATE_." state
																ON abstractRequest.abstract_author_state_id = state.st_id
																
												   LEFT OUTER JOIN (
																		SELECT SUM(abstractReview.marks_obtained) AS totalMarksObtained,
																			   COUNT(abstractReview.id) AS totalReviewCount,
																			   
																			   abstractReview.abstract_id 
																		   
																		  FROM "._DB_ABSTRACT_REVIEW_RESULT_." abstractReview 
																	
																	
																		 
																		 WHERE abstractReview.status = 'A' 
																	  
																	  GROUP BY abstractReview.abstract_id 
																   ) abstractReview 
																ON abstractReview.abstract_id = abstractRequest.id 
																
												   
												    WHERE abstractRequest.status = 'A'
													 ) grandTAB 
									 
									 WHERE 1 ".$filterCondition." ".$searchCondition;
												
									    
									
									 
							$resultAbstractDetails         = $mycms->pagination(1, $sqlAbstractDetails, 25, $restrt);	
							if($resultAbstractDetails)
							{
								foreach($resultAbstractDetails as $i=>$rowAbstractDetails) 
								{
								
								
										$counter++;
											$ReviewCount['QUERY'] = "SELECT COUNT(*) AS totalReviewAttemptCount FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
															 
													WHERE abstract_id =".$rowAbstractDetails['id'].""; 
															 
													$resultReviewCount         = $mycms->sql_select($ReviewCount);
													$rowReviewCount            = $resultReviewCount[0];
																		
														
									$rowStyleDecission     = "";
									
									if($rowReviewCount   ['totalReviewAttemptCount']==0)
									{
										$rowStyleDecission = " style='background-color: #FFFFFF;'";
									}
									else
									{
										$rowStyleDecission = " style='background-color: #DDF1D6;'";
									}
							?>
									<tr class="tlisting" <?=$rowStyleDecission?>>
										<td align="center" valign="top"><?=$counter + ($_REQUEST['_pgn1_']*25)?></td>
										<td align="center" valign="top"><?=$rowAbstractDetails['abstract_submition_code']?></td>
										<td align="center" valign="top"><?=$rowAbstractDetails['abstract_topic']?></td>
										<td align="left" valign="top"><?=escapeHTMLSpecialCharacter($rowAbstractDetails['abstract_title'])?></td>
										<td align="center" valign="top">
										<?php
										if($rowReviewCount   ['totalReviewAttemptCount']>0)
										{
											echo $rowAbstractDetails['totalMarksObtained'];
										}
										else
										{
										?>
											<span class="ticket ticket-important">Not Reviewed</span>
										<?php
										}
										?>
										</td>
										<td align="center" valign="top">
											
										<?php
										if($rowReviewCount  ['totalReviewAttemptCount']==0)
										{
										?>
											<a href="all_abstract.free_paper.php?show=view&id=<?=$rowAbstractDetails['id'].$searchString?>">Review</a>
										<?php
										}
										else if($rowReviewCount  ['totalReviewAttemptCount']<2)
										{
										?>
											<a href="all_abstract.free_paper.php?show=view&id=<?=$rowAbstractDetails['id'].$searchString?>">Update Review</a>
										<?php
										}
										else
										{
										?>
											<a href="all_abstract.free_paper.php?show=view&id=<?=$rowAbstractDetails['id'].$searchString?>">View</a>
										<?php
										}
										?>
											
										</td>
									</tr>
							<?php
								}
							}
							else
							{
							?>
								<tr>
									<td colspan="6" align="center">
										<span class="mandatory">No Record(s) Found</span>
									</td>
								</tr>
							<?php
							}
							?>
						</table>
							
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>	
			</table>
		</form>
	<?php
	}
	
	/****************************************************************************/
	/*                       ABSTRACT VIEW DETAILS WINDOW                       */
	/****************************************************************************/
	function abstractReviewDetailsWindow($cfg, $mycms)
	{
		global $searchString, $searchArray;
		
		$abstractId 				   = addslashes(trim($_REQUEST['id']));
		
		$searchCondition               = "";
		$searchCondition              .= " AND grandTAB.abstract_parent_type = 'VIDEO_PRESENTATION'";
		
		
		$sqlAbstractDetails['QUERY']            = " SELECT abstractRequest.*,
															   abstractTopic.abstract_topic,
															   
															   registeredDelegates.user_email_id,
															   registeredDelegates.user_mobile_no,
															   registeredDelegates.user_unique_sequence,
															   registeredDelegates.user_registration_id,
															   
															   IFNULL(registeredDelegates.user_title, '') AS user_title,
															   IFNULL(registeredDelegates.user_first_name, '') AS user_first_name,
															   IFNULL(registeredDelegates.user_middle_name, '') AS user_middle_name,
															   IFNULL(registeredDelegates.user_last_name, '') AS user_last_name,
															
															   registeredDelegates.isRegistration,
															   registeredDelegates.isWorkshop,
															  
															   
															   registeredDelegates.registration_payment_status,
															   registeredDelegates.workshop_payment_status,
															   
															   country.country_name AS author_country_name,
															   state.state_name AS author_state_name,
															   
															   IFNULL(abstractRequest.applicant_first_name, '') AS applicantFirstName,
															   IFNULL(abstractRequest.applicant_middle_name, '') AS applicantMiddleName,
															   IFNULL(abstractRequest.applicant_last_name, '') AS applicantLastName,
															   
															   IFNULL(abstractReview.totalMarksObtained, 0) AS totalMarksObtained,
															   IFNULL(abstractReview.totalReviewCount, 0) AS totalReviewCount
															 
																   
																   
								
															  FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 
															  
												   		INNER JOIN "._DB_ABSTRACT_TOPIC_." abstractTopic 
																ON abstractRequest.abstract_topic_id = abstractTopic.id 
												   
												   		INNER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
																ON abstractRequest.applicant_id = registeredDelegates.id 
															   AND registeredDelegates.status = 'A'
												   
												   LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
																ON abstractRequest.abstract_author_country_id = country.country_id
															 
												   LEFT OUTER JOIN "._DB_COMN_STATE_." state
																ON abstractRequest.abstract_author_state_id = state.st_id
																
												   LEFT OUTER JOIN (
																		SELECT SUM(abstractReview.marks_obtained) AS totalMarksObtained,
																			   COUNT(abstractReview.id) AS totalReviewCount,
																			   
																			   abstractReview.abstract_id 
																		   
																		  FROM "._DB_ABSTRACT_REVIEW_RESULT_." abstractReview 
																	
																	
																		 
																		 WHERE abstractReview.status = 'A' 
																	  
																	  GROUP BY abstractReview.abstract_id 
																   ) abstractReview 
																ON abstractReview.abstract_id = abstractRequest.id 
																
												   
												    WHERE abstractRequest.status = 'A'
													AND abstractRequest.id=".$abstractId ."";
													
												
		$resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);
		$rowAbstractDetails            = $resultAbstractDetails[0];
		
	?>
		<style>
			.tborder td, .tborder th {
				font-size: 14px;
			}
		</style>
		<form name="frmAbstractReview" id="frmAbstractReview" action="all_abstract.free_paper.process.php" method="post">
			<input type="hidden" name="act" value="review" />
			<input type="hidden" name="abstract_id" value="<?=$rowAbstractDetails['id']?>" />
			<?php
			foreach($searchArray as $keyString=>$valString)
			$ReviewCount['QUERY'] = "SELECT COUNT(*) AS totalReviewAttemptCount FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
															 
													WHERE abstract_id =".$rowAbstractDetails['id'].""; 
															 
													$resultReviewCount         = $mycms->sql_select($ReviewCount);
													$rowReviewCount            = $resultReviewCount[0];
			{
				if($valString!="")
				{
			?>
					<input type="hidden" name="<?=$keyString?>" id="<?=$keyString?>" value="<?=$valString?>" />
			<?php
				}
			}
			?>
			<table width="100%" class="tborder">
				<tr>
					<td colspan="2" align="left" class="tcat">Abstract Review Panel</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<a href="all_abstract.free_paper.php?<?=$searchString?>">Back To Abstract Listing</a>
					</td>
				</tr>
				<tr>
					<td width="65%" style="margin:0px; padding:0px;" class="tborder" valign="top">
						
						<table width="100%">
							<tr>
								<td colspan="2" align="left" class="thighlight">Basic Details</td>
							</tr>
							<tr>
								<td width="30%" align="left">Abstract Submission Code</td>
								<td align="left"><?=strtoupper($rowAbstractDetails['abstract_submition_code'])?></td>
							</tr>
							<tr>
								<td align="left">Submission Date</td>
								<td align="left"><?=setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D")?></td>
							</tr>
							<tr>
								<td align="left">Preference</td>
								<td align="left">
									<?php
									$abstractType     = "";
									
									if($rowAbstractDetails['abstract_child_type']=="ORAL")
									{
										$abstractType = "ORAL";
									}
									else if($rowAbstractDetails['abstract_child_type']=="POSTER")
									{
										$abstractType = "POSTER";
									}
									else if($rowAbstractDetails['abstract_child_type']=="NONE")
									{
										$abstractType = "NO CHOICE";
									}
									
									echo $abstractType;
									?>
								</td>
							</tr>
						</table>
						
						<table width="100%">
							<tr>
								<td colspan="2" align="left" class="thighlight">Abstract Details</td>
							</tr>
							<tr>
								<td width="30%" align="left">Abstract Topic</td>
								<td align="left"><?=$rowAbstractDetails['abstract_topic']?></td>
							</tr>
							<tr>
								<td align="left" valign="top">Abstract Title</td>
								<td align="left" valign="top"><?=escapeHTMLSpecialCharacter($rowAbstractDetails['abstract_title'])?></td>
							</tr>
							<tr class="tlisting">
								<td align="left" valign="top">Abstract Title Word Count</td>
								<td align="left" valign="top">
									<b><?=str_word_count($rowAbstractDetails['abstract_title'])?></b>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Background &amp; Aims</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount   = 0;
									$totalWordCount  += str_word_count($rowAbstractDetails['abstract_background_aims']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAbstractDetails['abstract_background_aims']));
									?>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Material &amp; Methods</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($rowAbstractDetails['abstract_material_methods']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAbstractDetails['abstract_material_methods']));
									?>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Results</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($rowAbstractDetails['abstract_results']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAbstractDetails['abstract_results']));
									?>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Conclusion</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($rowAbstractDetails['abstract_conclution']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAbstractDetails['abstract_conclution']));
									?>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Total Word Count</td>
								<td align="left" valign="top">
									<b><?=$totalWordCount?></b>
								</td>
							</tr>
						</table>
							
					</td>
					<td width="35%" style="margin:0px; padding:0px;" class="tborder" valign="top">
						
						<table width="100%">
							<tr>
								<td align="left" class="thighlight">Scoring Section</td>
							</tr>
						</table>
						
						<?php
						if($rowReviewCount['totalReviewAttemptCount']<2)
						{
							abstractReviewUpdatePanel($rowAbstractDetails['id'], $rowAbstractDetails);
						}
						else
						{
							abstractReviewDisplayPanel($rowAbstractDetails['id'], $rowAbstractDetails);
						}
						?>
						
						<table width="100%">
							<tr>
								<td align="left">
									
									<input type="button" name="bttnWindowView" id="bttnWindowView" value="<?=($rowAbstractDetails ['totalReviewAttemptCount']<2)?'Cancel':'Back'?>" 
									 class="btn btn-medium btn-red" onclick="window.location.href='all_abstract.free_paper.php?<?=$searchString?>'" style=" margin:10px 10px 10px 0;" />
									
									<?php
									if($rowReviewCount['totalReviewAttemptCount']<2)
									{
									?>
										<input type="submit" name="submitWindowView" id="submitWindowView" class="btn btn-medium btn-blue"  
										 value="<?=($rowReviewCount['totalReviewAttemptCount']==0)?'Save':'Update'?>" style=" margin:10px 10px 10px 0;" />
									<?php
									}
									?>
									
								</td>
							</tr>
						</table>
							
					</td>
				</tr>
				<tr>
					<td colspan="2" class="tfooter">&nbsp;</td>
				</tr>
			</table>
		</form>
	<?php
	}
?>