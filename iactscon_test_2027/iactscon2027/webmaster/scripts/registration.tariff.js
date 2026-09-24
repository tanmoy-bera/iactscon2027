/**********************************************************************/
/*                       REGISTRATION                          */
/**********************************************************************/

var REGISTRATION_IS_FACULTY = false;
var REGISTRATION_IS_GUEST = false;

$(document).ready(function () {

	$("input[type=checkbox][operationMode=MakeZeroValue]").each(function () {
		$(this).click(function () {
			$("input[type=checkbox][operationMode=MakeZeroValue]").prop("checked", false);
			if ($(this).prop("checked") == "true") {
				$(this).prop("checked", true);
			}
			else {
				$(this).prop("checked", true);

			}
		});
	});

	$("input[type=submit][operationMode=registrationMode]").click(function () {
		return onSubmitAction(function () {
			checked = $("input[type=checkbox][name=regsitaion_mode]:checked").length;
			if (!checked) {

				$('input[type=checkbox][operationMode=MakeZeroValue]').css('outline-color', '#D41000');
				$('input[type=checkbox][operationMode=MakeZeroValue]').css('outline-style', 'solid');
				$('input[type=checkbox][operationMode=MakeZeroValue]').css('outline-width', 'thin');
				$("html, body").animate({ scrollTop: 50 }, 1000);
				alert("You must check regsitration mode.");
				return false;
			}
			else {
				return true;
			}
		});
	});


    $(document).on('change', "input[type=radio][operationMode='regCutoff']", function (event) {
		$("input[type=radio][operationMode=registration_tariff]").prop("checked", false);
		$("input[type=radio][operationMode=registration_tariff]").attr("chkStat", "false");
		$("input[type=checkbox][operationMode=workshopId]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId]").attr("chkStat", "false");
		$("input[type=checkbox][operationMode=workshopId_nov]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId_nov]").attr("chkStat", "false");
		
        var selectedCutoffId = $(this).val();  
	    var currency = $(this).data('currency'); // data-currency
       var amount = parseFloat($(this).data('amount')) || 0; // data-amount
  
	    $('#add_cutoff_id').val(selectedCutoffId);
	    $('#registration_cutoff').val(selectedCutoffId);


        // Update the display dynamically
		$(".dinner_check").each(function () {
			if ($(this).attr("use") == selectedCutoffId) {
				$(this).show(); // show the matching label
			} else {
				$(this).hide(); // hide everything else
			}
		});
        $('.accompanyTariffAmount span').text(currency + " " + amount.toFixed(2));
		$('#accompanyAmount').val(amount);
		$('#accompanyTariffAmount').val(amount);
		$('#accompanyCounts').val(1);
    
      $.ajax({
		url: jsWemaster_BASE_URL + 'includes/popup.php',
		type: 'POST',
		data: {
			selectedCutoffIdHotel: selectedCutoffId
		},
		success: function (response) {
			var temp = $('<div>').html(response);
			
			// Store the original carousel structure
			var newHotelBoxes = temp.find('#hotelBoxesContainer').html();
			
			if (newHotelBoxes && newHotelBoxes.trim() !== '') {
				// Replace the content
				$('#hotelBoxesContainer').html(newHotelBoxes);
				
				// CRITICAL: Re-initialize ALL carousels inside hotel boxes
				$('.hotel_box .hotel_link_owl').each(function() {
					var $carousel = $(this);
					if ($carousel.hasClass('owl-loaded')) {
						$carousel.trigger('destroy.owl.carousel');
						$carousel.removeClass('owl-loaded');
					}
					$carousel.owlCarousel({
						items: 1,
						loop: false,
						margin: 10,
						nav: false,
						dots: true
					});
				});
				
				// Show first hotel box
				$('.hotel_box').hide();
				$('.hotel_box').first().show();
			}
			
			initializeAccoPlugins();
			calculateRegistrationTariff();
		}
	});
		calculateRegistrationTariff();
	});
		/////////////spot///////////

	   $(document).on('change', "select[operationMode='regCutoff']", function (event) {
		$("input[type=radio][operationMode=registration_tariff]").prop("checked", false);
		$("input[type=radio][operationMode=registration_tariff]").attr("chkStat", "false");
		$("input[type=checkbox][operationMode=workshopId]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId]").attr("chkStat", "false");
		$("input[type=checkbox][operationMode=workshopId_nov]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId_nov]").attr("chkStat", "false");
		
        var selectedCutoffId = $(this).val();  
	     var selectedOption = $(this).find('option:selected');
		var currency = selectedOption.data('currency');
		var amount = parseFloat(selectedOption.data('amount')) || 0;
	    $('#add_cutoff_id').val(selectedCutoffId);
	    $('#registration_cutoff').val(selectedCutoffId);


        // Update the display dynamically
		$(".dinner_check").each(function () {
			if ($(this).attr("use") == selectedCutoffId) {
				$(this).show(); // show the matching label
			} else {
				$(this).hide(); // hide everything else
			}
		});
        $('.accompanyTariffAmountSpot span').text(currency + " " + amount.toFixed(2));
		$('#accompanyAmountSpot').val(amount);
		$('#accompanyTariffAmountSpot').val(amount);
		$('#accompanyCountsSpot').val(1);
    
      $.ajax({
		url: jsWemaster_BASE_URL + 'includes/popup.php',
		type: 'POST',
		data: {
			selectedCutoffIdHotel: selectedCutoffId
		},
		success: function (response) {
			var temp = $('<div>').html(response);
			
			// Store the original carousel structure
			var newHotelBoxes = temp.find('#hotelBoxesContainer').html();
			
			if (newHotelBoxes && newHotelBoxes.trim() !== '') {
				// Replace the content
				$('#hotelBoxesContainer').html(newHotelBoxes);
				
				// CRITICAL: Re-initialize ALL carousels inside hotel boxes
				$('.hotel_box .hotel_link_owl').each(function() {
					var $carousel = $(this);
					if ($carousel.hasClass('owl-loaded')) {
						$carousel.trigger('destroy.owl.carousel');
						$carousel.removeClass('owl-loaded');
					}
					$carousel.owlCarousel({
						items: 1,
						loop: false,
						margin: 10,
						nav: false,
						dots: true
					});
				});
				
				// Show first hotel box
				$('.hotel_box').hide();
				$('.hotel_box').first().show();
			}
			
			initializeAccoPlugins();
			calculateRegistrationTariff();
		}
	});
		calculateRegistrationTariff();
	});
	/////////////spot///////////
	$(document).on('change', "input[type=radio][operationMode='workshopCutoff']", function () {
		// Reset checkboxes (your existing logic)
		$("input[type=checkbox][operationMode=workshopId]").prop("checked", false).attr("chkStat", "false");
		$("input[type=checkbox][operationMode=workshopId_nov]").prop("checked", false).attr("chkStat", "false");

		// Get selected cutoff ID
		let selectedworkCutoffId = $(this).val();

		// Hide all workshop sections
		$(".work_category").hide();

		// Show only selected one
		$("#work_" + selectedworkCutoffId).show();
        $("#workSPot_" + selectedworkCutoffId).show();

		calculateRegistrationTariff();
	});
	$("select[operationMode=accomodationPackage]").change(function () {
		var checkInDate = $(this).children("option:selected").attr("checkInDate");
		var checkOutDate = $(this).children("option:selected").attr("checkOutDate");
		$("input[type=hidden][name=accommodation_checkIn]").attr("value", checkInDate);
		$("input[type=hidden][name=accommodation_checkOut]").attr("value", checkOutDate);
	});

	$("input[type=radio][operationMode=registration_tariff]").each(function () {
		$(this).click(function () {

			var regClsfId = $(this).val();

			$(".work_category .cus_check").each(function () {

				if ($(this).attr("use") == regClsfId) {
					$(this).show(); // show the matching label
				} else {
					$(this).hide(); // hide everything else
				}

				// if ($(this).attr("useworkshop") == regClsfId) {
				// 	$(this).show(); // show the matching label
				// } else {
				// 	$(this).hide(); // hide everything else
				// }
			});

			var regType = $(this).attr('registrationType');
			$("input[type=radio][operationMode=registration_tariff]").prop("checked", false);

			$("input[type=checkbox][operationMode=workshopId]").prop("checked", false);
			$("input[type=checkbox][operationMode=workshopId]").attr("chkStat", "false");

			$("input[type=checkbox][operationMode=workshopId_nov]").prop("checked", false);
			$("input[type=checkbox][operationMode=workshopId_nov]").attr("chkStat", "false");

			$("input[type=checkbox][operationMode=dinner]").prop("checked", false);

			$("tr[operetionMode=checkInCheckOutTr]").css("display", "none");

			$("tr[operetionMode=workshopTariffTr]").hide();

			$("table[use=AccommodationDate]").hide();

			$("tr[operetionMode=checkInCheckOutTr]").hide();

			$("tr[operetionMode=dinnerTariffTr][use=FreeDinners]").hide();
			$("tr[operetionMode=dinnerTariffTr][use=PaidDinners]").hide();
			$("input[type=checkbox][operationMode=dinner]").prop("checked", false);

			if ($(this).attr("chkStat") == "true") {
				$(this).attr("chkStat", "false");

				$("tr[operetionMode=workshopTariffTr]").css("display", "none");
				$("tr[operetionMode=workshopTariffTr][use=ai]").css("display", "none");
				$("tr[operetionMode=workshopTariffTr][use=na]").fadeIn();
			}
			else {

				if (regType == 'COMBO') {
					var packageId = $(this).attr("accommodationPackageId");
					$("input[type=hidden][name=accommodation_package_id]").attr("value", packageId);

					$("tr[operetionMode=checkInCheckOutTr]").hide();
					$("tr[operetionMode=checkInCheckOutTr][use='" + packageId + "']").show();

					var selectScope = $("tr[operetionMode=checkInCheckOutTr][use='" + packageId + "']").find("select[operationMode=accomodationPackage]");
					var checkInDate = $(selectScope).find("option:selected").attr("checkInDate");
					var checkOutDate = $(selectScope).find("option:selected").attr("checkOutDate");

					//var checkInDate =$("select[operationMode=accomodationPackage]").find("option:selected").attr("checkInDate");
					//var checkOutDate =$("select[operationMode=accomodationPackage]").find("option:selected").attr("checkOutDate");

					$("input[type=hidden][name=accommodation_checkIn]").attr("value", checkInDate);
					$("input[type=hidden][name=accommodation_checkOut]").attr("value", checkOutDate);

					$("tr[operetionMode=dinnerTariffTr][use=FreeDinners]").show();
					$("tr[operetionMode=dinnerTariffTr][use=PaidDinners]").hide();
				}
				else {
					$("tr[operetionMode=dinnerTariffTr][use=FreeDinners]").hide();
					$("tr[operetionMode=dinnerTariffTr][use=PaidDinners]").show();
				}

				if (regClsfId == 3 || regClsfId == 7 || regClsfId == 8 || regClsfId == 9 || regClsfId == 10 || regClsfId == 11 || regClsfId == 12 || regClsfId == 13 || regClsfId == 14 || regClsfId == 15 || regClsfId == 16 || regClsfId == 17 || regClsfId == 18) {
					$(this).attr("chkStat", "true");
					$(this).prop("checked", true);
					$("tr[operetionMode=workshopTariffTr]").css("display", "none");
					// $("tr[operetionMode=workshopTariffTr][use=ai]").fadeIn();

					$("tr[operetionMode=workshopTariffTr][use=" + regClsfId + "]").fadeIn();

					$("tr[operetionMode=dinnerTariffTr][use=FreeDinners]").show();
					$("tr[operetionMode=dinnerTariffTr][use=PaidDinners]").hide();


					if (regClsfId != 3) {
						var packageId = $(this).attr("accommodationPackageId");
						$("table[use=AccommodationDate]").show();
						$("input[type=hidden][name=accommodation_package_id]").attr("value", packageId);
						$("tr[operetionMode=checkInCheckOutTr]").hide();
						$("tr[operetionMode=checkInCheckOutTr][use='" + packageId + "']").show();
					}
				}
				else {

					$(this).attr("chkStat", "true");
					$(this).prop("checked", true);

					$("tr[operetionMode=workshopTariffTr][use=" + regClsfId + "]").fadeIn();

					$("tr[operetionMode=dinnerTariffTr][use=FreeDinners]").hide();
					$("tr[operetionMode=dinnerTariffTr][use=PaidDinners]").show();
				}

			}

			calculateRegistrationTariff();
		});
	});

	$("tr[operetionMode=workshopTr]").hide();
	$("tr[operetionMode=workshopTariffTr][use=na]").fadeIn();

	$("input[type=checkbox][operationMode=workshopId]").each(function () {
		$(this).click(function (event) {
			if (REGISTRATION_IS_FACULTY || REGISTRATION_IS_GUEST) {
				event.preventDefault()
			}
			else {
				//===== commented out the below line to allow multiple selection of workshops  17 JuL 2025 ============
				// var checkedObj  = $(this).is(":checked");

				// $("input[type=checkbox][operationMode=workshopId]").prop("checked",false);

				// if(checkedObj)
				// {
				// 	$(this).attr("chkStat","true");
				// 	$(this).prop("checked",true);
				// }
				// else
				// {
				// 	$(this).attr("chkStat","false");
				// 	$(this).prop("checked",false);
				// }
				//  ============================= X ============================


			}
         var currentCheckbox = $(this);

        // Only run restriction when checking
        if (currentCheckbox.is(":checked")) {

            var currentType = currentCheckbox.data("type");
            var currentDate = currentCheckbox.data("date");

            $("input[type=checkbox][operationMode=workshopId]:checked").each(function () {

                if (this === currentCheckbox[0]) return;

                var otherType = $(this).data("type");
                var otherDate = $(this).data("date");

                // SAME type AND SAME date → uncheck previous
                if (otherType == currentType && otherDate == currentDate) {
                    $(this).prop("checked", false);
                }
            });
        }
			calculateRegistrationTariff();
		});
	});
    $("input[type=checkbox][operationMode=dinnerTariffTr]").each(function () {
		$(this).click(function (event) {
			
     

			calculateRegistrationTariff();
		});
	});

	$("input[type=checkbox][operationMode=workshopId_nov]").each(function () {
		$(this).click(function (event) {
			//===== commented out the below line to allow multiple selection of workshops ============
			// $("input[type=checkbox][operationMode=workshopId_nov]").attr("chkStat","false");
			if (REGISTRATION_IS_FACULTY || REGISTRATION_IS_GUEST) {
				event.preventDefault()
			}
			else {
				var checkedObj = $(this).is(":checked");
				//alert(checkedObj);

				//===== commented out the below line to allow multiple selection of workshops 17 JuL 2025 ============
				// $("input[type=checkbox][operationMode=workshopId_nov]").prop("checked",false);

				// if(checkedObj)
				// {
				// 	$(this).attr("chkStat","true");
				// 	$(this).prop("checked",true);
				// }
				// else
				// {
				// 	$(this).attr("chkStat","false");
				// 	$(this).prop("checked",false);
				// }

				//  ============================= X ============================

			}

			calculateRegistrationTariff();
		});
	});

	$("input[type=checkbox][operationMode=workshopId_postConference]").each(function () {
		$(this).click(function (event) {
			if (REGISTRATION_IS_FACULTY || REGISTRATION_IS_GUEST) {
				event.preventDefault()
			}
			else {
				//===== commented out the below line to allow multiple selection of workshops 17 JuL 2025 ============

				// $("input[type=checkbox][operationMode=workshopId_postConference]").prop("checked", false);
				// if ($(this).attr("chkStat") == "true") {
				// 	$(this).attr("chkStat", "false");
				// }
				// else {
				// 	$(this).attr("chkStat", "true");
				// 	$(this).prop("checked", true);
				// }
				// ============================= X ============================
			}

			calculateRegistrationTariff();
		});
	});

	$("input[type=checkbox][operationMode=dinner]").each(function () {
		$(this).click(function () {
			calculateRegistrationTariff();
		});
	});

	$("input[forType=emailValidate]").on('blur', function () {
		/// BACKEND EMAIL VALIDATION
		initiateEmailValidation(this);
	});

	$("input[forType=mobileValidate]").on('blur', function () {
		/// BACKEND MOBILE VALIDATION
		mobile = $(this).val();
		checkMobileValidation(mobile, jsWemaster_BASE_URL);
	});
});


