$(document).ready(function(){
	$('#accName').hide();
	try{	
		console.log('start validation;');
		validateform();
	}catch(e){
		console.log(e.message);
	}

	try{		
		addMoreAccommodationTemplate(1);
	}catch(e){
		console.log(e.message);
	}
	
		
	$('#ApplyForAccommodation').submit(function(){
		var cutoff			= $('#cutoff_id_add').val();

		var hotelId			= $('#accommodation_hotel_id').val();
		var checkOutDate	= $('#check_out_date').val();		
		var checkInDate 	= $('#check_in_date').val();
		var roomType		= $('#accommodation_roomType_id').val();
		
		if(cutoff=="")
		{
			alert('Please select Cutoff.');
			$('#cutoff_id_add').focus();
			return false;
		}
		if(hotelId=="")
		{
			alert('Please select hotel.');
			$('#accommodation_hotel_id').focus();
			return false;
		}
		if(checkInDate=="")
		{
			alert('Please select check in date.');
			$('#check_in_date').focus();
			return false;
		}
		if(checkOutDate=="")
		{
			alert('Please select check out date.');
			$('#check_out_date').focus();
			return false;
		}
		if(roomType=="")
		{
			alert('Please select room type.');
			$('#accommodation_roomType_id').focus();
			return false;
		}		
		
		if(roomType == 1)
		{
			var guest_counter = $("span[operationmode=guestDisplay]").text();
			if(guest_counter == "21")
			{
				var accmName	= $('#accmName').val();
				if(accmName == "")
				{
					alert('Please Enter Accompanying person name.');
					$('#accmName').focus();
					return false;
				}
			}
		}
		
		var discount = $("input[type=checkbox][operationMode=discountCheckbox]:checked").length;
		if(discount>0)
		{		
			if(fieldNotEmpty('#discountAmount', "Please Enter discount amount") == false){ 	
				return false;
			}	
			
			var discountAmount = $("input[type=text][operationMode=discountAmount]");		
			if(isNaN((discountAmount).val()))
			{
				cssAlert(discountAmount,'Enter Discount Amount correctly');
				$(discountAmount).focus();
				status = false;
				return false;
			}
			
			var total = parseFloat($("span[operationMode=totalAccomodationAmount]").text());	
			if(total <  parseFloat((discountAmount).val()))
			{
				cssAlert(discountAmount,'Enter Discount Amount correctly');
				$(discountAmount).focus();
				status = false;
				return false;
			}
		}
		
		//return false;
	});
	
	
	// GENERATE CUTTOFF ID
	$("#cutoff_id_add").change(function(){		
		//var cutoffId			= $(this).val();
		//var ClassificationId	= $("#regclassificationId").val();
		//var str=cutoffId+'~'+ClassificationId;
		
	//	generateAccommodationCutoffId(str);
	  $('#accommodation_hotel_id').val('');
	  $('#accommodation_roomType_id').val('');
	  $('#check_out_date').val('');
	  $('#check_in_date').val('');	
	  $("span[operationMode=totalAccomodationAmount]").text('0.00');  
	 
	  $("#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell] td[operationMode=tariffAmount]").text('INR 0.00');
	  $("#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell] td[operationMode=totalAmount]").text('INR 0.00');
	  $("#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell] span[operationMode=guestDisplay]").text("1");
	  $('#accName').hide();
	  $('#accmName').val("");
	});
	
	$("input[type=radio][operationMode=accomodation_registration_type]").change(function(){
	
	var registrationType = $("input[type=radio][operationMode=accomodation_registration_type]:checked").val();
	
	if(registrationType=="ZERO_VALUE")
	{
		$("span[operationMode=totalAccomodationAmount]").html('0.00');
		$("div[operationMode=paymentTermsSetUnit]").css("display", "none");
	}
	else
	{
		$("div[operationMode=paymentTermsSetUnit]").css("display", "block");
		calculateTotalTariffAmount();
	}
	});
		
	$("#booking_quantity").change(function(){
		
		var roomQuantity = $(this).val();
		
		addMoreAccommodationTemplate(roomQuantity);
	});
	$("#accommodation_hotel_id, #accommodation_roomType_id, #booking_quantity, #check_in_date, #check_out_date").change(function(){
		
		//if($(this).attr('id')=='check_out_date'){
			
			var checkoutVal 	= $('#check_out_date').val();
			var checkinVal		= $('#check_in_date').val();
			var hohel_type_id	= $('#accommodation_roomType_id').val();
			
			if(hohel_type_id == 3)
			{
				$('#accmName').val("");
				$('#accName').hide();
				$("span[operationmode=guestDisplay]").text("1");
			}
			if(hohel_type_id == 1)
			{
				$('#accmName').val("");
				$('#accName').hide();
				$("span[operationmode=guestDisplay]").text("1");
			}
			if(checkinVal=='')
			{
				alert('Please select Check In Date');
				$('#check_out_date').val("");
				clearFields();
			}
			if(checkoutVal=='')
			{				
				clearFields();							
			}
			if(hohel_type_id=='')
			{				
				clearFields();
			}
			else if(parseInt(checkinVal) > parseInt(checkoutVal))
			{
				alert('Please select a later Checkout Date');
				$('#check_out_date').val("");					
				clearFields();				
			}
		//}		
		
		resetGuestCounter();
		calculateTotalTariffAmount();
	});
	
	// GENERATE AVAILABLE PACKAGE LIST
	$("#accommodation_hotel_id").change(function(){
		
		var hotelId		= $(this).val();
		
		generateAvailableRoomTypeList(hotelId);
	});	
	
});

