<?php

//include("./engine/class.common.php"); 

$mycms = new CMS();
$sql = array();
$sql['QUERY']	= "SELECT * FROM " . _DB_COMPANY_INFORMATION_ . "
									  WHERE status = 'A' ";
$result	= $mycms->sql_select($sql);
//echo "<pre>";print_r($result[0]);
$row = $result[0];

$sqlLandingImg 	=	array();
$sqlLandingImg['QUERY']    = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
					   WHERE `title` = 'Landing Page Background Image' AND status='A'";

$resultLandingImg			 = $mycms->sql_select($sqlLandingImg);
$rowLandingImg    		 = $resultLandingImg[0];

$landingProfileImage = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowLandingImg['image'];

$sqlBgImg 	=	array();
$sqlBgImg['QUERY']    = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
					   WHERE `title` = 'Landing Page Outer Background Image' AND status='A'";
$resultBgImg		 = $mycms->sql_select($sqlBgImg);
$rowBgImg    		 = $resultBgImg[0];
$outerBgImg = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowBgImg['image'];


$sqlProfileImg 	=	array();
$sqlProfileImg['QUERY'] = "SELECT * FROM " . _DB_LANDING_FLYER_IMAGE_ . " 
                            WHERE `id`!='' AND `title`='Profile Page Background Image' AND status IN ('A', 'I')";
$resultProfileImg 	 = $mycms->sql_select($sqlProfileImg);
$rowProfileImg    		 = $resultProfileImg[0];

$profileImage = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowProfileImg['image'];

$sqlmailer 	=	array();
$sqlmailer['QUERY']    = "SELECT * FROM " . _DB_EMAIL_SETTING_ . " 
						  ";
$sql['PARAM'][]	=	array('FILD' => 'id',     		 'DATA' => 1,       	           'TYP' => 's');
$resultMailer			 = $mycms->sql_select($sqlmailer);
$rowMailer   		 = $resultMailer[0];

$cfg['MAILER.LOGO']  = $rowMailer['mailer_logo'];
$cfg['SCIENTIFIC.MAILER.HEADER']  = $rowMailer['sc_header_image'];



//$allCompanyInfo = allCompanyInfo();


//echo $allCompanyInfo['company_conf_name'];

$cfg['INTERNET.HANDLING.PERCENTAGE']  				= $row['internet_handling_percentage'];
$cfg['SERVICE.TAX.PERCENTAGE']  			   		= ($row['igst_flag'] == "1") ? ($row['igst_percentage']) : ($row['cgst_percentage'] + $row['sgst_percentage']);
$cfg['CONFERENCE.CGST']       						= $row['cgst_percentage'];
$cfg['CONFERENCE.SGST']       						= $row['sgst_percentage'];
$cfg['CONFERENCE.IGST']       						= $row['igst_percentage'];
$cfg['IGST.FLAG']       							= $row['igst_flag']; // 0=CGST/SGST, 1=IGST
$cfg['GST.FLAG']       								= $row['gst_flag'];
$cfg['PAYMENT.METHOD']       						= $row['offline_payment_method'];

$cfg['WORKSHOP.CGST']       						= 9;
$cfg['WORKSHOP.SGST']       						= 9;

$cfg['ACCOMPANY.CGST']       						= 9;
$cfg['ACCOMPANY.SGST']       						= 9;

$cfg['ACCOMMODATION.CGST']       					= 9;
$cfg['ACCOMMODATION.SGST']       					= 9;

$cfg['RESIDENTIAL.CGST']       					    = 0;
$cfg['RESIDENTIAL.SGST']       					    = 0;

$cfg['DINNER.CGST']       					    	= 9;
$cfg['DINNER.SGST']       					    	= 9;

$cfg['INT.CGST']       					            = 9;
$cfg['INT.SGST']       					            = 9;

$cfg['EXHIBITOR.CGST']								= 9;
$cfg['EXHIBITOR.SGST']								= 9;
$cfg['INVOICE_EXB_HSN']								= $row['invoice_exb_hsn'];

$cfg['GSTIN']       					            = $row['gst_number']; //'19AADTR6277P1ZI';//'19AAOFR5501R1ZF';
$cfg['PAN']       					                = $row['pan_number']; //'AADTR6277P';//'AAABR0430R';	