function validateRegTariffform() {
	var status = 0;
	var abstructId = $("#abstructId").val();
	var redregTariffId = $("input[type=checkbox][operationMode=residential_tariff]:checked").length;
	var regTariffId = $("input[type=radio][operationMode=registration_tariff]:checked").length;
	var workClassId = $("input[type=checkbox][operationMode=workshop_tariff]:checked").length;
	var comboTarriffId = $("input[type=checkbox][operationMode=comboTarriff]:checked").length;
	if (regTariffId != 0 || redregTariffId != 0 || workClassId != 0 || comboTarriffId != 0) {
		status = 1;
	}
	if (status == 0) {
		alert("Please Select Conference / Tutorial Tariff");
		return false;
	}

	var accessValidation = formValidation();
	if (accessValidation == 1) {
		return false;
	}

	return true;
}

// function calculateRegistrationTariff() {
//     var billHTML = "";
//     var total = 0;
//     var currency = "INR";
//     var dinnerAmount = 0;
//     var selecetedCutoff = $("input[type=radio][operationMode='regCutoff']:checked").val();
//     var selecetedWorkshopCutoff = $("input[type=radio][operationMode='workshopCutoff']:checked").val();
//     var discountAmount = 0;
//     var gst_flag = parseFloat($('#gst_flag').val());
//     var service_tax_percentage = parseFloat($('#service_tax_percentage').val());
    
