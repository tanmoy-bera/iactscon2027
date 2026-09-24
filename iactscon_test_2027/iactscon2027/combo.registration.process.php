<?php
include_once("includes/frontend.init.php");
include_once('includes/function.accommodation.php');
include_once("includes/function.registration.php");

$action                = $_REQUEST['act'];
switch ($action) {
	case 'combinedRegistrationProcess':

		combinedRegistrationProcess();
		break;
		exit();

	case 'allotComboSection':

		global $cfg, $mycms;
		$id = $_REQUEST['combo_id'];

		$sqlRegClasf			= array();
		$sqlRegClasf['QUERY']	= "SELECT *
								 FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . "
								WHERE status = 'A' AND id='" . $id . "'";

		$resRegClasf			 = $mycms->sql_select($sqlRegClasf);
		$row = $resRegClasf[0];
		$data = '{ "WORKSHOP_PRICE":"' . $row['workshop_price'] . '",
				   "DINNER_PRICE" : "'.$row['dinner_price'].'"}';
		// echo '<pre>';print_r($resRegClasf);die;
		echo $data;
		exit();
		break;
		

	case 'getAccommodationDetails':

		include_once("../../includes/function.registration.php");

		$hotel_id  = trim($_REQUEST['hotel_id']);
		$currentCutoffId = getTariffCutoffId();

		if (!empty($hotel_id)) {
		} ?>
		<ul class="pac-ul" style="padding-left: 0rem;">
			<?php


			$comboTariffArray   = getAllRegistrationComboTariffs("", $hotel_id);
			// echo '<pre>'; print_r($comboTariffArray);die;  
			foreach ($comboTariffArray as $key => $rowComboTariff) {
				$id = $rowComboTariff['residential_hotel_id'];

				$sqlFetchHotel      = array();
				$sqlFetchHotel['QUERY'] = "SELECT * FROM " . _DB_MASTER_HOTEL_ . "
                                       WHERE `id`=? AND `status` =  ? ";

				$sqlFetchHotel['PARAM'][] = array('FILD' => 'residential_hotel_id',    'DATA' => $id,     'TYP' => 's');
				$sqlFetchHotel['PARAM'][] = array('FILD' => 'status',    'DATA' => 'A',     'TYP' => 's');
				$resultFetchHotel        = $mycms->sql_select($sqlFetchHotel);
				$rowHotel = $resultFetchHotel[0];
				// echo '<pre>'; print_r($rowComboTariff);die;
				$invoiceTitle = "Combo Registration<br>Residential package-" . $rowComboTariff['classification_title'] . "@" . $rowHotel['hotel_name'];

				$sqlRoom = array();
				$sqlRoom['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $id . "'AND accessories_name='" . $rowComboTariff['room_type'] . "'  AND status='A' AND purpose='room'";

				$queryRoom = $mycms->sql_select($sqlRoom, false);

				// $hotel_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowHotel['hotel_image'];
				$hotel_image = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $queryRoom[0]['accessories_icon'];


			?>
				<li>
					<!-- <img src="<?= $hotel_image ?>" alt=""> -->
					<input type="hidden" name="registration_classification_id" id="registration_classification_id" value="<?= $rowComboTariff['registration_classification_id'] ?>">
					<h6 class="pac-clk" style="padding-left: 0%;">
						<span class="pac-wrap">
							<span class="pac-wrap-lft">
								<b><?= $rowComboTariff['classification_title'] ?></b>
								<i><?= $rowComboTariff['room_type'] ?></i>
							</span>
							<span class="pac-wrap-rt" id="price_individual<?= $rowComboTariff['id'] ?>">
								<b><?= $rowComboTariff['currency'] . " " . $rowComboTariff['total_round_price'] ?></b>
								<i>Per Night</i>
							</span>
							<span class="pac-wrap-rt" id="price_shared<?= $rowComboTariff['id'] ?>" style="display:none">
								<b><?= $rowComboTariff['currency'] . " " . $rowComboTariff['total_round_price_shared'] ?></b>
								<i>Per Night</i>
							</span>
						</span>
					</h6>
					<div class="pac-box" style="padding-left: 0%;">
						<div class="mb-2">
							<label>Select Room Type</label>
							<div class="cus-rad" id="package_type_check_<?= $rowComboTariff['id'] ?>" invoiceTitle="<?= $invoiceTitle ?>" operationMode="combo_tariff" operationModeType="combo"  currency="<?= $rowComboTariff['currency'] ?>" amountIndividual="<?= $rowComboTariff['total_round_price'] ?>" amountShared="<?= $rowComboTariff['total_round_price_shared'] ?>">
								<label class="con">Individual
									<input class="con-in" type="radio" checked="checked" name="package_type" value="individual" operationMode="combo_tariff" combo_id="<?= $rowComboTariff['id'] ?>"  invoiceTitle="<?= $invoiceTitle."<br>[Individual]" ?>" reg="reg" currency="<?= $rowComboTariff['currency'] ?>" amount="<?= $rowComboTariff['total_round_price'] ?>" >
									<span class="checkmark"></span>
								</label>
								<label class="con">Shared
									<input class="con-in" type="radio" name="package_type" id="package_type" value="shared" combo_id="<?= $rowComboTariff['id'] ?>" operationMode="combo_tariff"  invoiceTitle="<?= $invoiceTitle."<br>[Shared]" ?>" reg="reg" currency="<?= $rowComboTariff['currency'] ?>" amount="<?= $rowComboTariff['total_round_price_shared'] ?>">
									<span class="checkmark"></span>
								</label>
							</div>
						</div>
						<div>
							<?php
							$packageCheckDate = array();
							$packageCheckDate['QUERY'] = "SELECT * FROM " . _DB_TARIFF_COMBO_ACCOMODATION_ . " 
                                       WHERE `hotel_id` = ?
                                         AND `classification_id` = ? AND `status`= ?
                                    ORDER BY  checkin_date_id desc";
							$packageCheckDate['PARAM'][]    =    array('FILD' => 'hotel_id',         'DATA' => $rowComboTariff['residential_hotel_id'],     'TYP' => 's');
							$packageCheckDate['PARAM'][]    =    array('FILD' => 'classification_id', 'DATA' => $rowComboTariff['id'],         'TYP' => 's');
							$packageCheckDate['PARAM'][]    =    array('FILD' => 'status',             'DATA' => 'A',         'TYP' => 's');
							$resCheckIns = $mycms->sql_select($packageCheckDate);
							// echo '<pre>';
							// print_r($resCheckIns);
							?>
							<label>Select Date</label>
							<div class="check-date">
								<span class="check-span">
									<span class="mb-1">
										<!-- <b>Ckeck In</b><input type="date"> -->
										<b>Ckeck In</b>
										<select name="checkin_checkout_date_id" id="checkin_checkout_date_id<?= $rowComboTariff['id'] ?>" combo_id="<?= $rowComboTariff['id'] ?>">
											<option value="">dd-mm-yyyy</option>
											<?php
											foreach ($resCheckIns as $key => $rowDate) {
												$checkInDate = array();
												$checkInDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
                                             WHERE `id` = ? AND `status`=?";
												$checkInDate['PARAM'][]    =    array('FILD' => 'id',         'DATA' => $rowDate['checkin_date_id'],     'TYP' => 's');
												$checkInDate['PARAM'][]    =    array('FILD' => 'status',             'DATA' => 'A',         'TYP' => 's');
												$resCheckInDate = $mycms->sql_select($checkInDate);

												$checkOutDate = array();
												$checkOutDate['QUERY'] = "SELECT * FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
                                             WHERE `id` = ? AND `status`=?";
												$checkOutDate['PARAM'][]    =    array('FILD' => 'id',         'DATA' => $rowDate['checkout_date_id'],     'TYP' => 's');
												$checkOutDate['PARAM'][]    =    array('FILD' => 'status',             'DATA' => 'A',         'TYP' => 's');
												$resCheckOutDate = $mycms->sql_select($checkOutDate);
											?>
												<option value="<?= $rowDate['id'] ?>" checkout_date="<?= date('d/m/Y', strtotime($resCheckOutDate[0]['check_out_date'])) ?>"><?= date('d/m/Y', strtotime($resCheckInDate[0]['check_in_date'])) ?></option>
												<!-- <input type="hidden" name="checkout_date" id="checkout_date" value="<?= date('d/m/Y', strtotime($resCheckOutDate[0]['check_out_date'])) ?>"> -->

											<?php
											}
											?>
										</select>
									</span>
									<span>
										<b>Ckeck Out</b><input type="text" name="check_out_date" id="check_out_date<?= $rowComboTariff['id'] ?>" value="dd-mm-yyyy" style="pointer-events: none">
									</span>
								</span>
							</div>
						</div>
						<a class="cnf-bok" id="" onclick="confirmComboHotelBooking(<?= $rowComboTariff['id'] ?>)">Confirm Booking</a>
					</div>

				</li>
			<?php } ?>

		</ul>
	<?php
		break;
		exit();
}


