<?php
// include_once('webmaster/section_exhibitor/includes/init.php');
// include_once('webmaster/section_exhibitor/includes/pdfcrowd.php');
include_once("includes/frontend.init.php");
include_once("includes/function.invoice.php");
include_once("includes/function.exhibitor.php");
?>
<html>

<head>
	<title>:: Invoice ::</title>
</head>

<body style="margin:0px; padding:0px;" onload="window.print()">
	<?php
	$exhibitorId   = addslashes(trim($_REQUEST['exhibitorId']));
	$invoiceId     = addslashes(trim($_REQUEST['invoice_id']));

	echo mailInvoiceContentEX($exhibitorId, $invoiceId);



	?>
</body>

</html>

<?
function mailInvoiceContentEX($exhibitorId, $invoiceId)
{
	global $cfg, $mycms;
	$contentBody     = "";

	$sqlFetchExhibitorInvoice['QUERY']       = "SELECT *
										FROM " . _DB_EXIBITOR_INVOICE_ . " 
										WHERE `status` = 'A'
										AND `exhibitor_id` = '" . $exhibitorId . "'
										AND `id` = '" . $invoiceId . "'";
	$resultExhibitorInvoice         = $mycms->sql_select($sqlFetchExhibitorInvoice);
	$rowInvoice                     = $resultExhibitorInvoice['0'];

	$sqlFetchExhibitor['QUERY']       = "SELECT exhibitor.*,
										contactPerson.exhibitor_id,
										contactPerson.contact_person_first_name,
										contactPerson.contact_person_middle_name,
										contactPerson.contact_person_last_name,
										contactPerson.contact_person_phone_no,
										contactPerson.contact_person_email_id
										
								FROM " . _DB_EXIBITOR_COMPANY_ . " exhibitor
	
								INNER JOIN " . _DB_EXIBITOR_CONTACT_PERSON_ . " contactPerson
										ON exhibitor.id = contactPerson.exhibitor_id
										WHERE exhibitor.status = 'A'
										AND exhibitor.id = '" . $exhibitorId . "'";

	$resultExhibitor         = $mycms->sql_select($sqlFetchExhibitor);
	$rowExhibitor            = $resultExhibitor['0'];

	$amount_arr = calculateExhibitorBulkRegAmount($rowExhibitor['exhibitor_company_code']);
	// echo '<pre>'; print_r($amount_arr); die;

	$cgstPrice = $rowInvoice['service_roundoff_price'] * ($cfg['INT.CGST'] / 100);
	$sgstPrice = $rowInvoice['service_roundoff_price'] * ($cfg['INT.SGST'] / 100);


	$sql    =   array();
	$sql['QUERY'] = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
	                        WHERE `status`='A' order by id desc limit 1";
	//$sql['PARAM'][]  =   array('FILD' => 'status' ,           'DATA' => 'A' ,                   'TYP' => 's');                    
	$result = $mycms->sql_select($sql);
	$row             = $result[0];

	$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['header_image'];
	if ($row['footer_image'] != '') {
		$footer_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['footer_image'];
	}
	if ($row['header_image'] != '') {
		$emailHeader  = $header_image;
	}

	$contentBody                    .= '<div style="width:790px; bottom center; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#000;">
												<table width="100%" border="0" cellpadding="0" cellspacing="0">												
													<tbody>	
													<tr>
																<td align="center" style="border-collapse:collapse;">
															<img src="' . $header_image . '" width="100%" height="40%" />
															</td>
															</tr>														
														<tr>
															<td align="center" height="400px" style="border-collapse:collapse;" valign="top">
																<table width="100%" cellpadding="1" style="font-size:13px;" border="0">
																<tr>
																	<td colspan="7" style="border:0; width:70%;">
																	<div style="color:#DA251C; font-weight:bold; padding:10px; margin-top:5px; font-size:16px; text-align:center;">
																	TAX INVOICE
																	</div>
																	</td>																	
																</tr>
																</table>
																<table width="100%" cellpadding="1" style="font-size:13px;" border="1" cellpadding="5">
																	<tr>
																		<td rowspan="2" style="width:33%;"><table width="100%" cellpadding="1" cellspacing="0" style="font-size:11px;" border="0">
																				<tr>
																					<td><b>' . $cfg['INVOICE_COMPANY_NAME'] . '</b></td>
																				</tr>
																																				
																				<tr>
																					<td>' . $cfg['INVOICE_ADDRESS'] . '</td>
																				</tr>
																				<tr>
																					<td colspan="6"> GSTN/UIN11 : ' . $cfg['GSTIN'] . '</td>
																				</tr>
																				<tr>
																					<td colspan="6"> STATE NAME : ' . $cfg['INVOICE_STATE_NAME'] . ' &nbsp;&nbsp;&nbsp; CODE : ' . $cfg['INVOICE_STATE_CODE'] . '</td>
																				</tr>
																				<tr>
																					<td colspan="6"> CONTACT : ' . $cfg['INVOICE_CONTACT'] . '</td>
																				</tr>
																				<tr>
																					<td colspan="6"> EMAIL : ' . $cfg['INVOICE_EMAIL'] . '</td>
																				</tr>
																				<tr>
																					<td colspan="6"> WEBSITE : ' . $cfg['INVOICE_WEBSITE'] . '</td>
																				</tr>
																			</table>
																		</td>
																		<td valign="top" style="width:33%;">Invoice No.<br />
																			<b>' . $rowInvoice['invoice_number'] . '</b>
																		</td>
																		<td valign="top">Dated<br />
																			<b>' . date("d/m/Y", strtotime($rowInvoice['invoice_date'])) . '</b>
																		</td>
																	</tr>
																	<tr>
																		<td valign="top">' . "Supplier's" . ' Ref.<br />
																		</td>
																		<td valign="top">Other Reference(s)<br />
																		</td>
																	</tr>																	
																	<tr>
																		<td valign="top" rowspan="5">
																		Billed To <br />
																		<b>' . $rowExhibitor['exhibitor_company_name'] . '</b>
																		<br />
																		' . $rowExhibitor['exhibitor_company_address'] . '<br />
																		GSTIN/UIN       :' . $rowExhibitor['exhibitor_company_gstin'] . '<br />
																		PAN/IT NO       :' . $rowExhibitor['exhibitor_company_pan'] . '<br />
																		CITY NAME       :' . $rowExhibitor['exhibitor_company_city'] . '<br />
																		PLACE OF SUPPLY : WEST BENGAL
																		</td>
																																			
																	</tr>
																	<tr>
																																			
																	</tr>
																	<tr>
																																			
																	</tr>
																	<tr>
																																			
																	</tr>
																	<tr>
																																																					
																	</tr>																																	
																</table><br>';
	$contentBody                    .= '					<table width="100%" style="font-size:13px;" border="1" cellpadding="5">
																	<tbody>
																	<tr>
																		<th align="center">Sl. No.</th>
																		<th align="left">Particulars</th>
																		<th align="center">HSN/SAC</th>													
																		<th align="center">Quantity</th>
																		<th align="center">Rate</th>
																		<th align="center">per</th>
																		<th align="right">Amount (INR)</th>
																	</tr>';


	if ($rowInvoice['service_type'] == 'EXHIBITOR_STALL_REGISTRATION') {

		$cgst 							 = $cfg['CONFERENCE.CGST'];
		$sgst 							 = $cfg['CONFERENCE.SGST'];
		$contentBody                    .= '
												<tr>
													<td align="center">1</td>
													<td align="left">EXHIBITOR STALL in ' . $cfg['EMAIL_CONF_NAME'] . '</td>
													<td align="center">'.$cfg['INVOICE_EXB_HSN'].'</td>	
													<td align="center">1</td>
													<td align="center"></td>
													<td align="center"></td>
													<td align="right"> ' . $rowInvoice['service_roundoff_price'] . '</td>
												</tr>';
	} else {
		if ($rowInvoice['service_type'] == 'EXHIBITOR_BULK_REGISTRATION') {
			$particulars = $rowExhibitor['bulk_reg_invoice_particular'];
		} else {
			$particulars = $rowInvoice['invoice_details'];
		}

		$cgst 							 = $cfg['CONFERENCE.CGST'];
		$sgst 							 = $cfg['CONFERENCE.SGST'];
		$contentBody                    .= '
												<tr>
													<td align="center">1</td>
													<td align="left">' . $rowInvoice['invoice_details'] . '</td>
													<td align="center">'.$cfg['INVOICE_EXB_HSN'].'</td>	
													<td align="center">' . $rowInvoice['service_consumed_quantity'] . '</td>
													<td align="center"></td>
													<td align="center"></td>
													<td align="right"> ' . number_format($rowInvoice['service_basic_price'], 2) . '</td>
												</tr>';
		if ($rowInvoice['gst_flag'] == '2') {
			$contentBody                    .= '
												<tr>
													<td align="center">2</td>
													<td align="left">IGST</td>
													<td align="center">-</td>	
													<td align="center">-</td>
													<td align="center"></td>
													<td align="center"></td>
													<td align="right"> ' . number_format(($rowInvoice['sgst_price'] * 2), 2) . '</td>
												</tr>';
		} else {

			$contentBody                    .= '
												<tr>
													<td align="center">2</td>
													<td align="left">CGST</td>
													<td align="center">-</td>	
													<td align="center">-</td>
													<td align="center"></td>
													<td align="center"></td>
													<td align="right"> ' . number_format($rowInvoice['cgst_price'], 2) . '</td>
												</tr>';
			$contentBody                    .= '
												<tr>
													<td align="center">3</td>
													<td align="left">SGST</td>
													<td align="center">-</td>	
													<td align="center">-</td>
													<td align="center"></td>
													<td align="center"></td>
													<td align="right"> ' . number_format($rowInvoice['sgst_price'], 2) . '</td>
												</tr>';
		}
	}

	$cgstPrice = $rowInvoice['cgst_price'];
	$sgstPrice = $rowInvoice['sgst_price'];


	$contentBody                    .= '<tr>
											<td colspan="6" align="right">';
	$contentBody                    .=		'<b>Total (Rounded)</b></td>
											<td align="right" ><b>' . number_format($rowInvoice['service_roundoff_price'], 2) . '</b></td>
										</tr>
										<tr>
											<td colspan="7">Amount Chargeable (in words)<br /> 
											<b><i>INR
											  ' . convert_number($amount_arr['TOTAL.AMOUNT']) . ' Only.</i></b>
											<span style="float:right;">E. & O.E</span>
											</td>
										</tr>
									</tbody>
								</table><br>';

	$contentBody           .= ' <table width="100%" style="font-size:13px;argin-top: 2%;" border="1" >
										<tbody>
											<tr>
												<td width="40%">HSN/SAC</td>
												<td align="center">Taxable Value</td>';
	if ($rowInvoice['gst_flag'] == '2') {
		$contentBody           .= '					<td align="right">IGST Rate</td>
												<td align="right">Amount</td>';
	} else {
		$contentBody           .= '					<td align="right">CGST Rate</td>
												<td align="right">Amount</td>
												<td align="right">SGST Rate</td>
												<td align="right">Amount</td>';
	}
	$contentBody           .= '					<td align="center">Total Tax Amount</td>
											</tr>
											
											<tr>
												<td>998429</td>
												<td align="right">' . $rowInvoice['service_basic_price'] . '</td>';
	if ($rowInvoice['gst_flag'] == '2') {
		$contentBody           .= '				<td align="right">' . ($cfg['INT.CGST'] + $cfg['INT.CGST']) . '%</td>
												<td align="right">' . number_format(round($cgstPrice + $sgstPrice, 0), 2) . '</td>';
	} else {
		$contentBody           .= '				<td align="right">' . (round($rowInvoice['cgst_percentage'], 0)) . '%</td>
												<td align="right">' . number_format(round($cgstPrice, 0), 2) . '</td>
												<td align="right">' . (round($rowInvoice['sgst_percentage'], 0)) . '%</td>
												<td align="right">' . number_format(round($sgstPrice, 0), 2) . '</td>';
	}
	$contentBody           .= '			<td align="right">' . number_format(round($cgstPrice + $sgstPrice, 0), 2) . '</td>
											</tr>
											<!--<tr>
												<td>Total</td>
												<td align="right">' . $rowInvoice['service_roundoff_price'] . '</td>
												<td align="right"></td>
												<td align="right">' . number_format(round($cgstPrice, 0), 2) . '</td>
												<td align="right"></td>
												<td align="right">' . number_format(round($sgstPrice, 0), 2) . '</td>
												<td align="right">' . number_format(round($cgstPrice + $sgstPrice, 0), 2) . '</td>
											</tr>-->
											<tr>
												<td>Tax Amount (in words)  :</td>
												<td colspan="6" align="right"><strong>INR ' . convert_number(round($cgstPrice + $sgstPrice, 0), 2) . ' Only.</strong></td>														
											</tr>
											<tr>
												<td colspan="7">Amount of tax subject to Reverse Charge</td>
											</tr>
										</tbody>
									</table>';

	$contentBody              .= ' <br />
								<table width="100%" style="font-size:13px;argin-top: 2%;" border="1" >	
									<tr colspan="4">
										<td width="50%"  valign="top">
																			Company\'s Pan No: ' . $cfg['PAN'] . '
																		</td>
																						
									
										<td width="50%" valign="top">
																			<table width="100%" cellpadding="1" cellspacing="0" style="font-size:11px;" border="0">
																				<tr>
																					<td colspan=2>
																						<b>Company\'s Bank Details</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="30%">
																						Bank Name:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BANKNAME'] . '</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="20%">
																						Beneficiary Name:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BENEFECIARY'] . '</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="20%">
																						A/c No.:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BANKACNO'] . '</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="20%">
																						Branch Address:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BANKBRANCH'] . '</b>
																					</td>
																				</tr>
																				<tr>
																					<td width="20%">
																						IFSC:
																					</td>
																					<td>
																						<b>' . $cfg['INVOICE_BANKIFSC'] . '</b>
																					</td>
																				</tr>
																			</table>
																		</td>											
									</tr>
									<tr>
																		<td valign="top" align="right" colspan="2">
																			<table width="40%" cellpadding="1" style="font-size:11px;" border="0">
																				<tr>
																					<td align="center"><b>' . $cfg['INVOICE_AUTORIZED_SIGNATURE_PREFIX'] . '</b></td>
																				</tr>
																				<tr>
																					<td><br></td>
																				</tr>
																				<tr>
																					<td align="center">Authorized Signatory</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td valign="bottom" align="center" colspan="2">
																			This is a Computer Generated Invoice<br>
																			Subject to Kolkata Jurisdiction
																		</td>
																	</tr>
								';

	if ($footer_image != '') {
		$contentBody    .= '							<tr >
																						<td align="center" valign="bottom" style="border-collapse:collapse;" colspan="4">
																						<img src="' . $footer_image . '" width="100%"/>
																						</td>
																					</tr></table>';
	}

	return $contentBody;
}
?>