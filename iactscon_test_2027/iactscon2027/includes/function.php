<?php



function getCountryList($selected = "")
{
	global $cfg, $mycms;
?>
	<option value="">-- Choose Country --</option>
	<?php

	$sqlFetchCountry['QUERY'] = "SELECT * FROM " . _DB_COMN_COUNTRY_ . "
							  	      WHERE `status` = ?
								   ORDER BY TRIM(country_name) ASC ";
	$sqlFetchCountry['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
	$resultCountry	= $mycms->sql_select($sqlFetchCountry);
	if ($resultCountry) {
		foreach ($resultCountry as $key => $rowCountry) {
	?>
			<option value="<?= $rowCountry['country_id'] ?>" <?= ($rowCountry['country_id'] == $selected) ? "selected" : "" ?>><?= $rowCountry['country_name'] ?></option>
	<?php
		}
	}
}

function getSateList($countryId = "", $selected = "")
{
	global $cfg, $mycms;
	?>
	<option value="">-- Choose State --</option>
	<?php

	$sqlFetchState['QUERY'] = "SELECT * FROM " . _DB_COMN_STATE_ . "
							  	    WHERE `country_id` = ?
									  AND `status` = ?
								  ORDER BY TRIM(state_name) ASC ";
	$sqlFetchState['PARAM'][]  = array('FILD' => 'country_id',  'DATA' => $countryId,  	'TYP' => 's');
	$sqlFetchState['PARAM'][]  = array('FILD' => 'status',  	'DATA' => 'A',  			'TYP' => 's');
	$resultState	= $mycms->sql_select($sqlFetchState);
	if ($resultState) {
		foreach ($resultState as $keyState => $rowState) {
	?>
			<option value="<?= $rowState['st_id'] ?>" <?= ($rowState['st_id'] == $selected) ? "selected" : "" ?>><?= $rowState['state_name'] ?></option>
	<?php
		}
	}
}

function displayDateFormat($date)
{
	global $cfg, $mycms;
	return $mycms->cDate('d/m/Y', $date);
}

function getCurrency($regClasfId = "")
{
	global $mycms;
	$mycms->kill("Depricated Function getCurrency <br>USE<br> getRegistrationCurrency");
}

function setPaymentRecord($formType)
{
	global $mycms;
	$mycms->kill("Depricated Function setPaymentRecord use setPaymentTemplate");
}

function setPaymentTemplate($formType)
{
	global $cfg, $mycms;

	?>
	<tr>
		<td align="left" colspan="4" class="thighlight">Discount</td>
	</tr>
	<tr>
		<td align="left" colspan="4" valign="top">
			<input type="checkbox" id="discount" name="discount" value="INR" operationMode="discountCheckbox" style="width: 18px; height: 18px;" /> Give Discount
		</td>
	</tr>
	<tr style="display:none;" operationMode="discount">
		<td>Discount Amount</td>
		<td valign="top" colspan="3">
			<input type="text" id="discountAmount" name="discountAmount" operationMode="discountAmount" style="width:32%;" onkeyup="recalculateOndiscount(this);" />
			<script>
				function recalculateOndiscount(obj) {
					try {
						calculationWorkshopAmount();
					} catch (e) {
						console.log('FAIL on calculationWorkshopAmount');
					}
					try {
						calculateTotalAmountNew();
					} catch (e) {
						console.log('FAIL on calculateTotalAmountNew');
					}
				}
			</script>
		</td>
	</tr>
	<tr class="thighlight">
		<td colspan="4" align="left">Payment Record</td>
	</tr>
	<tr>
		<td width="20%" align="left">Payment Mode</td>
		<td width="30%" align="left">
			<select name="payment_mode" id="payment_mode_<?= $formType ?>" style="width:90%;"
				onchange="paymentModeRetriver(this.value)" use="payment_mode">
				<option value="Cash" selected="selected">Cash</option>
				<option value="Cheque">Cheque</option>
				<option value="Draft">Draft</option>
				<option value="NEFT">NEFT</option>
				<option value="RTGS">RTGS</option>
				<option value="CARD">CARD</option>
				<option value="Credit">Exhibitor Credit</option>
				<option value="UPI">UPI</option>
			</select>
		</td>
		<td width="20%" align="left"></td>
		<td width="30%" align="left"></td>
	</tr>
	<tr>
		<td colspan="4" style="margin:0px; padding:0px;">

			<script>
				function validatePaymentTermsSubmission(form) {
					var paymode = $(form).find("select[use=payment_mode]");
					if ($(paymode).val() == '') {
						//cssAlert(paymode,'Select Payment Terms');
						alert('Select Payment Terms');
						$(paymode).focus();
						return false;
					}
					if ($(paymode).val() == 'Cash') {
						var cash_deposit_date = $(form).find("input[type=date][use=cash_deposit_date]");
						if ($(cash_deposit_date).val() == '') {
							//cssAlert(cash_deposit_date,'Select Cash Deposit Date');
							alert('Select Cash Deposit Date');
							$(cash_deposit_date).focus();
							return false;
						}
					} else if ($(paymode).val() == 'Cheque') {
						var cheque_number = $(form).find("input[type=text][use=cheque_number]");
						if ($(cheque_number).val() == '') {
							//cssAlert(cheque_number,'Enter Cheque No.');
							alert('Enter Cheque No.');
							$(cheque_number).focus();
							return false;
						}

						var cheque_drawn_bank = $(form).find("input[type=text][use=cheque_drawn_bank]");
						if ($(cheque_drawn_bank).val() == '') {
							//cssAlert(cheque_drawn_bank,'Enter Drawn Bank');
							alert('Enter Drawee Bank');
							$(cheque_drawn_bank).focus();
							return false;
						}

						var cheque_date = $(form).find("input[type=date][use=cheque_date]");
						if ($(cheque_date).val() == '') {
							//cssAlert(cheque_date,'Enter Cheque Date.');
							alert('Enter Cheque Date.');
							$(cheque_date).focus();
							return false;
						}
					} else if ($(paymode).val() == 'Draft') {
						var draft_number = $(form).find("input[type=text][use=draft_number]");
						if ($(draft_number).val() == '') {
							//cssAlert(draft_number,'Enter Draft No.');
							alert('Enter Draft No.');
							$(draft_number).focus();
							return false;
						}

						var draft_drawn_bank = $(form).find("input[type=text][use=draft_drawn_bank]");
						if ($(draft_drawn_bank).val() == '') {
							//cssAlert(draft_drawn_bank,'Enter Drawn Bank');
							alert('Enter Drawee Bank');
							$(draft_drawn_bank).focus();
							return false;
						}

						var draft_date = $(form).find("input[type=date][use=draft_date]");
						if ($(draft_date).val() == '') {
							//cssAlert(draft_date,'Enter Draft Date.');
							alert('Enter Draft Date.');
							$(draft_date).focus();
							return false;
						}
					} else if ($(paymode).val() == 'NEFT') {
						var neft_bank_name = $(form).find("input[type=text][use=neft_bank_name]");
						if ($(neft_bank_name).val() == '') {
							//cssAlert(neft_bank_name,'Enter Drawn Bank.');
							alert('Enter Drawee Bank.');
							$(neft_bank_name).focus();
							return false;
						}

						var neft_date = $(form).find("input[type=date][use=neft_date]");
						if ($(neft_date).val() == '') {
							//cssAlert(neft_date,'Enter Date.');
							alert('Enter Date.');
							$(neft_date).focus();
							return false;
						}

						var neft_transaction_no = $(form).find("input[type=text][use=neft_transaction_no]");
						if ($(neft_transaction_no).val() == '') {
							//cssAlert(neft_transaction_no,'Enter Transaction Id.');
							alert('Enter Transaction Id.');
							$(neft_transaction_no).focus();
							return false;
						}
					} else if ($(paymode).val() == 'RTGS') {
						var rtgs_bank_name = $(form).find("input[type=text][use=rtgs_bank_name]");
						if ($(rtgs_bank_name).val() == '') {
							//cssAlert(rtgs_bank_name,'Enter Drawn Bank.');
							alert('Enter Drawee Bank.');
							$(rtgs_bank_name).focus();
							return false;
						}

						var rtgs_date = $(form).find("input[type=date][use=rtgs_date]");
						if ($(rtgs_date).val() == '') {
							//cssAlert(rtgs_date,'Enter Date.');
							alert('Enter Date.');
							$(rtgs_date).focus();
							return false;
						}

						var rtgs_transaction_no = $(form).find("input[type=text][use=rtgs_transaction_no]");
						if ($(rtgs_transaction_no).val() == '') {
							//cssAlert(rtgs_transaction_no,'Enter Transaction Id.');
							alert('Enter Transaction Id.');
							$(rtgs_transaction_no).focus();
							return false;
						}
					} else if ($(paymode).val() == 'UPI') {
						var txn_no = $(form).find("input[type=text][use=txn_no]");
						if ($(txn_no).val() == '') {
							//cssAlert(rtgs_bank_name,'Enter Drawn Bank.');
							alert('Enter transaction number.');
							//$(rtgs_bank_name).focus();
							return false;
						}

						var upi_date = $(form).find("input[type=date][use=upi_date]");
						if ($(upi_date).val() == '') {
							//cssAlert(rtgs_date,'Enter Date.');
							alert('Enter Date.');
							//$(rtgs_date).focus();
							return false;
						}

						var upi_bank_name = $(form).find("input[type=text][use=upi_bank_name]");
						if ($(upi_bank_name).val() == '') {
							//cssAlert(rtgs_transaction_no,'Enter Transaction Id.');
							alert('Enter Bank name.');
							//$(rtgs_transaction_no).focus();
							return false;
						}
					}

					return true;
				}

				function paymentModeRetriver(type) {
					var paymentType = type;
					if (paymentType == "Cash") {
						$("#cashPaymentDiv").css("display", "block");
						$("#chequePaymentDiv").css("display", "none");
						$("#draftPaymentDiv").css("display", "none");
						$("#neftPaymentDiv").css("display", "none");
						$("#rtgsPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "none");
						$("#creditPaymentDiv").css("display", "none");
						$("#upiPaymentDiv").css("display", "none");
					}

					if (paymentType == "Cheque") {
						$("#cashPaymentDiv").css("display", "none");
						$("#chequePaymentDiv").css("display", "block");
						$("#draftPaymentDiv").css("display", "none");
						$("#neftPaymentDiv").css("display", "none");
						$("#rtgsPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "none");
						$("#creditPaymentDiv").css("display", "none");
						$("#upiPaymentDiv").css("display", "none");
					}

					if (paymentType == "Draft") {
						$("#cashPaymentDiv").css("display", "none");
						$("#chequePaymentDiv").css("display", "none");
						$("#draftPaymentDiv").css("display", "block");
						$("#neftPaymentDiv").css("display", "none");
						$("#rtgsPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "none");
						$("#creditPaymentDiv").css("display", "none");
						$("#upiPaymentDiv").css("display", "none");
					}


					if (paymentType == "NEFT") {
						$("#cashPaymentDiv").css("display", "none");
						$("#chequePaymentDiv").css("display", "none");
						$("#draftPaymentDiv").css("display", "none");
						$("#neftPaymentDiv").css("display", "block");
						$("#rtgsPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "none");
						$("#creditPaymentDiv").css("display", "none");
						$("#upiPaymentDiv").css("display", "none");
					}

					if (paymentType == "RTGS") {
						$("#cashPaymentDiv").css("display", "none");
						$("#chequePaymentDiv").css("display", "none");
						$("#draftPaymentDiv").css("display", "none");
						$("#neftPaymentDiv").css("display", "none");
						$("#rtgsPaymentDiv").css("display", "block");
						$("#cardPaymentDiv").css("display", "none");
						$("#creditPaymentDiv").css("display", "none");
						$("#upiPaymentDiv").css("display", "none");
					}
					if (paymentType == "CARD") {
						$("#cashPaymentDiv").css("display", "none");
						$("#chequePaymentDiv").css("display", "none");
						$("#draftPaymentDiv").css("display", "none");
						$("#neftPaymentDiv").css("display", "none");
						$("#rtgsPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "block");
						$("#creditPaymentDiv").css("display", "none");
						$("#upiPaymentDiv").css("display", "none");
					}
					if (paymentType == "Credit") {
						$("#cashPaymentDiv").css("display", "none");
						$("#chequePaymentDiv").css("display", "none");
						$("#draftPaymentDiv").css("display", "none");
						$("#neftPaymentDiv").css("display", "none");
						$("#rtgsPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "none");
						$("#creditPaymentDiv").css("display", "block");
						$("#exhibitorBalMsg").hide();
						$("#exhibitorRemainBal").hide();
						$("#exhibitorTotalRemainBalMsg").hide();
						$("#exhibitorTotalRemainBal").hide();
						$("#upiPaymentDiv").css("display", "none");

					}

					if (paymentType == "UPI") {
						$("#cashPaymentDiv").css("display", "none");
						$("#chequePaymentDiv").css("display", "none");
						$("#draftPaymentDiv").css("display", "none");
						$("#neftPaymentDiv").css("display", "none");
						$("#rtgsPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "none");
						$("#cardPaymentDiv").css("display", "none");
						$("#creditPaymentDiv").css("display", "none");
						$("#upiPaymentDiv").css("display", "block");
						$("#exhibitorBalMsg").hide();
						$("#exhibitorRemainBal").hide();
						$("#exhibitorTotalRemainBalMsg").hide();
						$("#exhibitorTotalRemainBal").hide();

					}


				}

				function creditExhibitorRemainBal(ExhibitorCode) {
					$("#exhibitorTotalRemainBal").hide();
					if (ExhibitorCode != "") {
						console.log("http://localhost/isarcon/dev/developer/webmaster/section_registration/registration.process.php?act=exhibitorBal&exhibitorCode=" + ExhibitorCode);
						$.ajax({
							type: "POST",
							url: "registration.process.php",
							data: 'act=exhibitorBal&exhibitorCode=' + ExhibitorCode,
							dataType: "json",
							async: false,
							success: function(object) {

								remain_amount = $.trim(object.remain_amount);

								$("#exhibitorTotalRemainBalMsg").show();

								var totalInvoiceAmount = $("#totalInvoiceAmount").text();

								totalBal = parseFloat(remain_amount) - parseFloat(totalInvoiceAmount);

								if (parseFloat(totalBal) <= 0) {
									$("#exhibitorTotalRemainBal").html("<b>WARNING!!!<b><br>The balance is over by Rs." + ((-1) * totalBal));
									$("#exhibitorTotalRemainBal").show();
								}
							}
						});
					} else {

						$("#exhibitorBalMsg").hide();
						$("#exhibitorRemainBal").hide();
						$("#exhibitorTotalRemainBalMsg").hide();
						$("#exhibitorTotalRemainBal").hide();
						return false;
					}


				}
			</script>
			<script>
				$(function() {
					$("input[rel=tcal]").datepicker({
						maxDate: new Date(),
						changeMonth: true,
						changeYear: true,
						yearRange: "c-100:c",
						dateFormat: "yy-mm-dd"
					});
				});
			</script>
			<div id="cashPaymentDiv">
				<table width="100%" class="noborder">
					<tr>
						<td width="20%" align="left">Date of Deposit</td>
						<td width="30%" align="left">
							<input type="date" name="cash_deposit_date" id="cash_deposit_date_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" value="<?= date('Y-m-d') ?>" use="cash_deposit_date" />
						</td>
						<td width="20%" align="left"></td>
						<td width="30%" align="left"></td>
					</tr>
				</table>
			</div>

			<div id="chequePaymentDiv" style="display:none;">
				<table width="100%" class="noborder">
					<tr>
						<td width="20%" align="left">Cheque No</td>
						<td width="30%" align="left">
							<input type="text" name="cheque_number" id="cheque_number_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use="cheque_number" />
						</td>
						<td width="20%" align="left">Drawn Bank</td>
						<td width="30%" align="left">
							<input type="text" name="cheque_drawn_bank" id="cheque_drawn_bank_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='cheque_drawn_bank' />
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">Cheque Date</td>
						<td width="30%" align="left">
							<input type="date" name="cheque_date" id="cheque_date_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='cheque_date' />
						</td>
						<td width="20%" align="left"></td>
						<td width="30%" align="left"></td>
					</tr>
				</table>
			</div>

			<div id="draftPaymentDiv" style="display:none;">
				<table width="100%" class="noborder">
					<tr>
						<td width="20%" align="left">Draft No</td>
						<td width="30%" align="left">
							<input type="text" name="draft_number" id="draft_number_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='draft_number' />
						</td>
						<td width="20%" align="left">Drawn Bank</td>
						<td width="30%" align="left">
							<input type="text" name="draft_drawn_bank" id="draft_drawn_bank_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use="draft_drawn_bank" />
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">Draft Date</td>
						<td width="30%" align="left">
							<input type="date" name="draft_date" id="draft_date_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='draft_date' />
						</td>
						<td width="20%" align="left"></td>
						<td width="30%" align="left"></td>
					</tr>
				</table>
			</div>

			<div id="neftPaymentDiv" style="display:none;">
				<table width="100%" class="noborder">
					<tr>
						<td width="20%" align="left">Drawn Bank</td>
						<td width="30%" align="left">
							<input type="text" name="neft_bank_name" id="neft_bank_name_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='neft_bank_name' />
						</td>
						<td width="20%" align="left">Date</td>
						<td width="30%" align="left">
							<input type="date" name="neft_date" id="neft_date_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='neft_date' />
						</td>
					</tr>
					<tr>
						<td align="left">Transaction Id</td>
						<td align="left">
							<input type="text" name="neft_transaction_no" id="neft_transaction_no_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='neft_transaction_no' />
						</td>
						<td align="left"></td>
						<td align="left"></td>
					</tr>
				</table>
			</div>

			<div id="rtgsPaymentDiv" style="display:none;">
				<table width="100%" class="noborder">
					<tr>
						<td width="20%" align="left">Drawn Bank</td>
						<td width="30%" align="left">
							<input type="text" name="rtgs_bank_name" id="rtgs_bank_name_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='rtgs_bank_name' />
						</td>
						<td width="20%" align="left">Date</td>
						<td width="30%" align="left">
							<input type="date" name="rtgs_date" id="rtgs_date_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='rtgs_date' />
						</td>
					</tr>
					<tr>
						<td align="left">Transaction Id</td>
						<td align="left">
							<input type="text" name="rtgs_transaction_no" id="rtgs_transaction_no_<?= $formType ?>"
								style="width:90%; text-transform:uppercase;" use='rtgs_transaction_no' />
						</td>
						<td align="left"></td>
						<td align="left"></td>
					</tr>
				</table>
			</div>

			<div id="cardPaymentDiv" style="display:none;">
				<table width="100%" class="noborder">
					<tr>
						<td width="20%" align="left">Card Number <!--<span class="mandatory">*</span>--></td>
						<td width="30%" align="left">
							<input type="text" name="card_number" id="card_number"
								style="width:90%; text-transform:uppercase;" use='card_number' />
						</td>
						<td align="left">Remarks</td>
						<td align="left">
							<input type="text" name="remarks" id="remarks"
								style="width:90%; text-transform:uppercase;" use='remarks' />
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">Date <span class="mandatory">*</span></td>
						<td width="30%" align="left">
							<input type="date" name="card_date" id="card_date"
								style="width:90%; text-transform:uppercase;" use='card_date' />
						</td>
						<td align="left"></td>
						<td align="left"></td>
					</tr>
				</table>
			</div>
			<div id="upiPaymentDiv" style="display:none;">
				<table width="100%" class="noborder">
					<tr>
						<td width="20%" align="left">UPI Transaction No <span class="mandatory">*</span></td>
						<td width="30%" align="left">
							<input type="text" name="txn_no[]" id="txn_no"
								style="width:90%; text-transform:uppercase;" use="txn_no" />
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">UPI Bank Name<span class="mandatory">*</span></td>
						<td width="30%" align="left">
							<input type="text" name="upi_bank_name[]" id="upi_bank_name"
								style="width:90%; text-transform:uppercase;" use="upi_bank_name" />
						</td>
					</tr>
					<tr>
						<td width="20%" align="left">UPI Date <span class="mandatory">*</span></td>
						<td width="30%" align="left">
							<input type="date" name="upi_date[]" id="upi_date"
								style="width:90%; text-transform:uppercase;" use='upi_date' />
						</td>
						<td width="20%" align="left"></td>
						<td width="30%" align="left"></td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
<?php
}

function getNoticeDetails($heading)
{
	global $cfg, $mycms;

	$totalAccompanyCount  = 0;
	$sqlFetch 	= array();
	$sqlFetch['QUERY']	 = "SELECT `content`
								  FROM " . _DB_TERMS_CONDITION_ . "
								 WHERE status = ?
								   AND heading = ? ";
	$sqlFetch['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'A',  	  'TYP' => 's');
	$sqlFetch['PARAM'][]   = array('FILD' => 'heading', 'DATA' => $heading,  'TYP' => 's');
	$result			      = $mycms->sql_select($sqlFetch);
	return $result[0]['content'];
}

function insertRawData($tableName, $data)
{
	global $cfg, $mycms;

	$insertString 	= "";
	foreach ($data as $columnName => $value) {
		$insertString 	.= "`" . $columnName . "`  = '" . addslashes(trim($value)) . "',";
	}

	$sqlInsert['QUERY']	       = "INSERT INTO " . $tableName . " 
									  SET " . $insertString . "
									 	  `status` = 'A'";
	$lastInsertedId = $mycms->sql_insert($sqlInsert, false);
	return $lastInsertedId;
}



function additionalPaymentConfirmationProcess($paymentId)
{
	global $cfg, $mycms;


	$slipId 			  = $_REQUEST['slip_id'];
	$paymentRemark 	 	  = $_REQUEST['remarks'];
	$paymentDate 		  = date('Y-m-d');
	$delegateId 		  = $_REQUEST['delegate_id'];
	$totalPaySlipAmt      = invoiceAmountOfSlip($slipId);
	$exhibitorCode 	 	  = $_REQUEST['exhibitor_name'];

	$sqlDelegateInfo = array();
	$sqlDelegateInfo['QUERY']	= "SELECT `user_type`,`user_full_name` FROM " . _DB_USER_REGISTRATION_ . " 
								WHERE `id` = '" . $delegateId . "' ";

	$delegateInfo		= $mycms->sql_select($sqlDelegateInfo, false);

	$paidAmmount		  = invoiceAmountOfSlip($slipId);

	$sqlUpdatePayment = array();
	$sqlUpdatePayment['QUERY']	  = "UPDATE " . _DB_PAYMENT_ . "
										SET `payment_date` = '" . $paymentDate . "',
											`payment_remark` = '" . $paymentRemark . "',
											`amount` = '" . $paidAmmount . "',
											`payment_status` = 'PAID'
									  WHERE `id` = '" . $paymentId . "'";

	$mycms->sql_update($sqlUpdatePayment, false);

	if (floatval($totalPaySlipAmt) > 0) {
		$sqlUpdatePayment = array();
		$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_SLIP_ . "
										SET `payment_status` = 'PAID'
									  WHERE `id` = '" . $slipId . "'";

		$mycms->sql_update($sqlUpdateSlip, false);
	}

	if ($exhibitorCode != "") {
		$sqlInsertExhibitorUser = array();
		$sqlInsertExhibitorUser['QUERY']		= "INSERT INTO " . _DB_EXIBITOR_COMPANY_USERS_ . "
																SET `exhibitor_company_code` = '" . $exhibitorCode . "',
																	`exhibitor_company_name` = '" . $exhibitorInfo[0]['exhibitor_company_name'] . "',
																					`amount` = '" . $paidAmmount . "',
																			   `delegate_id` = '" . $delegateId . "',
																			`payment_status` = 'PAID',
																					`status` = 'A', 
																				 `user_type` = '" . $delegateInfo[0]['user_type'] . "',
																			`user_full_name` = '" . $delegateInfo[0]['user_full_name'] . "',
																			   `slip_number` = '" . $slipId . "' ";

		$mycms->sql_insert($sqlInsertExhibitorUser, false);
	}

	$activeInvoice = invoiceDetailsActiveOfSlip($slipId);
	foreach ($activeInvoice as $keyActiveInvoice => $valActiveInvoice) {
		if ($valActiveInvoice['payment_status'] == 'UNPAID') {
			if ($valActiveInvoice['service_type'] == 'DELEGATE_CONFERENCE_REGISTRATION') {
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

				$mycms->sql_update($sqlUpdateSlip, false);
			}
			if ($valActiveInvoice['service_type'] == 'DELEGATE_RESIDENTIAL_REGISTRATION') {
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

				$mycms->sql_update($sqlUpdateSlip, false);

				$sqlUpdateWorkshop = array();
				$sqlUpdateWorkshop['QUERY']	    = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														  SET `payment_status` = 'PAID'
													    WHERE `delegate_id` = '" . $valActiveInvoice['delegate_id'] . "'
														  AND `status`='A'";

				$mycms->sql_update($sqlUpdateWorkshop, false);

				$sqlUpdateAccom = array();
				$sqlUpdateAccom['QUERY']	      = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
															SET `payment_status` = 'PAID'
														  WHERE `user_id` = '" . $valActiveInvoice['delegate_id'] . "'
															AND `status`='A'";

				$mycms->sql_update($sqlUpdateAccom, false);
			}
			if ($valActiveInvoice['service_type'] == 'DELEGATE_WORKSHOP_REGISTRATION') {
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_REQUEST_WORKSHOP_ . "
														SET `payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

				$mycms->sql_update($sqlUpdateSlip, false);

				$sqlUpdate = array();
				$sqlUpdate['QUERY']		      = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `workshop_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

				$mycms->sql_update($sqlUpdate, false);
			}
			if ($valActiveInvoice['service_type'] == 'DELEGATE_DINNER_REQUEST') {
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_REQUEST_DINNER_ . "
														SET `payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

				$mycms->sql_update($sqlUpdateSlip, false);
			}
			if ($valActiveInvoice['service_type'] == 'ACCOMPANY_CONFERENCE_REGISTRATION') {
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `registration_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

				$mycms->sql_update($sqlUpdateSlip, false);
			}
			if ($valActiveInvoice['service_type'] == 'DELEGATE_ACCOMMODATION_REQUEST') {
				$sqlUpdateSlip = array();
				$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_REQUEST_ACCOMMODATION_ . "
														SET `payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['refference_id'] . "'";

				$mycms->sql_update($sqlUpdateSlip, false);

				$sqlUpdate = array();
				$sqlUpdate['QUERY']		      = "UPDATE " . _DB_USER_REGISTRATION_ . "
														SET `accommodation_payment_status` = 'PAID'
													  WHERE `id` = '" . $valActiveInvoice['delegate_id'] . "'";

				$mycms->sql_update($sqlUpdate, false);
			}

			$sqlUpdateSlip = array();
			$sqlUpdateSlip['QUERY']	      = "UPDATE " . _DB_INVOICE_ . "
													SET `payment_status` = 'PAID'
												  WHERE `id` = '" . $valActiveInvoice['id'] . "'";

			$mycms->sql_update($sqlUpdateSlip, false);
		}
	}
	//offline_conference_payment_confirmation_message($delegateId,$slipId , $paymentId, 'SEND');

	$userDetails = getUserDetails($delegateId);

	if ($userDetails['registration_request'] == 'EXHIBITOR') {
		offline_exhibitor_registration_confirmation_message($delegateId, $paymentId, $slipId, 'SEND', 'BACK');
	} else {
		offline_conference_registration_confirmation_message($delegateId, $paymentId, $slipId, 'SEND', 'BACK', 'EXHIBITOR');
	}
}

function getExhibitorName($exhibitorId)
{
	global $cfg, $mycms;
	$sqlFetchExibitor['QUERY']       = "SELECT *
		                             FROM " . _DB_EXIBITOR_COMPANY_ . "
									WHERE `id` = '" . $exhibitorId . "'";
	$resExibitor = $mycms->sql_select($sqlFetchExibitor);
	return $resExibitor[0]['exhibitor_company_name'];
}

function getlogedUserRecivedAmount()
{
	global $cfg, $mycms;
	$loggedUserId			  = $mycms->getLoggedUserId();
	$dateconference['QUERY'] = "SELECT * FROM " . _DB_CONFERENCE_DATE_ . " WHERE status = 'A'";
	$rowdateDetails = $mycms->sql_select($dateconference);

	foreach ($rowdateDetails as $key => $rowfetchDetails) {
		$returnArray[$rowfetchDetails['conf_date']]['INVOICEDAMT']	= 0;
		$returnArray[$rowfetchDetails['conf_date']]['CASH']	= 0;
		$returnArray[$rowfetchDetails['conf_date']]['CARD'] =  0;
		$returnArray[$rowfetchDetails['conf_date']]['CHEQUE'] =  0;
		$returnArray[$rowfetchDetails['conf_date']]['NEFT'] =  0;
		$returnArray[$rowfetchDetails['conf_date']]['RTGS'] =  0;
		$returnArray[$rowfetchDetails['conf_date']]['TOTAL'] =  0;

		$returnArray[$rowfetchDetails['conf_date']]['DISCOUNT'] =  0;


		$sqlPaymentList['QUERY']  = "SELECT * 
								  FROM " . _DB_PAYMENT_ . " pay
							   INNER JOIN " . _DB_USER_REGISTRATION_ . " user
								    ON pay.delegate_id = user.id
								 WHERE pay.status = 'A' 
								   AND user.status = 'A'
								   AND DATE( pay.created_dateTime)='" . $rowfetchDetails['conf_date'] . "' 
								   AND pay.collected_by = '" . $loggedUserId . "'";
		$resPaymentList = $mycms->sql_select($sqlPaymentList);

		foreach ($resPaymentList as $key => $rowPaymentList) {
			$returnArray[$rowfetchDetails['conf_date']]['INVOICEDAMT'] =  invoiceAmountOfSlip($rowPaymentList['slip_id'], false, true);
			$finalPaybleAmount = $rowPaymentList['amount'];
			if ($finalPaybleAmount == '') {
				$finalPaybleAmount = 0;
			}

			$discountAmount = discountAmountOfSlip($rowPaymentList['slip_id']);
			if ($discountAmount == '') {
				$discountAmount = 0;
			}

			if ($rowPaymentList['payment_mode'] == 'Cash') {
				$returnArray[$rowfetchDetails['conf_date']]['CASH']	+= $finalPaybleAmount;
				$returnArray[$rowfetchDetails['conf_date']]['TOTAL'] += $finalPaybleAmount;
			}
			if ($rowPaymentList['payment_mode'] == 'Cheque') {
				$returnArray[$rowfetchDetails['conf_date']]['CHEQUE']	+= $finalPaybleAmount;
				$returnArray[$rowfetchDetails['conf_date']]['TOTAL'] += $finalPaybleAmount;
			}
			if ($rowPaymentList['payment_mode'] == 'Card') {
				$returnArray[$rowfetchDetails['conf_date']]['CARD']	+= $finalPaybleAmount;
				$returnArray[$rowfetchDetails['conf_date']]['TOTAL'] += $finalPaybleAmount;
			}
			if ($rowPaymentList['payment_mode'] == 'NEFT') {
				$returnArray[$rowfetchDetails['conf_date']]['RTGS']	+= $finalPaybleAmount;
				$returnArray[$rowfetchDetails['conf_date']]['TOTAL'] += $finalPaybleAmount;
			}
			if ($rowPaymentList['payment_mode'] == 'RTGS') {
				$returnArray[$rowfetchDetails['conf_date']]['NEFT']	+= $finalPaybleAmount;
				$returnArray[$rowfetchDetails['conf_date']]['TOTAL'] += $finalPaybleAmount;
			}


			$returnArray[$rowfetchDetails['conf_date']]['DISCOUNT']	+= $discountAmount;
		}
	}
	return $returnArray;
}

function getTotalCaseCount($delegatesId)
{
	global $cfg, $mycms;
	$sqlFetch			  =	array();
	$sqlFetch['QUERY']	  = " SELECT count(id) AS totalCase
									FROM " . _DB_ABSTRACT_REQUEST_ . " 
							   	   WHERE status = ?
								  	 AND tags = ?
								 	 AND applicant_id = ?";

	$sqlFetch['PARAM'][]   = array('FILD' => 'status',  	   'DATA' => 'A',  'TYP' => 's');
	$sqlFetch['PARAM'][]   = array('FILD' => 'tags',  	  	   'DATA' => 'Case report',  'TYP' => 's');
	$sqlFetch['PARAM'][]   = array('FILD' => 'applicant_id',  'DATA' => $delegatesId,  'TYP' => 's');


	$result			      		= $mycms->sql_select($sqlFetch);
	return $result[0]['totalCase'];
}

function registrationDetailsQuerySet($delegateId = "", $searchCondition = "", $orderCondition = '')
{
	global $cfg, $mycms;

	//$sqlBigJoin                = "SET OPTION SQL_BIG_SELECTS = 1";
	//mysql_query($sqlBigJoin);

	$filterCondition           = "";

	if ($delegateId != "") {
		$filterCondition      .= " AND delegate.id = '" . $delegateId . "'";
	}

	$sqlDelegateQueryset = array();
	$sqlDelegateQueryset['QUERY']       = "SELECT delegate.*,
		                                     
											 country.country_name,
											 state.state_name,
											 
											 tariffCutoff.cutoff_title AS cutoffTitle,
											 registrationClassification.classification_title
											 
					 
										FROM " . _DB_USER_REGISTRATION_ . " delegate 
										
							 LEFT OUTER JOIN " . _DB_REGISTRATION_CLASSIFICATION_ . " AS registrationClassification
									      ON delegate.registration_classification_id = registrationClassification.id
										 
							 LEFT OUTER JOIN " . _DB_TARIFF_CUTOFF_ . " AS tariffCutoff
								          ON delegate.registration_tariff_cutoff_id = tariffCutoff.id
							 
							 LEFT OUTER JOIN " . _DB_COMN_COUNTRY_ . " country
										  ON delegate.user_country_id = country.country_id
												 
							 LEFT OUTER JOIN " . _DB_COMN_STATE_ . " state
										  ON delegate.user_state_id = state.st_id
							 
									   WHERE delegate.user_type = ?
										AND delegate.status != ? " . $filterCondition . " " . $searchCondition . " ";

	$sqlDelegateQueryset['PARAM'][]  = array('FILD' => 'delegate.user_type',  'DATA' => 'DELEGATE',  'TYP' => 's');
	$sqlDelegateQueryset['PARAM'][]  = array('FILD' => 'delegate.status',  'DATA' => 'D',  'TYP' => 's');


	if (trim($orderCondition) != '') {
		$sqlDelegateQueryset['QUERY']  .=  " " . $orderCondition . " ";
	} else {
		$sqlDelegateQueryset['QUERY']  .=  " ORDER BY delegate.id DESC";
	}

	return $sqlDelegateQueryset;
}

function  login_session_control($willRedirect = true, $checkPayment = true, $redirect = "")
{
	global $cfg, $mycms;
	$redirect = _BASE_URL_;



	if ($mycms->isSession('LOGGED.USER.ID')) {
		$delegateId   		  =	$mycms->getSession('LOGGED.USER.ID');
		$rowFetchUserDetails  = getUserDetails($delegateId, true);

		$sqlFetchAbs               = array();
		$sqlFetchAbs['QUERY']       = "SELECT * 
											 FROM " . _DB_ABSTRACT_REQUEST_ . "
											WHERE `applicant_id` = ? 
											  AND `status` = ?";

		$sqlFetchAbs['PARAM'][]   = array('FILD' => 'applicant_id', 'DATA' => $delegateId, 'TYP' => 's');
		$sqlFetchAbs['PARAM'][]   = array('FILD' => 'status',        'DATA' => 'A',    'TYP' => 's');

		$resultFetchAbs    		= $mycms->sql_select($sqlFetchAbs);
		if ($resultFetchAbs) {
			// If the user has an abstract request, we consider it as in use
			$isAbstract = 'Y';
		} else {
			$isAbstract = 'Y';
		}
	}

	if ($mycms->getSession('LOGIN.TYPE') == 'ABSTRACT') {
		return array("DELEGATE_ID" => $delegateId, "USERDETAIL" => $rowFetchUserDetails);
	} elseif ($checkPayment && ($rowFetchUserDetails['registration_payment_status'] == 'PAID' || $rowFetchUserDetails['registration_payment_status'] == 'COMPLIMENTARY'
		|| $rowFetchUserDetails['registration_payment_status'] == 'ZERO_VALUE' || $isAbstract == 'Y')) {
		return array("DELEGATE_ID" => $delegateId, "USERDETAIL" => $rowFetchUserDetails);
	} elseif ($checkPayment && $rowFetchUserDetails['registration_request'] == 'ABSTRACT') {
		return array("DELEGATE_ID" => $delegateId, "USERDETAIL" => $rowFetchUserDetails);
	} elseif (!$checkPayment && $delegateId != '') {
		return array("DELEGATE_ID" => $delegateId, "USERDETAIL" => $rowFetchUserDetails);
	}
	 elseif ($willRedirect) {
		// echo $rowFetchUserDetails['registration_payment_status'];die;
		$mycms->redirect($redirect);
	} else {
		return array("DELEGATE_ID" => '');
	}
}
?>