//     // Check if GST elements exist
//     if (isNaN(gst_flag)) gst_flag = 0;
//     if (isNaN(service_tax_percentage)) service_tax_percentage = 0;
    
//     // Registration Tariff
//     if (!$("#workshopToggle").is(":checked")) {
//         $("input[type=radio][operationMode=registration_tariff]:checked").each(function() {
//             var tariffAmount = parseFloat($(this).attr("tariffAmount"));
//             if (isNaN(tariffAmount)) tariffAmount = 0;
//             total += tariffAmount;
//             currency = $(this).attr("currency") || "INR";
//             var labelText = $(this)
//                 .closest("label")
//                 .find(".checkmark")
//                 .clone()
//                 .children("i")
//                 .remove()
//                 .end()
//                 .text()
//                 .trim() || "Registration";
            
//             billHTML += `
//                 <p class="frm-head text_dark d-flex justify-content-between align-items-center">
//                     ${escapeHtml(labelText)}
//                     <span class="text-white">${currency} ${tariffAmount.toFixed(2)}</span>
//                 </p>
//             `;
//         });
//     }
    
//     // Workshop Tariff
//     $("input[type=checkbox][operationMode=workshopId]:checked").each(function() {
//         var tariffAmount = parseFloat($(this).attr("tariffAmount"));
//         if (isNaN(tariffAmount)) tariffAmount = 0;
//         total += tariffAmount;
//         currency = $(this).attr("currency") || "INR";
//         var labelText = $(this)
//             .closest("label")
//             .find(".checkmark")
//             .clone()
//             .children("i")
//             .remove()
//             .end()
//             .text()
//             .trim() || "Workshop";
        
//         billHTML += `
//             <p class="frm-head text_dark d-flex justify-content-between align-items-center">
//                 ${escapeHtml(labelText)}
//                 <span class="text-white">${currency} ${tariffAmount.toFixed(2)}</span>
//             </p>
//         `;
//     });
    
//     // Dinner Tariff
//     $("input[type=checkbox][operationMode=dinnerTariffTr]:checked").each(function() {
//         var tariffAmount = parseFloat($(this).attr("tariffAmount"));
//         if (isNaN(tariffAmount)) tariffAmount = 0;
//         total += tariffAmount;
//         dinnerAmount += tariffAmount;
//         currency = $(this).attr("currency") || "INR";
//         var labelText = $(this)
//             .closest("label")
//             .find(".checkmark")
//             .clone()
//             .children("i")
//             .remove()
//             .end()
//             .text()
//             .trim() || "Dinner";
        
//         billHTML += `
//             <p class="frm-head text_dark d-flex justify-content-between align-items-center">
//                 ${escapeHtml(labelText)}
//                 <span class="text-white">${currency} ${tariffAmount.toFixed(2)}</span>
//             </p>
//         `;
//     });
    
//     // Accompanying persons
//     $(".accm_add_acco_box").each(function () {
// 		const nameInput = $(this).find("input[type=text]").first(); // get the first input
// 		const checkbox = $(this).find("input.accompanyCount");
// 		const amountElem = $(this).find(".accompanyTariffAmount span");
// 		// Parse the amount from the <h6> text
// 		let amount = parseFloat(amountElem.text().replace(/[^0-9\.]+/g,"")) || 0;

// 		let nameVal = nameInput.val() || ""; // default to empty string if undefined

// 		if (nameVal.trim() !== "") {
// 			checkbox.prop("checked", true); // auto-check
// 			total += amount; // add this box amount
// 			currency = amountElem.text().replace(/[0-9\.\s]/g,"") || currency; // get currency symbol
// 			var labelText = 'Accompanying Person';


