$(document).ready(function(){
	
	$("input[operationMode=award_review_marks]").click(function(){
		obtainedMarksCalculation();
	});
	
	$("input[operationMode=award_review_marks]").blur(function(){
		obtainedMarksCalculation();
	});
	
	$("input[operationMode=award_review_marks]").keyup(function(){
		obtainedMarksCalculation();
	});
	
	$("#frmAwardReview").submit(function(){
		
		if(reviewFrom.validation()==0)
		{
			return false;
		}
	
	});
	
});

function obtainedMarksCalculation()
{
	var totalMarksObtained = 0; 
	
	$("input[type=radio][operationMode=award_review_marks]").each(function(){
	
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
	
	$("input[type=text][operationMode=award_review_marks]").each(function(){
	
		var optionValue    	   = 0;
			
		if($(this).val()=="" || isNaN($(this).val())==true)
		{
			optionValue        = 0;
		}
		else
		{
			optionValue        = $(this).val();
		}
		
		totalMarksObtained     = parseFloat(totalMarksObtained) + parseFloat(optionValue);
		
	});
	
	$("span[operationMode=award_review_marks_obtained]").text("");
	$("span[operationMode=award_review_marks_obtained]").text(totalMarksObtained);
}

var reviewFrom = {
	
	validation : function(){
		
		var validationAccess        = 1;
		
		$("input[operationMode=award_review_criteria_id]").each(function(){
			
			var reviewCriteriaId	= $(this).val();
			var criteriaTitle       = $(this).attr("criteriaTitle");
			
			if($("input[type=radio][operationMode=award_review_marks][criteriaId="+reviewCriteriaId+"]").length>0)
			{
				if($("input[type=radio][operationMode=award_review_marks][criteriaId="+reviewCriteriaId+"]:checked").length==0)
				{
					alert("Please Select Option For: "+criteriaTitle);
					
					validationAccess    = 0;
					return false;
				}
			}
			if($("input[type=text][operationMode=award_review_marks][criteriaId="+reviewCriteriaId+"]").length>0)
			{
				if($("input[type=text][operationMode=award_review_marks][criteriaId="+reviewCriteriaId+"]").val()=="")
				{
					alert("Please Enter: "+criteriaTitle);
					
					validationAccess    = 0;
					return false;
				}
				if(fieldShouldDecimalValidate("input[type=text][operationMode=award_review_marks][criteriaId="+reviewCriteriaId+"]", "Invalid Entry For: "+criteriaTitle)==false)
				{
					validationAccess    = 0;
					return false;
				}
				if($("input[type=text][operationMode=award_review_marks][criteriaId="+reviewCriteriaId+"]").val()>10)
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