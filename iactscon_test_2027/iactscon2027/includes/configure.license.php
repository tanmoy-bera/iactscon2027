<?php
// APPLICATION LICENCE DETAILS

$cfg['APP_URL']		                				= 'weavers-web.com';
$cfg['VERSION']			            				= 'ver. 0.1 beta';
$cfg['MGMT_USAGE_COUNT']            				= 10; 	 
$cfg['EMP_USAGE_COUNT']	            				= 10; 	 
$cfg['LICENSE_TO']		            				= '';
$cfg['LICENSE_TILL']	            				= '';
$cfg['SERVICE_TAG']		            				= 'RCOG';
$cfg['SYSTEM_NOTIFICATION']         				= '';
$cfg['COPYRIGHT']                   				= '&copy;&nbsp;AICC RCOG&nbsp;&nbsp;&reg;&nbsp;'.date('Y').'-'.(date('Y')+1).'. All Rights Reserved';
$cfg['SHOW_COPYRIGHT']              				= 0;   

//$cfg['ATOM_REQUEST_URL']		    				= "https://payment.atomtech.in/paynetz/epi/fts";
//$cfg['ATOM_LOGIN_ID']		        				= "77854";
//$cfg['ATOM_PASSWORD']		        				= "7aad9695";//"User@123";
//$cfg['ATOM_PRODUCT_ID']		        				= "EASTERN";
//$cfg['ATOM_REQ_HASH']                               = "5aa055cd11b0dccba2";
//$cfg['ATOM_RESP_HASH']		        			    = "83c67e5ba7240279b3";

// taken from neocon2022 live 11.07.2022----
$cfg['ATOM_REQUEST_URL']	    					= "https://payment.atomtech.in/paynetz/epi/fts"; 
$cfg['ATOM_LOGIN_ID']		       					= "1442";
$cfg['ATOM_PASSWORD']		       					= "RUEDA@123";
$cfg['ATOM_PRODUCT_ID']	       						= "RUEDA";
$cfg['ATOM_REQ_HASH']		        			    = "2f403cf17e44d6e730";
$cfg['ATOM_RESP_HASH']		        			    = "7b3ab91ce3adcde262";
// taken from neocon2022 live 11.07.2022----

$cfg['TEST.ATOM_REQUEST_URL']	    				= "https://paynetzuat.atomtech.in/paynetz/epi/fts"; 
$cfg['TEST.ATOM_LOGIN_ID']		       				= "197";
$cfg['TEST.ATOM_PASSWORD']		       				= "Test@123";
$cfg['TEST.ATOM_PRODUCT_ID']	       				= "NSE";
$cfg['TEST.ATOM_REQ_HASH']                          = "KEY123657234";
$cfg['TEST.ATOM_RESP_HASH']		        			= "KEYRESP123657234";

$cfg['INT_ATOM_REQUEST_URL']		    			= "";//"https://payment.atomtech.in/paynetz/epi/fts";
$cfg['INT_ATOM_LOGIN_ID']		        			= "";//"28877";//"19370";
$cfg['INT_ATOM_PASSWORD']		        			= "";//"Rueda@123";
$cfg['INT_ATOM_PRODUCT_ID']		        			= "";//"IAACON_2017";//"RUEDA";

$cfg['WL_REQUEST_URL']		    			        = "https://www.tpsl-india.in/PaymentGateway/services/TransactionDetailsNew"; //"https://ipg.in.worldline.com/doMEPayRequest";
$cfg['WL_MERCHANT_ID']		    				    = "WL0000000037161";//"WL0000000006920";
$cfg['WL_ENC_KEY']		        				    = "9141226511CFQFQY"; //  6ef0f8471ab9868e741c6cb19842c930

$cfg['TEST.WL_REQUEST_URL']		    			    = "https://cgt.in.worldline-solutions.com/ipg/doMEPayRequest";
$cfg['TEST.WL_MERCHANT_ID']		    				= "WL0000000027698";
$cfg['TEST.WL_ENC_KEY']		        				= "9141226511CFQFQY";//"6375b97b954b37f956966977e5753ee6";


$cfg['TEST.WL_NEW_REQUEST_URL']		    			= "https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl"; 
$cfg['TEST.WL_NEW_MERCHANT_CODE']		    	    = "L951330"; //"T951330";//"WL0000000006920";
$cfg['TEST.WL_NEW_ENC_KEY']		        		    = "9141226511CFQFQY";
$cfg['TEST.WL_NEW_IV_KEY']		        		    = "5680421606ESDMEP";
$cfg['TEST.WL_NEW_SCHEME_CODE']		        		= "first";

$cfg['WL_NEW_REQUEST_URL']		    			= "https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl"; 
$cfg['WL_NEW_MERCHANT_CODE']		    	    = "L951330"; //"T951330";//"WL0000000006920";
$cfg['WL_NEW_ENC_KEY']		        		    = "9141226511CFQFQY";
$cfg['WL_NEW_IV_KEY']		        		    = "5680421606ESDMEP";
$cfg['WL_NEW_SCHEME_CODE']		        		= "FIRST";



// ARGOS SMS GATEWAY
$cfg['ARGOS_USERNAME']		        				= "isncon2014"; 
$cfg['ARGOS_PASSWORD']		        				= "4kzLpmS5R4"; 
$cfg['ARGOS_SENDERID']	            				= "RCOGKL";

// THEME 
$cfg['THEME']                       				= 'skyCanvas';

// CONTACT DETAILS
$cfg['ADMIN_CONTACT']		        				= "+91 9433351280"; 

// LIFE CYCLE STATE
$cfg['LIFE_CYCLE']	                				= "LIVE";

// CROWD PDF API DETAILS
$cfg['CROWD.PDF.USERNAME']		        			= "flynewsletter"; 
$cfg['CROWD.PDF.API.KEY']		        			= "509c38ff7f042c440a9e7c2c98f07c13"; 

$cfg['SENDGRID_URL']	                	= "https://api.sendgrid.com/";
$cfg['SENDGRID_USERNAME']	               	= "production@ruedakolkata.com"; //"flynewsletter@gmail.com";
$cfg['SENDGRID_PASSWORD']	                = "dirgGDnes-ultra-secreto12"; //"flynewsletter@2Gmail123456";

$cfg['RAZORPAY_KEY_ID'] = 'rzp_live_S3SGlJZDdvd5Xe';
$cfg['RAZORPAY_SECRET'] = 'JlrZh28sXpYnpOR0C97r7btS';

          //////////////for test////////////
// $cfg['RAZORPAY_KEY_ID'] = 'rzp_test_SFffDin9pNz04S';
// $cfg['RAZORPAY_SECRET'] = 's4H0hc0b8Plsnwm1RjN5aPZw';
          ////////////////////////////////
?>