$cfg['invoive_number_format']       				= $row['invoive_number_format'];
// Abstract configuration array data added by weavers start
$cfg['ABSTRACT_SUBMISSION_CATEGORY']				= array('Free Paper', 'Award Paper');

//$cfg['ABSTRACT_SUBMISSION_TYPE']					= array('Abstract','Case Report');
$cfg['ABSTRACT_SUBMISSION_TYPE']					= json_decode($row['abstract_submission_type']);
//$cfg['ABSTRACT_SUB_SUBMISSION_TYPE']				= array('Oral','Poster');
$cfg['ABSTRACT_SUB_SUBMISSION_TYPE']				= json_decode($row['abstract_presentation_type']);
// Abstract configuration array data added by weavers end

$cfg['SERVICE.SEQUENCE']      						= array(
	'DELEGATE_CONFERENCE_REGISTRATION',
	'DELEGATE_RESIDENTIAL_REGISTRATION',
	'DELEGATE_WORKSHOP_REGISTRATION',
	'DELEGATE_DINNER_REQUEST',
	'ACCOMPANY_CONFERENCE_REGISTRATION',
	'ACCOMPANY_DINNER_REQUEST',
	'DELEGATE_ACCOMMODATION_REQUEST'
);
$cfg['FULL_CONF_NAME']		            		= $row['company_conf_full_name'];
$cfg['EMAIL_CONF_NAME']		            			= $row['company_conf_name'];
$cfg['APP_NAME']		            				= $row['company_conf_name'];
//$cfg['EMAIL_CONF_HELD_FROM']		            	= "September 5 - 8, 2019";
$cfg['EMAIL_CONF_HELD_FROM']		            	= "1st - 4th December 2022";
//$cfg['EMAIL_CONF_VENUE']		            		= "ITC Royal Bengal, Kolkata";
$cfg['EMAIL_CONF_VENUE']		            		= $row['company_conf_venue'];
$cfg['CONF_START_DATE']		            			= $row['conf_start_date'];
$cfg['CONF_END_DATE']		            			= $row['conf_end_date'];

$cfg['EMAIL_CONF_CONTACT_US']	           	        = $row['company_conf_mobileno'];
$cfg['EMAIL_CONF_EMAIL_US']		            		= $row['company_conf_email'];
$cfg['SC_SENDER_EMAIL']		            		= $row['scientific_sender_email'];
$cfg['SC_REPLY_EMAIL']		            		= $row['scientific_reply_email'];
$cfg['EMAIL_CONF_REPLY_US']		            		= $row['company_reply_email'];
//$cfg['EMAIL_CONF_WEBSITE']		            		= "www.aiccrcog2019.com"; 
$cfg['EMAIL_CONF_WEBSITE']		            		= "www.neocon2022.com";
//$cfg['EMAIL_SECRETARIAT']		            		= "AICC RCOG 2019 Secretariat"; 
$cfg['EMAIL_SECRETARIAT']		            		= $row['company_conf_name'];
//$cfg['EMAIL_WELCOME_TO']		            		= "33rd Annual Conference of All India Co-ordinating Committee Royal College of Obstetricians and Gynaecologists"; 
$cfg['EMAIL_WELCOME_TO']		            		= $row['company_conf_name'];
$cfg['BANQUET_DINNER_DATE']		            		= "6th September, 2019";
$cfg['BANQUET_DINNER_NAME']		            		= "GALA DINNER";