function clearFields()
{
	$('#accommodation_roomType_id').val("");
	$("#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell] td[operationMode=tariffAmount]").text('INR 0.00');
	$("#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell] td[operationMode=totalAmount]").text('INR 0.00');
	$("#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell] span[operationMode=guestDisplay]").text("1");
	$('#accmName').val("");
	$('#accName').hide();
}

function resetGuestCounter(){
	
	var roomTypeId	     = $("#accommodation_roomType_id").val();
	
	var bookingSelection = 1;
	var bookingQuantity  = $("#booking_quantity").val();
	
	if(bookingQuantity!=""){
		
		bookingSelection = bookingQuantity;
	}
	
	//$("#booking_quantity").html("");
	//$("#booking_quantity").html("<option value='1'>1</option><option value='2'>2</option><option value='3'>3</option>");
	
	$("#booking_quantity").val(bookingSelection);
	
	if(roomTypeId=="" || roomTypeId==3){
		
		$("span[operationMode=guestDisplay]").text(1);
		$("input[operationMode=room_guest_counter]").val(1);
		
		$("#booking_quantity").html("");
		$("#booking_quantity").html("<option value='1' selected='selected'>1</option>");
		
		$("#booking_quantity").val(1);
		addMoreAccommodationTemplate(1);
	}
	
	calculateTotalTariffAmount();
}

function guestCounterMinus(sequenceBy){
	
	var accommodation_roomType_id  = $("#accommodation_roomType_id").val();
	var guestCounter              = parseInt($("span[operationMode=guestDisplay][sequenceBy="+sequenceBy+"]").text());
	//alert();
	if(guestCounter>1 && (accommodation_roomType_id==1)){
		
		guestCounter = parseInt(guestCounter) - 1;
		
		$("span[operationMode=guestDisplay][sequenceBy="+sequenceBy+"]").text(guestCounter);
		$("input[operationMode=room_guest_counter][sequenceBy="+sequenceBy+"]").val(guestCounter);
	}
	if(guestCounter>1 && (accommodation_roomType_id==3)){
		
		guestCounter = parseInt(guestCounter) - 1;
		
		$("span[operationMode=guestDisplay][sequenceBy="+sequenceBy+"]").text(guestCounter);
		$("input[operationMode=room_guest_counter][sequenceBy="+sequenceBy+"]").val(guestCounter);
	}
	if(guestCounter==2)
	{
		$('#accName').show();
	}
	else
	{
		$('#accName').hide();	
	}
	
	calculateTotalTariffAmount();
}

