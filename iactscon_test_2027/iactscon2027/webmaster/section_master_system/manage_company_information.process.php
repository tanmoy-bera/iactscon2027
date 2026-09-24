<?php
session_start();
?>
<?php
include_once('includes/init.php');

$act = @$_REQUEST['act'];
switch ($act) {

		/*================ COUNTRY AVAILABILITY ====================*/
	case 'countryAvailability':

		$getVal = addslashes($_REQUEST['getVal']);
		$sql 	=	array();
		$sql['QUERY']    = "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . " 
									    WHERE `country_name`=? 
									      AND `status`!= ?";
		$sql['PARAM'][]	=	array('FILD' => 'country_name',    	 'DATA' => $getVal,            'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'status',    			'DATA' => 'D',         		'TYP' => 's');
		$res    		 = $mycms->sql_select($sql);
		$maxrow 		 = $mycms->sql_numrows($res);

		if ($maxrow > 0) {
			echo 1;
		} else {
			echo 0;
		}
		exit();
		break;

		/*==================== SEARCH COUNTRY =======================*/
	case 'search_country':
		$mycms->redirect('company_info.php');
		//pageRedirection('company_info.php', 5);
		exit();
		break;

		/*================= ACTIVE COUNTRY ==================*/
	case 'Active':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'A',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("company_info.php", 2);
		exit();
		break;

		/*================ INACTIVE COUNTRY =================*/
	case 'Inactive':

		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'I',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("company_info.php", 2);
		exit();
		break;

		/*================= REMOVE COUNTRY ==================*/
	case 'Remove':
		$sql 	=	array();
		$sql['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
							   SET `status` = ? 
							 WHERE `id` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'status',    	 'DATA' => 'D',           					 'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'id',    	 'DATA' => $_REQUEST['id'],            'TYP' => 's');
		$mycms->sql_update($sql);
		pageRedirection("company_info.php", 3);
		exit();
		break;

	case 'insertDate':
		insertDate($mycms, $cfg);
		pageRedirection('manage_cutoff.php', 2, "");
		break;

	case 'ActiveDate':
		ActiveDate($mycms, $cfg);
		pageRedirection('manage_cutoff.php', 2, "");
		break;
		/************************ACTIVE**********************/
	case 'InactiveDate':
		InactiveDate($mycms, $cfg);
		pageRedirection('manage_cutoff.php', 2, "");
		break;
	case 'deleteDate':
		deleteDate($mycms, $cfg);
		pageRedirection('manage_cutoff.php', 2, "");
		break;

		/*================= ADD COUNTRY ============================*/
	case 'add_template':

		global $mycms, $cfg;


		$sql 	=	array();

		$sql['QUERY'] = "INSERT INTO " . _DB_COMPANY_INFORMATION_ . " 
									SET 
									`company_conf_name`=?,
									`company_conf_email`=?,
									`company_conf_mobileno`=?,
									`company_conf_venue`=?,
									`conf_start_date`=?,
									`conf_end_date`=?,
									`cgst_percentage`=?,
									`sgst_percentage`=?,
									`igst_percentage`=?,
									`internet_handling_percentage`=?,
									`gst_number`=?,
									`pan_number`=?,
									`invoive_number_format`=?,
									`invoice_company_name_prefix`=?,
									`invoice_company_name`=?,
									`invoice_address`=?,
									`invoice_phone_number`=?,
									`invoice_email_address`=?,
									`invoice_website_name`=?,
									`invoice_bank_name`=?,
									`invoice_bank_account_number`=?,
									`invoice_beneficiary`=?,
									`invoice_bank_branch_name`=?,
									`invoice_bank_ifsc_code`=?,
									`created_dateTime`=?

									";

		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_name',    	 'DATA' => $_REQUEST['company_conf_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_email',    	 'DATA' => $_REQUEST['company_conf_email'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_mobileno',    	 'DATA' => $_REQUEST['company_conf_mobileno'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_venue',    	 'DATA' => $_REQUEST['company_conf_venue'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'conf_start_date',    	 'DATA' => $_REQUEST['conf_start_date'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'conf_end_date',    	 'DATA' => $_REQUEST['conf_end_date'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'cgst_percentage',    	 'DATA' => $_REQUEST['cgst_percentage'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'sgst_percentage',    	 'DATA' => $_REQUEST['sgst_percentage'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'igst_percentage',    	 'DATA' => $_REQUEST['igst_percentage'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'internet_handling_percentage',    	 'DATA' => $_REQUEST['internet_handling_percentage'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'gst_number',    	 'DATA' => $_REQUEST['gst_number'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'pan_number',    	 'DATA' => $_REQUEST['pan_number'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'invoive_number_format',    	 'DATA' => $_REQUEST['invoive_number_format'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_company_name_prefix',    	 'DATA' => $_REQUEST['invoice_company_name_prefix'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_company_name',    	 'DATA' => $_REQUEST['invoice_company_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_address',    	 'DATA' => $_REQUEST['invoice_address'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'invoice_phone_number',    	 'DATA' => $_REQUEST['invoice_phone_number'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_email_address',    	 'DATA' => $_REQUEST['invoice_email_address'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_website_name',    	 'DATA' => $_REQUEST['invoice_website_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_bank_name',    	 'DATA' => $_REQUEST['invoice_bank_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_bank_account_number',    	 'DATA' => $_REQUEST['invoice_bank_account_number'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_beneficiary',    	 'DATA' => $_REQUEST['invoice_beneficiary'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'invoice_bank_branch_name',    	 'DATA' => $_REQUEST['invoice_bank_branch_name'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'invoice_bank_ifsc_code',    	 'DATA' => $_REQUEST['invoice_bank_ifsc_code'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'created_dateTime',    	 'DATA' => date('Y-m-d H:i:s'), 		 'TYP' => 's');

		$lastInsertId = $mycms->sql_insert($sql);


$mycms->redirect('company_info.php');
		//pageRedirection('company_info.php', 1);
		exit();
		break;

		/*================= EDIT COUNTRY ===========================*/
	case 'edit_template':

		$id = $_REQUEST['id'];

		global $mycms, $cfg;


		if ($_REQUEST['guideline_pdf_flag'] == '0') {
			// print_r($id);die;
			removeGuideLinePdf($id);
			$abstract_guideline_pdf = "";
		} else {
			$abstract_guideline_pdf = $_REQUEST['abstract_guideline_pdf'];
		}

		//echo '<pre>'; print_r($_REQUEST); 
		//echo ltrim($_REQUEST['color'], '#');
		//die;
		$sql 	=	array();

		$sql['QUERY'] = "UPDATE " . _DB_COMPANY_INFORMATION_ . " 
									SET 
									`company_conf_name`=?,
									`company_conf_full_name`=?,
									`company_conf_email`=?,
									`company_conf_mobileno`=?,
									`scientific_sender_email`=?,
									`company_conf_venue`=?,
									`conf_start_date`=?,
									`conf_end_date`=?,
									`cgst_percentage`=?,
									`sgst_percentage`=?,
									`igst_percentage`=?,
									`igst_flag`=?,
									`internet_handling_percentage`=?,
									`gst_number`=?,
									`pan_number`=?,
									`invoive_number_format`=?,
									`invoice_company_name_prefix`=?,
									`invoice_company_name`=?,
									`invoice_address`=?,
									`invoice_state_name`=?,
									`invoice_phone_number`=?,
									`invoice_email_address`=?,
									`cart_helpline`=?,
									`invoice_website_name`=?,
									`invoice_bank_name`=?,
									`invoice_bank_account_number`=?,
									`invoice_beneficiary`=?,
									`invoice_bank_branch_name`=?,
									`invoice_bank_ifsc_code`=?,
									`invoice_conf_hsn`=?,
									`invoice_exb_hsn`=?,
									`invoice_footer_text`=?,
									`abstract_submission_date`=?,
									`abstract_confirmation_date`=?,
									`abstract_title_word_limit`=?,
									`abstract_total_word_limit`=?,
									`abstract_submission_type`=?,
									`abstract_presentation_type`=?,
									`abstract_word_title_type`=?,
									`abstract_total_word_type`=?,
									`abstract_field_type`=?,
									`abstract_sender_email`=?,
									`abstract_guideline_pdf`=?,
									`guideline_pdf_flag`=?,

									`gst_flag`=?,
									`offline_payment_method`=?,
									`available_registration_fields`=?,
									`hod_consent_file_types`=?,
									`abstract_file_types`=?,
									`color`=?,
									`dark_color`=?,
									`light_color`=?,
									`cheque_info`=?,
									`draft_info`=?,
									`neft_info`=?,
									`rtgs_info`=?,
									`cash_info`=?,
									`payment_declaration`=?,
									`terms_info`=?,
									`privacy_info`=?,
									`terms_page_info`=?,
									`privacy_page_info`=?,
									`cancellation_page_info`=?,
									`payment_failure_info`=?,
									`abstract_submission_success_info`=?,
									`success_page_info`=?,
									`notification_invalid_email`=?,
									`notification_registered_email`=?,
									`notification_empty_email`=?,
									`notification_unpaid_offline`=?,
									`notification_unpaid_online`=?,
									`process_page_info`=?,
									`online_payment_success_page_info`=?,
									`tariff_category_title`=?,
									`tariff_user_details_title`=?,
									`tariff_workshop_title`=?,
									`tariff_accompany_title`=?,
									`tariff_banquet_title`=?,
									`tariff_accommodation_title`=?,
									`tariff_cart_title`=?,
									`tariff_login_title`=?,
									`conference_site_url`=?,
									`conference_site_url_link`=?
									
									WHERE `id` = ?
									";

		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_name',    	 'DATA' => $_REQUEST['company_conf_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_full_name',    	 'DATA' => $_REQUEST['company_conf_full_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_email',    	 'DATA' => $_REQUEST['company_conf_email'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_mobileno',    	 'DATA' => $_REQUEST['company_conf_mobileno'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'scientific_sender_email',    	 'DATA' => $_REQUEST['scientific_sender_email'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'company_conf_venue',    	 'DATA' => $_REQUEST['company_conf_venue'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'conf_start_date',    	 'DATA' => $_REQUEST['conf_start_date'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'conf_end_date',    	 'DATA' => $_REQUEST['conf_end_date'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'cgst_percentage',    	 'DATA' => $_REQUEST['cgst_percentage'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'sgst_percentage',    	 'DATA' => $_REQUEST['cgst_percentage'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'igst_percentage',    	 'DATA' => $_REQUEST['igst_percentage'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'igst_flag',    	 'DATA' => $_REQUEST['igst_flag'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'internet_handling_percentage',    	 'DATA' => $_REQUEST['internet_handling_percentage'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'gst_number',    	 'DATA' => $_REQUEST['gst_number'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'pan_number',    	 'DATA' => $_REQUEST['pan_number'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'invoive_number_format',    	 'DATA' => $_REQUEST['invoive_number_format'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_company_name_prefix',    	 'DATA' => $_REQUEST['invoice_company_name_prefix'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_company_name',    	 'DATA' => $_REQUEST['invoice_company_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_address',    	 'DATA' => $_REQUEST['invoice_address'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_state_name',    	 'DATA' => $_REQUEST['invoice_state_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_phone_number',    	 'DATA' => $_REQUEST['invoice_phone_number'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_email_address',    	 'DATA' => $_REQUEST['invoice_email_address'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'cart_helpline',    	 'DATA' => $_REQUEST['cart_helpline'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_website_name',    	 'DATA' => $_REQUEST['invoice_website_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_bank_name',    	 'DATA' => $_REQUEST['invoice_bank_name'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_bank_account_number',    	 'DATA' => $_REQUEST['invoice_bank_account_number'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_beneficiary',    	 'DATA' => $_REQUEST['invoice_beneficiary'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'invoice_bank_branch_name',    	 'DATA' => $_REQUEST['invoice_bank_branch_name'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'invoice_bank_ifsc_code',    	 'DATA' => $_REQUEST['invoice_bank_ifsc_code'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_conf_hsn',    	 'DATA' => $_REQUEST['invoice_conf_hsn'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_exb_hsn',    	 'DATA' => $_REQUEST['invoice_exb_hsn'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'invoice_footer_text',    	 'DATA' => $_REQUEST['invoice_footer_text'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_submission_date',    	 'DATA' => $_REQUEST['abstract_submission_date'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_confirmation_date',    	 'DATA' => $_REQUEST['abstract_confirmation_date'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_title_word_limit',    	 'DATA' => $_REQUEST['abstract_title_word_limit'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_total_word_limit',    	 'DATA' => $_REQUEST['abstract_total_word_limit'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_submission_type',    	 'DATA' => json_encode($_REQUEST['submission_type']), 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_presentation_type',    	 'DATA' => json_encode($_REQUEST['presentation_type']), 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'abstract_word_title_type',    	 'DATA' => $_REQUEST['abstract_word_title_type'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'abstract_total_word_type',    	 'DATA' => $_REQUEST['abstract_total_word_type'], 		 'TYP' => 's');
		
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_field_type',    	 'DATA' => json_encode($_REQUEST['abstract_field_type']), 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'abstract_sender_email',    	 'DATA' => $_REQUEST['abstract_sender_email'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_guideline_pdf',    	 'DATA' => $abstract_guideline_pdf, 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'guideline_pdf_flag',    	 'DATA' => $_REQUEST['guideline_pdf_flag'], 		 'TYP' => 's');


		$sql['PARAM'][]		  =	array('FILD' => 'gst_flag',    	 'DATA' => $_REQUEST['gst_flag'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'offline_payment_method',    	 'DATA' => json_encode($_REQUEST['payments']), 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'available_registration_fields',    	 'DATA' => json_encode($_REQUEST['fields']), 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'hod_consent_file_types',    	 'DATA' => json_encode($_REQUEST['consent_files']), 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'abstract_file_types',    	 'DATA' => json_encode($_REQUEST['abstract_files']), 		 'TYP' => 's');


		$sql['PARAM'][]		  =	array('FILD' => 'color',    	 'DATA' => ltrim($_REQUEST['color'], '#'), 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'dark_color',    	 'DATA' => ltrim($_REQUEST['dark_color'], '#'), 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'light_color',    	 'DATA' => ltrim($_REQUEST['light_color'], '#'), 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'cheque_info',    	 'DATA' => $_REQUEST['cheque_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'draft_info',    	 'DATA' => $_REQUEST['draft_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'neft_info',    	 'DATA' => $_REQUEST['neft_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'rtgs_info',    	 'DATA' => $_REQUEST['rtgs_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'cash_info',    	 'DATA' => $_REQUEST['cash_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'payment_declaration',    	 'DATA' => $_REQUEST['payment_declaration'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'terms_info',    	 'DATA' => $_REQUEST['terms_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'privacy_info',    	 'DATA' => $_REQUEST['privacy_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'terms_page_info',    	 'DATA' => $_REQUEST['terms_page_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'privacy_page_info',    	 'DATA' => $_REQUEST['privacy_page_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'cancellation_page_info',    	 'DATA' => $_REQUEST['cancellation_page_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'payment_failure_info',    	 'DATA' => $_REQUEST['payment_failure_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'abstract_submission_success_info',    	 'DATA' => $_REQUEST['abstract_submission_success_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'success_page_info',    	 'DATA' => $_REQUEST['success_page_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'notification_invalid_email',    	 'DATA' => $_REQUEST['notification_invalid_email'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'notification_registered_email',    	 'DATA' => $_REQUEST['notification_registered_email'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'notification_empty_email',    	 'DATA' => $_REQUEST['notification_empty_email'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'notification_unpaid_offline',    	 'DATA' => $_REQUEST['notification_unpaid_offline'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'notification_unpaid_online',    	 'DATA' => $_REQUEST['notification_unpaid_online'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'process_page_info',    	 'DATA' => $_REQUEST['process_page_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'online_payment_success_page_info',    	 'DATA' => $_REQUEST['online_payment_success_page_info'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'tariff_category_title',    	 'DATA' => $_REQUEST['tariff_category_title'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'tariff_user_details_title',    'DATA' => $_REQUEST['tariff_user_details_title'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'tariff_workshop_title',    	 'DATA' => $_REQUEST['tariff_workshop_title'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'tariff_accompany_title',    	 'DATA' => $_REQUEST['tariff_accompany_title'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'tariff_banquet_title',    	 'DATA' => $_REQUEST['tariff_banquet_title'], 		 'TYP' => 's');
		$sql['PARAM'][]		  =	array('FILD' => 'tariff_accommodation_title',    	 'DATA' => $_REQUEST['tariff_accommodation_title'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'tariff_cart_title',    	 'DATA' => $_REQUEST['tariff_cart_title'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'tariff_login_title',    	 'DATA' => $_REQUEST['tariff_login_title'], 		 'TYP' => 's');


		$sql['PARAM'][]		  =	array('FILD' => 'conference_site_url',    	 'DATA' => $_REQUEST['conference_site_url'], 		 'TYP' => 's');

		$sql['PARAM'][]		  =	array('FILD' => 'conference_site_url_link',    	 'DATA' => $_REQUEST['conference_site_url_link'], 		 'TYP' => 's');


		$sql['PARAM'][]		  =	array('FILD' => 'id',    	 'DATA' => $id, 		 'TYP' => 's');

		$mycms->sql_update($sql);



		if ($_FILES['abstract_guideline_pdf_file']['name'] && $_REQUEST['guideline_pdf_flag'] != '0') {

			guidelineImageUpload($id, $_FILES['abstract_guideline_pdf_file']);
		}


$mycms->redirect('company_info.php');
$_SESSION['success_message'] = "Company Info Updated Successfully";
		//pageRedirection('company_info.php', 2);
		exit();
		break;
}

function guidelineImageUpload($participantId, $header_Image)
{
	global $mycms, $cfg;
	$userImage 			= str_replace(" ", "", $header_Image['name']);
	$userImageTempFile 	= $header_Image['tmp_name'];
	if ($userImageTempFile != "") {
		$ids 							= str_pad($participantId, 4, '0', STR_PAD_LEFT);
		$rand							= 'GUIDELINE_' . $ids . '_' . date('ymdHis');
		$ext							= pathinfo($userImage, PATHINFO_EXTENSION);

		$userImageFileName				= $rand . '.' . $ext;

		$userImagePath     				= '../../' . $cfg['FILES.ABSTRACT.REQUEST'] . $userImageFileName;

		if (move_uploaded_file($userImageTempFile, $userImagePath)) {
			$sqlUserImage = array();
			$sqlUserImage['QUERY']           = "   UPDATE " . _DB_COMPANY_INFORMATION_ . "
														  SET `abstract_guideline_pdf_file` = '" . $userImageFileName . "' 
														WHERE `id` = '" . $participantId . "'";
			$mycms->sql_update($sqlUserImage, false);
		}
	}
}

function removeGuideLinePdf($id)
{
	global $mycms, $cfg;
	$sqlPdfFile = array();
	$sqlPdfFile['QUERY']           = "SELECT abstract_guideline_pdf_file FROM " . _DB_COMPANY_INFORMATION_ . "
														WHERE `id` = '" . $id . "'";
	$res = $mycms->sql_select($sqlPdfFile, false);
	$imgpath = '../../' . $cfg['FILES.ABSTRACT.REQUEST'] . $res[0]['abstract_guideline_pdf_file'];
	unlink($imgpath);
	$sqlPdfFile = array();
	$sqlPdfFile['QUERY']           = "   UPDATE " . _DB_COMPANY_INFORMATION_ . "
														  SET `abstract_guideline_pdf_file` = '' 
														WHERE `id` = '" . $id . "'";
	$mycms->sql_update($sqlPdfFile, false);
}

function insertDate($mycms, $cfg)
{
	$loggedUserID 		= $mycms->getLoggedUserId();
	$conf_date			= addslashes($_REQUEST['date']);
	$purpose			= addslashes($_REQUEST['purpose']);

	$insertOrderHistory	=	array();
	$insertOrderHistory['QUERY']	= "INSERT INTO " . _DB_INCLUSION_DATE_ . "
										  SET  `date` 		= ?, `purpose`=?";


	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'date',  'DATA' => $conf_date,  'TYP' => 's');
	$insertOrderHistory['PARAM'][]	=	array('FILD' => 'purpose',  'DATA' => $purpose,  'TYP' => 's');

	$mycms->sql_insert($insertOrderHistory);
	pageRedirection("company_info.php?show=edit&id=1", 2);
	exit();
}

// function updateDate($mycms, $cfg)
// {
// 	$loggedUserID 					 = $mycms->getLoggedUserId();
// 	$cutoff_id						 = $_REQUEST['cutoff_id'];
// 	$cutoff_title                 	 = addslashes(trim($_REQUEST['cutoff_title']));
// 	$start_date         			 = $_REQUEST['start_date'];
// 	$end_date         				 = $_REQUEST['end_date'];

// 	$sql		  =	array();
// 	$sql['QUERY'] = "UPDATE" . _DB_TARIFF_CUTOFF_ . " 
// 							SET `cutoff_title`				= ?,
// 							  	`start_date`  				= ?,
// 							 	`end_date`					= ?,
// 							  	`modified_by`				= ?,
// 								`modified_ip`				= ?,
// 								`modified_sessionid`		= ?,
// 								`modified_datetime`			= ?
// 							  WHERE	`id`                	= ?";

// 	$sql['PARAM'][]	=	array('FILD' => 'cutoff_title', 				'DATA' => $cutoff_title, 						'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'start_date', 				    'DATA' => $start_date, 						'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'end_date', 				    'DATA' => $end_date, 						    'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'modified_by', 			    'DATA' => $loggedUserID, 						'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'modified_ip', 			    'DATA' => $_SERVER['REMOTE_ADDR'], 			'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'modified_sessionid', 		    'DATA' => session_id(), 						'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'modified_datetime', 			'DATA' => date('Y-m-d'), 				        'TYP' => 's');
// 	$sql['PARAM'][]	=	array('FILD' => 'id', 						    'DATA' => $cutoff_id, 							'TYP' => 's');

// 	$mycms->sql_update($sql);
// }

function ActiveDate($mycms, $cfg)
{

	$sql_date['QUERY'] = "UPDATE " . _DB_INCLUSION_DATE_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql_date['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'A', 			 			  'TYP' => 's');
	$sql_date['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql_date);
	pageRedirection("company_info.php", 2);
	exit();
}
/*================ INACTIVE WORKSHOP =================*/
function InactiveDate($mycms, $cfg)
{

	$sql_date['QUERY'] = "UPDATE " . _DB_INCLUSION_DATE_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql_date['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'I', 			 			  'TYP' => 's');
	$sql_date['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql_date);
	pageRedirection("company_info.php", 2);
	exit();
}
function deleteDate($mycms, $cfg)
{

	$sql_date['QUERY'] = "UPDATE " . _DB_INCLUSION_DATE_ . " 
						    SET `status`	 = ? 
						  WHERE `id`		 = ? ";
	$sql_date['PARAM'][]	=	array('FILD' => 'status', 		 'DATA' => 'D', 			 			  'TYP' => 's');
	$sql_date['PARAM'][]	=	array('FILD' => 'id', 		     'DATA' => $_REQUEST['id'], 			  'TYP' => 's');
	$mycms->sql_update($sql_date);
	pageRedirection("company_info.php", 2);
	exit();
}

/*========== UTILITY METHOD ===============*/
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
	global $mycms, $cfg;

	$pageKey = "_pgn_";

	$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

	@$searchString = "";
	$searchArray  = array();

	$searchArray[$pageKey]                 = $pageKeyVal;
	$searchArray['src_country_name']       = trim($_REQUEST['src_country_name']);

	foreach ($searchArray as $searchKey => $searchVal) {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}

	$mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}
