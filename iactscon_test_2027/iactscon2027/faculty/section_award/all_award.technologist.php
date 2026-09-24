<?php
	include_once('includes/init.php');
	
	page_header("Technologist");
	
	$pageKey                       		       		 = "_pgn1_";
	$pageKeyVal                    		       		 = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString                 		       		 = "";
	$searchArray                   		       		 = array();
	
	$searchArray[$pageKey]         		       		 = $pageKeyVal;
	$searchArray['src_award_submission_code']  	     = trim($_REQUEST['src_award_submission_code']);
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
?>
	<script language="javascript" src="scripts/award.technologist.js"></script>
	<div class="container">
		<?php 
		switch($show){
			
			// AWARD VIEW DETAILS WINDOW
			case'view':
			
				awardReviewDetailsWindow($cfg, $mycms);
				break;	
														
			// AWARD LISTING DETAILS WINDOW
			default:
				
				awardListingDisplayWindow($cfg, $mycms); 
				break;				
		
		}
		?>
	</div>
<?php

	page_footer();

	/****************************************************************************/
	/*                       AWARD LISTING DETAILS WINDOW                       */
	/****************************************************************************/
	function awardListingDisplayWindow($cfg, $mycms)
	{
		global $searchString, $searchArray;
	?>
		<form name="frmSearch" id="frmSearch" action="all_award.technologist.process.php" method="post">
			<input type="hidden" name="act" value="search_award" />
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
									<td align="right">
										
										Nomination Code:
										
										<input type="text" name="src_award_submission_code" id="src_award_submission_code" 
										 style="width:150px; text-transform:uppercase;" value="<?=$_REQUEST['src_award_submission_code']?>" />
										 
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
								<td width="100" align="center">Nomination Code</td>
								<td width="120" align="center">Submission Date</td>
								<td align="left">Award Title</td>
								<td width="100" align="center">Review Result</td>
								<td width="85" align="center">Action</td>
							</tr>
							<?php
							$counter                       = 0;
							
							$searchCondition               = "";
							$searchCondition              .= " AND grandTAB.award_category = 'AWARD_TECHNOLOGIST'";
							
							if($_REQUEST['src_award_submission_code']!="")
							{
								$searchCondition          .= " AND grandTAB.submission_code LIKE '%".addslashes(trim($_REQUEST['src_award_submission_code']))."%'";
							}
							
							$searchCondition              .= " ORDER BY grandTAB.id ASC";
							
							$sqlAwardDetails               = awardFacultyPanelDetailsQueryset("", $searchCondition);
							$resultAwardDetails            = $mycms->pagination(1, $sqlAwardDetails, 25, $restrt);	
							if($resultAwardDetails)
							{
								foreach($resultAwardDetails as $i=>$rowAwardDetails) 
								{
									$counter++;
									
									$rowStyleDecission     = "";
									
									if($rowAwardDetails['awardReviewId']==0)
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
										<td align="center" valign="top"><?=$rowAwardDetails['submission_code']?></td>
										<td align="center" valign="top"><?=setDateTimeFormat($rowAwardDetails['created_dateTime'], "D")?></td>
										<td align="left" valign="top"><?=escapeHTMLSpecialCharacter($rowAwardDetails['award_title'])?></td>
										<td align="center" valign="top">
										<?php
										if($rowAwardDetails['awardReviewId']>0)
										{
											echo $rowAwardDetails['marks_obtained'];
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
										if($rowAwardDetails['totalReviewAttemptCount']==0)
										{
										?>
											<a href="all_award.technologist.php?show=view&id=<?=$rowAwardDetails['id'].$searchString?>">Review</a>
										<?php
										}
										else if($rowAwardDetails['totalReviewAttemptCount']<2)
										{
										?>
											<a href="all_award.technologist.php?show=view&id=<?=$rowAwardDetails['id'].$searchString?>">Update Review</a>
										<?php
										}
										else
										{
										?>
											<a href="all_award.technologist.php?show=view&id=<?=$rowAwardDetails['id'].$searchString?>">View</a>
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
	/*                        AWARD VIEW DETAILS WINDOW                         */
	/****************************************************************************/
	function awardReviewDetailsWindow($cfg, $mycms)
	{
		global $searchString, $searchArray;
		
		$awardId 				    = addslashes(trim($_REQUEST['id']));
		
		$searchCondition            = "";
		$searchCondition           .= " AND grandTAB.award_category = 'AWARD_TECHNOLOGIST'";
		
		$sqlAwardDetails            = awardFacultyPanelDetailsQueryset($awardId, $searchCondition);
		$resultAwardDetails         = $mycms->sql_select($sqlAwardDetails);
		$rowAwardDetails            = $resultAwardDetails[0];
	?>
		<style>
			.tborder td, .tborder th {
				font-size: 14px;
			}
		</style>
		<form name="frmAwardReview" id="frmAwardReview" action="all_award.technologist.process.php" method="post">
			<input type="hidden" name="act" value="review" />
			<input type="hidden" name="award_id" value="<?=$rowAwardDetails['id']?>" />
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
					<td colspan="2" align="left" class="tcat">Award Review Panel</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<a href="all_award.technologist.php?<?=$searchString?>">Back To Award Listing</a>
					</td>
				</tr>
				<tr>
					<td width="65%" style="margin:0px; padding:0px;" class="tborder" valign="top">
						
						<table width="100%">
							<tr>
								<td colspan="2" align="left" class="thighlight">Basic Details</td>
							</tr>
							<tr>
								<td width="30%" align="left">Nomination Code</td>
								<td align="left"><?=strtoupper($rowAwardDetails['submission_code'])?></td>
							</tr>
							<tr>
								<td align="left">Submission Date</td>
								<td align="left"><?=setDateTimeFormat($rowAwardDetails['created_dateTime'], "D")?></td>
							</tr>
						</table>
						
						<table width="100%">
							<tr>
								<td colspan="2" align="left" class="thighlight">Award Details</td>
							</tr>
							<tr>
								<td width="30%" align="left" valign="top">Award Title</td>
								<td align="left" valign="top"><?=escapeHTMLSpecialCharacter($rowAwardDetails['award_title'])?></td>
							</tr>
							<tr class="tlisting">
								<td align="left" valign="top">Title Word Count</td>
								<td align="left" valign="top">
									<b><?=str_word_count($rowAwardDetails['award_title'])?></b>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Background &amp; Aims</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount   = 0;
									$totalWordCount  += str_word_count($rowAwardDetails['award_background_aims']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAwardDetails['award_background_aims']));
									?>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Material &amp; Methods</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($rowAwardDetails['award_material_methods']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAwardDetails['award_material_methods']));
									?>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Results</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($rowAwardDetails['award_results']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAwardDetails['award_results']));
									?>
								</td>
							</tr>
							<tr>
								<td align="left" valign="top">Conclusion</td>
								<td align="left" valign="top">
									<?php
									$totalWordCount  += str_word_count($rowAwardDetails['award_conclution']);
									
									echo escapeHTMLSpecialCharacter(nl2br($rowAwardDetails['award_conclution']));
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
						if($rowAwardDetails['totalReviewAttemptCount']<2)
						{
							awardReviewUpdatePanel($rowAwardDetails['id'], $rowAwardDetails);
						}
						else
						{
							awardReviewDisplayPanel($rowAwardDetails['id'], $rowAwardDetails);
						}
						?>
						
						<table width="100%">
							<tr>
								<td align="left">
									
									<input type="button" name="bttnWindowView" id="bttnWindowView" value="<?=($rowAwardDetails['totalReviewAttemptCount']<2)?'Cancel':'Back'?>" 
									 class="btn btn-medium btn-red" onclick="window.location.href='all_award.technologist.php?<?=$searchString?>'" style=" margin:10px 10px 10px 0;" />
									
									<?php
									if($rowAwardDetails['totalReviewAttemptCount']<2)
									{
									?>
										<input type="submit" name="submitWindowView" id="submitWindowView" class="btn btn-medium btn-blue"  
										 value="<?=($rowAwardDetails['totalReviewAttemptCount']==0)?'Save':'Update'?>" style=" margin:10px 10px 10px 0;" />
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