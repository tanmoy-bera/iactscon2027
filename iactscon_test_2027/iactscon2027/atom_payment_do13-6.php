<?php
	include_once("includes/frontend.init.php");
	include_once('includes/function.delegate.php');
	include_once('includes/function.invoice.php');
	include_once('includes/function.accompany.php');
	include_once('includes/function.workshop.php');
	include_once('includes/function.registration.php');
	include_once('includes/function.messaging.php');
	include_once('includes/function.messaging.php');
	
	// FETCHING USER DETAILS
	$delegateId                     = $_REQUEST['delegate_id'];
	$slipId		                    = $_REQUEST['slip_id'];
	$cardMode		                = $_REQUEST['card_mode'];
	
	if($cardMode=='' && isset($_SESSION['PG_CARDMODE']))
	{
		$cardMode					= $_SESSION['PG_CARDMODE'];
	}
	
	$_SESSION['PG_CARDMODE']		= $cardMode;
	
	$userDetails					= getUserDetails($delegateId);
	$user_unique_sequence			= trim($userDetails['user_unique_sequence'],'#');
	
	$payCurrency 					= getRegistrationCurrency(getUserClassificationId($delegateId));
	
	$pendingAmountOfSlip 			= pendingAmountOfSlip($slipId);
	$invoiceAmountOfSlip 			= invoiceAmountOfSlip($slipId);
	$totalSetPaymentAmountOfSlip 	= getTotalSetPaymentAmount($slipId);
	
	if(floatval($pendingAmountOfSlip) > 0 && floatval($pendingAmountOfSlip) != floatval($invoiceAmountOfSlip))
	{
		$serviceRoundOffPrice			= $pendingAmountOfSlip;//invoiceAmountOfSlip($slipId);
	}
	else
	{
		$serviceRoundOffPrice			= invoiceAmountOfSlip($slipId);
	}
	
	$paymentGateway='ATOM';
	
	if($cardMode == 'International')
	{
		$paymentGateway='WORLDLINE';
	}
	else
	{
		$paymentGateway='ATOM';
	}
	
	//echo $paymentGateway;
	// die;

	// INSERTING PAYMENT REQUEST
	$sqlInsertPaymentRequest        = array();
	$sqlInsertPaymentRequest['QUERY']       = "INSERT INTO "._DB_PAYMENT_REQUEST_." 
										       SET `transaction_date` = ?, 
											       `delegate_id` = ?,
												   `slip_id` = ?,
												   `payment_gateway` = ?, 
												   `status` = ?, 
												   `created_ip` = ?, 
												   `created_sessionId` = ?, 
												   `created_browser` = ?,
												   `created_dateTime` = ?";	
												   
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'transaction_date', 'DATA' =>date('Y-m-d'),                'TYP' => 's');
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'delegate_id',      'DATA' =>$delegateId,                  'TYP' => 's');
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'slip_id',          'DATA' =>$slipId,                      'TYP' => 's');
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'payment_gateway',  'DATA' =>'ATOM',                       'TYP' => 's');
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'status',           'DATA' =>'A',                          'TYP' => 's');
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'created_ip',       'DATA' =>$_SERVER['REMOTE_ADDR'],      'TYP' => 's');
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'created_sessionId','DATA' =>session_id(),                 'TYP' => 's');
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'created_browser',  'DATA' =>$_SERVER['HTTP_USER_AGENT'],  'TYP' => 's');
	$sqlInsertPaymentRequest['PARAM'][]  = array('FILD' => 'created_dateTime', 'DATA' =>date('Y-m-d H:i:s'),           'TYP' => 's');
	
	$requestId = $mycms->sql_insert($sqlInsertPaymentRequest, false);
	
	
	$sqlProcessUpdateStep  = array();
	$sqlProcessUpdateStep['QUERY']          = " UPDATE  "._DB_PROCESS_STEP_."
												   SET `payment_status` = ?
												 WHERE `id` = ?";
												 
	$sqlProcessUpdateStep['PARAM'][]  = array('FILD' => 'payment_status', 'DATA' =>'COMPLETE',                                  'TYP' => 's');
	$sqlProcessUpdateStep['PARAM'][]  = array('FILD' => 'id',             'DATA' =>$mycms->getSession('PROCESS_FLOW_ID_FRONT'), 'TYP' => 's');
													 
	$mycms->sql_update($sqlProcessUpdateStep, false);
	
			
	$mycms->removeSession('PROCESS_FLOW_ID_FRONT');
	
    $transactionId                  = $cfg['invoive_number_format'].number_pad($requestId, 5).date('Ymdhi').$slipId;
	
	$returnUrl                      = _BASE_URL_."atom_payment_dr.php?HAND=".$requestId;	
	
	if(strtolower($_SERVER['HTTP_HOST'])=='localhost')
	{
		$mycms->redirect($returnUrl);
		exit();
	}
	
	if($paymentGateway=='ATOM')
	{
		$baseRequestUrl                 = $cfg['ATOM_REQUEST_URL']."";
		$aUrlPostFields                 = "&login=".$cfg['ATOM_LOGIN_ID']."";
		$aUrlPostFields                .= "&pass=".$cfg['ATOM_PASSWORD']."";
		$aUrlPostFields                .= "&ttype=NBFundTransfer";
		$aUrlPostFields                .= "&prodid=".$cfg['ATOM_PRODUCT_ID']."";
		$aUrlPostFields                .= "&amt=".$serviceRoundOffPrice."";
		$aUrlPostFields                .= "&txncurr=".$payCurrency."";
		$aUrlPostFields                .= "&txnscamt=0";
		$aUrlPostFields                .= "&clientcode=".base64_encode($user_unique_sequence)."";
		$aUrlPostFields                .= "&txnid=".$transactionId."";
		$aUrlPostFields                .= "&date=".date('d/m/Y H:i:s')."";
		$aUrlPostFields                .= "&custacc=1234567890";
		$aUrlPostFields                .= "&udf1=".$userDetails['user_full_name']."";
		$aUrlPostFields                .= "&udf2=".$userDetails['user_email_id']."";
		$aUrlPostFields                .= "&udf3=".$userDetails['user_mobile_no']."";
		$aUrlPostFields                .= "&signature=".getChecksum($cfg['ATOM_LOGIN_ID'],$cfg['ATOM_PASSWORD'],$cfg['ATOM_PRODUCT_ID'],$transactionId,$serviceRoundOffPrice,$payCurrency,$cfg['ATOM_REQ_HASH'])."";
		$aUrlPostFields                .= "&ru=".$returnUrl."";
	
		$atomRequestUrl				    = $baseRequestUrl."?".substr($aUrlPostFields,1);
		$sqlUpdatePaymentReq        	= array();
		$sqlUpdatePaymentReq['QUERY']       = "UPDATE "._DB_PAYMENT_REQUEST_." 
												 SET `transaction_id` = ?,
													 `request_url` = ?,
													 `return_url` = ? 
											   WHERE `id` = ?";
									   
		$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'transaction_id',  'DATA' =>$transactionId,  'TYP' => 's');
		$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'request_url',     'DATA' =>$atomRequestUrl, 'TYP' => 's');
		$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'return_url',      'DATA' =>$returnUrl,      'TYP' => 's');
		$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'id',              'DATA' =>$requestId,      'TYP' => 's');
		
		$mycms->sql_update($sqlUpdatePaymentReq, false);
			
		if($_SESSION['DEKHARE']=='PANN')
		{
			$baseRequestUrl                 = $cfg['TEST.ATOM_REQUEST_URL']."";
			$aUrlPostFields                 = "&login=".$cfg['TEST.ATOM_LOGIN_ID']."";
			$aUrlPostFields                .= "&pass=".$cfg['TEST.ATOM_PASSWORD']."";
			$aUrlPostFields                .= "&ttype=NBFundTransfer";
			$aUrlPostFields                .= "&prodid=".$cfg['TEST.ATOM_PRODUCT_ID']."";
			$aUrlPostFields                .= "&amt=".$serviceRoundOffPrice."";
			$aUrlPostFields                .= "&txncurr=".$payCurrency."";
			$aUrlPostFields                .= "&txnscamt=0";
			$aUrlPostFields                .= "&clientcode=".base64_encode($user_unique_sequence)."";
			$aUrlPostFields                .= "&txnid=".$transactionId."";
			$aUrlPostFields                .= "&date=".date('d/m/Y')."";
			$aUrlPostFields                .= "&custacc=1234567890";
			$aUrlPostFields                .= "&udf1=".$userDetails['user_full_name']."";
			$aUrlPostFields                .= "&udf2=".$userDetails['user_email_id']."";
			$aUrlPostFields                .= "&udf3=".$userDetails['user_mobile_no']."";
			$aUrlPostFields                .= "&signature=".getChecksum($cfg['TEST.ATOM_LOGIN_ID'],$cfg['TEST.ATOM_PASSWORD'],$cfg['TEST.ATOM_PRODUCT_ID'],$transactionId,$serviceRoundOffPrice,$payCurrency,$cfg['TEST.ATOM_REQ_HASH'])."";
			$aUrlPostFields                .= "&ru=".$returnUrl."";
		
			$atomRequestUrl				    = $baseRequestUrl."?".substr($aUrlPostFields,1);
		
			$sqlUpdatePaymentReq  = array();
			$sqlUpdatePaymentReq['QUERY']      		= "UPDATE "._DB_PAYMENT_REQUEST_." 
														  SET `transaction_id` = ?,
															  `request_url` = ?,
															  `return_url` = ?,
															  `payment_gateway` = ?
														WHERE `id` = ?";
														
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'transaction_id',  'DATA' =>$transactionId,   'TYP' => 's');
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'request_url',     'DATA' =>$atomRequestUrl,  'TYP' => 's');
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'return_url',      'DATA' =>$returnUrl,       'TYP' => 's');
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'payment_gateway', 'DATA' =>'ATOM_TEST',      'TYP' => 's');
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'id',              'DATA' =>$requestId,       'TYP' => 's');
			
			$mycms->sql_update($sqlUpdatePaymentReq, false);		
			unset($_SESSION['DEKHARE']);
			
			$logfileName = 'logs/log.atom_payment_do.'.$delegateId.'.txt';		
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: DATA :: slipId[TEST] => ".$slipId.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: DATA :: amt[TEST] => ".$serviceRoundOffPrice.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: DATA :: transactionId[TEST] => ".$transactionId.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: DATA :: atomRequestUrl[TEST] => ".$atomRequestUrl.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, "".PHP_EOL , FILE_APPEND | LOCK_EX); 	
			$mycms->redirect($atomRequestUrl);
			exit();
		}
		else
		{
			$logfileName = 'logs/log.atom_payment_do.'.$delegateId.'.txt';
			
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: DATA :: slipId => ".$slipId.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: DATA :: amt => ".$serviceRoundOffPrice.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: DATA :: transactionId => ".$transactionId.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: DATA :: atomRequestUrl => ".$atomRequestUrl.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, "".PHP_EOL , FILE_APPEND | LOCK_EX); 	
			
			header("Location: ".$atomRequestUrl);
			exit();
		}
		
		// XML RELATED OPERATION
		$MMP                        = simplexml_load_file($atomRequestUrl) ;
		if($MMP)
		{
			$proceedUrl                 = $MMP->MERCHANT[0]->RESPONSE[0]->url;
			$ttype                      = $MMP->MERCHANT[0]->RESPONSE[0]->param[0];
			$tempTxnId                  = $MMP->MERCHANT[0]->RESPONSE[0]->param[1];
			$token                      = $MMP->MERCHANT[0]->RESPONSE[0]->param[2];
			$txnStage                   = $MMP->MERCHANT[0]->RESPONSE[0]->param[3];
			$finalRedirectUrl           = $proceedUrl."?ttype=".$ttype."&tempTxnId=".$tempTxnId."&token=".$token."&txnStage=".$txnStage."";
			
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: PROCESS :: simplexml_load_file".PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: proceedUrl => ".$proceedUrl.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: ttype => ".$ttype.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: tempTxnId => ".$tempTxnId.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: token => ".$token.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: txnStage => ".$txnStage.PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: finalRedirectUrl => ".$finalRedirectUrl.PHP_EOL , FILE_APPEND | LOCK_EX);
		}
		else
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $baseRequestUrl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_PORT , 443); 
			//curl_setopt($ch, CURLOPT_SSLVERSION,3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $aUrlPostFields);
			$returnData = curl_exec($ch);
			curl_close($ch);
			
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: PROCESS :: curl".PHP_EOL , FILE_APPEND | LOCK_EX);
			file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: returnData => ".$returnData.PHP_EOL , FILE_APPEND | LOCK_EX);
			
			if(trim($returnData) == '')
			{
				$finalRedirectUrl = "online.payment.failure.php";
			}
			else
			{
				$parser = xml_parser_create('');
				xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); 
				xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
				xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
				xml_parse_into_struct($parser, trim($returnData), $xml_values);
				xml_parser_free($parser);
				
				$proceedUrl                 = $xml_values[3]['value'];
				$ttype                      = $xml_values[4]['value'];
				$tempTxnId                  = $xml_values[5]['value'];
				$token                      = $xml_values[6]['value'];
				$txnStage                   = $xml_values[7]['value'];
				$finalRedirectUrl           = $proceedUrl."?ttype=".$ttype."&tempTxnId=".$tempTxnId."&token=".$token."&txnStage=".$txnStage."";
				
				if(trim($proceedUrl)=='')
				{
					$finalRedirectUrl = "online.payment.failure.php";
				}
				else
				{	
					file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: proceedUrl => ".$proceedUrl.PHP_EOL , FILE_APPEND | LOCK_EX);
					file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: ttype => ".$ttype.PHP_EOL , FILE_APPEND | LOCK_EX);
					file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: tempTxnId => ".$tempTxnId.PHP_EOL , FILE_APPEND | LOCK_EX);
					file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: token => ".$token.PHP_EOL , FILE_APPEND | LOCK_EX);
					file_put_contents($logfileName, date("Y-m-d H:i:s.u").":: RESULT :: txnStage => ".$txnStage.PHP_EOL , FILE_APPEND | LOCK_EX);
				}
			}
		}
			
		$mycms->redirect($finalRedirectUrl);
	}	
	else if($paymentGateway=='WORLDLINE')
	{
		//include_once("PG/WorldLine/AWLMEAPI.php");
		include_once("PG/WorldLineNew/TransactionRequestBean.php");
		
		//$returnUrl = _BASE_URL_."atom_payment_dr.php?HAND=".$requestId."&FROM=WL";

		$returnUrl = _BASE_URL_."atom_payment_dr.php?HAND=".$requestId."&FROM=WL";	
		
		
		/*$obj = new AWLMEAPI();
	
		$reqMsgDTO = new ReqMsgDTO();
		$reqMsgDTO->setOrderId($transactionId);
		$reqMsgDTO->setTrnAmt(floatval($serviceRoundOffPrice)*100);
		$reqMsgDTO->setTrnCurrency($payCurrency);*/
		
		$_SESSION['DEKHARE'] = 'PANN';
		if($_SESSION['DEKHARE']=='PANN')
		{
				/*$WLREQUESTURL = $cfg['TEST.WL_REQUEST_URL'];
			
				$reqMsgDTO->setMid($cfg['TEST.WL_MERCHANT_ID']);
				
				$reqMsgDTO->setTrnRemarks("Test Transaction ");
				
				$reqMsgDTO->setMeTransReqType('S');
				
				$reqMsgDTO->setEnckey($cfg['TEST.WL_ENC_KEY']);
				
				$reqMsgDTO->setResponseUrl($returnUrl."&MODE=DEMO");
				
				$reqMsgDTO->setTrnCurrency('INR');*/

				//echo $cfg['TEST.WL_NEW_REQUEST_URL']; die;

				 $transactionRequestBean = new TransactionRequestBean();
				 //Setting all values here
			    $transactionRequestBean->merchantCode = $cfg['TEST.WL_NEW_MERCHANT_CODE'];
			    $transactionRequestBean->ITC = 'email:demo@demo.com';
			    $transactionRequestBean->customerName = 'test';
			    $transactionRequestBean->requestType = 'T';
			    $transactionRequestBean->merchantTxnRefNumber = number_pad($requestId, 5).date('Ymdhi').$slipId;
			    $transactionRequestBean->amount = '1.00';
			    $transactionRequestBean->currencyCode = 'INR';
			    $transactionRequestBean->returnURL = $returnUrl;
			    $transactionRequestBean->shoppingCartDetails = 'first_1.0_0.0';
			    $transactionRequestBean->TPSLTxnID = '';
			    $transactionRequestBean->mobileNumber = '';
			    $transactionRequestBean->txnDate = date('Y-m-d');
			    $transactionRequestBean->bankCode = '470';
			    $transactionRequestBean->custId = 'test';
			    $transactionRequestBean->key = $cfg['TEST.WL_NEW_ENC_KEY'];
			    $transactionRequestBean->iv = $cfg['TEST.WL_NEW_IV_KEY'];
			    $transactionRequestBean->accountNo = '';
			    $transactionRequestBean->webServiceLocator = $cfg['TEST.WL_NEW_REQUEST_URL'];
			    $transactionRequestBean->timeOut = '30';

		    //Writing in Request Log
		    $log  = "Name : ".$transactionRequestBean->customerName."; Date : ".date("F j, Y, g:i a")."; Request Data : ".$transactionRequestBean->merchantCode."|".$transactionRequestBean->ITC."|".$transactionRequestBean->customerName."|".$transactionRequestBean->requestType."|".$transactionRequestBean->merchantTxnRefNumber."|".$transactionRequestBean->amount."|".$transactionRequestBean->currencyCode."|".$transactionRequestBean->returnURL."|".$transactionRequestBean->shoppingCartDetails."|".$transactionRequestBean->TPSLTxnID."|".$transactionRequestBean->mobileNumber."|".$transactionRequestBean->txnDate."|".$transactionRequestBean->bankCode."|".$transactionRequestBean->custId."|".$transactionRequestBean->key."|".$transactionRequestBean->iv."|".$transactionRequestBean->accountNo."|".$transactionRequestBean->webServiceLocator.PHP_EOL;
		    
		    //Saving string to log by using "FILE_APPEND" to append.
		    file_put_contents('logs/request/log_'.date("j.n.Y").'.log', $log, FILE_APPEND);

		    $responseDetails = $transactionRequestBean->getTransactionToken();
		    $responseDetails = (array)$responseDetails;
		    $WLREQUESTURL = $responseDetails[0];

		    //echo '<pre>'; print_r($responseDetails);

		     //echo $WLREQUESTURL; die;

		     echo "<script>window.location = '" . $WLREQUESTURL . "'</script>";
    		ob_flush();



							
				$sqlUpdatePaymentReq  = array();
				$sqlUpdatePaymentReq['QUERY']      		= "UPDATE "._DB_PAYMENT_REQUEST_." 
															  SET `transaction_id` = ?,
																  `request_url` = ?,
																  `return_url` = ?,
																  `payment_gateway` = ?
															WHERE `id` = ?";
															
				$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'transaction_id',  'DATA' =>$transactionId,   				'TYP' => 's');
				$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'request_url',     'DATA' =>$WLREQUESTURL,  					'TYP' => 's');
				$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'return_url',      'DATA' =>$returnUrl."&MODE=DEMO",       	'TYP' => 's');
				$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'payment_gateway', 'DATA' =>'WL_TEST',      					'TYP' => 's');
				$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'id',              'DATA' =>$requestId,       				'TYP' => 's');
				
				$mycms->sql_update($sqlUpdatePaymentReq, false);
				
				unset($_SESSION['DEKHARE']);
		}
		else
		{	
			$WLREQUESTURL = $cfg['WL_REQUEST_URL'];
			
			// PG MID
			$reqMsgDTO->setMid($cfg['WL_MERCHANT_ID']);
			//Transaction remarks
			$reqMsgDTO->setTrnRemarks("LIVE Transaction ");
			// Merchant transaction type (S/P/R)
			$reqMsgDTO->setMeTransReqType('S');
			// Merchant encryption key
			$reqMsgDTO->setEnckey($cfg['WL_ENC_KEY']);
			// Merchant response URl
			$reqMsgDTO->setResponseUrl($returnUrl);
			
			
			$sqlUpdatePaymentReq  = array();
			$sqlUpdatePaymentReq['QUERY']      		= "UPDATE "._DB_PAYMENT_REQUEST_." 
														  SET `transaction_id` = ?,
															  `request_url` = ?,
															  `return_url` = ?,
															  `payment_gateway` = ?
														WHERE `id` = ?";
														
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'transaction_id',  'DATA' =>$transactionId,   				'TYP' => 's');
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'request_url',     'DATA' =>$WLREQUESTURL,  					'TYP' => 's');
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'return_url',      'DATA' =>$returnUrl,       				'TYP' => 's');
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'payment_gateway', 'DATA' =>'WL',      						'TYP' => 's');
			$sqlUpdatePaymentReq['PARAM'][]  = array('FILD' => 'id',              'DATA' =>$requestId,       				'TYP' => 's');
			
			$mycms->sql_update($sqlUpdatePaymentReq, false);		
		}
		
			 //Generate transaction request message
			/*$merchantRequest = "";		
			$reqMsgDTO = $obj->generateTrnReqMsg($reqMsgDTO);		
			if ($reqMsgDTO->getStatusDesc() == "Success"){
				$merchantRequest = $reqMsgDTO->getReqMsg();
			}*/
