<?php
	include_once('includes/init.php');
	include_once('../../includes/function.section.php');
	
	$loggedUserID = $mycms->getLoggedUserId();
	
	switch($action)
	{
		/******************************************************************/
		/*                    SEARCH SECTION OPERATION                    */
		/******************************************************************/
		case'search':
			
			pageRedirection(5, "");
			exit();
			break;
		
		/******************************************************************/
		/*                    SECTION NAME AVAILABILITY                   */
		/******************************************************************/
		case'isPresent':
			
			sectionNamePresenceChecker($cfg, $mycms);
			exit();
			break;
			
		/******************************************************************/
		/*                    INSERT SECTION OPERATION                    */
		/******************************************************************/
		case'insert':
			
			insertSectionProcess($cfg, $mycms);
			exit();		
			break;
			
		/******************************************************************/
		/*                    UPDATE SECTION OPERATION                    */
		/******************************************************************/
		case'update':
			
			updateSectionProcess($cfg, $mycms);
			exit();	
			break;
	}
		
	/**************************************************************/
	/*                    INSERT SECTION PROCESS                  */
	/**************************************************************/
	function insertSectionProcess($cfg, $mycms)
	{
		$loggedUserID       = $mycms->getLoggedUserId();
		$secCode            = addslashes('#'.$_REQUEST['secCode']);
		$sectionName        = addslashes($_REQUEST['sectionName']);
		$seq                = addslashes($_REQUEST['seq']);
		$path               = addslashes($_REQUEST['path']);
		$img                = addslashes($_REQUEST['img']);
		
		$sql 		=	array();
		$sql['QUERY'] = "UPDATE "._DB_CONF_SECTION_." 
						   SET seq = (seq+1), 
							   modifiedBy = ? 
						 WHERE seq >= ? ";
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $seq ,         		  'TYP' => 's');				 
		$mycms->sql_update($sql);	
		
		$sql 		=	array();
		$sql['QUERY'] = "INSERT INTO "._DB_CONF_SECTION_." 
							SET `domainId`= ? ,
								`secCode`= ?,
								`sectionName`= ?,
								`seq`= ?,
								`path`= ? ,
								`img`= ? ,
								`modifiedBy`= ?";	
		$sql['PARAM'][]	=	array('FILD' => 'domainId' , 	  'DATA' =>getDomainId() ,       	'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'secCode' , 	  'DATA' => $secCode ,       	    'TYP' => 's');						
		$sql['PARAM'][]	=	array('FILD' => 'sectionName' ,   'DATA' => $sectionName ,  		'TYP' => 's');						
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $seq ,         			'TYP' => 's');						
		$sql['PARAM'][]	=	array('FILD' => 'path' , 	      'DATA' => $path ,        			'TYP' => 's');						
		$sql['PARAM'][]	=	array('FILD' => 'img' , 	      'DATA' => $img ,         			'TYP' => 's');						
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,         'TYP' => 's');							
		$mycms->sql_insert($sql);
		pageRedirection(1, "");
	 }
	
	/**************************************************************/
	/*                    UPDATE SECTION PROCESS                  */
	/**************************************************************/ 
	function updateSectionProcess($cfg, $mycms)
	{
		$loggedUserID = $mycms->getLoggedUserId();
		$sectionId    = trim($_REQUEST['id']);
		$sectionName  = addslashes($_REQUEST['sectionName']);
		$presentSeq   = addslashes($_REQUEST['presentSeq']);
		$seq          = addslashes($_REQUEST['seq']);
		$path         = addslashes($_REQUEST['path']);
		$img          = addslashes($_REQUEST['img']);
		
		$sql 		=	array();
		$sql['QUERY'] = "UPDATE "._DB_CONF_SECTION_." 
						   SET seq 		  = (seq-1), 
							   modifiedBy = ? 
						 WHERE seq 		  > ? ";
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $presentSeq ,         'TYP' => 's');				
		$mycms->sql_update($sql);
		
		$sql 		=	array();
		$sql['QUERY'] = "UPDATE "._DB_CONF_SECTION_." 
						   SET seq 		  = (seq+1), 
							   modifiedBy = ? 
						 WHERE seq       >= ? ";
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $seq ,         		  'TYP' => 's');						 
		$mycms->sql_update($sql);
		
		$sql 		=	array();
		$sql['QUERY'] = "UPDATE "._DB_CONF_SECTION_." 
							SET `sectionName` = ?,
							   `seq` 		  = ?,
							   `path` 		  = ?,
							   `img`		  = ?,
							   `modifiedBy`	  = ? 
						 WHERE `sectionId` 	  = ?";

		$sql['PARAM'][]	=	array('FILD' => 'sectionName' ,   'DATA' => $sectionName ,            'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	  	  'DATA' => $seq ,       			  'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'path' , 	      'DATA' => $path ,         		  'TYP' => 's');	
		$sql['PARAM'][]	=	array('FILD' => 'img' , 	  	  'DATA' => $img ,                    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,           'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  'DATA' => $sectionId ,         	  'TYP' => 's');					 
		$mycms->sql_update($sql);
		pageRedirection(2, "");
	 }
	
	/**************************************************************/
	/*               SECTION NAME AVAILABILITY PROCESS            */
	/**************************************************************/ 
	function sectionNamePresenceChecker($cfg, $mycms)
	{
	 	$loggedUserID = $mycms->getLoggedUserId();
		$sectionId    = trim($_REQUEST['id']);
		$sectionName  = trim($_REQUEST['sectionName']);
		
		$sql 		=	array();
		$sql['QUERY']    = "SELECT COUNT(*) AS count 
						     FROM "._DB_CONF_SECTION_." 
						    WHERE `sectionName` = ? ";
		$sql['QUERY']   .= (($_REQUEST['id'])?(" AND `sectionId` != ? "):"");

		$sql['PARAM'][]	=	array('FILD' => 'sectionName' ,   	  'DATA' => $sectionName ,                'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  	  'DATA' => $sectionId ,       			  'TYP' => 's');
		$result = $mycms->sql_select($sql);	
		$rowFetchSection_stat = $result[0]; 
		
		$mycms->generateJSONHeader();
		
		echo "{ \n";
		echo "\"status\":".(($rowFetchSection_stat["count"]>0)?"\"1\"":"\"0\"");
		echo "}\n";
		$mycms->flushAll();
	 }
	 
	function pageRedirection($messageCode, $additionalString="")
	{
		global $mycms, $cfg;
		
		$pageKey                       		     = "_pgn_";
		$pageKeyVal                    		     = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
		
		@$searchString                 	  	     = "";
		$searchArray                   		     = array();
		
		$searchArray[$pageKey]         		     = $pageKeyVal;
		$searchArray['src_filter_text']          = trim($_REQUEST['src_filter_text']);
		$searchArray['src_filter_selection']     = trim($_REQUEST['src_filter_selection']);
		
		foreach($searchArray as $searchKey=>$searchVal)
		{
			if($searchVal!="")
			{
				$searchString .= "&".$searchKey."=".$searchVal;
			}
		}
		
		$mycms->redirect("section.php?m=".$messageCode.$searchString.$additionalString);
	}
?>

