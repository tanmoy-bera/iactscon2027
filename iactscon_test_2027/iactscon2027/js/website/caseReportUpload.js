function fileChangeHandler(targetObj,callbackFunction)
{
	var sliceSize = SLICE_SIZE;
	var target = targetObj.obj;
	var fileName = $(target).val();
	if(fileName != ""){ 
		var uploader = {index			: "",
							reader 			: {},
							file 			: {},
							slice_size 		: (sliceSize * 1024),
							delegateId		: $("input[type=hidden][name=applicantId]").val(),
							dynamicFileName : "",
							target			: {},
							targetParent	: {},
							filenameTranP	: {},
							uploadProgrOb	: {},
							size_done		: 0,
							percent_done	: 0,
							nounce			: ""};
		start_upload(targetObj, uploader, callbackFunction);
	}
}

function start_upload( target, uploader, callbackFunction ) {
		//event.preventDefault();
		
		// event.target.id
		//var target = event.target;
		var d = new Date();
		
		uploader.index = d.getTime();
		
		uploader.reader = new FileReader();
		
		//file = document.querySelector( '#upload_case_file' ).files[0];
		uploader.file = $(target.obj).prop('files')[0];
		
		// createDynamicFileName
		uploader.delegateId 	 = $("input[type=hidden][name=applicantId]").val();
		uploader.dynamicFileName = uploader.delegateId+d.getTime()+'_'+uploader.file.name;	
		uploader.target			 = target.obj;
		uploader.targetParent	 = $(target.obj).parent().closest("div[use=uploadFileDiv]");
		uploader.filenameTranP	 = $(uploader.targetParent).find("input[type=hidden][use=caseReportFileName]");	
		uploader.uploadProgrOb	 = $(target.message).find("span[use=upload_case_file_msg]");
		uploader.uploadProgrM	 = $(uploader.uploadProgrOb).find("span[use=upload_case_file_msg_progress]");
		uploader.targetForm	 	 = $(target.obj).parent().closest("form");
		uploader.nounce			 = 'E'+uploader.delegateId+d.getTime()+'x';
		
		
		console.log('::'+uploader.index+':: start upload');
		
		$(uploader.uploadProgrOb).show();
		upload_file( 0, uploader, callbackFunction);
	}


function upload_file( start, uploader, callbackFunction) {
	
	var next_slice = start + uploader.slice_size + 1;
	
	var blob = uploader.file.slice( start, next_slice );
	
	console.log('::'+uploader.index+':: upload file with ==> '+start);
	
	uploader.reader.onloadend = function( event ) {
		if ( event.target.readyState !== FileReader.DONE ) {
			return;
		}
		
		console.log('::'+uploader.index+':: upload file to ==> '+jBaseUrl+'dbi-file-uploader.php');
		$.ajax({
			url: jBaseUrl+'dbi-file-uploader.php',
			type: 'POST',
			dataType: 'json',
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
				
				console.log('::'+uploader.index+':: upload file progress ==> '+uploader.size_done+' bytes ['+uploader.percent_done+'%]');
				
				$(uploader.uploadProgrM).html(uploader.percent_done);
				
				if ( next_slice < uploader.file.size ) 
				{
					// Update upload progress
					//$( '#upload_case_file_msg' ).html( 'Uploading File - ' + percent_done + '%' );
					
					console.log('::'+uploader.index+':: upload file continue ==> '+next_slice);
					
					// More to upload, call function recursively
					upload_file( next_slice, uploader );
					
				} 
				else 
				{
					// Update upload progress
					//$( '#upload_case_file_msg' ).html( 'Upload Complete!' );
					
					console.log('::'+uploader.index+':: upload file END');
					
					$(uploader.filenameTranP).val(uploader.dynamicFileName);
					
					$(uploader.uploadProgrOb.find("img")).hide();
					
					try
					{
						console.log('::'+uploader.index+':: upload file ENDED Call callback');
						
						var status = true;
						$.each($("#popup_details").find("div[use=uploaderProgressMsg]"),function(){
							var completion = $(this).find("span[use=upload_case_file_msg_progress]").html();
							if(parseInt(completion) < 100)
							{
								status = false;
								return false;
							}
						});
						console.log(':: callback called status = '+status);
						if(status)
						{
							alert("now ready to submit")
							$("#casereportRequestForm").removeAttr("onsubmit");
							$("#casereportRequestForm").submit();
						}
					}
					catch(e)
					{
						console.log('::'+uploader.index+':: upload file ENDED callback fail ==>'+e.message);
					}
					
					// set the newFileName
					//var parent = $(file.targetOnScreen ).parent().closest("span[use=uploadFileSpan]");
					//$(parent).find("input[type=hidden][use=caseReportFileName]").val(file.name);
				}
			}
		} );
	};

	uploader.reader.readAsDataURL( blob );
}