?>	
			<!-- <center>
				<form action="<?=$WLREQUESTURL?>" method="post" name="txnSubmitFrm">
					<h4 align="center">Redirecting To Payment Please Wait..</h4>
					<h4 align="center">Please Do Not Press Back Button OR Refresh Page</h4>
					<input type="hidden" size="200" name="merchantRequest" id="merchantRequest" value="<?php echo $merchantRequest; ?>"  />
					<input type="hidden" name="MID" id="MID" value="<?php echo $reqMsgDTO->getMid(); ?>"/>
					<h5 align="center">Redirectiong to Payment Gateway<br />Please Wait</h5>
					<img src="<?=_BASE_URL_?>images/PaymentPreloader.gif"/><br/>
					<h3 align="center">Please do not click 'back' or 'refresh' button or close the browser window.</h3>
					<br/>
					<hr />
				</form>
			</center> -->
			<script  type="text/javascript">
				
				window.location="<?=$WLREQUESTURL?>";
			</script>
<?
	}	
	
	exit();
	
	function getChecksum($login,$password,$productId,$transactionId,$amount,$curr="INR",$reqHashKey)
	{
        $str = $login . $password . "NBFundTransfer" . $productId . $transactionId . $amount . $curr;
       
        $signature =  hash_hmac("sha512",$str,$reqHashKey,false);
        return $signature;
    }
?>