//$cfg['INVOICE_COMPANY_NAME']		            	= "RCOG EASTERN ZONE REPRESENTATIVE COMMITTEE"; 
$cfg['INVOICE_COMPANY_NAME_PREFIX']					= $row['invoice_company_name_prefix'];
$cfg['INVOICE_AUTORIZED_SIGNATURE_PREFIX']			= $row['invoice_company_name_prefix'];
$cfg['INVOICE_COMPANY_NAME']		            	= $row['invoice_company_name'];
//$cfg['INVOICE_ADDRESS']		            	        = "GC-209, Sector 3, Salt Lake City, Kolkata 700106"; 
$cfg['INVOICE_ADDRESS']		            	        = $row['invoice_address'];
$cfg['INVOICE_STATE_NAME']		            	    = $row['invoice_state_name'];
$cfg['INVOICE_FOOTER_TEXT']		            		= $row['invoice_footer_text'];
$cfg['INVOICE_STATE_CODE']		            	    = substr($row['gst_number'],0, 2);
$cfg['INVOICE_CONTACT']		            	        = $row['invoice_phone_number'];
$cfg['CART_HELPLINE']		            	        = $row['cart_helpline'];
//$cfg['INVOICE_EMAIL']		            	        = "secretariat@aiccrcog2019.com"; 
$cfg['INVOICE_EMAIL']		            	        = $row['invoice_email_address'];
//$cfg['INVOICE_WEBSITE']		            	        = "www.aiccrcog2019.com"; 
$cfg['INVOICE_WEBSITE']		            	        = $row['invoice_website_name'];
$cfg['INVOICE_BANKNAME']							= $row['invoice_bank_name'];
$cfg['INVOICE_BENEFECIARY']							= $row['invoice_beneficiary'];
$cfg['INVOICE_BANKACNO']							= $row['invoice_bank_account_number'];
$cfg['INVOICE_BANKBRANCH']							= $row['invoice_bank_branch_name'];
$cfg['INVOICE_BANKIFSC']							= $row['invoice_bank_ifsc_code'];

$cfg['PRIVACY_PAGE_INFO']							= $row['privacy_page_info'];
$cfg['TERMS_PAGE_INFO']								= $row['terms_page_info'];
//$cfg['DRAFT_INFO']							        = $row['draft_info'];
$cfg['CANCELLATION_PAGE_INFO']						= $row['cancellation_page_info'];

$cfg['REG_SUCCESS_PAGE_INFO']						= $row['success_page_info'];

$cfg['PROCESS_PAGE_INFO']							= $row['process_page_info'];
$cfg['ONLINE_PAYMENT_SUCCESS_INFO']					= $row['online_payment_success_page_info'];
$cfg['PAYMENT_FAILURE_INFO']						= $row['payment_failure_info'];
$cfg['ABSTRACT_SUBMISSION_SUCCESS_INFO']			= $row['abstract_submission_success_info'];

$cfg['CATEGORY_TITLE']								= $row['tariff_category_title'];
$cfg['USER_TITLE']									= $row['tariff_user_details_title'];
$cfg['WORKSHOP_TITLE']								= $row['tariff_workshop_title'];
$cfg['ACCOMAPNY_TITLE']								= $row['tariff_accompany_title'];
$cfg['DINNER_TITLE']								= $row['tariff_banquet_title'];
$cfg['LOGIN_TITLE']									= $row['tariff_login_title'];
$cfg['ACCOMODATION_TITLE']									= $row['tariff_accommodation_title'];

$cfg['COUNTDOWN_TITLE']								= $row['display_countdown_text'];
$cfg['SITE_URL']									= $row['conference_site_url'];
$cfg['SITE_LINK']									= $row['conference_site_url_link'];
$cfg['WEATHER_CITY']								= $row['city_for_weather_api'];
$cfg['CART_TITLE']								= $row['tariff_cart_title'];


$cfg['LANDING_PROFILE_IMG'] = $landingProfileImage;
$cfg['OUTER_BG_IMG'] = $outerBgImg;

$cfg['PROFILE_Back_IMG'] = $profileImage;

$cfg['PROFILE_VENUE_TEXT']		        = $row['profile_venue_text'];

$cfg['cheque_info']						= $row['cheque_info'];
$cfg['draft_info']						= $row['draft_info'];
$cfg['neft_info']						= $row['neft_info'];
$cfg['rtgs_info']						= $row['rtgs_info'];
$cfg['cash_info']						= $row['cash_info'];
$cfg['upi_info']						= $row['upi_info'];
$cfg['payment_declaration']				= $row['payment_declaration'];


$cfg['color']			    			= $row['color'];
$cfg['dark_color']			    		= $row['dark_color'];
$cfg['light_color']			    		= $row['light_color'];

$cfg['accomodation_date']				= "2023-05-03,2023-05-04,2023-05-05";


$cfg['CONFERENCE_NAME']		            		    = "CONFERENCE REGISTRATION";
$cfg['RESIDENTIAL_NAME']                            = "CONFERENCE REGISTRATION (INAUGURAL OFFER)";

