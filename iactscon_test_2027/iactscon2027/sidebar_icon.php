<?php

$currentCutoffId = getTariffCutoffId();

$conferenceInvoiceDetails   = reset($invoiceList[$delegateId]['REGISTRATION']);

$workshopDetails   = $invoiceList[$delegateId]['WORKSHOP'];
$accompanyDtlsArr  = $invoiceList[$delegateId]['ACCOMPANY'];
$delgDinner    = getDinnerDetailsOfDelegate($delegateId);

$offline_payments = json_decode($cfg['PAYMENT.METHOD']);

$dinnerDtls  = array();

if ($delgDinner && !empty($delgDinner)) {
    $dinnerDtls[$delegateId]                = $delgDinner;
    $dinnerDtls[$delegateId]['INVOICE']     = getInvoiceDetails($delgDinner['refference_invoice_id']);
    $dinnerDtls[$delegateId]['USER']        = $rowUserDetails;
}

$dinnerDtlsAccm                 = array();
foreach ($accompanyDtlsArr as $key => $accompanyFullDtls) {
    $accomDtlsForDinnr                                  = $accompanyFullDtls['ROW_DETAIL'];
    $accompDinnrDet                                     = getDinnerDetailsOfDelegate($accomDtlsForDinnr['id']);

    if (!empty($accompDinnrDet)) {
        $dinnerDtlsAccm[$accomDtlsForDinnr['id']]               = $accompDinnrDet;
        $dinnerDtlsAccm[$accomDtlsForDinnr['id']]['INVOICE']    = getInvoiceDetails($accompDinnrDet['refference_invoice_id']);
        $dinnerDtlsAccm[$accomDtlsForDinnr['id']]['USER']       = $accomDtlsForDinnr;
    }
}

$abstract_details = delegateAbstractDetailsSummeryWithoutTopic($delegateId);

//fetching social media icon
$sql     =    array();
$sql['QUERY']    = "SELECT * FROM " . _DB_SOCIAL_ICON_SETTING_ . " 
                        WHERE `purpose`='Regular Icon' AND `status`='A'";
$resultSocialIcon            = $mycms->sql_select($sql);

// $valSocial    	= $result[0];

$sql                =    array();
$sql['QUERY']    = "SELECT * 
                                            FROM " . _DB_COMPANY_INFORMATION_;
$result            = $mycms->sql_select($sql);
$social_icon_text    =    $result[0]['social_icon_text'];

if ($resultSocialIcon) {
?>


    <div class="media-icons" data-aos="flip-left" data-aos-duration="800">
        <div class="media-bottom">
            <?php
            foreach ($resultSocialIcon as $k => $valSocial) {
                $icon_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $valSocial['icon'];
            ?>
                <a href="<?= $valSocial['link']; ?>" target="_blank" class="media-hidden"><img src="<?= $icon_image ?>" alt="" /></a>
            <?php
            }

            $sql     =    array();
            $sql['QUERY']    = "SELECT * FROM " . _DB_SOCIAL_ICON_SETTING_ . " 
                            WHERE `purpose`='Button Icon'";
            $result            = $mycms->sql_select($sql);
            $valSocialButton        = $result[0]['icon'];
            ?>
            <span class="media-click"><img src="<?= _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $valSocialButton ?>" width="40px" height="40px" alt="" /></span>
        </div>

        <div class="media-label"><span><?= $social_icon_text ?></span></div>
    </div>
<?php } ?>

