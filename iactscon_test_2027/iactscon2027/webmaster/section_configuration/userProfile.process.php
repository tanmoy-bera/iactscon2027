<?php 
	include_once('includes/init.php');
	include_once('../../includes/function.userProfile.php');
	
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action){
		
		/*******************************************************************************/
		/*                              UPDATE PHYSICAL DETAILS                        */
		/*******************************************************************************/
		case'updatePhysicalDetails':
			
			/***************************************************************************/
			/* Name:       updatePhysicalDetailsProcess                                */
			/* Location:   root/includes/function.userProfile.php                      */
			/* Parameters:                                                             */
			/***************************************************************************/
			updatePhysicalDetailsProcess();			  
			$mycms->redirect("userProfile.php");
			exit();
			break;
			
		/*******************************************************************************/
		/*                               UPDATE OTHER DETAILS                          */
		/*******************************************************************************/
		case'updateOtherDetails':
			
			/***************************************************************************/
			/* Name:       updateOtherDetailsProcess                                   */
			/* Location:   root/includes/function.userProfile.php                      */
			/* Parameters:                                                             */
			/***************************************************************************/
			updateOtherDetailsProcess();		  
			$mycms->redirect("userProfile.php");
			exit();
			break;
			
		/*******************************************************************************/
		/*                              UPDATE PERSONAL DETAILS                        */
		/*******************************************************************************/
		case'updatePersonalDetails':
			
			/***************************************************************************/
			/* Name:       updatePersonalDetailsProcess                                */
			/* Location:   root/includes/function.userProfile.php                      */
			/* Parameters:                                                             */
			/***************************************************************************/
			updatePersonalDetailsProcess();
			$mycms->redirect("userProfile.php");
			exit();
			break;
			
		/*******************************************************************************/
		/*                               UPDATE CONATCT DETAILS                        */
		/*******************************************************************************/
		case'updateContactDetails':
			
			/***************************************************************************/
			/* Name:       updateContactDetailsProcess                                 */
			/* Location:   root/includes/function.userProfile.php                      */
			/* Parameters:                                                             */
			/***************************************************************************/
			updateContactDetailsProcess();
			$mycms->redirect("userProfile.php");
			exit();
			break;
		
		/*******************************************************************************/
		/*                                 CHANGE USER IMAGE                           */
		/*******************************************************************************/
		case'changeUserImage':
			
			/***************************************************************************/
			/* Name:       updateUserImageProcess                                      */
			/* Location:   root/includes/function.userProfile.php                      */
			/* Parameters:                                                             */
			/***************************************************************************/
			updateUserImageProcess();
			$mycms->redirect("userProfile.php");	
			exit();
			break;
	}
?>