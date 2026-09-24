$(document).ready(function(){
	jBaseUrl = jsBASE_URL;
	
	$("textarea[checkFor=wordCount]").keyup(function(){		
		wordLimitCounter(this);
	});
	
	$("textarea[checkFor=wordCount]").blur(function(){		
		wordLimitCounter(this);
	});
	
	
	$('input[required]').on('invalid', function(e){
	  	alert("Error, please fill all required fields before submitting.");
	});
	
	
	
	
	
	
	
	
	
	if(false)
	{
	
	
	try
	{
		addMoreTemplate1_CR("addMoreCoAuthor_placeholder_CR", "addMoreCoAuthor_template_CR");
	}
	catch(e)
	{
	}
	
	try
	{
		addMoreTemplateEditMode_CR("addMoreCoAuthor_edit_placeholder_CR", "addMoreCoAuthor_edit_template_CR");
	} 
	catch(e)
	{
	}
	
	
	$("textarea[operationMode=abstractWordCounter]").keyup(function(){		
		wordLimitCounter(this);
	});
	
	$("textarea[operationMode=abstractWordCounter]").blur(function(){
		wordLimitCounter(this);
	});
	
	$("textarea[operationMode=casereportWordCounter]").keyup(function(){
		CasereportwordLimitCounter(this);
	});
	
	$("textarea[operationMode=casereportWordCounter]").blur(function(){
		CasereportwordLimitCounter(this);
	});
	
	
	try
	{
		abstractAlertMaker();
	}
	catch(e){}
	
	try
	{
		generateStateOptionList();
	}
	catch(e){}	
	
	$("a[operationMode=addMoreCoAuthorButton1]").click(function(){
		addMoreTemplate1("addMoreCoAuthor_placeholder", "addMoreCoAuthor_template");
		
	});
	
	$("a[operationMode=addMoreCoAuthorButtonEditMode]").click(function(){
		addMoreTemplateEditMode("addMoreCoAuthor_edit_placeholder", "addMoreCoAuthor_edit_template");
		
	});
	
	$("a[operationMode=addMoreCoAuthorButton1_CR]").click(function(){
		addMoreTemplate1_CR("addMoreCoAuthor_placeholder_CR", "addMoreCoAuthor_template_CR");
		
	});
	
	$("a[operationMode=addMoreCoAuthorButtonEditMode_CR]").click(function(){
		addMoreTemplateEditMode_CR("addMoreCoAuthor_edit_placeholder_CR", "addMoreCoAuthor_edit_template_CR");
		
	});
	
	$("#abstract_title").keyup(function(){	
		abstractTitleWordLimitCounterSubmit(this);
	});
	
	$("#abstract_title").blur(function(){
		abstractTitleWordLimitCounterSubmit(this);
	});
	
	$("#casereport_title").keyup(function(){
		casereportTitleWordLimitCounterSubmit(this);
	});
	
	$("#casereport_title").blur(function(){
		casereportTitleWordLimitCounterSubmit(this);
	});
	
	$("div[for=AbstractEdit]").find("textarea[name=abstract_edit_title]").keyup(function(){
		console.log('ttt');
		abstractTitleWordLimitCounter(this);
	});
	
	$("div[for=AbstractEdit]").find("textarea[name=abstract_edit_title]").blur(function(){			//	input[type=text][fieldType=abstractTitle]
		abstractTitleWordLimitCounter(this);
	});
	
	$("div[for=CaseEdit]").find("textarea[name=abstract_edit_title]").keyup(function(){
		casereportTitleWordLimitCounter(this);
	});
	
	$("div[for=CaseEdit]").find("textarea[name=abstract_edit_title]").blur(function(){			//	input[type=text][fieldType=abstractTitle]
		casereportTitleWordLimitCounter(this);
	});
	
	var totalExistingCaseFile = $("#totalExistingCaseFile").text();
	
	if(totalExistingCaseFile == '')
	{
		totalExistingCaseFile = 0;	
	}
	try
	{
		if(totalExistingCaseFile >= 0 && totalExistingCaseFile < 15)
		{
			for(var i=1; i<=1;i++)
			{
				addMoreCaseTemplate("addMoreCase_placeholder", "addMoreCase_template");
			}
			if(totalExistingCaseFile == 0)
			{
				$("a[operationMode=removeCaseRow][fortype=removeFile]").first().css("display","none");
			}
		}
	}catch(e){}
	
	$("a[operationMode=addMoreCaseButton]").click(function(){
		addMoreCaseTemplate("addMoreCase_placeholder", "addMoreCase_template",count=0);
	});
	
	$("#upload_case_file").change(function(){
        var allowed_extensions = new Array("doc","docx","jpg","png","avi","mp4");	//	jpg, png, <br />avi & mp4
       
        var attr = $(this).attr('allowedExtensions'); //For Limited Files (Ex. -  allowedExtensions='jpg,jpeg')
        if (typeof attr !== typeof undefined && attr !== false) {
            if($(this).attr("allowedExtensions")!=''){
                var alExt = $(this).attr("allowedExtensions");
                allowed_extensions = alExt.split(",");
            }
        }
        
        var fileName = $(this).val();
        var file_extension = fileName.split('.').pop();
   
        for(var i = 0; i <= allowed_extensions.length; i++)
        {
            if($.trim(allowed_extensions[i])==file_extension)
            {
                return true;
            }
        }
       
        alert("Please Enter Proper File Type.");
        //$(this).replaceWith($(this).clone());
        //$(this).after($(this).clone(true)).remove();
        $(this).val('');
        $(this).focus();
    });
	
	
	}
	
});

function abstractRequestFormValidation(obj)
{
	try
	{
		if(abstractAddValidatePresenter(obj))
			if(abstractAddValidateAuthor(obj))
				if(abstractAddValidateCoAuthor(obj))
					if(abstractAddValidateDetails(obj))
					{
						console.log("has=>"+$(obj).find("input[type=submit][name=submit]").length);
						if( $(obj).find("input[type=submit][name=submit]").attr('isDisplay') == 'Y')
						{
							return true;	
						}
						else
						{	
							return false;	
						}
					}
					else return false;
				else return false;
			else return false;
		else return false;		
		return false;
	}
	catch(e)
	{
		console.log(e.message);
	}
	return false;
}

function abstractAddValidatePresenter(obj)
{
	if(fieldNotEmpty($(obj).find('#abstract_presenter_institute_name'), "Please Enter Presenter Institute Name") == false){ return false; } 
	if(fieldNotEmpty($(obj).find('#abstract_presenter_department'), "Please Enter Presenter Department Name") == false){ return false; }	
	return true;
}

