<?php
include_once('includes/init.php');

page_header("Accomodation Tariff");

$pageKey                       		       = "_pgn_";
$pageKeyVal                    		       = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString                 		       = "";
$searchArray                   		       = array();

$searchArray[$pageKey]         		       = $pageKeyVal;
foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}
?>
<div class="body_wrap">
	<?php
	switch ($show) {

		// TARIFF CLASSIFICATION EDIT FORM LAYOUT
		case 'edit':

			tariffClassificationEditFormLayout($cfg, $mycms);
			break;

		// TARIFF CLASSIFICATION LISTING LAYOUT
		default:

			tariffClassificationListingLayout($cfg, $mycms);
			break;
	}
	?>
</div>
<?php

page_footer();

/**********************************************************************/
/*                TARIFF CLASSIFICATION LISTING LAYOUT                */
/**********************************************************************/
function tariffClassificationListingLayout($cfg, $mycms)
{

?><div class="body_content_box">
		<div class="con_box-grd row">
			<div class="table_wrap">
				<table width="100%" class="tborder">
					<?
					$counter                 = 0;
					$searchCondition         = "";

					$displaydata 	= array();
					$titleheaeder 	= array();

					$sql 	=	array();
					$sql['QUERY']	=	"SELECT id, cutoff.cutoff_title  
												FROM " . _DB_TARIFF_CUTOFF_ . " cutoff
											   WHERE status = ?";
					$sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
					$resCutoff = $mycms->sql_select($sql);
					$cutOffsArray = array();
					?>
					<thead>
						<tr class="theader">
							<th rowspan="2" colspan="2" align="center">Package</th>
							<?
							foreach ($resCutoff as $key => $title) {
							?>
								<th align="right" colspan="2"><?= strip_tags($title['cutoff_title']) ?></th>
							<?
							}
							?>
						</tr>
						<tr class="theader">
							<?
							foreach ($resCutoff as $key => $title) {
							?>
								<th align="right">INR</th>
								<th align="right">USD</th>
							<?
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						// FETCH ACCCOMODATION DETAILS
						//$accomodationDetails = accomodationDetailsBackend();						
						$dataArray = array();
						$rowCount  = 0;

						$sqlHotel	=	array();
						$sqlHotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . " hotel WHERE status =?";
						$sqlHotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
						$resHotels = $mycms->sql_select($sqlHotel);
						foreach ($resHotels as $keyHotel => $rowHotels) {

							// get the min date of chekin 
							$packageCheckDate	=	array();
							$packageCheckDate['QUERY'] = "select id, min(check_in_date) as MinCheckIn
															FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " check_in_date 
														    WHERE status = ? 
														      AND hotel_id = ?
															  GROUP BY `id`";
							$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		        'TYP' => 's');
							$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 	 'DATA' => $rowHotels['id'], 		  'TYP' => 's');
							$resCheckIns = $mycms->sql_select($packageCheckDate);
							$rowCheckIn =	$resCheckIns[0];


							// get the min date of chekout							
							$packageCheckoutDate	=	array();
							$packageCheckoutDate['QUERY']  = "select *
																FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " check_in_out 
															    WHERE status = ?
																  AND hotel_id = ?";
							$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
							$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 	 'DATA' => $rowHotels['id'], 		  'TYP' => 's');
							$resCheckOut = $mycms->sql_select($packageCheckoutDate);
							$rowCheckOut =	$resCheckOut[0];


							$package	=	array();
							$package['QUERY'] = "select * 
												   FROM " . _DB_ACCOMMODATION_PACKAGE_ . " package 
												  WHERE package.hotel_id = ? 
												    AND status = ?";
							$package['PARAM'][]	=	array('FILD' => 'package.hotel_id', 		'DATA' => $rowHotels['id'], 		'TYP' => 's');
							$package['PARAM'][]	=	array('FILD' => 'status', 					'DATA' => 'A', 		        'TYP' => 's');
							$resPackage = $mycms->sql_select($package);
							foreach ($resPackage as $keyPackage => $package) {

								$dataArray[$rowCount]['HOTEL'] = $rowHotels['hotel_name'];
								$dataArray[$rowCount]['PACKAGE'] = $package['package_name'];
								$dataArray[$rowCount]['hotelId'] = $rowHotels['id'];
								$dataArray[$rowCount]['packageId'] = $package['id'];

								$sqlcutoff	=	array();
								//query in cutoff table
								$sqlcutoff['QUERY'] = "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " cutof WHERE status = ?";
								$sqlcutoff['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
								$resCutoff = $mycms->sql_select($sqlcutoff);
								foreach ($resCutoff as $keyCutoff => $rowCutoff) {
									$dataArray[$rowCount]['CUTOFF'][$rowCutoff['id']]['TITLE'] = $rowCutoff['cutoff_title'];
									$dataArray[$rowCount]['CUTOFF'][$rowCutoff['id']]['cutoffId'] = $rowCutoff['id'];
									$sqlPackageCheckoutDate	=	array();
									// query in tariff table
									$sqlPackageCheckoutDate['QUERY'] = "select * 
																		  FROM " . _DB_TARIFF_ACCOMMODATION_ . " accomodation
																		 WHERE status = ?
																		   AND tariff_cutoff_id = ?
																		   AND hotel_id = ?
																		   AND package_id = ?";
									$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 					'TYP' => 's');
									$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id', 'DATA' => $rowCutoff['id'], 		'TYP' => 's');
									$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $rowHotels['id'], 		'TYP' => 's');
									$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'package_id', 		'DATA' => $package['id'], 		'TYP' => 's');
									$resPackageCheckoutDate = $mycms->sql_select($sqlPackageCheckoutDate);

									//echo '<pre>'; print_r($resPackageCheckoutDate);
									foreach ($resPackageCheckoutDate as $keyPrice => $price) {
										$dataArray[$rowCount]['CUTOFF'][$rowCutoff['id']]['INR'] = $price['inr_amount'];
										$dataArray[$rowCount]['CUTOFF'][$rowCutoff['id']]['USD'] = $price['usd_amount'];
									}
								}
								$rowCount++;
							}
						}
						// echo '<pre>';
						// print_r($dataArray);
						// echo '<pre>';
						foreach ($dataArray as $key => $AccomodationArray) {
						?>
							<tr class="thighlight">
								<td rowspan="2"><?= $AccomodationArray['HOTEL']; ?></td>
								<td rowspan="2"><?= $AccomodationArray['PACKAGE']; ?></td>
								<?
								$cutoffPriceArray = $AccomodationArray['CUTOFF'];
								foreach ($resCutoff as $key => $cutoffP) {
								?>
									<td><?= isset($cutoffPriceArray[$cutoffP['id']]['INR']) ? $cutoffPriceArray[$cutoffP['id']]['INR'] : 'N/A' ?></td>
									<td><?= isset($cutoffPriceArray[$cutoffP['id']]['USD']) ? $cutoffPriceArray[$cutoffP['id']]['USD'] : 'N/A' ?></td>
								<?
								}
								?>
							</tr>
							<tr>
								<?
								foreach ($resCutoff as $key => $cutoffP) {
								?>
									<td colspan="2"><a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="accomodation_tariff.php?show=edit&hotel_id=<?= $AccomodationArray['hotelId'] ?>&package_id=<?= $AccomodationArray['packageId'] ?>&cutoff_id=<?= $cutoffP['id'] ?>"><span alt="Edit" title="Edit Record" class="icon-pen"></span></a></td>
								<?
								}
								?>
							</tr>
						<?
						}

						?>
					</tbody>
				</table>
				<div class="bbp-pagination">
					<div class="bbp-pagination-count"><?= $mycms->paginateRecInfo($indexVal) ?></div>
					<span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
				</div>
			</div>
		</div>
	</div>