// 			billHTML += `
// 			<p class="frm-head text_dark d-flex justify-content-between align-items-center">
// 				${labelText}
// 				<span class="text-white">${amount.toFixed(2)}</span>
// 			</p>
// 		`;
// 		} else {
// 			checkbox.prop("checked", false); // uncheck if empty
// 		}
// 	});
//     //Spot Accompanying persons
//     $(".accm_add_acco_box_spot").each(function () {
// 		const nameInput = $(this).find("input[type=text]").first(); // get the first input
// 		const checkbox = $(this).find("input.accompanyCountSpot");
// 		const amountElem = $(this).find(".accompanyTariffAmountSpot span");
// 		// Parse the amount from the <h6> text
// 		let amount = parseFloat(amountElem.text().replace(/[^0-9\.]+/g,"")) || 0;

// 		let nameVal = nameInput.val() || ""; // default to empty string if undefined

// 		if (nameVal.trim() !== "") {
// 			checkbox.prop("checked", true); // auto-check
// 			total += amount; // add this box amount
// 			currency = amountElem.text().replace(/[0-9\.\s]/g,"") || currency; // get currency symbol
// 			var labelText = 'Accompanying Person';


// 			billHTML += `
// 			<p class="frm-head text_dark d-flex justify-content-between align-items-center">
// 				${labelText}
// 				<span class="text-white">${amount.toFixed(2)}</span>
// 			</p>
// 		`;
// 		} else {
// 			checkbox.prop("checked", false); // uncheck if empty
// 		}
// 	});
    
//     // Workshop Nov
//     $("input[type=checkbox][operationMode=workshopId_nov]").each(function() {
//         if ($(this).is(":checked")) {
//             var tariffCol = $(this).parent().closest("tr").find("td[use=workshopTariff][cutoff='" + selecetedWorkshopCutoff + "']");
//             if (tariffCol.length) {
//                 var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));
//                 if (isNaN(tariffAmount)) tariffAmount = 0;
//                 total += tariffAmount;
//             }
//         }
//     });
    
//     // Workshop Post Conference
//     $("input[type=checkbox][operationMode=workshopId_postConference]").each(function() {
//         if ($(this).is(":checked")) {
//             var tariffCol = $(this).parent().closest("tr").find("td[use=workshopTariff][cutoff='" + selecetedWorkshopCutoff + "']");
//             if (tariffCol.length) {
//                 var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));
//                 if (isNaN(tariffAmount)) tariffAmount = 0;
//                 total += tariffAmount;
//             }
//         }
//     });
    
//     // Dinner
//     $("input[type=checkbox][operationMode=dinner]").each(function() {
//         if ($(this).is(":checked")) {
//             var tariffCol = $(this).parent().closest("tr").find("td[use=dinnerTariff][cutoff='" + selecetedCutoff + "']");
//             if (tariffCol.length) {
//                 var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));
//                 if (isNaN(tariffAmount)) tariffAmount = 0;
//                 total += tariffAmount;
//             }
//         }
//     });
    
//     // ============ ACCOMMODATION - Add to bill summary ============
//     var accommodationTotal = 0;
//     var accommodationHTML = "";
    
//     // Get all checked accommodation packages
//     $("input[name='package_id[]']:checked").each(function() {
//         var $pkg = $(this);
//         var hotelId = $pkg.data('hotel-id');
//         var roomId = $pkg.data('room-id');
//         var packageName = $pkg.data('package-name') || '';
//         var roomName = $pkg.data('room-name') || '';
//         var amount = parseFloat($pkg.attr('amount'));
        
//         // If amount is not set, calculate it
//         if (isNaN(amount) || amount === 0) {
//             var baseAmount = parseFloat($pkg.data('base-amount')) || 0;
//             var checkInVal = $('#accomodation_package_checkin_id').val();
//             var checkOutVal = $('#accomodation_package_checkout_id').val();
//             var nights = 1;
//             if (checkInVal && checkOutVal) {
//                 var checkInDate = checkInVal.split("/")[1];
//                 var checkOutDate = checkOutVal.split("/")[1];
//                 nights = Math.ceil((new Date(checkOutDate) - new Date(checkInDate)) / (1000 * 60 * 60 * 24));
//                 nights = Math.max(1, nights);
//             }
            
//             // Get quantity
//             var quantity = 1;
//             var $qtyInput = $("input.accmomdation-qty[data-hotel-id='" + hotelId + "'][data-room-id='" + roomId + "']");
//             if ($qtyInput.length && !$qtyInput.prop('disabled')) {
//                 quantity = parseInt($qtyInput.val(), 10) || 1;
//             }
            
//             var isSharing = packageName.toLowerCase() === 'sharing';
//             var finalQuantity = isSharing ? 1 : quantity;
//             amount = baseAmount * nights * finalQuantity;
//             $pkg.attr('amount', amount.toFixed(2));
//         }
        
//         if (amount > 0) {
//             accommodationTotal += amount;
            
//             // Get hotel name (from the active hotel box)
//             var hotelName = '';
//             if (hotelId) {
//                 var $hotelBox = $('#hotel_' + hotelId);
//                 if ($hotelBox.length) {
//                     hotelName = $hotelBox.closest('.registration-pop_body_box_inner').find('.hotel_tab_btn[data-tab="' + hotelId + '"] .checkmark').text().trim() || 'Hotel';
//                 }
//             }
            
//             // Get nights for display
//             var checkInVal = $('#accomodation_package_checkin_id').val();
//             var checkOutVal = $('#accomodation_package_checkout_id').val();
//             var nights = 1;
//             if (checkInVal && checkOutVal) {
//                 var checkInDate = checkInVal.split("/")[1];
//                 var checkOutDate = checkOutVal.split("/")[1];
//                 nights = Math.ceil((new Date(checkOutDate) - new Date(checkInDate)) / (1000 * 60 * 60 * 24));
//                 nights = Math.max(1, nights);
//             }
            
//             // Get quantity for display
//             var quantity = 1;
//             var $qtyInput = $("input.accmomdation-qty[data-hotel-id='" + hotelId + "'][data-room-id='" + roomId + "']");
//             if ($qtyInput.length && !$qtyInput.prop('disabled')) {
//                 quantity = parseInt($qtyInput.val(), 10) || 1;
//             }
            
//             var isSharing = packageName.toLowerCase() === 'sharing';
//             var displayText = '';
//             var invoiceName = $pkg.attr('invoiceName') || packageName;
            
//             if (isSharing) {
//                 displayText = hotelName + ' - '  + invoiceName + ' (1 Person / ' + nights + ' Night' + (nights > 1 ? 's' : '') + ')';
//             } else {
//                 displayText = hotelName + ' - ' + invoiceName + ' (' + quantity + ' Room' + (quantity > 1 ? 's' : '') + ' / ' + nights + ' Night' + (nights > 1 ? 's' : '') + ')';
//             }
            
//             accommodationHTML += `
//                 <p class="frm-head text_dark d-flex justify-content-between align-items-center accommodation-bill-item" data-hotel-id="${hotelId}" data-room-id="${roomId}">
//                     ${escapeHtml(displayText)}
//                     <span class="text-white">${currency} ${amount.toFixed(2)}</span>
//                 </p>
//             `;
//         }
//     });
    
//     // Add accommodation section to bill if there are any items
//     if (accommodationHTML) {
//         // billHTML += '<hr class="my-2">';
//         // billHTML += '<p class="frm-head text_dark font-weight-bold">Accommodation</p>';
//         billHTML += accommodationHTML;
//         total += accommodationTotal;
//     }
//          // Discount
//          discountAmount = $("input[type=number][operationMode=discountAmount]").val();
// 	    discountAmountSpot = $("input[type=number][operationMode=discountAmt]").val();