$cfg['RESIDENTIAL_NAME_IN_2N']						= "RESIDENTIAL PACKAGE - Individual (2N Package)"; //(Includes ".$cfg['BANQUET_DINNER_NAME'].")
$cfg['RESIDENTIAL_NAME_IN_3N']						= "RESIDENTIAL PACKAGE - Individual (3N Package)"; //(Includes ".$cfg['BANQUET_DINNER_NAME'].")
$cfg['RESIDENTIAL_NAME_SH_2N']						= "RESIDENTIAL PACKAGE - Sharing  (2N Package)"; //(Includes ".$cfg['BANQUET_DINNER_NAME'].")
$cfg['RESIDENTIAL_NAME_SH_3N']						= "RESIDENTIAL PACKAGE - Sharing  (3N Package)"; //(Includes ".$cfg['BANQUET_DINNER_NAME'].")
$cfg['RESIDENTIAL_NAME']                            = "CONFERENCE REGISTRATION (INAUGURAL OFFER)"; //(Includes ".$cfg['BANQUET_DINNER_NAME'].")	

$cfg['ABSTRACT.FREE.PAPER.SESSION.WORD.LIMIT']      = $row['abstract_total_word_limit'];
$cfg['CASEREPORT.FREE.PAPER.SESSION.WORD.LIMIT']    = $row['abstract_total_word_limit'];

$cfg['ABSTRACT.TITLE.WORD.LIMIT']       			= $row['abstract_title_word_limit'];
$cfg['CASEREPORT.TITLE.WORD.LIMIT']       			= $row['abstract_title_word_limit'];

$cfg['ABSTRACT.SUBMIT.LIMIT']       				= 999;
$cfg['CASEREPORT.SUBMIT.LIMIT']       				= 999;

$cfg['ABSTRACT.SUBMIT.LASTDATE']       				= $row['abstract_submission_date'];
$cfg['CASEREPORT.SUBMIT.LASTDATE']       			= $row['abstract_submission_date'];

$cfg['ABSTRACT.EDIT.LASTDATE']       				= $row['abstract_submission_date'];
$cfg['CASEREPORT.EDIT.LASTDATE']       				= $row['abstract_submission_date'];

$cfg['ABSTRACT.CONFIRMATION.DATE']       			= $row['abstract_confirmation_date'];
$cfg['CASEREPORT.CONFIRMATION.DATE']       			= $row['abstract_confirmation_date'];

$cfg['ABSTRACT.TITLE.WORD.TYPE']       				= $row['abstract_word_title_type'];
$cfg['ABSTRACT.TOTAL.WORD.TYPE']       				= $row['abstract_total_word_type'];


$cfg['ABSTRACT.GUIDELINE.PDF']       				= $row['abstract_guideline_pdf'];

$cfg['ABSTRACT.GUIDELINE.PDF.FLAG']       			= $row['guideline_pdf_flag'];
$cfg['ABSTRACT.GUIDELINE.PDF.FILE']       			= $row['abstract_guideline_pdf_file'];
$cfg['ABSTRACT.SUCCESS.IMAGE.TEXT']       			= $row['abstract_success_img_text'];
$cfg['REGISTRATION.FAILURE.IMAGE.TEXT']       		= $row['reg_failure_img_text'];
$cfg['INVOICE_SIGN_NAME']       					= $row['invoice_sign_name'];


$cfg['REGISTER.COUNTDOWN']       				    = $row['registration_countdown_date'];
$cfg['EXHIBITOR.BULK.SUBMIT.LASTDATE']       		= '2019-08-31';

$cfg['INDEPENDANT.WORKSHOPS']       				= array(9);

$cfg['UPGRADABILITY']['1'] 			= array(7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);
$cfg['UPGRADABILITY']['3'] 			= array(7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);
$cfg['UPGRADABILITY']['4'] 			= array(7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);
$cfg['UPGRADABILITY']['5'] 			= array(7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);
$cfg['UPGRADABILITY']['6'] 			= array(7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18);
$cfg['UPGRADABILITY']['7'] 			= array(8);
$cfg['UPGRADABILITY']['9'] 			= array(7, 8, 10);
$cfg['UPGRADABILITY']['11'] 		= array(12);
$cfg['UPGRADABILITY']['13'] 		= array(11, 12, 14);
$cfg['UPGRADABILITY']['15'] 		= array(16);
$cfg['UPGRADABILITY']['17'] 		= array(15, 16, 18);

