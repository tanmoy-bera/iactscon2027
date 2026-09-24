$(document).ready(function(){
	
	$("input[operationMode=abstract_review_marks]").click(function(){
		obtainedMarksCalculation();
	});
	
	$("input[operationMode=abstract_review_marks]").blur(function(){
		//obtainedMarksCalculation();
	});
	
	$("input[operationMode=abstract_review_marks]").keyup(function(){
		
		obtainedMarksCalculation();
	});
	
	$("#frmAbstractReview").submit(function(){
		
		if(reviewFrom.validation()==0)
		{
			return false;
		}
	
	});


/*$("input[type=text][operationMode=abstract_review_marks]").keyup(function() {
    var $input = $(this);
    var totalValue = parseFloat($(this).attr('marks'));
    var enteredValue = parseFloat($input.val());
    var textTotalMarks = 0;
    console.log('val=',enteredValue);
			console.log('marks=',totalValue);
	if(!isNaN(enteredValue))
	{
		if (!isNaN(totalValue) && !isNaN(enteredValue) && enteredValue > totalValue) {
	     $("input[type=text][operationMode=abstract_review_marks]").val("");
	    
	    } else {
	      
	      console.log('total=',textTotalMarks);
	     
	    }

	    
	}
		
    
});
*/

	
});



function obtainedMarksCalculation()
{
	var totalMarksObtained = 0; 

	
	
	$("input[type=radio][operationMode=abstract_review_marks]").each(function(){
	
		if($(this).is(":checked")==true)
		{
			var optionValue    = 0;
			
			if($(this).attr("reviewMarksId")=="" || isNaN($(this).attr("reviewMarksId"))==true)
			{
				optionValue    = 0;
			}
			else
			{
				optionValue    = $(this).attr("reviewMarksId");
			}
			
			totalMarksObtained = parseFloat(totalMarksObtained) + parseFloat(optionValue);
		}
		
	});


	
	$("input[type=text][operationMode=abstract_review_marks]").each(function(){
	
		var optionValue    	   = 0;
		var marks = $(this).attr('marks');
		var countdata = $(this).attr('countdata');

			
		if($(this).val()=="" || isNaN($(this).val())==true)
		{
			optionValue        = 0;
		}
		else
		{

			console.log('val=',$(this).val());
			console.log('marks=',marks);
			if($.trim($(this).val()) > $.trim(marks))
			{
				console.log(11);
				$('#abstract_review_option_id'+countdata).val("");
				
			}
			else
			{
				optionValue  = $(this).val();
				textboxVal =$(this).val();
			}

			

		}
		
		totalMarksObtained     = parseFloat(totalMarksObtained) + parseFloat(optionValue);
		
	});

	
	
	$("span[operationMode=abstract_review_marks_obtained]").text("");
	$("span[operationMode=abstract_review_marks_obtained]").text(totalMarksObtained);
}

var reviewFrom = {
	
	validation : function(){
		
		var validationAccess        = 1;
		
		$("input[operationMode=abstract_review_criteria_id]").each(function(){
			
			var reviewCriteriaId	= $(this).val();
			var criteriaTitle       = $(this).attr("criteriaTitle");
			var fullMarks  			= $(this).attr("fullMarks");
			
			var targetObj 		    = $("[operationMode=abstract_review_marks][criteriaId="+reviewCriteriaId+"]");
			
			/*if($(targetObj).val()=="")
			{
				alert("Please Rate on: "+criteriaTitle);
				$(targetObj).focus();
				validationAccess    = 0;
				return false;
			}
			
			if(isNaN($(targetObj).val()))
			{
				alert("Please use Numeric value to Rate on: "+criteriaTitle);
				$(targetObj).focus();
				validationAccess    = 0;
				return false;
			}
			
			if((parseInt($(targetObj).val())*1000)%1000 > 0)
			{
				alert("Please Rate in Whole Numbers on: "+criteriaTitle);
				$(targetObj).focus();
				validationAccess    = 0;
				return false;
			}
			
			if(parseInt($(targetObj).val()) > 5)
			{
				alert("Please Rate within Full Marks on: "+criteriaTitle);
				$(targetObj).focus();
				validationAccess    = 0;
				return false;
			}*/
			
			if($("input[type=text][operationMode=abstract_review_marks][criteriaId="+reviewCriteriaId+"]").length>0)
			{
				if($("input[type=text][operationMode=abstract_review_marks][criteriaId="+reviewCriteriaId+"]").val()=="")
				{
					alert("Please Enter: "+criteriaTitle);
					
					validationAccess    = 0;
					return false;
				}
				if(fieldShouldDecimalValidate("input[type=text][operationMode=abstract_review_marks][criteriaId="+reviewCriteriaId+"]", "Invalid Entry For: "+criteriaTitle)==false)
				{
					validationAccess    = 0;
					return false;
				}
				if($("input[type=text][operationMode=abstract_review_marks][criteriaId="+reviewCriteriaId+"]").val()>10)
				{
					alert("Invalid Entry For: "+criteriaTitle);
					
					validationAccess    = 0;
					return false;
				}
			}
			
		});
		
		return validationAccess;		
	}
}