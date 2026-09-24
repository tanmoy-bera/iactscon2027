$(document).ready(function () {
	$("input[type=checkbox][operationmode=workshopId_postConference]").each(function () {
		$(this).click(function () {
			if ($(this).is(":checked")) {
				//===== commented out the below line to allow multiple selection of workshops 17 JuL 2025 ============
				// $("input[type=checkbox][operationmode=workshopId_postConference]").prop("checked",false);	

				$(this).prop("checked", true);
				calculationWorkshopAmount();
			}
			else {
				calculationWorkshopAmount();
				$(this).prop("checked", false);
			}
		});
	});

	$("select[operationMode=workshopCutoff]").change(function () {
		$("input[type=checkbox][operationMode=workshopId_postConference]").prop("checked", false);
		$("input[type=checkbox][operationMode=workshopId_postConference]").attr("chkStat", "false");

		calculationWorkshopAmount();
	});
});


function calculationWorkshopAmount2() {
	var cutOffId = $("#cutoff_id_add").val();
	//var prevValue	= parseInt($('#prevValue').val());

	var total = 0;

	$("input[operationMode=workshop]:checked").each(function () {

		var workshopId = $(this).val();

		var newValue = $("input[type=hidden][workshop_id=" + workshopId + "][cutoff_id=" + cutOffId + "]").val();
		total = parseFloat(newValue);
	});
	if (isNaN(total)) {
		alert("Please Select Cutoff Frist");
		$("#cutoff_id_add").css('border-color', '');
		var total = 0;
	}
	$("#amount").text("");
	$("#amount").text(total.toFixed(2));

	//var newValue 	= $(parentDiv).find("input[type=checkbox][id=workshop_id"+id+"_"+trid+"]").attr('ammount');
	//alert(prevValue);
}

// function calculationWorkshopAmount()
// {		  
// 	var cutOffId	= $("#registration_cutoff").val();

// 	if(cutOffId=="")
// 	{
// 		alert("Please Select Cutoff First");
// 		$("#cutoff_id_add").css('border-color','');	
// 	}

// 	var total =0;

// 	$("input[operationMode=workshopId_postConference]:checked").each(function(){

// 		var parent = $(this).parent().closest('tr');

// 		var rate = $(parent).find("td[use=workshopTariff][cutoff='"+cutOffId+"']").attr("tariffAmount");

// 		total += parseFloat(rate);
// 	});

// 	var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
// 	if(discount>0)
// 	{		
// 		var discountAmount = $("input[type=text][operationMode=discountAmount]").val();
// 	}
// 	else
// 	{
// 			discountAmount = 0;
// 	}
// 	total = total - parseFloat(discountAmount);

// 	if(isNaN(total))
// 	{

// 		var total = 0;
// 	}

// 	$("#amount").text("");
// 	$("#amount").text(total.toFixed(2));
// 	//var newValue 	= $(parentDiv).find("input[type=checkbox][id=workshop_id"+id+"_"+trid+"]").attr('ammount');
// 	//alert(prevValue);

// }

function calculationWorkshopAmount() {
	var cutOffId = $("#registration_cutoff").val();

	if (cutOffId == "") {
		alert("Please Select Cutoff First");
		$("#cutoff_id_add").css('border-color', '');
		$("input[operationMode=workshopId_postConference]:checked").prop("checked", false);
	}

	var total = 0;

	$("input[operationMode=workshopId_postConference]:checked").each(function () {

		var parent = $(this).parent().closest('tr');

		var rate = $(parent).find("td[use=workshopTariff][cutoff='" + cutOffId + "']").attr("tariffAmount");

		total += parseFloat(rate);

	});
	var totalPrev = total;

	var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
	$("input[type=checkbox][operationMode=discountCheckbox]").click(function () {

		if ($(this).is(":checked")) {
			$("#amount").text(total.toFixed(2));
		}
		else {
			$("#amount").text(totalPrev.toFixed(2));

		}
	});

	if (discount > 0) {
		$("#amount").text(total.toFixed(2));
		var discountAmount = $("input[type=text][operationMode=discountAmount]").val();
		if (discountAmount > totalPrev) {
			alert("Enter a valid discount amount!");
			$("input[type=text][operationMode=discountAmount]").val(0);
		}
	}
	else {
		discountAmount = 0;
	}
	total = total - parseFloat(discountAmount);

	if (isNaN(total)) {
		var total = 0;
	}

	//$("#amount").text("");
	$("#amount").text(total.toFixed(2));
	//var newValue 	= $(parentDiv).find("input[type=checkbox][id=workshop_id"+id+"_"+trid+"]").attr('ammount');
	//alert(prevValue);

}


$(document).ready(function () {
	$("input[operationMode=discountAmount]").keyup(function () {
		var serviceType = $('input[type=hidden][id=type]').attr('value');
		//alert(serviceType);
		if (serviceType == 'Workshop') {
			calculationWorkshopAmount();
		}
	});
});

function validateWorkshopFrom(form) {
	var regTariffId = $("input[type=checkbox][operationMode=workshop]:checked").length;
	var status = 0;
	var accessValidation = true;
	if ($("input[type=checkbox][operationMode=workshop]").length && regTariffId > 0) { status = 1; }

	if (fieldNotEmpty('#cutoff_id_add', "Please Chose Cutoff") == false) {

		accessValidation = false;
		return false;
	}

	if (status == 0) {
		alert("Please Select Workshop");
		$("table[use=Tariff]").css('border-color', '');
		$('input[type=checkbox][operationMode=workshop]').css('outline-color', '#D41000');
		$('input[type=checkbox][operationMode=workshop]').css('outline-style', 'solid');
		$('input[type=checkbox][operationMode=workshop]').css('outline-width', 'thin');

		accessValidation = false;
		return false;
	}

	if (!validatePaymentTermsSubmission(form)) {
		accessValidation = false;
		return false;
	}

	var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
	if (discount > 0) {
		if (fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false) {
			accessValidation = false;
			return false;
		}

		var discountAmount = $("input[type=text][operationMode=discountAmount]");
		if (isNaN((discountAmount).val())) {
			alert('Enter Discount Amount correctly');
			$(discountAmount).focus();
			accessValidation = false;
			return false;
		}

		var total = parseFloat($("#amount").text());
		if (total < parseFloat((discountAmount).val())) {
			alert('Enter Discount Amount correctly');
			$(discountAmount).focus();
			accessValidation = false;
			return false;
		}
	}
	//alert(status);
	return accessValidation;
}