function abstractAddValidateAuthor(obj)
{
	if(fieldNotEmpty($(obj).find('#abstract_author_name'), "Please Enter Author Name") == false){ return false; }							
	if(fieldNotEmpty($(obj).find('#abstract_author_institute_name'), "Please Enter Institute Name") == false){ return false; }				
	if(fieldNotEmpty($(obj).find('#abstract_author_department'), "Please Enter Department Name") == false){ return false; }					
	
	if(fieldNotEmpty($(obj).find('#abstract_author_country'), "Please Select Author Country") == false){ return false; }					
	if(fieldNotEmpty($(obj).find('#abstract_author_state_id'), "Please Select Author State") == false){ return false; }						
	if(fieldNotEmpty($(obj).find('#abstract_author_city'), "Please Enter Author City") == false){ return false; }							
	
	if(fieldNotEmpty($(obj).find('#abstract_author_phone_isd_code'), "Please Enter isd code") == false){ return false; }					
	if(fieldNotEmpty($(obj).find('#abstract_author_phone_no'), "Please Enter mobile number") == false){ return false; }	
	return true;
}

function abstractAddValidateCoAuthor(obj)
{
	var coAuthorSttatus = true;
	$.each($(obj).find("div[use=abstractCoAuthor_placeholder]").find("div[use=clonableDiv][operationMode=coauthor]"),function(){
		var name = $.trim($(this).find('input[defaultName=abstract_coauthor_name]').val());
		if(name!='')
		{
			if(fieldNotEmpty($(this).find('select[defaultName=abstract_coauthor_country]'), "Please select country") == false){ coAuthorSttatus = false; return false; } 
			if(fieldNotEmpty($(this).find('select[defaultName=abstract_coauthor_state]'), "Please select state") == false){ coAuthorSttatus = false; return false; }			
		}		
	});
	
	if(coAuthorSttatus == false){ return false; } 
	return true;
}

function abstractAddValidateDetails(obj)
{
	if(fieldNotEmpty($(obj).find('#abstract_child_type'), "Please Select Abstract Type") == false){ return false; } 
	if(fieldNotEmpty($(obj).find('#abstract_topic_id'), "Please Select Abstract Topic") == false){ return false; } 
	if(fieldNotEmpty($(obj).find('#abstract_study'), "Please Select Study") == false){ return false; } 
	
	if($(obj).find('#abstract_title').length> 0 && $(obj).find('#abstract_title').parent().closest('tr').attr('showing')!='N') 
	{
		if(fieldNotEmpty($(obj).find('#abstract_title'), "Please Enter Abstract Title") == false){ return false; } 
	}
	
	if($(obj).find('#abstract_background').length> 0 && $(obj).find('#abstract_background').parent().closest('tr').attr('showing')!='N')
	{
		if(fieldNotEmpty($(obj).find('#abstract_background'), "Please Enter Background") == false){ return false; } 
	}
	
	if($(obj).find('#abstract_background_aims').length> 0 && $(obj).find('#abstract_background_aims').parent().closest('tr').attr('showing')!='N')
	{
		if(fieldNotEmpty($(obj).find('#abstract_background_aims'), "Please Enter Aims & Objectives") == false){ return false; } 
	}
	
	if($(obj).find('#abstract_material_methods').length> 0 && $(obj).find('#abstract_material_methods').parent().closest('tr').attr('showing')!='N')
	{
		if(fieldNotEmpty($(obj).find('#abstract_material_methods'), "Please Enter Methods and Material") == false){ return false; } 
	}
	
	if($(obj).find('#abstract_results').length> 0 && $(obj).find('#abstract_results').parent().closest('tr').attr('showing')!='N')
	{
		if(fieldNotEmpty($(obj).find('#abstract_results'), "Please Enter Results") == false){ return false; } 
	}
	
	if($(obj).find('#abstract_description').length> 0 && $(obj).find('#abstract_description').parent().closest('tr').attr('showing')!='N')
	{
		if(fieldNotEmpty($(obj).find('#abstract_description'), "Please Enter Description") == false){ return false; } 
	}
	
	if($(obj).find('#abstract_conclusion').length> 0 && $(obj).find('#abstract_conclusion').parent().closest('tr').attr('showing')!='N')
	{
		if(fieldNotEmpty($(obj).find('#abstract_conclusion'), "Please Enter Conclusion") == false){ return false; } 
	}
	
	
	var showTitleWordCount 	= $(obj).find("span[use=abstract_title_word_count]");
	var titleWordLimit		= parseInt($(showTitleWordCount).attr('limit'));
	var titleWordEntered 	= parseInt($(showTitleWordCount).find("span[use=total_word_entered]").text());
			
	if(titleWordEntered == 0)
	{
		alert("Total Title Word Count Is Zero");
		return false;
	}
	else if(titleWordEntered > titleWordLimit)
	{	
		alert("Total Word Entered Is Greater Than Word Limit");
		return false;
	}
	
	var showContentWordCount 	= $(obj).find("span[use=abstract_total_word_display]");
	var contentWordLimit		= parseInt($(showContentWordCount).attr('limit'));
	var contentWordEntered 		= parseInt($(showContentWordCount).find("span[use=total_word_entered]").text());
			
	if(contentWordEntered==0)
	{
		alert("Total Title Word Count Is Zero");
		return false;
	}
	else if(contentWordEntered > contentWordLimit)
	{	
		alert("Total Word Entered Is Greater Than Word Limit");
		return false;
	}
	
	if($(obj).find('#abstract_child_type').first().val()=='VIDEO')
	{
		if(fieldNotEmpty( $(obj).find('#upload_abstract_video'), "Please upload Video") == false){ return false; }
	}
	return true;
}



