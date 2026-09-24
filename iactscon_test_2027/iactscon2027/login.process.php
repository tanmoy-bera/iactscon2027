<?php
session_start();
include_once("includes/frontend.init.php");
include_once("includes/function.registration.php");
include_once("includes/function.delegate.php");

include_once("includes/function.messaging.php");

include_once("includes/function.invoice.php");
$action        	 = $_REQUEST['action'];
$currentCutoffId = getTariffCutoffId();

//to fetch pop-up msg
$sql   =  array();
$sql['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
			WHERE `id` = 1";
$result       = $mycms->sql_select($sql);
$row         = $result[0];
$invalidEmail = $row['notification_invalid_email'];
$registeredEmail = $row['notification_registered_email'];
$emptyEmail = $row['notification_empty_email'];
$unpaid_offline = $row['notification_unpaid_offline'];
$unpaid_online = $row['notification_unpaid_online'];

switch ($action) {

	/***************************************************************************/
	/*                            LOGIN BASIC PROCESS                          */
	/***************************************************************************/
	case 'loginRegToken':
		$registration_token             = trim($_REQUEST['registration_token']);

		$sqlFetchUser 					 = array();
		$sqlFetchUser['QUERY']          = "SELECT user.id AS delegateId,
											   		   slip.id AS slipId, 
													   user.registration_payment_status,
													   slip.registration_token
												  FROM " . _DB_SLIP_ . " slip
											LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " user
													ON slip.delegate_id = user.id
												 WHERE user.user_email_id = ?
												   AND slip.status = ?
												   AND user.status = ?";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user.user_email_id',  'DATA' => trim($_REQUEST['user_details']),  'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.status',         'DATA' => 'A',                              'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user.status',         'DATA' => 'A',                              'TYP' => 's');

		$resultUser              = $mycms->sql_select($sqlFetchUser);

		if ($resultUser) {
			foreach ($resultUser as $k => $rowUser) {


				$mycms->setSession('LOGGED.USER.ID', $rowUser['delegateId']);
				$mycms->setSession('IS_LOGIN', "YES");
				$mycms->removeSession('OTP.ID');
				$mycms->removeSession('TEMP.ID');
				$mycms->removeSession('USER.EM');
				$mycms->removeSession('USER.ID');
				$mycms->removeSession('USER.PWD');
				$mycms->removeSession('USER.TOKEN');

				$resFetchSlip = slipDetailsOfUser($rowUser['delegateId']);
				$rowFetchSlip = $resFetchSlip[0];
				$mycms->setSession('SLIP_ID', $rowFetchSlip['id']);
				if ($currentCutoffId > 0) {
					if ($rowFetchSlip['invoice_mode'] == "OFFLINE") {
						$mycms->redirect('login.php?m=5');
						//$mycms->redirect('registration.offline.checkout.php');
					} else {
?>
						<center>
							<form action="<?= _BASE_URL_ ?>payment.retry.php" method="post" name="loginUnpaidOnlineFrm"> <!--registration.process.php-->
								<input type="hidden" id="slip_id" name="slip_id" value="<?= $rowUser['slipId'] ?>" />
								<input type="hidden" id="delegate_id" name="delegate_id" value="<?= $rowUser['delegateId'] ?>" />
								<input type="hidden" name="act" value="paymentSet" />
								<input type="hidden" name="mode" value="<?= $rowFetchSlip['invoice_mode'] ?>" />
								<h5 align="center">Processing Payment Mode<br />Please Wait</h5>
								<img src="<?= _BASE_URL_ ?>images/PaymentPreloader.gif" /><br />
								<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
								<br />
								<hr />
							</form>
						</center>
						<script type="text/javascript">
							document.loginUnpaidOnlineFrm.submit();
						</script>
<?
						exit();
						//$mycms->redirect('registration.online.checkout.php');
					}
				} else {
					$mycms->redirect('registration.tariff.php');
				}
				exit();
			}
			$mycms->redirect('login.php?m=3');
		}
		$mycms->redirect('login.php?m=5');
		exit();
		break;

		/***************************************************************************/
		/*                            LOGIN ACCESS PROCESS                         */
	/***************************************************************************/


	case 'loginprocess':
		$user_email    			= trim($_REQUEST['user_email_id']);
		$user_password 			= $mycms->encoded($_REQUEST['userPassword']);
		$mycms->setSession('USER.ID', $user_email);
		$mycms->setSession('USER.EM', $user_email);

		$sqlFetchval  = array();
		$sqlFetchval['QUERY']   = "SELECT * 
										FROM " . _DB_USER_REGISTRATION_ . "
									   WHERE `user_email_id` = ?
										 AND `status` = ?
										 AND `registration_payment_status` = ?";

		$sqlFetchval['PARAM'][]   = array('FILD' => 'user_email_id',                'DATA' => $user_email,  'TYP' => 's');
		$sqlFetchval['PARAM'][]   = array('FILD' => 'status',                       'DATA' => 'A',          'TYP' => 's');
		$sqlFetchval['PARAM'][]   = array('FILD' => 'registration_payment_status',  'DATA' => 'PAID',       'TYP' => 's');


		$resultUserval              = $mycms->sql_select($sqlFetchval);
		if ($resultUserval) {
			$rowUserval                 = $resultUserval[0];
			if ($rowUserval['user_password'] === $user_password) {
				$mycms->setSession('LOGGED.USER.ID', $rowUserval['id']);
				$mycms->setSession('IS_LOGIN', "YES");
				$mycms->redirect('profile.php');
			} else {
				$mycms->setSession('USER.PWD', $mycms->decoded($rowUserval['user_password']));
				$mycms->setSession('TEMP.ID', $rowUserval['id']);
				$mycms->redirect('login.php?m=3');
			}
		} else {
			$mycms->redirect('login.php?m=4');
		}
		exit();
		break;

	case 'loginUniqueSequence':
		$mystring = trim($_REQUEST['user_details']);
		$mycms->setSession('USER.ID', $mystring);
		if (strpos($mystring, '@') > 1) {
			$sqlFetch = array();
			$sqlFetch['QUERY'] = "SELECT * 
									   FROM " . _DB_USER_REGISTRATION_ . "
									  WHERE `user_email_id` = ? 
										AND `status` = ?";

			$sqlFetch['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $mystring,  'TYP' => 's');
			$sqlFetch['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',        'TYP' => 's');

			$resultUser              = $mycms->sql_select($sqlFetch);
			if ($resultUser) {

				$rowUser                 = $resultUser[0];
				if ($rowUser['registration_payment_status'] != 'UNPAID' || $rowUser['registration_request'] == 'ABSTRACT' || $rowUser['registration_request'] == 'EXHIBITOR') {
					$mobileNum			 = $rowUser['user_mobile_no'];

					$mycms->setSession('USER.PWD', $rowUser['user_unique_sequence']);
					$mycms->setSession('USER.EM', $mystring);
					$mycms->setSession('USER.MOB', $mobileNum);
					$mycms->setSession('TEMP.ID', $rowUser['id']);
					send_uniqueSequence($rowUser['id'], 'SEND');
					$mycms->getSession('USER.PWD');
					$mycms->redirect('login.php');
				} else {
					$sqlFetchSlip          = array();
					$sqlFetchSlip['QUERY'] = " SELECT user.id AS delegateId,
														   slip.id AS slipId, 
														   user.registration_payment_status,
														   slip.registration_token,
														   slip.invoice_mode
													  FROM " . _DB_SLIP_ . " slip
												INNER JOIN " . _DB_USER_REGISTRATION_ . " user
														ON slip.delegate_id = user.id
													 WHERE user.id = ?
													   AND slip.status = ?
													   AND user.status = ?";

					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.id',           'DATA' => $rowUser['id'],  'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'slip.status',       'DATA' => 'A',             'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.status',       'DATA' => 'A',             'TYP' => 's');

					$resultSlip              = $mycms->sql_select($sqlFetchSlip);

					$resFetchSlip = slipDetailsOfUser($rowUser['id']);
					$rowFetchSlip = $resFetchSlip[0];
					$resFetchPay = paymentDetails($rowFetchSlip['id']);
					$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);

					if ($resultSlip && $totalNoOfUnpaidCount == 0) {
						$rowSlip                 = $resultSlip[0];
						$mycms->setSession('USER.TOKEN', $rowSlip['registration_token']);
						$mycms->setSession('USER.EM', $mystring);
						$mycms->setSession('TEMP.ID', $rowUser['id']);

						$mycms->redirect('login.php?m=2');
					} else {
						$resFetchSlip = slipDetailsOfUser($rowUser['id']);
						$rowFetchSlip = $resFetchSlip[0];
						$resFetchPay = paymentDetails($rowFetchSlip['id']);

						if ($resFetchPay) {
							$mycms->redirect('login.php?m=5&eml=' . $mystring);
						} else {
							$mycms->redirect('login.process.php?action=loginRegToken&user_details=' . $mystring);
						}
					}
				}
			} else {
				$mycms->redirect('login.php?m=4&eml=' . $mystring);
			}
		} else if (strpos($mystring, '#') === 0) {
			$sqlFetchval = array();
			$sqlFetchval['QUERY'] = "SELECT * 
										  FROM " . _DB_USER_REGISTRATION_ . "
										 WHERE `user_unique_sequence` = ? 
										   AND `status` =?
										   AND ( `registration_payment_status` != 'UNPAID' OR registration_request = 'ABSTRACT')";

			$sqlFetchval['PARAM'][]   = array('FILD' => 'user_unique_sequence',        'DATA' => trim($_REQUEST['user_details']),  'TYP' => 's');
			$sqlFetchval['PARAM'][]   = array('FILD' => 'status',                      'DATA' => 'A',                               'TYP' => 's');


			$resultUserval              = $mycms->sql_select($sqlFetchval);
			$rowUserval                 = $resultUserval[0];
			if ($resultUserval) {
				$mycms->setSession('LOGGED.USER.ID', $rowUserval['id']);
				$mycms->setSession('IS_LOGIN', "YES");
				$mycms->redirect('profile.php');
			} else {
				$mycms->redirect('login.php?m=1');
			}
		} else if (is_numeric($_REQUEST['user_details']) && $_REQUEST['user_details'] && strlen($_REQUEST['user_details']) == 10) {
			$sqlFetch = array();
			$sqlFetch['QUERY'] = "SELECT * FROM " . _DB_USER_REGISTRATION_ . "
												WHERE `user_mobile_no` = ? 
												  AND `status` = ?";

			$sqlFetch['PARAM'][]   = array('FILD' => 'user_mobile_no',  'DATA' => $mystring, 'TYP' => 's');
			$sqlFetch['PARAM'][]   = array('FILD' => 'status',          'DATA' => 'A',      'TYP' => 's');

			$resultUser              = $mycms->sql_select($sqlFetch);
			if ($resultUser) {

				$rowUser                 = $resultUser[0];
				$emailId				 = $rowUser['user_email_id'];
				if ($rowUser['registration_payment_status'] != 'UNPAID') {
					$mobileNum				 = $rowUser['user_mobile_no'];


					$mycms->setSession('USER.PWD', $rowUser['user_unique_sequence']);
					$mycms->setSession('USER.MOB', $mystring);
					$mycms->setSession('USER.EM', $emailId);
					$mycms->setSession('TEMP.ID', $rowUser['id']);
					send_uniqueSequence($rowUser['id'], 'SEND');
					$mycms->redirect('login.php');
				} else {
					$sqlFetchSlip       = array();
					$sqlFetchSlip['QUERY']	= " SELECT user.id AS delegateId,
															   slip.id AS slipId, 
															   user.registration_payment_status,
															   slip.registration_token 
														  FROM " . _DB_SLIP_ . " slip
													INNER JOIN " . _DB_USER_REGISTRATION_ . " user
															ON slip.delegate_id = user.id
														 WHERE user.id = ?
														   AND slip.status = ?
														   AND user.status = ? ";

					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.id',       'DATA' => $rowUser['id'], 'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'slip.status',   'DATA' => 'A',           'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.status',   'DATA' => 'A',            'TYP' => 's');

					$resultSlip              = $mycms->sql_select($sqlFetchSlip);
					$resFetchSlip = slipDetailsOfUser($rowUser['id']);
					$rowFetchSlip = $resFetchSlip[0];
					$resFetchPay = paymentDetails($rowFetchSlip['id']);
					$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);

					if ($resultSlip && $totalNoOfUnpaidCount == 0) {
						$rowSlip                 = $resultSlip[0];
						$mycms->setSession('USER.TOKEN', $rowSlip['registration_token']);
						$mycms->setSession('USER.EM', $emailId);
						$mycms->setSession('TEMP.ID', $rowUser['id']);
						//registration_token_request_message($rowUser['id'], $rowSlip['registration_token'], 'SEND');			
						$mycms->redirect('login.php?m=2');
					} else {
						$mycms->redirect('login.php?m=5&eml=' . $mystring);
					}
				}
			} else {
				$mycms->redirect('login.php?m=4&eml=' . $mystring);
			}
		} else {
			$mycms->redirect('login.php?m=1');
		}
		exit();
		break;


	case 'getLoginValidation':

		$user_email_id = addslashes(trim($_REQUEST['user_email_id']));
		$user_unique_sequence = addslashes(trim($_REQUEST['user_unique_sequence']));
		//  if(!empty($user_email_id) && !empty($user_unique_sequence))
		if (!empty($user_email_id)) {

			$sqlFetch = array();
			$sqlFetch['QUERY'] = "SELECT * 
									   FROM " . _DB_USER_REGISTRATION_ . "
									  WHERE `user_email_id` = ? 
									  	AND  `user_unique_sequence` = ?
										AND `status` = ?";

			$sqlFetch['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $user_email_id,  'TYP' => 's');
			$sqlFetch['PARAM'][]   = array('FILD' => 'user_unique_sequence', 'DATA' => $user_unique_sequence,  'TYP' => 's');
			$sqlFetch['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',        'TYP' => 's');

			$resultUser              = $mycms->sql_select($sqlFetch);

			if ($resultUser) {


				$rowUser                 = $resultUser[0];

				$sqlFetchAbs               = array();
				$sqlFetchAbs['QUERY']       = "SELECT * 
											 FROM " . _DB_ABSTRACT_REQUEST_ . "
											WHERE `applicant_id` = ? 
											  AND `status` = ?";

				$sqlFetchAbs['PARAM'][]   = array('FILD' => 'applicant_id', 'DATA' => $rowUser['id'], 'TYP' => 's');
				$sqlFetchAbs['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

				$resultFetchAbs    		= $mycms->sql_select($sqlFetchAbs);
				if ($resultFetchAbs) {
					// If the user has an abstract request, we consider it as in use
					$isAbstract = 'Y';
				} else {
					$isAbstract = 'N';
				}

				if (
					$rowUser['registration_payment_status'] != 'UNPAID' || $rowUser['registration_request'] == 'ABSTRACT'
					|| $rowUser['registration_request'] == 'EXHIBITOR' || $isAbstract == 'Y'
				) {
					$mobileNum			 = $rowUser['user_mobile_no'];

					$mycms->setSession('USER.PWD', $rowUser['user_unique_sequence']);
					$mycms->setSession('USER.EM', $mystring);
					$mycms->setSession('USER.MOB', $rowUser['user_mobile_no']);
					$mycms->setSession('LOGGED.USER.ID', $rowUser['id']);

					// send_uniqueSequence($rowUser['id'], 'SEND');	
					$mycms->getSession('USER.PWD');
					//$mycms->redirect('profile.php');  

					$arr1 = array(
						'succ' => 200,
						'msg' => 'You have been login successfully'
					);
				} else {
					$sqlFetchSlip          = array();
					$sqlFetchSlip['QUERY'] = " SELECT user.id AS delegateId,
														   slip.id AS slipId, 
														   user.registration_payment_status,
														   slip.registration_token,
														   slip.invoice_mode
													  FROM " . _DB_SLIP_ . " slip
												INNER JOIN " . _DB_USER_REGISTRATION_ . " user
														ON slip.delegate_id = user.id
													 WHERE user.id = ?
													   AND slip.status = ?
													   AND user.status = ?";

					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.id',           'DATA' => $rowUser['id'],  'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'slip.status',       'DATA' => 'A',             'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.status',       'DATA' => 'A',             'TYP' => 's');

					$resultSlip              = $mycms->sql_select($sqlFetchSlip);

					$resFetchSlip = slipDetailsOfUser($rowUser['id']);
					$rowFetchSlip = $resFetchSlip[0];
					$resFetchPay = paymentDetails($rowFetchSlip['id']);
					$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);

					if ($resultSlip && $totalNoOfUnpaidCount == 0) {
						$rowSlip                 = $resultSlip[0];
						$mycms->setSession('USER.TOKEN', $rowSlip['registration_token']);
						$mycms->setSession('USER.EM', $mystring);
						$mycms->setSession('TEMP.ID', $rowUser['id']);

						//$mycms->redirect('login.php?m=2'); 

						$msg = 'Your e-mail id is already registered with us but the payment procedure remained incomplete. To complete, please pay the registration fees.';

						$arr = array(
							'error' => 400,
							'msg' => $msg
						);
					} else {
						$resFetchSlip = slipDetailsOfUser($rowUser['id']);
						$rowFetchSlip = $resFetchSlip[0];
						$resFetchPay = paymentDetails($rowFetchSlip['id']);

						if ($resFetchPay) {
							//$mycms->redirect('login.php?m=5&eml='.$mystring);
							// $msg = 'This e-mail id is already registered with us. <br />For further assistance please contact with '.$cfg['EMAIL_CONF_NAME'].' Registration Secretariat. Ph no. '.$cfg['EMAIL_CONF_CONTACT_US'].' Time: 11:00 - 18:00';
							$find = ['[CONF NAME]', '[PHONE NUMBER]'];
							$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
							$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline);
							$msg = $unpaid_offline_msg;
							$arr = array(
								'error' => 400,
								'msg' => $msg
							);
						} else {
							//$mycms->redirect('login.process.php?action=loginRegToken&user_details='.$mystring);
						}
					}
				}
			} else {
				$arr = array(
					'error' => 400,
					'msg' => 'your credentials not registered. Please enter right credentials'
				);
			}
		} else if (!empty($user_email_id)) {
			$sqlFetch = array();
			$sqlFetch['QUERY'] = "SELECT * 
									   FROM " . _DB_USER_REGISTRATION_ . "
									  WHERE `user_email_id` = ? 
									  	
										AND `status` = ?";

			$sqlFetch['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $user_email_id,  'TYP' => 's');

			$sqlFetch['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',        'TYP' => 's');

			$resultUser              = $mycms->sql_select($sqlFetch);

			if ($resultUser) {

				$rowUser                 = $resultUser[0];
				if ($rowUser['registration_payment_status'] != 'UNPAID' || $rowUser['registration_request'] == 'ABSTRACT' || $rowUser['registration_request'] == 'EXHIBITOR') {
					$mobileNum			 = $rowUser['user_mobile_no'];



					$arr = array(
						'succ' => 200,
						'msg' => ''
					);
				} else {
					$sqlFetchSlip          = array();
					$sqlFetchSlip['QUERY'] = " SELECT user.id AS delegateId,
														   slip.id AS slipId, 
														   user.registration_payment_status,
														   slip.registration_token,
														   slip.invoice_mode
													  FROM " . _DB_SLIP_ . " slip
												INNER JOIN " . _DB_USER_REGISTRATION_ . " user
														ON slip.delegate_id = user.id
													 WHERE user.id = ?
													   AND slip.status = ?
													   AND user.status = ?";

					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.id',           'DATA' => $rowUser['id'],  'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'slip.status',       'DATA' => 'A',             'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.status',       'DATA' => 'A',             'TYP' => 's');

					$resultSlip              = $mycms->sql_select($sqlFetchSlip);

					$resFetchSlip = slipDetailsOfUser($rowUser['id']);
					$rowFetchSlip = $resFetchSlip[0];
					$resFetchPay = paymentDetails($rowFetchSlip['id']);
					$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);

					if ($resultSlip && $totalNoOfUnpaidCount == 0) {
						$rowSlip                 = $resultSlip[0];
						$mycms->setSession('USER.TOKEN', $rowSlip['registration_token']);
						$mycms->setSession('USER.EM', $mystring);
						$mycms->setSession('TEMP.ID', $rowUser['id']);

						//$mycms->redirect('login.php?m=2'); 

						$msg = 'Your e-mail id is already registered with us but the payment procedure remained incomplete. To complete, please pay the registration fees.';

						$arr = array(
							'error' => 400,
							'msg' => $msg
						);
					} else {
						$resFetchSlip = slipDetailsOfUser($rowUser['id']);
						$rowFetchSlip = $resFetchSlip[0];
						$resFetchPay = paymentDetails($rowFetchSlip['id']);

						if ($resFetchPay) {
							//$mycms->redirect('login.php?m=5&eml='.$mystring);
							// $msg = 'This e-mail id is already registered with us. <br />For further assistance please contact with '.$cfg['EMAIL_CONF_NAME'].' Registration Secretariat. Ph no. '.$cfg['EMAIL_CONF_CONTACT_US'].' Time: 11:00 - 18:00';
							$find = ['[CONF NAME]', '[PHONE NUMBER]'];
							$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
							$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline);
							$msg = $unpaid_offline_msg;
							$arr = array(
								'error' => 400,
								'msg' => $msg
							);
						} else {
							//$mycms->redirect('login.process.php?action=loginRegToken&user_details='.$mystring);
						}
					}
				}
			} else {
				$_SESSION['user_email_from_index_page'] = $user_email_id;
				$arr = array(
					'error' => 400,
					'msg' => 'your credentials not registered. Please enter right credentials'
				);
			}
		} else {
			$arr = array(
				'error' => 400,
				'msg' => 'Please enter right credentials'
			);
		}

		echo json_encode($arr1);

		exit();
		break;

	case 'getLoginValidationAbstract':

		$user_email_id = addslashes(trim($_REQUEST['user_email_id']));
		$user_unique_sequence = addslashes(trim($_REQUEST['user_unique_sequence']));
		//  if(!empty($user_email_id) && !empty($user_unique_sequence))
		if (!empty($user_email_id)) {

			$sqlFetch = array();
			$sqlFetch['QUERY'] = "SELECT * 
									   FROM " . _DB_USER_REGISTRATION_ . "
									  WHERE `user_email_id` = ? 
									  	AND  `user_unique_sequence` = ?
										AND `status` = ?";

			$sqlFetch['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $user_email_id,  'TYP' => 's');
			$sqlFetch['PARAM'][]   = array('FILD' => 'user_unique_sequence', 'DATA' => $user_unique_sequence,  'TYP' => 's');
			$sqlFetch['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',        'TYP' => 's');

			$resultUser              = $mycms->sql_select($sqlFetch);

			if ($resultUser) {


				$rowUser                 = $resultUser[0];

				if (
					/*$rowUser['registration_payment_status'] != 'UNPAID'*/
					$rowUser['registration_request'] == 'GENERAL' || $rowUser['registration_request'] == 'ABSTRACT'
					|| $rowUser['registration_request'] == 'EXHIBITOR'
				) {
					$mobileNum			 = $rowUser['user_mobile_no'];

					$mycms->setSession('USER.PWD', $rowUser['user_unique_sequence']);
					$mycms->setSession('USER.EM', $mystring);
					$mycms->setSession('USER.MOB', $rowUser['user_mobile_no']);
					$mycms->setSession('LOGGED.USER.ID', $rowUser['id']);

					// send_uniqueSequence($rowUser['id'], 'SEND');	
					$mycms->getSession('USER.PWD');
					//$mycms->redirect('profile.php');  

					$arr1 = array(
						'succ' => 200,
						'msg' => 'You have been login successfully'
					);
				} else {
					$sqlFetchSlip          = array();
					$sqlFetchSlip['QUERY'] = " SELECT user.id AS delegateId,
														   slip.id AS slipId, 
														   user.registration_payment_status,
														   slip.registration_token,
														   slip.invoice_mode
													  FROM " . _DB_SLIP_ . " slip
												INNER JOIN " . _DB_USER_REGISTRATION_ . " user
														ON slip.delegate_id = user.id
													 WHERE user.id = ?
													   AND slip.status = ?
													   AND user.status = ?";

					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.id',           'DATA' => $rowUser['id'],  'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'slip.status',       'DATA' => 'A',             'TYP' => 's');
					$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.status',       'DATA' => 'A',             'TYP' => 's');

					$resultSlip              = $mycms->sql_select($sqlFetchSlip);

					$resFetchSlip = slipDetailsOfUser($rowUser['id']);
					$rowFetchSlip = $resFetchSlip[0];
					$resFetchPay = paymentDetails($rowFetchSlip['id']);
					$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);

					if ($resultSlip && $totalNoOfUnpaidCount == 0) {
						$rowSlip                 = $resultSlip[0];
						$mycms->setSession('USER.TOKEN', $rowSlip['registration_token']);
						$mycms->setSession('USER.EM', $mystring);
						$mycms->setSession('TEMP.ID', $rowUser['id']);

						//$mycms->redirect('login.php?m=2'); 

						$msg = 'Your e-mail id is already registered with us but the payment procedure remained incomplete. To complete, please pay the registration fees.';

						$arr1 = array(
							'error' => 400,
							'msg' => $msg
						);
					} else {
						$resFetchSlip = slipDetailsOfUser($rowUser['id']);
						$rowFetchSlip = $resFetchSlip[0];
						$resFetchPay = paymentDetails($rowFetchSlip['id']);

						if ($resFetchPay) {
							//$mycms->redirect('login.php?m=5&eml='.$mystring);
							// $msg = 'This e-mail id is already registered with us. <br />For further assistance please contact with '.$cfg['EMAIL_CONF_NAME'].' Registration Secretariat. Ph no. '.$cfg['EMAIL_CONF_CONTACT_US'].' Time: 11:00 - 18:00';
							$find = ['[CONF NAME]', '[PHONE NUMBER]'];
							$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
							$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline);
							$msg = $unpaid_offline_msg;
							$arr1 = array(
								'error' => 400,
								'msg' => $msg
							);
						} else {
							//$mycms->redirect('login.process.php?action=loginRegToken&user_details='.$mystring);
						}
					}
				}
			} else {
				$arr1 = array(
					'error' => 400,
					'msg' => 'your credentials not registered. Please enter right credentials'
				);
			}
		}
		// else if (!empty($user_email_id)) {
		// 	$sqlFetch = array();
		// 	$sqlFetch['QUERY'] = "SELECT * 
		// 							   FROM " . _DB_USER_REGISTRATION_ . "
		// 							  WHERE `user_email_id` = ? 

		// 								AND `status` = ?";

		// 	$sqlFetch['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' => $user_email_id,  'TYP' => 's');

		// 	$sqlFetch['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',        'TYP' => 's');

		// 	$resultUser              = $mycms->sql_select($sqlFetch);

		// 	if ($resultUser) {

		// 		$rowUser                 = $resultUser[0];
		// 		if ($rowUser['registration_payment_status'] != 'UNPAID' || $rowUser['registration_request'] == 'ABSTRACT' || $rowUser['registration_request'] == 'EXHIBITOR') {
		// 			$mobileNum			 = $rowUser['user_mobile_no'];



		// 			$arr = array(
		// 				'succ' => 200,
		// 				'msg' => ''
		// 			);
		// 		} else {
		// 			$sqlFetchSlip          = array();
		// 			$sqlFetchSlip['QUERY'] = " SELECT user.id AS delegateId,
		// 												   slip.id AS slipId, 
		// 												   user.registration_payment_status,
		// 												   slip.registration_token,
		// 												   slip.invoice_mode
		// 											  FROM " . _DB_SLIP_ . " slip
		// 										INNER JOIN " . _DB_USER_REGISTRATION_ . " user
		// 												ON slip.delegate_id = user.id
		// 											 WHERE user.id = ?
		// 											   AND slip.status = ?
		// 											   AND user.status = ?";

		// 			$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.id',           'DATA' => $rowUser['id'],  'TYP' => 's');
		// 			$sqlFetchSlip['PARAM'][]   = array('FILD' => 'slip.status',       'DATA' => 'A',             'TYP' => 's');
		// 			$sqlFetchSlip['PARAM'][]   = array('FILD' => 'user.status',       'DATA' => 'A',             'TYP' => 's');

		// 			$resultSlip              = $mycms->sql_select($sqlFetchSlip);

		// 			$resFetchSlip = slipDetailsOfUser($rowUser['id']);
		// 			$rowFetchSlip = $resFetchSlip[0];
		// 			$resFetchPay = paymentDetails($rowFetchSlip['id']);
		// 			$totalNoOfUnpaidCount = unpaidCountOfPaymnet($rowFetchSlip['id']);

		// 			if ($resultSlip && $totalNoOfUnpaidCount == 0) {
		// 				$rowSlip                 = $resultSlip[0];
		// 				$mycms->setSession('USER.TOKEN', $rowSlip['registration_token']);
		// 				$mycms->setSession('USER.EM', $mystring);
		// 				$mycms->setSession('TEMP.ID', $rowUser['id']);

		// 				//$mycms->redirect('login.php?m=2'); 

		// 				$msg = 'Your e-mail id is already registered with us but the payment procedure remained incomplete. To complete, please pay the registration fees.';

		// 				$arr = array(
		// 					'error' => 400,
		// 					'msg' => $msg
		// 				);
		// 			} else {
		// 				$resFetchSlip = slipDetailsOfUser($rowUser['id']);
		// 				$rowFetchSlip = $resFetchSlip[0];
		// 				$resFetchPay = paymentDetails($rowFetchSlip['id']);

		// 				if ($resFetchPay) {
		// 					//$mycms->redirect('login.php?m=5&eml='.$mystring);
		// 					// $msg = 'This e-mail id is already registered with us. <br />For further assistance please contact with '.$cfg['EMAIL_CONF_NAME'].' Registration Secretariat. Ph no. '.$cfg['EMAIL_CONF_CONTACT_US'].' Time: 11:00 - 18:00';
		// 					$find = ['[CONF NAME]', '[PHONE NUMBER]'];
		// 					$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
		// 					$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline);
		// 					$msg = $unpaid_offline_msg;
		// 					$arr = array(
		// 						'error' => 400,
		// 						'msg' => $msg
		// 					);
		// 				} else {
		// 					//$mycms->redirect('login.process.php?action=loginRegToken&user_details='.$mystring);
		// 				}
		// 			}
		// 		}
		// 	} else {
		// 		$_SESSION['user_email_from_index_page'] = $user_email_id;
		// 		$arr = array(
		// 			'error' => 400,
		// 			'msg' => 'your credentials not registered. Please enter right credentials'
		// 		);
		// 	}
		// } 
		else {
			$arr1 = array(
				'error' => 400,
				'msg' => 'Please enter right credentials'
			);
		}

		echo json_encode($arr1);

		exit();
		break;

	case 'getNewTabData':

		$conf_id = addslashes(trim($_REQUEST['conf_id']));
		$delegatetID = addslashes(trim($_REQUEST['delegatetID']));

		if (!empty($conf_id) && !empty($delegatetID)) {

			$msg = '';

			$sqlQ = '';
			if ($_REQUEST['hall_id'] != '') {
				$sqlQ .= " AND session_hall.id='" . $_REQUEST['hall_id'] . "'";
			}

			$sqlParti = array();
			$sqlParti['QUERY'] 	   = "SELECT participant_schedule.*, 
											 program_date.conf_date, 
											 program_topic.topic_title, program_topic.reference_tag, program_topic.topic_time_start, program_topic.topic_time_end, 
											 program_theme.theme_title, program_theme.theme_time_start, program_theme.theme_time_end, 
											 program_session.session_title, program_session.session_start_time, program_session.session_end_time, 
											 IFNULL( IFNULL(program_hallTempname.hall_name, program_hall.hall_title) , IFNULL(session_hallTempname.hall_name, session_hall.hall_title) ) AS hall_title,		
											 participant_details.participant_full_name 
										FROM " . _DB_SP_PARTICIPANT_SCHEDULE_ . " participant_schedule
								  INNER JOIN " . _DB_PROGRAM_SCHEDULE_DATE_ . " program_date
										  ON participant_schedule.date_id = program_date.id
								  INNER JOIN " . _DB_SP_PARTICIPANT_DETAILS_ . " participant_details
										  ON participant_schedule.participant_id = participant_details.id
							 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_TOPIC_ . " program_topic
										  ON participant_schedule.topic_id = program_topic.id
							 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_SESSION_ . " program_session
										  ON participant_schedule.session_id = program_session.id
							 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " session_hall
										  ON program_session.session_hall_id = session_hall.id	
							  LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " session_hallTempname
										   ON session_hall.id = session_hallTempname.hall_id  
										  AND session_hallTempname.date_id = program_date.id
							 LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_THEME_ . " program_theme
										  ON participant_schedule.theme_id = program_theme.id
							 LEFT OUTER JOIN " . _DB_MASTER_HALL_ . " program_hall
										  ON participant_schedule.hall_id = program_hall.id
							 LEFT OUTER JOIN " . _DB_MASTER_HALL_NAME_ . " program_hallTempname
										  ON session_hall.id = program_hallTempname.hall_id  
										 AND program_hallTempname.date_id = program_date.id
									   WHERE program_date.status = 'A' 
										 AND program_date.id = '" . $conf_id . "' " . $sqlQ . " ";

			$resParti		= $mycms->sql_select($sqlParti);

			//echo '<pre>'; print_r($resParti); die;

			$msg .= '<div class="col-lg-12 pro-list-title d-flex align-items-center program-list">
                                <div class="col-md-2 pro-list-title-each text-center">
                                  <img src="images/clock-icon.png" alt="" />
                                </div>
                                <div class="col-md-2 pro-list-title-each text-center">
                                  <h3>Topic</h3>
                                </div>
                                 <div class="col-md-2 pro-list-title-each text-center">
                                  <h3>Hall</h3>
                                </div>
                                <div class="col-md-3 pro-list-title-each text-center">
                                  <h3>Speaker</h3>
                                </div>
                                <div class="col-md-3 pro-list-title-each text-center">
                                  <h3>Moderator /
                                    Chairperson</h3>
                                </div>
                              </div>
                              <div class="pro-list-row-wrap col-12">	
                              ';

			if ($resParti) {
				foreach ($resParti as $k => $val) {
					if (!empty($val['topic_title']) && $val['topic_title'] != '') {

						if (!empty($val['participant_type']) && $val['participant_type'] == 'Speaker') {
							$speakerParticipant = $val['participant_full_name'];
						} else {
							$speakerParticipant = '-';
						}

						if (!empty($val['participant_type']) && ($val['participant_type'] == 'Chairperson' || $val['participant_type'] == 'Moderator')) {
							$chairpersonParticipant = $val['participant_full_name'] . '(' . $val['participant_type'] . ')';
						} else {
							$chairpersonParticipant = '-';
						}

						$msg .= '<div class="pro-list-each-row d-flex align-items-center">
                                  <div class="col-md-2 pro-list-info-each text-center">
                                    <h3>' . $val['session_start_time'] . '-' . $val['session_end_time'] . '</h3>

                                  </div>
                                  <div class="col-md-2 pro-list-info-each text-center">
                                    <h6><a href="javascript:void(0)">' . $val['topic_title'] . '</a></h6>
                                  </div>
                                   <div class="col-md-2 pro-list-info-each text-center">
                                    <h3>' . $val['hall_title'] . '</h3>
                                  </div>

                                  <div
                                    class="col-md-3 pro-list-info-each text-center d-flex align-items-center speaker justify-content-center">
                                    
                                    <h3>' . $speakerParticipant . '</h3>
                                  </div>
                                  <div class="col-md-3 pro-list-info-each text-center">
                                    <h3>' . $chairpersonParticipant . ' </h3>
                                  </div>
                                </div>';
					}
				}
			} else {
				$msg .= '<h6 style="color:d76b6b;text-align:center;">No Record Found!</h6>';
			}

			$msg .= '</div>';

			$arr = array(
				'succ' => 200,
				'data' => $msg
			);
		} else {
			$arr = array(
				'error' => 400,
				'msg' => 'Please enter right credentials'
			);
		}

		echo json_encode($arr);

		exit();
		break;

	case 'getPaymentVoucharDetails':

		include_once('includes/function.workshop.php');
		include_once('includes/function.delegate.php');
		include_once('includes/function.dinner.php');
		include_once('includes/function.invoice.php');
		$user_email_id = addslashes(trim($_REQUEST['user_email_id']));
		$sqlFetchUser 					 = array();
		$sqlFetchUser['QUERY']          = "SELECT user.id AS delegateId,
											   		   slip.id AS slipId, 
													   user.registration_payment_status,
													   slip.registration_token
												  FROM " . _DB_SLIP_ . " slip
											LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " user
													ON slip.delegate_id = user.id
												 WHERE user.user_email_id = ?
												   AND slip.status = ?
												   AND user.status = ?
												    AND slip.id = ?";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user.user_email_id',  'DATA' => $user_email_id,  'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.status',         'DATA' => 'A',                              'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user.status',         'DATA' => 'A',                              'TYP' => 's');
        $sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.id',         'DATA' =>$_REQUEST['slipId'],                              'TYP' => 's');

		$resultUser              = $mycms->sql_select($sqlFetchUser);

		if ($resultUser) {
			foreach ($resultUser as $k => $rowUser) {


				$mycms->setSession('LOGGED.USER.ID', $rowUser['delegateId']);
				$mycms->setSession('IS_LOGIN', "YES");
				$mycms->removeSession('OTP.ID');
				$mycms->removeSession('TEMP.ID');
				$mycms->removeSession('USER.EM');
				$mycms->removeSession('USER.ID');
				$mycms->removeSession('USER.PWD');
				$mycms->removeSession('USER.TOKEN');

				$resFetchSlip = slipDetailsOfUserSpecific($rowUser['delegateId'],'',$_REQUEST['slipId']);
				$rowFetchSlip = $resFetchSlip[0];
		
				$mycms->setSession('SLIP_ID', $rowFetchSlip['id']);
				if ($currentCutoffId > 0) {
					if ($rowFetchSlip['invoice_mode'] == "OFFLINE") {

						$isSetPayment = getPaymentDetailsDelegate($rowUser['id']);
						if ($isSetPayment == '') {
							// =============================== OFFLINE PAYMENT DETAILS ====================================
							$delegateId = $rowUser['delegateId'];
							$slipId = $rowUser['slipId'];

							$slipDetails  	 				= slipDetails($rowUser['slipId']);
							$delegateDetails 				= getUserDetails($rowUser['delegateId']);
							$pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
							$invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
							$totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);

							$data = ' <p>Name
                      			   : <b>' . $delegateDetails['user_full_name'] . '</b>
									</p>
									<p>Email Id
										 : <b>' . $delegateDetails['user_email_id'] . '</b>
									</p>
									<p>Mobile
										 : <b>' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</b>
									</p>
									<p>PV No.
										 : <b>' . $slipDetails['slip_number'] . '</b>
									</p>
									<p>Payment Status
										 : <b>' . (($slipDetails['payment_status'] == 'UNPAID') ? 'Pending' : (($slipDetails['payment_status'] == 'PAID') ? 'Paid' : (($slipDetails['payment_status'] == 'COMPLIMENTARY') ? 'Complimentary' : 'Zero Value'))) . '</b>
									</p><br>
								';

							// =================================== Order Summery =========================================
							$invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
							$counter = 0;
							$invoiceDisplaysArr = array();
							foreach ($invoiceDetailsArr as $key => $invoiceDetails) {
								$show = true;
								if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
									$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id'], true);
									if ($workShopDetails['display'] == 'N') {
										if ($invoiceDetails['remarks'] == 'Adjusted Workshop') {
											$show = false;
										}
									}
								}

								if ($show) {
									$counter 		 = $counter + 1;
									$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);

									$type			 = "";

									$invoiceServiceType = $invoiceDetails['service_type'];
									if ($invoiceServiceType == "DELEGATE_CONFERENCE_REGISTRATION") {
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
									}
									if ($invoiceServiceType == "DELEGATE_RESIDENTIAL_REGISTRATION") {
										// $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
										$comboDetails = getComboPackageDetails($thisUserDetails['combo_classification_id'], $thisUserDetails['accDateCombo']);
										$type = "COMBO REGISTRATION - " . $comboDetails['PACKAGE_NAME'] . " @" . $comboDetails['HOTEL_NAME'];
									}
									if ($invoiceServiceType == "DELEGATE_WORKSHOP_REGISTRATION") {
										$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
									}
									if ($invoiceServiceType == "ACCOMPANY_CONFERENCE_REGISTRATION") {
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
									}
									if ($invoiceServiceType == "DELEGATE_ACCOMMODATION_REQUEST") {
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
									}
									if ($invoiceServiceType == "DELEGATE_TOUR_REQUEST") {
										$tourDetails = getTourDetails($invoiceDetails['refference_id']);

										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
									}
									if ($invoiceServiceType == "DELEGATE_DINNER_REQUEST") {
										$dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
										$dinnerRefId = $dinnerDetails['refference_id'];
										$dinner_user_type = dinnerForWhome($dinnerRefId);
										if ($dinner_user_type == 'ACCOMPANY') {
											$invoiceServiceType = 'ACCOMPANY_DINNER_REQUEST';
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
										} else {
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
										}
									}
									$invoiceDisplaysArr[$invoiceServiceType][$counter]['TYPE'] = $type;
									$invoiceDisplaysArr[$invoiceServiceType][$counter]['INVOICE_DETAILS'] = $invoiceDetails;
									$counter++;
								}
							}
							// echo '<pre>'; print_r($cfg['SERVICE.SEQUENCE']);die;
							$orderSummeryInvoices = ' <ul >';
							foreach ($cfg['SERVICE.SEQUENCE'] as $keyServ => $servType) {
								$invoices = $invoiceDisplaysArr[$servType];

								foreach ($invoices as $k => $invDetails) {
									// echo '<pre>'; print_r($invDetails);die;

									$invoiceDetails = $invDetails['INVOICE_DETAILS'];
									$type			= $invDetails['TYPE'];
									$returnArray 	= discountAmount($invoiceDetails['id']);
									if ($invoiceDetails['invoice_mode'] == 'OFFLINE') {
										$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
										// $invoiceVal 	= $returnArray['BASIC_AMOUNT'];
									} else {
										$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
									}
									$orderSummeryInvoices .= '<li >
											<p class="order-id">
												
												<span use="icon">' . $invoiceDetails['invoice_number'] . '</span>
											</p>
											<p class="order-name">
												<span use="invTitle">' . $type . '</span>
											</p>
											<p class="order-amount">
												<span use="amount">₹ ' . number_format($invoiceVal, 2) . '</span>
											</p>
											<span use="deleteIcon"></span>
											<!-- <button class="order-dlt" use="deleteIcon" id="deleteItem" style="display:none">
															<i class="fas fa-times"></i>
														</button> -->
											</li>';
								}
							}
							$orderSummeryInvoices .= '</ul>
						<!-- <ul>
						<li >
							<p class="order-id">
								
								<span use="icon">*Inclusive of All Taxes and Internet Handling Charges</span>
							</p>
							<p class="order-name">
								<span use="invTitle">Total Amount</span>
							</p>
							<p class="order-amount">
								<span use="amount">₹ ' . number_format($invoiceAmountOfSlip, 2) . '</span>
							</p>
							
						</li>
						<li >
							<p class="order-id">
								
								<span use="icon"></span>
							</p>
							<p class="order-name">
								<span use="invTitle">Paid Amount</span>
							</p>
							<p class="order-amount">
								<span use="amount">₹ ' . number_format($totalSetPaymentAmountOfSlip, 2) . '</span>
							</p>
							
						</li>
						</ul> -->
						<hr>
						<div class="total-bill-amount" use="totalAmount">
                        <h5>Total Payable Amount</h5>
						<span style="font-size=10px">*Inclusive of All Taxes</span>
                        <h3 use="totalAmount">₹ ' . number_format($pendingAmountOfSlip, 2) . '</h3>
                    </div>
					';
							
							$_SESSION['REG_PAYMENT_DATA'] = array(
								'slipId' => $rowUser['slipId'],
								'delegateId' => $rowUser['delegateId'],
								'user_email_id' => $delegateDetails['user_email_id'],
								'invoice_mode' => $rowFetchSlip['invoice_mode'],
								'data' => $data,
								'orderSummeryInvoices' => $orderSummeryInvoices,
								'mode' => isset($arr['mode']) ? $arr['mode'] : 'ONLINE'
							);

							// ================================================== X =======================================================
							$arr = array(
								'succ' => 200,
								'msg' => $unpaid_online,
								'slipId' => $rowUser['slipId'],
								'delegateId' => $rowUser['delegateId'],
								'invoice_mode' => $rowFetchSlip['invoice_mode'],
								'data' => $data,
								'mode' => 'OFFLINE',
								'orderSummeryInvoices' => $orderSummeryInvoices

							);

							echo json_encode($arr);

							exit();
						} else {
							// Payment set already
							$find = ['[CONF NAME]', '[PHONE NUMBER]'];
							$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
							$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline);
							$msg = $unpaid_offline_msg;
							$arr = array(
								'error' => 400,
								'msg' => $msg
							);

							echo json_encode($arr);
						}
					} else {
						// =================================== DATA  FOR Order Details  =====================================================

						// $data =  getPrintSlipDetailsContent($rowUser['delegateId'], $rowUser['slipId'], false, false, true);

						$delegateId = $rowUser['delegateId'];
						$slipId = $rowUser['slipId'];

						$slipDetails  	 				= slipDetails($rowUser['slipId']);
						$delegateDetails 				= getUserDetails($rowUser['delegateId']);
						$pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
						$invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
						$totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);

						$data = ' <p>Name
                      			  <br><b>' . $delegateDetails['user_full_name'] . '</b>
									</p>
									<p>Email Id
										<br><b>' . $delegateDetails['user_email_id'] . '</b>
									</p>
									<p>Mobile
										<br><b>' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</b>
									</p>
									<p>PV No.
										<br><b>' . $slipDetails['slip_number'] . '</b>
									</p>
									<p>Payment Status
										<br><b>' . (($slipDetails['payment_status'] == 'UNPAID') ? 'Pending' : (($slipDetails['payment_status'] == 'PAID') ? 'Paid' : (($slipDetails['payment_status'] == 'COMPLIMENTARY') ? 'Complimentary' : 'Zero Value'))) . '</b>
									</p><br>
								';

						// =================================== Order Summery =========================================
						$invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
						$counter = 0;
						$invoiceDisplaysArr = array();
						foreach ($invoiceDetailsArr as $key => $invoiceDetails) {
							$show = true;
							if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
								$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id'], true);
								if ($workShopDetails['display'] == 'N') {
									if ($invoiceDetails['remarks'] == 'Adjusted Workshop') {
										$show = false;
									}
								}
							}

							if ($show) {
								$counter 		 = $counter + 1;
								$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);

								$type			 = "";

								$invoiceServiceType = $invoiceDetails['service_type'];
								if ($invoiceServiceType == "DELEGATE_CONFERENCE_REGISTRATION") {
									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
								}
								if ($invoiceServiceType == "DELEGATE_RESIDENTIAL_REGISTRATION") {
									// $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
									$comboDetails = getComboPackageDetails($thisUserDetails['combo_classification_id'], $thisUserDetails['accDateCombo']);
									$type = "COMBO REGISTRATION - " . $comboDetails['PACKAGE_NAME'] . " @" . $comboDetails['HOTEL_NAME'];
								}
								if ($invoiceServiceType == "DELEGATE_WORKSHOP_REGISTRATION") {
									$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
								}
								if ($invoiceServiceType == "ACCOMPANY_CONFERENCE_REGISTRATION") {
									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
								}
								if ($invoiceServiceType == "DELEGATE_ACCOMMODATION_REQUEST") {
									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
								}
								if ($invoiceServiceType == "DELEGATE_TOUR_REQUEST") {
									$tourDetails = getTourDetails($invoiceDetails['refference_id']);

									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
								}
								if ($invoiceServiceType == "DELEGATE_DINNER_REQUEST") {
									$dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
									$dinnerRefId = $dinnerDetails['refference_id'];
									$dinner_user_type = dinnerForWhome($dinnerRefId);
									if ($dinner_user_type == 'ACCOMPANY') {
										$invoiceServiceType = 'ACCOMPANY_DINNER_REQUEST';
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
									} else {
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
									}
								}
								$invoiceDisplaysArr[$invoiceServiceType][$counter]['TYPE'] = $type;
								$invoiceDisplaysArr[$invoiceServiceType][$counter]['INVOICE_DETAILS'] = $invoiceDetails;
								$counter++;
							}
						}
						// echo '<pre>'; print_r($cfg['SERVICE.SEQUENCE']);die;
						$orderSummeryInvoices = ' <ul >';
						foreach ($cfg['SERVICE.SEQUENCE'] as $keyServ => $servType) {
							$invoices = $invoiceDisplaysArr[$servType];

							foreach ($invoices as $k => $invDetails) {
								// echo '<pre>'; print_r($invDetails);die;

								$invoiceDetails = $invDetails['INVOICE_DETAILS'];
								$type			= $invDetails['TYPE'];
								$returnArray 	= discountAmount($invoiceDetails['id']);
								if ($invoiceDetails['invoice_mode'] == 'OFFLINE') {
									// $invoiceVal 	= $returnArray['BASIC_AMOUNT'];
									$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
								} else {
									$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
								}
								$orderSummeryInvoices .= '<li >
											<p class="order-id">
												
												<span use="icon">' . $invoiceDetails['invoice_number'] . '</span>
											</p>
											<p class="order-name">
												<span use="invTitle">' . $type . '</span>
											</p>
											<p class="order-amount">
												<span use="amount">₹ ' . number_format($invoiceVal, 2) . '</span>
											</p>
											<span use="deleteIcon"></span>
											<!-- <button class="order-dlt" use="deleteIcon" id="deleteItem" style="display:none">
															<i class="fas fa-times"></i>
														</button> -->
											</li>';
							}
						}
						$orderSummeryInvoices .= '</ul>
						<!-- <ul>
						<li >
							<p class="order-id">
								
								<span use="icon">*Inclusive of All Taxes and Internet Handling Charges</span>
							</p>
							<p class="order-name">
								<span use="invTitle">Total Amount</span>
							</p>
							<p class="order-amount">
								<span use="amount">₹ ' . number_format($invoiceAmountOfSlip, 2) . '</span>
							</p>
							
						</li>
						<li >
							<p class="order-id">
								
								<span use="icon"></span>
							</p>
							<p class="order-name">
								<span use="invTitle">Paid Amount</span>
							</p>
							<p class="order-amount">
								<span use="amount">₹ ' . number_format($totalSetPaymentAmountOfSlip, 2) . '</span>
							</p>
							
						</li>
						</ul> -->
						<hr>
						<div class="total-bill-amount" use="totalAmount">
                        <h5>Total Payable Amount</h5>
						<span style="font-size=10px">*Inclusive of All Taxes and Internet Handling Charges</span>
                        <h3 use="totalAmount">₹ ' . number_format($pendingAmountOfSlip, 2) . '</h3>
                    </div>
					';

						$_SESSION['REG_PAYMENT_DATA'] = array(
								'slipId' => $rowUser['slipId'],
								'delegateId' => $rowUser['delegateId'],
								'user_email_id' => $delegateDetails['user_email_id'],
								'invoice_mode' => $rowFetchSlip['invoice_mode'],
								'data' => $data,
								'orderSummeryInvoices' => $orderSummeryInvoices,
								'mode' => isset($arr['mode']) ? $arr['mode'] : 'ONLINE'
							);
						// ================================================== X =======================================================
						$arr = array(
							'succ' => 200,
							'msg' => $unpaid_online,
							'slipId' => $rowUser['slipId'],
							'delegateId' => $rowUser['delegateId'],
							'invoice_mode' => $rowFetchSlip['invoice_mode'],
							'data' => $data,
							'orderSummeryInvoices' => $orderSummeryInvoices

						);

						echo json_encode($arr);

						exit();
					}
				} else {
					$mycms->redirect('registration.tariff.php');
				}
				exit();
			}
			//$mycms->redirect('login.php?m=3');
		} else {
			$msg = 'Wrong credentials';
			$arr = array(
				'error' => 400,
				'msg' => $msg
			);
			// Store full payment data in session
		

			echo json_encode($arr);
		}
		//$mycms->redirect('login.php?m=5');

		exit();
		break;
		case 'getPaymentVoucharDetailsIndex':

		include_once('includes/function.workshop.php');
		include_once('includes/function.delegate.php');
		include_once('includes/function.dinner.php');
		include_once('includes/function.invoice.php');
		$user_email_id = addslashes(trim($_REQUEST['user_email_id']));
		$sqlFetchUser 					 = array();
		$sqlFetchUser['QUERY']          = "SELECT user.id AS delegateId,
											   		   slip.id AS slipId, 
													   user.registration_payment_status,
													   slip.registration_token
												  FROM " . _DB_SLIP_ . " slip
											LEFT OUTER JOIN " . _DB_USER_REGISTRATION_ . " user
													ON slip.delegate_id = user.id
												 WHERE user.user_email_id = ?
												   AND slip.status = ?
												   AND user.status = ?
												 ";

		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user.user_email_id',  'DATA' => $user_email_id,  'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.status',         'DATA' => 'A',                              'TYP' => 's');
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user.status',         'DATA' => 'A',                              'TYP' => 's');

		$resultUser              = $mycms->sql_select($sqlFetchUser);

		if ($resultUser) {
			foreach ($resultUser as $k => $rowUser) {


				$mycms->setSession('LOGGED.USER.ID', $rowUser['delegateId']);
				$mycms->setSession('IS_LOGIN', "YES");
				$mycms->removeSession('OTP.ID');
				$mycms->removeSession('TEMP.ID');
				$mycms->removeSession('USER.EM');
				$mycms->removeSession('USER.ID');
				$mycms->removeSession('USER.PWD');
				$mycms->removeSession('USER.TOKEN');

				$resFetchSlip = slipDetailsOfUser($rowUser['delegateId']);
				$rowFetchSlip = $resFetchSlip[0];
		
				$mycms->setSession('SLIP_ID', $rowFetchSlip['id']);
				if ($currentCutoffId > 0) {
					if ($rowFetchSlip['invoice_mode'] == "OFFLINE") {

						$isSetPayment = getPaymentDetailsDelegate($rowUser['id']);
						if ($isSetPayment == '') {
							// =============================== OFFLINE PAYMENT DETAILS ====================================
							$delegateId = $rowUser['delegateId'];
							$slipId = $rowUser['slipId'];

							$slipDetails  	 				= slipDetails($rowUser['slipId']);
							$delegateDetails 				= getUserDetails($rowUser['delegateId']);
							$pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
							$invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
							$totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);

							$data = ' <p>Name
                      			   : <b>' . $delegateDetails['user_full_name'] . '</b>
									</p>
									<p>Email Id
										 : <b>' . $delegateDetails['user_email_id'] . '</b>
									</p>
									<p>Mobile
										 : <b>' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</b>
									</p>
									<p>PV No.
										 : <b>' . $slipDetails['slip_number'] . '</b>
									</p>
									<p>Payment Status
										 : <b>' . (($slipDetails['payment_status'] == 'UNPAID') ? 'Pending' : (($slipDetails['payment_status'] == 'PAID') ? 'Paid' : (($slipDetails['payment_status'] == 'COMPLIMENTARY') ? 'Complimentary' : 'Zero Value'))) . '</b>
									</p><br>
								';

							// =================================== Order Summery =========================================
							$invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
							$counter = 0;
							$invoiceDisplaysArr = array();
							foreach ($invoiceDetailsArr as $key => $invoiceDetails) {
								$show = true;
								if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
									$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id'], true);
									if ($workShopDetails['display'] == 'N') {
										if ($invoiceDetails['remarks'] == 'Adjusted Workshop') {
											$show = false;
										}
									}
								}

								if ($show) {
									$counter 		 = $counter + 1;
									$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);

									$type			 = "";

									$invoiceServiceType = $invoiceDetails['service_type'];
									if ($invoiceServiceType == "DELEGATE_CONFERENCE_REGISTRATION") {
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
									}
									if ($invoiceServiceType == "DELEGATE_RESIDENTIAL_REGISTRATION") {
										// $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
										$comboDetails = getComboPackageDetails($thisUserDetails['combo_classification_id'], $thisUserDetails['accDateCombo']);
										$type = "COMBO REGISTRATION - " . $comboDetails['PACKAGE_NAME'] . " @" . $comboDetails['HOTEL_NAME'];
									}
									if ($invoiceServiceType == "DELEGATE_WORKSHOP_REGISTRATION") {
										$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
									}
									if ($invoiceServiceType == "ACCOMPANY_CONFERENCE_REGISTRATION") {
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
									}
									if ($invoiceServiceType == "DELEGATE_ACCOMMODATION_REQUEST") {
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
									}
									if ($invoiceServiceType == "DELEGATE_TOUR_REQUEST") {
										$tourDetails = getTourDetails($invoiceDetails['refference_id']);

										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
									}
									if ($invoiceServiceType == "DELEGATE_DINNER_REQUEST") {
										$dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
										$dinnerRefId = $dinnerDetails['refference_id'];
										$dinner_user_type = dinnerForWhome($dinnerRefId);
										if ($dinner_user_type == 'ACCOMPANY') {
											$invoiceServiceType = 'ACCOMPANY_DINNER_REQUEST';
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
										} else {
											$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
										}
									}
									$invoiceDisplaysArr[$invoiceServiceType][$counter]['TYPE'] = $type;
									$invoiceDisplaysArr[$invoiceServiceType][$counter]['INVOICE_DETAILS'] = $invoiceDetails;
									$counter++;
								}
							}
							// echo '<pre>'; print_r($cfg['SERVICE.SEQUENCE']);die;
							$orderSummeryInvoices = ' <ul >';
							foreach ($cfg['SERVICE.SEQUENCE'] as $keyServ => $servType) {
								$invoices = $invoiceDisplaysArr[$servType];

								foreach ($invoices as $k => $invDetails) {
									// echo '<pre>'; print_r($invDetails);die;

									$invoiceDetails = $invDetails['INVOICE_DETAILS'];
									$type			= $invDetails['TYPE'];
									$returnArray 	= discountAmount($invoiceDetails['id']);
									if ($invoiceDetails['invoice_mode'] == 'OFFLINE') {
										$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
										// $invoiceVal 	= $returnArray['BASIC_AMOUNT'];
									} else {
										$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
									}
									$orderSummeryInvoices .= '<li >
											<p class="order-id">
												
												<span use="icon">' . $invoiceDetails['invoice_number'] . '</span>
											</p>
											<p class="order-name">
												<span use="invTitle">' . $type . '</span>
											</p>
											<p class="order-amount">
												<span use="amount">₹ ' . number_format($invoiceVal, 2) . '</span>
											</p>
											<span use="deleteIcon"></span>
											<!-- <button class="order-dlt" use="deleteIcon" id="deleteItem" style="display:none">
															<i class="fas fa-times"></i>
														</button> -->
											</li>';
								}
							}
							$orderSummeryInvoices .= '</ul>
						<!-- <ul>
						<li >
							<p class="order-id">
								
								<span use="icon">*Inclusive of All Taxes and Internet Handling Charges</span>
							</p>
							<p class="order-name">
								<span use="invTitle">Total Amount</span>
							</p>
							<p class="order-amount">
								<span use="amount">₹ ' . number_format($invoiceAmountOfSlip, 2) . '</span>
							</p>
							
						</li>
						<li >
							<p class="order-id">
								
								<span use="icon"></span>
							</p>
							<p class="order-name">
								<span use="invTitle">Paid Amount</span>
							</p>
							<p class="order-amount">
								<span use="amount">₹ ' . number_format($totalSetPaymentAmountOfSlip, 2) . '</span>
							</p>
							
						</li>
						</ul> -->
						<hr>
						<div class="total-bill-amount" use="totalAmount">
                        <h5>Total Payable Amount</h5>
						<span style="font-size=10px">*Inclusive of All Taxes</span>
                        <h3 use="totalAmount">₹ ' . number_format($pendingAmountOfSlip, 2) . '</h3>
                    </div>
					';
							
							$_SESSION['REG_PAYMENT_DATA'] = array(
								'slipId' => $rowUser['slipId'],
								'delegateId' => $rowUser['delegateId'],
								'user_email_id' => $delegateDetails['user_email_id'],
								'invoice_mode' => $rowFetchSlip['invoice_mode'],
								'data' => $data,
								'orderSummeryInvoices' => $orderSummeryInvoices,
								'mode' => isset($arr['mode']) ? $arr['mode'] : 'ONLINE'
							);

							// ================================================== X =======================================================
							$arr = array(
								'succ' => 200,
								'msg' => $unpaid_online,
								'slipId' => $rowUser['slipId'],
								'delegateId' => $rowUser['delegateId'],
								'invoice_mode' => $rowFetchSlip['invoice_mode'],
								'data' => $data,
								'mode' => 'OFFLINE',
								'orderSummeryInvoices' => $orderSummeryInvoices

							);

							echo json_encode($arr);

							exit();
						} else {
							// Payment set already
							$find = ['[CONF NAME]', '[PHONE NUMBER]'];
							$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
							$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline);
							$msg = $unpaid_offline_msg;
							$arr = array(
								'error' => 400,
								'msg' => $msg
							);

							echo json_encode($arr);
						}
					} else {
						// =================================== DATA  FOR Order Details  =====================================================

						// $data =  getPrintSlipDetailsContent($rowUser['delegateId'], $rowUser['slipId'], false, false, true);

						$delegateId = $rowUser['delegateId'];
						$slipId = $rowUser['slipId'];

						$slipDetails  	 				= slipDetails($rowUser['slipId']);
						$delegateDetails 				= getUserDetails($rowUser['delegateId']);
						$pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
						$invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
						$totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);

						$data = ' <p>Name
                      			  <br><b>' . $delegateDetails['user_full_name'] . '</b>
									</p>
									<p>Email Id
										<br><b>' . $delegateDetails['user_email_id'] . '</b>
									</p>
									<p>Mobile
										<br><b>' . $delegateDetails['user_mobile_isd_code'] . ' ' . $delegateDetails['user_mobile_no'] . '</b>
									</p>
									<p>PV No.
										<br><b>' . $slipDetails['slip_number'] . '</b>
									</p>
									<p>Payment Status
										<br><b>' . (($slipDetails['payment_status'] == 'UNPAID') ? 'Pending' : (($slipDetails['payment_status'] == 'PAID') ? 'Paid' : (($slipDetails['payment_status'] == 'COMPLIMENTARY') ? 'Complimentary' : 'Zero Value'))) . '</b>
									</p><br>
								';

						// =================================== Order Summery =========================================
						$invoiceDetailsArr = invoiceDetailsActiveOfSlip($slipId);
						$counter = 0;
						$invoiceDisplaysArr = array();
						foreach ($invoiceDetailsArr as $key => $invoiceDetails) {
							$show = true;
							if ($invoiceDetails['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
								$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id'], true);
								if ($workShopDetails['display'] == 'N') {
									if ($invoiceDetails['remarks'] == 'Adjusted Workshop') {
										$show = false;
									}
								}
							}

							if ($show) {
								$counter 		 = $counter + 1;
								$thisUserDetails = getUserDetails($invoiceDetails['delegate_id']);

								$type			 = "";

								$invoiceServiceType = $invoiceDetails['service_type'];
								if ($invoiceServiceType == "DELEGATE_CONFERENCE_REGISTRATION") {
									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "CONFERENCE");
								}
								if ($invoiceServiceType == "DELEGATE_RESIDENTIAL_REGISTRATION") {
									// $type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "RESIDENTIAL");
									$comboDetails = getComboPackageDetails($thisUserDetails['combo_classification_id'], $thisUserDetails['accDateCombo']);
									$type = "COMBO REGISTRATION - " . $comboDetails['PACKAGE_NAME'] . " @" . $comboDetails['HOTEL_NAME'];
								}
								if ($invoiceServiceType == "DELEGATE_WORKSHOP_REGISTRATION") {
									$workShopDetails = getWorkshopDetails($invoiceDetails['refference_id']);
									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "WORKSHOP");
								}
								if ($invoiceServiceType == "ACCOMPANY_CONFERENCE_REGISTRATION") {
									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMPANY");
								}
								if ($invoiceServiceType == "DELEGATE_ACCOMMODATION_REQUEST") {
									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "ACCOMMODATION");
								}
								if ($invoiceServiceType == "DELEGATE_TOUR_REQUEST") {
									$tourDetails = getTourDetails($invoiceDetails['refference_id']);

									$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "TOUR");
								}
								if ($invoiceServiceType == "DELEGATE_DINNER_REQUEST") {
									$dinnerDetails = getDinnerDetails($invoiceDetails['refference_id']);
									$dinnerRefId = $dinnerDetails['refference_id'];
									$dinner_user_type = dinnerForWhome($dinnerRefId);
									if ($dinner_user_type == 'ACCOMPANY') {
										$invoiceServiceType = 'ACCOMPANY_DINNER_REQUEST';
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
									} else {
										$type = getInvoiceTypeString($invoiceDetails['delegate_id'], $invoiceDetails['refference_id'], "DINNER");
									}
								}
								$invoiceDisplaysArr[$invoiceServiceType][$counter]['TYPE'] = $type;
								$invoiceDisplaysArr[$invoiceServiceType][$counter]['INVOICE_DETAILS'] = $invoiceDetails;
								$counter++;
							}
						}
						// echo '<pre>'; print_r($cfg['SERVICE.SEQUENCE']);die;
						$orderSummeryInvoices = ' <ul >';
						foreach ($cfg['SERVICE.SEQUENCE'] as $keyServ => $servType) {
							$invoices = $invoiceDisplaysArr[$servType];

							foreach ($invoices as $k => $invDetails) {
								// echo '<pre>'; print_r($invDetails);die;

								$invoiceDetails = $invDetails['INVOICE_DETAILS'];
								$type			= $invDetails['TYPE'];
								$returnArray 	= discountAmount($invoiceDetails['id']);
								if ($invoiceDetails['invoice_mode'] == 'OFFLINE') {
									// $invoiceVal 	= $returnArray['BASIC_AMOUNT'];
									$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
								} else {
									$invoiceVal 	= $returnArray['TOTAL_AMOUNT'];
								}
								$orderSummeryInvoices .= '<li >
											<p class="order-id">
												
												<span use="icon">' . $invoiceDetails['invoice_number'] . '</span>
											</p>
											<p class="order-name">
												<span use="invTitle">' . $type . '</span>
											</p>
											<p class="order-amount">
												<span use="amount">₹ ' . number_format($invoiceVal, 2) . '</span>
											</p>
											<span use="deleteIcon"></span>
											<!-- <button class="order-dlt" use="deleteIcon" id="deleteItem" style="display:none">
															<i class="fas fa-times"></i>
														</button> -->
											</li>';
							}
						}
						$orderSummeryInvoices .= '</ul>
						<!-- <ul>
						<li >
							<p class="order-id">
								
								<span use="icon">*Inclusive of All Taxes and Internet Handling Charges</span>
							</p>
							<p class="order-name">
								<span use="invTitle">Total Amount</span>
							</p>
							<p class="order-amount">
								<span use="amount">₹ ' . number_format($invoiceAmountOfSlip, 2) . '</span>
							</p>
							
						</li>
						<li >
							<p class="order-id">
								
								<span use="icon"></span>
							</p>
							<p class="order-name">
								<span use="invTitle">Paid Amount</span>
							</p>
							<p class="order-amount">
								<span use="amount">₹ ' . number_format($totalSetPaymentAmountOfSlip, 2) . '</span>
							</p>
							
						</li>
						</ul> -->
						<hr>
						<div class="total-bill-amount" use="totalAmount">
                        <h5>Total Payable Amount</h5>
						<span style="font-size=10px">*Inclusive of All Taxes and Internet Handling Charges</span>
                        <h3 use="totalAmount">₹ ' . number_format($pendingAmountOfSlip, 2) . '</h3>
                    </div>
					';

						$_SESSION['REG_PAYMENT_DATA'] = array(
								'slipId' => $rowUser['slipId'],
								'delegateId' => $rowUser['delegateId'],
								'user_email_id' => $delegateDetails['user_email_id'],
								'invoice_mode' => $rowFetchSlip['invoice_mode'],
								'data' => $data,
								'orderSummeryInvoices' => $orderSummeryInvoices,
								'mode' => isset($arr['mode']) ? $arr['mode'] : 'ONLINE'
							);
						// ================================================== X =======================================================
						$arr = array(
							'succ' => 200,
							'msg' => $unpaid_online,
							'slipId' => $rowUser['slipId'],
							'delegateId' => $rowUser['delegateId'],
							'invoice_mode' => $rowFetchSlip['invoice_mode'],
							'data' => $data,
							'orderSummeryInvoices' => $orderSummeryInvoices

						);

						echo json_encode($arr);

						exit();
					}
				} else {
					$mycms->redirect('registration.tariff.php');
				}
				exit();
			}
			//$mycms->redirect('login.php?m=3');
		} else {
			$msg = 'Wrong credentials';
			$arr = array(
				'error' => 400,
				'msg' => $msg
			);
			// Store full payment data in session
		

			echo json_encode($arr);
		}
		//$mycms->redirect('login.php?m=5');

		exit();
		break;

	case 'getPaymentVoucharDetailsProfile':

		if (!empty($_REQUEST['delegateId']) && !empty($_REQUEST['slipId'])) {

			$resFetchSlip = slipDetailsOfUserSpecific($_REQUEST['delegateId'], '', $_REQUEST['slipId']);
			$rowFetchSlip = $resFetchSlip[0];
			$mycms->setSession('SLIP_ID', $_REQUEST['slipId']);

			if ($rowFetchSlip['invoice_mode'] == "OFFLINE") {
				// $msg = 'This e-mail id is already registered with us. <br />For further assistance please contact with '.$cfg['EMAIL_CONF_NAME'].' Registration Secretariat. Ph no. '.$cfg['EMAIL_CONF_CONTACT_US'].' Time: 11:00 - 18:00';
				$find = ['[CONF NAME]', '[PHONE NUMBER]'];
				$replacement = [$cfg['EMAIL_CONF_NAME'], $cfg['EMAIL_CONF_CONTACT_US']];
				$unpaid_offline_msg = str_replace($find, $replacement, $unpaid_offline);
				$msg = $unpaid_offline_msg;
				$arr = array(
					'error' => 400,
					'msg' => $msg
				);

				echo json_encode($arr);
			} else {

				$data =  getPrintSlipDetailsContent($_REQUEST['delegateId'], $_REQUEST['slipId'], false, false, true);

				$arr = array(
					'succ' => 200,
					'msg' => $unpaid_online,
					'slipId' => $_REQUEST['slipId'],
					'delegateId' => $_REQUEST['delegateId'],
					'invoice_mode' => $rowFetchSlip['invoice_mode'],
					'data' => $data,

				);
				//  echo '<pre>'; print_r($arr); die;
				echo json_encode($arr);

				exit();
			}
		} else {
			$msg = 'Wrong credentials';
			$arr = array(
				'error' => 400,
				'msg' => $msg
			);

			echo json_encode($arr);
		}

		/*$user_email_id = addslashes(trim($_REQUEST['user_email_id']));
			$sqlFetchUser 					 = array();
			 $sqlFetchUser['QUERY']          = "SELECT user.id AS delegateId,
											   		   slip.id AS slipId, 
													   user.registration_payment_status,
													   slip.registration_token
												  FROM "._DB_SLIP_." slip
											LEFT OUTER JOIN "._DB_USER_REGISTRATION_." user
													ON slip.delegate_id = user.id
												 WHERE user.user_email_id = ?
												   AND slip.status = ?
												   AND user.status = ?";
										   
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user.user_email_id',  'DATA' =>$user_email_id,  'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'slip.status',         'DATA' =>'A',                              'TYP' => 's');
			$sqlFetchUser['PARAM'][]   = array('FILD' => 'user.status',         'DATA' =>'A',                              'TYP' => 's');
										   
			$resultUser              = $mycms->sql_select($sqlFetchUser);
			
			if($resultUser)
			{
				foreach($resultUser as $k=>$rowUser)
				{
					
					
					$mycms->setSession('LOGGED.USER.ID',$rowUser['delegateId']);
					$mycms->setSession('IS_LOGIN',"YES");
					$mycms->removeSession('OTP.ID');
					$mycms->removeSession('TEMP.ID');
					$mycms->removeSession('USER.EM');
					$mycms->removeSession('USER.ID');
					$mycms->removeSession('USER.PWD');
					$mycms->removeSession('USER.TOKEN');
					
					$resFetchSlip = slipDetailsOfUser($rowUser['delegateId']);
					$rowFetchSlip = $resFetchSlip[0];		
					$mycms->setSession('SLIP_ID',$rowFetchSlip['id']);	
					if($currentCutoffId > 0)
					{		
						if($rowFetchSlip['invoice_mode']=="OFFLINE")
						{
							$msg = 'This e-mail id is already registered with us. <br />For further assistance please contact with '.$cfg['EMAIL_CONF_NAME'].' Registration Secretariat. Ph no. '.$cfg['EMAIL_CONF_CONTACT_US'].' Time: 11:00 - 18:00';
								$arr = array ( 
							 		'error' =>400,
							 		'msg'=>$msg 
						 		);

						  echo json_encode($arr);
						}
						else
						{

							$data =  getPrintSlipDetailsContent($rowUser['delegateId'], $rowUser['slipId'], false, false, true);

							 $arr = array ( 
							 		'succ' =>200,
							 		'msg'=>'Payment pending, check Your order and pay now',
							 		'slipId'=>$rowUser['slipId'],
							 		'delegateId'=>$rowUser['delegateId'],
							 		'invoice_mode'=>$rowFetchSlip['invoice_mode'],
							 		'data'=> $data,

						 		);

							  echo json_encode($arr);
							
							exit();
							
						}
					}
					else
					{
						$mycms->redirect('registration.tariff.php');
					}
					exit();
				}
				//$mycms->redirect('login.php?m=3');
			}
			else
			{
					$msg = 'Wrong credentials';
					$arr = array ( 
				 		'error' =>400,
				 		'msg'=>$msg 
			 		);

			  echo json_encode($arr);
			}*/
		//$mycms->redirect('login.php?m=5');

		exit();
		break;
	case 'uniqueSeqVerification';

		$unique_sequence          = addslashes(trim($_REQUEST['user_otp']));
		if ($mycms->getSession('USER.PWD') == $unique_sequence) {
			$mycms->setSession('LOGGED.USER.ID', $mycms->getSession('TEMP.ID'));
			$mycms->setSession('IS_LOGIN', "YES");
			$mycms->removeSession('OTP.ID');
			$mycms->removeSession('TEMP.ID');
			$mycms->removeSession('USER.EM');
			$mycms->removeSession('USER.ID');
			$mycms->removeSession('USER.PWD');
			$mycms->redirect('profile.php');
		} else {
			$mycms->redirect('login.php?m=3');
		}
		exit();
		break;

	case 'getCancelationInvoice';

		$delegateId          = addslashes(trim($_REQUEST['delegateId']));
		$type          = addslashes(trim($_REQUEST['type']));

		if (!empty($delegateId)) {

			$sqlSlip['QUERY']  	 = "SELECT invoice.invoice_number AS invoiceNumber,
										   invoice.service_roundoff_price AS amount,
										   invoice.cgst_price AS cgst_price,
										   invoice.sgst_price AS sgst_price,
										   invoice.cgst_int_price AS cgst_int_price,
										   invoice.sgst_int_price AS sgst_int_price,
										   invoice.service_product_price AS serviceAmount,
										   invoice.internet_handling_amount AS intCharge,
										   invoice.invoice_mode AS invoice_mode,
										   invoice.id AS invoiceId,
										   invoice.delegate_id AS delegate_id,
										   invoice.invoice_date AS invoiceDate,
										   invoice.status AS invoiceStatus,
										   invoice.remarks AS invoiceRemarks,	
										   invoice.service_type AS invoiceFor,
										   invoice.refference_id AS reqId,
										   IFNULL(IFNULL(invoice.service_basic_price,service_unit_price),0.0) AS service_basic_price,
										   IFNULL(IFNULL(invoice.service_basic_int_price,internet_handling_amount),0.0) AS service_basic_int_price,
										   invoice.payment_status AS paymentStatus,
										   slip.slip_number AS slipNumber,
										   slip.id AS slipId,
										   slipUser.user_full_name AS slipUserName,
										   invoiceUser.user_full_name AS invoiceUserName,
										   invoiceUser.user_registration_id AS user_registration_id,
										   invoiceUser.user_unique_sequence AS user_unique_sequence,
										   invoiceUser.user_email_id AS user_email_id,
										   invoiceUser.user_mobile_no AS user_mobile_no,
										   invoiceUser.status AS invoiceUserStatus,
										   invoiceUser.registration_classification_id AS registrationClassificationId
										     
											
									  FROM " . _DB_INVOICE_ . " invoice			  
						   
								INNER JOIN " . _DB_SLIP_ . " slip
										ON invoice.slip_id = slip.id
								
								
										
								LEFT OUTER JOIN " . _DB_CANCEL_INVOICE_ . " cancl
										ON cancl.invoice_id = invoice.id
												
								INNER JOIN " . _DB_USER_REGISTRATION_ . " invoiceUser
										ON invoice.delegate_id = invoiceUser.id
										
								INNER JOIN " . _DB_USER_REGISTRATION_ . " slipUser
										ON slip.delegate_id = slipUser.id
										" . $join . "
									 WHERE invoice.status IN ('A')
									   AND slip.status  = 'A'
									   AND invoiceUser.status = 'A'
									   AND invoiceUser.registration_request IN ('GENERAL','SPOT')
									   AND invoiceUser.id = '$delegateId'
									   AND invoice.service_type = '$type'
										  
								  ORDER BY invoice.id DESC";


			$resSlip   = $mycms->sql_select($sqlSlip, false);

			//echo '<pre>'; print_r($resSlip); die; 


			$msg = '';
			$msg1 .= '<p ><h3 style="text-align:center;">Invoice Cancelation</h3></p>';
			$msg1 .= '<table class="can-inv-table" width="100%" style="font-size:16px;color:#fff;" cellpadding="1">';
			$msg1 .= '<thead><tr>
				       
				        <td><strong>Invoice Details</strong></td>
				        <td><strong>Invoice For</strong></td>
				        <td><strong>Invoice Amount</strong></td>
				         <td><strong>Cause Of Cancelation</strong></td>
				        <td><strong>Action</strong></td>
				        </tr>
				</thead>
				<tbody>';

			$mode = '';
			if (count($resSlip) > 0) {
				foreach ($resSlip as $k => $val) {


					if ($val['paymentStatus'] == "PAID" || $val['paymentStatus'] == "UNPAID") {
						$cancelInvoiceDetails = getCancelInvoiceDetailsInvoiceWise($val['invoiceId']);
						$returnArray    = discountAmount($val['invoiceId']);
						$invType =  $returnArray['TOTAL_AMOUNT'];
					} else {
						if ($val['paymentStatus'] == "COMPLEMENTARY") {
							$invType = '<span style="color:#5E8A26;">Complimentary</span>';
						} elseif ($val['paymentStatus'] == "ZERO_VALUE") {
							$invType = '<span style="color:#009900;">Zero Value</span>';
						}
					}

					if ($type == 'DELEGATE_CONFERENCE_REGISTRATION') {
						$mode = 'Member';
					} else if ($type == 'DELEGATE_WORKSHOP_REGISTRATION') {
						$mode = 'Workshop';
					} else if ($type == 'DELEGATE_ACCOMMODATION_REQUEST') {
						$mode = 'Accommodation';
					} else if ($type == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
						$mode = 'Accompany';
					} else {
						$mode = 'Dinner';
					}

					$msg1 .= '<tr>';
					$msg1 .= '<td>' . $val['invoiceNumber'] . '<br>' . date('d M Y', strtotime($val['invoiceDate'])) . '</td>';
					$msg1 .= '<td>' . $mode . '</td>';
					$msg1 .= '<td>' . $invType . '</td>';


					$msg .= '<p><b>Invoice Details:</b> <br>' . $val['invoiceNumber'] . ' | ' . date('d M Y', strtotime($val['invoiceDate']));
					$msg .= ' | ' . $mode;
					$msg .= '<br>Amount: ' . $invType . '</p><br>';
					$msg .= '<h5 class="cancl-modal-heading">Reason for cancellation</h5>';
					$msg .= '<textarea name="cancelation_cause' . $val['invoiceId'] . '" id="cancelation_cause' . $val['invoiceId'] . '" ></textarea>';
					$msg .= '<button class="default-btn next-step"  onclick="cancelationOperation(\'' . $val['invoiceId'] . '\',\'' . $delegateId . '\',\'' . $type . '\',\'' . $val['reqId'] . '\')">Submit Request</button>';



					$msg1 .= '<td><textarea name="cancelation_cause' . $val['invoiceId'] . '" id="cancelation_cause' . $val['invoiceId'] . '" style="width: 300px;"></textarea></td>';
					//$msg.= '<td><a href="javascript:void(0)" onclick="cancelationOperation("'.$val['invoiceId'].'")">Cancel</a></td>';
					$msg1 .= '<td><a class="btn cncl-btn" href="javascript:void(0)" onclick="cancelationOperation(\'' . $val['invoiceId'] . '\',\'' . $delegateId . '\',\'' . $type . '\',\'' . $val['reqId'] . '\')">Cancel</a></td>';

					$msg1 .= '</tr>';
					$msg .= '<input type="hidden" name="invoice_id[]" id="invoice_id" value="' . $val['invoiceId'] . '" autocomplete="off">';

					$msg .= '<input type="hidden" name="serviceType" id="serviceType" value="' . $type . '" autocomplete="off">';

					$msg .= '<input type="hidden" name="delegateId" id="delegateId" value="' . $delegateId . '" autocomplete="off">';

					$msg .= '<input type="hidden" name="refundAmount" id="refundAmount' . $val['invoiceId'] . '" value="' . $invType . '" autocomplete="off">';


					if ($type == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
						$accompanyDetails = getUserDetails($val['reqId']);
						$msg .= '<input type="hidden" name="accompany_id" id="accompany_id" value="' . $accompanyDetails['id'] . '" autocomplete="off">';
					}
				}
			} else {
				$msg .= '<tr><td style="text-align:center; color:red;background: #f5f0f0;padding: 5px 17px;border-radius: 21px;">No Record Found!</td></tr>';
			}


			$msg .= '</tbody>';
			$msg .= '</table>';

			$arr = array(
				'succ' => 200,
				'data' => $msg,

			);

			echo json_encode($arr);

			exit();

			//$mycms->redirect('profile.php');

		} else {
			//$mycms->redirect('login.php?m=3');
		}
		exit();
		break;

	case 'cancelationInvoiceProcess';
		include_once("includes/function.cancel_invoice.php");
		$user_id = $_REQUEST['user_id'];
		$serviceType = $_REQUEST['serviceType'];
		$invoiceId = $_REQUEST['invoiceId'];
		$refferenceId = $_REQUEST['refference_id'];
		$cancelation_cause = $_REQUEST['cancelation_cause'];
		$refundAmount = $_REQUEST['refundAmount'];

		if (!empty($user_id) && !empty($serviceType) && !empty($invoiceId) && !empty($cancelation_cause) && !empty($refundAmount)) {

			if ($serviceType == "DELEGATE_DINNER_REQUEST") {
				$sqlUpdateRequest			 = array();
				$sqlUpdateRequest['QUERY']	  = "UPDATE " . _DB_REQUEST_DINNER_ . "
														SET `status` = 'D'
													  WHERE `refference_invoice_id` = '" . $invoiceId . "'";

				$mycms->sql_update($sqlUpdateRequest, false);
			}
			if ($serviceType == "ACCOMPANY_CONFERENCE_REGISTRATION") {

				$accompanyDetails = getUserDetails($refferenceId);

				$accompany_id  = $accompanyDetails['id'];
				$sqlUpdateRequest			 = array();
				$sqlUpdateRequest['QUERY']	  = "UPDATE " . _DB_REQUEST_DINNER_ . "
														SET `status` = 'D'
										 			  WHERE `refference_id` = '" . $accompany_id . "'";

				$mycms->sql_update($sqlUpdateRequest, false);

				$sqlDinnerRequest			 = array();
				$sqlDinnerRequest['QUERY']	  = "SELECT * FROM " . _DB_REQUEST_DINNER_ . "
													 WHERE `refference_id` = '" . $accompany_id . "'";

				$resultDinnerRequest = $mycms->sql_select($sqlDinnerRequest, false);
				$rowDinnerRequest = $resultDinnerRequest[0];
				if ($resultDinnerRequest) {
					$sqlUpdateRequest			 = array();
					$sqlUpdateRequest['QUERY']	  = "UPDATE " . _DB_INVOICE_ . "
															SET `status` = 'C'
											 			  WHERE `refference_id` = '" . $rowDinnerRequest['id'] . "'";

					$mycms->sql_update($sqlUpdateRequest, false);

					$sqlDinnerInvoice			 = array();
					$sqlDinnerInvoice['QUERY']	  = "SELECT * FROM " . _DB_INVOICE_ . "
														  WHERE `refference_id` = '" . $rowDinnerRequest['id'] . "'
															AND `service_type` = 'DELEGATE_DINNER_REQUEST'";
					$resultDinnerInvoice = $mycms->sql_select($sqlDinnerInvoice, false);

					$rowDinnerInvoice = $resultDinnerInvoice[0];
					$accompDinnerInvoiceId = $rowDinnerInvoice['id'];

					$cancelId     = cancelInvoice($user_id, $accompDinnerInvoiceId, $cancelation_cause);
					approveProveProcess($cancelId, $user_id, $accompDinnerInvoiceId);

					refundCancelInvoiceProcess($user_id, $accompDinnerInvoiceId);
				}
			}
			if ($sendMailSms == 'YES') {
				registration_cancellation_confirmation_message($user_id, $invoiceId, $slip_id, 'SEND');
			}

			$cancelId     = cancelInvoice($user_id, $invoiceId, $cancelation_cause);

			approveProveProcess($cancelId, $user_id, $invoiceId);

			if ($sendMailSms == 'YES') {
				registration_cancellation_confirmation_message($user_id, $invoiceId, $slip_id, 'SEND');
			}

			refundCancelInvoiceProcess($user_id, $invoiceId);


			$arr = array(
				'succ' => 200,
				'msg' => 'Invoice has been cancelled successfully.',

			);

			echo json_encode($arr);
			exit;
		} else {
			$arr = array(
				'error' => 400,
				'msg' => 'Please enter the data',

			);

			echo json_encode($arr);
			exit;
		}



		exit();
		break;

	case 'passwordVerification';
		$user_password           = addslashes(trim($_REQUEST['user_otp']));
		if ($mycms->getSession('USER.PWD') == $user_password) {
			$mycms->setSession('LOGGED.USER.ID', $mycms->getSession('TEMP.ID'));
			$mycms->setSession('IS_LOGIN', "YES");
			$mycms->removeSession('OTP.ID');
			$mycms->removeSession('TEMP.ID');
			$mycms->removeSession('USER.EM');
			$mycms->removeSession('USER.ID');
			$mycms->removeSession('USER.PWD');
			$mycms->redirect('profile.php');
		} else {
			$mycms->redirect('login.php?m=3');
		}
		exit();
		break;

	case 'otpverification';
		$user_otp           = addslashes(trim($_REQUEST['user_otp']));
		if ($mycms->getSession('OTP.ID') == $user_otp) {
			$mycms->setSession('LOGGED.USER.ID', $mycms->getSession('TEMP.ID'));
			$mycms->setSession('IS_LOGIN', "YES");
			$mycms->removeSession('OTP.ID');
			$mycms->removeSession('TEMP.ID');
			$mycms->removeSession('RECENT.OTP');
			$mycms->removeSession('USER.ID');
			$mycms->redirect('profile.php');
		} else {
			$mycms->setSession('RECENT.OTP', true);
			$mycms->redirect('login.php');
		}
		exit();
		break;

	case 'reset';
		$mycms->removeAllSession();



		$mycms->redirect('login.php');
		exit();
		break;

		/***************************************************************************/
		/*                              LOGOUT PROCESS                             */
	/***************************************************************************/
	case 'logout':
		$mycms->removeSession('LOGGED.USER.ID');
		$mycms->removeSession('REGISTRATION.INVOICE.ID');
		$mycms->removeSession('ABSTRACT.SUBMISSION.ID');
		$mycms->removeSession('IS_LOGIN');
		unset($_SESSION['WEATHER_DATA']);
		$mycms->redirect('');
		break;
}
?>