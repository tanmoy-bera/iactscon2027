<?php
	include_once('includes/init.php');
	
	$act=@$_REQUEST['act']; 
	switch($act){
	
		/*================ COUNTRY AVAILABILITY ====================*/
		case'countryAvailability':
			
			$getVal = addslashes($_REQUEST['getVal']);
			$sql 	=	array();
			$sql['QUERY']    = "SELECT * FROM "._DB_EMAIL_TEMPLATE_." 
									    WHERE `country_name`=? 
									      AND `status`!= ?"; 
			 $sql['PARAM'][]	=	array('FILD' => 'country_name' ,    	 'DATA' => $getVal ,            'TYP' => 's');			
			 $sql['PARAM'][]	=	array('FILD' => 'status' ,    			'DATA' => 'D' ,         		'TYP' => 's');				
			$res    		 = $mycms->sql_select($sql);
			$maxrow 		 = $mycms->sql_numrows($res);
			
			if($maxrow > 0)
			{
				echo 1;
			}
			else
			{
				echo 0;
			}	
			exit();
			break;
	
		/*==================== SEARCH COUNTRY =======================*/
		case'search_country':
			pageRedirection('manage_conference_mail.php',5);	
			exit();
			break;
		
		/*================= ACTIVE COUNTRY ==================*/
		case'Active':
			
			$sql 	=	array();
			$sql['QUERY'] = "UPDATE "._DB_EMAIL_TEMPLATE_." 
							   SET `status` = ? 
							 WHERE `id` = ?"; 
			 $sql['PARAM'][]	=	array('FILD' => 'status' ,    	 'DATA' => 'A' ,           					 'TYP' => 's');		
			 $sql['PARAM'][]	=	array('FILD' => 'id' ,    	 'DATA' => $_REQUEST['id'] ,            'TYP' => 's');	 
			$mycms->sql_update($sql);
			$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Status updated successfully!' // dynamic message
			];


			if(!empty($_SERVER['HTTP_REFERER'])){
				echo '<script>window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
			} else {
				echo '<script>window.location.href="default_page.php";</script>';
			}			
			exit();
			break;
		
		/*================ INACTIVE COUNTRY =================*/
		case'Inactive':
			
			$sql 	=	array();
			$sql['QUERY'] = "UPDATE "._DB_EMAIL_TEMPLATE_." 
							   SET `status` = ? 
							 WHERE `id` = ?"; 
			 $sql['PARAM'][]	=	array('FILD' => 'status' ,    	 'DATA' => 'I' ,           					 'TYP' => 's');		
			 $sql['PARAM'][]	=	array('FILD' => 'id' ,    	 'DATA' => $_REQUEST['id'] ,            'TYP' => 's');	
			 $mycms->sql_update($sql);
			 $mycms->sql_update($sql);
				$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Status updated successfully!' // dynamic message
			];


			if(!empty($_SERVER['HTTP_REFERER'])){
				echo '<script>window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
			} else {
				echo '<script>window.location.href="default_page.php";</script>';
			}		
			exit();
			break;
			
		/*================= REMOVE COUNTRY ==================*/
		case'Remove':
			
			$sql 	=	array();
			$sql['QUERY'] = "DELETE FROM  "._DB_EMAIL_TEMPLATE_." 
							   
							 WHERE `id` = ?"; 
					
			 $sql['PARAM'][]	=	array('FILD' => 'id' ,    	 'DATA' => $_REQUEST['id'] ,            'TYP' => 's');	
			 $con = $mycms->sql_delete($sql);
			 
			$mycms->sql_update($sql);
			$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Status updated successfully!' // dynamic message
			];


			if(!empty($_SERVER['HTTP_REFERER'])){
				echo '<script>window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
			} else {
				echo '<script>window.location.href="default_page.php";</script>';
			}	
			exit();
			break;
	
		/*================= ADD COUNTRY ============================*/
		case'add_template':
			
			global $mycms, $cfg;

			
			$sql 	=	array();
			
			$sql['QUERY'] = "INSERT INTO "._DB_EMAIL_TEMPLATE_." 
									SET 
									`title`=?,
									`subject`=?,
									`description`=?,
									`created_dateTime`=?

									";

			$sql['PARAM'][]		  =	array('FILD' => 'title',    	 'DATA' => $_REQUEST['mail_name'] , 		 'TYP' => 's');
			$sql['PARAM'][]		  =	array('FILD' => 'subject',    	 'DATA' => $_REQUEST['mail_subject'] , 		 'TYP' => 's');								
			$sql['PARAM'][]		  =	array('FILD' => 'description' ,    	 'DATA' => $_REQUEST['mail_description'] , 		 'TYP' => 's');	
			$sql['PARAM'][]		  =	array('FILD' => 'created_dateTime' ,    	 'DATA' => date('Y-m-d H:i:s') , 		 'TYP' => 's');		
			 	
			$lastInsertId = $mycms->sql_insert($sql);

			
			
			$mycms->sql_update($sql);
			$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Data Added successfully!' // dynamic message
			];


			if(!empty($_SERVER['HTTP_REFERER'])){
				echo '<script>window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
			} else {
				echo '<script>window.location.href="default_page.php";</script>';
			}	
			exit();
			break;
		
		/*================= EDIT COUNTRY ===========================*/
		case'edit_template':
		
		    $id = $_REQUEST['id'];

		    global $mycms, $cfg;

		    //echo $_REQUEST['mail_description']; die;


		    $encodedContent = htmlspecialchars($_REQUEST['mail_description'], ENT_QUOTES | ENT_HTML5, 'UTF-8');	
			
			$sql 	=	array();
			
			$sql['QUERY'] = "UPDATE "._DB_EMAIL_TEMPLATE_." 
									SET 
									`title`=?,
									`subject`=?,
									`description`=?
									
									WHERE `id` = ?
									";

			$sql['PARAM'][]		  =	array('FILD' => 'title',    	 'DATA' => $_REQUEST['mail_name'] , 		 'TYP' => 's');	
			$sql['PARAM'][]		  =	array('FILD' => 'subject',    	 'DATA' => $_REQUEST['mail_subject'] , 		 'TYP' => 's');						
			$sql['PARAM'][]		  =	array('FILD' => 'description' ,    	 'DATA' => $encodedContent , 		 'TYP' => 's');	
			$sql['PARAM'][]		  =	array('FILD' => 'id' ,    	 'DATA' => $id , 		 'TYP' => 's');	

			$mycms->sql_update($sql);
		   
			$mycms->sql_update($sql);
			$_SESSION['toaster'] = [ 
				'type' => 'success', // 'success' or 'error'
				'message' => 'Data Updated successfully!' // dynamic message
			];


			if(!empty($_SERVER['HTTP_REFERER'])){
				echo '<script>window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
			} else {
				echo '<script>window.location.href="default_page.php";</script>';
			}	
			exit();
			break;
	}
	
	/*========== UTILITY METHOD ===============*/
	function pageRedirection($fileName,$messageCode,$additionalString="")
	{
		global $mycms, $cfg;
		 
		 $pageKey = "_pgn_";
		 
		 $pageKeyVal = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	 
		@$searchString = "";
		 $searchArray  = array();
	  
		 $searchArray[$pageKey]                 = $pageKeyVal;
		 $searchArray['src_country_name']       = trim($_REQUEST['src_country_name']);
		  
		 foreach($searchArray as $searchKey=>$searchVal)
		 {
			 $searchString .= "&".$searchKey."=".$searchVal;
		 }
		 
		 $mycms->redirect($fileName."?m=".$messageCode.$additionalString.$searchString);
	}

	

	
?>