//     if (discountAmount > 0 && !isNaN(discountAmount) && discountAmount > 0 && total > discountAmount) {
//         total -= discountAmount;
//         billHTML += `
//             <hr class="my-2">
//             <p class="frm-head text_dark d-flex justify-content-between align-items-center text-success">
//                 Discount Applied
//                 <span class="text-white">- ${currency} ${parseFloat(discountAmount).toFixed(2)}</span>
//             </p>
//         `;
//     }
//     if (discountAmountSpot > 0 && !isNaN(discountAmountSpot) && discountAmountSpot > 0 && total > discountAmountSpot) {
//         total -= discountAmountSpot;
//         billHTML += `
//             <hr class="my-2">
//             <p class="frm-head text_dark d-flex justify-content-between align-items-center text-success">
//                 Discount Applied
//                 <span class="text-white">- ${currency} ${parseFloat(discountAmountSpot).toFixed(2)}</span>
//             </p>
//         `;
//     }
//     // GST Calculation
//     if (gst_flag == 1) {
//         var gst = (total * service_tax_percentage) / 100;
//         var amt = (gst + total);
        
//         $("span[use=GSTTOTAMT]").html((amt).toFixed(2));
//         $("span[use=TOTAMTGST]").html((gst).toFixed(2));
        
//         billHTML += `
//             <hr class="my-2">
//             <p class="frm-head text_dark d-flex justify-content-between align-items-center">
//                 GST (${service_tax_percentage}%)
//                 <span class="text-white">${currency} ${gst.toFixed(2)}</span>
//             </p>
//             <hr class="my-2">
//             <p class="frm-head text_dark font-weight-bold d-flex justify-content-between align-items-center">
//                 Total Amount
//                 <span class="text-white font-weight-bold">${currency} ${amt.toFixed(2)}</span>
//             </p>
//         `;
//     } else {
//         var amt = total;
//         $("span[use=GSTTOTAMT]").html((amt).toFixed(2));
//         billHTML += `
//             <hr class="my-2">
//             <p class="frm-head text_dark font-weight-bold d-flex justify-content-between align-items-center">
//                 Total Amount
//                 <span class="text-white font-weight-bold">${currency} ${amt.toFixed(2)}</span>
//             </p>
//         `;
//     }
    
//     // Update all spans
//     $("span[use=TOTCUR]").html(currency);
//     $("span[use=TOTAMT]").html((total).toFixed(2));
//     $("span[use=TOTDinner]").html((dinnerAmount).toFixed(2));
//     $("#billSummaryItems").html(billHTML);
// 	$("#billSummaryItemsSpot").html(billHTML);

