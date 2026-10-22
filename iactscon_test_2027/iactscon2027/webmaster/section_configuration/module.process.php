<?php
	include_once('includes/init.php');
	include_once('../includes/backend.function.module.php');
	$loggedUserID = $mycms->getLoggedUserId();
	switch($action){
		/*============== INSERTING DATA IN MODULE TABLE =================*/	
		case'insert':	
			insertModuleProcess($cfg, $mycms);
			break;	
		/*============== UPDATE DATA IN MODULE TABLE =================*/	
		case 'update':
			updateModuleProcess($cfg, $mycms);
			break;
		/*================== MODULE NAME AVAILABILITY ===================*/	
		case 'isPresent':
			moduleNamePresenceChecker($cfg, $mycms);
			break;
		
		/*================== MODULE SEQUENCE ===================*/	
		case 'getSeq':
			moduleSequence($cfg, $mycms);
			break;
	}
		/**************************************************************/
	/*                      MODULE INSERT PROCESS                 */
	/**************************************************************/
	function insertModuleProcess($cfg, $mycms)
	{
		$loggedUserID = $mycms->getLoggedUserId();
		$sectionId    = trim($_REQUEST['sectionId']);
		$moduleName   = addslashes($_REQUEST['moduleName']);
		$seq          = trim($_REQUEST['seq']);
		$sql = array();
		$sql['QUERY'] =    "UPDATE "._DB_CONF_MODULE_." 
					           SET seq = (seq+1), 
							       modifiedBy = ? 
							 WHERE sectionId = ? 
							   AND seq >= ? ";
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	 	'DATA' => $loggedUserID , 	      'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  	'DATA' => $sectionId ,     		  'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	  		'DATA' => $seq ,   				  'TYP' => 's');
																				
		$mycms->sql_update($sql);

		$sql = array();
		$sql['QUERY'] = "INSERT INTO "._DB_CONF_MODULE_." 
			                SET `moduleName` = ?, 
							    `sectionId`  = ?, 
								`seq` 		 = ?, 
								`modifiedBy` = ?";	
		$sql['PARAM'][]	=	array('FILD' => 'moduleName' , 	  		'DATA' => $moduleName ,      		  'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	 		'DATA' => $sectionId , 	     		  'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	  			'DATA' => $seq ,     		          'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  		'DATA' => $loggedUserID ,   		  'TYP' => 's');						
		$mycms->sql_insert($sql);			
		$mycms->redirect('module.php?m=1');
	}
	
	/**************************************************************/
	/*                      MODULE UPDATE PROCESS                 */
	/**************************************************************/
	function updateModuleProcess($cfg, $mycms)
	{
		$loggedUserID   = $mycms->getLoggedUserId();
		$moduleId       = trim($_REQUEST['id']);
		$sectionId      = trim($_REQUEST['sectionId']);
		$presentSection = trim($_REQUEST['presentSection']);
		$moduleName     = addslashes($_REQUEST['moduleName']);
		$seq            = trim($_REQUEST['seq']);
		$presentSeq     = trim(($_REQUEST['presentSeq']=="")?'99999999':$_REQUEST['presentSeq']);
		$sql 	=	array();
		$sql['QUERY'] 	=    "UPDATE "._DB_CONF_MODULE_." 
					           SET `seq` = (seq-1), 
							       `modifiedBy` = ? 
							 WHERE `sectionId` = ? 
							   AND `seq` > ? ";
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	 	'DATA' =>$loggedUserID , 	 	  'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  	'DATA' => $presentSection ,       'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	  		'DATA' => $presentSeq ,   		  'TYP' => 's');						   
		$mycms->sql_update($sql);
		
		$sql 	=	array();
		$sql['QUERY']	=    "UPDATE "._DB_CONF_MODULE_." 
					           SET `seq` 		= (seq+1), 
							       `modifiedBy` = ? 
							 WHERE `sectionId`  = ? 
							   AND `seq`	   >= ? ";
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	 	'DATA' =>$loggedUserID , 	 	  'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  	'DATA' => $sectionId ,       	  'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	  		'DATA' => $seq ,   		          'TYP' => 's');						   
		$mycms->sql_update($sql);
		
		$sql 	=	array();
		$sql['QUERY']	=    "UPDATE "._DB_CONF_MODULE_." 
					           SET `sectionId`  = ?, 
							       `moduleName` = ?, 
								   `seq` 		= ?, 
								   `modifiedBy` = ?
						     WHERE `moduleId`   = ?";

		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  		'DATA' => $sectionId ,      		    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'moduleName' , 	 		'DATA' =>$moduleName , 	 	 			'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'seq' , 	  			'DATA' => $seq ,       	  				'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'modifiedBy' , 	  		'DATA' => $loggedUserID ,               'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  		'DATA' => $moduleId ,   		        'TYP' => 's');
		$mycms->sql_update($sql);
			
		$mycms->redirect('module.php?m=2');
	}
	
	/**************************************************************/
	/*             MODULE NAME AVAILABILITY CHECK PROCESS         */
	/**************************************************************/
	function moduleNamePresenceChecker($cfg, $mycms)
	{
		$loggedUserID   = $mycms->getLoggedUserId();
		$moduleId       = trim($_REQUEST['id']);
		$sectionId      = trim($_REQUEST['sectionId']);
		$moduleName     = trim($_REQUEST['moduleName']);
		
		$sql 	=	array();
		$sql['QUERY']  =  "SELECT COUNT(*) AS count 
				            FROM "._DB_CONF_MODULE_." 
						   WHERE `moduleName` = ? 
						     AND `sectionId` = ?";
		
		$sql['QUERY'] .= (($_REQUEST['id'])?(" AND `moduleId` != ?"):"");
		
		$sql['PARAM'][]	=	array('FILD' => 'moduleName' , 	  		'DATA' => $moduleName ,      		    'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	 		'DATA' =>$sectionId , 	 	 	 		'TYP' => 's');
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  		'DATA' => $moduleId ,                   'TYP' => 's');

		$result   = $mycms->sql_select($sql);	
		$row_stat = $result[0];
		 
		$mycms->generateJSONHeader();
		
		echo "{ \n";
		echo "\"status\":".(($row_stat["count"]>0)?"\"1\"":"\"0\"");
		echo "}\n";
		
		$mycms->flushAll();
	}
	
	/**************************************************************/
	/*               MODULE SEQUENCE RETRIVING PROCESS            */
	/**************************************************************/
	function moduleSequence($cfg, $mycms)
	{
		$loggedUserID   = $mycms->getLoggedUserId();
		$moduleId       = trim($_REQUEST['id']);
		$sectionId      = trim($_REQUEST['sectionId']);
		
		$max            = (($_REQUEST['id'])?"IFNULL(MAX(seq),0)":"(IFNULL(MAX(seq),0)+1)");
		
		$sql 	=	array();
		$sql['QUERY']   =  "SELECT ".$max ." AS max 
		                      FROM "._DB_CONF_MODULE_." 
							 WHERE `sectionId` = ? ";
		
		$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  		'DATA' => $sectionId ,      		    'TYP' => 's');
		$result         = $mycms->sql_select($sql);	
		$row_cnt        = $result[0]; 
		
		$mycms->generateHTMLHeader();
		
		echo "<select name=\"seq\" id=\"seq\" label=\"Sequence\" validate=\"IsSel()\" >\n";
		// echo "<option value=\"\">None</option>\n";
		for($i=1; $i <= $row_cnt['max']; $i++)
		{
			echo "<option value=\"".$i."\">".$i."</option>\n";
		}
		echo "</select>\n";
		$mycms->flushAll();
	}	
?>
