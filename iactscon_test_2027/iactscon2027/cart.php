<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>NAPCON 2024</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<?php
$offline_payments = json_decode($cfg['PAYMENT.METHOD']);

$sql_qr = array();
$sql_qr['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . "
                                         WHERE `id`!='' AND `title`IN ('QR Code','Online Payment Logo')";
$result = $mycms->sql_select($sql_qr);
$onlinePaymentLogo = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[0]['image'];
$QR_code = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $result[1]['image'];
?>

<body>
    


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
                    $('#paymentDetailsSectionOnline').show();

                } else {
                    if ($(this).attr('act') == 'Upi') {
                        $('.for-upi-only').show();
                        $('.for-neft-rtgs-only').hide();
                    } else {
                        $('.for-upi-only').hide();
                        $('.for-neft-rtgs-only').show();

                    }
                    $('#registrationMode').val('OFFLINE');
                    $('#paymentDetailsSection').show();
                    $('#paymentDetailsSectionOnline').hide();
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

                        if ($(this).hasClass('mandatory')) {
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
            // alert(totalAmount);
            var totalAmount = 0;
            var totalDinnerAmount = 0;
            var subTotalAmount = 0;
            var totalGstAmt = 0;
            var internetAmount =0;
            var totalinternetAmount =0;
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

                // alert(attr);

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
                                totalGstAmt = totalGstAmt+totalGst;
                                subTotalAmount =subTotalAmount+amt;
                            }

                        }else if(gst_flag == 2){
                            var cgstP = <?= $cfg['INT.CGST'] ?>;
                            // var cgstAmnt = (amt * cgstP) / 100;

                            var sgstP = <?= $cfg['INT.SGST'] ?>;
                            // var sgstAmnt = (amt * sgstP) / 100;
                            var gstPercent = cgstP + sgstP; // 18
                            var totalGst = (amt * gstPercent) / (100 + gstPercent);
                            var totalGstAmount = cgstAmnt + sgstAmnt + amt;
                                
                            totalAmount = totalAmount + amt;
                            totalGstAmt = totalGstAmt+totalGst;
                            subTotalAmount =subTotalAmount+amt-totalGst;
                        } else {
                            if (isNaN(amt)) {

                            } else {
                                totalAmount = totalAmount + amt;
                                subTotalAmount =totalAmount;
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
                        // alert($(this).attr('invoiceName'));
                           if(cloneIt) {
                                const el = $(this);

                                const title  = el.attr('invoiceTitle');
                                const name   = el.attr('invoiceName');
                                const amount = parseFloat(el.attr('amount')).toFixed(2);
                                const icon   = el.attr('icon');
                                const reg    = el.attr('reg');
                                // alert(name);
                                const cloned = $(totTable).find("li[use=rowCloneable]").first().clone();
                                $(cloned).attr("use", "rowCloned");

                                // set icon
                                if(icon) {
                                    const img = $('<img>').attr('src', "<?= _BASE_URL_ ?>" + icon);
                                    $(cloned).find("[use=icon]").append(img);
                                }

                                // set title, name, amount
                               cloned.find("[use=invTitle]").html(`${title}<br>${name}`);
                                $(cloned).find("[use=invName]").text(name);
                                $(cloned).find("[use=amount]").text("₹ " + amount);

                                // delete button if needed
                                if(reg != 'reg') {
                                    const deleteBtn = $('<i></i>')
                                        .addClass('fas fa-times delete-accompany-btn')
                                        .attr('reg', reg)
                                        .attr('val', el.val())
                                        .text('delete');
                                    $(cloned).find("[use=deleteIcon]").append(deleteBtn).show();
                                }

                                $(cloned).show();
                               const subtotalRow = $(totTable).find('li[use=subtotalRow]');
                               $(cloned).insertBefore(subtotalRow);
                            }
                            if (reg != 'reg') {
                            // <i class="fas fa-times"></i>
                            var deleteLink = $('<i></i>')
                                .attr('id', 'deleteItem')
                                .attr('class', 'fas fa-times delete-accompany-btn')
                                .attr('reg', reg)
                                .attr('val', $(this).attr('value'))
                                .attr('regClsId', $(this).attr('registrationclassfid'))
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
            subTotalAmount = Math.round(subTotalAmount, 0);
            totalGstAmt= Math.round(totalGstAmt, 0);
            // totalinternetAmount =totalAmount;
            // $("input[type=radio][use=payment_mode_select]").click(function() {
            //     var val = $(this).val(); // Get selected payment value
            //     if (val === 'Card') {
            //         var internetHandling = <?= $cfg['INTERNET.HANDLING.PERCENTAGE'] ?>;
            //          internetAmount = (totalAmount * internetHandling) / 100;
            //          totalinternetAmount =(totalAmount+internetAmount);
            //          if(internetAmount>0){
            //                 $('[use=internetAmount]').text(
            //                     '₹ ' + internetAmount.toLocaleString('en-IN')
            //                 );
            //         }
                   
            //     }else{
            //         var internetHandling = <?= $cfg['INTERNET.HANDLING.PERCENTAGE'] ?>;
            //          internetAmount = 0;
            //           totalinternetAmount =totalAmount;
            //          if(internetAmount>0){
            //                 $('[use=internetAmount]').text(
            //                     '₹ ' + internetAmount.toLocaleString('en-IN')
            //                 );
            //         }
            //     }
            // })
            // Calculate internet charge based on selected mode
                var selectedPayment = $("input[type=radio][use=payment_mode_select]:checked").val();

                var internetHandling = <?= $cfg['INTERNET.HANDLING.PERCENTAGE'] ?>;
                var internetAmount = 0;

                if (selectedPayment === 'Card') {
                    internetAmount = (totalAmount * internetHandling) / 100;
                    totalinternetAmount = totalAmount + internetAmount;
                    $('.internetcharge').show();
                } else {
                    totalinternetAmount = totalAmount;
                   $('.internetcharge').each(function () {
                        this.style.setProperty('display', 'none', 'important');
                    });
                }

                $('[use=internetAmount]').text(
                    '₹ ' + internetAmount.toLocaleString('en-IN')
                );
            
            $(totTable).find("span[use=totalAmount]").text((totalAmount).toFixed(2));
            $("div[use=totalAmount]").find("h3[use=totalAmount]").text((totalAmount).toFixed(2));
            $("div[use=totalAmount]").find("h3[use=totalAmount]").attr('theAmount', totalAmount);
            $("div[use=totalAmount]").show();
            $('[use=subtotalAmount]').text(
                '₹ ' + subTotalAmount.toLocaleString('en-IN')
            );
            
            if(totalGstAmt>0){
            $('[use=totalGstAmount]').text(
                '₹ ' + totalGstAmt.toLocaleString('en-IN')
            );
            }else{
                                
             $('[use=totalGstAmount]').text(
                'Included'
            );

            }
            // totalinternetAmount =totalAmount;
            $('[use=totalAmountpay]').text(
                '₹ ' + totalinternetAmount.toLocaleString('en-IN')
            );
           $('#subTotalPrc').text(
                '₹ ' + totalinternetAmount.toLocaleString('en-IN')
            );

           
        }


        $(".qr-img").click(function() {
            $(".qr-large").show();
            // $(".checkout-main-wrap-box").addClass("blr");
            // $(".checkout-main-wrap-inner").addClass("overflow-hidden");
        });
        $(".qr-large button").click(function() {
            $(".qr-large").hide();
            // $(".checkout-main-wrap-box").removeClass("blr");
            // $(".checkout-main-wrap-inner").removeClass("overflow-hidden");
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