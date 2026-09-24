<?php
include_once('includes/init.php');
include_once(__DIR__. "/../includes/function.abstract.php");

$operation				= addslashes(trim($_REQUEST['operation']));

$abstractId 			= addslashes(trim($_REQUEST['id']));		 
$sqlAbstractDetails		= abstractDetailsQuerySet($abstractId,'');

$resultAbstractDetails  = $mycms->sql_select($sqlAbstractDetails);
$rowAbstractDetails     = $resultAbstractDetails[0];

if($operation=="docdownload")
{
	header("Content-Description: File Transfer");
	header('Content-Disposition: attachment; filename="'.$rowAbstractDetails['tags'].'_'.$rowAbstractDetails['abstract_submition_code'].'.doc"');
	header("Content-type: application/vnd.ms-word;");// charset=Windows-1252
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Expires: 0');//
}
?>
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<head></head>
<body>
<font face="Arial, Helvetica, sans-serif">
<?
/// this is the erstwhile code kept for reference
if(false)
{
?>
	<table width="100%">
		<tr>
			<td colspan="2">
				
				<table width="100%" cellpadding="6" cellspacing="1">
					<tr>
						<td colspan="4" align="left"><font size="2"><u><strong>Basic Details</strong></u></font></td>
					</tr>
					<tr>
						<td width="20%" align="left"><font size="2"><strong>Abstract Submision Code</strong></font></td>
						<td width="30%" align="left"><font size="2"><?=strtoupper($rowAbstractDetails['abstract_submition_code'])?></font></td>
						<td width="20%" align="left"><font size="2"><strong>Submission Date</strong></font></td>
						<td width="30%" align="left"><font size="2"><?=setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D")?></font></td>
					</tr>
				</table>
				
				<table width="100%" cellpadding="6" cellspacing="1">
					<tr colspan="4"></tr>
					<tr>
						<td colspan="4" align="left"><font size="2"><u><strong>Presenter Details</strong></u></font></td>
					</tr>
					<tr>
						<td width="20%" align="left"><font size="2"><strong>Name</strong></font></td>
						<td width="30%" align="left">
							<font size="2">
							<?php
							echo strtoupper($rowAbstractDetails['user_title']).". "; 
							echo strtoupper($rowAbstractDetails['user_first_name'])." "; 
							echo strtoupper($rowAbstractDetails['user_middle_name'])." "; 
							echo strtoupper($rowAbstractDetails['user_last_name'])." "; 
							?>
							</font>
						</td>
						<td width="20%" align="left"><font size="2"><strong>Email Id</strong></font></td>
						<td width="30%" align="left"><font size="2"><?=$rowAbstractDetails['applicant_email_id']?></font></td>
					</tr>
					<tr>
						<td align="left"><font size="2"><strong>Unique Sequence</strong></font></td>
						<td align="left" bgcolor="#F9E0C0"><font size="2"><?=$rowAbstractDetails['user_access_key']?></font></td>
						<td align="left"><font size="2"><strong>Mobile Number</strong></font></td>
						<td align="left"><font size="2"><?=strtoupper($rowAbstractDetails['applicant_mobile_no'])?></font></td>
					</tr>
					<tr>
						<td align="left"><font size="2"><strong>Registration Id</strong></font></td>
						<td align="left">
							<font size="2">
							<?php
							if(($rowAbstractDetails['isRegistration']=="Y" && $rowAbstractDetails['registration_payment_status']=="PAID")
							   || ($rowAbstractDetails['isWorkshop']=="Y" && $rowAbstractDetails['workshop_payment_status']=="PAID"))
							{
								echo $rowAbstractDetails['user_registration_id'];
							}
							else
							{
								echo "-";
							}
							?>
							</font>
						</td>
						<td align="left"><font size="2"><strong>Phone Number</strong></font></td>
						<td align="left"><font size="2"><?=strtoupper($rowAbstractDetails['applicant_phone_no'])?></font></td>
					</tr>
				</table>
				
				<table width="100%" cellpadding="6" cellspacing="1">
					<tr colspan="4"></tr>
					<tr>
						<td colspan="4" align="left"><font size="2"><u><strong>Author Details</strong></u></font></td>
					</tr>
					<tr>
						<td width="20%" align="left" valign="top"><font size="2"><strong>Author Name</strong></font></td>
						<td width="30%" align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractDetails['abstract_author_name'])?></font></td>
						<td width="20%" align="left" valign="top"><font size="2"><strong>Country</strong></font></td>
						<td width="30%" align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractDetails['author_country_name'])?></font></td>
					</tr>
					<tr>
						<td align="left" valign="top"><font size="2"><strong>Author Department</strong></font></td>
						<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractDetails['abstract_author_department'])?></font></td>
						<td align="left" valign="top"><font size="2"><strong>State</strong></font></td>
						<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractDetails['author_state_name'])?></font></td>
					</tr>
					<tr>
						<td align="left" valign="top"><font size="2"><strong>Author Institute Name</strong></font></td>
						<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractDetails['abstract_author_institute_name'])?></font></td>
						<td align="left" valign="top"><font size="2"><strong>City</strong></font></td>
						<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractDetails['abstract_author_city'])?></font></td>
					</tr>
					<tr>
						<td align="left" valign="top"><font size="2"><strong>Author Phone No</strong></font></td>
						<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractDetails['abstract_author_phone_no'])?></font></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				
				<?php
				$coAuthorCounter           = 0;
				$sqlAbstractCoAuthor['QUERY']= "SELECT coauthor.*, 
													 
													 country.country_name AS coauthor_country_name,
													 state.state_name AS coauthor_state_name
													 
												FROM ".$cfg['DB.ABSTRACT.COAUTHOR']." coauthor 
											
									 LEFT OUTER JOIN ".$cfg['DB.COMN.COUNTRY']." country
												  ON coauthor.abstract_coauthor_country_id = country.country_id
										 
									 LEFT OUTER JOIN ".$cfg['DB.COMN.STATE']." state
												  ON coauthor.abstract_coauthor_state_id = state.st_id	
													  
											   WHERE coauthor.status = 'A' 
												 AND coauthor.abstract_id = '".$rowAbstractDetails['id']."'";
														
				$resultAbstractCoAuthor    = $mycms->sql_select($sqlAbstractCoAuthor);
				if($resultAbstractCoAuthor)
				{
					foreach($resultAbstractCoAuthor as $keyAbstractCoAuthor=>$rowAbstractCoAuthor)
					{
						$coAuthorCounter++;
				?>
						<table width="100%" cellpadding="6" cellspacing="1">
							<tr colspan="4"></tr>
							<tr>
								<td colspan="4" align="left"><font size="2"><u><strong>Co-author Details <?=$coAuthorCounter?></strong></u></font></td>
							</tr>
							<tr>
								<td width="20%" align="left" valign="top"><font size="2"><strong>Co-author Name</strong></font></td>
								<td width="30%" align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractCoAuthor['abstract_coauthor_name'])?></font></td>
								<td width="20%" align="left" valign="top"><font size="2"><strong>Country</strong></font></td>
								<td width="30%" align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractCoAuthor['coauthor_country_name'])?></font></td>
							</tr>
							<tr>
								<td align="left" valign="top"><font size="2"><strong>Co-author Department</strong></font></td>
								<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractCoAuthor['abstract_coauthor_department'])?></font></td>
								<td align="left" valign="top"><font size="2"><strong>State</strong></font></td>
								<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractCoAuthor['coauthor_state_name'])?></font></td>
							</tr>
							<tr>
								<td align="left" valign="top"><font size="2"><strong>Co-author Institute Name</strong></font></td>
								<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractCoAuthor['abstract_coauthor_institute_name'])?></font></td>
								<td align="left" valign="top"><font size="2"><strong>City</strong></font></td>
								<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractCoAuthor['abstract_coauthor_city_name'])?></font></td>
							</tr>
							<tr>
								<td align="left" valign="top"><font size="2"><strong>Co-author Phone No</strong></font></td>
								<td align="left" valign="top"><font size="2"><?=strtoupper($rowAbstractCoAuthor['abstract_coauthor_phone_no'])?></font></td>
								<td></td>
								<td></td>
							</tr>
						</table>
				<?php
					}
				}

				$searchArrayStng       = array("<", ">");
				$replaceArrayStng      = array("&lt;", "&gt;");
				?>
				
				<table width="100%" cellpadding="6" cellspacing="1">
					<tr colspan="4"></tr>
					<tr>
						<td colspan="4" align="left"><font size="2"><u><strong>Abstract Details</strong></u></font></td>
					</tr>
					<tr>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Abstract Title</strong></font></td>
						<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><?=$rowAbstractDetails['abstract_title']?></font></td>
					</tr>
					<tr>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Abstract Title Word Count</strong></font></td>
						<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
							<font size="2"><b><?=str_word_count($rowAbstractDetails['abstract_title'])?></b></font>
						</td>
					</tr>
					<tr>
						<td width="20%" align="left" style="border-bottom:thin solid #ccc"><font size="2"><strong>Abstract Topic</strong></font></td>
						<td width="30%" align="left" style="border-bottom:thin solid #ccc"><font size="2"><?=$rowAbstractDetails['abstract_topic']?></font></td>
						<td width="20%" align="left" style="border-bottom:thin solid #ccc"><font size="2"><strong>Type of Abstract</strong></font></td>
						<td width="30%" align="left" style="border-bottom:thin solid #ccc">
							<font size="2">
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
							</font>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Background &amp; Aims</strong></font></td>
						<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
							<font size="2">
							<?php
							$totalWordCount          = 0;
							$totalWordCount         += str_word_count($rowAbstractDetails['abstract_background_aims']);
							
							$aimsNBackgroundString   = str_replace($searchArrayStng, $replaceArrayStng, $rowAbstractDetails['abstract_background_aims']);
							echo nl2br($aimsNBackgroundString);
							?>
							</font>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Material &amp; Methods</strong></font></td>
						<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
							<font size="2">
							<?php
							$totalWordCount         += str_word_count($rowAbstractDetails['abstract_material_methods']);
							
							$materialNMethodString   = str_replace($searchArrayStng, $replaceArrayStng, $rowAbstractDetails['abstract_material_methods']);
							echo nl2br($materialNMethodString);
							?>
							</font>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Results</strong></font></td>
						<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
							<font size="2">
							<?php
							$totalWordCount  += str_word_count($rowAbstractDetails['abstract_results']);
							
							$resultString     = str_replace($searchArrayStng, $replaceArrayStng, $rowAbstractDetails['abstract_results']);
							echo nl2br($resultString);
							?>
							</font>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top"><font size="2" style="border-bottom:thin solid #ccc"><strong>Conclusion</strong></font></td>
						<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
							<font size="2">
							<?php
							$totalWordCount              += str_word_count($rowAbstractDetails['abstract_conclution']);
							
							$abstractConclutionString     = str_replace($searchArrayStng, $replaceArrayStng, $rowAbstractDetails['abstract_conclution']);
							echo nl2br($abstractConclutionString);
							?>
							</font>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Total Word Count</strong></font></td>
						<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
							<font size="2">
							<b><?=$totalWordCount?></b>
							</font>
						</td>
					</tr>
				</table>
				
				<table width="100%" cellpadding="6" cellspacing="1">
					<tr colspan="4"></tr>
					<tr>
						<td colspan="2" align="left"><font size="2"><u><strong>Review Result</strong></u></font></td>
					</tr>
					<tr>
						<td width="20%" align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Total Marks Obtained</strong></font></td>
						<td width="80%" align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><?=$rowAbstractDetails['totalMarksObtained']?></font></td>
					</tr>
					<tr>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Total Review</strong></font></td>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><?=$rowAbstractDetails['totalReviewCount']?></font></td>
					</tr>
					<tr>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><strong>Average Marks Obtained</strong></font></td>
						<td align="left" valign="top" style="border-bottom:thin solid #ccc"><font size="2"><?=intToFloat($rowAbstractDetails['averageMarksObtained'])?></font></td>
					</tr>
				</table>
				
			</td>
		</tr>
	</table>