function guestCounterPlus(sequenceBy){
	
	var accommodation_roomType_id  = $("#accommodation_roomType_id").val();
		
	var guestCounter = parseInt($("span[operationMode=guestDisplay][sequenceBy="+sequenceBy+"]").text());
	//alert(accommodation_roomType_id+" "+guestCounter);
	if(guestCounter<2 && (accommodation_roomType_id==3)){
		
		guestCounter = parseInt(guestCounter) + 1;
		//alert(guestCounter);
		$("span[operationMode=guestDisplay][sequenceBy="+sequenceBy+"]").text(guestCounter);
		$("input[operationMode=room_guest_counter][sequenceBy="+sequenceBy+"]").val(guestCounter);
	
		if(guestCounter==2)
		{
			$('#accmName').val("");
			$('#accName').show();
		}
		else
		{
			$('#accName').hide();	
		}	
	}
	
	
	
	calculateTotalTariffAmount();
}

function generateAvailableRoomTypeList(hotelId){
	
	if(hotelId!=''){
		
		$.ajax({
					type: "POST",
					url: 'registration.process.php',
					data: 'act=generateAvailableRoomTypeList&hotelId='+hotelId,
					dataType: 'html',
					async: false,
					success: function(returnMessage){
						
						if(returnMessage!=''){
							
							$("#accommodation_roomType_id").html("");
							$("#accommodation_roomType_id").html(returnMessage);
							
							$("#accommodation_roomType_id").prop("disabled", false);
						}
					}
			  });
	}
	else{
		
		$("#accommodation_roomType_id").html("");
		$("#accommodation_roomType_id").html("<option value=''>-- Select Hotel First --</option>");
		
		$("#accommodation_roomType_id").prop("disabled", true);
		
		return false;
	}}

function calculateTotalTariffAmount(){
		
		var grandTariffAmount     = 0;	
		var hotelId	              = $('#accommodation_hotel_id').val();
		var roomTypeId	          = $('#accommodation_roomType_id').val();
		var bookingDateId	      = $('#check_in_date').val();
		var checkoutDateId	      = $('#check_out_date').val();
		var cutoffId		      = $('#cutoff_id_add').val();
		console.log("hotelId="+hotelId+",roomTypeId="+roomTypeId+",bookingDateId="+bookingDateId+",checkoutDateId="+checkoutDateId+",cutoffid="+cutoffId);
		
		if(roomTypeId!="" && bookingDateId!="" && hotelId!="" && cutoffId !="" && checkoutDateId != "")
		{
			var registrationType = $("input[type=radio][operationMode=accomodation_registration_type]:checked").val();
			
			if(registrationType=="ZERO_VALUE")
			{
				var tariffAmount  = 0; 
			}
			else
			{
				var tariffAmount      = $("input[operationMode=accommodation_tariff_details][checkInId="+bookingDateId+"][checkOutId="+checkoutDateId+"][roomTypeId="+roomTypeId+"][hotelId="+hotelId+"][cutoff="+cutoffId+"]").val();
				console.log(tariffAmount);
			}
			//alert(tariffAmount);
			if (isNaN(tariffAmount))
			{
				tariffAmount = 0;
			}
			console.log(tariffAmount);
			$("td[operationMode=tariffAmount]").text("INR "+tariffAmount);
			$("td[operationMode=totalAmount]").text("INR "+tariffAmount);
			$("input[operationMode=room_tariff_amount]").val(tariffAmount);
			
			$("#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell] span[operationMode=guestDisplay]").each(function(){
				
				var guestQuantity 		= $(this).text();
				var sequenceBy    		= $(this).attr("sequenceBy");
				
				grandTariffAmount   = parseFloat(grandTariffAmount) + parseFloat(tariffAmount);
				console.log(grandTariffAmount);
				
			});
			if (isNaN(grandTariffAmount))
			{
				grandTariffAmount = 0;
			}
			console.log(grandTariffAmount);
			$("span[operationMode=totalAccomodationAmount]").html(grandTariffAmount.toFixed(2));
		
		}
		else
		{
			$("span[operationMode=totalAccomodationAmount]").text('0.00');
		}
	
	}
	