function abstractEditValidationform(obj)
{
	try
	{
		var indx = $(obj).attr('indx');	
		
		if(fieldNotEmpty($(obj).find('#abstract_edit_author_name'+indx), "Please enter name") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_edit_country'+indx), "Please select country") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_edit_state_id'+indx), "Please select state") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_edit_city'+indx), "Please enter city") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_edit_phone_isd_code'+indx), "Please enter isd code") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_edit_phone_no'+indx), "Please enter mobile number") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_edit_institute_name'+indx), "Please enter institute name") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_edit_department'+indx), "Please enter department name") == false){ return false; }
		
		var coAuthorSttatus = true;
		$.each($(obj).find("div[use=addMoreCoAuthor_edit_placeholder]").find("div[use=clonableDiv][operationMode=coauthor]"),function(){
			var name = $.trim($(this).find('input[defaultName=abstract_coauthor_name]').val());
			if(name!='')
			{
				if(fieldNotEmpty($(this).find('select[defaultName=abstract_coauthor_country]'), "Please select country") == false){ coAuthorSttatus = false; return false; }
				if(fieldNotEmpty($(this).find('select[defaultName=abstract_coauthor_state]'), "Please select state") == false){ coAuthorSttatus = false; return false; }				
			}		
		});
		
		if(coAuthorSttatus == false){ return false; }
		
		
		if(fieldNotEmpty($(obj).find('#abstract_edit_topic_id'+indx), "Please select topic") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_edit_study'+indx), "Please select study type") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_edit_title'+indx), "Please enter title") == false){ return false; }
		
		if(fieldNotEmpty($(obj).find('#abstract_background_data'+indx), "Please Enter Background") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_background_aims_data'+indx), "Please Enter Aims & Objectives") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_material_methods_data'+indx), "Please Enter Methods and Material") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_results_data'+indx), "Please Enter Results") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_description_data'+indx), "Please Enter Description") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_conclusion_data'+indx), "Please Enter Conclusion") == false){ return false; }
		
		
		var showTitleWordCount 	= $(obj).find("span[use='abstract_title_word_count"+indx+"']");
		var titleWordLimit		= parseInt($(showTitleWordCount).attr('limit'));
		var titleWordEntered 	= parseInt($(showTitleWordCount).find("span[use=total_word_entered]").text());
		
		if(titleWordEntered == 0)
		{
			alert("Total Title Word Count Is Zero");
			return false;
		}
		else if(titleWordEntered > titleWordLimit)
		{	
			alert("Total Word Entered Is Greater Than Word Limit");
			return false;
		}
		
		
		var showContentWordCount 	= $(obj).find("span[use='abstract_total_word_display"+indx+"']");
		var contentWordLimit		= parseInt($(showContentWordCount).attr('limit'));
		var contentWordEntered 		= parseInt($(showContentWordCount).find("span[use=total_word_entered]").text());
				
		if(contentWordEntered==0)
		{
			alert("Total Title Word Count Is Zero");
			return false;
		}
		else if(contentWordEntered > contentWordLimit)
		{	
			alert("Total Word Entered Is Greater Than Word Limit");
			return false;
		}
		
		if( $(obj).find("input[type=submit][name=submit]").attr('isDisplay') == 'Y')
		{
			return true;	
		}
		else
		{
			return false;	
		}
		return false;
	}
	catch(e)
	{ 
		return false; 
	}
}

function caseReportRequestFormValidation(obj)
{	
	try
	{
		if(fieldNotEmpty($(obj).find('#abstract_presenter_institute_name'), "Please Enter Presenter Institute Name") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_presenter_department'), "Please Enter Presenter Department Name") == false){ return false; }		
		if(fieldNotEmpty($(obj).find('#abstract_author_name'), "Please Enter Author Name") == false){ return false; }		
		if(fieldNotEmpty($(obj).find('#abstract_author_institute_name'), "Please Enter Institute Name") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_department'), "Please Enter Department Name") == false){ return false; }
		
		if(fieldNotEmpty($(obj).find('#abstract_author_country'), "Please Select Author Country") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_state_id'), "Please Select Author State") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_city'), "Please Enter Author City") == false){ return false; }
		
		if(fieldNotEmpty($(obj).find('#abstract_author_phone_isd_code'), "Please Enter isd code") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_author_phone_no'), "Please Enter mobile number") == false){ return false; }
		
		var coAuthorSttatus = true;
		$.each($(obj).find("div[use=abstractCoAuthor_placeholder]").find("div[use=clonableDiv][operationMode=coauthor]"),function(){
			var name = $.trim($(this).find('input[defaultName=abstract_coauthor_name]').val());
			if(name!='')
			{
				if(fieldNotEmpty($(this).find('select[defaultName=abstract_coauthor_country]'), "Please select country") == false){ coAuthorSttatus = false; return false; }
				if(fieldNotEmpty($(this).find('select[defaultName=abstract_coauthor_state]'), "Please select state") == false){ coAuthorSttatus = false; return false; }				
			}		
		});
		
		if(coAuthorSttatus == false){ return false; }
		
		if(fieldNotEmpty($(obj).find('#abstract_child_type'), "Please Select Abstract Type") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_topic_id'), "Please Select Abstract Topic") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_study'), "Please Select Study") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_title'), "Please Enter Abstract Title") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_background'), "Please Enter Background") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_background_aims'), "Please Enter Aims & Objectives") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_material_methods'), "Please Enter Methods and Material") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_results'), "Please Enter Results") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_description'), "Please Enter Description") == false){ return false; }
		if(fieldNotEmpty($(obj).find('#abstract_conclusion'), "Please Enter Conclusion") == false){ return false; }
		
		
		var showTitleWordCount 	= $(obj).find("span[use=abstract_title_word_count]");
		var titleWordLimit		= parseInt($(showTitleWordCount).attr('limit'));
		var titleWordEntered 	= parseInt($(showTitleWordCount).find("span[use=total_word_entered]").text());
		
		console.log("titleWordLimit="+titleWordLimit);
		console.log("titleWordEntered="+titleWordEntered);
		
		if(titleWordEntered == 0)
		{
			alert("Total Title Word Count Is Zero");
			return false;
		}
		else if(titleWordEntered > titleWordLimit)
		{	
			alert("Total Word Entered Is Greater Than Word Limit");
			return false;
		}
		
		
		var showContentWordCount 	= $(obj).find("span[use=abstract_total_word_display]");
		var contentWordLimit		= parseInt($(showContentWordCount).attr('limit'));
		var contentWordEntered 		= parseInt($(showContentWordCount).find("span[use=total_word_entered]").text());
				
		if(contentWordEntered==0)
		{
			alert("Total Title Word Count Is Zero");
			return false;
		}
		else if(contentWordEntered > contentWordLimit)
		{	
			alert("Total Word Entered Is Greater Than Word Limit");
			return false;
		}
						
		if( $(obj).find("input[type=submit][name=submit]").attr('isDisplay') == 'Y')
		{
			return true;	
		}
		else
		{
			return false;	
		}
		return false;
	}
	catch(e)
	{
	}
	return false;
}

/***********************************************************************************/
/*                                   UITLITY                                       */
/***********************************************************************************/
function wordLimitCounter(obj)
{
	var totalWordCount  = 0; 
	
	var parent 			= $(obj).parent().closest('table');
	var wordCount 		= parseInt($(obj).attr("wordcount"));	
	var group 			= $(obj).attr("spreadInGroup");
	var showWordCount 	= $(parent).find("span[use='"+$(obj).attr("displayText")+"']");
	var wordLimit		= parseInt($(showWordCount).attr('limit'));
	
	$(parent).find("textarea[spreadInGroup='"+group+"']").each(function(){												   
		if($(this).val()!="")
		{
			totalWordCount  = parseInt(totalWordCount) + parseInt(countWords($(this).val()));
		}
	});
	
	$(showWordCount).find("span[use=total_word_entered]").text("");
	$(showWordCount).find("span[use=total_word_entered]").text(totalWordCount);
	
	if(totalWordCount > wordLimit)
	{
		$(showWordCount).css("color","#D41000");
	}
	else
	{
		$(showWordCount).css("color","");
	}
}

