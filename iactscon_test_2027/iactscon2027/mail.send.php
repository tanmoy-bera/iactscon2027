<?php
		
include_once("includes/frontend.init.php");
include_once("includes/function.delegate.php"); 
include_once("includes/function.registration.php");
include_once("includes/function.workshop.php");
include_once("includes/function.dinner.php"); 

	echo 12; 
	// FETCHING DELEGATE
	$sqlFetchDelegate 	=	array();
	  $sqlFetchDelegate   	= "SELECT * FROM `isncon2023`  
	                            WHERE `status` = 'A' 
								  AND `isBlackListed` = 'N' 
								 
				        	 ORDER BY `id` DESC 
				           		LIMIT 20";
						
	$resultFetchDelegate 	= $mycms->sql_select($sqlFetchDelegate);

	print_r($resultFetchDelegate); die;
	if($resultFetchDelegate)
	{
	    $c=0;
		foreach($resultFetchDelegate as $keyDelegate=>$rowDelegate)
		{
			
			if($rowDelegate['delegate_email_id']!="")
			{				
				if($mycms->checkForBlackListed($rowDelegate['delegate_email_id']))
				{
				    $c++;
				   	//echo '-- TO : '.trim($rowDelegate['delegate_email_id']);	
				
					//$templateData =  newsletterTemplate_0026(trim($rowDelegate['delegate_email_id']));

				    //$templateMessage =  newsletterTemplate_2022(trim($rowDelegate['delegate_email_id']));

				    $templateMessage =  isncon2023_2022(trim($rowDelegate['delegate_email_id']));

				     // $templateMessage =  ispnconnew2023_2022(trim($rowDelegate['delegate_email_id']));

				    // $templateMessage =  isot2023_2022(trim($rowDelegate['delegate_email_id']));
				     
				  	/*echo $templateMessage; 
				    die();*/
				    
					
					// SENDING EMAIL
					$subjectTag     = $mycms->encoded($rowDelegate['id']);
					$subjectTag     = " [#".$subjectTag." ".date('d/m/Y')." ]";
					$toName         = trim($rowDelegate['delegate_email_id']);
					$toEmail        = trim($rowDelegate['delegate_email_id']);
					$fromName       = 'ISNCON 2023';
					$fromEmail      = 'isncon2023@gmail.com';
					$subject        = '𝐋𝐢𝐬𝐭𝐞𝐧 𝐭𝐨 𝐌𝐚𝐬𝐚𝐨𝐦𝐢 𝐍𝐚𝐧𝐠𝐚𝐤𝐮 𝐚𝐬 𝐡𝐞 𝐰𝐢𝐥𝐥 𝐬𝐡𝐚𝐫𝐞 𝐤𝐧𝐨𝐰𝐥𝐞𝐝𝐠𝐞 𝐨𝐧 𝐭𝐡𝐞 𝐭𝐨𝐩𝐢𝐜 𝐀𝐍𝐄𝐌𝐈𝐀 𝐈𝐍 𝐂𝐊𝐃 𝐚𝐭 𝐈𝐒𝐍𝐂𝐎𝐍 𝟐𝟎𝟐𝟑, 𝐊𝐨𝐥𝐤𝐚𝐭𝐚.';
					//$message        = $templateData['message'];

					$message        = $templateMessage;

					
					
					$attachments = array();
					
					$response =  $mycms->sendgridMailSendV3($toName, $toEmail, $fromName, $fromEmail, $subject, $message);
					
					$sendStatus = 'SENT';
					
					//$stat = $mycms->record_mailSent_status($cfg['SEND_MAIL_FOR'], $rowDelegate['id'], $rowDelegate['delegate_email_id'], $sendStatus, $templateData['template'], $templateData['subject'],$response);
				
				   echo '>>'.$response;
				    
				}
				else
				{
					$sqlUpdate      = "UPDATE `isncon2023` 
										  SET `isBlackListed` = 'Y'										  
										WHERE `id` = '".$rowDelegate['id']."'";				
					$mycms->sql_update($sqlUpdate, false);
				}
			}
			// UPDATING DELEGATE STATUS
			$sqlUpdate      = "UPDATE `isncon2023` 
								  SET `status` = 'D' 
								WHERE `id` = '".$rowDelegate['id']."'";				
			$mycms->sql_update($sqlUpdate, false);
		}
	}
	?>
	<script>
	    setTimeout(function(){ window.location.reload();},15000);
	</script>
	<?php
	
?>