function validateform(){
	$('#frmRegistrationStep3').submit(function()
	{

		
		var cutoff			= $('#cutoff_id_add').val();
		var checkindate		= $('#check_in_date').val();
		var checkoutDate	= $("#check_out_date").val();
		var hotelId			= $('#accommodation_hotel_id').val();
		var roomType		= $('#accommodation_roomType_id').val();

		if(cutoff=="")
		{
			alert('Please Select Cut Off.');
			return false;
		}
		
		if(hotelId=="")
		{
			alert('Please Select Hotel.');
			return false;
		}
		if(checkindate=="")
		{
			alert('Please Select CheckIn Date.');
			return false;
		}
		if(checkoutDate=="")
		{
			alert('Please Select CheckOut Date.');
			return false;
		}
		if(roomType=="")
		{
			alert('Please Select Room Type.');
			return false;
		}
		
	});}	
	
function generateAccommodationCutoffId(str){
		
	if(str!=''){
			$.ajax({
						type: "POST",
						url: 'general_registration.process.php',
						data: 'act=accommodationCutoffValue&string='+str,
						dataType: 'html',
						async: false,
						success: function(returnMessage){
							if(returnMessage!=''){
								$("#tariffValue").html("");
								$("#tariffValue").html(returnMessage);
								$("#tariffValue").prop("disabled", false);	
							}
						}
				  });
		}}	

/**********************************************************************/
/*                   ADD MORE ACCOMMODATION TEMPLATE                  */
/**********************************************************************/
function addMoreAccommodationTemplate(roomQuantity){
	
	var totalRows      = parseInt($('#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell]').length);
	var adjustAmount   = parseInt(roomQuantity) - parseInt(totalRows)
	
	if(parseInt(adjustAmount) > 0){
		
		for(var adj=0; adj<adjustAmount; adj++){		
			
			var nextCounter    = adj + totalRows + 1;
			
			var getTemplate    = $("#accommodationDetailsTemplate table tbody:first").html();
			var modifiedTemp   = getTemplate.replace(/\#COUNTER/g, nextCounter);
		
			$('#accommodationDetailsPlaceholderTable tr:last').after(modifiedTemp);
		}
	}
	else if(parseInt(adjustAmount) < 0){
		
		for(var adj=0; adj<Math.abs(adjustAmount); adj++){	
			
			$('#accommodationDetailsPlaceholderTable tr[operationMode=roomDetailsCell]:last').remove();
		}
	}
	
	$("button[operationMode=guestMinus]").click(function(){
		
		var sequenceBy = $(this).attr("sequenceBy");
		//alert(sequenceBy);
		guestCounterMinus(sequenceBy);
	});
	
	$("button[operationMode=guestPlus]").click(function(){
		
		var sequenceBy = $(this).attr("sequenceBy");
		guestCounterPlus(sequenceBy);
	});
}
	
	
	
function formAccommodationValidation()
{
	accessValidation = true;	
	if(fieldNotEmpty('#accommodation_hotel_id', "Please Select Hotel") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if(fieldNotEmpty('#check_in_date', "Please Select Check In Date") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if(fieldNotEmpty('#check_out_date', "Please Select Check Out Date") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	if(fieldNotEmpty('#accommodation_roomType_id', "Please Select Room Type") == false){ 
		
		accessValidation	 = false;
		return false; 
	}
	var roomType		= $('#accommodation_roomType_id').val();
	if(roomType == 1)
	{
		var guest_counter = $("span[operationmode=guestDisplay]").text();
		
		if(guest_counter == "21")
		{
			var accmName	= $('#accmName').val();
			if(accmName == "")
			{
				alert('Please Enter Accompanying person name.');
				$('#accmName').focus();
				return false;
			}
		}
	}
	
	return accessValidation;	
}

// $(document).ready(function(){
// 	$('#accommodation_roomType_id').change(function(){
// 		alert('hello');
// 	});
// });