function generateStateOptionList(obj, callback)
{
	var parent 		= $(obj).parent().closest("table");
	var countryId	= $(obj).val();
	console.log(jsBASE_URL+'abstract_request.process.php?act=getStateOptionList&countryId='+countryId);
	
	$.ajax({
		type: "POST",
		url: jsBASE_URL+'abstract_request.process.php',
		data: 'act=getStateOptionList&countryId='+countryId,
		dataType: 'html',
		async: false,
		success: function(returnMessage)
		{
			if(returnMessage!='')
			{
				$(parent).find("select[operationMode=stateControl]").html("");
				$(parent).find("select[operationMode=stateControl]").html(returnMessage);
			}
			
			try
			{
				callback();
			}
			catch(e){}
		}
	});
}

var SLICE_SIZE = 500;

function fileChangeHandler(targetObj,callbackFunction)
{
	var sliceSize = SLICE_SIZE;
	var target = targetObj;
	var parent = $(target).parent().closest("td");
	var fileName = $(target).val();
	if(fileName != ""){ 
		var uploader = {
						index			: "",
						reader 			: {},
						file 			: {},
						slice_size 		: (sliceSize * 1024),
						sessionId		: $(parent).find("input[type=hidden][name=sessionId]").val(),
						dynamicFileName : "",
						target			: {},
						targetParent	: {},
						garndParent		: {},
						filenameTranP	: {},
						filenameOrgnP	: {},
						uploadProgrOb	: {},
						uploadProgrIm	: {},
						size_done		: 0,
						percent_done	: 0,
						nounce			: ""
					};
		start_upload(targetObj, uploader, callbackFunction);
	}
}

function start_upload( target, uploader, callbackFunction )
{
	var d = new Date();				
	uploader.index = d.getTime();
	
	uploader.reader = new FileReader();				
	//file = document.querySelector( '#upload_case_file' ).files[0];
	uploader.file = $(target).prop('files')[0];
	
	var maxSize = $(target).attr("allowedSize");
	
	if((uploader.file.size/1024)/1024 > parseInt(maxSize))
	{
		alert("Upload file within "+maxSize+" MB");
		return false;
	}	
	
	var fileTypes = $(target).attr("allowedFileTypes");	
	var fArray = fileTypes.split(',');
	var typeMatch = false;
	for(var i = 0; i < fArray.length; i++)
	{
		if(uploader.file.name.endsWith($.trim(fArray[i])))
		{
			typeMatch = true;
			break;
		}
	}
	
	if(!typeMatch)
	{
		alert("Upload "+fileTypes+" files only");
		return false;
	}
	
	
	// createDynamicFileName
	uploader.dynamicFileName = uploader.sessionId+d.getTime()+'_'+uploader.file.name;	
	uploader.target			 = target;
	uploader.garndParent	 = $(target).parent().closest("div[operation=Entry]");
	uploader.targetParent	 = $(target).parent().closest("td");
	uploader.filenameTranP	 = $(uploader.targetParent).find("input[type=hidden][use=upload_temp_fileName]");	
	uploader.filenameOrgnP	 = $(uploader.targetParent).find("input[type=hidden][use=upload_original_fileName]");
	uploader.uploadProgrOb	 = $(uploader.targetParent).find("div[use=progressbar]");		
	uploader.targetForm	 	 = $(target).parent().closest("form");
	uploader.nounce			 = 'E'+uploader.sessionId+d.getTime()+'x';
					
	console.log('::'+uploader.index+':: start upload');
	
	$(uploader.target).prop("disabled",true);
	
	$(uploader.garndParent).find("input[type=submit][name=submit]").attr('isDisplay','N');
	$(uploader.garndParent).find("input[type=submit][name=submit]").hide();
	$(uploader.garndParent).find("span[use=preSubmitProcess]").show();
	
	$(uploader.uploadProgrOb).find('div[use=progress]').css('width','0%');
	$(uploader.uploadProgrOb).show();	
	
	upload_file( 0, uploader, callbackFunction);
}

function upload_file( start, uploader, callbackFunction) 
{
	var next_slice = start + uploader.slice_size + 1;
	
	var blob = uploader.file.slice( start, next_slice );
	
	console.log('::'+uploader.index+':: upload file with ==> '+start);
	
	uploader.reader.onloadend = function( event ) {											
		if ( event.target.readyState !== FileReader.DONE ) {
			return;
		}
		
		console.log('::'+uploader.index+':: upload file to ==> chunked-file-uploader.php');
		$.ajax({
			url: jsBASE_URL+'chunked-file-uploader.php',
			type: 'POST',
			dataType: 'text',
			cache: false,
			data: {						
				action: 'uploadCaseReportFile',
				file_data: event.target.result,
				file: uploader.dynamicFileName,
				file_type: uploader.file.type,
				nonce: uploader.nounce
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				console.log( jqXHR, textStatus, errorThrown );
			},
			success: function( data ) {
				console.log('::'+uploader.index+':: upload file response ==> '+data);
				uploader.size_done = start + uploader.slice_size;
				uploader.percent_done = Math.floor( ( uploader.size_done / uploader.file.size ) * 100 );
				
				if(uploader.percent_done > 100)
				{
					uploader.percent_done = 100;
				}
				
				console.log('::'+uploader.index+':: upload file progress ==> '+uploader.size_done+' bytes ['+uploader.percent_done+'%]');
				
				$(uploader.uploadProgrOb).find('div[use=progress]').css('width',uploader.percent_done+'%');
								
				if ( next_slice < uploader.file.size ) 
				{
					// Update upload progress
					//$( '#upload_case_file_msg' ).html( 'Uploading File - ' + percent_done + '%' );								
					console.log('::'+uploader.index+':: upload file continue ==> '+next_slice+' ===>'+uploader.percent_done+'% done');
					
					// More to upload, call function recursively
					upload_file( next_slice, uploader );								
				} 
				else 
				{
					// Update upload progress
					//$( '#upload_case_file_msg' ).html( 'Upload Complete!' );
					
					console.log('::'+uploader.index+':: upload file END');
					
					$(uploader.filenameTranP).val(uploader.dynamicFileName);
					$(uploader.filenameOrgnP).val(uploader.file.name);
					
					$(uploader.target).prop("disabled",false);
					$(uploader.garndParent).find("span[use=preSubmitProcess]").hide();
					$(uploader.garndParent).find("input[type=submit][name=submit]").attr('isDisplay','Y');
					$(uploader.garndParent).find("input[type=submit][name=submit]").show();
					
					alert("File upload complete. \nYou can submit now.");					
					
					try
					{
						callbackFunction();
					}
					catch(e)
					{
						console.log('::'+uploader.index+':: upload file ENDED callback fail ==>'+e.message);
					}
				}
			}
		} );
	};

	uploader.reader.readAsDataURL( blob );
}