function combinedRegistrationProcess()
{
	global $mycms, $cfg;
	// echo '<pre>'; print_r($_REQUEST);die;

	///// Step 0 process /////

	/*echo '<pre>'; print_r($_REQUEST); 
		die();*/

	$dataArray = array();
	foreach ($_REQUEST as $key => $value) {
		$dataArray[$key] = $value;
	}

	$abstractDelegateId 					= $_REQUEST['abstractDelegateId'];

	$mycms->setSession('CUTOFF_ID_FRONT', 	$_REQUEST['cutoff_id']);
	$mycms->setSession('CLSF_ID_FRONT', 	$_REQUEST['registration_classification_id'][0]);

	$mycms->setSession('REGISTRATION_MODE', $_REQUEST['registrationMode']);

	$mycms->setSession('WORKSHOP_ID', 		$_REQUEST['workshop_id']);
	$mycms->setSession('DINNER_VALUE', 		$_REQUEST['dinner_value']);
	$mycms->setSession('HOTEL_ID', 			$_REQUEST['hotel_id']);

	$regClsfId 								= $_REQUEST['registration_classification_id'][0];
	$dataArray['regClsfId'] 				= $regClsfId;

	//*************** Accommodation details *****************************

	$accmodationPackageId 					= $_REQUEST['package_id'];
	////$accmodationDateSet 					= $_REQUEST['accDate'][$accmodationPackageId];


	// $accmodationDateSet 					= $_REQUEST['accDate'][0];

	// $dataArray['accmodationPackageId'] 		= $accmodationPackageId;
	// $dataArray['accmodationDateSet'] 		= $accmodationDateSet;

	// $accDates 			= explode('-', $accmodationDateSet);
	// $accCheckinDateId 	= $accDates[0];
	// $accCheckOutDateId 	= $accDates[1];

	//*************************** Accommodation details 01-08-2024 *************************************
	$accmodationCheckIn	= $_REQUEST['accomodation_package_checkin_id'];
	$accmodationCheckInarr = explode('/', $accmodationCheckIn);
	$accCheckinDateId 	= $accmodationCheckInarr[0];
	$accCheckinDate	= $accmodationCheckInarr[1];

	$accmodationCheckOut	= $_REQUEST['accomodation_package_checkout_id'];
	$accmodationCheckOutarr = explode('/', $accmodationCheckOut);
	$accCheckOutDateId 	= $accmodationCheckOutarr[0];
	$accCheckoutDate	= $accmodationCheckOutarr[1];
	$hotel_id = $_REQUEST['hotel_id'];
	// **************************************************************************************************


	$dataArray['hotel_id'] 			= $_REQUEST['hotel_id'];
	$dataArray['accCheckinDateId'] 			= $accCheckinDateId;
	$dataArray['accCheckOutDateId'] 		= $accCheckOutDateId;

	$mycms->setSession('STEP0_ACCM_PACKID', 		$accmodationPackageId);
	$mycms->setSession('STEP0_ACCM_CHECKINDATE',	$accCheckinDateId);
	$mycms->setSession('STEP0_ACCM_CHECKOUTDATE',	$accCheckOutDateId);

	$preffered_accommpany_name 				=  $_REQUEST['preffered_accommpany_name'];
	$preffered_accommpany_email 			=  $_REQUEST['preffered_accommpany_email'];
	$preffered_accommpany_mobile 			=  $_REQUEST['preffered_accommpany_mobile'];

	$dataArray['preffered_accommpany_name'] 	= $preffered_accommpany_name;
	$dataArray['preffered_accommpany_email'] 	= $preffered_accommpany_email;
	$dataArray['preffered_accommpany_mobile'] 	= $preffered_accommpany_mobile;

	$mycms->setSession('STEP0_PREF_ACMP_NAME', 	$preffered_accommpany_name);
	$mycms->setSession('STEP0_PREF_ACMP_EMAIL',	$preffered_accommpany_email);
	$mycms->setSession('STEP0_PREF_ACMP_MOB',	$preffered_accommpany_mobile);

	insertIntoProcessFlow("step0", $dataArray);


	///// Step 1 process /////		
	$dataArray = array();
	foreach ($_REQUEST as $key => $value) {
		$dataArray[$key] = $value;
	}

	$dataArray['registration_cutoff'] 	        	 	= $mycms->getSession('CUTOFF_ID_FRONT');
	$dataArray['registrationMode']        			 	= $mycms->getSession('REGISTRATION_MODE');
	$dataArray['registration_mode']        				= $mycms->getSession('REGISTRATION_MODE');

	$dataArray['preffered_accommpany_name'] 	 		= $mycms->getSession('STEP0_PREF_ACMP_NAME');
	$dataArray['preffered_accommpany_email']  			= $mycms->getSession('STEP0_PREF_ACMP_EMAIL');
	$dataArray['preffered_accommpany_mobile'] 			= $mycms->getSession('STEP0_PREF_ACMP_MOB');

	if ($mycms->isSession('HOTEL_ID')) {
		$dataArray['hotel_id'] = $mycms->getSession('HOTEL_ID');
	}
	if ($mycms->isSession('STEP0_ACCM_PACKID')) {
		$dataArray['accommodation_package_id'] = $mycms->getSession('STEP0_ACCM_PACKID');
	}
	if ($mycms->isSession('STEP0_ACCM_CHECKINDATE')) {
		$dataArray['accommodation_checkIn'] = $mycms->getSession('STEP0_ACCM_CHECKINDATE');
	}
	if ($mycms->isSession('STEP0_ACCM_CHECKOUTDATE')) {
		$dataArray['accommodation_checkOut'] = $mycms->getSession('STEP0_ACCM_CHECKOUTDATE');
	}

	$USER_DETAILS_FRONT['NAME'] 	  = addslashes(trim(strtoupper($_REQUEST['user_initial_title'] . ". " . $_REQUEST['user_first_name'] . " " . $_REQUEST['user_middle_name'] . " " . $_REQUEST['user_last_name'])));
	$USER_DETAILS_FRONT['EMAIL']	  = addslashes(trim(strtolower($_REQUEST['user_email_id'])));
	$USER_DETAILS_FRONT['PH_NO'] 	  = addslashes(trim($_REQUEST['user_usd_code'] . " - " . $_REQUEST['user_mobile']));
	$USER_DETAILS_FRONT['CUTOFF']     = addslashes(trim($mycms->getSession('CUTOFF_ID_FRONT')));
	$USER_DETAILS_FRONT['CATAGORY']   = addslashes(trim($mycms->getSession('CLSF_ID_FRONT')));

	$accDetails	= getUserTypeAndRoomType($mycms->getSession('CLSF_ID_FRONT'));

	$mycms->setSession('USER_DETAILS_FRONT', $USER_DETAILS_FRONT);

	insertIntoProcessFlow("step1", $dataArray);


	$selectedWorkshop 				= $mycms->getSession('WORKSHOP_ID');
	$selectedRegClassf 				= $mycms->getSession('CLSF_ID_FRONT');

	$mycms->removeSession('WORKSHOP_ID');
	$mycms->removeSession('DINNER_VALUE');
	$mycms->removeSession('HOTEL_ID');
	$mycms->removeSession('STEP0_ACCM_PACKID');
	$mycms->removeSession('STEP0_ACCM_CHECKINDATE');
	$mycms->removeSession('STEP0_ACCM_CHECKOUTDATE');
	$mycms->removeSession('STEP0_PREF_ACMP_NAME');
	$mycms->removeSession('STEP0_PREF_ACMP_EMAIL');
	$mycms->removeSession('STEP0_PREF_ACMP_MOB');

	if (in_array($selectedWorkshop[0], $cfg['INDEPENDANT.WORKSHOPS']) && $selectedRegClassf == '') {
		$regNextAct	=	"onlyWorkshopReg";
	} else {
		///// Step 3 process /////			
		$dataArray = array();
		foreach ($_REQUEST as $key => $value) {
			$dataArray[$key] = $value;
		}

		$registrationClassificationId   			= $mycms->getSession('CLSF_ID_FRONT');
		$registrationCutoffId  	        			= $mycms->getSession('CUTOFF_ID_FRONT');
		$isAccompany	                			= $mycms->getSession('IS_ACCOMPANY');
		$no_accompany	                			= $mycms->getSession('NO_ACCOMPANY');
		//$accompanyCatagory              			= 2;
		$accompanyCatagory      = 1; // accompany persion registration fees set to the cutoff value of 'Member' registration classification type 

		$registrationDetails 						= getAllRegistrationTariffs();
		$registrationAmount 						= $registrationDetails[$accompanyCatagory][$registrationCutoffId]['AMOUNT'];
		$registrationCurrency 						= $registrationDetails[$accompanyCatagory][$registrationCutoffId]['CURRENCY'];

		$dataArray['accompanyTariffAmount']        	= $registrationAmount;
		$dataArray['registration_cutoff']        	= $registrationCutoffId;
		$dataArray['accompanyClasfId']        		= $accompanyCatagory;
		$dataArray['registration_mode']        		= $mycms->getSession('REGISTRATION_MODE');

		foreach ($dataArray['accompany_name_add'] as $kl => $val) {
			if (trim($val) == '') {
				unset($dataArray['accompany_name_add'][$kl]);
				unset($dataArray['accompany_selected_add'][$kl]);
			}
		}
		$dataArray['dinner_value'] = $_REQUEST['accompany_dinner_value'];

		insertIntoProcessFlow("step3", $dataArray);

		if ($abstractDelegateId != '' && $abstractDelegateId > 0) {
			$regNextAct	=	"registerAbstractUser";
		} else {
			$regNextAct	=	"step6";
		}
	}

	// accommodation related work by weavers start //
	///// Step 4 process /////		

	$dataArray = array();
	foreach ($_REQUEST as $key => $value) {
		$dataArray[$key] = $value;
	}
	$dataArray['hotel_id'] 	        	 			= $hotel_id;
	$dataArray['check_in_date'] 	        	 	= $accCheckinDateId;
	$dataArray['check_out_date']        			= $accCheckOutDateId;
	$dataArray['accmName']        					= $_REQUEST['accomDetails'];

	insertIntoProcessFlow("step4", $dataArray);


	// accommodation related work by weavers end //

	///// Step 6 process /////

	$dataArray = array();

	if (isset($_FILES['cash_document'])) {
		// Handle the file upload
		$tempFile = $_FILES['cash_document']['tmp_name'];
		// echo $tempFile;die;
		$fileName = "CASH_DOC_" .date('ymdHis')."_". $_FILES['cash_document']['name'];
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
		

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$dataArray['cash_document'] 	= $fileName;
		} else {
			// echo "Error: Unable to move file to destination directory.";
		}
		
	}
	
	if (isset($_FILES['neft_document'])) {
		// Handle the file upload
		$tempFile = $_FILES['neft_document']['tmp_name'];
		// echo $tempFile;die;
		$fileName = "NEFT_DOC_" .date('ymdHis')."_". $_FILES['neft_document']['name'];
		$uploadDir = $cfg['FILES.ABSTRACT.REQUEST'];
		

		if (move_uploaded_file($tempFile, $uploadDir . $fileName)) {
			$dataArray['neft_document'] 	= $fileName;
		} else {
			// echo "Error: Unable to move file to destination directory.";
		}
	}
	
	foreach ($_REQUEST as $key => $value) {
		$dataArray[$key] = $value;
	}
	insertIntoProcessFlow("step6", $dataArray);



	?>
	<center>
		<form action="<?= _BASE_URL_ ?>registration.process.php" method="post" name="srchProcessFrm">
			<input type="hidden" name="act" value="<?= $regNextAct ?>" />
			<h5 align="center">Running Registration Process<br />Please Wait</h5>
			<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
			<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
			<br />
			<hr />
		</form>
	</center>
	<script type="text/javascript">
		document.srchProcessFrm.submit();
	</script>
