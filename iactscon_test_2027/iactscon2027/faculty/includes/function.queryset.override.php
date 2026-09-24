<?php

function webmaster_page_footer($scr='&nbsp;')
{
	global $cfg, $mycms;

	if($mycms->getPageName()!="login.php")
	{
?>
				<div id="menuRedirectionLoader" use="menuRedirectionLoader" >		
					<div>				
						<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/facebook.gif" /><br/>
						<span popupRelatedSubObj="processingArea">...LOADING...</span><br/>
						<span popupRelatedSubObj="networkSpeed"></span>
					</div>
				</div>
			</div>
			<div id="footer">
				<span style="float:left;">This system is running on ENWI architecture</span>
				<span style="float:right;"><?=$cfg['COPYRIGHT']?></span>
			</div>
			<style>
				.notify-defult { background-color: #dff0d8; color: #468847; border-color: #d6e9c6; }
			</style>
			<script>
			$(document).ready(function(){
				$("div[use=menuRedirectionLoader]").hide();
				$(".container").show();
			});
			</script>
<?php
	}
	$notificationClass = "";
	if($_REQUEST['m']!="")
	{
			if($_REQUEST['m']==1 || $_REQUEST['m']==2)
			{
				$notificationClass = "notify-success";
			}
			else if($_REQUEST['m']==3 || $_REQUEST['m']==0)
			{
				$notificationClass = "notify-error";
			}
			else if($_REQUEST['m']==5)
			{
				$notificationClass = "notify-info";
			}
			else
			{
				$notificationClass = "notify-defult";
			}
?>
			<div class="notify <?=$notificationClass?>" forType="messageDiv">
				<p><?=$mycms->getDisplayMessage()?></p>
			</div>
<?php
	}
?>
			<div id="onFormSubmitOvrelay" popupRelatedObj="formSubmit"></div>
			<div id="onFormSubmitLoader" popupRelatedObj="formSubmitDetails">
				<div>
					<img src="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/images/loaders/big-roller.gif" /><br/>
					<span popupRelatedSubObj="processingArea">...PROCESSING...</span><br>
					<span popupRelatedSubObj="networkSpeed"></span>
				</div>
			</div>
			<script type="text/javascript">
				function onSubmitAction(preAction)
				{
					try{
						var preAct = true;
						try{ preAct = preAction(); } catch(error){}
						if(preAct)
						{
							$("div[popupRelatedObj=formSubmit]").toggle();
							$("div[popupRelatedObj=formSubmitDetails]").toggle();
						}
						return preAct;
					} catch(error){console.log(error);}
				}
			</script>				
			<script type="text/javascript" src="<?=_BASE_URL_?>js/adminPanel/all.js"></script>
		</body>
	</html>
<?php
}

function abstractDetailsQuerySet_old($abstractId="", $searchCondition="", $limitCondition="")
{
	global $cfg, $mycms;
	
	$sqlBigJoin              = "SET OPTION SQL_BIG_SELECTS = 1";
	mysql_query($sqlBigJoin);
	
	$filterCondition         = "";
	
	if($abstractId != "")
	{
		$filterCondition    .= " AND abstractRequest.id =".$abstractId."";
	}
	
	
	$sqlAbstractDetails['QUERY']			   ="	    SELECT abstractRequest.*,
															   abstractTopic.abstract_topic,
															   
															   registeredDelegates.user_email_id,
															   registeredDelegates.user_mobile_no,
															   registeredDelegates.user_unique_sequence,
															   registeredDelegates.user_registration_id,
															   registeredDelegates.account_status,
															   registeredDelegates.registration_request,
															   
															   registeredDelegates.user_institute_name AS delegateInstitute,
															   registeredDelegates.user_department AS delegateDepertment,
															   registeredDelegates.user_designation AS delegateDesignation,
															   
															   IFNULL(registeredDelegates.user_title, '') AS user_title,
															   IFNULL(registeredDelegates.user_first_name, '') AS user_first_name,
															   IFNULL(registeredDelegates.user_middle_name, '') AS user_middle_name,
															   IFNULL(registeredDelegates.user_last_name, '') AS user_last_name,
															   
															   registeredDelegates.isRegistration,
															   registeredDelegates.isWorkshop,
															   
															   registeredDelegates.registration_payment_status,
															   registeredDelegates.workshop_payment_status,															   
															   registeredDelegates.user_city AS delegate_city_name,
															   
															   country.country_name AS author_country_name,
															   state.state_name AS author_state_name,
															   
															   IFNULL(abstractRequest.applicant_first_name, '') AS applicantFirstName,
															   IFNULL(abstractRequest.applicant_middle_name, '') AS applicantMiddleName,
															   IFNULL(abstractRequest.applicant_last_name, '') AS applicantLastName
															   
							
														  FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 
														  
											   LEFT OUTER JOIN "._DB_ABSTRACT_TOPIC_." abstractTopic 
															ON abstractRequest.abstract_topic_id = abstractTopic.id 
															
											   LEFT OUTER JOIN "._DB_COMN_COUNTRY_." country
															ON abstractRequest.abstract_author_country_id = country.country_id
														 
											   LEFT OUTER JOIN "._DB_COMN_STATE_." state
															ON abstractRequest.abstract_author_state_id = state.st_id
											   
											   LEFT OUTER JOIN "._DB_USER_REGISTRATION_." registeredDelegates 
															ON abstractRequest.applicant_id = registeredDelegates.id 
											   
											   
														  WHERE 1 AND registeredDelegates.status = 'A'  ".$filterCondition." ".$searchCondition." 
													
													  ORDER BY abstractRequest.id DESC ".$limitCondition."";
	
	
	
	//echo nl2br($sqlAbstractDetails);
	return $sqlAbstractDetails;
}	
?>