////////////////////////////////////////////////////////////////////




















/***********************************************************************************/
/*                           ABSTRACT SUBMISSION WORD LIMIT                        */
/***********************************************************************************/
function abstractSubmissionWordLimit_x(abstractParentType)
{
	if(abstractParentType!="")
	{
		$.ajax({
					type: "POST",
					url: jsBASE_URL+'abstract_request.process.php',
					data: 'act=getAbstractWordLimit&abstractParentType='+abstractParentType,
					dataType: 'text',
					async: true,
					success: function(returnMessage)
					{
						var returnMessage = returnMessage.trim();
						
						$("#total_word_limit").text("");
						$("#total_word_limit").text(returnMessage);
						
						wordLimitDisplayStyle();
					}
			  });
	}
	else
	{
		$("#total_word_limit").text("");
		$("#total_word_limit").text(0);
		
		wordLimitDisplayStyle();
	}
}



function CasereportwordLimitCounter_x(obj)
{
	var totalWordCount  = 0; 
	
	var parent = $(obj).parent().closest("table");
	$(parent).find("textarea[operationMode=casereportWordCounter]").each(function(){
		if($(this).val()!="")
		{
			totalWordCount  = parseInt(totalWordCount) + parseInt(countWords($(this).val()));
		}
	});
	
	$(parent).find("#cr_total_word_entered").text("");
	$(parent).find("#cr_total_word_entered").text(totalWordCount);
	
	caserereport_wordLimitDisplayStyle(obj);
}

function abstractTitleWordLimitCounterSubmit_x(obj)
{
	var parent 			= $(obj).parent().closest("table[use=abstractSubmit]");
	var wordLimit 		= parseInt($(parent).find("#abstractTitle_total_word_limit").text());
	var totalWordCount  = 0;
	
	if($(parent).find("#abstract_title").length>0)
	{
		if($(parent).find("#abstract_title").val()=="")
		{
			totalWordCount  = 0;		
		}
		else
		{
			totalWordCount  = parseInt(countWords($(parent).find("#abstract_title").val()));
			
		}
	}
	else
	{
		if($(parent).find("textarea[name=abstract_edit_title]").val()=="")
		{
			totalWordCount  = 0;		
		}
		else
		{
			totalWordCount  = parseInt(countWords($(parent).find("textarea[name=abstract_edit_title]").val()));
		}
	}
	
	$(parent).find("#abstractTitle_total_word_entered").text("");
	$(parent).find("#abstractTitle_total_word_entered").text(totalWordCount+" words");
	$(parent).find("#title_word_counter").text("Available");
	
	if(totalWordCount>wordLimit)
	{
		//alert("if");
		$(parent).find("#abstractTitle_total_word_entered").text(totalWordCount+" words"+" - exceed max. limit");
		$(parent).find("#abstractTitle_total_word_entered").css("color","#D41000");
		$(parent).find("#abstract_title").focus();
		$(parent).find("#title_word_counter").text("exceed");
	}
	else if(totalWordCount == 0)
	{
		$(parent).find("#abstractTitle_total_word_entered").text("");
	}
	else
	{
		//alert("else");
		$(parent).find("#abstractTitle_total_word_entered").css("color","#009933");
	}
}

function abstractTitleWordLimitCounter_x(obj)
{
	var parent 			= $(obj).parent().closest("table[use=abstractEdit]");
	var wordLimit 		= parseInt($(parent).find("#abstractTitle_total_word_limit").text());
	var totalWordCount  = 0;
	
	if($(parent).find("#abstract_title").length>0)
	{
		if($(parent).find("#abstract_title").val()=="")
		{
			totalWordCount  = 0;		
		}
		else
		{
			totalWordCount  = parseInt(countWords($(parent).find("#abstract_title").val()));
		}
	}
	else
	{
		if($(parent).find("textarea[name=abstract_edit_title]").val()=="")
		{
			totalWordCount  = 0;		
		}
		else
		{
			totalWordCount  = parseInt(countWords($(parent).find("textarea[name=abstract_edit_title]").val()));
		}
	}
	
	$(parent).find("#abstractTitle_total_word_entered").text("");
	$(parent).find("#abstractTitle_total_word_entered").text(totalWordCount+" words");
	$(parent).find("#title_word_counter").text("Available");
	
	if(totalWordCount>wordLimit)
	{
		//alert("if");
		$(parent).find("#abstractTitle_total_word_entered").text(totalWordCount+" words"+" - exceed max. limit");
		$(parent).find("#abstractTitle_total_word_entered").css("color","#D41000");
		$(parent).find("#abstract_title").focus();
		$(parent).find("#title_word_counter").text("exceed");
	}
	else if(totalWordCount == 0)
	{
		$(parent).find("#abstractTitle_total_word_entered").text("");
	}
	else
	{
		//alert("else");
		$(parent).find("#abstractTitle_total_word_entered").css("color","#009933");
	}
}

function casereportTitleWordLimitCounterSubmit_x(obj)
{
	var parent 			= $(obj).parent().closest("table[use=caseReportSubmit]");
	var wordLimit 		= parseInt($(parent).find("#casereport_total_word_limit").text());
	
	var totalWordCount  = 0;
	
	if($(parent).find("#casereport_title").length>0)
	{
		if($(parent).find("#casereport_title").val()=="")
		{
			totalWordCount  = 0;		
		}
		else
		{
			totalWordCount  = parseInt(countWords($(parent).find("#casereport_title").val()));
		}
	}
	else
	{
		if($(parent).find("textarea[name=abstract_edit_title]").val()=="")
		{
			totalWordCount  = 0;		
		}
		else
		{
			totalWordCount  = parseInt(countWords($(parent).find("textarea[name=abstract_edit_title]").val()));
		}
	}
	
	$(parent).find("#casereport_total_word_entered").text("");
	$(parent).find("#casereport_total_word_entered").text(totalWordCount+" words");
	$(parent).find("#casereporttitle_word_counter").text("Available");
	
	if(totalWordCount>wordLimit)
	{		
		$(parent).find("#casereport_total_word_entered").text(totalWordCount+" words"+" - exceed max. limit");
		$(parent).find("#casereport_total_word_entered").css("color","#D41000");
		$(parent).find("#casereport_total_word_entered").show();
		$(parent).find("#casereport_title").focus();
		$(parent).find("#casereporttitle_word_counter").text("exceed");
		$(parent).find("#casereporttitle_word_counter").show();
	}
	else if(totalWordCount == 0)
	{
		$(parent).find("#casereport_total_word_entered").text("");
	}
	else
	{
		//alert("else");
		$(parent).find("#casereport_total_word_entered").css("color","#009933");
	}
	
}

