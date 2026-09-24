<?php
include_once('includes/init.php');
include_once(__DIR__. "/../includes/function.abstract.php");
mb_internal_encoding("UTF-8");

$operation				= addslashes(trim($_REQUEST['operation']));

$abstractId 			= addslashes(trim($_REQUEST['id']));
$sqlAbstractDetails		= abstractDetailsQuerySet($abstractId, '');

$resultAbstractDetails  = $mycms->sql_select($sqlAbstractDetails);
$rowAbstractDetails     = $resultAbstractDetails[0];
// echo "<pre>"; print_r($rowAbstractDetails);die;

if ($operation == "docdownload") {
	// header("Content-Description: File Transfer");
	// header('Content-Disposition: attachment; filename="' . $rowAbstractDetails['tags'] . '_' . $rowAbstractDetails['abstract_submition_code'] . '.doc"');
	// header("Content-type: application/vnd.ms-word;"); // charset=Windows-1252
	// header('Content-Transfer-Encoding: binary');
	// header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	// header('Expires: 0'); //

	  header("Content-Description: File Transfer");
    header("Content-Type: application/msword; charset=UTF-8");
    header('Content-Disposition: attachment; filename="' . $rowAbstractDetails['tags'] . '_' . $rowAbstractDetails['abstract_submition_code'] . '.doc"');
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}
?>
<html>
<!-- <meta http-equiv="Content-Type\" content="text/html; charset=Windows-1252\"> -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<head></head>
<style type="text/css">
	/*table, th, td {
  border: none !important;
  border-collapse: none !important;
}*/
</style>