// }
function calculateRegistrationTariff() {
    var billHTML = "";
    var total = 0;
    var currency = "INR";
    var dinnerAmount = 0;
    var selecetedCutoff = $("input[type=radio][operationMode='regCutoff']:checked").val();
    var selecetedWorkshopCutoff = $("input[type=radio][operationMode='workshopCutoff']:checked").val();
    var discountAmount = 0;
    var discountAmountSpot = 0;
    var gst_flag = parseFloat($('#gst_flag').val());
    var service_tax_percentage = parseFloat($('#service_tax_percentage').val());
    
    // Check if GST elements exist
    if (isNaN(gst_flag)) gst_flag = 0;
    if (isNaN(service_tax_percentage)) service_tax_percentage = 0;
    
    // Registration Tariff
    if (!$("#workshopToggle").is(":checked")) {
        $("input[type=radio][operationMode=registration_tariff]:checked").each(function() {
            var tariffAmount = parseFloat($(this).attr("tariffAmount"));
            if (isNaN(tariffAmount)) tariffAmount = 0;
            total += tariffAmount;
            currency = $(this).attr("currency") || "INR";
            var labelText = $(this)
                .closest("label")
                .find(".checkmark")
                .clone()
                .children("i")
                .remove()
                .end()
                .text()
                .trim() || "Registration";
            
            billHTML += `
                <p class="frm-head text_dark d-flex justify-content-between align-items-center">
                    ${escapeHtml(labelText)}
                    <span class="text-white">${currency} ${tariffAmount.toFixed(2)}</span>
                </p>
            `;
        });
    }
    
    // Workshop Tariff
    $("input[type=checkbox][operationMode=workshopId]:checked").each(function() {
        var tariffAmount = parseFloat($(this).attr("tariffAmount"));
        if (isNaN(tariffAmount)) tariffAmount = 0;
        total += tariffAmount;
        currency = $(this).attr("currency") || "INR";
        var labelText = $(this)
            .closest("label")
            .find(".checkmark")
            .clone()
            .children("i")
            .remove()
            .end()
            .text()
            .trim() || "Workshop";
        
        billHTML += `
            <p class="frm-head text_dark d-flex justify-content-between align-items-center">
                ${escapeHtml(labelText)}
                <span class="text-white">${currency} ${tariffAmount.toFixed(2)}</span>
            </p>
        `;
    });
    
    // Dinner Tariff
    $("input[type=checkbox][operationMode=dinnerTariffTr]:checked").each(function() {
        var tariffAmount = parseFloat($(this).attr("tariffAmount"));
        if (isNaN(tariffAmount)) tariffAmount = 0;
        total += tariffAmount;
        dinnerAmount += tariffAmount;
        currency = $(this).attr("currency") || "INR";
        var labelText = $(this)
            .closest("label")
            .find(".checkmark")
            .clone()
            .children("i")
            .remove()
            .end()
            .text()
            .trim() || "Dinner";
        
        billHTML += `
            <p class="frm-head text_dark d-flex justify-content-between align-items-center">
                ${escapeHtml(labelText)}
                <span class="text-white">${currency} ${tariffAmount.toFixed(2)}</span>
            </p>
        `;
    });
    
    // Accompanying persons
    $(".accm_add_acco_box").each(function () {
        const nameInput = $(this).find("input[type=text]").first();
        const checkbox = $(this).find("input.accompanyCount");
        const amountElem = $(this).find(".accompanyTariffAmount span");
        let amount = parseFloat(amountElem.text().replace(/[^0-9\.]+/g,"")) || 0;
        let nameVal = nameInput.val() || "";

        if (nameVal.trim() !== "") {
            checkbox.prop("checked", true);
            total += amount;
            currency = amountElem.text().replace(/[0-9\.\s]/g,"") || currency;
            var labelText = 'Accompanying Person';

            billHTML += `
            <p class="frm-head text_dark d-flex justify-content-between align-items-center">
                ${labelText}
                <span class="text-white">${amount.toFixed(2)}</span>
            </p>
        `;
        } else {
            checkbox.prop("checked", false);
        }
    });
    
    // Spot Accompanying persons
    $(".accm_add_acco_box_spot").each(function () {
        const nameInput = $(this).find("input[type=text]").first();
        const checkbox = $(this).find("input.accompanyCountSpot");
        const amountElem = $(this).find(".accompanyTariffAmountSpot span");
        let amount = parseFloat(amountElem.text().replace(/[^0-9\.]+/g,"")) || 0;
        let nameVal = nameInput.val() || "";

        if (nameVal.trim() !== "") {
            checkbox.prop("checked", true);
            total += amount;
            currency = amountElem.text().replace(/[0-9\.\s]/g,"") || currency;
            var labelText = 'Accompanying Person';

            billHTML += `
            <p class="frm-head text_dark d-flex justify-content-between align-items-center">
                ${labelText}
                <span class="text-white">${amount.toFixed(2)}</span>
            </p>
        `;
        } else {
            checkbox.prop("checked", false);
        }
    });
    
    // Workshop Nov
    $("input[type=checkbox][operationMode=workshopId_nov]").each(function() {
        if ($(this).is(":checked")) {
            var tariffCol = $(this).parent().closest("tr").find("td[use=workshopTariff][cutoff='" + selecetedWorkshopCutoff + "']");
            if (tariffCol.length) {
                var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));
                if (isNaN(tariffAmount)) tariffAmount = 0;
                total += tariffAmount;
            }
        }
    });
    
    // Workshop Post Conference
    $("input[type=checkbox][operationMode=workshopId_postConference]").each(function() {
        if ($(this).is(":checked")) {
            var tariffCol = $(this).parent().closest("tr").find("td[use=workshopTariff][cutoff='" + selecetedWorkshopCutoff + "']");
            if (tariffCol.length) {
                var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));
                if (isNaN(tariffAmount)) tariffAmount = 0;
                total += tariffAmount;
            }
        }
    });
    
    // Dinner
    $("input[type=checkbox][operationMode=dinner]").each(function() {
        if ($(this).is(":checked")) {
            var tariffCol = $(this).parent().closest("tr").find("td[use=dinnerTariff][cutoff='" + selecetedCutoff + "']");
            if (tariffCol.length) {
                var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));
                if (isNaN(tariffAmount)) tariffAmount = 0;
                total += tariffAmount;
            }
        }
    });
    
    // ============ ACCOMMODATION - Add to bill summary ============
    var accommodationTotal = 0;
    var accommodationHTML = "";
    
    // Get all checked accommodation packages
    $("input[name='package_id[]']:checked").each(function() {
        var $pkg = $(this);
        var hotelId = $pkg.data('hotel-id');
        var roomId = $pkg.data('room-id');
        var packageName = $pkg.data('package-name') || '';
        var roomName = $pkg.data('room-name') || '';
        var amount = parseFloat($pkg.attr('amount'));
        
        if (isNaN(amount) || amount === 0) {
            var baseAmount = parseFloat($pkg.data('base-amount')) || 0;
            var checkInVal = $('#accomodation_package_checkin_id').val();
            var checkOutVal = $('#accomodation_package_checkout_id').val();
            var nights = 1;
            if (checkInVal && checkOutVal) {
                var checkInDate = checkInVal.split("/")[1];
                var checkOutDate = checkOutVal.split("/")[1];
                nights = Math.ceil((new Date(checkOutDate) - new Date(checkInDate)) / (1000 * 60 * 60 * 24));
                nights = Math.max(1, nights);
            }
            
            var quantity = 1;
            var $qtyInput = $("input.accmomdation-qty[data-hotel-id='" + hotelId + "'][data-room-id='" + roomId + "']");
            if ($qtyInput.length && !$qtyInput.prop('disabled')) {
                quantity = parseInt($qtyInput.val(), 10) || 1;
            }
            
            var isSharing = packageName.toLowerCase() === 'sharing';
            var finalQuantity = isSharing ? 1 : quantity;
            amount = baseAmount * nights * finalQuantity;
            $pkg.attr('amount', amount.toFixed(2));
        }
        
        if (amount > 0) {
            accommodationTotal += amount;
            
            var hotelName = '';
            if (hotelId) {
                var $hotelBox = $('#hotel_' + hotelId);
                if ($hotelBox.length) {
                    hotelName = $hotelBox.closest('.registration-pop_body_box_inner').find('.hotel_tab_btn[data-tab="' + hotelId + '"] .checkmark').text().trim() || 'Hotel';
                }
            }
            
            var checkInVal = $('#accomodation_package_checkin_id').val();
            var checkOutVal = $('#accomodation_package_checkout_id').val();
            var nights = 1;
            if (checkInVal && checkOutVal) {
                var checkInDate = checkInVal.split("/")[1];
                var checkOutDate = checkOutVal.split("/")[1];
                nights = Math.ceil((new Date(checkOutDate) - new Date(checkInDate)) / (1000 * 60 * 60 * 24));
                nights = Math.max(1, nights);
            }
            
            var quantity = 1;
            var $qtyInput = $("input.accmomdation-qty[data-hotel-id='" + hotelId + "'][data-room-id='" + roomId + "']");
            if ($qtyInput.length && !$qtyInput.prop('disabled')) {
                quantity = parseInt($qtyInput.val(), 10) || 1;
            }
            
            var isSharing = packageName.toLowerCase() === 'sharing';
            var displayText = '';
            var invoiceName = $pkg.attr('invoiceName') || packageName;
            
            if (isSharing) {
                displayText = hotelName + ' - '  + invoiceName + ' (1 Person / ' + nights + ' Night' + (nights > 1 ? 's' : '') + ')';
            } else {
                displayText = hotelName + ' - ' + invoiceName + ' (' + quantity + ' Room' + (quantity > 1 ? 's' : '') + ' / ' + nights + ' Night' + (nights > 1 ? 's' : '') + ')';
            }
            
            accommodationHTML += `
                <p class="frm-head text_dark d-flex justify-content-between align-items-center accommodation-bill-item" data-hotel-id="${hotelId}" data-room-id="${roomId}">
                    ${escapeHtml(displayText)}
                    <span class="text-white">${currency} ${amount.toFixed(2)}</span>
                </p>
            `;
        }
    });
    
    if (accommodationHTML) {
        billHTML += accommodationHTML;
        total += accommodationTotal;
    }
    
    // Get discount amounts
    discountAmount = parseFloat($("input[type=number][operationMode=discountAmount]").val()) || 0;
    discountAmountSpot = parseFloat($("input[type=number][operationMode=discountAmt]").val()) || 0;
    
    // ====== REVISED: GST Calculation FIRST, then discount ======
    
    // Store the original total before any deductions
    var originalTotal = total;
    var totalWithGST = total;
    var gstAmount = 0;
    
    // Calculate GST on the original total (without discount)
    if (gst_flag == 1 && service_tax_percentage > 0) {
        gstAmount = (total * service_tax_percentage) / 100;
        totalWithGST = total + gstAmount;
        
        // Add GST to bill
        billHTML += `
            <hr class="my-2">
            <p class="frm-head text_dark d-flex justify-content-between align-items-center">
                GST (${service_tax_percentage}%)
                <span class="text-white">${currency} ${gstAmount.toFixed(2)}</span>
            </p>
        `;
    }
    
    // Now apply discount to the GST-inclusive total
    var totalDiscount = 0;
    
    if (discountAmount > 0) {
        // Ensure discount doesn't exceed total
        if (discountAmount > totalWithGST) {
            discountAmount = totalWithGST;
        }
        totalDiscount += discountAmount;
        
        billHTML += `
            <hr class="my-2">
            <p class="frm-head text_dark d-flex justify-content-between align-items-center text-success">
                Discount Applied
                <span class="text-white">- ${currency} ${discountAmount.toFixed(2)}</span>
            </p>
        `;
    }
    
    if (discountAmountSpot > 0) {
        // Ensure remaining discount doesn't exceed remaining total
        var remainingAfterFirstDiscount = totalWithGST - totalDiscount;
        if (discountAmountSpot > remainingAfterFirstDiscount) {
            discountAmountSpot = remainingAfterFirstDiscount;
        }
        totalDiscount += discountAmountSpot;
        
        billHTML += `
            <p class="frm-head text_dark d-flex justify-content-between align-items-center text-success">
                Discount Applied (Spot)
                <span class="text-white">- ${currency} ${discountAmountSpot.toFixed(2)}</span>
            </p>
        `;
    }
    
    // Final amount after GST and discounts
    var finalAmount = totalWithGST - totalDiscount;
    
    // Display final total
    billHTML += `
        <hr class="my-2">
        <p class="frm-head text_dark font-weight-bold d-flex justify-content-between align-items-center">
            Total Amount
            <span class="text-white font-weight-bold">${currency} ${finalAmount.toFixed(2)}</span>
        </p>
    `;
    
    // Update all spans
    $("span[use=GSTTOTAMT]").html(finalAmount.toFixed(2));
    $("span[use=TOTAMTGST]").html(gstAmount.toFixed(2));
    $("span[use=TOTCUR]").html(currency);
    $("span[use=TOTAMT]").html(total.toFixed(2));
    $("span[use=TOTDinner]").html((dinnerAmount).toFixed(2));
    $("#billSummaryItems").html(billHTML);
    $("#billSummaryItemsSpot").html(billHTML);
}
// Helper function for escaping HTML
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Also update the initializeAccoPlugins function to call calculateRegistrationTariff
// Add this line after any accommodation changes:
function updateAccommodationAndBill() {
    calculateTotalAmount();
    updateHotelSummary(currentHotelId);
    calculateRegistrationTariff(); // This will update the bill summary
}