$accommodationPackageArray = array();
$accommodationPackageArray[1][0]['STARTDATE']['ID']   = 4;
$accommodationPackageArray[1][0]['STARTDATE']['DATE'] = "2019-09-04";
$accommodationPackageArray[1][0]['ENDDATE']['ID']     = 1;
$accommodationPackageArray[1][0]['ENDDATE']['DATE']   = "2019-09-06";

$accommodationPackageArray[1][1]['STARTDATE']['ID']   = 1;
$accommodationPackageArray[1][1]['STARTDATE']['DATE'] = "2019-09-05";
$accommodationPackageArray[1][1]['ENDDATE']['ID']     = 2;
$accommodationPackageArray[1][1]['ENDDATE']['DATE']   = "2019-09-07";

$accommodationPackageArray[1][2]['STARTDATE']['ID']   = 2;
$accommodationPackageArray[1][2]['STARTDATE']['DATE'] = "2019-09-06";
$accommodationPackageArray[1][2]['ENDDATE']['ID']     = 3;
$accommodationPackageArray[1][2]['ENDDATE']['DATE']   = "2019-09-08";

$accommodationPackageArray[1][3]['STARTDATE']['ID']   = 3;
$accommodationPackageArray[1][3]['STARTDATE']['DATE'] = "2019-09-07";
$accommodationPackageArray[1][3]['ENDDATE']['ID']     = 5;
$accommodationPackageArray[1][3]['ENDDATE']['DATE']   = "2019-09-09";

$accommodationPackageArray[2][0]['STARTDATE']['ID']   = 4;
$accommodationPackageArray[2][0]['STARTDATE']['DATE'] = "2019-09-04";
$accommodationPackageArray[2][0]['ENDDATE']['ID']     = 2;
$accommodationPackageArray[2][0]['ENDDATE']['DATE']   = "2019-09-07";

$accommodationPackageArray[2][1]['STARTDATE']['ID']   = 1;
$accommodationPackageArray[2][1]['STARTDATE']['DATE'] = "2019-09-05";
$accommodationPackageArray[2][1]['ENDDATE']['ID']     = 3;
$accommodationPackageArray[2][1]['ENDDATE']['DATE']   = "2019-09-08";

$accommodationPackageArray[2][2]['STARTDATE']['ID']   = 2;
$accommodationPackageArray[2][2]['STARTDATE']['DATE'] = "2019-09-06";
$accommodationPackageArray[2][2]['ENDDATE']['ID']     = 5;
$accommodationPackageArray[2][2]['ENDDATE']['DATE']   = "2019-09-09";

$accommodationPackageArray[3][0]['STARTDATE']['ID']   = 1;
$accommodationPackageArray[3][0]['STARTDATE']['DATE'] = "2019-09-05";
$accommodationPackageArray[3][0]['ENDDATE']['ID']     = 2;
$accommodationPackageArray[3][0]['ENDDATE']['DATE']   = "2019-09-07";
//	$accommodationPackageArray[3][1]['STARTDATE']['ID']   = 2;
//	$accommodationPackageArray[3][1]['STARTDATE']['DATE'] = "2019-09-06";
//	$accommodationPackageArray[3][1]['ENDDATE']['ID']     = 3;
//	$accommodationPackageArray[3][1]['ENDDATE']['DATE']   = "2019-09-08";

$accommodationPackageArray[4][0]['STARTDATE']['ID']   = 1;
$accommodationPackageArray[4][0]['STARTDATE']['DATE'] = "2019-09-05";
$accommodationPackageArray[4][0]['ENDDATE']['ID']     = 3;
$accommodationPackageArray[4][0]['ENDDATE']['DATE']   = "2019-09-08";

$cfg['ACCOMMODATION_PACKAGE_ARRAY'] = $accommodationPackageArray;


$residentialPackageArray = array();

$residentialPackageArray[7]         = "1";
$residentialPackageArray[8]         = "2";
$residentialPackageArray[9]         = "3";
$residentialPackageArray[10]        = "4";
$residentialPackageArray[11]        = "1";
$residentialPackageArray[12]        = "2";
$residentialPackageArray[13]        = "3";
$residentialPackageArray[14]        = "4";
$residentialPackageArray[15]        = "1";
$residentialPackageArray[16]        = "2";
$residentialPackageArray[17]        = "3";
$residentialPackageArray[18]        = "4";

$cfg['RESIDENTIAL_PACKAGE_ARRAY'] = $residentialPackageArray;
$cfg['INAUGURAL_OFFER_CLASF_ID']  = "3";

