<?php
include_once("includes/frontend.init.php");
include_once("includes/function.invoice.php");
include_once('includes/function.delegate.php');
include_once('includes/function.workshop.php');
include_once('includes/function.dinner.php');
include_once('includes/function.exhibitor.php');
include_once('includes/function.accommodation.php');
include_once('includes/function.registration.php');

global $mycms, $cfg;
// $loggedUserID = $mycms->getLoggedUserId();
$action = trim($_REQUEST['act']);
// echo '<pre>'; print_r($_REQUEST);die;
switch ($action) {
	case 'insert':
		insert_exhibitor($mycms, $cfg);
		exit();
		break;

	case 'updateAccompany':
		update_accompany($mycms, $cfg);
		exit();
		break;

	case 'updateWorkshop':
		update_workshop($mycms, $cfg);
		exit();
		break;

	case 'getEmailValidationStatus':
		getEmailValidation($mycms, $cfg);
		exit();
		break;

	case 'getMobileValidationStatus':
		getMobileValidationStatus($mycms, $cfg);
		exit();
		break;

	case "viewRegistrationList":

		$searchCondition       = "";
		$company_code = $_REQUEST['company_code'];
		$totalBulkAmount = 0.00;

		if (trim($_REQUEST['search_user']) != '') {
			$searchCondition   .= " AND (`email_id` LIKE '%" . trim($_REQUEST['search_user']) . "%'  OR `full_name` LIKE '%" . trim($_REQUEST['search_user']) . "%' )";
		}


		$sqlFetchUser  = array();
		$sqlFetchUser['QUERY']        = "SELECT * FROM " . _DB_EXHIBITOR_REGISTRATION_ . "
													 WHERE  `status` = 'A' AND `exhibitor_company_code`='" . $company_code . "' " . $searchCondition . " ORDER BY id";

		// $sqlFetchUser['PARAM'][]   = array('FILD' => 'mobile_no', 'DATA' => $mobile, 'TYP' => 's');

		$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
		if ($resultFetchUser) {
			foreach ($resultFetchUser as $key => $rowUser) {
				if ($rowUser['account_status'] == "REGISTERED") {
					$user_details = getUserDetails($rowUser['delegate_id']);
					$reg_id = $user_details['user_registration_id'];
				} else {
					$reg_id = '';
				}
				$rowtariff = calculateExhibitorBulkRegAmountPerUser($rowUser['id']);
				$totalBulkAmount += $rowtariff['TOTAL'];
				// print_r($totalBulkAmount);
?>
				<div class="product">
					<table>
						<tbody>
							<tr>
								<td>Name<span>:</span></td>
								<td><?= $rowUser['full_name'] ?></td>
							</tr>
							<tr>
								<td>Mobile<span>:</span></td>
								<td><?= $rowUser['mobile_no'] ?></td>
							</tr>
							<tr>
								<td>Email Id<span>:</span></td>
								<td><?= $rowUser['email_id'] ?></td>
							</tr>
							<tr>
								<td>Workshop<span>:</span></td>
								<td>
									<p>
										<?php
										$ids = json_decode($rowUser['workshop_id']);
										// foreach ($ids as $key => $id) {
										echo  $ids == '' ? '-' : getWorkshopName($ids);
										// }
										?>
									</p>
									<!-- <p>Workshop 2</p> -->
								</td>
							</tr>
							<tr>
								<td>Accompany Persons<span>:</span></td>
								<td>
									<?php
									if ($rowUser['accompanyCount'] > 0) {
										$accompany_names = json_decode($rowUser['accompany_name_add']);
										for ($i = 0; $i < $rowUser['accompanyCount']; $i++) {
											echo "<p>" . ($i + 1) . ". " . $accompany_names[$i] . "</p>";
										}
									} else {
										echo "-";
									}

									?>

									<!--  <p>Accompany Persons 1</p>
								 <p>Accompany Persons 2</p> -->
								</td>
							</tr>
							<!-- <tr>
								<td>Account Status<span>:</span></td>
								<td><b><?= $rowUser['account_status'] ?></b></td>
							</tr> -->
							<tr>
								<td>Registration Id<span>:</span></td>
								<td><b><?= $reg_id != "" ? $reg_id : "Pending" ?></b></td>
							</tr>
							<tr>
								<td>Amount<span>:</span></td>
								<td><b><?= number_format($rowtariff['TOTAL'], 2) ?> INR </b></td>
							</tr>
						</tbody>
						<?php if ($reg_id == '') {
						?>
							<tfoot>
								<tr>
									<td style="height: 10px;"></td>
								</tr>
								<tr>
									<td>
										<button class="editAccompany btn btn-warning" userId="<?= $rowUser['id'] ?>">Edit Accompany</button>
									</td>
									<td>
										<button class="editWorkshop btn btn-warning" userId="<?= $rowUser['id'] ?>">Edit Workshop</button>
									</td>
								</tr>
							</tfoot>
						<?php } ?>
					</table>
				</div>

			<?php } ?>
			<div id="totalAmount">TOTAL: <?= number_format($totalBulkAmount, 2) ?> INR</div>
		<?php
		} else {
		?>
			<h3>No Data Found!</h3>
<?php
		}
		exit();
		break;

	case 'getAccompanyDetails':

		$sqlFetchUser  = array();
		$sqlFetchUser['QUERY']        = "SELECT `id`,`full_name`,`accompanyCount`,`accompany_name_add`,`accompany_food_choice`  FROM " . _DB_EXHIBITOR_REGISTRATION_ . "
													 WHERE  `status` = 'A' AND id= '" . $_REQUEST['userId'] . "' ";

		$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
		$rowUser = $resultFetchUser[0];
		echo json_encode($rowUser);

		break;

	case 'getUserWorkshopDetails':

		$sqlFetchUser  = array();
		$sqlFetchUser['QUERY']        = "SELECT `id`,`full_name`,`workshop_id`,`registration_classification_id`  FROM " . _DB_EXHIBITOR_REGISTRATION_ . "
													 WHERE  `status` = 'A' AND id= '" . $_REQUEST['userId'] . "' ";

		$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
		$rowUser = $resultFetchUser[0];
		echo json_encode($rowUser);
		break;
}