// Call calculateRegistrationTariff whenever:
// 1. After checkbox change
// 2. After quantity change  
// 3. After date selection
// 4. After clear all

function backEndFromValidation() // REGISTRATION FORM VALIDATION
{
	accessValidation = true;

	if ($("input[type=radio][operationMode=registration_tariff]").length > 0) {
		var registration_tariff = $("input[type=checkbox][operationMode=registration_tariff]:checked").length;
		if ($("input[type=radio][operationMode=registration_tariff]").length > 0 && registration_tariff < 1) {
			$('input[type=radio][operationMode=registration_tariff]').css('outline-color', '#D41000');
			$('input[type=radio][operationMode=registration_tariff]').css('outline-style', 'solid');
			$('input[type=radio][operationMode=registration_tariff]').css('outline-width', 'thin');
			$("html, body").animate({ scrollTop: 50 }, 1000);
			alert("Please Select Conference");
			//cssAlert('table[use=registration_tariff]','Please Select Conference');

			accessValidation = false;
			return false;
		}
	}
	if ($('#email_id_validation').val() == "IN_USE") {
		alert("Please Check Your Email Id");
		//cssAlert('#user_email_id','Email Id Already In Use');
		accessValidation = 0;
		return false;
	}
	if ($('#mobile_validation').val() != "AVAILABLE") {
		$('#user_mobile_no').focus();
		$('#user_mobile_no').css('border-color', '#D41000');
		alert("Please Check Your Mobile No");
		//cssAlert('#user_mobile','Please Confirm Mobile Correctly');

		accessValidation = 0;
		return false;
	}
	return accessValidation;
}

function initiateEmailValidation(emailObj) // EMAIL VALIDATION PART I
{
	var emailid = $.trim($(emailObj).val());

	if (emailid != '') {
		$(emailObj).attr('VALDATD', 'N');
		var div = '<div style="float: left;" use="loader"><img src="' + jsWemaster_BASE_URL + 'section_registration/images/loadinfo.net.gif" /></div>';
		$("div[use=loader]").remove();
		$('#email_div').html('');
		$("#email_id_validation").after(div);
		$('#email_div_spot').html('');
		$("#email_id_validation_spot").after(div);
		setTimeout(function () {
			oldEmail = $('#oldEmail').val();
						
			if (emailid != oldEmail) {

				checkEmailValidation(emailid, jsBASE_URL, function (status) {

					try {
						if (status == 'AVAILABLE') {
							$(emailObj).attr('VALDATD', 'Y');
						}
						postValidationActivity(status);
					} catch (e) {
						console.log("postValidationActivity not found");
					}
				});
			}
		}, 500);
	}
}