$cfg['RESIDENTIAL_SHARING_CLASF_ID']  = array(9, 10, 13, 14, 17, 18);


//// ISD CODES
$cfg['ISD_CODES']									= array(
	'United States' => '+1',
	'Russia' => '+7',
	'Egypt' => '+20',
	'South Africa' => '+27',
	'Greece' => '+30',
	'Netherlands' => '+31',
	'Belgium' => '+32',
	'France' => '+33',
	'Spain' => '+34',
	'Hungary' => '+36',
	'Italy' => '+39',
	'Romania' => '+40',
	'Switzerland' => '+41',
	'Austria' => '+43',
	'United Kingdom' => '+44',
	'Denmark' => '+45',
	'Sweden' => '+46',
	'Norway' => '+47',
	'Svalbard and Jan Mayen' => '+47',
	'Poland' => '+48',
	'Germany' => '+49',
	'Peru' => '+51',
	'Mexico' => '+52',
	'Cuba' => '+53',
	'Argentina' => '+54',
	'Brazil' => '+55',
	'Chile' => '+56',
	'Colombia' => '+57',
	'Venezuela' => '+58',
	'Malaysia' => '+60',
	'Australia' => '+61',
	'Indonesia' => '+62',
	'Philippines' => '+63',
	'New Zealand' => '+64',
	'Singapore' => '+65',
	'Thailand' => '+66',
	'Japan' => '+81',
	'South Korea' => '+82',
	'Vietnam' => '+84',
	'China' => '+86',
	'Turkey' => '+90',
	'India' => '+91',
	'Pakistan' => '+92',
	'Afghanistan' => '+93',
	'Sri Lanka' => '+94',
	'Myanmar' => '+95',
	'Iran' => '+98',
	'South Sudan' => '+211',
	'Morocco' => '+212',
	'Western Sahara' => '+212',
	'Algeria' => '+213',
	'Tunisia' => '+216',
	'Libya' => '+218',
	'Gambia' => '+220',
	'Senegal' => '+221',
	'Mauritania' => '+222',
	'Mali' => '+223',
	'Guinea' => '+224',
	'Ivory Coast' => '+225',
	'Burkina Faso' => '+226',
	'Niger' => '+227',
	'Togo' => '+228',
	'Benin' => '+229',
	'Mauritius' => '+230',
	'Liberia' => '+231',
	'Sierra Leone' => '+232',
	'Ghana' => '+233',
	'Nigeria' => '+234',
	'Chad' => '+235',
	'Central African Republic' => '+236',
	'Cameroon' => '+237',
	'Cape Verde' => '+238',
	'Sao Tome and Principe' => '+239',
	'Equatorial Guinea' => '+240',
	'Gabon' => '+241',
	'Republic of the Congo' => '+242',
	'Democratic Republic of the Congo' => '+243',
	'Angola' => '+244',
	'Guinea-Bissau' => '+245',
	'British Indian Ocean Territory' => '+246',
	'Seychelles' => '+248',
	'Sudan' => '+249',
	'Rwanda' => '+250',
	'Ethiopia' => '+251',
	'Somalia' => '+252',
	'Djibouti' => '+253',
	'Kenya' => '+254',
	'Tanzania' => '+255',
	'Uganda' => '+256',
	'Burundi' => '+257',
	'Mozambique' => '+258',
	'Zambia' => '+260',
	'Madagascar' => '+261',
	'Mayotte' => '+262',
	'Zimbabwe' => '+263',
	'Namibia' => '+264',
	'Malawi' => '+265',
	'Lesotho' => '+266',
	'Botswana' => '+267',
	'Swaziland' => '+268',
	'Comoros' => '+269',
	'Saint Helena' => '+290',
	'Eritrea' => '+291',
	'Aruba' => '+297',
	'Faroe Islands' => '+298',
	'Greenland' => '+299',
	'Gibraltar' => '+350',
	'Portugal' => '+351',
	'Luxembourg' => '+352',

	'Ireland' => '+353',
	'Iceland' => '+354',
	'Albania' => '+355',
	'Malta' => '+356',
	'Cyprus' => '+357',
	'Finland' => '+358',
	'Bulgaria' => '+359',
	'Lithuania' => '+370',
	'Latvia' => '+371',
	'Estonia' => '+372',
	'Moldova' => '+373',
	'Armenia' => '+374',
	'Belarus' => '+375',
	'Andorra' => '+376',
	'Monaco' => '+377',
	'San Marino' => '+378',
	'Vatican' => '+379',
	'Ukraine' => '+380',
	'Serbia' => '+381',
	'Montenegro' => '+382',
	'Kosovo' => '+383',
	'Croatia' => '+385',
	'Slovenia' => '+386',
	'Bosnia and Herzegovina' => '+387',
	'Macedonia' => '+389',
	'Czech Republic' => '+420',
	'Slovakia' => '+421',
	'Liechtenstein' => '+423',
	'Falkland Islands' => '+500',
	'Belize' => '+501',
	'Guatemala' => '+502',
	'El Salvador' => '+503',
	'Honduras' => '+504',
	'Nicaragua' => '+505',
	'Costa Rica' => '+506',
	'Panama' => '+507',
	'Saint Pierre and Miquelon' => '+508',
	'Haiti' => '+509',
	'Saint Barthelemy' => '+590',
	'Saint Martin' => '+590',
	'Bolivia' => '+591',
	'Guyana' => '+592',
	'Ecuador' => '+593',
	'Paraguay' => '+595',
	'Suriname' => '+597',
	'Uruguay' => '+598',
	'Curacao' => '+599',
	'Netherlands Antilles' => '+599',
	'East Timor' => '+670',
	'Antarctica' => '+672',
	'Brunei' => '+673',
	'Nauru' => '+674',
	'Papua New Guinea' => '+675',
	'Tonga' => '+676',
	'Solomon Islands' => '+677',
	'Vanuatu' => '+678',
	'Fiji' => '+679',
	'Palau' => '+680',
	'Wallis and Futuna' => '+681',
	'Cook Islands' => '+682',
	'Niue' => '+683',
	'Samoa' => '+685',
	'Kiribati' => '+686',
	'New Caledonia' => '+687',
	'Tuvalu' => '+688',
	'French Polynesia' => '+689',
	'Tokelau' => '+690',
	'Micronesia' => '+691',
	'Marshall Islands' => '+692',
	'North Korea' => '+850',
	'Hong Kong' => '+852',
	'Macau' => '+853',
	'Cambodia' => '+855',
	'Laos' => '+856',
	'Bangladesh' => '+880',
	'Taiwan' => '+886',
	'Maldives' => '+960',
	'Lebanon' => '+961',
	'Jordan' => '+962',
	'Syria' => '+963',
	'Iraq' => '+964',
	'Kuwait' => '+965',
	'Saudi Arabia' => '+966',
	'Yemen' => '+967',
	'Oman' => '+968',
	'Palestine' => '+970',
	'United Arab Emirates' => '+971',
	'Israel' => '+972',
	'Bahrain' => '+973',
	'Qatar' => '+974',
	'Bhutan' => '+975',
	'Mongolia' => '+976',
	'Nepal' => '+977',
	'Tajikistan' => '+992',
	'Turkmenistan' => '+993',
	'Azerbaijan' => '+994',
	'Georgia' => '+995',
	'Kyrgyzstan' => '+996',
	'Uzbekistan' => '+998',
	'Bahamas' => '+1242',
	'Barbados' => '+1246',
	'Anguilla' => '+1264',
	'Antigua and Barbuda' => '+1268',
	'British Virgin Islands' => '+1284',
	'U.S. Virgin Islands' => '+1340',
	'Cayman Islands' => '+1345',
	'Bermuda' => '+1441',
	'Grenada' => '+1473',
	'Turks and Caicos Islands' => '+1649',
	'Montserrat' => '+1664',
	'Northern Mariana Islands' => '+1670',
	'Guam' => '+1671',
	'American Samoa' => '+1684',
	'Sint Maarten' => '+1721',
	'Saint Lucia' => '+1758',
	'Dominica' => '+1767',
	'Saint Vincent and the Grenadines' => '+1784',
	'Puerto Rico' => '+1787',
	'Dominican Republic' => '+1809',
	'Dominican Republic' => '+1829',
	'Dominican Republic' => '+1851',
	'Trinidad and Tobago' => '+1868',
	'Saint Kitts and Nevis' => '+1869',
	'Jamaica' => '+1876',
	'Puerto Rico' => '+1940'
);