// function insert_exhibitor($mycms, $cfg)
// {
// 	// echo '<pre>'; print_r($_REQUEST);die;
// 	$sqlInsertUser = array();
// 	$sqlInsertUser['QUERY']                    = "INSERT INTO " . _DB_EXHIBITOR_REGISTRATION_ . "
//                             SET `refference_delegate_id`		        = ?,
//                                 `user_type` 	        		        = ?,
//                                 `email_id`        		        = ?,
//                                 `title`            		        = ?,
//                                 `first_name`       		        = ?,
//                                 `middle_name`     		        = ?,
//                                 `last_name`       		        = ?,
//                                 `full_name` 				        = ?,
//                                 `mobile_isd_code`  		        = ?,
//                                 `mobile_no`				        = ?,
//                                 `address`					        = ?,	
//                                 `country_id`		 		        = ?,
//                                 `state_id`				        = ?,	
//                                 `city`					        = ?,
//                                 `pincode`					        = ?,
//                                 `workshop_id`					        = ?,
//                                 `accompanyCount`					        = ?,
//                                 `accompany_name_add`					        = ?,
//                                 `accompany_food_choice`					        = ?,

//                                 `dob` 					        = ?,
//                                 `gender`					        = ?,

//                                 `food_preference` 		        = ?,

//                                 `combo_registration`       		    = ?,
//                                 `accDateCombo`       		        	= ?,
//                                 `accommodation_room`                   = ?,

//                                 `registration_classification_id`       = ?,
//                                 `registration_tariff_cutoff_id`        = ?,
//                                 `registration_request`  		        = ?,
//                                 `exhibitor_company_code`  		        = ?,
//                                 `account_status`				        = ?,
//                                 `status`						        = ?,
//                                 `conf_reg_date`        		        = ?,
//                                 `created_ip` 					        = ?,
//                                 `created_sessionId`			        = ?,
//                                 `created_dateTime` 			        = ?";

// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'refference_delegate_id',             'DATA' => '0',                                                  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_type',                           'DATA' => 'DELEGATE',                                           'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'email_id',                       'DATA' => addslashes(trim(strtolower($_REQUEST['email_id']))),                   'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'title',                          'DATA' => addslashes(trim(strtoupper($_REQUEST['title']))),                      'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'first_name',                     'DATA' => addslashes(trim(strtoupper($_REQUEST['first_name']))),                 'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'middle_name',                    'DATA' => addslashes(trim(strtoupper($_REQUEST['middle_name']))),                'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'last_name',                      'DATA' => addslashes(trim(strtoupper($_REQUEST['last_name']))),                  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'full_name',                      'DATA' => addslashes(trim(strtoupper($_REQUEST['title'] . " " . $_REQUEST['first_name'] . " " . $_REQUEST['last_name']))),                  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'mobile_isd_code',                'DATA' => $_REQUEST['mobile_isd_code'],            'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'mobile_no',                      'DATA' => trim($_REQUEST['mobile_no']),                  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'address',                        'DATA' => addslashes(trim(strtoupper($_REQUEST['address']))),                    'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'country_id',                     'DATA' => trim($_REQUEST['country']),                    'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'state_id',                       'DATA' => trim($_REQUEST['state']),                      'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'city',                           'DATA' => addslashes(trim(strtoupper($_REQUEST['user_city']))),                       'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'pincode',                        'DATA' => $_REQUEST['postal_code'],                'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'workshop_id',                        'DATA' => json_encode($_REQUEST['workshop_id']),                'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompanyCount',                      'DATA' => $_REQUEST['accompanyCount'],                'TYP' => 's');
// 	if ($_REQUEST['accompanyCount'] > 0) {
// 		$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompany_name_add',                  'DATA' => json_encode($_REQUEST['accompany_name_add']),                'TYP' => 's');
// 		$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompany_food_choice',               'DATA' => json_encode($_REQUEST['accompany_food_choice']),                'TYP' => 's');
// 	} else {
// 		$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompany_name_add',                  'DATA' => "",                'TYP' => 's');
// 		$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompany_food_choice',               'DATA' => "",                'TYP' => 's');
// 	}
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'dob',                            'DATA' => $_REQUEST['dob'],                        'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'gender',                         'DATA' => $_REQUEST['gender'],                     'TYP' => 's');

// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'food_preference',                'DATA' => $_REQUEST['food_preference'],            'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'combo_registration',                      		 'DATA' => $userDetailsArray['combo_registration'],                  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'accDateCombo',                     	 'DATA' => $userDetailsArray['accDateCombo'],                  'TYP' => 's');

// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'accommodation_room',                  'DATA' => 0, 'TYP' => 's');

// 	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isConference',                        'DATA' =>$_REQUEST['isConference'],                    'TYP' => 's');	
// 	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isWorkshop',                          'DATA' =>$_REQUEST['isWorkshop'],                      'TYP' => 's');	
// 	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isAccommodation',                     'DATA' =>$_REQUEST['isAccommodation'],                 'TYP' => 's');
// 	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isTour',                              'DATA' =>$_REQUEST['isTour'],                          'TYP' => 's');
// 	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isAbstract',                          'DATA' =>$_REQUEST['IsAbstract'],                      'TYP' => 's');
// 	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isCombo',                          'DATA' =>$_REQUEST['isCombo'],                      'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_classification_id',      'DATA' => $_REQUEST['registration_classification_id'],  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_tariff_cutoff_id',       'DATA' => $_REQUEST['registration_tariff_cutoff_id'],   'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_request',                'DATA' => $_REQUEST['registration_request'],            'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'exhibitor_company_code',              'DATA' => $_REQUEST['exhibitor_company_code'],                  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'account_status',                      'DATA' => $_REQUEST['account_status'],                  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'status',                              'DATA' => 'A',                                                  'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'conf_reg_date',                       'DATA' => $date,                                                'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_ip',                          'DATA' => $_SERVER['REMOTE_ADDR'],                              'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_sessionId',                   'DATA' => session_id(),                                         'TYP' => 's');
// 	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_dateTime',                    'DATA' => date('Y-m-d H:i:s'),                                  'TYP' => 's');
// 	// echo "<pre>"; print_r($sqlInsertUser);die;

// 	$lastInsertedUserId               = $mycms->sql_insert($sqlInsertUser, false);
// 	$mycms->redirect('exhibitor.registration_step.php');
// }