function checkEmailValidation(emailId, jsBASE_URL, callback) // EMAIL VALIDATION PART II
{

	var conf_name = $('#conf_name').val();

	$('#email_id_validation').val("");
	$('#email_div').html("");
	$('#email_div_spot').html('');
	$("#email_id_validation_spot").html('');
	const regularExpressionEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	if (emailId != "") {
		if (regularExpressionEmail.test(emailId) == false) {
			//$('#email_div').html('<span style="color:#D41000">Invalid Email Id '+emailId+'</span>');
			$('#email_div').html('<span style="color: #D41000">Something went wrong. Please check the e-mail id.</span>');
			$('#email_id_validation').val('INVALID');
			$('#email_div_spot').html('<span style="color: #D41000">Something went wrong. Please check the e-mail id.</span>');
			$('#email_id_validation_spot').val('INVALID');
			$("div[use=loader]").remove();
			return false;
		}
		else {
			// alert(emailId)
			$.ajax({
				type: "POST",
				url: jsWemaster_BASE_URL + 'section_registration/registration.process.php',
				data: 'act=getEmailValidation&email=' + emailId,
				dataType: 'json',
				async: false,
				success: function (returnMessage) {
					//returnMessage = returnMessage.trim();
					console.log(returnMessage);
					//.log(returnMessage.DATA.ID);
					var stat = 'IN_USE';
					var use = $("input[type=text][forType=emailValidate]").attr("use");
					var msg = {};
					if (use == 'add') {
						msg.IN_USE = '<span style="color: #ec9087ff">Email already registered with us for ' + conf_name + '</span>';
						msg.NOT_PAID = '<span style="color: #D41000">Email already registered with us but the registration procedure remained incomplete.<br>To complete the same please contact with AOTS Secretariat Ph no.- 03340015677, 8697064019 or 8335897369.<br>Time: 11:00 - 18:00....</span>';
						msg.AVAILABLE = '<span style="color: #9be783ff">Available</span>';
					}
					else {
						msg.IN_USE = '<span style="color:blue">You are already registered with us for ' + conf_name + '. <br>Please log in to your user profile.</span>';
						msg.NOT_PAID = '<span style="color: #D41000">Your e-mail id is already registered with us but the registration procedure remained incomplete.</span>';
						msg.AVAILABLE = '<span style="color:green">AVAILABLE </span>';

					}

					if (returnMessage.STATUS == 'IN_USE') {
						$('#email_div').html(msg.IN_USE);
						$('#email_div_spot').html(msg.IN_USE);

						$("#myemail").val(emailId);
						$("div[use=submit_block]").css('display', 'none');
						$("#logindiv").css('display', 'block');

					}
					else if (returnMessage == 'NOT_PAID') {
						stat = 'NOT_PAID';
						$('#email_div').html(msg.NOT_PAID);
						$('#email_div_spot').html(msg.NOT_PAID);

						$("#unpaidlogin").css('display', 'block');

					}
					else {
						//alert(returnMessage.DATA.ID);
						$('#email_div').html(msg.AVAILABLE);
						$('#email_div_spot').html(msg.AVAILABLE);
						$("div[use=submit_block]").css('display', 'none');

						$("#regdiv").css('display', 'block');
						$("div[userform=userregform]").css('display', 'block');
						$("input[type=text][regmail=mailId]").val(emailId);
						$("div[emaildiv=email_div]").css('display', 'none');
						$("#logindiv").css('display', 'none');
						$("#note").css('display', 'none');

						if (returnMessage.DATA) {
							$('#user_mobile_no').val(returnMessage.DATA.MOBILE_NO);
							$('#user_first_name').val(returnMessage.DATA.FIRST_NAME);
							$('#user_middle_name').val(returnMessage.DATA.MIDDLE_NAME);
							$('#user_last_name').val(returnMessage.DATA.LAST_NAME);
							$('#abstractDelegateId').val(returnMessage.DATA.ID);
							let title = returnMessage.DATA.TITLE;

							if (title) {
								title = title.charAt(0).toUpperCase() + title.slice(1).toLowerCase();
							}

							$('#user_initial_title').val(title);	
                            let gender = returnMessage?.DATA?.GENDER;

							if (gender) {
								gender = gender.toUpperCase().trim();

								$('.user_gender').prop('checked', false); // clear previous selection
								$('.user_gender[value="' + gender + '"]').prop('checked', true);
							}
							$('#user_food_preference').val(returnMessage.DATA.FOOD_PREFERENCE);
                            $('#user_country').val(returnMessage.DATA.COUNTRY_ID).trigger('change');
							$('#user_state').val(returnMessage.DATA.STATE_ID);
							$('#user_address').val(returnMessage.DATA.ADDRESS);
							$('#user_city').val(returnMessage.DATA.CITY);
							$('#user_postal_code').val(returnMessage.DATA.PIN_CODE);
                            if(returnMessage.DATA.REG_REQUEST=='ABSTRACT'){
								$('#user_mobile_no').prop('readonly', true);
								$('#user_first_name').prop('readonly', true);
								$('#user_middle_name').prop('readonly', true);
								$('#user_last_name').prop('readonly', true);
								$('#user_initial_title').prop('readonly', true);
								$('.user_gender').prop('readonly', true);
								$('#user_food_preference').prop('readonly', true);
								$('#user_country').prop('readonly', true);
								$('#user_state').prop('readonly', true);
								$('#user_address').prop('readonly', true);
								$('#user_city').prop('readonly', true);
								$('#user_postal_code').prop('readonly', true);
								$('#user_email_id').prop('readonly', true);

							}
							if(returnMessage.DATA.REG_REQUEST=='FACULTY'){
								$('#user_first_name').prop('readonly', true);
								$('#user_middle_name').prop('readonly', true);
								$('#user_last_name').prop('readonly', true);
							}
							if(returnMessage.DATA.REG_REQUEST=='FACULTY'){
								// ENABLE faculty options for faculty users
								$('#categoryContainer .category_check[data-is-faculty="true"]').removeClass('disabled-option');
								$('#categoryContainer .category_check[data-is-faculty="true"] input').prop('disabled', false);
								$('#categoryContainer .category_check[data-is-faculty="true"]').removeAttr('title');
							} else {
								// DISABLE faculty options for ALL other users (ABSTRACT, GENERAL, etc.)
								$('#categoryContainer .category_check[data-is-faculty="true"]').addClass('disabled-option');
								$('#categoryContainer .category_check[data-is-faculty="true"] input').prop('disabled', true);
								$('#categoryContainer .category_check[data-is-faculty="true"]').attr('title', 'Only for Faculty members');
							}
							
						} else {
							    $('#user_mobile_no').prop('readonly', false);
								$('#user_first_name').prop('readonly', false);
								$('#user_middle_name').prop('readonly', false);
								$('#user_last_name').prop('readonly', false);
								$('#user_initial_title').prop('readonly', false);
								$('.user_gender').prop('readonly', false);
								$('#user_food_preference').prop('readonly', false);
								$('#user_country').prop('readonly', false);
								$('#user_state').prop('readonly', false);
								$('#user_address').prop('readonly', false);
								$('#user_city').prop('readonly', false);
								$('#user_postal_code').prop('readonly', false);
								$('#user_email_id').prop('readonly', false);
							    $('#abstractDelegateId').val('');

								// DISABLE faculty options for ALL other users (ABSTRACT, GENERAL, etc.)
								$('#categoryContainer .category_check[data-is-faculty="true"]').addClass('disabled-option');
								$('#categoryContainer .category_check[data-is-faculty="true"] input').prop('disabled', true);
								$('#categoryContainer .category_check[data-is-faculty="true"]').attr('title', 'Only for Faculty members');
							}
							




						stat = 'AVAILABLE';
						// if (use == 'add') {
						// 	returnDelegateDetails(emailId, jsBASE_URL);
						// }
						//  var successContent   = '<div style="color:#FF0000; font-size:15px; text-align:center;">You are already registered.</div>';
						//	successContent  += '<div style="color:#FF0000; font-size:15px; text-align:center;">For any addition regarding registration, no need to create another account.</div>';
						//	successContent  += '<div style="text-align:center;">Please <a href="login.php" style="padding:1px 5px 1px 5px; color:#FFFFFF; background-color:#FF0000; border:1px solid #660000; cursor:pointer;">Login</a> to you account.</div>';
						//  $('#email_div').html(successContent);
					}

					$('#email_id_validation').val(returnMessage);
			         $('#email_id_validation_spot').val(returnMessage);

					try { callback(stat); } catch (e) { }


				}
			});
			$("div[use=loader]").remove();
		}


	}

}

function checkMobileValidation(mobile, jsWemaster_BASE_URL) {
	if (mobile != "") {
		if (isNaN(mobile) || mobile.toString().length != 10) {
			$('#mobile_div').html('<span style="color:#D41000">Invalid Mobile No ' + mobile + '</span>');
			$('#mobile_validation').val('INVALID');
			$('#mobile_div_spot').html('<span style="color:#D41000">Invalid Mobile No ' + mobile + '</span>');
			$('#mobile_validation_spot').val('INVALID');
			$("div[use=loader]").remove();
			return false;
		}
		else {
			//console.log(jsWemaster_BASE_URL + 'section_registration/registration.process.php?act=getMobileValidation&mobile=' + mobile);
			$.ajax({
				type: "POST",
				url: jsWemaster_BASE_URL + 'section_registration/registration.process.php',
				data: 'act=getMobileValidation&mobile=' + mobile,
				dataType: 'text',
				async: false,
				success: function (returnMessage) {
					//console.log(returnMessage,56677)
					returnMessage = returnMessage.trim();
					if (returnMessage == 'IN_USE') {
						$('#mobile_div').html('<span style="color:#FF0000">Mobile Number Already In Use</span>');
						$("#user_mobile_no").val("");
						$('#mobile_div_spot').html('<span style="color:#FF0000">Mobile Number Already In Use</span>');
						$("#user_mobile_no_spot").val("");

					}
					else {

						$('#mobile_div').html('<span style="color: #89dda5ff">Available</span>');
						$('#mobile_div_spot').html('<span style="color: #89dda5ff">Available</span>');

					}

					//$('#mobile_validation').val(returnMessage);


				}
			});
			$("div[use=loader]").remove();
		}

	}

}