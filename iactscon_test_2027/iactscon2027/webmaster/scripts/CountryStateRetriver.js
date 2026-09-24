/*******************************************************/
/*                     GET STATE LIST                  */
/*******************************************************/
$(document).ready(function(){
	console.log("stateRetriverReady");
	$.each($("select[forType=countryState]"), function(){
		stateRetriver(this);
	});
});

function stateRetriver(obj)
{
	var sequenceVal        = $(obj).attr("sequence");
	var countryValue       = $(obj).val();
	var stateControl       = $(obj).attr("stateId");
	var stateControlDiv    = $('#'+stateControl).parent().closest("div[use=stateContainer]");	
	
	if(countryValue!="")
	{
		console.log(jsWemaster_BASE_URL+'section_login/countryState.process.php?act=getStateControl&countryValue='+countryValue+'&stateControl='+stateControl+'&sequenceVal='+sequenceVal);
		var act='getStateControl';
		$.ajax({
			type: "POST",
			url: jsWemaster_BASE_URL+"section_login/countryState.process.php",
			data: 'act=getStateControl&countryValue='+countryValue+'&stateControl='+stateControl+'&sequenceVal='+sequenceVal,
			dataType: "html",
			async:false,
			success: function(message){
				if(message!="")
				{
					$(stateControlDiv).html('');
					$(stateControlDiv).html(message);
				}
			}
		});
	}
	else
	{
		console.log(jsWemaster_BASE_URL+'section_login/countryState.process.php?act=getBlankStateControl&countryValue='+countryValue+'&stateControl='+stateControl+'&sequenceVal='+sequenceVal);
		$.ajax({
			type: "POST",
			url: jsWemaster_BASE_URL+"section_login/countryState.process.php",
			data: 'act=getBlankStateControl&countryValue='+countryValue+'&stateControl='+stateControl+'&sequenceVal='+sequenceVal,
			dataType: "html",
			async:false,
			success: function(message){
				if(message!="")
				{
					$(stateControlDiv).html('');
					$(stateControlDiv).html(message);
				}
			}
		});
	}
}