function casereportTitleWordLimitCounter_x(obj)
{
	var parent 			= $(obj).parent().closest("table[use=caseEdit]");
	var wordLimit 		= parseInt($(parent).find("#casereport_total_word_limit").text());
	
	var totalWordCount  = 0;
	
	if($(parent).find("#casereport_title").length>0)
	{
		if($(parent).find("#casereport_title").val()=="")
		{
			totalWordCount  = 0;		
		}
		else
		{
			totalWordCount  = parseInt(countWords($(parent).find("#casereport_title").val()));
		}
	}
	else
	{
		if($(parent).find("textarea[name=abstract_edit_title]").val()=="")
		{
			totalWordCount  = 0;		
		}
		else
		{
			totalWordCount  = parseInt(countWords($(parent).find("textarea[name=abstract_edit_title]").val()));
		}
	}
	
	$(parent).find("#casereport_total_word_entered").text("");
	$(parent).find("#casereport_total_word_entered").text(totalWordCount+" words");
	$(parent).find("#casereporttitle_word_counter").text("Available");
	
	if(totalWordCount>wordLimit)
	{		
		$(parent).find("#casereport_total_word_entered").text(totalWordCount+" words"+" - exceed max. limit");
		$(parent).find("#casereport_total_word_entered").css("color","#D41000");
		$(parent).find("#casereport_total_word_entered").show();
		$(parent).find("#casereport_title").focus();
		$(parent).find("#casereporttitle_word_counter").text("exceed");
		$(parent).find("#casereporttitle_word_counter").show();
	}
	else if(totalWordCount == 0)
	{
		$(parent).find("#casereport_total_word_entered").text("");
	}
	else
	{
		//alert("else");
		$(parent).find("#casereport_total_word_entered").css("color","#009933");
	}
	
}
/***********************************************************************************/
/*                             WORD LIMIT DISPLAY STYLE                            */
/***********************************************************************************/
function wordLimitDisplayStyle_x(obj)
{
	var parent = $(obj).parent().closest("table");
	var wordLimit	= parseInt($(parent).find("#total_word_limit").text());
	var wordEntered = parseInt($(parent).find("#total_word_entered").text());
	
	if(wordEntered>wordLimit)
	{
		$(parent).find("#total_word_display").css("color","#D41000");
	}
	else
	{
		$(parent).find("#total_word_display").css("color","#34495E");
	}
}

function caserereport_wordLimitDisplayStyle_x(obj)
{
	var parent = $(obj).parent().closest("table");
	var wordLimit	= parseInt($(parent).find("#cr_total_word_limit").text());
	var wordEntered = parseInt($(parent).find("#cr_total_word_entered").text());
	
	if(wordEntered>wordLimit)
	{
		$(parent).find("#cr_total_word_display").css("color","#D41000");
	}
	else
	{
		$(parent).find("#cr_total_word_display").css("color","#34495E");
	}
}

/***********************************************************************************/
/*                          ABSTRACT REQUEST FROM VALIDATION                       */
/***********************************************************************************/


var CASE_REPORT_PROPER_SUBMIT = false;
var CASE_REPORT_PROPER_SUBMIT_EDIT = false;

function caseReportFormValidation_x(frmObj, proceed)
{
	if(proceed)
	{
		return CASE_REPORT_PROPER_SUBMIT;
	}
	return false;
}

