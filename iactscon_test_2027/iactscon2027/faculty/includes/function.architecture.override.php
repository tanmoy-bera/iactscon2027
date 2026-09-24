<?php
	function getFacultyAccessArray()
	{
		global $cfg, $mycms;	
		
		$ses=false;
		if($mycms->isSession($mycms->pageAccess_session))
		{
			$ses = $mycms->pageAccess_session;
			if($ses['DOMAIN'] === $cfg['DOMAIN_TAG'])
			{
				return $mycms->getSession($mycms->pageAccess_session);
			}
		} 
		
		if(!$ses || $ses['DOMAIN']!== $cfg['DOMAIN_TAG']) 
		{
			$mycms->resetDatabase();
			$accessArray  = array();
			$sectionArray = array();
						
			$sql['QUERY'] = "SELECT d.id AS domainId,
						   d.tag AS domainTag,
						   d.name AS domainName,
						   d.path AS domainPath,
						   d.img AS domainImg,
						   d.seqBy AS domainSeq,
						   d.scope AS domainScope,
						   
						   s.sectionId AS sectionId, 
						   s.sectionName AS sectionName, 
						   s.secCode AS secCode, 
						   s.seq AS sectionSeq, 
						   s.path AS sectionPath, 
						   s.img AS sectionImg,
						   
						   m.moduleId AS moduleId, 
						   m.moduleName AS moduleName, 
						   m.seq AS moduleSeq,
						   
						   p.pageId AS pageId, 
						   p.pageName AS pageName, 
						   p.fileName AS pageFileName,
						   p.seq AS pageSeq				
						   
					  FROM "._DB_CONF_PAGE_." p 
				
				INNER JOIN "._DB_CONF_MODULE_." m 
						ON p.moduleId = m.moduleId 
				
				INNER JOIN "._DB_CONF_SECTION_." s 
						ON m.sectionId = s.sectionId 
					   AND s.status != 'D'
						
				INNER JOIN "._DB_CONF_DOMAIN_." d 
						ON (s.domainId = d.id 
							OR s.domainId = 0)
					   AND d.tag = '".$cfg['DOMAIN_TAG']."'";	
			
			$sql['QUERY'] .= ($mycms->getLoggedUserId()==1)?"":(" AND p.pageId IN ( SELECT a.web_page_id  
																			 FROM "._DB_FACULTY_ACCESS_DETAILS_." a  
																			WHERE a.faculty_id = '".$mycms->getLoggedUserId()."' )");	
			$sql['QUERY'] .= "ORDER BY d.seqBy, s.seq, m.seq, p.seq";
			
			$result = $mycms->sql_select($sql);
			
			if($result)
			{
				$domainId 	  = '';
				$sectionId    = '';
				$moduleId     = '';
				
				foreach($result as $ind => $row_pg)
				{					
					if($row_pg["domainId"] != $domainId)
					{
						$domainId    = $row_pg["domainId"];
						$domainTag   = $row_pg["domainTag"];
						$domainName  = $row_pg["domainName"];
						$domainPath  = $row_pg["domainPath"];
						$domainImg   = $row_pg["domainImg"];
						$domainSeq   = $row_pg["domainSeq"];
						$domainScope = $row_pg["domainScope"];
						
						$accessArray[$domainId]['domainTag']    = $domainTag;
						$accessArray[$domainId]['domainName']   = $domainName;
						$accessArray[$domainId]['domainPath']   = $domainPath;
						$accessArray[$domainId]['domainImg']    = $domainImg;
						$accessArray[$domainId]['domainSeq']    = $domainSeq;
						$accessArray[$domainId]['domainScope']  = $domainScope;
						$accessArray[$domainId]['section']      = array();
					}
					
					if($row_pg["sectionId"] != $sectionId)
					{
						$sectionId    = $row_pg["sectionId"];
						$sectionName  = $row_pg["sectionName"];
						$secCode      = $row_pg["secCode"];
						$sectionSeq   = $row_pg["sectionSeq"];
						$sectionPath  = $row_pg["sectionPath"];
						$sectionImg   = $row_pg["sectionImg"];
						
						$accessArray[$domainId]['section'][$sectionId]['sectionName']  = $sectionName;
						$accessArray[$domainId]['section'][$sectionId]['secCode']      = $secCode;
						$accessArray[$domainId]['section'][$sectionId]['sectionSeq']   = $sectionSeq;
						$accessArray[$domainId]['section'][$sectionId]['sectionPath']  = $sectionPath;
						$accessArray[$domainId]['section'][$sectionId]['sectionImg']   = $sectionImg;
						$accessArray[$domainId]['section'][$sectionId]['module']       = array();
					}
					
					if($row_pg["moduleId"] != $moduleId)
					{
						$moduleId   = $row_pg["moduleId"];
						$moduleName = $row_pg["moduleName"];
						$moduleSeq  = $row_pg["moduleSeq"];
						
						$accessArray[$domainId]['section'][$sectionId]['module'][$moduleId]['moduleName'] = $moduleName;
						$accessArray[$domainId]['section'][$sectionId]['module'][$moduleId]['moduleSeq']  = $moduleSeq;
						$accessArray[$domainId]['section'][$sectionId]['module'][$moduleId]['page']       = array();
					}
					
					$pageId         = $row_pg["pageId"];
					
					$accessArray[$domainId]['section'][$sectionId]['module'][$moduleId]['page'][$pageId]['pageName']     = $row_pg["pageName"];
					$accessArray[$domainId]['section'][$sectionId]['module'][$moduleId]['page'][$pageId]['pageFileName'] = $row_pg["pageFileName"];
					$accessArray[$domainId]['section'][$sectionId]['module'][$moduleId]['page'][$pageId]['pageSeq']      = $row_pg["pageSeq"];
				}
			}
			$mycms->setSession($mycms->pageAccess_session, $accessArray);
			return $mycms->getSession($mycms->pageAccess_session);
		}
		else 
		{
			return $ses;
		}
	}
		
	function getFacultySectionArray()
	{
		global $cfg, $mycms;	
		
		$accessArray       = getFacultyAccessArray();
		$currentDomain     = $cfg['DOMAIN_TAG'];
		$domainId          = getDomainId($currentDomain);
		
		$domainAccessDet   = $accessArray[$domainId]['section'];
		
		$sectionArray      = array();
		
		foreach($domainAccessDet  as $sectionId => $sectionDetails)
		{
			$secCode                                  = $sectionDetails['secCode'];
			$sectionArray[$secCode]['sectionTag']     = $sectionDetails['secCode'];
			$sectionArray[$secCode]['sectionName']    = $sectionDetails['sectionName'];
			$sectionArray[$secCode]['sectionPath']    = $sectionDetails['sectionPath'];
			$sectionArray[$secCode]['sectionImg']     = $sectionDetails['sectionImg'];
			$sectionArray[$secCode]['module']         = $sectionDetails['module'];
		}
		return $sectionArray;	
	}
	
	/********************************************************************/
	/*                      FACULTY LOGIN PROCESS                       */
	/********************************************************************/
	function faculty_login_process()
	{
		global $cfg, $mycms;
		
		$user_name        	= $_REQUEST['username'];
		$faculty_password         = $_REQUEST['password'];
		$user_pass             = $mycms->encoded($faculty_password);
		$user_type         	= $_REQUEST['user_type'];
		
		$sqlFetchFaculty['QUERY'] 	= "SELECT * FROM "._DB_FACULTY_ACCOUNT_." 
							           WHERE `faculty_login_username` = '".$user_name."' 
								         AND `faculty_login_password` = '".$user_pass."'"; 
	
		$resultFaculty 		= $mycms->sql_select($sqlFetchFaculty);	
		
		if($resultFaculty)
		{
			$rowFaculty     = $resultFaculty[0];
			
			if($rowFaculty["status"]=='A')
			{
				$mycms->login($rowFaculty["id"], $rowFaculty["faculty_login_username"], $user_type);	
				$mycms->redirect("../section_abstract/admin.php?cat=1");	
			} 
			else 
			{
				$msg        = "Account Disabled, Please Contact To Administrator.";
				$mycms->redirect("login.php?msg=".$msg);
			}	
			
			
		} 
		else
		{
			$msg = "Invalid User Access";
			$mycms->redirect("login.php?msg=".$msg);
		} 
	}
?>