<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>NAPCON 2024</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<?php
$offline_payments = json_decode($cfg['PAYMENT.METHOD']);
?>

<body>

    <div id="checkout-main-wrap" class="checkout-main-wrap">
        <div class="checkout-main-wrap-inner">
            <div class="checkout-main-wrap-box">
                <div class="left-wrap">
                    <?php
                    $sql_qr = array();
                    $sql_qr['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . "
                                         WHERE `id`!='' AND `title`IN ('QR Code','Online Payment Logo')";
                    $result = $mycms->sql_select($sql_qr);
                    $onlinePaymentLogo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['image'];
                    $QR_code = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['image'];


                    if (in_array("Card", $offline_payments)) {
                    ?>
                        <div class="card-info">
                            <h4 class="block-head">Accepted Cards</h4>
                            <p>
                                <img src="<?= $onlinePaymentLogo ?>" style="width: 100%;">
                                <!-- <img src=""> -->
                            </p>
                        </div>
                    <?php } ?>
                    <div class="bank-info">
                        <h4 class="block-head">Bank Details</h4>
                        <p>Bank Name
                            <br><b><?= $cfg['INVOICE_BANKNAME'] ?></b>
                        </p>
                        <p>Account Number
                            <br><b><?= $cfg['INVOICE_BANKACNO'] ?></b>
                        </p>
                        <p>Benefeciary Name
                            <br><b><?= $cfg['INVOICE_BENEFECIARY'] ?></b>
                        </p>
                        <p>IFSC Code
                            <br><b><?= $cfg['INVOICE_BANKIFSC'] ?></b>
                        </p>
                        <p>Branch
                            <br><b><?= $cfg['INVOICE_BANKBRANCH'] ?></b>
                        </p>
                    </div>
                    <?php

                    if (in_array("Neft", $offline_payments)) {
                    ?>
                        <div class="qr-info">
                            <h4 class="block-head">UPI Details</h4>
                            <p>
                                <span class="qr-img"><img src="<?= $QR_code ?>"></span>
                                <!-- <span>Lorem Ipsum</span> -->
                            </p>
                        </div>
                    <?php } ?>
                    <div class="bank-info">
                        <h4 class="block-head">Helpline No.</h4>
                        <p><i class="mr-2"></i><?= $cfg['CART_HELPLINE'] ?></p>
                    </div>
                </div>
                <!-- ============================ PAYMENT OPTIONS DIV ============================-->
                <div class="payment-wrap" id="paymentOptions">
                    <div class="payment-type-wrap">
                        <input type="hidden" name="registrationMode" id="registrationMode">
                        <?php


                        if (in_array("Card", $offline_payments)) {
                        ?>
                            <label class="card-con">
                                Cards
                                <input type="radio" class="payRadioBtn" name="payment_mode" use="payment_mode_select" value="Card" for="Card" paymentMode='ONLINE' disabled>
                                <input type="radio" name="card_mode" use="card_mode_select" value="Indian" checked style="visibility: hidden;">

                                <span class="checkmark"></span>
                            </label>
                        <?php
                        }
                        if (in_array("Cheque/DD", $offline_payments)) {
                        ?>
                            <label class="card-con">
                                DD
                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Cheque" for="Cheque" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                                <span class="checkmark"></span>
                            </label>
                        <?php
                        }
                        if (in_array("Neft", $offline_payments)) {
                        ?>
                            <label class="card-con">
                                NEFT/UPI
                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Neft" for="Neft" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                                <span class="checkmark"></span>
                            </label>
                        <?php
                        }
                        if (in_array("Cash", $offline_payments)) {
                        ?>
                            <label class="card-con">
                                Cash
                                <input type="radio" name="payment_mode" use="payment_mode_select" value="Cash" for="Cash" paymentMode='OFFLINE' class="payRadioBtn" disabled>
                                <span class="checkmark"></span>
                            </label>
                        <?php
                        }
                        ?>
                    </div>
                    <div id="paymentDetailsSection" style="display: none;">
                        <div class="paymnet-box" id="chq-dd" style="display:none;" use="offlinePaymentOption" for="Cheque" actAs='fieldContainer'>
                            <div class="top-input">
                                <label>DD No.</label>
                                <!-- <input type="text" placeholder="Enter DD No."> -->
                                <input type="number" class="form-control" name="cheque_number" id="cheque_number" validate="Please enter cheque number" placeholder="Enter DD No." type="number" maxlength="6" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;">

                            </div>
                            <div class="input-box">
                                <label>Drawee Bank</label>
                                <!-- <input type="text" placeholder="Enter Drawee Bank Name"> -->
                                <input type="text" class="form-control" name="cheque_drawn_bank" id="cheque_drawn_bank" validate="Please enter drawn bank" placeholder="Enter Drawee Bank Name">

                            </div>
                            <div class="input-box">
                                <label>Date</label>
                                <input type="date" class="form-control" name="cheque_date" id="cheque_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select cheque date">
                            </div>
                        </div>
                        <div class="paymnet-box" id="neft-upi" style="display:none;" use="offlinePaymentOption" for="Neft" actAs='fieldContainer'>
                            <div class="top-input">
                                <label>Transaction Id</label>
                                <!-- <input type="text" placeholder="Enter Transaction Id"> -->
                                <input type="text" class="form-control" name="neft_transaction_no" id="neft_transaction_no" validate="Please enter transaction number" placeholder="Enter Transaction Id">

                            </div>
                            <div class="input-box">
                                <label>Date</label>
                                <input type="date" class="form-control" name="neft_date" id="neft_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select neft date" placeholder="Date">
                            </div>

                            <div class="input-box">
                                <label>Drawee Bank</label>
                                <input type="text" class="form-control" name="neft_bank_name" id="neft_bank_name" validate="Please enter neft bank" placeholder="Bank Name">
                            </div>
                            <div class="input-box">
                                <label>Image</label>
                                <input type="file" name="neft_document" id="neft_document" style="display:none" validate="Please upload a image">
                                <label for="neft_document" class="file-label">+</label>
                                <br>
                                <span id="neft_file_name" style="font-size: 14px;padding: 5px;"></span>
                            </div>

                        </div>
                        <div class="paymnet-box" id="cash" style="display:none;" use="offlinePaymentOption" for="Cash" actAs='fieldContainer'>
                            <div class="top-input">
                                <label>Date</label>
                                <input type="date" class="form-control" name="cash_deposit_date" id="cash_deposit_date" max="<?= $mycms->cDate("Y-m-d") ?>" min="<?= $mycms->cDate("Y-m-d", "-6 Months") ?>" validate="Please select date" placeholder="Date">
                            </div>
                            <div class="input-box">
                                <label>Image</label>
                                <input type="file" name="cash_document" id="cash_document" style="display:none" validate="Please upload a image">
                                <label for="cash_document" class="file-label">+</label>
                                <br>
                                <span id="cash_file_name" style="font-size: 14px;padding: 5px;"></span>
                            </div>
                        </div>
                        <div class="paymnet-box" style="display:none;" use="offlinePaymentOption" for="Card" actAs='fieldContainer'>
                            <input type="radio" name="card_mode" use="card_mode_select" value="Indian" checked style="visibility: hidden;">
                        </div>
                    </div>
                    <!-- <button class="payment-button" id="pay-button">Submit</button> -->
                    <input type="button" class="payment-button" id="pay-button" value="Submit">
                </div>

                <div class="total-bill">
                    <a class="close-check" style="cursor: pointer;"><span>&#10005;</span></a>

                    <div class="bill-info-text">
                        <?= $cfg['cheque_info'] ?>
                    </div>
                    <div class="total-bill-amount" use="totalAmount">
                        <h5>Total Payable Amount</h5>
                        <h3 use="totalAmount">₹ 0.00</h3>
                    </div>
                </div>

                <!-- ============================ ORDER SUMMERY =================================== -->
                <div class="summery" id="orderSummerySection">
                    <h4 class="block-head">Order Summery</h4>
                    <ul use="totalAmountTable">
                        <li use='rowCloneable' style="display:none;">
                            <p class="order-image">
                                <!-- <img
                                    src="https://ruedakolkata.com/napcon2024/uploads/EMAIL.HEADER.FOOTER.IMAGE/ICON_0002_240913131957.png"> -->
                                <span use="icon"></span>
                            </p>
                            <p class="order-name">
                                <span use="invTitle"></span>
                            </p>
                            <p class="order-amount">
                                <span use="amount">0.00</span>
                            </p>
                            <p>
                            <span use="deleteIcon"></span></p>
                            <!-- <button class="order-dlt" use="deleteIcon" id="deleteItem" style="display:none">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </li>


                    </ul>
                </div>
            </div>
            <div class="qr-large">
                <img src="<?= $QR_code ?>">
                <button type="button" style="color:black">
                    X
                </button>
            </div>
        </div>
    </div>


    <!--=============================================== ONLINE PAYMENT RETRY MODAL ===========================================-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
    <script>
        $("input[type=radio][use=payment_mode_select]").click(function() {
            var val = $(this).val();

            $("div[use=offlinePaymentOption]").hide();
            if (val != undefined) {
                $("div[use=offlinePaymentOption][for=" + val + "]").show();
                if (val === 'Card') {
                    $('#registrationMode').val('ONLINE');
                    $('#paymentDetailsSection').hide();
                } else {
                    $('#registrationMode').val('OFFLINE');
                    $('#paymentDetailsSection').show();
                }
            }

        });

        $('#neft_document').on('change', function() {
            var file = this.files[0];
            if (file) {
                $('#neft_file_name').html(file['name']);
            } else {
                $('#neft_file_name').html('');
            }
        })

        $('#cash_document').on('change', function() {
            var file = this.files[0];
            if (file) {
                $('#cash_file_name').html(file['name']);
            } else {
                $('#cash_file_name').html('');
            }
        })

        $("#pay-button").click(function() {

            var selectedOption = $("input[type=radio][name='payment_mode']:checked").val();
            var flag = 0;

            if (selectedOption) {

                $("div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='text'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='date'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='radio'], div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='number'],div[use='offlinePaymentOption'][for='" + selectedOption + "'] input[type='file']").each(function() {

                    if ($(this).attr('type') === 'radio') {
                        if (!$("input[type='radio'][name='card_mode']:checked").length) {
                            toastr.error('Please select the card', 'Error', {
                                "progressBar": true,
                                "timeOut": 5000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });
                            flag = 1;
                            return false;
                        }
                    } else {


                        var textBoxValue = $(this).val();
                        if (textBoxValue === '') {
                            toastr.error($(this).attr('validate'), 'Error', {
                                "progressBar": true,
                                "timeOut": 5000,
                                "showMethod": "slideDown",
                                "hideMethod": "slideUp"
                            });
                            flag = 1;
                            return false;
                        }
                    }
                });
            } else {
                //alert("No option selected!");
                toastr.error('Please select payment mode', 'Error', {
                    "progressBar": true,
                    "timeOut": 5000,
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp"
                });

                flag = 1;
                return false;
            }

            if (flag == 0) {
                //alert(1212);
                $("form[name='registrationForm']").submit();
            } else {
                return false;
            }

        });

        function get_checkout_val(val) {
            // alert(val);
            var checkInVal = $('#accomodation_package_checkin_id').val();
            var checkOutVal = val;
            const checkInArray = checkInVal.split("/");
            var checkInID = checkInArray[0];
            var checkInDate = checkInArray[1];

            const checkOutArray = checkOutVal.split("/");
            var checkOutID = checkOutArray[0];
            var checkOutDate = checkOutArray[1];

            var date1 = new Date(checkInDate);
            var date2 = new Date(checkOutDate);

            // Calculate the difference in milliseconds
            var differenceMs = Math.abs(date2 - date1);

            // Convert the difference to days
            var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));

            var totalAmount = 0;
            var totTable = $("ul[use=totalAmountTable]");
            // $(totTable).children("li").find("tr").remove();
            // $(totTable).find("li[use='rowCloned']").remove();

            var gst_flag = $('#gst_flag').val();
            var cloneIt = false;
            $("input[name=package_id]").each(function() {
                if ($(this).prop('checked') == true) {
                    var packageID = $(this).val();
                    var amount = ($(this).attr('amount'));
                    var amountIncludedDay = parseFloat(amount) * parseInt(differenceDays);
                    calculateTotalAmount();

                }
            });


        }

        function get_checkin_val(val) {
            if (typeof val !== 'undefined' && val != '') {
                var checkOutVal = $('#accomodation_package_checkout_id').val("");
            }
        }

        function getPackageVal(val) {
            if (typeof val !== 'undefined' && val != '') {

                $("input[name='accomodation_package_checkout_id']").prop('checked', false);
                $("input[name='accomodation_package_checkin_id']").prop('checked', false);
                calculateTotalAmount();
            }
        }

        function calculateTotalAmount() {
            console.log("====calculateTotalAmount====");

            var totalAmount = 0;
            var totalDinnerAmount = 0;
            var totTable = $("ul[use=totalAmountTable]");
            $(totTable).find("li[use='rowCloned']").remove();
            // $(totTable).find("li").remove();
            var gst_flag = $('#gst_flag').val();
            var dinnerFlag = false;

            $('input[type=checkbox]:checked,input[type=radio]:checked,#accomodation_package_checkout_id option,#accommodation_room option').each(function() {

                var attr = $(this).attr('amount');
                var operation = $(this).attr('operationmodetype');
                var regtype = $(this).attr('regtype');
                var reg = $(this).attr('reg');
                var qty = $(this).attr('qty');
                console.log('Qty=' + qty);
                var hasTotalAmntFlag = false;

                //alert(reg);

                var package = $(this).attr('package');

                //alert(11)

                if (typeof attr !== typeof undefined && attr !== false) {
                    var amt = parseFloat(attr);


                    if (typeof package !== typeof undefined && package !== false) {



                        // alert(checkedValue);

                        /*var checkInVal = $("input[name='accomodation_package_checkin_id']:checked").val();
                        var checkOutVal = $("input[name='accomodation_package_checkout_id']:checked").val();*/

                        var checkInVal = $('#accomodation_package_checkin_id').val();
                        var checkOutVal = $('#accomodation_package_checkout_id').val();

                        console.log('checkInVal====', checkInVal)
                        // alert(checkInVal);


                        if (checkInVal !== undefined && checkOutVal !== undefined) {
                            const checkInArray = checkInVal.split("/");
                            var checkInID = checkInArray[0];
                            var checkInDate = checkInArray[1];

                            //alert('checkindate',checkInDate);

                            const checkOutArray = checkOutVal.split("/");
                            var checkOutID = checkOutArray[0];
                            var checkOutDate = checkOutArray[1];


                            var date1 = new Date(checkInDate);
                            var date2 = new Date(checkOutDate);

                            // Calculate the difference in milliseconds
                            var differenceMs = Math.abs(date2 - date1);

                            var accommodation_room = $('#accommodation_room').val();
                            if (typeof accommodation_room !== typeof undefined && accommodation_room !== false && !isNaN(accommodation_room)) {
                                var roomQty = accommodation_room;
                            } else {
                                var roomQty = 1;
                            }

                            console.log('room qty=' + roomQty);

                            var differenceDays = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));


                            console.log('accoAmnt=' + differenceDays);
                            var amt = parseFloat(amt) * parseInt(differenceDays) * parseInt(roomQty);

                            if (isNaN(amt)) {
                                amt = 0;
                            }

                            hasTotalAmntFlag = true;
                        }



                    }
                    if (regtype !== 'combo') {

                        if (gst_flag == 1) {
                            if (isNaN(amt)) {

                            } else {
                                var cgstP = <?= $cfg['INT.CGST'] ?>;
                                var cgstAmnt = (amt * cgstP) / 100;

                                var sgstP = <?= $cfg['INT.SGST'] ?>;
                                var sgstAmnt = (amt * sgstP) / 100;

                                var totalGst = cgstAmnt + sgstAmnt;
                                var totalGstAmount = cgstAmnt + sgstAmnt + amt;
                                totalAmount = totalAmount + totalGstAmount;
                            }

                        } else {
                            if (isNaN(amt)) {

                            } else {
                                totalAmount = totalAmount + amt;
                            }


                        }

                        console.log('reg===' + reg);

                        if (reg != undefined && reg == 'reg') {
                            if (isNaN(amt)) {
                                $('#confPrc').text(0.00.toFixed(2));
                            } else {
                                $('#confPrc').text((amt).toFixed(2));
                            }

                        }

                        //alert(reg);

                        if (reg != undefined && reg == 'workshop') {
                            if (isNaN(amt)) {
                                $('#workshopPrc').text(0.00.toFixed(2));
                            } else {
                                $('#workshopPrc').text((amt).toFixed(2));
                            }

                            if (Number(amt) > 0) {
                                $('#wrkshopPrcdiv').show();
                            }

                        } else {
                            $('#wrkshopPrcdiv').hide();

                        }

                        if (reg != undefined && reg == 'accompany') {
                            if (isNaN(amt)) {
                                $('#accompanyPrc').text(0.00.toFixed(2));
                            } else {
                                $('#accompanyPrc').text((amt).toFixed(2));
                            }

                            $('.accompanyPrcdiv').show();

                        } else {
                            $('#accompanyPrcdiv').hide();

                        }

                        if (reg != undefined && reg == 'dinner' && qty != undefined) {

                            var checkedCount = $('.checkboxClassDinner:checked').length;
                            console.log("Number of checked checkboxes: " + checkedCount);

                            var totalDinnerAmounts = checkedCount * amt;

                            $('#dinnerPrc').text((totalDinnerAmounts).toFixed(2));
                            $('.dinnerPrcdiv').show();

                        } else {
                            $('#dinnerPrcdiv').hide();

                        }

                    }


                    console.log(">>amt" + amt + ' ==> ' + totalAmount);

                    var attrReg = $(this).attr('operationMode');
                    var isConf = false;
                    if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'registration_tariff') {
                        isConf = true;
                    }
                    var isMastCls = false;
                    if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId') {
                        isMastCls = true;
                    }

                    // november22 workshop related work by weavers start

                    var isNovWorkshop = false;
                    if (typeof attrReg !== typeof undefined && attrReg !== false && attrReg === 'workshopId_nov') {
                        isNovWorkshop = true;
                    }

                    // november22 workshop related work by weavers end

                    var cloneIt = false;
                    var amtAlterTxt = 'Complimentary';

                    if (amt > 0) {
                        cloneIt = true;
                    } else if (isConf) {
                        cloneIt = true;
                        amtAlterTxt = 'Complimentary'
                    } else if (isMastCls || isNovWorkshop) {
                        cloneIt = true;
                        amtAlterTxt = 'Included in Registration'
                    }

                    if (cloneIt) {
                        //alert($(this).attr('invoiceTitle'));
                        var cloned = $(totTable).find("li[use=rowCloneable]").first().clone();
                        $(cloned).attr("use", "rowCloned");
                        var imageElement = $('<img>').attr('src', "<?= _BASE_URL_ ?>" + $(this).attr('icon'));
                        //alert("<?= _BASE_URL_ ?>"+$(this).attr('icon'));
                        $(cloned).find("span[use=icon]").append(imageElement);
                        $(cloned).find("span[use=invTitle]").append($(this).attr('invoiceTitle'));
                        if (regtype === 'combo') {

                            $(cloned).find("span[use=amount]").text((amt > 0) ? ('Included') : amtAlterTxt);
                        } else {

                            $(cloned).find("span[use=amount]").text((amt > 0) ? (amt).toFixed(2) : amtAlterTxt);
                        }

                        if (reg != 'reg') {
                            // <i class="fas fa-times"></i>
                            var deleteLink = $('<i></i>')
                                .attr('id', 'deleteItem')
                                .attr('class', 'fas fa-times delete-accompany-btn')
                                .attr('reg', reg)
                                .attr('val', $(this).attr('value'))
                                .attr('regClsId', $(this).attr('registrationclassfid'))
                                .css('cursor','pointer')
                                .text('delete')
                            $(cloned).find("span[use=deleteIcon]").append(deleteLink);
                            $(cloned).find("span[use=deleteIcon]").show();
                        }


                        $(cloned).show();
                        $(totTable).append(cloned);


                    }
                    if (regtype !== 'combo') {
                        if (gst_flag == 1) {
                            if (cloneIt) {
                                var cgstP = <?= $cfg['INT.CGST'] ?>;
                                var cgstAmnt = (amt * cgstP) / 100;

                                var sgstP = <?= $cfg['INT.SGST'] ?>;
                                var sgstAmnt = (amt * sgstP) / 100;

                                var totalGst = cgstAmnt + sgstAmnt;
                                var totalGstAmount = cgstAmnt + sgstAmnt + amt;


                                var cloned = $(totTable).children("tfoot").find("tr[use=rowCloneable]").first()
                                    .clone();
                                $(cloned).attr("use", "rowCloned");
                                $(cloned).find("span[use=invTitle]").text("GST 18%");
                                $(cloned).find("span[use=amount]").text((totalGst).toFixed(2));
                                $(cloned).show();
                                $(totTable).children("tbody").append(cloned);
                            }
                        }
                    }
                }

                if ($(this).attr('operationMode') == 'registrationMode' && $(this).attr('use') ==
                    'tariffPaymentMode') {

                    if ($(this).val() == 'ONLINE') {
                        var internetHandling = <?= $cfg['INTERNET.HANDLING.PERCENTAGE'] ?>;
                        var internetAmount = (totalAmount * internetHandling) / 100;
                        totalAmount = totalAmount + internetAmount;

                        console.log(">>amt" + internetAmount + ' ==> ' + totalAmount);



                        var cloned = $(totTable).find("li[use=rowCloneable]").first()
                            .clone();

                        $(cloned).attr("use", "rowCloned");
                        $(cloned).find("span[use=invTitle]").text("Internet Handling Charge");
                        $(cloned).find("span[use=amount]").text((internetAmount).toFixed(2));
                        $(cloned).show();
                        $(totTable).append(cloned);
                    }
                }
            });

            totalAmount = Math.round(totalAmount, 0);
            totalDinnerAmount = Math.round(totalDinnerAmount, 0);




            $(totTable).find("span[use=totalAmount]").text((totalAmount).toFixed(2));
            $("div[use=totalAmount]").find("h3[use=totalAmount]").text((totalAmount).toFixed(2));
            $("div[use=totalAmount]").find("h3[use=totalAmount]").attr('theAmount', totalAmount);
            $("div[use=totalAmount]").show();

            $('#subTotalPrc').text((totalAmount).toFixed(2));

        }


        $(".qr-img").click(function() {
            $(".qr-large").show();
            $(".checkout-main-wrap-box").addClass("blr");
            $(".checkout-main-wrap-inner").addClass("overflow-hidden");
        });
        $(".qr-large button").click(function() {
            $(".qr-large").hide();
            $(".checkout-main-wrap-box").removeClass("blr");
            $(".checkout-main-wrap-inner").removeClass("overflow-hidden");
        });

        $(document).on("click", "#deleteItem", function() {

            var reg = $(this).attr('reg');
            var val = $(this).attr('val');
            var regClsId = $(this).attr('regClsId');

            if (reg === 'workshop') {
                var workshop = 'workshop_id_' + val + '_' + regClsId;

                $('#' + workshop).prop('checked', false);
                calculateTotalAmount();
            }
            if (reg === 'accompany') {
                $('#accompanyCount').prop('checked', false);
                $('.form-control accompany_name').val("");
                calculateTotalAmount();
            }
            $(this).closest('li').remove();

        });
    </script>
</body>

</html>