<div class="dashbord-tow-menu sidebar1">
    <div class="close"><img src="images/close.svg" alt=""></div>
    <div class="register-header">
        <a href="<?= _BASE_URL_ ?>profile.php"><img src="<?= _BASE_URL_ ?>images/cat-ic-1.png" alt="" /></a>
        <h3>Registration</h3>
    </div>
    <div class="register-header2">
        <ul>
            <li>
                <img src="<?= _BASE_URL_ ?>images/badge1.png" alt="" />
                <h5>Member.</h5>
            </li>
            <li>
                <img src="<?= _BASE_URL_ ?>images/badge2.png" alt="" />
                <h5>Registration ID</h5>
            </li>
        </ul>
        <h7><?= $invoiceList[$delegateId]['REGISTRATION'][$delegateId]['REG_DETAIL'] ?></h7>
        <!-- <a href="#" class="btn">Invoice</a> -->
        <?php
        if (!empty($conferenceInvoiceDetails['INVOICE']['id']) && $conferenceInvoiceDetails['INVOICE']['id'] > 0) {

        ?>
            <a href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $conferenceInvoiceDetails['INVOICE']['id'] ?>" target="_blank" class="btn">INVOICE</a>
            <?php
            if ($conferenceInvoiceDetails['INVOICE']['payment_status'] == 'UNPAID' && $conferenceInvoiceDetails['INVOICE']['invoice_mode'] == 'ONLINE') {
            ?>


                <a onclick="onlinePayNow('<?= $conferenceInvoiceDetails['INVOICE']['slip_id'] ?>', '<?= $delegateId ?>')" style=" border-radius: 20px;  margin-right: 10px; color: #ffffff; display: inline-block; margin-top: 6px;padding: 7px;cursor: pointer;">PAY NOW</a>

            <?php

            }
        } else {

            ?>
            <a href="<?= _BASE_URL_ ?>registration.tariff.php?abstractDelegateId=<?= $delegateId ?>" class="btn">REGISTER NOW</a>
            <a href="<?= _BASE_URL_ ?>" class="btn">Log In</a>
        <?php
        }
        ?>
    </div>
    <?php
    if (!empty($delegateId) && $delegateId > 0) {
    ?>
        <div class="workshopmore-holder">
            <div class="accordion" id="accordionExample">
                <?php
                $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);

                if (count($workshopDetailsArray) > 0) {
                ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <img src="<?= _BASE_URL_ ?>images/cat-ic-2.png" alt=""> Workshop
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                            <div class="accordion-body">
                                <div class="workshopmore-btnholder">
                                    <?php
                                    if ($workshopDetails) {
                                        $wrksp_Cnt = 0;
                                        $existingWorkShops = array();
                                        foreach ($workshopDetails as $key => $rowWorkshopDetails) {

                                            if ($rowWorkshopDetails && $rowWorkshopDetails['INVOICE']['status'] == 'A') {

                                                $existingWorkShops[] = $rowWorkshopDetails['ROW_DETAIL']['type'];
                                                $workshopStatus = true;
                                                $wrksp_Cnt++;

                                                if ($rowWorkshopDetails['INVOICE']['service_roundoff_price'] > 0) {
                                                    $workshopAmountDisp = $rowWorkshopDetails['INVOICE']['currency'] . ' ' . $rowWorkshopDetails['INVOICE']['service_roundoff_price'];
                                                } else {
                                                    $workshopAmountDisp = 'Included in package';
                                                }


                                                if (!empty($rowWorkshopDetails['ROW_DETAIL']['workshop_date'])) {
                                                    $workshop_date = '(' . $rowWorkshopDetails['ROW_DETAIL']['workshop_date'] . ')';
                                                } else {
                                                    $workshop_date = '';
                                                }

                                                if (!empty($rowWorkshopDetails['ROW_DETAIL'])) {

                                                    if ($rowWorkshopDetails['INVOICE']['service_roundoff_price'] > 0) {
                                    ?>
                                                        <h7><?= $rowWorkshopDetails['REG_DETAIL'] ?> <?= $workshop_date ?></h7>
                                                        <a href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $rowWorkshopDetails['INVOICE']['id'] ?>" target="_blank" class="btn">INVOICE</a>
                                                        <?php

                                                        if ($rowWorkshopDetails['INVOICE']['payment_status'] == 'UNPAID' && $rowWorkshopDetails['INVOICE']['invoice_mode'] == 'ONLINE') {
                                                        ?>


                                                            <a onclick="onlinePayNow('<?= $rowWorkshopDetails['INVOICE']['slip_id'] ?>', '<?= $delegateId ?>')" style=" border-radius: 20px;  margin-right: 10px; color: #ffffff; display: inline-block; margin-top: 6px;padding: 7px;cursor: pointer;">PAY NOW</a>

                                            <?php

                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {

                                        $workshopDetailsArray    = getAllWorkshopTariffs($currentCutoffId);
                                        //echo count($workshopDetailsArray);
                                        if (count($workshopDetailsArray) > 0) {
                                            ?>
                                            <a href="<?= _BASE_URL_ . "profile-add.php?section=3" ?>" class="btn">Add Workshop</a>
                                    <?php
                                        }
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                }
                $registrationAccompanyAmount  = getCutoffTariffAmnt($currentCutoffId);
                if (!empty($registrationAccompanyAmount) && $registrationAccompanyAmount > 0) {
                ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <img src="<?= _BASE_URL_ ?>images/cat-ic-3.png" alt=""> Accompaning
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample" style="">
                            <div class="accordion-body">
                                <div class="workshopmore-btnholder">
                                    <?php
                                    if (sizeof($accompanyDtlsArr) > 0) {
                                    ?>
                                        <table class="tableCard" style="margin-bottom: 0;">
                                            <tr>
                                                <th>Accompany Details</th>
                                                <th></th>
                                            </tr>
                                            <?php


                                            foreach ($accompanyDtlsArr as $key => $accompanyFullDtls) {

                                                $accompanyDtls        = $accompanyFullDtls['ROW_DETAIL'];

                                                $accompanySlipDtls    = $accompanyFullDtls['SLIP_DETAILS'];
                                                $accompanInvoiceDtls  = $accompanyFullDtls['INVOICE'];
                                                $accompanyPaymentDtls = $accompanyFullDtls['SLIP_PAYMENT'];
                                                $dataVal              = $accompanInvoiceDtls['currency'] . ' ' . $accompanInvoiceDtls['service_roundoff_price'];

                                                //echo 'ID='. $accompanyDtls['id'];

                                                $dinnerDetails  = $dinnerDtlsAccm[$accompanyDtls['id']];

                                                // echo '<pre>'; print_r($accompanInvoiceDtls);
                                            ?>
                                                <tr>
                                                    <td><?= strtoupper($accompanyDtls['user_full_name']) ?></td>
                                                    <td><a href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $accompanInvoiceDtls['id'] ?>" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; display: inline-block; ">INVOICE</a>
                                                        <?php
                                                        if ($accompanInvoiceDtls['payment_status'] == 'UNPAID' && $accompanInvoiceDtls['invoice_mode'] == 'ONLINE') {
                                                        ?>


                                                            <a onclick="onlinePayNow('<?= $accompanInvoiceDtls['slip_id'] ?>', '<?= $delegateId ?>')" style=" border-radius: 20px;  margin-right: 10px; color: #ffffff; display: inline-block; margin-top: 6px;padding: 7px;cursor: pointer;">PAY NOW</a>
                                                            <!--<a>CANCEL</a>-->
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php

                                                    if (!empty($dinnerDetails)) {


                                                        $dinrInvAmtDisp = 'Included in Package';
                                                        $hasInvoice     = false;
                                                        if ($dinnerDetails['INVOICE']['service_type'] == 'DELEGATE_DINNER_REQUEST') {
                                                            $dinrInvAmtDisp = $dinnerDetails['INVOICE']['currency'] . ' ' . $dinnerDetails['INVOICE']['service_roundoff_price'];
                                                            $hasInvoice     = true;

                                                    ?>
                                                <tr>

                                                    <td class="accompany_price" style="border: 0; padding: 2px 17px">
                                                        BANQUET

                                                    </td>
                                                    <td>

                                                        <?
                                                            if ($hasInvoice) {
                                                        ?>
                                                            <a href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $dinnerDetails['INVOICE']['id'] ?>" style="background: #2393c3; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: white ">INVOICE</a>
                                                            <!--<a>CANCEL</a>-->
                                                        <?
                                                            }
                                                            if ($dinnerDetails['INVOICE']['payment_status'] == 'UNPAID' && $dinnerDetails['INVOICE']['invoice_mode'] == 'ONLINE') {

                                                        ?>
                                                            <!-- <a onclick="onlinePayNow('<?= $dinnerDetails['INVOICE']['slip_id'] ?>', '<?= $delegateId ?>')" style=" border-radius: 20px;  margin-right: 10px; color: #ffffff; display: inline-block; margin-top: 6px;padding: 7px;cursor: pointer;">PAY NOW</a> -->
                                                        <?php
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>


                                        <?php
                                                        }
                                                    }


                                        ?>
                                        </tr>

                                    <?php

                                            }

                                    ?>

                                        </table>
                                    <?php
                                    }
                                    ?>
                                    <a href="<?= _BASE_URL_ . "profile-add.php?section=4"; ?>" class="btn">Add Accompaning</a>
                                    <!-- <a href="#" class="btn">Invoice</a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            <img src="<?= _BASE_URL_ ?>images/Banquet.png" alt=""> Banquet
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample" style="">
                        <div class="accordion-body">
                            <div class="workshopmore-btnholder">

                                <?php
                                if (sizeof($dinnerDtls) > 0) {
                                ?>
                                    <table class="table" style=" margin-bottom: 0">
                                        <?
                                        $dinrCount = 0;
                                        foreach ($dinnerDtls as $uId => $dinnerDetails) {
                                            $dinrCount++;
                                            if ($dinrCount % 2 == 1) {
                                                $dinrClass  = "gala_dinner_accompany";
                                                $dinrBG     = "#2393c3";
                                                $dinrInvBG  = "#27a5db";
                                            } else {
                                                $dinrClass  = "gala_dinner_workshop";
                                                $dinrBG     = "#27a5db";
                                                $dinrInvBG  = "#2393c3";
                                            }

                                            $dinrInvAmtDisp = 'Included in Package';
                                            $hasInvoice     = false;
                                            if ($dinnerDetails['INVOICE']['service_type'] == 'DELEGATE_DINNER_REQUEST') {
                                                $dinrInvAmtDisp = $dinnerDetails['INVOICE']['currency'] . ' ' . $dinnerDetails['INVOICE']['service_roundoff_price'];
                                                $hasInvoice     = true;
                                            }


                                        ?>

                                            <tr>
                                                <td class="gala_dinner_accompany_name" colspan="2" style="border: 0 ; font-size: 18px; color: white; font-weight: bold; padding: 13px 0 0 13px">
                                                    BANQUET-DINNER

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="gala_dinner_accompany_name" colspan="2" style="border: 0 ; font-size: 18px; color: white; font-weight: bold; padding: 13px 0 0 13px">
                                                    <span style="font-weight:normal;"><?= $dinnerDetails['USER']['user_full_name'] ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="gala_dinner_accompany_price" colspan="2" style="border: 0 ; font-size: 16px; color: white; padding: 0px 0 13px 13px">
                                                    <span style="font-style: italic"><?= $dinrInvAmtDisp ?></span>

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="gala_dinner_accompany_print" style="border: 0">
                                                    <?
                                                    if ($hasInvoice) {
                                                    ?>
                                                        <a href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $dinnerDetails['INVOICE']['id'] ?>" style="background: <?= $dinrInvBG ?>; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color white ">INVOICE</a>
                                                    <?
                                                    }
                                                    if ($dinnerDetails['INVOICE']['payment_status'] == 'UNPAID' && $dinnerDetails['INVOICE']['invoice_mode'] == 'ONLINE') {
                                                    ?>

                                                        <!-- <a onclick="onlinePayNow('<?= $dinnerDetails['INVOICE']['slip_id'] ?>', '<?= $delegateId ?>')" style=" border-radius: 20px;  margin-right: 10px; color: #ffffff; display: inline-block; margin-top: 6px;padding: 7px;cursor: pointer;">PAY NOW</a> -->
                                                    <?php
                                                    }
                                                    ?>


                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                } else {
                                ?>
                                    <a href="<?= _BASE_URL_ . "profile-add.php?section=5"; ?>" class="btn">Add Banquet</a>
                                    <!-- <a href="#" class="btn">Invoice</a> -->
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <img src="<?= _BASE_URL_ ?>images/cat-ic-6.png" alt=""> Accomodation
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample" style="">
                        <div class="accordion-body">
                            <div class="workshopmore-btnholder">

                                <table class="table" style=" margin-bottom: 0">

                                    <?php
                                    if (isset($invoiceList[$delegateId]['ACCOMMODATION'])) {
                                        //echo '<pre>'; print_r($invoiceList[$delegateId]['ACCOMMODATION']['ROW_DETAIL']);

                                        global $mycms, $cfg;

                                        $sqlGetAccommodation   = array();
                                        $sqlGetAccommodation['QUERY']  = "SELECT req.id,req.refference_invoice_id,req.user_id,req.accompany_name,req.accommodation_details,req.hotel_id,req.package_id,req.checkin_date,req.checkout_date,req.booking_quantity,R.room_id, req.rooms_no FROM " . _DB_REQUEST_ACCOMMODATION_ . " req INNER JOIN  " . _DB_MASTER_ROOM_ . " R ON req.id = R.request_accommodation_id WHERE req.`user_id` = '" . trim($delegateId) . "' AND req.`status`='A' AND R.`status`='A' ORDER BY R.room_id ASC, req.created_dateTime ASC";

                                        $resultGetAccommodation = $mycms->sql_select($sqlGetAccommodation);
                                        $roomsArr = array();
                                        $countAccoDetils = 0;
                                        $countAccoDetilsInnr = '';
                                        $countAccoDetils += count($roomsArr);
                                        foreach ($resultGetAccommodation as $key => $value) {
                                            $roomsArr[$value['room_id']][] = $value;

                                            $countAccoDetilsInnr = count($value);
                                        }

                                        //echo '<pre>'; print_r($roomsArr); 
                                        //echo $countAccoDetilsInnr;
                                        //echo $countAccoDetils + $countAccoDetilsInnr;
                                    ?>

                                        <tr>
                                            <td style="border: 0; vertical-align: bottom; padding: 8px 0;">
                                                <table class="tableCard">
                                                    <tr>

                                                        <td colspan="2" style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">
                                                            ACCOMMODATION

                                                        </td>
                                                    </tr>
                                                    <?
                                                    //echo '<pre>'; print_r($invoiceList[$delegateId]['ACCOMMODATION']);
                                                    $accommodation_room = '';
                                                    $count = 0;

                                                    $hotel_id = '';
                                                    $distinctArray = [];
                                                    foreach ($roomsArr as $key => $accommodationRow) {
                                                    ?>
                                                        <tr>
                                                            <td style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">Room <?= $key ?></td>
                                                        </tr>
                                                        <?php

                                                        foreach ($accommodationRow as $key => $value) {
                                                            //print_r($value);
                                                            $getAccommodationMaxRoom = getAccommodationMaxRoom($delegateId);

                                                            if (!empty($value['rooms_no'])) {
                                                                $accommodations_room = $value['rooms_no'];
                                                            } else {
                                                                $accommodations_room = 1;
                                                            }

                                                            $hotel_id =  $value['hotel_id'];
                                                            $hotel_name = getHotelNameByID($hotel_id);

                                                            if (!in_array($accommodations_room, $distinctArray)) {
                                                                $distinctArray[] = $accommodations_room;
                                                            }

                                                            $getPackageNameById =  getPackageNameById($value['package_id']);
                                                            $total_stays = getDaysBetweenDates($value['checkin_date'], $value['checkout_date']);
                                                            $total_stay = $getPackageNameById . " for " . $total_stays;

                                                        ?>
                                                            <tr>
                                                                <td class="resi_pkg_hotel" style="border: 0; padding: 10px 5px; font-size: 14px; padding-top: 0 ">
                                                                    <span style="font-style: italic;font-size: 14px;"><?= (!empty($total_stay) ? str_replace("+", "", $total_stay) . ' at ' : '') . $hotel_name ?></span>
                                                                    <br>
                                                                    <h5>
                                                                        <span><sup>*</sup>Check In -</span>
                                                                        <span>DATE: <?= $mycms->cDate('d/m/Y', $value['checkin_date']) ?></span>

                                                                        <br>
                                                                        <span><sup>*</sup>Check Out -</span>
                                                                        <span>DATE: <?= $mycms->cDate('d/m/Y', $value['checkout_date']) ?></span>

                                                                    </h5>
                                                                </td>
                                                                <td class="left-btn" style="border: 0; text-align: right; vertical-align:middle !important;">
                                                                    <a href="pdf.download.invoice.php?user_id=<?= $delegateId ?>&invoice_id=<?= $value['refference_invoice_id'] ?>" target="_blank" style="background: #005e82; border-radius: 20px; padding: 5px 15px; margin-right: 10px; color: #ffffff; display: inline-block; ">INVOICE</a>

                                                                </td>


                                                            </tr>
                                                    <?php

                                                        }
                                                    }



                                                    ?>
                                                    <tr>
                                                        <td>

                                                            <?php

                                                            foreach ($distinctArray as $key => $value) {
                                                                //echo $value;
                                                                //echo $hotel_id;
                                                                $minCheckInDateByHotelID = minCheckInDateByHotelID($hotel_id);
                                                                $maxCheckInDateByHotelID = maxCheckOutDateByHotelID($hotel_id);
                                                                $getNightValidate = getNightValidate($value, $minCheckInDateByHotelID, $maxCheckInDateByHotelID, $delegateId, $count);
                                                            }
                                                            if ($getNightValidate < 3) {
                                                            ?>
                                                                <h5 class="smallhed addCategory" style="text-align: center;padding: 0px; color: #ffffff; cursor:pointer; white-space: nowrap;font-size: 14px;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationMoreNight(6,'<?= $hotel_id ?>')">ADD MORE NIGHTS</h5>
                                                            <?php
                                                            }
                                                            //echo $accommodation_room;
                                                            if ($getAccommodationMaxRoom < 3) {
                                                            ?>


                                                                <h5 class="smallhed addCategory" style="text-align: center;padding: 0px; color: #ffffff; cursor:pointer; white-space: nowrap;font-size: 14px;" use="addCategory" linkId="wrokshop_add" onclick="addAccommodationMoreNight(6,'<?= $hotel_id ?>')">ADD MORE ROOMS</h5>

                                                            <?php
                                                            }
                                                            ?>

                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                    <?php
                                    } else {
                                    ?>
                                        <a href="<?= _BASE_URL_ . "profile-add.php?section=6"; ?>" class="btn">Add Accomodation</a>
                                    <?php
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <? if ($cfg['ABSTRACT.EDIT.LASTDATE'] >= date('Y-m-d')) {
                ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <img src="<?= _BASE_URL_ ?>images/cat-ic-4.png" alt=""> Abstract Details
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionExample" style="">
                            <div class="accordion-body">
                                <div class="workshopmore-btnholder">
                                    <table class="tableCard" style="margin-bottom: 0">

                                        <tbody>
                                            <?php

                                            foreach ($abstract_details as $key => $value) {
                                            ?>
                                                <tr>

                                                    <td colspan="2" class="reg_cat" style="border: 0; padding: 10px 5px; font-size: 18px; font-weight: bold">
                                                        <!--  <?= strtoupper($value['abstract_category']) ?> <?= $value['abstract_parent_type'] ?> DETAILS -->

                                                    </td>
                                                </tr>
                                                <tr style="vertical-align: initial;">

                                                    <td class="reg_name">


                                                        Submission Code: <b><?= $value['abstract_submition_code'] ?></b>
                                                    </td>

                                                    <?

                                                    if ($cfg['ABSTRACT.EDIT.LASTDATE'] >= date('Y-m-d')) { ?>
                                                        <td class="left-btn">
                                                            <a href="abstract.user.edit.php?user_id=<?= $delegateId ?>&abstract_id=<?= $value['id'] ?>" target="" class="btn">VIEW AND EDIT</a>
                                                        </td>


                                                    <? } ?>
                                                </tr>

                                            <?php
                                            }

                                            if (count($abstract_details) < 100) {
                                            ?>
                                                <tr>
                                                    <td class="left-btn"><a href="<?= _BASE_URL_ ?>abstract.user.entrypoint.php" class="btn">Add New</a></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                }
                ?>

            </div>
            <a href="<?= _BASE_URL_ ?>login.process.php?action=logout" class="btn" style="margin-top: 12px;">Logout</a>
        </div>
    <?php
    }
    ?>
</div>

<!-- <div class="checkout-main-wrap1" id="paymentVoucherModal1">
    <?php
    $sqlLogo    =   array();
    $sqlLogo['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
			    WHERE title='Online Payment Logo' ";

    $resultLogo      = $mycms->sql_select($sqlLogo);
    $logo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $resultLogo[0]['image'];
    ?>
    <div class="checkout-popup" style="max-width: 900px;">
        <div class="card-details">
            <div class="card-details-inner">
                <img src="<?= $logo ?>" alt="" />

                <form name="frmApplyPayment" id="frmApplyPayment" action="registration.process.php" method="post">
                    <div class="col-xs-12 form-group" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>

                        <div class="checkbox custom-radio-holder">


                            <input type="hidden" id="slip_id" name="slip_id" />
                            <input type="hidden" id="delegate_id" name="delegate_id" />
                            <input type="hidden" name="act" value="paymentSet" />
                            <input type="hidden" name="mode" id="mode" />
                          
                            &nbsp;
                            <input type="radio" name="card_mode" use="card_mode_select" value="Indian" checked style="visibility: hidden;">

                        </div>

                    </div>

                    <div class="policy-div">
                        <ul>
                            <li><a href="">Cancellation Policy</a></li>
                            <li><a href="">Privacy Policy</a></li>
                        </ul>
                    </div>

                    <div class="righr-btn"><input type="button" class="pay-button" id="pay-button-vouchar" value="Pay Now"></div>
                </form>

            </div>
        </div>
        <div class="cart-details" style="width: 50%;">
            <div class="cart-heading">
                <h5>Order Summary</h5>

                <a class="close-check" style="cursor: pointer;"><span>&#10005;</span> close</a>
            </div>

            <div class="cart-data-row add-inclusion" use="totalAmount" id="paymentVoucherBody">


            </div>
        </div>
    </div>
</div> -->


<script type="text/javascript">
    function onlinePayNow(slipId, delegateId) {

        if (slipId != '' && delegateId != '') {
            $('#paymentVoucherBody').text("");
            $.ajax({
                type: "POST",
                url: jsBASE_URL + 'login.process.php',
                data: 'action=getPaymentVoucharDetailsProfile&delegateId=' + delegateId + '&slipId=' + slipId,
                dataType: 'json',
                async: false,
                success: function(JSONObject) {
                    JSONObject = JSONObject.replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '');
                    JSONObject = JSON.parse(JSONObject);
                  

                    console.log(JSONObject);

                    if (JSONObject.error == 400) {
                        toastr.error(JSONObject.msg, 'Error', {
                            "progressBar": true,
                            "timeOut": 3000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });
                    } else if (JSONObject.succ == 200) {
                        $('#user_email_id').val("");
                        $('#loading_indicator').show();
                        $('#payModal').hide();
                        $('#paymentVoucherBody').append(JSONObject.data);

                        $('#slip_id').val(JSONObject.slipId);
                        $('#delegate_id').val(JSONObject.delegateId);
                        $('#mode').val(JSONObject.invoice_mode);

                        $('#loginBtn').prop('disabled', true);

                        toastr.success(JSONObject.msg, 'Success', {
                            "progressBar": true,
                            "timeOut": 2000,
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });
                        setTimeout(function() {
                            $('#loading_indicator').hide();
                            $('#paymentVoucherModal').show();
                            $('#loginBtn').prop('disabled', false);
                            //window.location.href= jsBASE_URL + 'profile.php';

                        }, 2000);
                    }


                }
            });
        }
    }
</script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer">
</script> -->