/**********************************************************************/
/*                       SELECT REGISTRATION                          */
/**********************************************************************/
$(document).ready(function(){
   try{
	   var default_hotal_id = $("select[operationMode=hotel_select_id]").val();
		$("tr[operetionMode=residenTariffTr][hotel_id="+default_hotal_id+"]").show();
		$("input[type=checkbox][operationMode=registration_tariff]:checked").each(function(){
			var isShared = $(this).attr('isShared');
			if(isShared=='Y')
			{
				$("div[id=accdiv]").hide();
			}
			else{
				$("div[id=accdiv]").show();				
			}
		});
	   }
	   catch(e){}
	
	
	$("select[operationMode=hotel_select_id]").each(function(){
		$(this).change(function(){
			var hotal_id = $(this).val();
			$("tr[operetionMode=residenTariffTr]").hide();
			$("tr[operetionMode=residenTariffTr][hotel_id="+hotal_id+"]").show();
		});
	 });
	
	var operationModeType = $("input[type=checkbox][operationMode=registration_tariff]:checked").attr("operationModeType");
	if(operationModeType == "residential")
	{   
        
		$("input[type=checkbox][operationMode=workshopId]").prop("checked",false);
		$("input[type=checkbox][operationMode=workshopId]").prop('disabled',false);
		$("input[type=checkbox][operationMode=dinner]").prop("checked",false);
		$("input[type=checkbox][operationMode=dinner]").prop('disabled','disabled');
	}
	
	$("input[type=checkbox][operationMode=registration_tariff]").each(function(){
		$(this).click(function(){	 
			
			console.log('>>'+$(this).attr("chkStatus"));
			
			var currChkbxStatus = $(this).attr("chkStatus");
			
			$("div[use=DelegateRegIncludeServ]").hide();
			$("div[use=ResidentialRegIncludeServ]").hide();
			$("div[use=ResidentialAccommodation]").hide();
			$("div[use=OfferRegIncludeServ]").hide();
			$("div[use=WorkshopReg]").hide();
										   
			$("input[type=checkbox][operationMode=registration_tariff]").prop("checked",false);	
			$("input[type=checkbox][operationMode=registration_tariff]").attr("chkStatus","false");
			
			$("tr[operetionMode=workshopTariffTr]").hide();
			$("tr[operetionMode=checkInCheckOutTr]").hide();
			
			$("input[type=checkbox][operationMode=workshopId]").prop("checked",false);
			$("input[type=checkbox][operationMode=workshopId]").attr("chkStatus","false");	
			
			console.log('>w>'+$(this).attr("chkStatus"));
			
			if(currChkbxStatus=="true")
			{
				$(this).attr("chkStatus","false");	
			}
			else
			{	
				$(this).attr("chkStatus","true");
				$(this).prop("checked",true);
				
			
				var regType = $(this).attr('operationModeType');
				var isShared = $(this).attr('isShared');
				var regClsfId = $(this).val();
				var currency = $(this).attr('currency');
				var offer = $(this).attr('offer');
				
				if(regType=='residential'){
					if(isShared=='Y')
					{
						$("div[id=accdiv]").hide();
						$("table[use=optionTable]").show();
					}
					else
					{
						$("div[id=accdiv]").show();
						$("table[use=optionTable]").hide();
					}
					$("div[use=DelegateRegIncludeServ]").hide();
					
					if(offer == 'Y')
					{
						$("div[use=ResidentialAccommodation]").hide();
					}
					else
					{
						$("div[use=ResidentialAccommodation]").show();
						
						var packageId = $(this).attr("accommodationPackageId");
						$("input[type=hidden][name=accomPackId]").attr("value",packageId);
						//alert($packageId);
						$("tr[operetionMode=checkInCheckOutTr]").hide();
						$("tr[operetionMode=checkInCheckOutTr][use='"+packageId+"']").show();
					}
					
					$("div[use=WorkshopReg]").show();
					$("tr[operetionMode=workshopTariffTr]").hide();
					$("tr[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();
					$("input[type=checkbox][operationMode=workshopId]").prop("checked",false);
					$("input[type=checkbox][operationMode=workshopId]").prop('disabled',false);
					$("input[type=checkbox][operationMode=dinner]").prop("checked",false);
					$("input[type=checkbox][operationMode=dinner]").prop('disabled','disabled');
				}
				else if(regType=='conference')
				{
					$("div[use=ResidentialAccommodation]").hide();
					
					$("div[use=WorkshopReg]").show();
					$("tr[operetionMode=checkInCheckOutTr]").hide();
					$("tr[operetionMode=workshopTariffTr]").hide();
					$("input[type=checkbox][operationMode=workshopId]").prop('disabled',false);
					$("input[type=checkbox][operationMode=dinner]").prop('disabled',false);
					$("input[type=checkbox][operationMode=workshopId]").prop("checked",false);
					$("input[type=checkbox][operationMode=dinner]").prop("checked",false);
					
					
					$("tr[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();
					$("div[id=accdiv]").show();
				}
				
				
				
				$("tr[operetionMode=workshopTariffTr][use="+regClsfId+"]").show();
				
				$("input[type=checkbox][operationMode=dinner]").prop("checked",false);	
				$("tr[operetionMode=dinnerTr]").hide();
				$("tr[operetionMode=dinnerTr][use="+currency+"]").show();				
				
				
			}
			
			console.log($(this).attr("chkStatus"));
			
			var cutoffId =  $("#cutoff_id").val();
			
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



/**********************************************************************/
/*                         Choose Dinner Option                       */
/**********************************************************************/
function dinnerChoice(obj)
{	
	var parent = $(obj).parent().closest('tr');
	if($(obj).val() == 'YES' )
	{
		$(parent).find("input[type=checkbox][operationMode=dinner]").prop('checked', true);
	}
	else
	{
		$(parent).find("input[type=checkbox][operationMode=dinner]").prop('checked', false);
	}
	
	//calculateTotalAmount();
}

/**********************************************************************/
/*                         ADD MORE TEMPLATE                          */
/**********************************************************************/
function addMoreTemplate(placeholderDiv, templateDiv)
{	

	var totalTables  = parseInt($('#'+placeholderDiv+' div[operationMode=accompanyPerson]').length);
	
	var clasfId 	 = $('#delegateClasfId').val();
	var ID  			   = $("input[type=checkbox][operationMode=registration_tariff]:checked").val();
	var nextCounter  = totalTables + 1;
	var getTemplate  = $("#"+templateDiv).clone();
	var modifiedTemp = getTemplate.html().replace(/\#COUNTER/g,nextCounter);;
	$('#'+placeholderDiv).append(modifiedTemp);
	removeTableRow();
	var maxLimit = 4;
	if(nextCounter>=maxLimit)
	{
		$('a[operationMode=addAccompanyButton]').css("display","none");
	}
	$('a[operationMode=removeRow][sequenceBy=0]').css("display","none");
	$('div[class=dvdr][sequenceBy=1]').css("width","93%");
	
	$("input[operationMode=accompany_name], input[operationMode=accompany_age], input[operationMode=accompany_relationship]").blur(function(){
		
		calculateTotalAmount();
	});
	
	$("input[operationMode=accompany_name], input[operationMode=accompany_age], input[operationMode=accompany_relationship]").keyup(function(){
		
		calculateTotalAmount();
	});
}

/**********************************************************************/
/*                         ADD MORE ACCOMPANY                         */
/**********************************************************************/
function addMoreAccompany(placeholderDiv, templateDiv)
{
	addMoreTemplate(placeholderDiv, templateDiv);
	//calculateTotalAmount();
}

/**********************************************************************/
/*                               REMOVE ROW                           */
/**********************************************************************/
function removeTableRow()
{
	$("a[operationMode=removeRow]").each(function(){
		$(this).click(function(){
			
			var sequenceBy = $(this).attr("sequenceBy");
			$("div[operationMode=accompanyPerson][sequenceBy="+sequenceBy+"]").remove();
			calculateTotalAmount();
			
			var totalTables  = parseInt($('#addMoreAccompany_placeholder div[operationMode=accompanyPerson]').length);	
			var nextCounter  = totalTables + 1;
			if(nextCounter<=4)
			{
				$('a[operationMode=addAccompanyButton]').css("display","inline");
				
			}
			//alert(nextCounter);
			
			$('a[operationMode=removeRow][sequenceBy=1]').css("display","none");
			$('div[class=dvdr][sequenceBy=1]').css("width","93%");
			
			
		});
	});
}

function editformvalidation()
{
	var status = 0;
	accessValidation	= true;
	var regType = $("input[type=checkbox][operationMode=registration_tariff]:checked").attr('operationModeType'); 
	var regTariffId = $("input[type=checkbox][operationMode=registration_tariff]:checked").length;
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
	if($('input[type=checkbox][operationMode=workshopId]:checked').length == 0 && regType=='conference')
	{
		$('input[type=checkbox][operationMode=workshopId]').css('outline-color', '#D41000');
        $('input[type=checkbox][operationMode=workshopId]').css('outline-style', 'solid');
        $('input[type=checkbox][operationMode=workshopId]').css('outline-width', 'thin');
		alert("Please Select A Workshop!!");
		//cssAlert("input[type=checkbox][operationMode=combo_acknowledge]","Please Accept the Terms & Conditions");
		accessValidation	= false;
		return false;
	}
	
	return accessValidation;
}

function validationaccompany()
{
	 $("input[type=checkbox][operationMode=accompanytariff]").prop("disabled",true);	
	 $("input[type=checkbox][operationMode=accompanytariff]").prop("checked",false);
}

function workshopOpen(obj)
{
	var sessionType = $(obj).attr("use");
	var sesionid = $(obj).attr("count");
	if(sesionid==1){var oppsite = 2;}
	if(sesionid==2){var oppsite = 1;}
	//$("input[type=checkbox][operationMode=workshopId_session"+sesionid+"]").prop("checked",false);
	if($(obj).is(":checked")){
		var regClsfId 	 = $("input[type=checkbox][operationMode=registration_tariff]:checked").val();
		
	
		if(!regClsfId)
		{
			 regClsfId = 'XXX';
		}
		
		$("tr[operetionMode=workshopTariffTr][forType="+sessionType+"]").hide();
		var regClsfId 	 = $("input[type=checkbox][operationMode=registration_tariff]:checked").val();
		$("tr[operetionMode=workshopTariffTr][forType="+sessionType+"][use="+regClsfId+"]").show();
		//$("tr[operetionMode=workshopTariffTr][forType="+sessionType+"][use="+regClsfId+"][workshopName="+workshopName+"]").hide();
	}
	else{
		$("tr[operetionMode=workshopTariffTr][forType="+sessionType+"]").hide();
		$("tr[operetionMode=workshopTariffTr][forType="+sessionType+"][use=SELECT]").show();
	}
	
	//calculateTotalAmount();
}




