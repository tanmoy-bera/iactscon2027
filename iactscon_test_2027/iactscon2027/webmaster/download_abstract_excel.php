<?php
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
	
	ini_set('max_execution_time', 9000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=abstractexcelreport".time().".xls");
	
	$indexVal          								 = 1;
	$pageKey                       		       		 = "_pgn_";
	$pageKeyVal                    		       		 = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString                 		       		 = "";
	$searchArray                   		       		 = array();
	
	$searchArray[$pageKey]         		       		 = $pageKeyVal;
	$searchArray['goto']  						 	 = trim($_REQUEST['goto']);
	$searchArray['src_full_name']  		             = trim($_REQUEST['src_full_name']);
	$searchArray['src_applicant_middle_name']  		 = trim($_REQUEST['src_applicant_middle_name']);
	$searchArray['src_applicant_last_name']  		 = trim($_REQUEST['src_applicant_last_name']);
	$searchArray['src_applicant_unique_sequence']  	 = trim($_REQUEST['src_applicant_unique_sequence']);
	$searchArray['src_abstract_submission_code']  	 = trim($_REQUEST['src_abstract_submission_code']);
	$searchArray['src_applicant_email_id']  		 = trim($_REQUEST['src_applicant_email_id']);
	$searchArray['src_paper_presentation_category']  = trim($_REQUEST['src_paper_presentation_category']);
	$searchArray['src_apply_from_date']  			 = trim($_REQUEST['src_apply_from_date']);
	$searchArray['src_apply_to_date']  				 = trim($_REQUEST['src_apply_to_date']);
	$searchArray['src_abstract_topic_id']  			 = trim($_REQUEST['src_abstract_topic_id']);
	$searchArray['src_abstract_award_id']  			 = trim($_REQUEST['src_abstract_award_id']);
	$searchArray['src_type']  			 			 = trim($_REQUEST['src_type']);
	$searchArray['src_presentation_type']  			 = trim($_REQUEST['src_presentation_type']);
	$searchArray['src_allocated']  					 = trim($_REQUEST['src_allocated']);
	$searchArray['src_abstract_category_id']  		 = trim($_REQUEST['src_abstract_category_id']);
		
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
	
	switch($show){
		
		case'meritList':			
			meritListExcel($cfg, $mycms);
			break;
		
		// ABSTRACT LISTING DETAILS WINDOW
		default:				
			abstractExcel($cfg, $mycms); 
			break;
	}
	exit();	
	
	function meritListExcel($cfg, $mycms)
	{
				global $searchString, $searchArray, $searchStatus;
				
				if($_REQUEST['src_registration_id']!="")
				{
					$searchCondition          .= " AND registeredDelegates.user_registration_id LIKE '%".$_REQUEST['src_registration_id']."%'";
				}
				if($_REQUEST['src_full_name']!="")
				{
					$searchCondition          .= " AND registeredDelegates.user_full_name LIKE '%".$_REQUEST['src_full_name']."%'";
				}
				if($_REQUEST['src_applicant_unique_sequence']!="")
				{
					$searchCondition          .= " AND registeredDelegates.user_unique_sequence LIKE '%".$_REQUEST['src_applicant_unique_sequence']."%'";
				}
				if($_REQUEST['src_abstract_submission_code']!="")
				{
					$searchCondition          .= " AND abstractRequest.abstract_submition_code LIKE '%".$_REQUEST['src_abstract_submission_code']."%'";
				}
				if($_REQUEST['src_applicant_email_id']!="")
				{
					$searchCondition          .= " AND registeredDelegates.user_email_id LIKE '%".$_REQUEST['src_applicant_email_id']."%'";
				}
				if($_REQUEST['src_apply_from_date']!="" && $_REQUEST['src_apply_to_date']=="")
				{
					$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '".$_REQUEST['src_apply_from_date']."'  AND '3000-01-01'";
				}
				if($_REQUEST['src_apply_from_date']=="" && $_REQUEST['src_apply_to_date']!="")
				{
					$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '2000-01-01'  AND '".$_REQUEST['src_apply_to_date']."'";
				}
				if($_REQUEST['src_apply_from_date']!="" && $_REQUEST['src_apply_to_date']!="")
				{
					$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '".$_REQUEST['src_apply_from_date']."'  AND '".$_REQUEST['src_apply_to_date']."'";
				}
				if($_REQUEST['src_paper_presentation_category']!="")
				{
					$searchCondition          .= " AND abstractRequest.abstract_child_type = '".$_REQUEST['src_paper_presentation_category']."'";
				}
				if($_REQUEST['src_abstract_topic_id']!="")
				{
					$searchCondition          .= " AND abstractRequest.abstract_topic_id = '".$_REQUEST['src_abstract_topic_id']."'";
				}
				if($_REQUEST['src_abstract_category_id']!="")
				{
					$searchCondition          .= " AND abstractRequest.abstract_cat = '".$_REQUEST['src_abstract_category_id']."'";
				}
				if($_REQUEST['src_presentation_type']!="")
				{
					$searchCondition          .= " AND ( (abstractRequest.abstract_child_type = '".$_REQUEST['src_presentation_type']."' AND abstractRequest.abstract_presentation_decision = 'DEFAULT')
														  OR abstractRequest.abstract_presentation_decision = '".$_REQUEST['src_presentation_type']."')";
				}
				if($_REQUEST['src_abstract_award_id']!="")
				{
					if($_REQUEST['src_abstract_award_id']=='NULL')
					{
						$searchCondition          .= " AND abstractRequest.id NOT IN ( SELECT submission_id FROM "._DB_AWARD_REQUEST_." WHERE status = 'A' )";
					}
					else
					{
						$searchCondition          .= " AND abstractRequest.id IN ( SELECT submission_id FROM "._DB_AWARD_REQUEST_." WHERE award_id = '".$_REQUEST['src_abstract_award_id']."' AND status = 'A' )";
					}
				}
				if($_REQUEST['src_type']!="")
				{
					$searchCondition          .= " AND abstractRequest.abstract_parent_type = '".$_REQUEST['src_type']."'";
				}
				if($_REQUEST['src_allocated']!="" && $_REQUEST['src_allocated'] == 'Y')
				{
					$searchCondition          .= " AND abstractRequest.id IN ( SELECT abstract_id FROM "._DB_ABSTRACT_ALLOTMENT_." )";
				}
				if($_REQUEST['src_allocated']!="" && $_REQUEST['src_allocated'] == 'N')
				{
					$searchCondition          .= " AND abstractRequest.id NOT IN ( SELECT abstract_id FROM "._DB_ABSTRACT_ALLOTMENT_." )";
				}
				
				
				$recordSet 			= array();
				
				$sorter	 			= array();
				$unsorter			= array();
				
				$sortedRecordSet 	= array();
				
				$abstractCounter               = 0;
				//$sqlAbstractDetails			   = abstractDetailsQuerySet("",$searchCondition);
				
				//$resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);

				/*$sqlFetchval = array();
				$sqlFetchval['QUERY'] = "SELECT registeredDelegates.user_full_name, AC.category,abstractRequest.abstract_title,abstractRequest.id
											  FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 
											  LEFT OUTER JOIN "._DB_ABSTRACT_REVIEW_RESULT_." RR
											  	 ON RR.abstract_id = abstractRequest.id
											  LEFT OUTER JOIN "._DB_FACULTY_ACCOUNT_." F
											  	 ON F.id = RR.faculty_id	 
											   LEFT OUTER JOIN "._DB_ABSTRACT_TOPIC_CATEGORY_." AC 
											   	 ON AC.id = abstractRequest.abstract_cat	
											   LEFT OUTER JOIN "._DB_USER_REGISTRATION_." registeredDelegates
											   	 ON registeredDelegates.id = abstractRequest.applicant_id
											 WHERE RR.status = 'A' AND abstractRequest.status = 'A' AND `AC`.status = 'A' AND registeredDelegates.status = 'A' AND F.status = 'A' AND RR.marks_obtained>0 ORDER BY RR.marks_obtained DESC ";


				$resultReview = $mycms->sql_select($sqlFetchval);	*/

				$sqlAbstractDetails   = array();
			$sqlAbstractDetails['QUERY']  ="					SELECT abstractRequest.*,
																	   abstractTopic.abstract_topic,
																	   
																	   registeredDelegates.id AS delegate_id,
																	   registeredDelegates.user_email_id,
																	   registeredDelegates.user_mobile_no,
																	   registeredDelegates.user_unique_sequence,
																	   registeredDelegates.user_registration_id,
																	   registeredDelegates.account_status,
																	   
																	   registeredDelegates.user_institute_name AS delegateInstitute,
																	   registeredDelegates.user_department AS delegateDepertment,
																	   registeredDelegates.user_designation AS delegateDesignation,
																	   
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
																	   IFNULL(abstractRequest.abstract_submition_code, '') AS abstract_submition_code,
																	   
																	   abstractAward.award_ids AS award_ids, 
																	   abstractAward.award_names AS award_names
									
																  FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 
																  
													   LEFT OUTER JOIN "._DB_ABSTRACT_TOPIC_." abstractTopic 
																	ON abstractRequest.abstract_topic_id = abstractTopic.id 
																	
													   LEFT OUTER JOIN ( SELECT GROUP_CONCAT(award_id) AS award_ids, GROUP_CONCAT(award_name) AS award_names, req.submission_id AS submission_id
													   					   FROM "._DB_AWARD_REQUEST_." req
																	 INNER JOIN "._DB_AWARD_MASTER_." awrd
																	 		 ON awrd.id = req.award_id
																		  WHERE req.status = 'A'
																	   GROUP BY req.submission_id) abstractAward 
																	ON abstractRequest.id = abstractAward.submission_id 
																	
													   LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
																	ON abstractRequest.abstract_author_country_id = country.country_id
																 
													   LEFT OUTER JOIN "._DB_COMN_STATE_." state
																	ON abstractRequest.abstract_author_state_id = state.st_id
													   
													   LEFT OUTER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
																	ON abstractRequest.applicant_id = registeredDelegates.id 
													   
													   
																  WHERE abstractRequest.status = 'A' AND registeredDelegates.status = 'A'  ".$filterCondition." ".$searchCondition." 
															
															  ORDER BY abstractRequest.id DESC ".$limitCondition."";

				//echo '<pre>'; print_r($sqlFetchval); die;

				$resultReview = $mycms->sql_select($sqlAbstractDetails);

				

				$recordsWithMarks = array();

				foreach ($resultReview as $i => $rowRecordSet) {
				    $sqlReview = array();
				    $sqlReview['QUERY'] = "SELECT SUM(R.`marks_obtained`) MARKS, COUNT(R.`faculty_id`) COUNTDATA
				                           FROM " . _DB_ABSTRACT_REVIEW_RESULT_ . " R INNER JOIN " . _DB_FACULTY_ACCOUNT_ . " F ON R.faculty_id = F.id
				                           WHERE R.abstract_id = '" . $rowRecordSet['id'] . "' AND R.status='A' AND F.status='A'";
				    $resultReview = $mycms->sql_select($sqlReview);

				    $totalMarks = $resultReview[0]['MARKS'];
				    $totalReviewer = $resultReview[0]['COUNTDATA'];

				    if (!empty($totalMarks) && $totalReviewer > 0) {
				        $totalAvg = floatval($totalMarks / $totalReviewer);
				        
				        // Store the record along with its total marks in a new array
				        $recordsWithMarks[] = array(
				            'record' => $rowRecordSet,
				            'totalMarks' => $totalMarks,
				        );
				    }
				}

		// Sort the records by total marks in descending order
			usort($recordsWithMarks, function ($a, $b) {
			    return $b['totalMarks'] - $a['totalMarks'];
			});



				//echo '<pre>'; print_r($recordsWithMarks); die;		
				
		?>
			<table border="1">
				<tr>
					<td colspan="12" align="left">
					<h4 style="color:#000000;">Merit List</h4>
					</td>
				</tr>
				<tr class="theader">
					<td width="30" align="center"><strong>Sl No</strong></td>
					<td align="left" width="230"><b>Name</b></td>
					<td align="left" width="230"><b>Email Address</b></td>
					<td align="left" width="230"><b>Phone Number</b></td>
					<td align="left" width="100"><b>Submission Code</b></td>
					<td align="left" width="100"><b>Category</b></td>
					<td align="left" width="100"><b>Sub Category</b></td>
					<td align="left" width="100"><b>Sub Sub Category</b></td>
					<td align="left" width="100"><b>Abstract Title</b></td>
					<td align="left" width="100"><b>Co-author</b></td>
					<td align="left" width="100"><b>Total Marks</b></td>
					<td align="left" width="100"><b>Avg. Marks</b></td>
					
				</tr>
				<?php		
				if(sizeof($recordsWithMarks)>0)
				{
					foreach($recordsWithMarks as $i=>$rowRecordSet) 
					{
						
						//echo '<pre>'; print_r($rowRecordSet['record']);
						
						$abstractCounter++;

						$coAuthorCounter           = 0;
							
						$sqlAbstractCoAuthor            = array();
						$sqlAbstractCoAuthor['QUERY']   = "SELECT coauthor.*, 
															 
															 country.country_name AS coauthor_country_name,
															 state.state_name AS coauthor_state_name
															 
														FROM "._DB_ABSTRACT_COAUTHOR_." coauthor 
													
											 LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
														  ON coauthor.abstract_coauthor_country_id = country.country_id
												 
											 LEFT OUTER JOIN "._DB_COMN_STATE_." state
														  ON coauthor.abstract_coauthor_state_id = state.st_id	
															  
													   WHERE coauthor.status = ?
														 AND coauthor.abstract_id = ?";
													 
							$sqlAbstractCoAuthor['PARAM'][]   = array('FILD' => 'coauthor.status',  	   'DATA' =>'A',                        'TYP' => 's');
							$sqlAbstractCoAuthor['PARAM'][]   = array('FILD' => 'coauthor.abstract_id',    'DATA' =>$rowRecordSet['record']['id'],  'TYP' => 's');
																	
							$resultAbstractCoAuthor    = $mycms->sql_select($sqlAbstractCoAuthor);

					
							$sqlReview	= array();
								$sqlReview['QUERY'] 			= " SELECT SUM(R.`marks_obtained`) MARKS, COUNT(R.`faculty_id`) COUNTDATA
																	  FROM "._DB_ABSTRACT_REVIEW_RESULT_." R INNER JOIN "._DB_FACULTY_ACCOUNT_." F ON R.faculty_id = F.id
																	 WHERE R.abstract_id = '".$rowRecordSet['record']['id']."' AND R.status='A' AND F.status='A'";
														 
							$resultReview = $mycms->sql_select($sqlReview);	

							//echo '<pre>'; print_r($resultReview[0]['']);
							

							$totalMarks = $resultReview[0]['MARKS'];
							$totalReviewer = $resultReview[0]['COUNTDATA'];

							
							if(!empty($totalMarks) && $totalReviewer>0)
							{
								 $totalAvg = floatval($totalMarks/$totalReviewer);
							}

							$sqlFetchCat			  =	array();
							$sqlFetchCat['QUERY']	  = "SELECT category FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
													  	WHERE id = ? AND status = 'A'";
							$sqlFetchCat['PARAM'][]   = array('FILD' => 'id',  'DATA' =>$rowRecordSet['record']['abstract_cat'],  'TYP' => 's');	
							$resultCat = $mycms->sql_select($sqlFetchCat,false);	

							//echo '<pre>'; print_r($rowRecordSet['record']);

							$sqlAbstractSUbcat			  =	array();
							$sqlAbstractSUbcat['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_SUBMISSION_." 
															  WHERE `status` = 'A' AND id='".$rowRecordSet['record']['abstract_parent_type']."'
														   ORDER BY `id` ASC";
							
								
						    $resultAbstractSubCat = $mycms->sql_select($sqlAbstractSUbcat);

						    $sqlAbstractSUbSubcat			  =	array();
							$sqlAbstractSUbSubcat['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_PRESENTATION_." 
															  WHERE `status` = 'A' AND id='".$rowRecordSet['record']['abstract_child_type']."'
														   ORDER BY `id` ASC";
							
								
						    $resultAbstractSubSubCat = $mycms->sql_select($sqlAbstractSUbSubcat);		
																	
						
				?>
				<tr class="tlisting" <?=$rowStyleDecission?>>
					<td align="center" valign="top"><?=$abstractCounter?></td>
					<td align="left" valign="top"><?=$rowRecordSet['record']['applicant_first_name']." ".$rowRecordSet['record']['applicant_last_name'];?></td>
					<td align="left" valign="top"><?=$rowRecordSet['record']['user_email_id'];?></td>
					<td align="left" valign="top"><?=$rowRecordSet['record']['user_mobile_no'];?></td>
					<td align="left" valign="top"><?=$rowRecordSet['record']['abstract_submition_code'];?></td>
					<td align="left" valign="top"><?=$resultCat[0]['category'];?></td>
					<td align="left" valign="top"><?=$resultAbstractSubCat[0]['abstract_submission'];?></td>
					<td align="left" valign="top"><?=$resultAbstractSubSubCat[0]['abstract_presentation'];?></td>
					<td align="left" valign="top"><?=$rowRecordSet['record']['abstract_title'];?></td>
					<td align="center" valign="top">
						<?php
						foreach($resultAbstractCoAuthor as $keyAbstractCoAuthor=>$rowAbstractCoAuthor)
						{
							echo strtoupper($rowAbstractCoAuthor['abstract_coauthor_name'])."<br><br>";
						}
						?>

					</td>
					<td align="center" valign="top">
					<?php
					 echo $totalMarks;
					?>	
					</td>
					<td align="center" valign="top"><?=$totalAvg?></td>
				</tr>
				<?php
					}
				}
				else 
				{
				?>
				<tr>
					<td colspan="8" align="center">
						<span class="mandatory">No Record Present.</span>												
					</td>
				</tr>  
				<?php 
				} 
				?>
				<tr>
					<td  colspan="12" align="left">
						<h3>Excel Download Date and Time : <?=date('d/m/Y h:i A')?>	</h3>											
					</td>
			  	</tr> 
			</table>
		<?
	}
	
	
	function abstractExcel($cfg, $mycms)
	{
		global $searchString, $searchArray, $searchStatus;
		
		if($_REQUEST['src_registration_id']!="")
		{
			$searchCondition          .= " AND registeredDelegates.user_registration_id LIKE '%".$_REQUEST['src_registration_id']."%'";
		}
		if($_REQUEST['src_full_name']!="")
		{
			$searchCondition          .= " AND registeredDelegates.user_full_name LIKE '%".$_REQUEST['src_full_name']."%'";
		}
		if($_REQUEST['src_applicant_unique_sequence']!="")
		{
			$searchCondition          .= " AND registeredDelegates.user_unique_sequence LIKE '%".$_REQUEST['src_applicant_unique_sequence']."%'";
		}
		if($_REQUEST['src_abstract_submission_code']!="")
		{
			$searchCondition          .= " AND abstractRequest.abstract_submition_code LIKE '%".$_REQUEST['src_abstract_submission_code']."%'";
		}
		if($_REQUEST['src_applicant_email_id']!="")
		{
			$searchCondition          .= " AND registeredDelegates.user_email_id LIKE '%".$_REQUEST['src_applicant_email_id']."%'";
		}
		if($_REQUEST['src_apply_from_date']!="" && $_REQUEST['src_apply_to_date']=="")
		{
			$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '".$_REQUEST['src_apply_from_date']."'  AND '3000-01-01'";
		}
		if($_REQUEST['src_apply_from_date']=="" && $_REQUEST['src_apply_to_date']!="")
		{
			$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '2000-01-01'  AND '".$_REQUEST['src_apply_to_date']."'";
		}
		if($_REQUEST['src_apply_from_date']!="" && $_REQUEST['src_apply_to_date']!="")
		{
			$searchCondition          .= " AND abstractRequest.created_dateTime BETWEEN '".$_REQUEST['src_apply_from_date']."'  AND '".$_REQUEST['src_apply_to_date']."'";
		}
		if($_REQUEST['src_paper_presentation_category']!="")
		{
			//$searchCondition          .= " AND abstractRequest.abstract_child_type = '".$_REQUEST['src_paper_presentation_category']."'";
			$searchCondition          .= " AND abstractRequest.abstract_cat = '".$_REQUEST['src_paper_presentation_category']."'";
		}
		if($_REQUEST['src_abstract_topic_id']!="")
		{
			$searchCondition          .= " AND abstractRequest.abstract_topic_id = '".$_REQUEST['src_abstract_topic_id']."'";
		}
		if($_REQUEST['src_presentation_type']!="")
		{
			//$searchCondition          .= " AND ( (abstractRequest.abstract_child_type = '".$_REQUEST['src_presentation_type']."' AND abstractRequest.abstract_presentation_decision = 'DEFAULT')
			$searchCondition          .= " AND ( (abstractRequest.abstract_cat = '".$_REQUEST['src_presentation_type']."' AND abstractRequest.abstract_presentation_decision = 'DEFAULT')
												  OR abstractRequest.abstract_presentation_decision = '".$_REQUEST['src_presentation_type']."')";
		}
		if($_REQUEST['src_abstract_award_id']!="")
		{
			if($_REQUEST['src_abstract_award_id']=='NULL')
			{
				$searchCondition          .= " AND abstractRequest.id NOT IN ( SELECT submission_id FROM "._DB_AWARD_REQUEST_." WHERE status = 'A' )";
			}
			else
			{
				$searchCondition          .= " AND abstractRequest.id IN ( SELECT submission_id FROM "._DB_AWARD_REQUEST_." WHERE award_id = '".$_REQUEST['src_abstract_award_id']."' AND status = 'A' )";
			}
		}
		if($_REQUEST['src_type']!="")
		{
			$searchCondition          .= " AND abstractRequest.abstract_parent_type = '".$_REQUEST['src_type']."'";
		}

		if($_REQUEST['src_abstract_category_id']!="")
		{
			$searchCondition          .= " AND abstractRequest.abstract_cat = '".$_REQUEST['src_abstract_category_id']."'";
		}
		
?>
<table border="1">
		<tr>
			<td colspan="12" align="left">
			<h4 style="color:#000000;"><?
				if($_REQUEST['goto']=='abstract')
				{					
					echo "Abstract Listing";
				}
				elseif($_REQUEST['goto']=='caseReport')
				{					
					echo "Case Report Listing";
				}
				?>
			</h4>
			</td>
		</tr>
		<tr class="theader">
			<td width="30" align="center">Sl No</td>
			<td align="left" width="230">Name</td>
			<td align="left" width="100">Reg Id</td>
			<td align="left" width="100">Unique Seq</td>
			<td align="left" width="100">Registration Category</td>
			<td align="left" width="100">Phone no</td>
			<td align="left" width="100">Email Id</td>
			<td align="left" width="100">City</td>
			<td align="left" width="100">State</td>
			<td align="left" width="100">Country</td>
			<td align="left" width="100">Submission Code</td>
			<td align="left" width="100">Abstract/Case Report</td>
			<td align="left" width="100">Abstract Type</td>			
			<td align="center" width="120">Topic</td>
			<td align="center" width="120">Co-author Name</td>
			<td align="center" width="120">Co-author Institute</td>
			<td align="center">Title</td>
			<td width="100" align="center">Nomination</td>
			<td width="100" align="center">Marks</td>
		</tr>
		<?php
						

				    


							
							$abstractCounter               = 0;
							$sqlAbstractDetails			   = abstractDetailsQuerySet("",$searchCondition);

							//print_r($sqlAbstractDetails);
							
							$resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);
		
		
							if($resultAbstractDetails)
							{
								foreach($resultAbstractDetails as $i=>$rowAbstractDetails) 
								{
									$abstractDetailsArray = getAbstractDetailsArray($rowAbstractDetails['id']);
									
									
									$totalAbstractCount				= getTotalAbstractCount($rowAbstractDetails['applicant_id']);
									$totalCaseCount 				= getTotalCaseCount($rowAbstractDetails['applicant_id']);
		
									$abstractCounter++;
									$ReviewCount        = array();
									$ReviewCount['QUERY'] = " SELECT COUNT(*) AS totalReviewAttemptCount 
															  FROM "._DB_ABSTRACT_REVIEW_RESULT_."
															 WHERE abstract_id =".$rowAbstractDetails['id'].""; 
									$resultReviewCount  = $mycms->sql_select($ReviewCount);
									$rowReviewCount		= $resultReviewCount[0];
									
									$ReviewMarks        = array();		
									$ReviewMarks['QUERY'] = "SELECT SUM(marks_obtained) AS totalMarksObtained 
															 FROM "._DB_ABSTRACT_REVIEW_RESULT_." 
															WHERE abstract_id =".$rowAbstractDetails['id']." 
															  AND status = 'A'"; 
									$resultReviewMarks  = $mycms->sql_select($ReviewMarks);
									$rowReviewMarks     = $resultReviewMarks[0];
															
									$rowStyleDecission     = "";
									if($rowReviewCount['totalReviewAttemptCount']==0)
									{
										$rowStyleDecission = " style='background-color: #FFFFFF;'";
									}
									else
									{
										$rowStyleDecission = " style='background-color: #DDF1D6;'";
									}

									$sqlClassification    = array();
								    $sqlClassification['QUERY']    = "SELECT * 
								                                      FROM "._DB_REGISTRATION_CLASSIFICATION_." 
								                                     WHERE `status` = ? AND `type` = ? AND `id` = ?";
								                                  
								    $sqlClassification['PARAM'][]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');
								    $sqlClassification['PARAM'][]   = array('FILD' => 'type',  'DATA' =>'DELEGATE',  'TYP' => 's');
								    $sqlClassification['PARAM'][]   = array('FILD' => 'id',  'DATA' =>$rowAbstractDetails['registration_classification_id'],  'TYP' => 's');
								    
								    $resultClassificationTitle = $mycms->sql_select($sqlClassification);

								    $sqlAbstractTopic			  =	array();
									$sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
																	  WHERE `id` = ?";
									
									$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'id', 'DATA' =>$rowAbstractDetails['abstract_cat'],  'TYP' => 's');
									//$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'category', 'DATA' =>$abstract_topic_id,  'TYP' => 's');
									$resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

									$sqlSubCat			 = array();
									$sqlSubCat['QUERY']  = "SELECT `abstract_submission` 
																			   FROM "._DB_ABSTRACT_SUBMISSION_."
																			 WHERE `id` = '".$rowAbstractDetails['abstract_parent_type']."' AND status='A'";
																			 
																 
									$resultSubCat = $mycms->sql_select($sqlSubCat);

									$sqlState			 = array();
									$sqlState['QUERY']  = "SELECT S.state_name,U.user_city,C.country_name
																			   FROM "._DB_ABSTRACT_REQUEST_." A INNER JOIN "._DB_USER_REGISTRATION_." U ON A.applicant_id = U.id INNER JOIN "._DB_COMN_STATE_." S ON U.user_state_id = S.st_id

																			   INNER JOIN "._DB_COMN_COUNTRY_." C ON U.user_country_id = C.country_id
																			 WHERE A.`applicant_id` = '".$rowAbstractDetails['applicant_id']."' AND A.status='A' AND S.status='A' AND U.status='A'";
																			 
																 
									$resultState = $mycms->sql_select($sqlState);

									//print_r($resultState[0]['country_name']);



									$coAuthorCounter           = 0;
					
								$sqlAbstractCoAuthor            = array();
								$sqlAbstractCoAuthor['QUERY']   = "SELECT coauthor.*, 
																	 
																	 country.country_name AS coauthor_country_name,
																	 state.state_name AS coauthor_state_name
																	 
																FROM "._DB_ABSTRACT_COAUTHOR_." coauthor 
															
													 LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
																  ON coauthor.abstract_coauthor_country_id = country.country_id
														 
													 LEFT OUTER JOIN "._DB_COMN_STATE_." state
																  ON coauthor.abstract_coauthor_state_id = state.st_id	
																	  
															   WHERE coauthor.status = ?
																 AND coauthor.abstract_id = ?";
																 
										$sqlAbstractCoAuthor['PARAM'][]   = array('FILD' => 'coauthor.status',  	   'DATA' =>'A',                        'TYP' => 's');
										$sqlAbstractCoAuthor['PARAM'][]   = array('FILD' => 'coauthor.abstract_id',    'DATA' =>$rowAbstractDetails['id'],  'TYP' => 's');
																				
										$resultAbstractCoAuthor    = $mycms->sql_select($sqlAbstractCoAuthor);

										$sqlReview					= array();
										$sqlReview['QUERY'] 			= " SELECT SUM(R.`marks_obtained`) MARKS, COUNT(R.`faculty_id`) COUNTDATA
																			  FROM "._DB_ABSTRACT_REVIEW_RESULT_." R INNER JOIN "._DB_FACULTY_ACCOUNT_." F ON R.faculty_id = F.id
																			 WHERE R.abstract_id = '".$rowAbstractDetails['id']."' AND R.status='A' AND F.status='A'";
																 
									$resultReview = $mycms->sql_select($sqlReview);	

									//echo '<pre>'; print_r($resultReview[0]['']);

									$totalMarks = $resultReview[0]['MARKS'];
									$totalReviewer = $resultReview[0]['COUNTDATA'];

									
									if(!empty($totalMarks) && $totalReviewer>0)
									{
										 $totalAvg = floatval($totalMarks/$totalReviewer);
									}	

								   // echo '<pre>'; print_r($rowAbstractDetails);
							?>
									<tr class="tlisting" <?=$rowStyleDecission?>>
										<td align="center" valign="top" style="width:80px;"><?=$abstractCounter + ($_REQUEST['_pgn1_']*10)?></td>
										<td align="left" valign="top"><?=$rowAbstractDetails['user_first_name']." ".$rowAbstractDetails['user_middle_name']." ".$rowAbstractDetails['user_last_name']." "?></td>
										<td align="left" valign="top">
											<?
											if($rowAbstractDetails['registration_payment_status']=="PAID" 
											   || $rowAbstractDetails['registration_payment_status']=="COMPLIMENTARY"
											   || $rowAbstractDetails['registration_payment_status']=="ZERO_VALUE")
											{
												echo $rowAbstractDetails['user_registration_id'];
											}
											elseif($rowAbstractDetails['registration_request']=='ABSTRACT')
											{
												echo '<span style="color:#FF00FF;">Not Registered Yet</span>';
											}
											else
											{
												echo "-";
											}
											?>
										</td>
										<td align="left" valign="top">
											<?=$rowAbstractDetails['user_unique_sequence']?>
										</td>
										<td align="left" valign="top">
											<?=$resultClassificationTitle[0]['classification_title']?>
										</td>
										<td align="left" valign="top">
											<?=$rowAbstractDetails['user_mobile_no']?>
										</td>	
										<td align="left" valign="top">
											<?=$rowAbstractDetails['user_email_id']?>
										</td>	
										<td align="left" valign="top">
											<!-- <?=$rowAbstractDetails['author_state_name']?> -->
										 <?php	echo strtoupper($resultState[0]['state_name']); ?>
										</td>

										<td align="left" valign="top">
											<!-- <?=$rowAbstractDetails['author_state_name']?> -->
										 <?php	echo strtoupper($resultState[0]['user_city']); ?>
										</td>

										<td align="left" valign="top">
											<!-- <?=$rowAbstractDetails['author_state_name']?> -->
										 <?php	echo strtoupper($resultState[0]['country_name']); ?>
										</td>
										<td lign="center" valign="top"><?php echo "&nbsp;".$rowAbstractDetails['abstract_submition_code']?></td>
										<td lign="center" valign="top"><?=ucfirst($resultAbstractTopic[0]['category'])?></td>
										<!-- <td lign="center" valign="top"><?=$rowAbstractDetails['abstract_child_type']?></td> -->
										<td lign="center" valign="top"><?=ucfirst($resultSubCat[0]['abstract_submission'])?></td>
										<td align="center" valign="top"><?=$rowAbstractDetails['abstract_topic']?></td>	

										<td align="center" valign="top">
											<?php
											foreach($resultAbstractCoAuthor as $keyAbstractCoAuthor=>$rowAbstractCoAuthor)
											{
												echo strtoupper($rowAbstractCoAuthor['abstract_coauthor_name'])."<br><br>";
											}
											?>

										</td>
										<td align="center" valign="top">
											<?php
											foreach($resultAbstractCoAuthor as $keyAbstractCoAuthor=>$rowAbstractCoAuthor)
											{
												echo strtoupper($rowAbstractCoAuthor['abstract_coauthor_institute_name'])."<br><br>";
											}
											?>

										</td>
										<td align="left" valign="top"><?=$rowAbstractDetails['abstract_title']?></td>
																		
										
										<td align="left" valign="top"><?=$rowAbstractDetails['award_names']?></td>
										<td align="center" valign="top">
										<?php
										if(!empty($totalMarks) && $totalReviewer>0)
										{
											//echo "".round($abstractDetailsArray['MARKS']['AVERAGE'],2);
											if(!empty($totalMarks) && $totalReviewer>0)
													{
														//echo "<span style='font-size:10px;'>".$totalMarks."/".$totalReviewer."=</span><br>".$totalAvg;

														echo "".round($totalAvg,2);
													}
										}
										else
										{
										?>
											<span class="ticket ticket-important" style="background-color:#fbbf18;">Not Reviewed</span>
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
				<td colspan="8" align="center">
					<span class="mandatory">No Record Present.</span>												
				</td>
			</tr>  
		<?php 
		} 
		?>
		<tr>
			<td  colspan="12" align="left">
				<h3>Excel Download Date and Time : <?=date('d/m/Y h:i A')?>	</h3>											
			</td>
	  	</tr> 
	</table>
<?
	}
	
?>