function insert_exhibitor($mycms, $cfg)
{
	// echo '<pre>';
	// print_r($_REQUEST);
	// die;
	$accom_details = explode('-', $_REQUEST['accommodation_details']);
	$hotel_id = $accom_details[0];
	$room = $accom_details[1];

	if ($_REQUEST['isPackageAccom'] == 'Y') {
		$checkin_checkout_date_id = explode('-', $_REQUEST['checkin_checkout_date_id']);
		$checkin_id = $accom_details[0];
		$checkout_id = $accom_details[1];
	} else {
		$checkin_id = explode('/', $_REQUEST['accomodation_checkin_id'])[0];
		$checkout_id = explode('/', $_REQUEST['accomodation_checkout_id'])[0];
	}
	// echo $checkin_id;die;

	$sqlInsertUser = array();
	$sqlInsertUser['QUERY'] = "INSERT INTO " . _DB_EXHIBITOR_REGISTRATION_ . "
                            SET `refference_delegate_id`		        = ?,
                                `user_type` 	        		        = ?,
                                `email_id`        		        = ?,
                                `title`            		        = ?,
                                `first_name`       		        = ?,
                                `middle_name`     		        = ?,
                                `last_name`       		        = ?,
                                `full_name` 				    = ?,
                                `mobile_isd_code`  		        = ?,
                                `mobile_no`				        = ?,
                                `address`					        = ?,	
                                `country_id`		 		        = ?,
                                `state_id`				        = ?,	
                                `city`					        = ?,
                                `pincode`					    = ?,
                                `workshop_id`					= ?,
                                `accompanyCount`					        = ?,
                                `accompany_name_add`					        = ?,
                                `accompany_food_choice`			= ?,

                                `dob` 					        = ?,
                                `gender`					        = ?,
                                
                                `food_preference` 		        = ?,
                              
                                `accommodation_room`                   = ?,
                                `isPackageAccom`                   = ?,
                                `hotel_id`                   = ?,
                                `checkin_id`                   = ?,
                                `checkout_id`                   = ?,
                                
                                `registration_classification_id`       = ?,
                                `registration_tariff_cutoff_id`        = ?,
                                `exhibitor_company_code`  		        = ?,
                                `account_status`				        = ?,
                                `status`						        = ?,
                                `created_ip` 					        = ?,
                                `created_sessionId`			        = ?,
                                `created_dateTime` 			        = ?";

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'refference_delegate_id',             'DATA' => '0',                                                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'user_type',                           'DATA' => 'DELEGATE',                                           'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'email_id',                       'DATA' => addslashes(trim(strtolower($_REQUEST['email_id']))),                   'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'title',                          'DATA' => addslashes(trim(strtoupper($_REQUEST['title']))),                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'first_name',                     'DATA' => addslashes(trim(strtoupper($_REQUEST['first_name']))),                 'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'middle_name',                    'DATA' => addslashes(trim(strtoupper($_REQUEST['middle_name']))),                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'last_name',                      'DATA' => addslashes(trim(strtoupper($_REQUEST['last_name']))),                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'full_name',                      'DATA' => addslashes(trim(strtoupper($_REQUEST['title'] . " " . $_REQUEST['first_name'] . " " . $_REQUEST['last_name']))),                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'mobile_isd_code',                'DATA' => $_REQUEST['mobile_isd_code'],            'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'mobile_no',                      'DATA' => trim($_REQUEST['mobile_no']),                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'address',                        'DATA' => addslashes(trim(strtoupper($_REQUEST['address']))),                    'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'country_id',                     'DATA' => trim($_REQUEST['country']),                    'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'state_id',                       'DATA' => trim($_REQUEST['state']),                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'city',                           'DATA' => addslashes(trim(strtoupper($_REQUEST['user_city']))),                       'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'pincode',                        'DATA' => $_REQUEST['postal_code'],                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'workshop_id',                        'DATA' => json_encode($_REQUEST['workshop_id']),                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompanyCount',                      'DATA' => $_REQUEST['accompanyCount'],                'TYP' => 's');
	if ($_REQUEST['accompanyCount'] > 0) {
		$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompany_name_add',                  'DATA' => json_encode($_REQUEST['accompany_name_add']),                'TYP' => 's');
		$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompany_food_choice',               'DATA' => json_encode($_REQUEST['accompany_food_choice']),                'TYP' => 's');
	} else {
		$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompany_name_add',                  'DATA' => "",                'TYP' => 's');
		$sqlInsertUser['PARAM'][]   = array('FILD' => 'accompany_food_choice',               'DATA' => "",                'TYP' => 's');
	}
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'dob',                            'DATA' => $_REQUEST['dob'],                        'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'gender',                         'DATA' => $_REQUEST['gender'],                     'TYP' => 's');

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'food_preference',                'DATA' => $_REQUEST['food_preference'],            'TYP' => 's');
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'combo_registration',                      		 'DATA' => $userDetailsArray['combo_registration'],                  'TYP' => 's');
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'accDateCombo',                     	 'DATA' => $userDetailsArray['accDateCombo'],                  'TYP' => 's');

	$sqlInsertUser['PARAM'][]   = array('FILD' => 'accommodation_room',                  'DATA' => $room, 'TYP' => 's');

	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isConference',                        'DATA' =>$_REQUEST['isConference'],                    'TYP' => 's');	
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isWorkshop',                          'DATA' =>$_REQUEST['isWorkshop'],                      'TYP' => 's');	
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isAccommodation',                     'DATA' =>$_REQUEST['isAccommodation'],                 'TYP' => 's');
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isTour',                              'DATA' =>$_REQUEST['isTour'],                          'TYP' => 's');
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'isAbstract',                          'DATA' =>$_REQUEST['IsAbstract'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'isPackageAccom',                      'DATA' => $_REQUEST['isPackageAccom'],                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'hotel_id',                      'DATA' => $hotel_id,                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'checkin_id',                      'DATA' => $checkin_id,                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'checkout_id',                      'DATA' => $checkout_id,                      'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_classification_id',      'DATA' => $_REQUEST['registration_classification_id'],  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_tariff_cutoff_id',       'DATA' => $_REQUEST['registration_tariff_cutoff_id'],   'TYP' => 's');
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'registration_request',                'DATA' => $_REQUEST['registration_request'],            'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'exhibitor_company_code',              'DATA' => $_REQUEST['exhibitor_company_code'],                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'account_status',                      'DATA' => 'UNREGISTERED',                  'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'status',                              'DATA' => 'A',                                                  'TYP' => 's');
	// $sqlInsertUser['PARAM'][]   = array('FILD' => 'conf_reg_date',                       'DATA' => $date,                                                'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_ip',                          'DATA' => $_SERVER['REMOTE_ADDR'],                              'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_sessionId',                   'DATA' => session_id(),                                         'TYP' => 's');
	$sqlInsertUser['PARAM'][]   = array('FILD' => 'created_dateTime',                    'DATA' => date('Y-m-d H:i:s'),                                  'TYP' => 's');
	// echo "<pre>"; print_r($sqlInsertUser);die;

	$lastInsertedUserId               = $mycms->sql_insert($sqlInsertUser, false);
	$mycms->redirect('exhibitor.registration_step.php?id=' . $_REQUEST['encoded_code']);
}