<?
	exit();
}





function insertIntoProcessFlow($stepName, $requestValues)
{
	global $mycms, $cfg;

	$sessionId	    = session_id();
	$userIp		    = $_SERVER['REMOTE_ADDR'];
	$userBrowser    = $_SERVER['HTTP_USER_AGENT'];

	if ($mycms->getSession('PROCESS_FLOW_ID_FRONT') == "") {
		$sqlProcessInsertStep = array();
		$sqlProcessInsertStep['QUERY']	  = "INSERT INTO " . _DB_PROCESS_STEP_ . "  
												  SET `" . $stepName . "` 	    = ?,
													  `created_ip`          = ?,
													  `reg_area`            = ?,
													  `created_sessionId`   = ?,
													  `created_browser`     = ?,
													  `created_dateTime`    = ?";

		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => $stepName,           'DATA' => serialize($requestValues),    'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_ip',        'DATA' => $userIp,                      'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'reg_area',          'DATA' => 'FRONT',                      'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_sessionId', 'DATA' => $sessionId,                   'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_browser',   'DATA' => $userBrowser,                 'TYP' => 's');
		$sqlProcessInsertStep['PARAM'][]   = array('FILD' => 'created_dateTime',  'DATA' => date('Y-m-d H:i:s'),          'TYP' => 's');

		$id = $mycms->sql_insert($sqlProcessInsertStep, false);
		$mycms->setSession('PROCESS_FLOW_ID_FRONT', $id);
		$mycms->setSession('REGISTRATION_TOKEN', "#" . $mycms->getSession('PROCESS_FLOW_ID_FRONT'));
	} else {
		$sql  			= array();
		$sql['QUERY'] 	= "SELECT `" . $stepName . "` AS theData 
								 FROM " . _DB_PROCESS_STEP_ . "
								WHERE `id` = ?";
		$sql['PARAM'][] = array('FILD' => 'id', 	'DATA' => $mycms->getSession('PROCESS_FLOW_ID_FRONT'),	'TYP' => 's');
		$result       	= $mycms->sql_select($sql);
		$row			= $result[0];

		if (trim($row['theData']) != '') {
			$theData =  unserialize(trim($row['theData']));
		} else {
			$theData =  array();
		}

		foreach ($requestValues as $key => $value) {
			$theData[$key] = $value;
		}

		$sqlProcessUpdateStep = array();
		$sqlProcessUpdateStep['QUERY']       = "UPDATE  " . _DB_PROCESS_STEP_ . "
													   SET `" . $stepName . "` 		= ?,
														   `created_dateTime`   = ?
													 WHERE `id` = ?";
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => $stepName,          'DATA' => serialize($theData),     				 		'TYP' => 's');
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'created_dateTime', 'DATA' => date('Y-m-d H:i:s'), 							'TYP' => 's');
		$sqlProcessUpdateStep['PARAM'][]   = array('FILD' => 'id',               'DATA' => $mycms->getSession('PROCESS_FLOW_ID_FRONT'),	'TYP' => 's');
		$mycms->sql_update($sqlProcessUpdateStep, false);
	}
}

