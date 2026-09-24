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


	$("select[operationMode=regCutoff]").change(function () {
		$("input[type=checkbox][operationMode=registration_tariff]").prop("checked", false);
		$("input[type=checkbox][operationMode=registration_tariff]").attr("chkStat", "false");
		$("input[type=checkbox][operationMode=workshopId]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId]").attr("chkStat", "false");
		$("input[type=checkbox][operationMode=workshopId_nov]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId_nov]").attr("chkStat", "false");

		calculateRegistrationTariff();
	});

	$("select[operationMode=workshopCutoff]").change(function () {
		$("input[type=checkbox][operationMode=workshopId]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId]").attr("chkStat", "false");
		$("input[type=checkbox][operationMode=workshopId_nov]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId_nov]").attr("chkStat", "false");
		// $("input[type=checkbox][operationMode=workshopId_postConference]").prop("checked", false);
		// $("input[type=checkbox][operationMode=workshopId_postConference]").attr("chkStat", "false");

		calculateRegistrationTariff();
	});

	$("select[operationMode=accomodationPackage]").change(function () {
		var checkInDate = $(this).children("option:selected").attr("checkInDate");
		var checkOutDate = $(this).children("option:selected").attr("checkOutDate");
		$("input[type=hidden][name=accommodation_checkIn]").attr("value", checkInDate);
		$("input[type=hidden][name=accommodation_checkOut]").attr("value", checkOutDate);
	});

	$("input[type=checkbox][operationMode=registration_tariff]").each(function () {
		$(this).click(function () {


			var regClsfId = $(this).val();
			var regType = $(this).attr('registrationType');
			$("input[type=checkbox][operationMode=registration_tariff]").prop("checked", false);

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
	var regTariffId = $("input[type=checkbox][operationMode=registration_tariff]:checked").length;
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

function calculateRegistrationTariff() {

	var total = 0;
	var currency = "INR";

	var selecetedCutoff = $("select[operationMode=regCutoff]").val();
	var selecetedWorkshopCutoff = $("select[operationMode=workshopCutoff]").val();

	var gst_flag = parseFloat($('#gst_flag').val());
	var service_tax_percentage = parseFloat($('#service_tax_percentage').val());

	$("input[type=checkbox][operationMode=registration_tariff]").each(function () {
		if ($(this).is(":checked")) {
			var tariffCol = $(this).parent().closest("tr").find("td[use=registrationTariff][cutoff='" + selecetedCutoff + "']");
			var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));
			//alert(tariffAmount);
			if (isNaN(tariffAmount)) {
				tariffAmount = 0;
			}

			total += tariffAmount;

			currency = $(tariffCol).attr("tariffCurrency");

		}
	});

	$("input[type=checkbox][operationMode=workshopId]").each(function () {
		if ($(this).is(":checked")) {
			var tariffCol = $(this).parent().closest("tr").find("td[use=workshopTariff][cutoff='" + selecetedWorkshopCutoff + "']");
			var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));
			if (isNaN(tariffAmount)) {
				tariffAmount = 0;
			}

			total += tariffAmount;
		}
	});

	$("input[type=checkbox][operationMode=workshopId_nov]").each(function () {
		if ($(this).is(":checked")) {
			var tariffCol = $(this).parent().closest("tr").find("td[use=workshopTariff][cutoff='" + selecetedWorkshopCutoff + "']");
			var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));

			if (isNaN(tariffAmount)) {
				tariffAmount = 0;
			}

			total += tariffAmount;
		}
	});

	$("input[type=checkbox][operationMode=workshopId_postConference]").each(function () {
		if ($(this).is(":checked")) {
			var tariffCol = $(this).parent().closest("tr").find("td[use=workshopTariff][cutoff='" + selecetedWorkshopCutoff + "']");

			console.log(tariffCol);

			var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));

			if (isNaN(tariffAmount)) {
				tariffAmount = 0;
			}

			total += tariffAmount;
		}
	});

	$("input[type=checkbox][operationMode=dinner]").each(function () {
		if ($(this).is(":checked")) {
			var tariffCol = $(this).parent().closest("tr").find("td[use=dinnerTariff][cutoff='" + selecetedCutoff + "']");
			var tariffAmount = parseFloat($(tariffCol).attr("tariffAmount"));

			if (isNaN(tariffAmount)) {
				tariffAmount = 0;
			}

			total += tariffAmount;
		}
	});


	if (gst_flag == 1) {
		var gst = (total * service_tax_percentage) / 100;
		var amt = (gst + total);

		$("span[use=GSTTOTAMT]").html((amt).toFixed(2));
		$("span[use=TOTAMTGST]").html((gst).toFixed(2));
	}
	else {
		var amt = total;
	}

	$("span[use=TOTCUR]").html(currency);
	$("span[use=TOTAMT]").html((total).toFixed(2));


	//alert(amt);
}