function update_accompany($mycms, $cfg)
{

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_EXHIBITOR_REGISTRATION_ . "
                            SET `accompanyCount`				= ?,
                                `accompany_name_add`			= ?,
                                `accompany_food_choice`			= ?,
                                `modified_ip`				= ?,
                                `modified_dateTime`					= ?

								WHERE  `id` = ? AND `status`	= ? ";

	$sqlUpdate['PARAM'][]   = array('FILD' => 'accompanyCount',                      'DATA' => $_REQUEST['accompanyCount'],                'TYP' => 's');
	if ($_REQUEST['accompanyCount'] > 0) {
		$sqlUpdate['PARAM'][]   = array('FILD' => 'accompany_name_add',                  'DATA' => json_encode($_REQUEST['accompany_name_add']),                'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'accompany_food_choice',               'DATA' => json_encode($_REQUEST['accompany_food_choice']),                'TYP' => 's');
	} else {
		$sqlUpdate['PARAM'][]   = array('FILD' => 'accompany_name_add',                  'DATA' => "",                'TYP' => 's');
		$sqlUpdate['PARAM'][]   = array('FILD' => 'accompany_food_choice',               'DATA' => "",                'TYP' => 's');
	}
	$sqlUpdate['PARAM'][]   = array('FILD' => 'modified_ip',                          'DATA' => $_SERVER['REMOTE_ADDR'],                              'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'modified_dateTime',                    'DATA' => date('Y-m-d H:i:s'),                                  'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                      'DATA' => $_REQUEST['user_id'],                'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'status',                      'DATA' => 'A',                'TYP' => 's');

	$mycms->sql_update($sqlUpdate);
	$mycms->redirect('exhibitor.registration_step.php?id=' . $_REQUEST['encoded_code']);
}
function update_workshop($mycms, $cfg)
{

	$sqlUpdate = array();
	$sqlUpdate['QUERY'] = "UPDATE " . _DB_EXHIBITOR_REGISTRATION_ . "
                            SET `workshop_id`				= ?,
                                `modified_ip`				= ?,
                                `modified_dateTime`					= ?

								WHERE  `id` = ? AND `status`	= ? ";

	$sqlUpdate['PARAM'][]   = array('FILD' => 'workshop_id',                      'DATA' => json_encode($_REQUEST['workshop_id']),                'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'modified_ip',                          'DATA' => $_SERVER['REMOTE_ADDR'],                              'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'modified_dateTime',                    'DATA' => date('Y-m-d H:i:s'),                                  'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                      'DATA' => $_REQUEST['user_id'],                'TYP' => 's');
	$sqlUpdate['PARAM'][]   = array('FILD' => 'status',                      'DATA' => 'A',                'TYP' => 's');
	// print_r($sqlUpdate);die;
	$mycms->sql_update($sqlUpdate);
	$mycms->redirect('exhibitor.registration_step.php?id=' . $_REQUEST['encoded_code']);
}

function getEmailValidation($mycms, $cfg)
{
	$email                  	= trim($_REQUEST['email']);
	$mycms->setSession('USER_EMAIL_FROM_INDEX', $email);
	$availabilityStatus 		= "AVAILABLE";

	$sqlFetchUser               = array();
	$sqlFetchUser['QUERY']       = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_email_id` = ? 
												  AND `status` = ?";
	// AND `registration_request` = 'GENERAL'	

	$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $email, 'TYP' => 's');
	$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

	$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
	$row 						= $resultFetchUser[0];

	$rowFetchUser	=	array();
	if ($resultFetchUser) {
		$rowFetchUser           = $resultFetchUser[0];
	}

	header('Content-type: application/json');

	if (!empty($rowFetchUser) && ($rowFetchUser['registration_request'] == 'GENERAL')) {
		$availabilityStatus 	= '{"STATUS" : "IN_USE"}';
	} else {
		$sqlFetchUser  = array();
		$sqlFetchUser['QUERY']        = "SELECT * 
													 FROM " . _DB_EXHIBITOR_REGISTRATION_ . "
													WHERE `email_id` = ? 
													  AND `status` = ?";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'email_id', 'DATA' => $email, 'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);

		if ($resultFetchUser) {
			$availabilityStatus 	= '{"STATUS" : "IN_USE_EXHIBITOR"}';
		} else {
			$availabilityStatus 	= '{"STATUS" : "AVAILABLE"}';
		}
	}
	echo $availabilityStatus;
}

function getMobileValidationStatus($mycms, $cfg)
{
	$mobile                  	= trim($_REQUEST['mobile']);
	// $mycms->setSession('USER_EMAIL_FROM_INDEX', $email);
	$availabilityStatus 		= "AVAILABLE";

	$sqlFetchUser               = array();
	$sqlFetchUser['QUERY']       = "SELECT * 
												 FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_mobile_no` = ? 
												  AND `status` = ?";
	// AND `registration_request` = 'GENERAL'	

	$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_mobile_no', 'DATA' => $mobile, 'TYP' => 's');
	$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

	$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
	$row 						= $resultFetchUser[0];

	$rowFetchUser	=	array();
	if ($resultFetchUser) {
		$rowFetchUser           = $resultFetchUser[0];
	}

	header('Content-type: application/json');

	if (!empty($rowFetchUser) && ($rowFetchUser['registration_request'] == 'GENERAL')) {
		$availabilityStatus 	= '{"STATUS" : "IN_USE"}';
	} else {
		$sqlFetchUser  = array();
		$sqlFetchUser['QUERY']        = "SELECT * 
													 FROM " . _DB_EXHIBITOR_REGISTRATION_ . "
													WHERE `mobile_no` = ? 
													  AND `status` = ?";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'mobile_no', 'DATA' => $mobile, 'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);

		if ($resultFetchUser) {
			$availabilityStatus 	= '{"STATUS" : "IN_USE_EXHIBITOR"}';
		} else {
			$availabilityStatus 	= '{"STATUS" : "AVAILABLE"}';
		}
	}
	echo $availabilityStatus;
}