<?
}
abstractOnlyDetailsWindow($rowAbstractDetails);
?>
</font>
</body>
</html>
<script>
<?
if($operation=="print")
{
?>
setTimeout(function(){
				window.print();
		   },2000);
<?
}
?>
</script>
<?
	function abstractOnlyDetailsWindow($rowAbstractDetails)
	{
		global $cfg, $mycms;		
?>
		<table width="100%" class="tborder">			
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%">
						<tr>
							<td colspan="4" align="center" class="thighlight" style="font-size: 32px"><strong><?=ucwords($rowAbstractDetails['tags'])?></strong></td>
						</tr>
						<tr>
							<td align="left"><b>Submission Code</b> : <?=strtoupper($rowAbstractDetails['abstract_submition_code'])?></td>
						</tr>
					</table>
					<?
						if ($rowAbstractDetails['tags'] == 'Abstract') {
?><table>
	<tr>
		<td>
	<div width="100%">
		<tr colspan="4">
	</div><hr>
	<div>
		<div colspan="4" align="left" class="thighlight" style="font-size: 18px"><strong>Abstract Details</strong></div>
	</div><hr>
	<?php if (!empty($rowAbstractDetails['abstract_title'])) { ?>
		<div>
			<div align="left" valign="top" width="20%"><b>TITLE</b></div>
		</div>
		<div>
			<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= htmlentities($rowAbstractDetails['abstract_title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
		</div><br>
	<?php }
		if (!empty($rowAbstractDetails['abstract_topic'])) { ?>
		<div>
			<div align="left" valign="top" width="20%"><b>TOPIC</b></div>
		</div>
		<div>
			<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= htmlentities($rowAbstractDetails['abstract_topic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
		</div><br>
	<?php } ?>

	<?php
		$sqlAbstractFields			  =	array();
		$sqlAbstractFields['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
						WHERE `status` = ?";

		$sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

		$resultAbstractFields = $mycms->sql_select($sqlAbstractFields);

		foreach ($resultAbstractFields as $key => $value) {


			$sqlAbstractFieldsVal			  =	array();
			$sqlAbstractFieldsVal['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_ABSTRACT_REQUEST_ . " 
							WHERE " . $value['field_key'] . "!='NULL' AND id='" . $rowAbstractDetails['id'] . "'";

			$resultAbstractFieldsVal = $mycms->sql_select($sqlAbstractFieldsVal);


			if ($resultAbstractFieldsVal[0]['COUNTDATA'] > 0) {

				$totalWordCount  += str_word_count($rowAbstractDetails[$value['field_key']]);
	?>


			<div>
				<div align="left" valign="top" width="20%"><b><?= $value['display_name'] ?></b></div>
			</div>
			<div>
				<div colspan="3" align="left" valign="top"><?= htmlentities($rowAbstractDetails[$value['field_key']], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
			</div><br>
	<?php
			}
		}
	?>
	</div>
	</td>
	</tr>
</table>
<?
	}
					elseif($rowAbstractDetails['tags']=='Case report')
					{
					?>
					<table width="100%">
						<tr colspan="4"></tr>
						<tr>
							<td colspan="4" align="left" class="thighlight" style="font-size: 24px"><strong>Case Report Details</strong></td>
						</tr>
						<tr>
							<td  align="left" valign="top"  width="20%"><b>TITLE</b></td>
						</tr>
						<tr>
							<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?=$rowAbstractDetails['abstract_title']?></td>
						</tr>
						<!--<tr>
							<td align="left" valign="top" width="20%"><b>TOPIC</b></td>
						</tr>
						<tr>
							<td colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?=$rowAbstractDetails['abstract_topic']?></td>
						</tr>-->
						<tr>
							<td  align="left" valign="top" width="20%"><b>Introduction</b></td>
						</tr>
						<tr>
							<td colspan="3"  align="left" style="border-bottom:thin solid #ccc;"><?=$rowAbstractDetails['abstract_background_aims']?></td>
						</tr>
						<!--<tr>
							<td  align="left" valign="top" width="20%"><b>Case Report</b></td>
						</tr>
						<tr>
							<td colspan="3"  align="left" style="border-bottom:thin solid #ccc;"><?=$rowAbstractDetails['abstract_description']?></td>
							
						</tr>-->
						<tr>
							<td align="left" valign="top" width="20%"><b>Discussion</b></td>
						</tr>
						<tr>
							<td colspan="3" align="left" valign="top"><?=$rowAbstractDetails['abstract_results']?></td>
						</tr>
					</table>
					<? 
					}
					?>
				</td>
			</tr>
		</table>
	<?php
	}
?>