function backEndFromValidation() // REGISTRATION FORM VALIDATION
{
	accessValidation = true;

	if ($("input[type=checkbox][operationMode=registration_tariff]").length > 0) {
		var registration_tariff = $("input[type=checkbox][operationMode=registration_tariff]:checked").length;
		if ($("input[type=checkbox][operationMode=registration_tariff]").length > 0 && registration_tariff < 1) {
			$('input[type=checkbox][operationMode=registration_tariff]').css('outline-color', '#D41000');
			$('input[type=checkbox][operationMode=registration_tariff]').css('outline-style', 'solid');
			$('input[type=checkbox][operationMode=registration_tariff]').css('outline-width', 'thin');
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
	if (emailId != "") {
		if (regularExpressionEmail.test(emailId) == false) {
			//$('#email_div').html('<span style="color:#D41000">Invalid Email Id '+emailId+'</span>');
			$('#email_div').html('<span style="color:#D41000">Something went wrong. Please check the e-mail id.</span>');
			$('#email_id_validation').val('INVALID');
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
					console.log("1");
					//.log(returnMessage.DATA.ID);
					var stat = 'IN_USE';
					var use = $("input[type=text][forType=emailValidate]").attr("use");
					var msg = {};
					if (use == 'add') {
						msg.IN_USE = '<span style="color:#D41000">Email already registered with us for ' + conf_name + '</span>';
						msg.NOT_PAID = '<span style="color:#D41000">Email already registered with us but the registration procedure remained incomplete.<br>To complete the same please contact with AOTS Secretariat Ph no.- 03340015677, 8697064019 or 8335897369.<br>Time: 11:00 - 18:00....</span>';
						msg.AVAILABLE = '<span style="color:green">Available</span>';
					}
					else {
						msg.IN_USE = '<span style="color:blue">You are already registered with us for ' + conf_name + '. <br>Please log in to your user profile.</span>';
						msg.NOT_PAID = '<span style="color:#D41000">Your e-mail id is already registered with us but the registration procedure remained incomplete.</span>';
						msg.AVAILABLE = '<span style="color:green">AVAILABLE </span>';

					}

					if (returnMessage.STATUS == 'IN_USE') {
						$('#email_div').html(msg.IN_USE);
						$("#myemail").val(emailId);
						$("div[use=submit_block]").css('display', 'none');
						$("#logindiv").css('display', 'block');

					}
					else if (returnMessage == 'NOT_PAID') {
						stat = 'NOT_PAID';
						$('#email_div').html(msg.NOT_PAID);
						$("#unpaidlogin").css('display', 'block');

					}
					else {
						//alert(returnMessage.DATA.ID);
						$('#email_div').html(msg.AVAILABLE);
						$("#regdiv").css('display', 'block');
						$("div[userform=userregform]").css('display', 'block');
						$("input[type=text][regmail=mailId]").val(emailId);
						$("div[emaildiv=email_div]").css('display', 'none');
						$("#logindiv").css('display', 'none');
						$("#note").css('display', 'none');
						$("div[use=submit_block]").css('display', 'none');

						if (returnMessage.DATA) {
							$('#user_mobile_no').val(returnMessage.DATA.MOBILE_NO);
							$('#user_first_name').val(returnMessage.DATA.FIRST_NAME);
							$('#user_middle_name').val(returnMessage.DATA.MIDDLE_NAME);
							$('#user_last_name').val(returnMessage.DATA.LAST_NAME);
							$('#abstractDelegateId').val(returnMessage.DATA.ID);
							if(returnMessage.DATA.REG_REQUEST=='FACULTY'){
								$('#user_first_name').prop('readonly', true);
								$('#user_middle_name').prop('readonly', true);
								$('#user_last_name').prop('readonly', true);
							}
						}




						stat = 'AVAILABLE';
						if (use == 'add') {
							returnDelegateDetails(emailId, jBaseUrl);
						}
						//  var successContent   = '<div style="color:#FF0000; font-size:15px; text-align:center;">You are already registered.</div>';
						//	successContent  += '<div style="color:#FF0000; font-size:15px; text-align:center;">For any addition regarding registration, no need to create another account.</div>';
						//	successContent  += '<div style="text-align:center;">Please <a href="login.php" style="padding:1px 5px 1px 5px; color:#FFFFFF; background-color:#FF0000; border:1px solid #660000; cursor:pointer;">Login</a> to you account.</div>';
						//  $('#email_div').html(successContent);
					}

					$('#email_id_validation').val(returnMessage);

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
			$("div[use=loader]").remove();
			return false;
		}
		else {
			console.log(jsWemaster_BASE_URL + 'section_registration/registration.process.php?act=getMobileValidation&mobile=' + mobile);
			$.ajax({
				type: "POST",
				url: jsWemaster_BASE_URL + 'section_registration/registration.process.php',
				data: 'act=getMobileValidation&mobile=' + mobile,
				dataType: 'text',
				async: false,
				success: function (returnMessage) {
					returnMessage = returnMessage.trim();
					if (returnMessage == 'IN_USE') {
						$('#mobile_div').html('<span style="color:#FF0000">Mobile Number Already In Use</span>');
						$("#user_mobile_no").val("");

					}
					else {

						$('#mobile_div').html('<span style="color:#009933">Available</span>');
					}

					$('#mobile_validation').val(returnMessage);


				}
			});
			$("div[use=loader]").remove();
		}

	}

}