<body>
	<font face="Arial, Helvetica, sans-serif">
		<?
		/// this is the erstwhile code kept for reference
		if (false) {
		?>
			<div width="100%" border="0" style='border-collapse:collapse;border:none;mso-border-alt:solid windowtext .0pt;'>
				<div>
					<div colspan="2">

						<div width="100%" cellpadding="6" cellspacing="1" style='border-collapse:collapse;border:none;mso-border-alt:solid windowtext .0pt;'>
							<div>
								<div colspan="4" align="left">
									<font size="2"><u><strong>Basic Details</strong></u></font>
								</div>
							</div>
							<div>
								<div width="20%" align="left">
									<font size="2"><strong>Abstract Submision Code</strong></font>
								</div>
								<div width="30%" align="left">
									<font size="2"><?= strtoupper($rowAbstractDetails['abstract_submition_code']) ?></font>
								</div>
								<div width="20%" align="left">
									<font size="2"><strong>Submission Date</strong></font>
								</div>
								<div width="30%" align="left">
									<font size="2"><?= setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D") ?></font>
								</div>
							</div>
						</div>

						<div width="100%" cellpadding="6" cellspacing="1">
							<tr colspan="4">
						</div>
						<div>
							<div colspan="4" align="left">
								<font size="2"><u><strong>Presenter Details</strong></u></font>
							</div>
						</div>
						<div>
							<div width="20%" align="left">
								<font size="2"><strong>Name</strong></font>
							</div>
							<div width="30%" align="left">
								<font size="2">
									<?php
									echo strtoupper($rowAbstractDetails['user_title']) . ". ";
									echo strtoupper($rowAbstractDetails['user_first_name']) . " ";
									echo strtoupper($rowAbstractDetails['user_middle_name']) . " ";
									echo strtoupper($rowAbstractDetails['user_last_name']) . " ";
									?>
								</font>
							</div>
							<div width="20%" align="left">
								<font size="2"><strong>Email Id</strong></font>
							</div>
							<div width="30%" align="left">
								<font size="2"><?= $rowAbstractDetails['applicant_email_id'] ?></font>
							</div>
						</div>
						<div>
							<div align="left">
								<font size="2"><strong>Unique Sequence</strong></font>
							</div>
							<div align="left" bgcolor="#F9E0C0">
								<font size="2"><?= $rowAbstractDetails['user_access_key'] ?></font>
							</div>
							<div align="left">
								<font size="2"><strong>Mobile Number</strong></font>
							</div>
							<div align="left">
								<font size="2"><?= strtoupper($rowAbstractDetails['applicant_mobile_no']) ?></font>
							</div>
						</div>
						<div>
							<div align="left">
								<font size="2"><strong>Registration Id</strong></font>
							</div>
							<div align="left">
								<font size="2">
									<?php
									if (($rowAbstractDetails['isRegistration'] == "Y" && $rowAbstractDetails['registration_payment_status'] == "PAID")
										|| ($rowAbstractDetails['isWorkshop'] == "Y" && $rowAbstractDetails['workshop_payment_status'] == "PAID")
									) {
										echo $rowAbstractDetails['user_registration_id'];
									} else {
										echo "-";
									}
									?>
								</font>
							</div>
							<div align="left">
								<font size="2"><strong>Phone Number</strong></font>
							</div>
							<div align="left">
								<font size="2"><?= strtoupper($rowAbstractDetails['applicant_phone_no']) ?></font>
							</div>
						</div>
					</div>

					<div width="100%" cellpadding="6" cellspacing="1">
						<tr colspan="4">
					</div>
					<div>
						<div colspan="4" align="left">
							<font size="2"><u><strong>Author Details</strong></u></font>
						</div>
					</div>
					<div>
						<div width="20%" align="left" valign="top">
							<font size="2"><strong>Author Name</strong></font>
						</div>
						<div width="30%" align="left" valign="top">
							<font size="2"><?= strtoupper($rowAbstractDetails['abstract_author_name']) ?></font>
						</div>
						<div width="20%" align="left" valign="top">
							<font size="2"><strong>Country</strong></font>
						</div>
						<div width="30%" align="left" valign="top">
							<font size="2"><?= strtoupper($rowAbstractDetails['author_country_name']) ?></font>
						</div>
					</div>
					<div>
						<div align="left" valign="top">
							<font size="2"><strong>Author Department</strong></font>
						</div>
						<div align="left" valign="top">
							<font size="2"><?= strtoupper($rowAbstractDetails['abstract_author_department']) ?></font>
						</div>
						<div align="left" valign="top">
							<font size="2"><strong>State</strong></font>
						</div>
						<div align="left" valign="top">
							<font size="2"><?= strtoupper($rowAbstractDetails['author_state_name']) ?></font>
						</div>
					</div>
					<div>
						<div align="left" valign="top">
							<font size="2"><strong>Author Institute Name</strong></font>
						</div>
						<div align="left" valign="top">
							<font size="2"><?= strtoupper($rowAbstractDetails['abstract_author_institute_name']) ?></font>
						</div>
						<div align="left" valign="top">
							<font size="2"><strong>City</strong></font>
						</div>
						<div align="left" valign="top">
							<font size="2"><?= strtoupper($rowAbstractDetails['abstract_author_city']) ?></font>
						</div>
					</div>
					<div>
						<div align="left" valign="top">
							<font size="2"><strong>Author Phone No</strong></font>
						</div>
						<div align="left" valign="top">
							<font size="2"><?= strtoupper($rowAbstractDetails['abstract_author_phone_no']) ?></font>
						</div>
						<div></div>
						<div></div>
					</div>
				</div>

				<?php
				$coAuthorCounter           = 0;
				$sqlAbstractCoAuthor       = array();
				$sqlAbstractCoAuthor['QUERY'] = "SELECT coauthor.*, 
													 
													 country.country_name AS coauthor_country_name,
													 state.state_name AS coauthor_state_name
													 
												FROM " . _DB_ABSTRACT_COAUTHOR_ . " coauthor 
											
									 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
												  ON coauthor.abstract_coauthor_country_id = country.country_id
										 
									 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
												  ON coauthor.abstract_coauthor_state_id = state.st_id	
													  
											   WHERE coauthor.status = 'A' 
												 AND coauthor.abstract_id = '" . $rowAbstractDetails['id'] . "'";

				$resultAbstractCoAuthor    = $mycms->sql_select($sqlAbstractCoAuthor);
				if ($resultAbstractCoAuthor) {
					foreach ($resultAbstractCoAuthor as $keyAbstractCoAuthor => $rowAbstractCoAuthor) {
						$coAuthorCounter++;
				?>
						<div width="100%" cellpadding="6" cellspacing="1">
							<tr colspan="4">
						</div>
						<div>
							<div colspan="4" align="left">
								<font size="2"><u><strong>Co-author Details <?= $coAuthorCounter ?></strong></u></font>
							</div>
						</div>
						<div>
							<div width="20%" align="left" valign="top">
								<font size="2"><strong>Co-author Name</strong></font>
							</div>
							<div width="30%" align="left" valign="top">
								<font size="2"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_name']) ?></font>
							</div>
							<div width="20%" align="left" valign="top">
								<font size="2"><strong>Country</strong></font>
							</div>
							<div width="30%" align="left" valign="top">
								<font size="2"><?= strtoupper($rowAbstractCoAuthor['coauthor_country_name']) ?></font>
							</div>
						</div>
						<div>
							<div align="left" valign="top">
								<font size="2"><strong>Co-author Department</strong></font>
							</div>
							<div align="left" valign="top">
								<font size="2"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_department']) ?></font>
							</div>
							<div align="left" valign="top">
								<font size="2"><strong>State</strong></font>
							</div>
							<div align="left" valign="top">
								<font size="2"><?= strtoupper($rowAbstractCoAuthor['coauthor_state_name']) ?></font>
							</div>
						</div>
						<div>
							<div align="left" valign="top">
								<font size="2"><strong>Co-author Institute Name</strong></font>
							</div>
							<div align="left" valign="top">
								<font size="2"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_institute_name']) ?></font>
							</div>
							<div align="left" valign="top">
								<font size="2"><strong>City</strong></font>
							</div>
							<div align="left" valign="top">
								<font size="2"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_city_name']) ?></font>
							</div>
						</div>
						<div>
							<div align="left" valign="top">
								<font size="2"><strong>Co-author Phone No</strong></font>
							</div>
							<div align="left" valign="top">
								<font size="2"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_phone_no']) ?></font>
							</div>
							<div></div>
							<div></div>
						</div>
			</div>
	<?php
					}
				}

				$searchArrayStng       = array("<", ">");
				$replaceArrayStng      = array("&lt;", "&gt;");
	?>

	<div width="100%" cellpadding="6" cellspacing="1">
		<tr colspan="4">
	</div>
	<div>
		<div colspan="4" align="left">
			<font size="2"><u><strong>Abstract Details</strong></u></font>
		</div>
	</div>
	<div>
		<div align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><strong>Abstract Title</strong></font>
		</div>
		<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><?= $rowAbstractDetails['abstract_title'] ?></font>
		</div>
	</div>
	<div>
		<div align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><strong>Abstract Title Word Count</strong></font>
		</div>
		<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><b><?= str_word_count($rowAbstractDetails['abstract_title']) ?></b></font>
		</div>
	</div>
	<div>
		<div width="20%" align="left" style="border-bottom:thin solid #ccc">
			<font size="2"><strong>Abstract Topic</strong></font>
		</div>
		<div width="30%" align="left" style="border-bottom:thin solid #ccc">
			<font size="2"><?= $rowAbstractDetails['abstract_topic'] ?></font>
		</div>
		<div width="20%" align="left" style="border-bottom:thin solid #ccc">
			<font size="2"><strong>Type of Abstract</strong></font>
		</div>
		<div width="30%" align="left" style="border-bottom:thin solid #ccc">
			<font size="2">
				<?php
				$abstractType     = "";

				if ($rowAbstractDetails['abstract_child_type'] == "ORAL") {
					$abstractType = "ORAL";
				} else if ($rowAbstractDetails['abstract_child_type'] == "POSTER") {
					$abstractType = "POSTER";
				} else if ($rowAbstractDetails['abstract_child_type'] == "NONE") {
					$abstractType = "NO CHOICE";
				}

				echo $abstractType;
				?>
			</font>
		</div>
	</div>

	<?php
			$sqlAbstractFields			  =	array();
			$sqlAbstractFields['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_FIELDS_ . " 
						WHERE `status` = ?";

			$sqlAbstractFields['PARAM'][]  = array('FILD' => 'status', 'DATA' => 'A',  'TYP' => 's');

			$resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
			$totalWordCount = 0;
			foreach ($resultAbstractFields as $key => $value) {


				$sqlAbstractFieldsVal			  =	array();
				$sqlAbstractFieldsVal['QUERY']    = "SELECT COUNT(*) AS COUNTDATA FROM " . _DB_ABSTRACT_REQUEST_ . " 
							WHERE " . $value['field_key'] . "!='NULL' AND id='" . $rowAbstractDetails['id'] . "'";

				$resultAbstractFieldsVal = $mycms->sql_select($sqlAbstractFieldsVal);


				if ($resultAbstractFieldsVal[0]['COUNTDATA'] > 0) {

					$totalWordCount  += str_word_count($rowAbstractDetails[$value['field_key']]);
	?>



			<div>
				<div align="left" valign="top">
					<font size="2" style="border-bottom:thin solid #ccc"><strong><?= $value['display_name'] ?></strong></font>
				</div>
				<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
					<font size="2">
					<?	$fixedText = $rowAbstractDetails[$value['field_key']]?>

						<?php echo $fixedText; ?>
						
					</font>
				</div>
			</div>
	<?php
				}
			}
	?>




	<div>
		<div align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><strong>Total Word Count</strong></font>
		</div>
		<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2">
				<b><?= $totalWordCount ?></b>
			</font>
		</div>
	</div>
	</div>

	<div width="100%" cellpadding="6" cellspacing="1">
		<tr colspan="4">
	</div>
	<div>
		<div colspan="2" align="left">
			<font size="2"><u><strong>Review Result</strong></u></font>
		</div>
	</div>
	<div>
		<div width="20%" align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><strong>Total Marks Obtained</strong></font>
		</div>
		<div width="80%" align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><?= $rowAbstractDetails['totalMarksObtained'] ?></font>
		</div>
	</div>
	<div>
		<div align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><strong>Total Review</strong></font>
		</div>
		<div align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><?= $rowAbstractDetails['totalReviewCount'] ?></font>
		</div>
	</div>
	<div>
		<div align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><strong>Average Marks Obtained</strong></font>
		</div>
		<div align="left" valign="top" style="border-bottom:thin solid #ccc">
			<font size="2"><?= intToFloat($rowAbstractDetails['averageMarksObtained']) ?></font>
		</div>
	</div>
	</div>

	</div>
	</div>
	</div>
<?
		}
		abstractDetailsWindow($rowAbstractDetails);
?>
	</font>
</body>

</html>
<script>
	<?
	if ($operation == "print") {
	?>
		setTimeout(function() {
			window.print();
		}, 2000);
	<?
	}
	?>
</script>
<?
function abstractDetailsWindow($rowAbstractDetails)
{
	global $cfg, $mycms;
?>
	<div width="100%">
		<div>
			<div colspan="2" style="margin:0px; padding:0px;">
				<div width="100%">
					<div>
						<div colspan="4" align="center" class="thighlight" style="font-size: 32px"><strong><?= ucwords($rowAbstractDetails['tags']) ?></strong></div>
					</div>
					<div>
						<div colspan="4" align="left" class="thighlight" style="font-size: 24px"><strong>Basic Details</strong></div>
					</div>
					<div>
						<div width="20%" align="left"><b><?= ucwords($rowAbstractDetails['tags']) ?> Submission Code</b></div>
						<div width="30%" align="left"><?= strtoupper($rowAbstractDetails['abstract_submition_code']) ?></div>
						<div width="20%" align="left"><b>Submission Date</b></div>
						<div width="30%" align="left"><?= setDateTimeFormat($rowAbstractDetails['created_dateTime'], "D") ?></div>
					</div>
				</div>
				<div width="100%">
					<tr colspan="4">
				</div>
				<div>
					<div colspan="4" align="left" class="thighlight" style="font-size: 24px"><strong>Presenter Details</strong></div>
				</div>
				<div>
					<div width="20%" align="left"><b>Name</b></div>
					<div width="30%" align="left">
						<?php
						echo strtoupper($rowAbstractDetails['user_first_name']) . " ";
						echo strtoupper($rowAbstractDetails['user_middle_name']) . " ";
						echo strtoupper($rowAbstractDetails['user_last_name']) . " ";
						?>
					</div>
					<div width="20%" align="left"><b>Email Id</b></div>
					<div width="30%" align="left"><?= $rowAbstractDetails['applicant_email_id'] ?></div>
				</div>
				<div>
					<div align="left"><b>Unique Sequence</b></div>
					<div align="left" bgcolor="#F9E0C0"><?= $rowAbstractDetails['user_unique_sequence'] ?></div>
					<div align="left"><b>Mobile Number</b></div>
					<div align="left"><?= strtoupper($rowAbstractDetails['applicant_mobile_no']) ?></div>
				</div>
				<div>
					<div align="left"><b>Registration Id</b></div>
					<div align="left">
						<?php
						if (($rowAbstractDetails['isRegistration'] == "Y" && ($rowAbstractDetails['registration_payment_status'] == "PAID"
								|| $rowAbstractDetails['registration_payment_status'] == "COMPLEMENTARY"))
							|| ($rowAbstractDetails['isWorkshop'] == "Y" && ($rowAbstractDetails['workshop_payment_status'] == "PAID"
								|| $rowAbstractDetails['workshop_payment_status'] == "COMPLEMENTARY"))
						) {
							echo $rowAbstractDetails['user_registration_id'];
						} else {
							echo "-";
						}
						?>
					</div>
					<?php /*?>	<div align="left"><b>Phone Number</b></div>
							<div align="left"><?=strtoupper($rowAbstractDetails['applicant_phone_no'])?></div><?php */ ?>
				</div>
				<div>
					<div align="left"><b>Institute Name</b></div>
					<div align="left"><?= $rowAbstractDetails['delegateInstitute'] ?></div>
					<div align="left"><b>Department</b></div>
					<div align="left"><?= strtoupper($rowAbstractDetails['delegateDepertment']) ?></div>
				</div>
				<div>
					<div align="left"><b>Country</b></div>
					<div align="left"><?= $rowAbstractDetails['author_country_name'] ?></div>
					<div align="left"><b>State</b></div>
					<div align="left"><?= strtoupper($rowAbstractDetails['author_state_name']) ?></div>
				</div>
				<div>
					<div align="left"><b>City</b></div>
					<div align="left"><?= (trim($rowAbstractDetails['delegatecity']) != '') ? strtoupper($rowAbstractDetails['delegatecity']) : "-" ?></div>
					<div align="left"></div>
					<div align="left"></div>
				</div>
			</div>
			<div width="100%">
				<tr colspan="4">
			</div>
			<div>
				<div colspan="4" align="left" class="thighlight" style="font-size: 24px"><strong>Authorship Details</strong></div>
			</div>
			<div>
				<div width="20%" align="left" valign="top"><b>Name</b></div>
				<div width="30%" align="left" valign="top">
					<?= strtoupper($rowAbstractDetails['abstract_author_name'] ?
						($rowAbstractDetails['abstract_author_title'] . ' ' . $rowAbstractDetails['abstract_author_first_name'] . ' ' . $rowAbstractDetails['abstract_author_last_name'])
						: $rowAbstractDetails['abstract_author_name'] == '') ?></div>
				<div width="20%" align="left" valign="top"><b>Country</b></div>
				<div width="30%" align="left" valign="top"><?= strtoupper($rowAbstractDetails['author_country_name']) ?></div>
			</div>
			<div>
				<div align="left" valign="top"><b>State</b></div>
				<div align="left" valign="top"><?= strtoupper($rowAbstractDetails['author_state_name']) ?></div>
				<div align="left" valign="top"><b>City</b></div>
				<div align="left" valign="top"><?= strtoupper($rowAbstractDetails['abstract_author_city']) ?></div>
			</div>

			<div>
				<div align="left" valign="top"><b> Mobile No</b></div>
				<div align="left" valign="top"><?= strtoupper($rowAbstractDetails['abstract_author_phone_no']) ?></div>
				<div></div>
				<div></div>
			</div>
			<div>
				<div align="left" valign="top"><b> Institute Name</b></div>
				<div align="left" valign="top"><?= strtoupper($rowAbstractDetails['abstract_author_institute_name']) ?></div>
				<div align="left" valign="top"><b>Department</b></div>
				<div align="left" valign="top"><?= strtoupper($rowAbstractDetails['abstract_author_department']) ?></div>
			</div>
		</div>

		<?php
		$coAuthorCounter           = 0;
		$sqlAbstractCoAuthor       = array();
		$sqlAbstractCoAuthor['QUERY']       = "SELECT coauthor.*, 														 
																 country.country_name AS coauthor_country_name,
																 state.state_name AS coauthor_state_name														 
															FROM " . _DB_ABSTRACT_COAUTHOR_ . " coauthor 												
												 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
															  ON coauthor.abstract_coauthor_country_id = country.country_id											 
												 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
															  ON coauthor.abstract_coauthor_state_id = state.st_id															  
														   WHERE coauthor.status = 'A' 
															 AND coauthor.abstract_id = '" . $rowAbstractDetails['id'] . "'";
		$resultAbstractCoAuthor    = $mycms->sql_select($sqlAbstractCoAuthor);

		if ($resultAbstractCoAuthor) {
			foreach ($resultAbstractCoAuthor as $keyAbstractCoAuthor => $rowAbstractCoAuthor) {
				$coAuthorCounter++;
		?>
				<div width="100%">
					<tr colspan="4">
				</div>
				<div>
					<div colspan="4" align="left" class="thighlight" style="font-size: 24px"><strong>Co-author Details <?= $coAuthorCounter ?></strong></div>
				</div>
				<div>
					<div width="20%" align="left" valign="top"><b>Name</b></div>
					<div width="30%" align="left" valign="top"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_name']) ?></div>
				</div>
				<div>
					<div align="left" valign="top"><b>Mobile No</b></div>
					<div align="left" valign="top"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_phone_no']==''?'-':$rowAbstractCoAuthor['abstract_coauthor_phone_no']) ?></div>
					<div align="left" valign="top"><b>Email Id</b></div>
					<div align="left" valign="top"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_email']==''?'-':$rowAbstractCoAuthor['abstract_coauthor_email']) ?></div>
				</div>
				<div>

					<div width="20%" align="left" valign="top"><b>Country</b></div>
					<div width="30%" align="left" valign="top"><?= strtoupper($rowAbstractCoAuthor['coauthor_country_name']) ?></div>
					<div align="left" valign="top"><b>State</b></div>
					<div align="left" valign="top"><?= strtoupper($rowAbstractCoAuthor['coauthor_state_name']) ?></div>
				</div>
				<div>
					<div align="left" valign="top"><b>City</b></div>
					<div align="left" valign="top"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_city_name']==''?'-':$rowAbstractCoAuthor['abstract_coauthor_city_name']) ?></div>
				</div>
				<div>
					<div align="left" valign="top"><b>Institute Name</b></div>
					<div align="left" valign="top"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_institute_name']==''?'-':$rowAbstractCoAuthor['abstract_coauthor_institute_name']) ?></div>
					<div align="left" valign="top"><b>Department</b></div>
					<div align="left" valign="top"><?= strtoupper($rowAbstractCoAuthor['abstract_coauthor_department']==''?'-':$rowAbstractCoAuthor['abstract_coauthor_department']) ?></div>

				</div>
				

	</div>
<?php
			}
		}
?>
<?
	if ($rowAbstractDetails['tags'] == 'Abstract') {
?>
	<div width="100%">
		<tr colspan="4">
	</div>
	<div>
		<div colspan="4" align="left" class="thighlight" style="font-size: 24px"><strong>Abstract Details</strong></div>
	</div>
	<?php if (!empty($rowAbstractDetails['abstract_title'])) { ?>
		<div>
			<div align="left" valign="top" width="20%"><b>TITLE</b></div>
		</div>
		<div>
			<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= htmlentities($rowAbstractDetails['abstract_title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
		</div>
	<?php }
		if (!empty($rowAbstractDetails['abstract_topic'])) { ?>
		<div>
			<div align="left" valign="top" width="20%"><b>TOPIC</b></div>
		</div>
		<div>
			<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= htmlentities($rowAbstractDetails['abstract_topic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
		</div>
	<?php } ?>


	<!-- <div>
							<div align="left" valign="top" width="20%"><strong>Background</strong></div>
						</div>
						<div>
							<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= $rowAbstractDetails['abstract_background'] ?></div>
						</div>
						<div>
							<div  align="left" valign="top" width="20%"><b>Aims and Objectives</b></div>
						</div>
						<div>
							<div colspan="3"  align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= $rowAbstractDetails['abstract_background_aims'] ?></div>
						</div>
						<div>
							<div align="left" valign="top" width="20%"><b>Methods and Material</b></div>
						</div>
						<div>
						
							<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= $rowAbstractDetails['abstract_material_methods'] ?></div>
							
						</div>
						<div>
							<div align="left" valign="top" width="20%"><b>Results</b></div>
						</div>
						<div>
							<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= $rowAbstractDetails['abstract_results'] ?></div>
						</div>
						<div>
							<div align="left" valign="top" width="20%"><b>Conclusion</b></div>
						</div>
						<div>
							<div colspan="3" align="left" valign="top" ><?= $rowAbstractDetails['abstract_conclution'] ?></div>
						</div>
						<div>
							<div align="left" valign="top" width="20%"><b>Description</b></div>
						</div>
						<div>
							<div colspan="3" align="left" valign="top" ><?= $rowAbstractDetails['abstract_description'] ?></div>
						</div> -->

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
					<?	$fixedText = $rowAbstractDetails[$value['field_key']] ?>

						
				<div colspan="3" align="left" valign="top"><?php echo $fixedText; ?></div>
			</div>
	<?php
			}
		}
	?>
	</div>
<?
	} elseif ($rowAbstractDetails['tags'] == 'Case report') {
?>
	<div width="100%">
		<tr colspan="4">
	</div>
	<div>
		<div colspan="4" align="left" class="thighlight" style="font-size: 24px"><strong>Case Report Details</strong></div>
	</div>
	<?php if (!empty($rowAbstractDetails['abstract_title'])) { ?>
		<div>
			<div align="left" valign="top" width="20%"><b>TITLE</b></div>
		</div>
		<div>
			<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= $rowAbstractDetails['abstract_title'] ?></div>
		</div>
	<?php } ?>
	<!--<div>
							<div align="left" valign="top" width="20%"><b>TOPIC</b></div>
						</div>
						<div>
							<div colspan="3" align="left" valign="top" style="border-bottom:thin solid #ccc;"><?= $rowAbstractDetails['abstract_topic'] ?></div>
						</div>-->
	<div>
		<div align="left" valign="top" width="20%"><b>Introduction</b></div>
	</div>
	<div>
		<div colspan="3" align="left" style="border-bottom:thin solid #ccc;"><?= $rowAbstractDetails['abstract_background_aims'] ?></div>
	</div>
	<!--<div>
							<div  align="left" valign="top" width="20%"><b>Case Report</b></div>
						</div>
						<div>
							<div colspan="3"  align="left" style="border-bottom:thin solid #ccc;"><?= $rowAbstractDetails['abstract_description'] ?></div>
							
						</div>-->
	<div>
		<div align="left" valign="top" width="20%"><b>Discussion</b></div>
	</div>
	<div>
		<div colspan="3" align="left" valign="top"><?= $rowAbstractDetails['abstract_results'] ?></div>
	</div>
	</div>
<?
	}
?>
</div>
</div>
</div>
<?php
}
?>