?>
<script>
	$(document).ready(function() {

		// function confirmComboHotelBooking(comboTariffId) {
		// 	alert(comboTariffId);
		// 	$('#section1').hide();
		// 	$('#section2').show();

		// }

		$('input[name="package_type"]').change(function() {
			var id = $(this).attr('combo_id');
			// alert(id);
			if ($(this).val() == 'individual') {
				console.log("Individual is checked");
				$('#price_individual' + id).show();
				$('#price_shared' + id).hide();

			} else if ($(this).val() == 'shared') {
				console.log("Shared is checked");
				$('#price_individual' + id).hide();
				$('#price_shared' + id).show();
			}
		});

		$('.pac-clk').click(function() {
			if ($(this).hasClass("activepac")) {
				$(".pac-box").slideUp();
				$(".pac-clk").removeClass("activepac");
			} else {
				$(".pac-box").slideUp();
				$(".pac-clk").removeClass("activepac");
				$(this).parent().find(".pac-box").slideToggle();
				$(this).parent().find(".pac-clk").toggleClass("activepac");
			}
		});

		

		// $('#firstBtn').on("click", function() {
		//     $('#section1').hide();
		//     $('#section2').show();

		// })
		$('select[name="checkin_checkout_date_id"]').change(function() {
			var id = $(this).attr('combo_id');

			var selectedOption = $('#checkin_checkout_date_id' + id + ' option:selected');
			// var value = selectedOption.val();
			var checkout_date = selectedOption.attr('checkout_date');
			// var checkout_date = $('#checkout_date').val();
			$('#check_out_date' + id).val(checkout_date);
			// alert(checkout_date)
		});




	});
</script>