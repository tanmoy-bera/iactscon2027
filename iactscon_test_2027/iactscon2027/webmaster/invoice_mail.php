<?php 
include_once("includes/source.php");
include_once('includes/init.php');
include_once("../../includes/function.registration.php");
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.invoice.php');
include_once('../../includes/function.workshop.php');
include_once('../../includes/function.dinner.php');
include_once('../../includes/function.accompany.php');
include_once('../../includes/function.accommodation.php');
include_once('../../includes/function.abstract.php');
include_once('includes/function.php');
// $cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
$delegateId 	=  $_REQUEST['id'];
$loggedUserId	= $mycms->getLoggedUserId();
$rowFetchUser   = getUserDetails($delegateId);
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Registration</a></li>
                        <li class="breadcrumb-item">Registration List</li>
                        <li class="breadcrumb-item active" aria-current="page">Invoice & Mail</li>
                    </ol>
                </nav>
                <h2>Invoice & Mail</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
            <div class="page_top_wrap_right">
                <a href="registration.php" class="badge_danger"><i class="fal fa-arrow-left"></i>Back</a>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="tracking_analytic_tab">
                <button data-tab="kit" class="active">Payment Voucher </button>
                <button data-tab="breakfast">User Invoice</button>
            </div>
        </div>

        <div class="filter_wrap mb-3">
            <h4 class="filter_heading"><span>Advanced Filtering</span><a class="close_filter" onclick="$('.filter_wrap').slideUp();"><?php close(); ?></a></h4>
            <div class="filter_body">
                <div>
                    <label>Registration Type</label>
                    <select>
                        <option>All Types</option>
                    </select>
                </div>
                <div>
                    <label>Payment Status</label>
                    <select>
                        <option>All Status</option>
                    </select>
                </div>
                <div>
                    <label>Registration Date</label>
                    <input type="date">
                </div>
            </div>
            <div class="filter_bottom">
                <button><?php reseti(); ?></button>
                <button type="submit">Apply</button>
            </div>
        </div>

        <div class="workshop_overview_wrap mb-3">
            <h2 class="sub_head">User Profile</h2>
            <ul>
                <li>Name<span><?= strtoupper($rowFetchUser['user_title']) ?>
								<?= strtoupper($rowFetchUser['user_first_name']) ?>
								<?= strtoupper($rowFetchUser['user_middle_name']) ?>
								<?= strtoupper($rowFetchUser['user_last_name']) ?></span></li>
                <li>Registration Type<span><?= $rowFetchUser['registration_request']?></span></li>
                <li>Registration Id<span><?php
								if ($rowFetchUser['registration_payment_status'] != "UNPAID") {
									echo $rowFetchUser['user_registration_id'];
								} else {
									echo "-";
								}
								?></span></li>
                <li>Unique Sequence<span><?=$rowFetchUser['user_unique_sequence']?></span></li>
                <li>Mobile<span><?= $rowFetchUser['user_mobile_isd_code'] . " - " . $rowFetchUser['user_mobile_no'] ?></span></li>
                <li>Email Id<span><?= $rowFetchUser['user_email_id'] ?></span></li>
                <li>Registration Mode<span><?= strtoupper($rowFetchUser['registration_mode']) ?></span></li>
                <li>Registration Date<span><?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?></span></li>
            </ul>
        </div>

        <div class="tracking_analytic_box active" id="kit">
            <div class="spot_listing">
            <?php
                $paymentCounter   = 0;
                $resFetchSlip	  = slipDetailsOfUser($delegateId, true);
                //print_r($resFetchSlip);
                if ($resFetchSlip) {
                    foreach ($resFetchSlip as $key => $rowFetchSlip) {
                        //echo '<pre>'; print_r($rowFetchSlip);
                        $counter++;
                        $resPaymentDetails      = paymentDetails($rowFetchSlip['id']);
                        //print_r($resPaymentDetails);
                        $paymentDescription     = "-";
                        if ($key == 0) {
                            $paymentId = $resPaymentDetails['id'];
                            $slipId    = $rowFetchSlip['id'];
                        }
                        $isChange 		= "YES";
                        $excludedAmount = invoiceAmountOfSlip($rowFetchSlip['id'], false, false);

                        $amount 		= invoiceAmountOfSlip($rowFetchSlip['id']);
                        $discountDeductionAmount = ($excludedAmount - $amount);
                        //$discountAmountofSlip= ($discountDeductionAmount/1.18);
                        foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
                            if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
                                $discountAmountofSlip = $discountDeductionAmount;
                            } else {
                                $discountAmountofSlip = ($discountDeductionAmount / 1.18);
                            }
                        }

                        $DiscountAmount = invoiceDiscountAmountOfSlip($rowFetchSlip['id'], true);
                    ?>
                    <div class="spot_box">
                        <div class="spot_box_top">
                            <div class="spot_name">
                                <div class="regi_name">PV No <?= $rowFetchSlip['slip_number'] ?></div>
                                <div class="regi_contact">
                                    <span>
                                        <?php call(); ?>Mode <?= $rowFetchSlip['invoice_mode'] ?>
                                    </span>
                                    <span>
                                        <?php email(); ?>Date <?= setDateTimeFormat2($rowFetchSlip['slip_date'], "D") ?>
                                    </span>
                                </div>
                            </div>
                            <div class="spot_details">
                                <div class="spot_details_box">
                                    <h5>PV Amount</h5>
                                    <h6></h6>
                                    <small>Discount: <?= ($DiscountAmount > 0) ? ($rowFetchSlip['currency'] . '&nbsp;' . number_format($DiscountAmount, 2)) : '-' ?></small>
                                </div>
                                <div class="spot_details_box">
                                    <h5>Total Amount</h5>
                                    <h6><?= $rowFetchSlip['currency'] . '&nbsp;' . number_format($amount, 2) ?></h6>
                                </div>
                                <div class="spot_details_box">
                                    <h5>Paid Amount</h5>
                                    <h6><?
										if ($resPaymentDetails['totalAmountPaid'] > 0) {
											echo $rowFetchSlip['currency'] . '&nbsp;' . number_format($resPaymentDetails['totalAmountPaid'], 2);
											// echo $rowFetchSlip['currency'] . '&nbsp;' . number_format($amount, 2);
										} else {
											echo $rowFetchSlip['currency'] . '&nbsp;' . "0.00";
										}
										?></h6>

                                </div>
                                <div class="spot_details_box align-items-end">
                                    <h5>Slip Created</h5>
                                    <?
                                       $historyOfSlip = historyOfslip($rowFetchSlip['id']);
                                       // Remove the text part
                                       if ($historyOfSlip) {
                                            foreach ($historyOfSlip as $key => $value) {

                                                // Remove the text part
                                                $cleanDateTime = str_replace('Slip Created On ', '', $value);

                                                // Create DateTime object
                                                $dt = DateTime::createFromFormat('d/m/Y h:i:s A', trim($cleanDateTime));

                                                if ($dt) {
                                                    $onlyDate = $dt->format('d/m/Y');
                                                    $onlyTime = $dt->format('h:i:s A');
                                                }
                                            }
                                        }
                                    ?>
                                    <h6><?php calendar(); ?><?= $onlyDate?></h6>
                                    <h6><?php clock() ?><?= $onlyTime?></h6>
                                
                                </div>
                            </div>
                        </div>
                        <?

                        foreach ($resPaymentDetails['paymentDetails'] as $key => $rowPayment) {

                            $paymentCounter++;
                            $lastPaymentStatus = $rowPayment['payment_status'];
                             
                            // Start box
                            $paymentDescription = '<div class="pv_box_mid badge_success">';

                            switch ($rowPayment['payment_mode']) {
                                 case 'Cash':
                                    $paymentDescription .= '<small>Paid by Cash.</small>';
                                    $paymentDescription .= '<small>Date of Deposit: ' .
                                        setDateTimeFormat2($rowPayment['cash_deposit_date'], "D") . '</small>';

                                    if (!empty($rowPayment['cash_document'])) {
                                        $paymentDescription .= '<small>
                                            Document:
                                            <a href="' . _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . $rowPayment['cash_document'] . '" target="_blank">View</a> |
                                            <a href="' . _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . $rowPayment['cash_document'] . '" download>Download</a>
                                        </small>';
                                    }
                                    break;

                                case 'Online':
                                    $paymentDescription .= '<small>Paid by Online.</small>';
                                    $paymentDescription .= '<small>Date of Payment: ' .
                                        setDateTimeFormat2($rowPayment['payment_date'], "D") . '</small>';

                                    if (!empty($rowPayment['atom_atom_transaction_id'])) {
                                        $paymentDescription .= '<small>Transaction Number: ' .
                                            $rowPayment['atom_atom_transaction_id'] . '</small>';
                                    }

                                    if (!empty($rowPayment['atom_bank_transaction_id'])) {
                                        $paymentDescription .= '<small>Bank Transaction Number: ' .
                                            $rowPayment['atom_bank_transaction_id'] . '</small>';
                                    }
                                    break;

                                case 'Card':
                                    $paymentDescription .= '<small>Paid by Card.</small>';
                                    $paymentDescription .= '<small>Reference Number: ' .
                                        $rowPayment['card_transaction_no'] . '</small>';
                                    $paymentDescription .= '<small>Date of Payment: ' .
                                        setDateTimeFormat2($rowPayment['card_payment_date'], "D") . '</small>';
                                    break;

                                case 'Draft':
                                    $paymentDescription .= '<small>Paid by Draft.</small>';
                                    $paymentDescription .= '<small>Draft Number: ' .
                                        $rowPayment['draft_number'] . '</small>';
                                    $paymentDescription .= '<small>Draft Date: ' .
                                        setDateTimeFormat2($rowPayment['draft_date'], "D") . '</small>';
                                    $paymentDescription .= '<small>Bank: ' .
                                        $rowPayment['draft_bank_name'] . '</small>';
                                    break;

                                case 'NEFT':
                                    $paymentDescription .= '<small>Paid by NEFT/UPI.</small>';
                                    $paymentDescription .= '<small>Transaction Number: ' .
                                        $rowPayment['neft_transaction_no'] . '</small>';
                                    $paymentDescription .= '<small>Transaction Date: ' .
                                        setDateTimeFormat2($rowPayment['neft_date'], "D") . '</small>';
                                    $paymentDescription .= '<small>Bank: ' .
                                        $rowPayment['neft_bank_name'] . '</small>';

                                    if (!empty($rowPayment['neft_document'])) {
                                        $paymentDescription .= '<small>
                                            Document:
                                            <a href="' . _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . $rowPayment['neft_document'] . '" target="_blank">View</a> |
                                            <a href="' . _BASE_URL_ . $cfg['FILES.ABSTRACT.REQUEST'] . $rowPayment['neft_document'] . '" download>Download</a>
                                        </small>';
                                    }
                                    break;

                                case 'RTGS':
                                    $paymentDescription .= '<small>Paid by RTGS.</small>';
                                    $paymentDescription .= '<small>Transaction Number: ' .
                                        $rowPayment['rtgs_transaction_no'] . '</small>';
                                    $paymentDescription .= '<small>Transaction Date: ' .
                                        setDateTimeFormat2($rowPayment['rtgs_date'], "D") . '</small>';
                                    $paymentDescription .= '<small>Bank: ' .
                                        $rowPayment['rtgs_bank_name'] . '</small>';
                                    break;

                                case 'Cheque':
                                    $paymentDescription .= '<small>Paid by Cheque.</small>';
                                    $paymentDescription .= '<small>Cheque Number: ' .
                                        $rowPayment['cheque_number'] . '</small>';
                                    $paymentDescription .= '<small>Cheque Date: ' .
                                        setDateTimeFormat2($rowPayment['cheque_date'], "D") . '</small>';
                                    $paymentDescription .= '<small>Bank: ' .
                                        $rowPayment['cheque_bank_name'] . '</small>';
                                    break;

                                case 'UPI':
                                    $paymentDescription .= '<small>Paid by UPI.</small>';

                                    if (!empty($rowPayment['txn_no'])) {
                                        $paymentDescription .= '<small>Transaction Number: ' .
                                            $rowPayment['txn_no'] . '</small>';
                                    }

                                    if (!empty($rowPayment['upi_transaction_number'])) {
                                        $paymentDescription .= '<small>Transaction Number: ' .
                                            $rowPayment['upi_transaction_number'] . '</small>';
                                    }

                                    if (!empty($rowPayment['upi_bank_name'])) {
                                        $paymentDescription .= '<small>Bank: ' .
                                            $rowPayment['upi_bank_name'] . '</small>';
                                    }

                                    if (!empty($rowPayment['upi_date'])) {
                                        $paymentDescription .= '<small>Date: ' .
                                            setDateTimeFormat2($rowPayment['upi_date'], "D") . '</small>';
                                    }
                                    break;
                            }

                            if (!empty($rowPayment['payment_remark'])) {
                                $paymentDescription .= '<small>Remark: ' .
                                    $rowPayment['payment_remark'] . '</small>';
                            }

                            $paymentDescription .= '</div>';

                            // OUTPUT
                            echo $paymentDescription;
                        }
                       if($rowFetchSlip['invoice_mode'] =='ONLINE'){
                        ?>
                        <div class="pv_box_mid badge_dark">
                            <a href="" class="badge_padding badge_primary icon_hover action-transparent">Payment via Link </a>
                            <a href="" class="badge_padding badge_primary icon_hover action-transparent">Convert to Complementary </a>
                            <a href="" class="badge_padding badge_primary icon_hover action-transparent">Change Payment Mode</a>

                        </div>
                        <?php
                        }
                        ?>
                        <div class="spot_box_bottom">
                            <div class="spot_box_bottom_left">
                                <div class="action_div">
                                    <a href="invoice_send_mail.php" class="icon_hover badge_info br-5 w-auto action-transparent"><?php view() ?>Send Mail</a>
                                    <a href="invoice_send_sms.php" class="icon_hover badge_danger br-5 w-auto action-transparent"><?php delete() ?>Send SMS</a>
                                </div>
                            </div>
                            <div class="spot_box_bottom_right">
                                <a href="#" class="badge_info"><?php add(); ?>Make Payment</a>
                                <a href="<?= _BASE_URL_ ?>downloadFinalInvoice.php?delegateId=<?= $mycms->encoded($rowFetchUser['id']) ?>&slipId=<?= $mycms->encoded($rowFetchSlip['id']) ?>&original=true" target="_blank" class="print"><?php printi(); ?>Print PV</a>
                                <a href="<?= _BASE_URL_ ?>downloadSlippdf.php?delegateId=<?= $mycms->encoded($rowFetchUser['id']) ?>&slipId=<?= $mycms->encoded($rowFetchSlip['id']) ?>" target="_blank" class="badge_secondary"><?php bag(); ?>Download PV</a>
                                <a href="#" class="drp icon_hover badge_dark action-transparent">Invoice<?php down(); ?></a>
                            </div>
                        </div>
                        <div class="spot_service_break">
                            <div class="service_breakdown_wrap mt-0">
                                <h4><?php invoive(); ?>Invoice Breakdown</h4>
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Invoice Details</th>
                                                <th>Invoice For</th>
                                                <th>Invoice Amount</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="sl">1</td>
                                                <td>
                                                    <div class="regi_name">Invoice No</div>
                                                    <div class="regi_contact">
                                                        <span>
                                                            <?php invoive(); ?>OFFLINE
                                                        </span>
                                                        <span>
                                                            <?php calendar(); ?>Invoice Date
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    NATCON 2025-00033
                                                </td>
                                                <td>
                                                    ₹ 6,000
                                                </td>
                                                <td class="action text-right">
                                                    <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                                </td>
                                                <td class="text-right">
                                                    <div class="action_div">
                                                        <a href="#" class="icon_hover badge_primary br-5 w-auto action-transparent"><?php view() ?></a>
                                                        <a href="#" class="icon_hover badge_danger br-5 w-auto action-transparent"><?php delete() ?></a>
                                                    </div>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?
                    }
                }
                ?>
                <!-- <div class="spot_box">
                    <div class="spot_box_top">
                        <div class="spot_name">
                            <div class="regi_name">PV No</div>
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?>Mode
                                </span>
                                <span>
                                    <?php email(); ?>Date
                                </span>
                            </div>
                        </div>
                        <div class="spot_details">
                            <div class="spot_details_box">
                                <h5>PV Amount</h5>
                                <h6></h6>
                                <small>Discount: </small>
                            </div>
                            <div class="spot_details_box">
                                <h5>Total Amount</h5>
                                <h6></h6>
                            </div>
                            <div class="spot_details_box">
                                <h5>Paid Amount</h5>
                                <h6>12000</h6>

                            </div>
                            <div class="spot_details_box align-items-end">
                                <h5>Slip Created</h5>
                                <h6><?php calendar(); ?> 03/02/2026</h6>
                                <h6><?php clock() ?> 02:17:53 PM</h6>
                                
                            </div>
                        </div>
                    </div>
                    <div class="pv_box_mid badge_dark">
                        <a href="" class="badge_padding badge_primary icon_hover action-transparent">Payment via Link </a>
                        <a href="" class="badge_padding badge_primary icon_hover action-transparent">Convert to Complementary </a>
                        <a href="" class="badge_padding badge_primary icon_hover action-transparent">Change Payment Mode</a>

                    </div>
                    <div class="spot_box_bottom">
                        <div class="spot_box_bottom_left">
                            <div class="action_div">
                                <a href="invoice_send_mail.php" class="icon_hover badge_info br-5 w-auto action-transparent"><?php view() ?>Send Mail</a>
                                <a href="invoice_send_sms.php" class="icon_hover badge_danger br-5 w-auto action-transparent"><?php delete() ?>Send SMS</a>
                            </div>
                        </div>
                        <div class="spot_box_bottom_right">
                            <a href="#" class="badge_info"><?php add(); ?>Make Payment</a>
                            <a href="#" class="print"><?php printi(); ?>Print PV</a>
                            <a href="#" class="badge_secondary"><?php bag(); ?>Download PV</a>
                            <a href="#" class="drp icon_hover badge_dark action-transparent">Invoice<?php down(); ?></a>
                        </div>
                    </div>
                    <div class="spot_service_break">
                        <div class="service_breakdown_wrap mt-0">
                            <h4><?php invoive(); ?>Invoice Breakdown</h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Invoice Details</th>
                                            <th>Invoice For</th>
                                            <th>Invoice Amount</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="sl">1</td>
                                            <td>
                                                <div class="regi_name">Invoice No</div>
                                                <div class="regi_contact">
                                                    <span>
                                                        <?php invoive(); ?>OFFLINE
                                                    </span>
                                                    <span>
                                                        <?php calendar(); ?>Invoice Date
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                NATCON 2025-00033
                                            </td>
                                            <td>
                                                ₹ 6,000
                                            </td>
                                            <td class="action text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                            <td class="text-right">
                                                <div class="action_div">
                                                    <a href="#" class="icon_hover badge_primary br-5 w-auto action-transparent"><?php view() ?></a>
                                                    <a href="#" class="icon_hover badge_danger br-5 w-auto action-transparent"><?php delete() ?></a>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="spot_box">
                    <div class="spot_box_top">
                        <div class="spot_name">
                            <div class="regi_name">PV No</div>
                            <div class="regi_contact">
                                <span>
                                    <?php call(); ?>Mode
                                </span>
                                <span>
                                    <?php email(); ?>Date
                                </span>
                            </div>
                        </div>
                        <div class="spot_details">
                            <div class="spot_details_box">
                                <h5>PV Amount</h5>
                                <h6></h6>
                                <small>Discount: </small>
                            </div>
                            <div class="spot_details_box">
                                <h5>Total Amount</h5>
                                <h6></h6>
                            </div>
                            <div class="spot_details_box">
                                <h5>Paid Amount</h5>
                                <h6>12000</h6>

                            </div>
                            <div class="spot_details_box align-items-end">
                                <h5>Slip Created</h5>
                                <h6><?php calendar(); ?> 03/02/2026</h6>
                                <h6><?php clock() ?> 02:17:53 PM</h6>
                            </div>
                        </div>
                    </div>
                    <div class="spot_box_bottom">
                        <div class="spot_box_bottom_left">
                            <div class="action_div">
                                <a href="invoice_send_mail.php" class="icon_hover badge_info br-5 w-auto action-transparent"><?php view() ?>Send Mail</a>
                                <a href="invoice_send_sms.php" class="icon_hover badge_danger br-5 w-auto action-transparent"><?php delete() ?>Send SMS</a>
                            </div>
                        </div>
                        <div class="spot_box_bottom_right">
                            <a href="#" class="badge_info">Complimentary</a>
                            <a href="#" class="print"><?php printi(); ?>Print PV</a>
                            <a href="#" class="badge_secondary"><?php bag(); ?>Download PV</a>
                            <a href="#" class="drp icon_hover badge_dark action-transparent">Invoice<?php down(); ?></a>
                        </div>
                    </div>
                    <div class="spot_service_break">
                        <div class="service_breakdown_wrap mt-0">
                            <h4><?php invoive(); ?>Invoice Breakdown</h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="sl">#</th>
                                            <th>Invoice Details</th>
                                            <th>Invoice For</th>
                                            <th>Invoice Amount</th>
                                            <th class="action">Status</th>
                                            <th class="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="sl">1</td>
                                            <td>
                                                <div class="regi_name">Invoice No</div>
                                                <div class="regi_contact">
                                                    <span>
                                                        <?php invoive(); ?>OFFLINE
                                                    </span>
                                                    <span>
                                                        <?php calendar(); ?>Invoice Date
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                NATCON 2025-00033
                                            </td>
                                            <td>
                                                ₹ 6,000
                                            </td>
                                            <td class="action text-right">
                                                <span class="mi-1 badge_padding badge_success  w-max-con text-uppercase"><?php paid(); ?>Paid</span>
                                            </td>
                                            <td class="text-right">
                                                <div class="action_div">
                                                    <a href="#" class="icon_hover badge_primary br-5 w-auto action-transparent"><?php view() ?></a>
                                                    <a href="#" class="icon_hover badge_danger br-5 w-auto action-transparent"><?php delete() ?></a>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="tracking_analytic_box" id="breakfast">
            <div class="spot_listing">
                <?php
                $resultFetchInvoice                = getInvoiceDetailsquery("", $delegateId, "");
                $invoiceCounter                 = 0;
                if ($resultFetchInvoice) {
                    //print_r($resultFetchInvoice);
                    foreach ($resultFetchInvoice as $key => $rowFetchInvoice) {
                        $showTheRecord 		= true;
                        $invoiceCounter++;

                        $slip = getInvoice($rowFetchInvoice['slip_id']);
                        //print_r($slip);
                        $returnArray    = discountAmount($rowFetchInvoice['id']);
                        $percentage     = $returnArray['PERCENTAGE'];

                        $totalAmount    = $returnArray['TOTAL_AMOUNT'];

                        $discountAmount = $returnArray['DISCOUNT'];
                        $thisUserDetails = getUserDetails($rowFetchInvoice['delegate_id']);
                        $type			 = "";
                        if ($rowFetchInvoice['service_type'] == "DELEGATE_CONFERENCE_REGISTRATION") {
                            $type = "CONFERENCE REGISTRATION - " . $thisUserDetails['user_full_name'];
                        }
                        if ($rowFetchInvoice['service_type'] == "DELEGATE_WORKSHOP_REGISTRATION") {
                            $workShopDetails = getWorkshopDetails($rowFetchInvoice['refference_id']);
                            $type =  strtoupper(getWorkshopName($workShopDetails['workshop_id'])) . " REGISTRATION - " . $thisUserDetails['user_full_name'];
                            if ($workShopDetails['showInInvoices'] != 'Y') {
                                $showTheRecord 		= false;
                            }
                        }
                        if ($rowFetchInvoice['service_type'] == "ACCOMPANY_CONFERENCE_REGISTRATION") {
                            $thisUserAccompanyDetails = getUserDetails($rowFetchInvoice['refference_id']);
                            $type = "ACCOMPANY REGISTRATION - " . $thisUserAccompanyDetails['user_full_name'];
                        }
                        if ($rowFetchInvoice['service_type'] == "DELEGATE_RESIDENTIAL_REGISTRATION") {
                            if ($rowFetchUser['registration_classification_id'] == 3) {
                                $type = $cfg['RESIDENTIAL_NAME'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 7) {
                                $type = $cfg['RESIDENTIAL_NAME_IN_2N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 8) {
                                $type = $cfg['RESIDENTIAL_NAME_IN_3N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 9) {
                                $type = $cfg['RESIDENTIAL_NAME_SH_2N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 10) {
                                $type = $cfg['RESIDENTIAL_NAME_SH_3N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 11) {
                                $type = $cfg['RESIDENTIAL_NAME_IN_2N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 12) {
                                $type = $cfg['RESIDENTIAL_NAME_IN_3N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 13) {
                                $type = $cfg['RESIDENTIAL_NAME_SH_2N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 14) {
                                $type = $cfg['RESIDENTIAL_NAME_SH_3N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 15) {
                                $type = $cfg['RESIDENTIAL_NAME_IN_2N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 16) {
                                $type = $cfg['RESIDENTIAL_NAME_IN_3N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 17) {
                                $type = $cfg['RESIDENTIAL_NAME_SH_2N'] . " - " . $thisUserDetails['user_full_name'];
                            } else if ($rowFetchUser['registration_classification_id'] == 18) {
                                $type = $cfg['RESIDENTIAL_NAME_SH_3N'] . " - " . $thisUserDetails['user_full_name'];
                            }
                        }
                        if ($rowFetchInvoice['service_type'] == "DELEGATE_ACCOMMODATION_REQUEST") {
                            $type = "ACCOMMODATION BOOKING - " . $thisUserDetails['user_full_name'];
                        }
                        if ($rowFetchInvoice['service_type'] == "DELEGATE_DINNER_REQUEST") {
                            $type = $cfg['BANQUET_DINNER_NAME'] . " - " . getInvoiceTypeStringForMail($rowFetchInvoice['delegate_id'], $rowFetchInvoice['refference_id'], "DINNER");
                        }
                        if ($rowFetchInvoice['status'] == 'C') {
                            $styleColor = 'background: #FFCCCC;';
                        } else {
                            $styleColor = 'background: rgb(204, 229, 204);';
                        }

                        if ($rowFetchInvoice['has_gst'] == 'Y' && $rowFetchInvoice['invoice_mode'] == 'ONLINE') {
                            $invRowSpan = 2;
                        } else {
                            $invRowSpan = 1;
                        }

                        if ($showTheRecord) {
                ?>
                <div class="spot_box">
                    <div class="spot_box_top">
                        <div class="spot_name ">
                            <div class="regi_name">Invoice No :<?= $rowFetchInvoice['invoice_number'] ?></div>
                            <div class="regi_contact">
                                <span>
                                    <?php invoive(); ?>Invoice Mode :<?= $rowFetchInvoice['invoice_mode'] ?>
                                </span>
                                <span>
                                    <?php calendar(); ?>Invoice Date :<?= setDateTimeFormat2($rowFetchInvoice['invoice_date'], "D") ?>
                                </span>
                            </div>
                        </div>
                        <div class="spot_details">
                            <div class="spot_details_box">
                                <h5>Invoice For</h5>
                                <p><?= $type ?></p>
                            </div>
                            <div class="spot_details_box">
                                <h5>Invoice Amount</h5>
                                <h6><?= $rowFetchInvoice['currency'] ?> <?= number_format($totalAmount, 2) ?></h6>
                            </div>
                            <div class="spot_details_box align-items-end">
                                <h5>Payment Status</h5>
                                <span class="pay_status badge_padding badge_success w-max-con text-uppercase"><?php paid(); ?><?=$rowFetchInvoice['payment_status']?></span>
                                <!-- <h6>INR 14,000</h6> -->
                            </div>
                        </div>
                    </div>
                    <div class="spot_box_bottom">
                        <div class="spot_box_bottom_left">
                            <h6 class="active">PV No: <?= $slip['slip_number'] ?></h6>
                        </div>
                        <div class="spot_box_bottom_right">
                            <a href="print.invoice.php?user_id=<?= $rowFetchUser['id'] ?>&invoice_id=<?= $rowFetchInvoice['id'] ?>" target="_blank" class="print"><?php view(); ?>View</a>
                        </div>
                    </div>
                </div>
               <?php
               }

                    }
                }
                    ?>
            </div>
        </div>

    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>