function caseReportFormValidationOnClick_x(frmObj)
{
	try
	{
		if(fieldNotEmpty('#casereport_presenter_institute_name', "Please Enter Presenter Institute Name") == false){ return false; }
		if(fieldNotEmpty('#casereport_presenter_department', "Please Enter Presenter Department Name") == false){ return false; }		
		
		if(fieldNotEmpty('#casereport_author_name', "Please Enter Author Name") == false){ return false; }		
		if(fieldNotEmpty('#casereport_author_institute_name', "Please Enter Institute Name") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_department', "Please Enter Department Name") == false){ return false; }
		
		if(fieldNotEmpty('#casereport_author_country', "Please Select Author Country") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_state_id', "Please Select Author State") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_city', "Please Enter Author City") == false){ return false; }
		
		if(fieldNotEmpty('#casereport_author_phone_isd_code', "Please Enter isd code") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_phone_no', "Please Enter mobile number") == false){ return false; }
		
		var coAuthorSttatus = true;
		$.each($("#casereportRequestForm").find("table[use=coAuthorTableNew_CR]"),function(){
			var indxCoAuthor = $(this).attr('indx');
			if(!isNaN(indxCoAuthor))
			{
				var name = $.trim($('#abstract_coauthor_name_CR'+indxCoAuthor).val());
				if(name!='')
				{
					if(fieldNotEmpty('#abstract_coauthor_country_CR'+indxCoAuthor, "Please select country") == false){ coAuthorSttatus = false; return false; }
					if(fieldNotEmpty('#abstract_coauthor_state_CR'+indxCoAuthor, "Please select state") == false){ coAuthorSttatus = false; return false; }
					
				}
			}
		});
		if(coAuthorSttatus == false){ return false; }
		
		
		if(fieldNotEmpty('#casereport_topic_id', "Please Select casereport Topic") == false){ return false; }
		if(fieldNotEmpty('#casereport_study', "Please Select Study") == false){ return false; }
		if(fieldNotEmpty('#casereport_title', "Please Enter casereport Title") == false){ return false; }
		
		if(fieldNotEmpty('#casereport_background_aims', "Please Enter Aims & Objective") == false){ return false; }
		if(fieldNotEmpty('#casereport_material_methods', "Please Enter Methods") == false){ return false; }
		if(fieldNotEmpty('#casereport_results', "Please Enter Results") == false){ return false; }
		if(fieldNotEmpty('#casereport_conclusion', "Please Enter Conclusion") == false){ return false; }
		if(fieldNotEmpty('#casereport_description', "Please Enter Description") == false){ return false; }
		
		var wordLimit		= parseInt($("#cr_total_word_limit").text());
		var wordEntered 	= parseInt($("#cr_total_word_entered").text());
		
		if(wordLimit==0)
		{
			alert("Total Word Count Is Zero");
			return false;
		}
		else if(wordEntered>wordLimit)
		{
			$("#cr_total_word_display").css("color","#D41000");
			alert("Total Word Entered Is Greater Than Word Limit");
			return false;
		}
		
		if($('#title_word_counter').text() == "exceed"){
			$("#abstract_title").focus();
			cssAlert('#abstract_title','Exceed Max. words limit');
			return false;
		}	
		
		var goForFileUpload = false;
		var uploadFileCount = $("#addMoreCase_placeholder").find("div[use=uploadFileDiv]").length;
		if(uploadFileCount > 0)
		{
			if(uploadFileCount == 1)
			{
				var target = $("#addMoreCase_placeholder").find("div[use=uploadFileDiv]").find("input[type=file]");
				try
				{
					var file = $(target).prop('files')[0];
					var fileName = file.name;
					if(fileName != '')
					{
						goForFileUpload = true;
					}
				}catch(e){
				}
			}
			
			$.each($("#addMoreCase_placeholder").find("div[use=uploadFileDiv]"),function(){					
				var target = $(this).find("input[type=file]");
				try
				{
					var file = $(target).prop('files')[0];
					var fileName = file.name;
					if(fileName != ""){ 
						var FileExtension = fileName.split('.')[fileName.split('.').length - 1].toLowerCase();
						if (!(FileExtension === "pdf" || FileExtension === "jpg" || FileExtension === "jpeg" || FileExtension === "png"))
						{ 
							alert("This is Not A Valid File For Upload\n\nPlease Select .pdf,.jpg,.jpeg,.png File");
							goForFileUpload = false;
						}
						else
						{
							goForFileUpload = true;
						}
					}
				}catch(e){
				}
				
			 });
			
		}
				
		if(goForFileUpload)
		{				
			$.each($("#addMoreCase_placeholder").find("div[use=uploadFileDiv]"),function(){					
				var target = $(this).find("input[type=file]");
				var file = $(target).prop('files')[0];
				try
				{
					var fileName = file.name;
					var uploadeMessage = $("div[use=uploaderProgressMsgContainer]").find("div[use=uploaderProgressMsg]").clone();
					$(uploadeMessage).find("span[use=fileName]").html(fileName);
					$("#popup_details").append(uploadeMessage);
					
					var targetDetails = { obj     : target,
										  message : uploadeMessage
										};
					
					fileChangeHandler(targetDetails);
				}catch(e){
					//alert(e.message)
				}
			});	
			
			if($("#popup_details").find("div[use=uploaderProgressMsg]").length > 0)
			{
				$("#fade_popup").show();
				$("#popup_details").show();
			}
		}
		else
		{
				$("#casereportRequestForm").removeAttr("onsubmit");
				$("#casereportRequestForm").submit();
		}
		
		return true;
	} catch(e){
		alert(e.message)
	}
	
}

function abstractTitleWordCount_x()
{	
	var txt = $('#abstract_title').val();
	var txtWords = txt;	
	var wordCount = txtWords.replace( /[^\w ]/g, "" ).split( /\s+/ ).length;
	alert(wordCount);	
	if(wordCount > 50)
	{		
		cssAlert("#abstract_title","Title must be within 50 words.");
		return false;
	}
	return false;
}

function casereportTitleWordCount_x()
{	
	var txt = $('#casereport_title').val();
	var txtWords = txt;	
	var wordCount = txtWords.replace( /[^\w ]/g, "" ).split( /\s+/ ).length;
	alert(wordCount);	
	if(wordCount > 50)
	{		
		cssAlert("#casereport_title","Title must be within 50 words.");
		return false;
	}
	return false;
}

function abstractAlertMaker_x()
{
	$("input[operationMode=abstract_child_type]").click(function(){		
		if($("#abstract_child_type_oral").is(":checked"))
		{
			alert("Thanks. The preference will be taken care sincerely. The scientific committee has the right to set final allocation for presentation.");
		}
		else if($("#abstract_child_type_poster").is(":checked"))
		{
			alert("Thanks. The preference will be taken care sincerely. The scientific committee has the right to set final allocation for presentation.");
		}
		else
		{
		}
	});
}




function caseEditValidation_x(frmObj, proceed)
{
	if(proceed)
	{
		return CASE_REPORT_PROPER_SUBMIT_EDIT;
	}
	return false;
}

function caseReportFormValidationEditOnClick_x(frmObj)
{
	try
	{
		var indx = $(frmObj).attr('indx');	
		if(fieldNotEmpty('#casereport_edit_author_name'+indx, "Please enter name") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_edit_country'+indx, "Please select country") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_edit_state_id'+indx, "Please select state") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_edit_city'+indx, "Please enter city") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_edit_phone_isd_code'+indx, "Please enter isd code") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_edit_phone_no'+indx, "Please enter mobile number") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_edit_institute_name'+indx, "Please enter institute name") == false){ return false; }
		if(fieldNotEmpty('#casereport_author_edit_department'+indx, "Please enter department name") == false){ return false; }
		
		var coAuthorSttatus = true;
		
		$.each($(frmObj).find("table[use=coAuthorTable]"),function(){
			var indxCoAuthor = $(this).attr('indx');	
			if(fieldNotEmpty('#casereport_coauthor_edit_name'+indxCoAuthor, "Please enter name") == false){ coAuthorSttatus = false; return false; }
			if(fieldNotEmpty('#casereport_coauthor_edit_country'+indxCoAuthor, "Please select country") == false){ coAuthorSttatus = false; return false; }
			if(fieldNotEmpty('#casereport_coauthor_edit_state'+indxCoAuthor, "Please select country") == false){ coAuthorSttatus = false; return false; }
		});
		
		if(coAuthorSttatus == false){ return false; }
		
		
		if(fieldNotEmpty('#casereport_edit_topic_id'+indx, "Please select topic") == false){ return false; }
		if(fieldNotEmpty('#casereport_edit_study'+indx, "Please select study type") == false){ return false; }
		if(fieldNotEmpty('#casereport_edit_title'+indx, "Please enter title") == false){ return false; }
		
		if(fieldNotEmpty('#casereport_background_aims_data'+indx, "Please Enter Aims & Objective..") == false){ return false; }
		if(fieldNotEmpty('#casereport_material_methods_data'+indx, "Please Enter Methods") == false){ return false; }
		if(fieldNotEmpty('#casereport_results_data'+indx, "Please Enter Results") == false){ return false; }
		if(fieldNotEmpty('#casereport_conclusion_data'+indx, "Please Enter Conclusion") == false){ return false; }
		if(fieldNotEmpty('#casereport_description_data'+indx, "Please Enter Description") == false){ return false; }
		
		var casereportEditWordCount	=	$('#casereport_edit_title'+indx).val();
		var totalWordCount  = parseInt(countWords(casereportEditWordCount));
		var wordLimit 		= parseInt($("#casereport_total_word_limit").text());
		
		if(totalWordCount>wordLimit)
		{
			cssAlert('#casereport_edit_title'+indx,'Please keep Word Count within 30 words.');			
			return false;
		}
		
		var wordLimit 		= parseInt($(frmObj).find("#cr_total_word_limit").text());
		var wordEntered 	= parseInt($(frmObj).find("#cr_total_word_entered").text());
				
		if(wordLimit==0)
		{
			alert("Total Word Count Is Zero");
			return false;
		}
		else if(wordEntered>wordLimit)
		{	
		   // alert(wordEntered);
			$("#cr_total_word_display").css("color","#D41000");
			alert("Total Word Entered Is Greater Than Word Limit");
			return false;
		}
		if($('#casereportEditTitle_total_word_entered').text()=="EXCEED"){		
			cssAlert('#casereport_edit_title','Please keep Word Count within 30 words.');	
			
			return false;
		}
		wordRequired = 30;
		if(totalWordCount>wordRequired)
		{
			cssAlert('#abstract_edit_title'+indx,'Please keep Word Count within 30 words.');			
			return false;
		}
		var goForFileUpload = false;
		var uploadFileCount = $("#addMoreCase_placeholder").find("div[use=uploadFileDiv]").length;
		if(uploadFileCount > 0)
		{
			if(uploadFileCount == 1)
			{
				var target = $("#addMoreCase_placeholder").find("div[use=uploadFileDiv]").find("input[type=file]");
				try
				{
					var file = $(target).prop('files')[0];
					var fileName = file.name;
					if(fileName != '')
					{
						goForFileUpload = true;
					}
				}catch(e){
				}
			}
			
			$.each($("#addMoreCase_placeholder").find("div[use=uploadFileDiv]"),function(){					
				var target = $(this).find("input[type=file]");
				try
				{
					var file = $(target).prop('files')[0];
					var fileName = file.name;
					if(fileName != ""){ 
						var FileExtension = fileName.split('.')[fileName.split('.').length - 1].toLowerCase();
						if (!(FileExtension === "pdf" || FileExtension === "jpg" || FileExtension === "jpeg" || FileExtension === "png"))
						{ 
							alert("This is Not A Valid File For Upload\n\nPlease Select .pdf,.jpg,.jpeg,.png File");
							goForFileUpload = false;
						}
						else
						{
							goForFileUpload = true;
						}
					}
				}catch(e){
				}
				
			 });
			
		}
				
		if(goForFileUpload)
		{				
			$.each($("#addMoreCase_placeholder").find("div[use=uploadFileDiv]"),function(){					
				var target = $(this).find("input[type=file]");
				var file = $(target).prop('files')[0];
				try
				{
					var fileName = file.name;
					var uploadeMessage = $("div[use=uploaderProgressMsgContainer]").find("div[use=uploaderProgressMsg]").clone();
					$(uploadeMessage).find("span[use=fileName]").html(fileName);
					$("#popup_details").append(uploadeMessage);
					
					var targetDetails = { obj     : target,
										  message : uploadeMessage
										};
					
					fileChangeHandler(targetDetails);
				}catch(e){
					//alert(e.message)
				}
			});	
			
			if($("#popup_details").find("div[use=uploaderProgressMsg]").length > 0)
			{
				$("#fade_popup").show();
				$("#popup_details").show();
			}
		}
		else
		{
				$("#caseRequestEditForm").removeAttr("onsubmit");
				$("#caseRequestEditForm").submit();
		}
		return true;
	} catch(e){
		alert(e.message)
	}
}

