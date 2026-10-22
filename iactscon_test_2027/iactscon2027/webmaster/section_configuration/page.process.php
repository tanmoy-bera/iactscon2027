<?php
	include_once('includes/init.php');
	include_once('../includes/backend.function.pages.php');
	
	$loggedUserID = $mycms->getLoggedUserId();
	switch($action)
	{		
		case'insert':	
			insertPageProces($cfg, $mycms);
			break;			
		case 'update':
			updatePageProces($cfg, $mycms);
			break;
		case'del':
			deletePageProcess($cfg, $mycms);
			break;		
		case 'getModule':
			getModuleDropDown($cfg, $mycms);
			break;
		case 'getSeq':
			pageSequence($cfg, $mycms);
			break;
		case 'isNamePresent':
			pageNamePresenceChecker($cfg, $mycms);
			break;
		case 'isFilePresent':
			pageFileNamePresenceChecker($cfg, $mycms);
			break;
	}
		
	/**************************************************************/
	/*                     WEB PAGE INSERT PROCESS                */
	/**************************************************************/
	function insertPageProces($cfg, $mycms){
		$loggedUserID = $mycms->getLoggedUserId();
		$pageName=addslashes($_REQUEST['pageName']);	
		$fileName=addslashes($_REQUEST['fileName']);
		$moduleId=$_REQUEST['moduleId'];	
		$seq=$_REQUEST['seq'];	
		
		$sql 	=	array();
		$sql['QUERY'] = "UPDATE "._DB_CONF_PAGE_." 
							SET seq 		= (seq+1), 
								modifiedBy  = ?
						  WHERE moduleId 	= ? 
						    AND seq 		>= ? ";
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $moduleId ,           'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $seq ,                'TYP' => 's');
		$mycms->sql_update($sql);	
		
		$sql 	=	array();
		$sql['QUERY']="INSERT INTO "._DB_CONF_PAGE_." (`moduleId`,`pageName`,`fileName`,`seq`,`modifiedBy`) 
							VALUES (?,?,?,?,?)";	
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $moduleId ,             'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'pageName' , 	  'DATA' => $pageName ,             'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'fileName' , 	  'DATA' => $fileName ,             'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $seq ,                  'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,         'TYP' => 's');					
		$mycms->sql_insert($sql);			
		$mycms->redirect('page.php?m=1');
		exit();
	}
	
	/**************************************************************/
	/*                     WEB PAGE UPDATE PROCESS                */
	/**************************************************************/
	function updatePageProces($cfg, $mycms){
		$loggedUserID = $mycms->getLoggedUserId();
		$pageId=trim($_REQUEST['id']);
		$pageName=addslashes($_REQUEST['pageName']);	
		$fileName=addslashes($_REQUEST['fileName']);
		$moduleId=addslashes($_REQUEST['moduleId']);
		$presentModule=trim($_REQUEST['presentModule']);	
		$seq=$_REQUEST['seq'];	
		$presentSeq=trim($_REQUEST['presentSeq']);
		
		$sql 	=	array();
		$sql['QUERY'] = "UPDATE "._DB_CONF_PAGE_." 
							SET seq = (seq-1), 
								modifiedBy = ?
						  WHERE moduleId = ?
						    AND seq > ? ";
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $presentModule ,      'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $presentSeq ,         'TYP' => 's');

		$mycms->sql_update($sql);
		
		$sql 	=	array();
		$sql['QUERY'] = "UPDATE "._DB_CONF_PAGE_." 
							SET seq 		= (seq+1), 
							    modifiedBy  = ? 
						  WHERE moduleId 	= ? 
						    AND seq 		>= ? ";

		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $moduleId ,           'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $seq ,                'TYP' => 's');
		$mycms->sql_update($sql);
		
		$sql 	=	array();
		$sql['QUERY'] =" UPDATE "._DB_CONF_PAGE_." 
							SET `moduleId`   = ?,
							    `pageName`   = ?,  
							    `fileName`   = ?, 
							    `seq` 	     = ?, 
							    `modifiedBy` = ?
			 			  WHERE `pageId`     = ? ";

		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $moduleId ,             'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'pageName' , 	  'DATA' => $pageName ,             'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'fileName' , 	  'DATA' => $fileName ,             'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	      'DATA' => $seq ,                  'TYP' => 's');	 			  
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  'DATA' => $loggedUserID ,         'TYP' => 's');	 			  
		$sql['PARAM'][]	=	array('FILD' => 'pageId' , 	      'DATA' => $pageId ,               'TYP' => 's');	 			  
		$mycms->sql_update($sql);	
		$mycms->redirect('page.php?m=2');
		exit();
	}
	
	/**************************************************************/
	/*                     WEB PAGE DELETE PROCESS                */
	/**************************************************************/
	function deletePageProcess($cfg, $mycms){
		$loggedUserID = $mycms->getLoggedUserId();
		$sql 	=	array();
		$sql="DELETE  FROM " ._DB_CONF_PAGE_." WHERE `pageId` = ".$_REQUEST['id']."";
		$mycms->sql_query($sql);
		$mycms->redirect('page.php?m=3');
		exit();
	}
	
	/**************************************************************/
	/*              MODULE DROP DOWN RETRIVING PROCESS            */
	/**************************************************************/
	function getModuleDropDown($cfg, $mycms){
		$loggedUserID = $mycms->getLoggedUserId();
		$sectionId=trim($_REQUEST['sectionId']);			
		$mycms->generateHTMLHeader();
		$sql 	=	array();
		$sql['QUERY'] =  "SELECT * 
							FROM "._DB_CONF_MODULE_."
						   WHERE `sectionId` = ? ";
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  'DATA' => $sectionId ,             'TYP' => 's');
		echo "<select name=\"moduleId\" id=\"moduleId\" class=\"forminputelement\" label=\"Module Name\" validate=\"IsSel()\" onchange=\"getSequence(this.form)\" >\n";
		echo "<option value=\"\">Select Module</option>\n";
		$result=$mycms->sql_select($sql);	 
		foreach($result as $i=>$row_mod){
			echo "<option value=\"".$row_mod['moduleId']."\">".$row_mod['moduleName']."</option>\n";			
		}
		echo "</select>\n";
		$mycms->flushAll();
		exit();
	}
	
	/**************************************************************/
	/*              WEB PAGE SEQUENCE RETRIVING PROCESS           */
	/**************************************************************/
	function pageSequence($cfg, $mycms){
		$loggedUserID = $mycms->getLoggedUserId();
		$pageId=trim($_REQUEST['id']);
		$moduleId=trim($_REQUEST['moduleId']);
		$max = (($_REQUEST['id'])?"IFNULL(MAX(seq),0)":"(IFNULL(MAX(seq),0)+1)");
		$sql 	=	array();
		$sql['QUERY'] =  "SELECT ".$max ." AS max 
							FROM "._DB_CONF_PAGE_."
						   WHERE `moduleId` = ? ";
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $moduleId ,             'TYP' => 's');
		$result=$mycms->sql_select($sql);	
		$row_cnt=$result[0]; 
		$mycms->generateHTMLHeader();
		echo "<select name=\"seq\" id=\"seq\" class=\"forminputelement\" label=\"Sequence\" validate=\"IsSel()\" >\n";
		// echo "<option value=\"\"></option>\n";
		for($i=1; $i <= $row_cnt['max']; $i++){
			echo "<option value=\"".$i."\">".$i."</option>\n";
		}
		echo "</select>\n";
		$mycms->flushAll();
		exit();
	}
	
	/**************************************************************/
	/*              WEB PAGE NAME AVAILABILITY PROCESS            */
	/**************************************************************/
	function pageNamePresenceChecker($cfg, $mycms){
		$loggedUserID = $mycms->getLoggedUserId();
		$pageId=trim($_REQUEST['id']);
		$pageName=addslashes($_REQUEST['pageName']);
		$moduleId=addslashes($_REQUEST['moduleId']);
		$sql 	=	array();
		$sql['QUERY'] =  "SELECT COUNT(*) AS count 
							FROM "._DB_CONF_PAGE_."
						   WHERE `moduleId` = ? 
						     AND `pageName` = ? ";
		
		$sql['QUERY'] .= (($_REQUEST['id'])?(" AND `pageId` <> '". $pageId ."'"):"");
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $moduleId ,             'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'pageName' , 	  'DATA' => $pageName ,             'TYP' => 's');
		$result=$mycms->sql_select($sql);	
		$row_stat=$result[0]; 
		$mycms->generateJSONHeader();
		echo "{ \n";
		echo "\"status\":".(($row_stat["count"]>0)?"\"1\"":"\"0\"");
		echo "}\n";
		$mycms->flushAll();
		exit();
	}
	
	/**************************************************************/
	/*            WEB PAGE FILE NAME AVAILABILITY PROCESS         */
	/**************************************************************/
	function pageFileNamePresenceChecker($cfg, $mycms){
		$loggedUserID = $mycms->getLoggedUserId();
		$pageId=trim($_REQUEST['id']);
		$fileName=addslashes($_REQUEST['fileName']);
		$moduleId=addslashes($_REQUEST['moduleId']);
		$sql 	=	array();
		$sql['QUERY'] 	=  "SELECT COUNT(*) AS count FROM "._DB_CONF_PAGE_." WHERE `moduleId` = ? AND `fileName` = ? ";
		$sql['QUERY']  .= (($_REQUEST['id'])?(" AND `pageId` <'". $pageId ."'>"):"");

		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $moduleId ,             'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'fileName' , 	  'DATA' => $fileName ,             'TYP' => 's');
		$result=$mycms->sql_select($sql);	
		$row_stat=$result[0]; 
		$mycms->generateJSONHeader();
		echo "{ \n";
		echo "\"status\":".(($row_stat["count"]>0)?"\"1\"":"\"0\"");
		echo "}\n";
		$mycms->flushAll();
		exit();
	}
?>
