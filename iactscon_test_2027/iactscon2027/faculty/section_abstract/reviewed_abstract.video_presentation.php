<?php
	include_once('includes/init.php');
	
	page_header("Video Presentation");
	
	$pageKey                       		       		 = "_pgn1_";
	$pageKeyVal                    		       		 = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString                 		       		 = "";
	$searchArray                   		       		 = array();
	
	$searchArray[$pageKey]         		       		 = $pageKeyVal;
	$searchArray['src_abstract_submission_code']  	 = trim($_REQUEST['src_abstract_submission_code']);
	$searchArray['src_abstract_topic_id']  		 	 = trim($_REQUEST['src_abstract_topic_id']);
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
?>
	<script language="javascript" src="scripts/abstract.video_presentation.js"></script>
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
		<form name="frmSearch" id="frmSearch" action="reviewed_abstract.video_presentation.process.php" method="post">
			<input type="hidden" name="act" value="search_abstract" />
			<table width="100%" class="tborder">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Reviewed List</span>
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
									<td align="left" width="150">Abstract Topic:</td>
									<td align="left" width="250">
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
									<td align="right">
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Search" 
										 class="btn btn-small btn-blue" />
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
							$searchCondition              .= " AND grandTAB.abstract_parent_type = 'VIDEO_PRESENTATION' 
							                                   AND grandTAB.abstractReviewId > 0";
															   
							
							
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
							
							$searchCondition              .= " ORDER BY grandTAB.marks_obtained DESC, grandTAB.id ASC";
							
							$sqlAbstractDetails            = abstractFacultyPanelDetailsQueryset("", $searchCondition);
							$resultAbstractDetails         = $mycms->pagination(1, $sqlAbstractDetails, 25, $restrt);	
							if($resultAbstractDetails)
							{
								foreach($resultAbstractDetails as $i=>$rowAbstractDetails) 
								{
									$counter++;
									
									$rowStyleDecission     = "";
									
									if($rowAbstractDetails['abstractReviewId']==0)
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
										if($rowAbstractDetails['abstractReviewId']>0)
										{
											echo $rowAbstractDetails['marks_obtained'];
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
										if($rowAbstractDetails['totalReviewAttemptCount']==0)
										{
										?>
											<a href="reviewed_abstract.video_presentation.php?show=view&id=<?=$rowAbstractDetails['id'].$searchString?>">Review</a>
										<?php
										}
										else if($rowAbstractDetails['totalReviewAttemptCount']<2)
										{
										?>
											<a href="reviewed_abstract.video_presentation.php?show=view&id=<?=$rowAbstractDetails['id'].$searchString?>">Update Review</a>
										<?php
										}
										else
										{
										?>
											<a href="reviewed_abstract.video_presentation.php?show=view&id=<?=$rowAbstractDetails['id'].$searchString?>">View</a>
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
		
		
		$sqlAbstractDetails            = abstractFacultyPanelDetailsQueryset($abstractId, $searchCondition);
		$resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);
		$rowAbstractDetails            = $resultAbstractDetails[0];
	?>
		<style>
			.tborder td, .tborder th {
				font-size: 14px;
			}
		</style>
		<form name="frmAbstractReview" id="frmAbstractReview" action="reviewed_abstract.video_presentation.process.php" method="post">
			<input type="hidden" name="act" value="review" />
			<input type="hidden" name="abstract_id" value="<?=$rowAbstractDetails['id']?>" />
			<?php
			foreach($searchArray as $keyString=>$valString)
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
						<a href="reviewed_abstract.video_presentation.php?<?=$searchString?>">Back To Abstract Listing</a>
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
								<td align="left" valign="top">Description</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount   = 0;
									$totalWordCount  += str_word_count($rowAbstractDetails['abstract_video_presentation_description']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAbstractDetails['abstract_video_presentation_description']));
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
						if($rowAbstractDetails['totalReviewAttemptCount']<2)
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
									
									<input type="button" name="bttnWindowView" id="bttnWindowView" value="<?=($rowAbstractDetails['totalReviewAttemptCount']<2)?'Cancel':'Back'?>" 
									 class="btn btn-medium btn-red" onclick="window.location.href='reviewed_abstract.video_presentation.php?<?=$searchString?>'" style=" margin:10px 10px 10px 0;" />
									
									<?php
									if($rowAbstractDetails['totalReviewAttemptCount']<2)
									{
									?>
										<input type="submit" name="submitWindowView" id="submitWindowView" class="btn btn-medium btn-blue"  
										 value="<?=($rowAbstractDetails['totalReviewAttemptCount']==0)?'Save':'Update'?>" style=" margin:10px 10px 10px 0;" />
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