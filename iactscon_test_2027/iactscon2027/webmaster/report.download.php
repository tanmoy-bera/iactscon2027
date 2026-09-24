<?php
include_once('includes/init.php');

include_once('../../includes/function.invoice.php');
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.registration.php');
include_once('../../includes/function.workshop.php');

$loggedUserID = $mycms->getLoggedUserId();
$action		  = $_REQUEST['act'];
switch ($action) {

	case 'downloadLunchReport':
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=LunchReport-" . time() . ".xls");
?>

		<table width="100%" class="tborder" align="center">
			<!-- <tr>
			<td class="tcat" colspan="2" align="left">
				<span style="float:left; margin-right:20px;">Lunch Report (<?= $date ?>)</span>
			</td>
		</tr> -->
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%" shortData="on">
						<thead>
							<tr class="theader">
								<td width="" align="center" data-sort="int">Sl No</td>
								<td align="">Name & Contact</td>
								<td width="" align="center" data-sort="int">Unique Sequence</td>
								<td width="" align="left">Registration Type</td>
								<td width="" align="left">Registration Details</td>
								<td width="" align="left">Tracking Time</td>

							</tr>
						</thead>
						<tbody>
							<?php


							$sqlFetchUser	 = array();
							$sqlFetchUser['QUERY']			   = "SELECT delegate.*, 
													 
													 registrationClassification.classification_title,lunchtracking.remarks,
													 lunchtracking.created_dateTime AS trackingdate
											 
												FROM " . _DB_USER_LUNCH_TRACKING_ . "
													  AS lunchtracking
										
										  LEFT JOIN " . _DB_USER_REGISTRATION_ . " delegate 
										  		  ON delegate.id = lunchtracking.user_id			
										
										
							 		 LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
									      		  ON delegate.registration_classification_id = registrationClassification.id
												  										  
									   		   WHERE (delegate.status = 'A' OR lunchtracking.user_id< 0)
									     		
									     		 AND (delegate.account_status = 'REGISTERED' OR lunchtracking.user_id< 0)
										 		 AND lunchtracking.status = 'A' " . $searchCondition . "
											ORDER BY lunchtracking.created_dateTime DESC";
							//  AND ( lunchtracking.conference_date_id = '" . $_REQUEST['dateId'] . "' OR lunchtracking.created_dateTime = '" . $_REQUEST['date'] . "' )

							$resultFetchUser           = $mycms->sql_select($sqlFetchUser);

							if ($resultFetchUser) {
								foreach ($resultFetchUser as $i => $rowFetchUser) {
									$counter             = $counter + 1;

							?>
									<tr class="tlisting">
										<td align="center" valign="top"><?= $counter + ($_REQUEST['_pgn1_'] * 10) ?></td>
										<td align="center" valign="top">
											<?= strtoupper($rowFetchUser['user_full_name'] == '' ? 'ADDITIONAL- ' . $rowFetchUser['remark'] : $rowFetchUser['user_full_name']) ?>


											<br />
											<?= $rowFetchUser['user_email_id'] ?>
										</td>
										<td align="center" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
										<td align="left" valign="top">
											<?php
											if ($rowFetchUser['registration_request'] == "EXHIBITOR" || $rowFetchUser['registration_request'] == "GUEST" || $rowFetchUser['registration_request'] == "VOLUNTEER") {
												echo $rowFetchUser['registration_request'];
											} else if ($rowFetchUser['isRegistration'] == "Y") {
												echo $rowFetchUser['classification_title'];
											}
											?>
										</td>
										<td align="left" valign="top">

											<?php
											if (
												$rowFetchUser['registration_payment_status'] == "PAID"
												|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
												|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
											) {
												echo $rowFetchUser['user_registration_id'];
												echo "<br />";
											} else {
												echo "-";
												echo "<br />";
											}
											?>
											<?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?>
										</td>

										<td align="left" valign="top"><?= $rowFetchUser['trackingdate'] ?></td>

									</tr>
								<?php
								}
							} else {
								?>
								<tr>
									<td colspan="7" align="center">
										<span class="mandatory">No Record Present.</span>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>

				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
				</td>
			</tr>
		</table>
	<?
		exit();
		break;

	case 'downloadKitReport':
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=KitReport-" . time() . ".xls");
	?>

		<table width="100%" class="tborder" align="center">
			<!-- <tr>
				<td class="tcat" colspan="2" align="left">
					<span style="float:left; margin-right:20px;">Lunch Report (<?= $date ?>)</span>
				</td>
			</tr> -->
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%" shortData="on">
						<thead>
							<tr class="theader">
								<td width="" align="center" data-sort="int">Sl No</td>
								<td align="">Name & Contact</td>
								<td width="" align="center" data-sort="int">Unique Sequence</td>
								<td width="" align="left">Registration Type</td>
								<td width="" align="left">Registration Details</td>
								<td width="" align="left">Tracking Time</td>

							</tr>
						</thead>
						<tbody>
							<?php


							$sqlFetchUser	 = array();
							$sqlFetchUser['QUERY']			   = "SELECT delegate.*, 
														 
														 registrationClassification.classification_title,
														 kittracking.remarks,
														 kittracking.created_dateTime AS trackingdate
												 
													FROM " . _DB_USER_KIT_TRACKING_ . "
														  AS kittracking
											
											  LEFT JOIN " . _DB_USER_REGISTRATION_ . " delegate 
														ON delegate.id = kittracking.user_id			
											
											
										  LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
														ON delegate.registration_classification_id = registrationClassification.id
																								
													  WHERE (delegate.status = 'A' OR kittracking.user_id< 0)
													 
													  AND (delegate.account_status = 'REGISTERED' OR kittracking.user_id< 0)
													  AND kittracking.status = 'A' " . $searchCondition . "
												ORDER BY kittracking.created_dateTime DESC";
							//  AND ( kittracking.conference_date_id = '" . $_REQUEST['dateId'] . "' OR kittracking.created_dateTime = '" . $_REQUEST['date'] . "' )

							$resultFetchUser           = $mycms->sql_select($sqlFetchUser);

							if ($resultFetchUser) {
								foreach ($resultFetchUser as $i => $rowFetchUser) {
									$counter             = $counter + 1;

							?>
									<tr class="tlisting">
										<td align="center" valign="top"><?= $counter + ($_REQUEST['_pgn1_'] * 10) ?></td>
										<td align="center" valign="top">
											<?= strtoupper($rowFetchUser['user_full_name'] == '' ? 'ADDITIONAL- ' . $rowFetchUser['remarks'] : $rowFetchUser['user_full_name']) ?>


											<br />
											<?= $rowFetchUser['user_email_id'] ?>
										</td>
										<td align="center" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
										<td align="left" valign="top">
											<?php
											if ($rowFetchUser['registration_request'] == "EXHIBITOR" || $rowFetchUser['registration_request'] == "GUEST" || $rowFetchUser['registration_request'] == "VOLUNTEER") {
												echo $rowFetchUser['registration_request'];
											} else if ($rowFetchUser['isRegistration'] == "Y") {
												echo $rowFetchUser['classification_title'];
											}
											?>
										</td>
										<td align="left" valign="top">

											<?php
											if (
												$rowFetchUser['registration_payment_status'] == "PAID"
												|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
												|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
											) {
												echo $rowFetchUser['user_registration_id'];
												echo "<br />";
											} else {
												echo "-";
												echo "<br />";
											}
											?>
											<?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?>
										</td>

										<td align="left" valign="top"><?= $rowFetchUser['trackingdate'] ?></td>

									</tr>
								<?php
								}
							} else {
								?>
								<tr>
									<td colspan="7" align="center">
										<span class="mandatory">No Record Present.</span>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>

				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
				</td>
			</tr>
		</table>
	<?
		exit();
		break;

	case 'downloadDinnerReport':
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=DinnerReport-" . time() . ".xls");
	?>

		<table width="100%" class="tborder" align="center">
			<!-- <tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left; margin-right:20px;">Lunch Report (<?= $date ?>)</span>
					</td>
				</tr> -->
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%" shortData="on">
						<thead>
							<tr class="theader">
								<td width="" align="center" data-sort="int">Sl No</td>
								<td align="">Name & Contact</td>
								<td width="" align="center" data-sort="int">Unique Sequence</td>
								<td width="" align="left">Registration Type</td>
								<td width="" align="left">Registration Details</td>
								<td width="" align="left">Tracking Time</td>

							</tr>
						</thead>
						<tbody>
							<?php


							$sqlFetchUser	 = array();
							$sqlFetchUser['QUERY']			   = "SELECT delegate.*, 
															 
															 registrationClassification.classification_title,
															 dinnertracking.remarks,
															 dinnertracking.created_dateTime AS trackingdate
													 
														FROM " . _DB_USER_DINNER_TRACKING_ . "
															  AS dinnertracking
												
												  LEFT JOIN " . _DB_USER_REGISTRATION_ . " delegate 
															ON delegate.id = dinnertracking.user_id			
												
												
											  LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
															ON delegate.registration_classification_id = registrationClassification.id
																									
														  WHERE (delegate.status = 'A' OR dinnertracking.user_id< 0)
														 
														  AND (delegate.account_status = 'REGISTERED' OR dinnertracking.user_id< 0)
														  AND dinnertracking.status = 'A' " . $searchCondition . "
													ORDER BY dinnertracking.created_dateTime DESC";
							//  AND ( dinnertracking.conference_date_id = '" . $_REQUEST['dateId'] . "' OR dinnertracking.created_dateTime = '" . $_REQUEST['date'] . "' )

							$resultFetchUser           = $mycms->sql_select($sqlFetchUser);

							if ($resultFetchUser) {
								foreach ($resultFetchUser as $i => $rowFetchUser) {
									$counter             = $counter + 1;

							?>
									<tr class="tlisting">
										<td align="center" valign="top"><?= $counter + ($_REQUEST['_pgn1_'] * 10) ?></td>
										<td align="center" valign="top">
											<?= strtoupper($rowFetchUser['user_full_name'] == '' ? 'ADDITIONAL- ' . $rowFetchUser['remarks'] : $rowFetchUser['user_full_name']) ?>


											<br />
											<?= $rowFetchUser['user_email_id'] ?>
										</td>
										<td align="center" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
										<td align="left" valign="top">
											<?php
											if ($rowFetchUser['registration_request'] == "EXHIBITOR" || $rowFetchUser['registration_request'] == "GUEST" || $rowFetchUser['registration_request'] == "VOLUNTEER") {
												echo $rowFetchUser['registration_request'];
											} else if ($rowFetchUser['isRegistration'] == "Y") {
												echo $rowFetchUser['classification_title'];
											}
											?>
										</td>
										<td align="left" valign="top">

											<?php
											if (
												$rowFetchUser['registration_payment_status'] == "PAID"
												|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
												|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
											) {
												echo $rowFetchUser['user_registration_id'];
												echo "<br />";
											} else {
												echo "-";
												echo "<br />";
											}
											?>
											<?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?>
										</td>

										<td align="left" valign="top"><?= $rowFetchUser['trackingdate'] ?></td>

									</tr>
								<?php
								}
							} else {
								?>
								<tr>
									<td colspan="7" align="center">
										<span class="mandatory">No Record Present.</span>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>

				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
				</td>
			</tr>
		</table>
	<?
		exit();
		break;

	case 'downloadBreakfastReport':
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=BreakfastReport-" . time() . ".xls");
	?>

		<table width="100%" class="tborder" align="center">
			<!-- <tr>
						<td class="tcat" colspan="2" align="left">
							<span style="float:left; margin-right:20px;">Lunch Report (<?= $date ?>)</span>
						</td>
					</tr> -->
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%" shortData="on">
						<thead>
							<tr class="theader">
								<td width="" align="center" data-sort="int">Sl No</td>
								<td align="">Name & Contact</td>
								<td width="" align="center" data-sort="int">Unique Sequence</td>
								<td width="" align="left">Registration Type</td>
								<td width="" align="left">Registration Details</td>
								<td width="" align="left">Tracking Time</td>

							</tr>
						</thead>
						<tbody>
							<?php


							$sqlFetchUser	 = array();
							$sqlFetchUser['QUERY']			   = "SELECT delegate.*, 
																 
																 registrationClassification.classification_title,
																  Breakfasttracking.remarks,
																 Breakfasttracking.created_dateTime AS trackingdate
														 
															FROM " . _DB_USER_BREAKFAST_TRACKING_ . "
																  AS Breakfasttracking
													
													  LEFT JOIN " . _DB_USER_REGISTRATION_ . " delegate 
																ON delegate.id = Breakfasttracking.user_id			
													
													
												  LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
																ON delegate.registration_classification_id = registrationClassification.id
																										
															  WHERE (delegate.status = 'A' OR Breakfasttracking.user_id< 0)
															 
															  AND (delegate.account_status = 'REGISTERED' OR Breakfasttracking.user_id< 0)
															  AND Breakfasttracking.status = 'A' " . $searchCondition . "
														ORDER BY Breakfasttracking.created_dateTime DESC";
							//  AND ( Breakfasttracking.conference_date_id = '" . $_REQUEST['dateId'] . "' OR Breakfasttracking.created_dateTime = '" . $_REQUEST['date'] . "' )

							$resultFetchUser           = $mycms->sql_select($sqlFetchUser);
							// echo '<pre>'; print_r($resultFetchUser);die;
							if ($resultFetchUser) {
								foreach ($resultFetchUser as $i => $rowFetchUser) {
									$counter             = $counter + 1;

							?>
									<tr class="tlisting">
										<td align="center" valign="top"><?= $counter + ($_REQUEST['_pgn1_'] * 10) ?></td>
										<td align="center" valign="top">
											<?= strtoupper($rowFetchUser['user_full_name'] == '' ? 'ADDITIONAL- ' . $rowFetchUser['remarks'] : $rowFetchUser['user_full_name']) ?>


											<br />
											<?= $rowFetchUser['user_email_id'] ?>
										</td>
										<td align="center" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
										<td align="left" valign="top">
											<?php
											if ($rowFetchUser['registration_request'] == "EXHIBITOR" || $rowFetchUser['registration_request'] == "GUEST" || $rowFetchUser['registration_request'] == "VOLUNTEER") {
												echo $rowFetchUser['registration_request'];
											} else if ($rowFetchUser['isRegistration'] == "Y") {
												echo $rowFetchUser['classification_title'];
											}
											?>
										</td>
										<td align="left" valign="top">

											<?php
											if (
												$rowFetchUser['registration_payment_status'] == "PAID"
												|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
												|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
											) {
												echo $rowFetchUser['user_registration_id'];
												echo "<br />";
											} else {
												echo "-";
												echo "<br />";
											}
											?>
											<?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?>
										</td>

										<td align="left" valign="top"><?= $rowFetchUser['trackingdate'] ?></td>

									</tr>
								<?php
								}
							} else {
								?>
								<tr>
									<td colspan="7" align="center">
										<span class="mandatory">No Record Present.</span>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>

				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
				</td>
			</tr>
		</table>
	<?
		exit();
		break;

	case 'downloadExhibitorReport':
		ini_set('max_execution_time', 1000);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/octet-stream");
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=ExhibitorReport-" . time() . ".xls");
	?>

		<table width="100%" class="tborder" align="center">
			<!-- <tr>
							<td class="tcat" colspan="2" align="left">
								<span style="float:left; margin-right:20px;">Lunch Report (<?= $date ?>)</span>
							</td>
						</tr> -->
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%" shortData="on">
						<thead>
							<tr class="theader">
								<td width="" align="center" data-sort="int">Sl No</td>
								<td align="">Name & Contact</td>
								<td width="" align="center" data-sort="int">Unique Sequence</td>
								<td width="" align="left">Registration Type</td>
								<td width="" align="left">Registration Details</td>
								<td width="" align="left">Tracking Time</td>

							</tr>
						</thead>
						<tbody>
							<?php


							$sqlFetchUser	 = array();
							$sqlFetchUser['QUERY']			   = "SELECT delegate.*, 
																	 
																	 registrationClassification.classification_title,
																	  Breakfasttracking.remarks,
																	 Breakfasttracking.created_dateTime AS trackingdate
															 
																FROM " . _DB_USER_EXHIBITOR_TRACKING_ . "
																	  AS Breakfasttracking
														
														  LEFT JOIN " . _DB_USER_REGISTRATION_ . " delegate 
																	ON delegate.id = Breakfasttracking.user_id			
														
														
													  LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
																	ON delegate.registration_classification_id = registrationClassification.id
																											
																  WHERE (delegate.status = 'A' OR Breakfasttracking.user_id< 0)
																 
																  AND (delegate.account_status = 'REGISTERED' OR Breakfasttracking.user_id< 0)
																  AND Breakfasttracking.status = 'A' " . $searchCondition . "
															ORDER BY Breakfasttracking.created_dateTime DESC";
							//  AND ( Breakfasttracking.conference_date_id = '" . $_REQUEST['dateId'] . "' OR Breakfasttracking.created_dateTime = '" . $_REQUEST['date'] . "' )

							$resultFetchUser           = $mycms->sql_select($sqlFetchUser);
							// echo '<pre>'; print_r($resultFetchUser);die;
							if ($resultFetchUser) {
								foreach ($resultFetchUser as $i => $rowFetchUser) {
									$counter             = $counter + 1;

							?>
									<tr class="tlisting">
										<td align="center" valign="top"><?= $counter + ($_REQUEST['_pgn1_'] * 10) ?></td>
										<td align="center" valign="top">
											<?= strtoupper($rowFetchUser['user_full_name'] == '' ? 'ADDITIONAL- ' . $rowFetchUser['remarks'] : $rowFetchUser['user_full_name']) ?>


											<br />
											<?= $rowFetchUser['user_email_id'] ?>
										</td>
										<td align="center" valign="top"><?= strtoupper($rowFetchUser['user_unique_sequence']) ?></td>
										<td align="left" valign="top">
											<?php
											if ($rowFetchUser['registration_request'] == "EXHIBITOR" || $rowFetchUser['registration_request'] == "GUEST" || $rowFetchUser['registration_request'] == "VOLUNTEER") {
												echo $rowFetchUser['registration_request'];
											} else if ($rowFetchUser['isRegistration'] == "Y") {
												echo $rowFetchUser['classification_title'];
											}
											?>
										</td>
										<td align="left" valign="top">

											<?php
											if (
												$rowFetchUser['registration_payment_status'] == "PAID"
												|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
												|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
											) {
												echo $rowFetchUser['user_registration_id'];
												echo "<br />";
											} else {
												echo "-";
												echo "<br />";
											}
											?>
											<?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?>
										</td>

										<td align="left" valign="top"><?= $rowFetchUser['trackingdate'] ?></td>

									</tr>
								<?php
								}
							} else {
								?>
								<tr>
									<td colspan="7" align="center">
										<span class="mandatory">No Record Present.</span>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>

				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
				</td>
			</tr>
		</table>
<?
		exit();
		break;
}
?>