<?php
}

/**************************************************************/
/*                       EDIT TARIFF FORM                     */
/**************************************************************/
function tariffClassificationEditFormLayout($cfg, $mycms)
{

	$hotelId		  =	$_REQUEST['hotel_id'];
	$packageId       = $_REQUEST['package_id'];
	$cutoff_id  	  = $_REQUEST['cutoff_id'];

	$sqlHotel =	array();
	$sqlHotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . " hotel WHERE status = ? AND id = ?";
	$sqlHotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 				'TYP' => 's');
	$sqlHotel['PARAM'][]	=	array('FILD' => 'id', 			'DATA' => $hotelId, 			'TYP' => 's');
	$resHotels = $mycms->sql_select($sqlHotel);
	$resHotels = $resHotels[0];

	$package =	array();
	$package['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_PACKAGE_ . " package WHERE package.hotel_id = ?  AND status = ? And id = ?";
	$package['PARAM'][]	=	array('FILD' => 'package.hotel_id', 	'DATA' => $resHotels['id'], 			'TYP' => 's');
	$package['PARAM'][]	=	array('FILD' => 'status', 				'DATA' => 'A', 						'TYP' => 's');
	$package['PARAM'][]	=	array('FILD' => 'id', 					'DATA' => $packageId, 					'TYP' => 's');

	$resPackage = $mycms->sql_select($package);
	$resPackages = $resPackage[0];

	$sqlPackageCheckoutDate =	array();
	$sqlPackageCheckoutDate['QUERY'] = "select * 
												  FROM " . _DB_TARIFF_ACCOMMODATION_ . " accomodation WHERE status = ?
												   AND tariff_cutoff_id = ?
												   AND hotel_id = ?
												   AND package_id = ?";
	$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 				'DATA' => 'A', 		'TYP' => 's');
	$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'tariff_cutoff_id', 	'DATA' => $cutoff_id,  'TYP' => 's');
	$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 			'DATA' => $hotelId, 	'TYP' => 's');
	$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'package_id', 			'DATA' => $packageId, 	'TYP' => 's');
	$resPackageCheckoutDate = $mycms->sql_select($sqlPackageCheckoutDate);

	$rowAmount = $resPackageCheckoutDate[0];

	$dates = array();
	$dCount = 0;
	$packageCheckDate = array();
	$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
												   WHERE `hotel_id` = ?
													 AND `status` = ?
											    ORDER BY  check_in_date";
	$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $hotelId, 	'TYP' => 's');
	$packageCheckDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 		'TYP' => 's');
	$resCheckIns = $mycms->sql_select($packageCheckDate);

	foreach ($resCheckIns as $key => $rowCheckIn) {
		$packageCheckoutDate = array();
		$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'" . $rowCheckIn['check_in_date'] . "',`check_out_date`) AS dayDiff
													   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
													  WHERE `hotel_id` = ?
													    AND `status` = ?
													    AND `check_out_date` > ?
												   ORDER BY check_out_date";
		$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id', 		'DATA' => $hotelId, 	    'TYP' => 's');
		$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
		$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date',	'DATA' => $rowCheckIn['check_in_date'], 			'TYP' => 's');


		$resCheckOut = $mycms->sql_select($packageCheckoutDate);
		//echo '<pre>'; print_r($resCheckOut);
		foreach ($resCheckOut as $key => $rowCheckOut) {
			$dates[$dCount]['CHECKIN'] 	  =  $rowCheckIn['check_in_date'];
			$dates[$dCount]['CHECKINID']  =  $rowCheckIn['id'];
			$dates[$dCount]['CHECKOUT']   =  $rowCheckOut['check_out_date'];
			$dates[$dCount]['CHECKOUTID'] =  $rowCheckOut['id'];
			$dates[$dCount]['DAYDIFF']    =  $rowCheckOut['dayDiff'];

			$dCount++;
		}
	}
