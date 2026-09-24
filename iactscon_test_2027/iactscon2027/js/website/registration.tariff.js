
/**********************************************************************/
/*                       SELECT REGISTRATION                          */
/**********************************************************************/
$(document).ready(function(){	
	
	$("input[type=checkbox][operationMode=registration_tariff]").each(function(){
		$(this).click(function(){	
							   
			$("div[use=DelegateRegIncludeServ]").hide();			
			$("div[use=ResidentialRegIncludeServ]").hide();			
			$("div[use=ResidentialAccommodation]").hide();		
			$("div[use=ResidentialAccommodationAccompanyOption]").hide();
			$("div[use=ResidentialAccommodationAccompanyOption]").find("input").val('');
			$("div[use=OfferRegIncludeServ]").hide();			
			$("div[use=WorkshopReg]").hide();
			
			$("tr[operetionMode=residenTariffTr]").hide();
			$("table[use=checkInCheckOutTable]").hide();
			$("tr[operetionMode=checkInCheckOutTr]").hide();			
			$("tr[use=residentialTaxDeclare]").hide();
			
			$("tr[operetionMode=workshopTariffTr]").hide();			
			
			$("tr[operetionMode=dinnerTr]").hide();			
			$("tr[operetionMode=JUSTshow]").hide();			
			
			$("input[type=checkbox][operationMode=registration_tariff]").prop("checked",false);	
			$("input[type=checkbox][operationMode=registration_tariff]").attr("chkStatus","false");
						
			$("input[type=checkbox][operationMode=workshopId]").prop("checked",false);
			$("input[type=checkbox][operationMode=workshopId]").attr("chkStatus","false");	
			
			$("input[type=checkbox][operationMode=dinner]").prop("checked",false);
			$("input[type=checkbox][operationMode=dinner]").attr("chkStatus","false");
			
			$("input[type=checkbox][operationMode=dinner]").prop('disabled',true);
			
			$("input[type=checkbox][operationMode=termsCondition]").prop("checked",false);
			$("input[type=checkbox][operationMode=termsCondition]").attr("chkStatus","false");	
			
			var currChkbxStatus = $(this).attr("chkStatus");
			var cutoffId =  $("#cutoff_id").val();
			
			if(currChkbxStatus=="true")
			{
				$(this).attr("chkStatus","false");	
			}
			else
			{		
				var regType 	= $(this).attr('operationModeType');
				var regClsfId 	= $(this).val();
				var currency 	= $(this).attr('currency');
				var offer 		= $(this).attr('offer');
				
				if(regType=='residential')
				{					
					if(offer == 'Y')
					{
						$("div[use=OfferRegIncludeServ]").show();
					}
					else
					{
						$("div[use=ResidentialRegIncludeServ]").show();
						$("div[use=ResidentialAccommodation]").show();
						
						onHotelSelected($("select[operationMode=hotel_select_id]"));
						
						var packageId = $(this).attr("accommodationPackageId");
						$("input[type=hidden][name=accomPackId]").attr("value",packageId);
						$("table[use=checkInCheckOutTable]").show();						
						$("tr[operetionMode=checkInCheckOutTr][use='"+packageId+"']").show();
						
						var accommodationType = $(this).attr("accommodationType");
						if(accommodationType=='SHARED')
						{
							$("div[use=ResidentialAccommodationAccompanyOption]").show();
						}
					}
					
					$("div[use=WorkshopReg]").show();					
					$("tr[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();					
					$("tr[operetionMode=JUSTshow]").show();	
				}
				else if(regType=='conference')
				{	
					$("select[operationMode=hotel_select_id]").val('');
				
					$("div[use=DelegateRegIncludeServ]").show();
					
					$("div[use=WorkshopReg]").show();					
					
					$("input[type=checkbox][operationMode=dinner]").prop('disabled',false);					
					
					$("tr[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();
					
					$("tr[operetionMode=dinnerTr][use="+currency+"]").show();
				}				
				$("tr[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();
				
				$(this).attr("chkStatus","true");
				$(this).prop("checked",true);
			}
			calculateTotalAmount();
		});
	});
	
	$("select[operationMode=hotel_select_id]").each(function(){
		$(this).change(function(){
			onHotelSelected($(this));			
		});
	 });
	
	
	$("input[type=checkbox][operationMode=dinner]").click(function(){
		calculateTotalAmount();															  
	});
	
	$("input[type=checkbox][operationMode=workshopId]").each(function(){
		$(this).click(function(){
			var regClsfId = $("input[type=checkbox][operationMode=registration_tariff]:checked").val();
			
			if($(this).is(":checked")){
				$("input[type=checkbox][operationMode=workshopId]").prop("checked",false);	
				$(this).prop("checked",true);
			}
			else
			{
				$("input[type=checkbox][operationMode=workshopId]").prop("checked",false);
				if(!regClsfId)
				{
					 regClsfId = 'XXX';
				}
				$("tr[operetionMode=workshopTariffTr][forType=GENERAL]").hide();
				$("tr[operetionMode=workshopTariffTr][forType=GENERAL][use="+regClsfId+"]").show();
			}
			calculateTotalAmount();
		});
	});	
	
});

function onHotelSelected(obj)
{
	$("input[type=checkbox][operationMode=registration_tariff]").prop("checked",false);	
	$("input[type=checkbox][operationMode=registration_tariff]").attr("chkStatus","false");			
	
	$("div[use=DelegateRegIncludeServ]").hide();	
	$("div[use=ResidentialRegIncludeServ]").hide();	
	$("div[use=ResidentialAccommodation]").hide();	
	$("div[use=ResidentialAccommodationAccompanyOption]").hide();
	$("div[use=WorkshopReg]").hide();	
	
	$("table[use=checkInCheckOutTable]").hide();
	$("tr[operetionMode=residenTariffTr]").hide();
	$("tr[use=residentialTaxDeclare]").hide();
	$("tr[operetionMode=dinnerTr]").hide();
	$("tr[operetionMode=JUSTshow]").hide();
	
	var hotal_id = $(obj).val();
	
	console.log(hotal_id);
	
	if(hotal_id!='')
	{
		$("div[use=ResidentialAccommodation]").show();	
		$("div[use=ResidentialRegIncludeServ]").show();	
		$("tr[operetionMode=residenTariffTr][hotel_id="+hotal_id+"]").show();	
		$("tr[use=residentialTaxDeclare]").show();
		$("div[use=DinnerReg]").show();
		$("tr[operetionMode=JUSTshow]").show();
	}
}

function calculateTotalAmount()
{
	var cutOffId		 	   = $("#cutoff_id").val();
	var parentDiv 			   = $("div[divTyp=parent][divId="+cutOffId+"]");	
	var regClsfId			   = $("input[type=checkbox][operationMode=registration_tariff]:checked").val();
	var regType				   = $("input[type=checkbox][operationMode=registration_tariff]:checked").attr('operationModeType');
	var currency  			   = $("input[type=checkbox][operationMode=registration_tariff]:checked").attr('currency');
	var regAmmount			   = $("input[type=checkbox][operationMode=registration_tariff]:checked").attr('ammount');
	
	var workshopAmt		       = $("input[type=checkbox][operationMode=workshopId]:checked").attr('ammount');
	if(regType !='residential')
	{
		var dinner		  		   = $("input[type=checkbox][operationMode=dinner]:checked").attr('ammount');
	}
	
	if(!dinner)
	{
		dinner = 0;
	}
	
	if(!workshopAmt)
	{
		workshopAmt = 0;
	}
	if(currency=='USD')
	{
		$("span[id=registrationCurrency]").text(currency);
	}
	else
	{
		$("span[id=registrationCurrency]").text('INR');
	}
	var totalAmmount = parseFloat(regAmmount) + parseFloat(workshopAmt) + parseFloat(dinner);
	
	if(totalAmmount)
	{
		$("span[id=ammount]").text(totalAmmount.toFixed(2));
	}
	else
	{
		$("span[id=ammount]").text('0.00');
	}
	//console.log(totalAmmount);
}

function validateRegTariffform()
{
	var status = 0;
	accessValidation	= true;
	var regType = $("input[type=checkbox][operationMode=registration_tariff]:checked").attr('operationModeType'); 
	var regTariffId = $("input[type=checkbox][operationMode=registration_tariff]:checked").length;
	var regClsfId = $("input[type=checkbox][operationMode=registration_tariff]:checked").val();
	if(regTariffId !=0 )
	{
		status = 1;
		
	}
	if(status == 0)
	{
		
		$('input[type=checkbox][operationMode=registration_tariff]').css('outline-color', '#D41000');
        $('input[type=checkbox][operationMode=registration_tariff]').css('outline-style', 'solid');
        $('input[type=checkbox][operationMode=registration_tariff]').css('outline-width', 'thin');
		$("html, body").animate({ scrollTop: 150 }, 1000);
		setTimeout(function(){$('input[type=checkbox][operationMode=registration_tariff]').css('outline-color', ''); 
							  $('input[type=checkbox][operationMode=registration_tariff]').css('outline-style', '');
       						  $('input[type=checkbox][operationMode=registration_tariff]').css('outline-width', '');}, 5000);
		alert("Please Select Conference");
		//cssAlert("table[use=registrationTariff]","Please Select Conference");
		accessValidation	= false;
		return false;
	}
	
	if($('input[type=checkbox][operationMode=termsCondition]:checked').length == 0)
	{
		$('input[type=checkbox][operationMode=termsCondition]').css('outline-color', '#D41000');
        $('input[type=checkbox][operationMode=termsCondition]').css('outline-style', 'solid');
        $('input[type=checkbox][operationMode=termsCondition]').css('outline-width', 'thin');
		alert("Please Accept the Terms & Conditions");
		//cssAlert("input[type=checkbox][operationMode=combo_acknowledge]","Please Accept the Terms & Conditions");
		accessValidation	= false;
		return false;
	}
	if($('input[type=checkbox][operationMode=workshopId]:checked').length == 0 )
	{
		$('input[type=checkbox][operationMode=workshopId]').css('outline-color', '#D41000');
        $('input[type=checkbox][operationMode=workshopId]').css('outline-style', 'solid');
        $('input[type=checkbox][operationMode=workshopId]').css('outline-width', 'thin');
		alert("Please Select A Workshop!!");
		//cssAlert("input[type=checkbox][operationMode=combo_acknowledge]","Please Accept the Terms & Conditions");
		accessValidation	= false;
		return false;
	}
	if($("input[type=radio][operationMode=registrationMode]:checked").length == 0)
	{
		$('input[type=radio][operationMode=registrationMode]').css('outline-color', '#D41000');
        $('input[type=radio][operationMode=registrationMode]').css('outline-style', 'solid');
        $('input[type=radio][operationMode=registrationMode]').css('outline-width', 'thin');
		
		alert("Please Select Registration Mode");
		setTimeout(function(){$('input[type=radio][operationMode=registrationMode]').css('outline-color', ''); 
							  $('input[type=radio][operationMode=registrationMode]').css('outline-style', '');
       						  $('input[type=radio][operationMode=registrationMode]').css('outline-width', '');}, 5000);
		accessValidation	= false;
		return false;
	}
	
	return accessValidation;
}
