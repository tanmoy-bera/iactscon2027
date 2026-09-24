$(document).ready(function(){

	checkAllWebPageProcess();
	
});

function checkAllWebPageProcess()
{
	$("input[operationMode=checkAllWebPage]").each(function(){
		$(this).click(function(){
			
			var sectionId = $(this).attr("sectionId");
			
			if($(this).is(':checked')) 
			{
				$("input[operationMode=webPage][sectionId="+sectionId+"]").prop('checked', true);
		  	}
		  	else 
			{
				$("input[operationMode=webPage][sectionId="+sectionId+"]").prop('checked', false);
		  	}
			
		});
	});
}