?>
	<script>
		var dates = [
			<?
			$pDateArr = array();
			foreach ($dates as $k => $vals) {
				$pDateArr[] = '{"checkin":"' . $vals['CHECKIN'] . '","checkinId":"' . $vals['CHECKINID'] . '", "checkout":"' . $vals['CHECKOUT'] . '","checkoutId":"' . $vals['CHECKOUTID'] . '", "days":"' . $vals['DAYDIFF'] . '"}';
			}
			echo implode(",", $pDateArr);
			?>
		];
	</script>
	<?
	$sqlcutoff = array();
	$sqlcutoff['QUERY'] = "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . "		
										    WHERE `id`	= ?
										     AND status = ?";
	$sqlcutoff['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $cutoff_id, 	    'TYP' => 's');
	$sqlcutoff['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 	    'TYP' => 's');
	$resCutoff 			= $mycms->sql_select($sqlcutoff);
	$rowCutoff			= $resCutoff[0];
	?>

	<form action="accomodation_tariff.process.php" method="post" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="edit" />
		<input type="hidden" name="cutoff" value="<?= $cutoff_id ?>" />
		<table class="tborder" style="width:50%;">
			<tr class="tlisting">
				<td align="">Hotel Name</td>
				<td align="" colspan="2">
					<?= $resHotels['hotel_name']; ?>
					<input type="hidden" name="hotel_id" value="<?= $hotelId ?>">
				</td>
			</tr>
			<tr class="tlisting">
				<td align="">Package Name</td>
				<td align="" colspan="2">
					<?= $resPackages['package_name']; ?>
					<input type="hidden" name="package_id" value="<?= $packageId ?>">
				</td>
			</tr>
			<tr class="tlisting">
				<td align="">Cutoff Title</td>
				<td align="" colspan="2">
					<?= $rowCutoff['cutoff_title']; ?>
				</td>
			</tr>
			<tr class="tlisting">
				<td align="">Rate/Night(INR)</td>
				<td align="" colspan="2"><input style="width:80%;" type="number" name="rate_per_night_inr" use="rate_per_night_inr" id="rate_per_night_inr" value="<?= $rowAmount['inr_amount'] ?>"></td>
			</tr>
			<tr class="tlisting">
				<td align="">Rate/Night(USD)</td>
				<td align="" colspan="2"><input style="width:80%;" type="number" name="rate" id="rate_per_night_usd" value="<?= ($rowAmount['usd_amount'] != "") ? $rowAmount['usd_amount'] : "0.00" ?>"></td>
			</tr>
			<tr>
				<table use="rateDes" style="width: 50%; border: 1px solid rgb(0, 0, 0); margin: 10px; text-align: center; border-collapse: collapse;">
					<?

					?>
					<tr>
						<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check In Date</th>
						<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check Out Date</th>
						<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">INR Rate</th>
						<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">USD Rate</th>
					</tr>
					<?php foreach ($resPackageCheckoutDate as $key => $accomodationTariff) {
						$packageCheckoutDate 	=	array();
						$packageCheckoutDate['QUERY'] = "SELECT * 
													   FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " _DB_ACCOMMODATION_CHECKIN_DATE_
													  WHERE `id` = ?
													    AND `status` = ?
												   ORDER BY check_out_date";
						$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $accomodationTariff['checkout_date_id'], 	    'TYP' => 's');
						$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');
						$respackageCheckoutDate 		= $mycms->sql_select($packageCheckoutDate);
						$rowpackageCheckoutDate			= $respackageCheckoutDate[0];

						$packageCheckinDate 	=	array();
						$packageCheckinDate['QUERY'] = "SELECT * 
													   FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
													  WHERE `id` = ?
													    AND `status` = ?
												   ORDER BY check_in_date";
						$packageCheckinDate['PARAM'][]	=	array('FILD' => 'id', 				'DATA' => $accomodationTariff['checkin_date_id'], 	    'TYP' => 's');
						$packageCheckinDate['PARAM'][]	=	array('FILD' => 'status', 			'DATA' => 'A', 			'TYP' => 's');

						$respackageCheckinDate 		= $mycms->sql_select($packageCheckinDate);
						$rowpackageCheckinDate			= $respackageCheckinDate[0];
					?>
						<tr use='rateVal'>
							<td style=' border: 1px solid rgb(0, 0, 0);'><?= $rowpackageCheckinDate['check_in_date'] ?></td>
							<td style=' border: 1px solid rgb(0, 0, 0);'><?= $rowpackageCheckoutDate['check_out_date'] ?></td>
							<td style=' border: 1px solid rgb(0, 0, 0);'><?= $accomodationTariff['inr_amount'] ?></td>
							<td style=' border: 1px solid rgb(0, 0, 0);'><?= $accomodationTariff['usd_amount'] ?></td>
						</tr>
					<? } ?>
				</table>
			</tr>
			<tr class="tlisting">
				<td align="center" colspan="3" style="margin-top: 10px;">
					<input type="button" value="Back" id="back" onclick="window.location.href='accomodation_tariff.php'" class="btn btn-small btn-black" style="margin-top: 20px;" />
					<input type="button" value="Compose" id="compose" class="btn btn-small btn-blue" style="margin-top: 20px;" />
					<input type="submit" value="Update" id="updateRate" class="btn btn-small btn-blue" style="display: none;" />
				</td>
			</tr>
		</table>

	</form>
	<script>
		$(document).ready(function() {
			var inrRate = $('#rate_per_night_inr').val();
			if (inrRate == 0) {
				var rateTable = $("table[use=rateDes]");
				$(rateTable).hide();
			}
			$('#compose').click(function() {
				var INRrate = $('#rate_per_night_inr').val();
				var USDrate = $('#rate_per_night_usd').val();
				if (INRrate != "") {
					var rateTable = $("table[use=rateDes]");

					$(rateTable).find("tr[use=rateVal]").remove();

					$.each(dates, function(key, value) {
						var INRAmount = parseFloat(INRrate) * parseInt(value.days);
						var USDAmount = parseFloat(USDrate) * parseInt(value.days);
						var trString = "<tr use='rateVal'>";
						trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
						trString += value.checkin;
						trString += '<input type="hidden" name="checkin_date[' + key + ']" value="' + value.checkinId + '" />';
						trString += "</td>";
						trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
						trString += value.checkout;
						trString += '<input type="hidden" name="checkout_date[' + key + ']" value="' + value.checkoutId + '" />';
						trString += "</td>";
						trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
						trString += 'INR ' + (INRAmount).toFixed(2);
						trString += '<input type="hidden" name="INRAmt[' + key + ']" value="' + INRAmount + '" />';
						trString += "</td>";
						trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
						trString += 'USD ' + (USDAmount).toFixed(2);
						trString += '<input type="hidden" name="USDAmt[' + key + ']" value="' + USDAmount + '" />';
						trString += "</td>";
						trString += "</tr>";

						$(rateTable).append(trString);
					});
					$(rateTable).show();
					$('#compose').hide();
					$('#updateRate').show();
				} else {
					alert('Please enter rate!!!');
					$('#rate_per_night_inr').focus()
				}

			});
			$('#rate_per_night_inr, #rate_per_night_usd').change(function() {
				$('#compose').show();
				$('#updateRate').hide();
			});
		});
	</script>
<?
}
?>