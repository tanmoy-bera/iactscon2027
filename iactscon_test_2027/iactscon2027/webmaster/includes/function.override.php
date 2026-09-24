<?php 
	/********************************************************************/
	/*                      DISPLAY RELATED METHOD                      */
	/********************************************************************/
	function adminDisplay(){
		
		global $cfg,$mycms;	
		webmaster_adminDisplay();
	}

	function notElligibleDisplay(){
		
		global $cfg,$mycms;	
		webmaster_notElligibleDisplay();		
	}
	
	function getCallRecordingTemplate()
	{
		global $cfg, $mycms;
	?>
	<script>
	function openCallDetailData(delegateId)
	{	
		$("div[use=callPopupForm]").find("input[type=text]").val('');
		$("div[use=callPopupForm]").find("input[type=date]").val('');
		$("div[use=callPopupForm]").find("select").val('');
		
		$("div[use=callPopupForm]").find("input[id=delegateId]").val(delegateId);
		
		$("div[use=callPopupForm]").fadeIn("slow");
	}
	function openCallDetailDataPraticipant(participantId)
	{	
		$("div[use=callPopupForm]").find("input[type=text]").val('');
		$("div[use=callPopupForm]").find("input[type=date]").val('');
		$("div[use=callPopupForm]").find("select").val('');
		
		$("div[use=callPopupForm]").find("input[id=participantId]").val(participantId);
		
		$("div[use=callPopupForm]").fadeIn("slow");
	}
	function submitCallDetails()
	{
		$("div[use=callPopupForm]").fadeOut("slow");
		return true;
	}
	</script>
	<div class="popup_form2" id="popup_form" use="callPopupForm">
		<form action="<?=_BASE_URL_?>webmaster/section_registration/card_distribution.process.php" name="nameUpdatePopup" id="nameUpdatePopup" method="post" onsubmit="return submitCallDetails();" target="_blank">
			<input type="hidden" name="act" value="addCallDetails" />	
			<input type="hidden" name="delegateId" id="delegateId">
			<input type="hidden" name="participantId" id="participantId">
			<table width="100%" class="tborder">
				<tr>
					<td colspan="2" class="tcat">
						<span style="float:left" >Call Records Entry Screen</span>
						<span class="close" forType="tsearchTool" onclick="$(this).parent().closest('div[use=callPopupForm]').fadeOut('slow');">X</span>
					</td>
				</tr>
				<tr>
					<td align="left" width="140"><b>Call DateTime</b></td>
					<td valign="top">
						<input type="date" name="callDate" id="callDate" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" value="<?=date('Y-m-d')?>"  style="width:35%" placeholder="Choose Date" required />&nbsp; -&nbsp; 
						<span rel="timeChooser">
							<input type="number" name="callTimeHour" id="callTimeHour" min="0" max="23" specific="hour" value="" style="width:15%;  text-align:center;" placeholder="HH" required/> : 
							 
							<input type="number" name="callTimeMin" id="callTimeMin" min="0" max="59" specific="min" value="" style="width:15%;   text-align:center" placeholder="MM" required/>
						</span>
					</td>
				</tr>
				<tr>
					<td align="left" width="140"><b>Subject</b></td>	
					<td align="left" width="140">
						<select name="call_subject" id="call_subject" required>
							<option value="">-- Select --</option>
							<option value="REGISTRATION">Registration</option>
							<option value="WORKSHOP">Workshop</option>
							<option value="ACCOMPANY">Accompany</option>
							<option value="ACCOMMODATION">Accommodation</option>
							<option value="PICKUP-DROP">Pick Up / Drop Off</option>
							<option value="DINNER">Dinner</option>
							<option value="ABSTRACT">Abstract</option>
							<option value="SCIENTIFIC-PROGRAM">Scientific Program</option>
							<option value="OTHERS">Others</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align="left" width="140"><b>Discussion</b></td>	
					<td width="22%" align="left" valign="top"><textarea name="call_contents" id="call_contents" style="width:440px; height:100px; text-transform:uppercase;" required /></textarea>
					<br>
						<input type="submit" name="submitData" id="submitData" class="btn btn-small btn-blue" align="right" style="margin-left:180px;">
					</td>
				</tr>
				<tr>
					<td colspan="2"></td>
				</tr>
			</table>		
		</form>
	</div>
	<?
	}
?>