<?php
	define("_DB_LOGIN_DETAILS_", 							"`_login_records_`");
	
	// ARCHITECTURE RELATED TABLES
	$TABLE_PREFIX					    		          = 'conf_';
	define("_DB_CONF_USER_", 								"`".$TABLE_PREFIX."user`");	
	define("_DB_CONF_SECTION_", 							"`".$TABLE_PREFIX."webpagesection`");
	define("_DB_CONF_MODULE_", 								"`".$TABLE_PREFIX."webpagemodule`");
	define("_DB_CONF_PAGE_", 								"`".$TABLE_PREFIX."webpage`");
	define("_DB_CONF_PAGE_ACCESS_", 						"`".$TABLE_PREFIX."webpageaccess`");
	define("_DB_CONF_ROLE_", 								"`".$TABLE_PREFIX."webmasterrole`");
	define("_DB_CONF_ROLEDETAILS_", 						"`".$TABLE_PREFIX."webmasterroledetails`");
	define("_DB_CONF_COMPANY_", 							"`".$TABLE_PREFIX."company`");
	define("_DB_CONF_DOMAIN_", 								"`".$TABLE_PREFIX."webdomain`");
	define("_DB_CONF_PAGE_ACCESS_CNTRL_MSTR_", 				"`".$TABLE_PREFIX."pagecontrolmaster`");
	define("_DB_CONF_PAGE_ACCESS_CNTRL_", 					"`".$TABLE_PREFIX."pagecontrol`");
	define("_DB_CONF_ISAR_MEMBER_", 						"`".$TABLE_PREFIX."isar_member`");	
	define("_DB_CONSTANTS_", 								"`".$TABLE_PREFIX."constant`");  
	
	define("_DB_WEBMASTER_SECTIONS_", 							"`".$TABLE_PREFIX."webmaster_section`");
	define("_DB_WEBMASTER_MODULE_", 								"`".$TABLE_PREFIX."webmaster_module`");
	define("_DB_WEBMASTER_PAGES_", 								"`".$TABLE_PREFIX."webmaster_pages`");
	define("_DB_WEBMASTER_SUBPAGE_", 						"`".$TABLE_PREFIX."webmaster_subpage`");
	define("_DB_WEBMASTER_TAB_", 								"`".$TABLE_PREFIX."webmaster_tab`");
	define("_DB_WEBMASTER_SUBTAB_", 						"`".$TABLE_PREFIX."webmaster_subtab`");
	// COMMON TABLES
	$TABLE_PREFIX					    		          = 'comn_';
	define("_DB_COMN_COUNTRY_", 							"`".$TABLE_PREFIX."country`");
	define("_DB_COMN_STATE_", 								"`".$TABLE_PREFIX."state`");
	define("_DB_COMN_CITIES_", 								"`".$TABLE_PREFIX."city`");
	define("_DB_COMN_LOCATION_", 							"`".$TABLE_PREFIX."location`");
	define("_DB_COMN_USER_DATA_", 							"`".$TABLE_PREFIX."user_databank`");
		
	// CRM PROJECT RELATED TABLES 
	$TABLE_PREFIX					    		          = 'rcg_';
	// FACULTY
	define("_DB_FACULTY_ACCESS_ROLE_", 						"`".$TABLE_PREFIX."abstract_review_faculty_access_role`");
	define("_DB_FACULTY_ACCESS_ROLE_DETAILS_", 				"`".$TABLE_PREFIX."abstract_review_faculty_access_role_details`");
	define("_DB_FACULTY_ACCOUNT_", 							"`".$TABLE_PREFIX."abstract_review_faculty_account`");
	define("_DB_FACULTY_ACCESS_DETAILS_", 					"`".$TABLE_PREFIX."abstract_review_faculty_access_details`");
	
	// ABSTRACT DETAILS
	define("_DB_ABSTRACT_TOPIC_", 							"`".$TABLE_PREFIX."abstract_request_topic`");
	define("_DB_ABSTRACT_SUBMISSION_", 						"`".$TABLE_PREFIX."abstract_request_submission`");
	define("_DB_ABSTRACT_PRESENTATION_", 					"`".$TABLE_PREFIX."abstract_request_presentation`");

	define("_DB_ABSTRACT_TOPIC_CATEGORY_", 					"`".$TABLE_PREFIX."abstract_category`");
	define("_DB_ABSTRACT_REQUEST_", 						"`".$TABLE_PREFIX."abstract_request`");
	define("_DB_ABSTRACT_COAUTHOR_", 						"`".$TABLE_PREFIX."abstract_coauthor`");
	
	define("_DB_ABSTRACT_ALLOTMENT_", 						"`".$TABLE_PREFIX."abstract_allotment`");
	
	define("_DB_ABSTRACT_REVIEW_", 							"`".$TABLE_PREFIX."abstract_review`");
	define("_DB_ABSTRACT_REVIEW_RESULT_", 					"`".$TABLE_PREFIX."abstract_review_result`");
	define("_DB_ABSTRACT_REVIEW_RESULT_DETAILS_", 			"`".$TABLE_PREFIX."abstract_review_result_details`");
	
	define("_DB_ABSTRACT_REVIEW_CRITERIA_", 				"`".$TABLE_PREFIX."abstract_review_criteria`");
	define("_DB_ABSTRACT_REVIEW_SCORE_", 					"`".$TABLE_PREFIX."abstract_review_category`");

	define("_DB_ABSTRACT_REVIEW_LIST_", 					"`".$TABLE_PREFIX."abstract_review_list`");
	
	define("_DB_ABSTRACT_REVIEW_CRITERIA_OPTIONS_", 		"`".$TABLE_PREFIX."abstract_review_criteria_options`");
	
	define("_DB_ABSTRACT_ACCEPTANCE_MAIL_", 				"`".$TABLE_PREFIX."abstract_request_acceptance_mailHistory`");
	define("_DB_ABSTRACT_COMMUNICATION_MAIL_", 				"`".$TABLE_PREFIX."abstract_request_communication_mail_history`");

	define("_DB_ABSTRACT_FIELDS_", 							"`".$TABLE_PREFIX."abstract_add_fields`");
	
	// AWARD
	define("_DB_AWARD_MASTER_", 							"`".$TABLE_PREFIX."award_master`");
	define("_DB_AWARD_REQUEST_", 							"`".$TABLE_PREFIX."award_request`");
	define("_DB_AWARD_REVIEW_RESULT_", 						"`".$TABLE_PREFIX."award_review_result`");
	define("_DB_AWARD_REVIEW_RESULT_DETAILS_", 				"`".$TABLE_PREFIX."award_review_result_details`");
	define("_DB_AWARD_REVIEW_CRITERIA_", 					"`".$TABLE_PREFIX."award_review_criteria`");
	define("_DB_AWARD_REVIEW_CRITERIA.OPTIONS_", 			"`".$TABLE_PREFIX."award_review_criteria_options`");
		
	define("_DB_MASTER_HOTEL_", 							"`".$TABLE_PREFIX."accommodation_hotel_master`");
	define("_DB_MASTER_ROOM_", 								"`".$TABLE_PREFIX."accommodation_room_master`");

	define("_DB_TARIFF_CUTOFF_", 							"`".$TABLE_PREFIX."tariff_cutoff`");
	define("_DB_WORKSHOP_CUTOFF_", 							"`".$TABLE_PREFIX."workshop_cutoff`");
	
	define("_DB_CONFERENCE_DATE_", 							"`".$TABLE_PREFIX."conference_date`");
	define("_DB_INCLUSION_DATE_", 							"`".$TABLE_PREFIX."inclusion_dates`");
	define("_DB_ACCOMMODATION_CHECKIN_DATE_", 				"`".$TABLE_PREFIX."accommodation_checkin_date`");
	define("_DB_ACCOMMODATION_CHECKOUT_DATE_", 				"`".$TABLE_PREFIX."accommodation_checkout_date`");

	define("_DB_ACCOMMODATION_PACKAGE_PRICE_", 				"`".$TABLE_PREFIX."accommodation_package_price`");

	define("_DB_COMBO_CHECKIN_DATE_", 						"`".$TABLE_PREFIX."combo_checkin_date`");
	define("_DB_COMBO_CHECKOUT_DATE_", 						"`".$TABLE_PREFIX."combo_checkout_date`");

	define("_DB_ACCOMMODATION_PACKAGE_", 				    "`".$TABLE_PREFIX."accommodation_package`");

	define("_DB_ACCOMMODATION_ACCESSORIES_", 				 "`".$TABLE_PREFIX."accommodation_hotel_accessories`");
	define("_DB_TAG_MASTER_", 								 "`".$TABLE_PREFIX."tag_master`");
		
	// REGISTRATION SERVICELIST
	define("_DB_REGISTRATION_SERVICELIST_", 				"`".$TABLE_PREFIX."registration_servicelist`");
		
	/*// SCIENTIFIC PROGRAM
	define("_DB_MASTER_HALL_", 								"`".$TABLE_PREFIX."master_hall`");
	define("_DB_PROGRAM_SCHEDULE_DATE_", 					"`".$TABLE_PREFIX."program_schedule_date`");
	define("_DB_PROGRAM_SCHEDULE_SESSION_", 				"`".$TABLE_PREFIX."program_schedule_session`");
	define("_DB_PROGRAM_SCHEDULE_THEME_", 					"`".$TABLE_PREFIX."program_schedule_theme`");
	define("_DB_PROGRAM_SCHEDULE_TOPIC_", 					"`".$TABLE_PREFIX."program_schedule_topic`");
	define("_DB_PROGRAM_SCHEDULE_EMAIL.SEND_", 				"`".$TABLE_PREFIX."program_schedule_email`");
	define("_DB_PROGRAM_CHECKLIST_", 						"`".$TABLE_PREFIX."program_checklist`");*/
	
	// Dinner
	define("_DB_DINNER_CLASSIFICATION_", 					"`".$TABLE_PREFIX."dinner_classification`");
	define("_DB_DINNER_TARIFF_", 							"`".$TABLE_PREFIX."dinner_tariff`");
	
	//CLOAKROOM
	define("_DB_CLOAKROOM_", 								"`".$TABLE_PREFIX."cloakroom`");
	
	// EXHIBITOR
	define("_DB_EXIBITOR_CUTOFF_", 							"`".$TABLE_PREFIX."exhibitor_tariff_cutoff`");
	define("_DB_EXIBITOR_COMPANY_", 						"`".$TABLE_PREFIX."exhibitor_company`");
	define("_DB_EXIBITOR_ASSOCIATION_TYPE_", 				"`".$TABLE_PREFIX."exhibitor_association`");
	define("_DB_EXIBITOR_CONTACT_PERSON_", 					"`".$TABLE_PREFIX."exhibitor_contact_person`");
	define("_DB_EXIBITOR_PARTNERSHIP_CATEGORY_", 			"`".$TABLE_PREFIX."exhibitor_partnership_category`");
	define("_DB_EXIBITOR_DELEGATE_MAPPING_", 				"`".$TABLE_PREFIX."exhibitor_delegate_mapping`");  
	define("_DB_EXIBITOR_BOOKING_ORDER_", 					"`".$TABLE_PREFIX."exhibitor_booking_order`");
	define("_DB_EXIBITOR_BOOKING_ORDER_PAYMENT_", 			"`".$TABLE_PREFIX."exhibitor_booking_payment`");
	
	define("_DB_EXIBITOR_STALL_BOOKING_", 					"`".$TABLE_PREFIX."exhibitor_stall_booking`");
	define("_DB_EXIBITOR_STALL_BOOKING_PAYMENT_", 			"`".$TABLE_PREFIX."exhibitor_stall_booking_payment`");
	define("_DB_EXIBITOR_STALL_BOOKING_LAYOUT_", 			"`".$TABLE_PREFIX."exhibitor_stall_booking_layout`");
	define("_DB_EXIBITOR_STALL_BOOKING_REQUEST_", 			"`".$TABLE_PREFIX."exhibitor_stall_booking_request`");

	define("_DB_EXIBITOR_STALL_BOOKING_ADDONS_", 			"`".$TABLE_PREFIX."exhibitor_stall_booking_addons`");
	
	define("_DB_EXIBITOR_COMPANY_COMMITMENT_", 				"`".$TABLE_PREFIX."exhibitor_company_commitment`");
	define("_DB_EXIBITOR_COMPANY_AMOUNT_", 					"`".$TABLE_PREFIX."exhibitor_company_amount_record`");
	
	define("_DB_EXIBITOR_COMPANY_USERS_", 					"`".$TABLE_PREFIX."exhibitor_company_user_record`"); 
	
	define("_DB_EXIBITOR_REGISTRATION_TARIFF_", 			"`".$TABLE_PREFIX."exhibitor_tariff_registration`");
	define("_DB_EXIBITOR_SLIP_", 							"`".$TABLE_PREFIX."exhibitor_slip`");		
	define("_DB_EXIBITOR_INVOICE_", 						"`".$TABLE_PREFIX."exhibitor_invoice`");	
	define("_DB_EXIBITOR_PAYMENT_", 						"`".$TABLE_PREFIX."exhibitor_payment`");
	define("_DB_EXIBITOR_CALLDETAILS_", 					"`".$TABLE_PREFIX."exhibitor_calldetails`");
	define("_DB_EXIBITOR_MAILHISTORY_", 					"`".$TABLE_PREFIX."exhibitor_mailhistory`");
		
	// PACKAGE
	define("_DB_PACKAGE_ACCOMMODATION_", 					"`".$TABLE_PREFIX."accommodation_package`");
	
	// CLASSIFICATION
	define("_DB_REGISTRATION_CLASSIFICATION_", 				"`".$TABLE_PREFIX."registration_classification`");
	define("_DB_REGISTRATION_COMBO_CLASSIFICATION_", 		"`".$TABLE_PREFIX."registration_combo_classification`");
	define("_DB_WORKSHOP_CLASSIFICATION_", 					"`".$TABLE_PREFIX."workshop_classification`");

	define("_DB_ACCOMPANY_CLASSIFICATION_", 				"`".$TABLE_PREFIX."accompany_classification`");
	define("_DB_TARIFF_ACCOMPANY_", 						"`".$TABLE_PREFIX."tariff_accompany`");
	
	// TARIFF
	define("_DB_TARIFF_REGISTRATION_", 						"`".$TABLE_PREFIX."tariff_registration`");
	define("_DB_TARIFF_COMBO_REGISTRATION_", 				"`".$TABLE_PREFIX."tariff_combo_registration`");
	define("_DB_TARIFF_COMBO_ACCOMODATION_", 				"`".$TABLE_PREFIX."tariff_combo_accommodation`");

	define("_DB_TARIFF_WORKSHOP_", 							"`".$TABLE_PREFIX."tariff_workshop`");
	define("_DB_TARIFF_ACCOMMODATION_", 					"`".$TABLE_PREFIX."tariff_accommodation`");
	define("_DB_TARIFF_TOUR_", 								"`".$TABLE_PREFIX."tariff_tour`");
	
	// USER
	define("_DB_USER_REGISTRATION_", 						"`".$TABLE_PREFIX."user_registration`");
	define("_DB_EXHIBITOR_REGISTRATION_", 						"`".$TABLE_PREFIX."exhibitor_user_registration`");
	define("_DB_USER_UNREGISTER_REQUEST_", 					"`".$TABLE_PREFIX."user_unregister_request`");
	define("_DB_USER_GUEST_REGISTRATION_", 					"`".$TABLE_PREFIX."user_registration_rec_for_guest`");
	define("_DB_USER_VOLUNTEER_REGISTRATION_", 				"`".$TABLE_PREFIX."user_registration_rec_for_volunteer`");

	define("_DB_BLUK_REGISTRATION_SESSION_", 				"`".$TABLE_PREFIX."user_registration_bulk_upload_session`");
	define("_DB_BLUK_REGISTRATION_DATA_", 					"`".$TABLE_PREFIX."user_registration_bulk_upload_data`");
	
	define("_DB_EC_MEMBERS_", 								"`".$TABLE_PREFIX."members_ec`");
	define("_DB_NF_MEMBERS_", 								"`".$TABLE_PREFIX."members_nf`");
	
	// INVOICE
	define("_DB_INVOICE_", 									"`".$TABLE_PREFIX."user_registration_invoice`");
	define("_DB_INVOICE_CAPTURE_", 							"`".$TABLE_PREFIX."user_registration_invoice_capture`");
	define("_DB_SLIP_", 									"`".$TABLE_PREFIX."slip`");
	
	define("_DB_INVOICE_COPY_", 							"`".$TABLE_PREFIX."invoice_history`");
	define("_DB_SLIP_COPY_", 								"`".$TABLE_PREFIX."slip_history`");	
	define("_DB_CANCEL_INVOICE_", 							"`".$TABLE_PREFIX."user_registration_invoice_cancel_request`");
	
	// PAYMENT
	define("_DB_PAYMENT_", 									"`".$TABLE_PREFIX."slip_payment`");
	define("_DB_PAYMENT_RAW_DATA_", 						"`".$TABLE_PREFIX."slip_payment_raw_data`");
	define("_DB_PAYMENT_REQUEST_", 							"`".$TABLE_PREFIX."slip_payment_request`");	
	define("_DB_DISCOUNT_", 								"`".$TABLE_PREFIX."slip_discount`");
	
	//WALLET
	define("_DB_WALLET_IN_", 								"`".$TABLE_PREFIX."wallet_in`");
	define("_DB_WALLET_OUT_", 								"`".$TABLE_PREFIX."wallet_out`");
	define("_DB_WALLET_REIMBURSE_REQUEST_", 				"`".$TABLE_PREFIX."wallet_reimburse_request`");	
	
	
	// REQUEST	
	define("_DB_REQUEST_WORKSHOP_", 						"`".$TABLE_PREFIX."request_workshop`");
	define("_DB_REQUEST_ACCOMMODATION_", 					"`".$TABLE_PREFIX."request_accommodation`");	
	define("_DB_REQUEST_DINNER_", 							"`".$TABLE_PREFIX."request_dinner`");	
	define("_DB_REQUEST_PICKUP_DROPOFF_", 					"`".$TABLE_PREFIX."request_pickup_dropoff`");
	
	//REFUND
	define("_DB_REFUND_RECORD_", 							"`".$TABLE_PREFIX."refund_record`"); 
			
	// SPOT
	define("_DB_USER_KIT_TRACKING_", 							"`".$TABLE_PREFIX."tracking_kit`");
	define("_DB_USER_FACULTY_KIT_TRACKING_", 					"`".$TABLE_PREFIX."tracking_kit_faculty`");
	define("_DB_USER_LUNCH_TRACKING_", 							"`".$TABLE_PREFIX."tracking_lunch`");
	define("_DB_USER_BREAKFAST_TRACKING_", 						"`".$TABLE_PREFIX."tracking_breakfast`");
	define("_DB_USER_DINNER_TRACKING_", 						"`".$TABLE_PREFIX."tracking_dinner`");
	define("_DB_USER_EXHIBITOR_TRACKING_", 						"`".$TABLE_PREFIX."tracking_exhibitor`");


	define("_DB_USER_PRESIDENTIAL_DINNER_TRACKING_", 			"`".$TABLE_PREFIX."tracking_presidential_dinner`");
	define("_DB_USER_IANUGRAL_DINNER_TRACKING_", 				"`".$TABLE_PREFIX."tracking_ianugral_dinner`");
	define("_DB_USER_GALA_DINNER_TRACKING_", 					"`".$TABLE_PREFIX."tracking_gala_dinner`");
	define("_DB_USER_CONFERENCE_TRACKING_", 					"`".$TABLE_PREFIX."tracking_conference`");

	define("_DB_USER_SPECIAL_DINNER_TRACKING_", 				"`".$TABLE_PREFIX."tracking_dinner_special`");	
	define("_DB_PRINT_ATTENDNCE_CARD_", 						"`".$TABLE_PREFIX."attendance_certificate_card`");
	define("_DB_USER_ATTENDANCE_TRACKING_", 					"`".$TABLE_PREFIX."user_attendance_tracking`");
	define("_DB_USER_ATTENDANCE_DELIVERY_TRACKING_", 			"`".$TABLE_PREFIX."tracking_attendance_card_delivery`");
	define("_DB_USER_CARD_DELIVERY_TRACKING_", 					"`".$TABLE_PREFIX."tracking_id_card_delivery`");
	define("_DB_USER_WORKSHOP_TRACKING_", 						"`".$TABLE_PREFIX."tracking_workshop`");
	define("_DB_USER_CERTIFICATE_DELIVERY_TRACKING_", 			"`".$TABLE_PREFIX."tracking_certificate_delivery`");
	define("_DB_USER_ABSTRACT_CERTIFICATE_DELIVERY_TRACKING_", 	"`".$TABLE_PREFIX."tracking_abstract_certificate_delivery`");
	
	define("_DB_USER_CERTIFICATE_MAP_", 						"`".$TABLE_PREFIX."user_certificate_mapping`");
	
	// COMMUNICATION TRACKING
	define("_DB_SEND_EMAIL_HISTORY_", 							"`".$TABLE_PREFIX."email_history`");
	define("_DB_SEND_ALTERNATE_EMAIL_HISTORY_", 				"`".$TABLE_PREFIX."alternate_email_history`");
	define("_DB_SEND_SMS_HISTORY_", 							"`".$TABLE_PREFIX."sms_history`");
	define("_DB_SEND_COMMUNICATION_SMS_HISTORY_", 				"`".$TABLE_PREFIX."sms_communication_history`");
	
	define("_DB_ADDITIONAL_EMAIL_HISTORY_", 					"`".$TABLE_PREFIX."additional`");
	define("_DB_PRINT_ID_CARD_", 								"`".$TABLE_PREFIX."print_id_card`");
	define("_DB_BOX_MASTER_", 									"`".$TABLE_PREFIX."id_print_box_master_for_delegate`");
	define("_DB_BOX_ASSIGNMENT_HISTORY_", 						"`".$TABLE_PREFIX."id_print_box_assignment_history_for_delegate`");
	define("_DB_BOX_EXIBITOR_MASTER_", 							"`".$TABLE_PREFIX."box_exhibitor_master`");
	define("_DB_BOX_EXIBITOR_ASSIGNMENT_HISTORY_", 				"`".$TABLE_PREFIX."box_exhibitor_assignment_history`");
	define("_DB_UNPAID_BOX_MASTER_", 							"`".$TABLE_PREFIX."id_print_box_master_for_unpaid_delegate`");
	define("_DB_UNPAID_BOX_ASSIGNMENT_HISTORY_", 				"`".$TABLE_PREFIX."id_print_box_assignment_history_for_unpaid_delegate`");
	
	define("_DB_CERTIFICATE_BOX_PLACEMENT_", 					"`".$TABLE_PREFIX."certificate_box_placement`");
	
	define("_DB_FAQ_", 											"`".$TABLE_PREFIX."faq`");

	define("_DB_EMAIL_SETTING_", 								"`".$TABLE_PREFIX."email_setting`");
	
	define("_DB_LANDING_PAGE_SETTING_", 						"`".$TABLE_PREFIX."landing_page_information`");

	define("_DB_LANDING_FLYER_IMAGE_", 							"`".$TABLE_PREFIX."landing_flyer_image`");

	define("_DB_ICON_SETTING_", 								"`".$TABLE_PREFIX."icon_setting`");

	define("_DB_SITE_ICON_SETTING_", 							"`".$TABLE_PREFIX."site_icon_setting`");

	define("_DB_SOCIAL_ICON_SETTING_", 							"`".$TABLE_PREFIX."social_icon_setting`");

	define("_DB_EMAIL_TEMPLATE_", 								"`".$TABLE_PREFIX."mail_template`");

	define("_DB_EMAIL_CONSTANT_", 								"`".$TABLE_PREFIX."mail_constant`");

	define("_DB_COMPANY_INFORMATION_", 								"`".$TABLE_PREFIX."company_information`");
	
	
	// CERTIFICATE	
	define("_DB_FEEDBACK_FROM_", 								"`".$TABLE_PREFIX."feedback_from`");
	define("_DB_FEEDBACK_FROM_DETAILS_", 						"`".$TABLE_PREFIX."feedback_from_details`");
		
	/*// SCIENTIFIC PROGRAM
	define("_DB_MASTER_HALL_", 									"`".$TABLE_PREFIX."program_schedule_hall`");
	define("_DB_PROGRAM_CHECKLIST_", 							"`".$TABLE_PREFIX."program_checklist`");	
	define("_DB_SP_PARTICIPANT_DETAILS_", 						"`".$TABLE_PREFIX."program_schedule_participant_details`");*/
		
	define("_DB_MASTER_HALL_", 									"`".$TABLE_PREFIX."program_schedule_hall`");
	define("_DB_MASTER_HALL_NAME_", 							"`".$TABLE_PREFIX."program_schedule_hall_name`");
	define("_DB_PROGRAM_SCHEDULE_VENUE_", 						"`".$TABLE_PREFIX."program_schedule_venue`");	
	define("_DB_PROGRAM_SCHEDULE_DATE_", 						"`".$TABLE_PREFIX."program_schedule_date`");

	define("_DB_PROGRAM_HIGHLIGHT_SPEAKER_", 					"`".$TABLE_PREFIX."program_highlight_speaker`");
	
	define("_DB_PROGRAM_SCHEDULE_SESSION_", 					"`".$TABLE_PREFIX."program_schedule_session`");
	define("_DB_PROGRAM_SCHEDULE_THEME_", 						"`".$TABLE_PREFIX."program_schedule_theme`");
	define("_DB_PROGRAM_SCHEDULE_TOPIC_", 						"`".$TABLE_PREFIX."program_schedule_topic`");
	define("_DB_PROGRAM_SCHEDULE_EMAIL_SEND_", 					"`".$TABLE_PREFIX."program_schedule_email`");
	define("_DB_PROGRAM_CHECKLIST_", 							"`".$TABLE_PREFIX."program_checklist`");
	define("_DB_SESSION_CLASSIFICATION", 						"`".$TABLE_PREFIX."program_schedule_session_classifications`");

	define("_DB_SP_PARTICIPANT_TYPE_", 							"`".$TABLE_PREFIX."program_schedule_participant_type_master`");
	
	define("_DB_SP_PARTICIPANT_DETAILS_", 						"`".$TABLE_PREFIX."program_schedule_participant_details`");
	define("_DB_SP_PARTICIPANT_CLASSIFICATION_", 				"`".$TABLE_PREFIX."program_schedule_participant_classifications`");
	define("_DB_SP_PARTICIPANT_DOCUMENT_", 						"`".$TABLE_PREFIX."program_schedule_participant_documents`");
	define("_DB_SP_PARTICIPANT_COMMENTS_", 						"`".$TABLE_PREFIX."program_schedule_participant_comments`");
	
	define("_DB_SP_SESSION_CLASSIFICATION_", 					"`".$TABLE_PREFIX."sp_session_classifications`");
	define("_DB_SP_MAPING_SC_TO_PC_", 							"`".$TABLE_PREFIX."sp_maping_sc_to_pc`");
	define("_DB_SP_MAPING_SC_TO_HALL_", 						"`".$TABLE_PREFIX."sp_maping_sc_to_hall`");
	define("_DB_SP_MAPING_PC_TO_PARTICIPANT_", 					"`".$TABLE_PREFIX."sp_maping_pc_to_participant`");
	define("_DB_SP_PARTICIPANT_AVAILABILITY_", 					"`".$TABLE_PREFIX."program_schedule_participant_availability`");
	define("_DB_SP_PARTICIPANT_SCHEDULE_", 						"`".$TABLE_PREFIX."program_schedule_participant_schedule`");
	define("_DB_SP_PARTICIPANT_SCHEDULE_MAIL_", 				"`".$TABLE_PREFIX."program_schedule_participant_schedule_mailhistory`");
			
	define("_DB_USER_CALLDETAILS_", 							"`".$TABLE_PREFIX."user_calldetails`");
	define("_DB_OLD_CONTACT_HISTORY_", 							"`".$TABLE_PREFIX."user_old_contact_history`");
	
	//OTHERS
	define("_DB_TERMS_CONDITION_", 						    	"`".$TABLE_PREFIX."contents`");
		
	define("_DB_USER_LEBELS_RECORDS_1_", 						"`".$TABLE_PREFIX."labels_user_records_1`");
	define("_DB_USER_LEBELS_RECORDS_2_", 						"`".$TABLE_PREFIX."labels_user_records_2`");
	define("_DB_USER_LEBELS_RECORDS_3_", 						"`".$TABLE_PREFIX."labels_user_records_3`");
	define("_DB_USER_LEBELS_RECORDS_4_", 						"`".$TABLE_PREFIX."labels_user_records_4`");
	define("_DB_USER_LEBELS_RECORDS_5_", 						"`".$TABLE_PREFIX."labels_user_records_5`");
	define("_DB_USER_LEBELS_RECORDS_6_", 						"`".$TABLE_PREFIX."labels_user_records_6`");
	define("_DB_USER_LEBELS_RECORDS_7_", 						"`".$TABLE_PREFIX."labels_user_records_7`");
	define("_DB_USER_LEBELS_RECORDS_8_", 						"`".$TABLE_PREFIX."labels_user_records_8`");
	define("_DB_USER_LEBELS_RECORDS_9_", 						"`".$TABLE_PREFIX."labels_user_records_9`");
	define("_DB_USER_LEBELS_RECORDS_10_", 						"`".$TABLE_PREFIX."labels_user_records_10`");
	define("_DB_USER_LEBELS_RECORDS_11_", 						"`".$TABLE_PREFIX."labels_user_records_11`");
	define("_DB_USER_LEBELS_RECORDS_12_", 						"`".$TABLE_PREFIX."labels_user_records_12`");
	define("_DB_USER_LEBELS_RECORDS_13_", 						"`".$TABLE_PREFIX."labels_user_records_13`");
	define("_DB_USER_LEBELS_RECORDS_14_", 						"`".$TABLE_PREFIX."labels_user_records_14`");

	define("_DB_PROCESS_STEP_", 								"`".$TABLE_PREFIX."process_flow`");
	define("_DB_IP_", 							           	 	"`".$TABLE_PREFIX."ip`");
	define("_DB_INVOICE_CANCEL_REQUEST_FROM_PROFILE_",      	"`".$TABLE_PREFIX."invoice_cancel_request_from_profile`");
?>