function proceedFromOneDivToAnother_x()
{
	var checkValidation = $("input[type=checkbox][name=agree]").is(':checked');
	if(checkValidation == false)
	{
		alert('Please agree with the terms & condition.');
		$('input[type=checkbox][name=agree]').css('outline-color', '#D41000');
        $('input[type=checkbox][name=agree]').css('outline-style', 'solid');
        $('input[type=checkbox][name=agree]').css('outline-width', 'thin');
	}
	if($("input[type=checkbox][name=agree]").is(':checked'))
	{
		$("div[use=presenterNextDiv]").show();
		$("div[use=presenterPreviousDiv]").hide();
	}
}

function proceedFromOneDivToAnotherCase_x()
{
	var checkValidation = $("input[type=checkbox][name=agreeCase]").is(':checked');
	if(checkValidation == false)
	{
		alert('Please agree with the terms & condition.');
		$('input[type=checkbox][name=agreeCase]').css('outline-color', '#D41000');
        $('input[type=checkbox][name=agreeCase]').css('outline-style', 'solid');
        $('input[type=checkbox][name=agreeCase]').css('outline-width', 'thin');
	}
	if($("input[type=checkbox][name=agreeCase]").is(':checked'))
	{
		$("div[use=presenterNextDivCase]").show();
		$("div[use=presenterPreviousDivCase]").hide();
	}
}

function addMoreCaseTemplate_x(placeholderDiv, templateDiv, counterCaseFile=0)
{
	//alert(counterCaseFile);
	var totalUploadedCaseFiles = $('#totalUploadedFiles').text();
	if(totalUploadedCaseFiles == "")
	{
		totalUploadedCaseFiles = 0;
	}
	//alert(totalUploadedCaseFiles);
	var totalTables  = parseInt($('#'+placeholderDiv+' div[operationMode=abstract_case]').length);
	var nextCounter  = totalTables + 1;
	var getTemplate  = $("#"+templateDiv).clone();
	var modifiedTemp = getTemplate.html().replace(/\#COUNTER/g,nextCounter);;
	$('#'+placeholderDiv).append(modifiedTemp);
	//alert(nextCounter);
	var totalExistingCaseFile = $("#totalExistingCaseFile").text();
	//alert(totalExistingCaseFile);
	if(totalExistingCaseFile == '')
	{
		totalExistingCaseFile = 0;	
	}
	
	var limit = 20-parseInt(totalExistingCaseFile); //parseInt(counterCaseFile)
	
	if(nextCounter>= limit)
	{
		$('a[fileType=caseFile]').css("display","none");
	}	
	removeCase();
}

function removeCase_x()
{
	$("a[operationMode=removeCaseRow]").each(function(){
		$(this).click(function(){
			
			var sequenceBy = $(this).attr("sequenceBy");
			$("div[operationMode=abstract_case][sequenceBy="+sequenceBy+"]").remove();
			var totalTables  = parseInt($('#addMoreCase_placeholder div[operationMode=abstract_case]').length);	
			var nextCounter  = totalTables + 1;
			//$("a[fortype=removeFile][sequenceBy="+sequenceBy+"]").css("display","block");
			//alert(nextCounter)
			//var totalExistingCaseFile = $("#totalExistingCaseFile").text();
			//alert(totalExistingCaseFile+"--"+nextCounter);
			
			//var totalExistingCaseFile = $("#totalExistingCaseFile").text();
			//nextCounter 
			if(nextCounter<=20)
			{
				$('a[fileType=caseFile]').css("display","block");
			}		
		});
	});
}




