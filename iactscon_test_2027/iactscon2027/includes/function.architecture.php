<?php
	function startsWith($haystack, $needle){
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	
	function endsWith($haystack, $needle){
		$length = strlen($needle);
		$start  = $length * -1; //negative
		return (substr($haystack, $start) === $needle);
	}
	
	function hasRecords($array){
		foreach($array as $key => $value){
			if($value > 0) return true;
		}
	}
		
	function isEven ($num){
	   return !($num % 2);
	} 
	
	function number_pad($number,$places) {
		return str_pad((int) $number, $places, "0", STR_PAD_LEFT);
	}
	
	function getProcessTime(){
		global $mycms, $processStartTime; 
		return $mycms->microtime_float() - $processStartTime;
	}
	
	function escapeHTMLSpecialCharacter($string){
		return preg_replace('/[^(\x20-\x7f)]*/s', '', $string);
	}
		
	function getHTMLized($text){
	    if($_SERVER['HTTP_HOST'] == 'localhost')
		{
			return htmlentities($text);
		}
		else
		{
			return mb_convert_encoding($text, "UTF-8", "HTML-ENTITIES"); 
		}
	}
	
	function convert_number($number,$format='IN') { 
		if (($number < 0) || ($number > 999999999)) { 
			throw new Exception("Number is out of range");
		} 
	
		switch($format){
			case 'US':
				$Gn = floor($number / 1000000);  /* Millions (giga) */ 
				$number -= $Gn * 1000000; 
				$GnName = " Million";
				$kn = floor($number / 1000);     /* Thousands (kilo) */ 
				$number -= $kn * 1000; 
				$knName = " Thousand";
				$Hn = floor($number / 100);      /* Hundreds (hecto) */ 
				$number -= $Hn * 100; 
				$HnName = " Hundred";
				$Dn = floor($number / 10);       /* Tens (deca) */ 
				$n = $number % 10;               /* Ones */ 
				break;
			case 'IN':
			default :
				$Cn = floor($number / 10000000);  /* Lacs  */ 
				$number -= $Cn * 10000000; 
				$CnName = " Cores";			
				$Gn = floor($number / 100000);  /* Lacs  */ 
				$number -= $Gn * 100000; 
				$GnName = " Lacs";
				$kn = floor($number / 1000);     /* Thousands */ 
				$number -= $kn * 1000; 
				$knName = " Thousand";
				$Hn = floor($number / 100);      /* Hundreds  */ 
				$number -= $Hn * 100; 
				$HnName = " Hundred";
				$Dn = floor($number / 10);       /* Tens */ 
				$n = $number % 10;               /* Ones */ 
				break;
		}
	
		$res = ""; 
	
		if ($Cn){ 
			$res .= (empty($res) ? "" : " ") . convert_number($Cn) . $CnName; 
		} 
		
		if ($Gn){ 
			$res .= (empty($res) ? "" : " ") . convert_number($Gn) . $GnName; 
		} 
	
		if ($kn) { 
			$res .= (empty($res) ? "" : " ") . convert_number($kn) . $knName; 
		} 
	
		if ($Hn) { 
			$res .= (empty($res) ? "" : " ") . convert_number($Hn) . $HnName; 
		} 
	
		$ones = array("", "One", "Two", "Three", "Four", 
					  "Five", "Six", "Seven", "Eight", "Nine", 
					  "Ten", "Eleven", "Twelve", "Thirteen","Fourteen", 
					  "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"); 
	
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety"); 
	
	
		if ($Dn || $n) { 
			if (!empty($res)) { 
				$res .= " and "; 
			} 
			
			if ($Dn < 2) { 
				$res .= $ones[$Dn * 10 + $n]; 
			} else { 
				$res .= $tens[$Dn]; 
				if ($n) { 
					$res .= "-" . $ones[$n]; 
				} 
			} 
		} 
	
		if (empty($res)) { 
			$res = "zero"; 
		} 
		
		return $res; 
	}
	
	function getMonth($id){
		$month=array();
		$month['1']='January';
		$month['2']='February';
		$month['3']='March';
		$month['4']='April';
		$month['5']='May';
		$month['6']='June';
		$month['7']='July';
		$month['8']='August';
		$month['9']='September';
		$month['10']='October';
		$month['11']='November';
		$month['12']='December';
		return $month[$id];
	}
	
	function formatMobileNo($no)
	{
		if(  preg_match( '/^\+\d(\d{4})(\d{3})(\d{3})$/', $no,  $matches ) )
		{
			$result = $matches[1] . ' ' .$matches[2] . ' ' . $matches[3];
			return $result;
		}
		else
		{
			return $no;
		}
	}
	
/////////////////////////////////////////////////////////////////////////////////////
// ACCESS RELATED FUNCTIONS
/////////////////////////////////////////////////////////////////////////////////////
	
	function getDomainId($tag='')
	{
		global  $mycms,$cfg;
		
		if($tag==='') 
		{
		    $tag = $cfg['DOMAIN_TAG'];
		}
		
		@$domainId = 0;
		
		$sql['QUERY'] = "SELECT * FROM "._DB_CONF_DOMAIN_." 
						WHERE `tag`='".$tag."'";
		
		$result = $mycms->sql_select($sql);
		if($result)
		{
			$row = $result[0];
			$domainId = $row['id'];
		}
		return $domainId;
	}
	
	function getDomainName($tag) 
	{
		global $cfg, $mycms;
		
		$sqlSelectDomain['QUERY'] = "SELECT * FROM "._DB_CONF_DOMAIN_." 
		                            WHERE `tag` = '".$tag."'";	
		$resultDomain    = $mycms->sql_select($sqlSelectDomain);
		$rowDomain       = $resultDomain[0]['name'];
		
		return $rowDomain;
	}
	
	function getDomainUrl($tag) 
	{
		global $cfg, $mycms;
		
		$sqlSelectDomainPath['QUERY'] = "SELECT * FROM "._DB_CONF_DOMAIN_." 
		                                	WHERE `tag` = '".$tag."'";	
		$resultDomainPath   		  = $mycms->sql_select($sqlSelectDomainPath);
		$rowDomainPath      		  = $resultDomainPath[0]['path'];
		
		return $rowDomainPath;
	}
	
	function getAccessArray()
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
			
			$sql['QUERY'] .= ($mycms->getLoggedUserId()==1)?"":(" AND p.pageId IN (SELECT a.pageId 
																			 FROM "._DB_CONF_PAGE_ACCESS_." a  
																			WHERE a.userId = '".$mycms->getLoggedUserId()."' )");	
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
					
					$sectionId      = $row_pg["sectionId"];
					$moduleId       = $row_pg["moduleId"];
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
		
	function getSectionArray()
	{
		global $cfg, $mycms;	
		
		$accessArray     = getAccessArray();
		$currentDomain   = $cfg['DOMAIN_TAG'];
		$domainId        = getDomainId($currentDomain);
	
		$domainAccessDet = $accessArray[$domainId]['section'];
		$sectionArray    = array();
		
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
	
	function getSectionName($sectionCode)
	{
		global $cfg,$mycms;	
		if(startsWith($sectionCode,'#')){
			$sectionArray = getSectionArray();
			return $sectionArray[$sectionCode]['name'];
		} else {
			$mycms->kill('improper section code `'.$sec.'`');
		}
	}
	
	function isSectionAccessible($sectionCode)
	{
		global $cfg,$mycms;	
		if($mycms->getLoggedUserId()==1) return true;
		
		if(startsWith($section,'#')){
			$sectionArray = getSectionArray(); 
			if(array_key_exists($sectionCode)){
				return true;
			} else {
				return false;
			}			
		} else {
			$mycms->kill('improper section code `'.$sec.'`');
		}
	}
	
	function getAccess($sectionCode)
	{
		global $cfg,$mycms;
		
		if(startsWith($sectionCode,'#'))
		{		
			$sectionArray = getSectionArray();		
			return $sectionArray[$sectionCode];
		} 
		else 
		{
			$mycms->kill('improper section code `'.$sectionCode.'`');
		}
	}
	
	function hasAccess()
	{
		global $cfg, $mycms, $cfgEscape;
		
		$page         = basename($_SERVER['PHP_SELF']);
		$pageExploded = explode(".", $page);		
		$firstPart    = $pageExploded[0].'.';
		foreach($cfgEscape as $keyEscape=>$valEscape)
		{
			if(startsWith($page, $valEscape) || startsWith($page, $firstPart))
			{
				return true;
			}
		}
		$sectionArray = getSectionArray();		
		foreach($sectionArray as $secCode=>$sectionDetails)
		{
			$moduleArray = $sectionDetails['module'];
			foreach($moduleArray as $moduleId => $moduleDetails)
			{
				$pageArray =  $moduleDetails['page'];
				foreach($pageArray as $pageId => $pageDetails)
				{
				    $pageName = $pageDetails['pageFileName']; 
					if(startsWith($pageName, $page) || startsWith($pageName, $firstPart))
					{
						return true;
					}
				}
			}
		}
		return false;
	}
	
	function accessValidation()
	{
		global  $mycms,$cfg;
		
		if($mycms->getPageName()!="login.php")
		{
			 $login = $mycms->isLogin();	
			if($login===true) 
			{
				
				$mycms->accessArray = getAccessArray();
				if(!hasAccess())
				{
					$mycms->redirect("notelligible.php");	
					exit();
				}
			} 
			else 
			{
				
				$mycms->redirect($cfg['DOMAIN_URL']."section_login/login.php".(($login==='F')?'?act='.md5('logout').'&m=You have been forced out of the system':'?m='.$login));	

				// $mycms->redirect("../section_login/login.php".(($login==='F')?'?act='.md5('logout').'&m=You have been forced out of the system':'?m='.$login));	
				exit();
			}
		}
	}

	/******************************************************************/
	/*                  LOGIN RELATED FUNCTIONS                       */
	/******************************************************************/
	function admin_loginScreen($cfg, $mycms, $typ)
	{
		global $msg;
		page_header("Login");
	?>
		<div class="lgn_bdy">
			<h3>Admin Login</h3>
			<p>Please Type Your Credentials for authentication.</p>
			<div class="lgn_area">
				<FORM name="frm_login" onsubmit="return FormValidator.validate(this);" action="login.php" method="post"> 
					<INPUT id="act" type="hidden" value='<?=md5("login")?>' name="act">
					<INPUT id="usrTyps" type="hidden" value='<?=$typ?>' name="usrTyps">
					<table cellpadding="0" cellspacing="10" border="0">
						<tr>
							<td valign="top" align="left">User Name:</td>
							<td valign="top" align="left">
								<input class='lgn_fld' id='uname' size='30' name='uname' value="<?=isset($user_name)?$user_name:""?>" 
								 label="User Name" placeholder="Enter Username" validate="IsNotBlnk()">
							</td>
						</tr>
						<tr>
							<td valign="top" align="left">Password:</td>
							<td valign="top" align="left">
								<input class='lgn_fld' id='pass' type='password' size='30' name='pass' 
								 label="Password" placeholder="Enter Password" validate="IsNotBlnk()">
							</td>
						</tr>
						<tr>
							<td valign="top" align="right" colspan="2">
								<INPUT class="cncl_btn" type="reset" value="Cancel" name="reset">
								<INPUT class="lgn_btn" type="submit" value="Login" name="submit"> 
							</td>
						</tr>
					</table>
				</FORM>
			</div>
			<p><?=$msg?></p>
		</div>
	<?php 
		page_footer("");
	}
		
	function admin_loginProcess()
	{
		global $cfg, $mycms;
		$user_name        = $_REQUEST['uname'];
		$user_pass        = $_REQUEST['pass'];
		$user_typ         = $_REQUEST['usrTyps'];
		$user_typs        = "'".implode("','",explode(",",$user_typ))."'";
		
		$user_check_quary    = array();
		$user_check_quary['QUERY'] = "SELECT * FROM "._DB_CONF_USER_." 
							          WHERE username= ? 
								        AND password= ?
								        AND user_type IN (".$user_typs.") "; 
		$user_check_quary['PARAM'][] =	array('FILD' => 'username' , 	  'DATA' => $user_name ,       'TYP' => 's');	
		$user_check_quary['PARAM'][] =	array('FILD' => 'password' , 	  'DATA' => $user_pass ,       'TYP' => 's');
	
		$result 	=	$mycms->sql_select($user_check_quary);
			
		if($result)
		{
			$field = $result[0];
			if($field["status"]=='A')
			{

				
				$mycms->login($field["a_id"], $field["username"], $field["user_type"]);	
				//die();
				$mycms->redirect("../dashboard.php");		
			} 
			else 
			{
				$msg = "Account Disabled, Please Contact To Administrator.";
				$mycms->redirect("login.php?msg=".$msg);
			}	
		} 
		else
		{
			$msg = "Invalid User Access";
			$mycms->redirect("login.php?msg=".$msg);
		} 
	}
	
	function admin_preLoginProcess()
	{
		global $cfg, $mycms;
		
		$user_name        = $_REQUEST['uname'];
		$user_typ         = $_REQUEST['usrTyps'];
		$user_typs        = "'".implode("','",explode(",",$user_typ))."'";
		
		$user_check_quary    = array();
		$user_check_quary['QUERY'] = "SELECT * FROM "._DB_CONF_USER_." 
							             WHERE username=?
								           AND user_type IN (".$user_typs.") "; 
		$user_check_quary['PARAM'][] =	array('FILD' => 'username' , 	  'DATA' => $user_name ,       'TYP' => 's');	

		$result 	=	$mycms->sql_select($user_check_quary);

		if ($result)
		{
			$field = $result[0];
			if($field["status"]=='A')
			{
				if(false && $field["a_id"]!=='1' && strtolower($_SERVER['HTTP_HOST'])!='localhost' && $field["a_id"]!=='2')
				{
					$ip = $_SERVER['REMOTE_ADDR'];
					$sqlIp			=	array();
					$sqlIp['QUERY']	=	"SELECT * 
											FROM "._DB_IP_."
											WHERE  `ip` = ?   ";
													
					$sqlIp['PARAM'][]	=	array('FILD' => 'ip' , 		    'DATA' => $ip , 		'TYP' => 's');
							
					$resIp = $mycms->sql_select($sqlIp);
					if($resIp || $field["a_id"]=='6' || $field["a_id"]=='123' || $field["a_id"]=='124')
					{
						$pass  = $mycms->getRandom(6, 'snum');
						$pass2 = md5($pass);
							
						$change_pass 	  = array();			
						$change_pass['QUERY'] = "UPDATE "._DB_CONF_USER_." 
													SET `password` = ? 
												  WHERE `a_id` = ?";
						$change_pass['PARAM'][]   = array('FILD' => 'password',  'DATA' =>$pass2, 				'TYP' => 's');
						$change_pass['PARAM'][]   = array('FILD' => 'a_id',      'DATA' => $field["a_id"],      'TYP' => 's');						 
												 
						$mycms->sql_update($change_pass);
						
						$messageString = $pass." is the OTP for your access to ".$cfg['EMAIL_CONF_NAME']." Webmaster on ".date("Y-m-d H:i");
						
						if($field['mobile_no']!='')
						{
							$mycms->send_sms($field['mobile_no'], $messageString, 'Informative');
						}
						
						$mycms->send_mail($field['name'], $field['email'], $cfg['CONF_NAME']." Webmaster OTP", $messageString);
					}
					else
					{
						$msg = "Your IP ".$ip." is not allowed, please contact the Administrator.";
						$mycms->redirect("login.php?allow=".$user_name."&msg=".$msg);
						exit();
					}	
				}
				$mycms->redirect("login.php?process=".$mycms->encoded(md5("loginScreen")."####".$user_name));	
				exit();
			} 
			else 
			{
				$msg = "Account disabled, please contact the Administrator.";
				$mycms->redirect("login.php?allow=".$user_name."&msg=".$msg);
				exit();
			}	
		} 
		else
		{
			$msg = "Invalid user Access";
			$mycms->redirect("login.php?allow=".$user_name."&msg=".$msg);
			exit();
		} 
	}
	
	function admin_changePasswordScreen($cfg, $mycms)
	{
		global $msg;
?>
	  <div class="contentDiv">
	  <form name="frm_changpass" action="" method="post" onsubmit="return FormValidator.validate(this);"> 
		<input type="hidden" name="act" value="<?=md5("changepass")?>" />
		<input type="hidden" name="id" value="<?=$mycms->getLoggedUserId()?>" />
		<table cellpadding="0" cellspacing="0" border="0" width="50%" class="tborder">
		  <tr>
			<td colspan="2" align="left" valign="top" class="theader">Change Password</td>
		  </tr>
		  <tr class="light">
			<td width="30%" align="left" valign="top">Username</td>
			<td width="70%" align="left" valign="top"><?=$mycms->getLoggedUserName()?></td>
		  </tr>
		  <tr>
			<td align="left" valign="top">Existing Password</td>
			<td align="left" valign="top">
			  <input name="pass1" type="password" class="forminputelement" id="pass1" label="Existing Password" 
			   validate="IsNotBlnk()">
			</td>
		  </tr>
		  <tr class="light">
			<td align="left" valign="top">New Password</td>
			<td align="left" valign="top">
			  <input name="pass2" type="password" class="forminputelement" id="pass2" label="New Password" 
			   validate="IsNotBlnk()&NotEquals(@pass1)">
			</td>
		  </tr>
		  <tr>
			<td align="left" valign="top">Confirm Password</td>
			<td align="left" valign="top">
			  <input name="pass3" type="password" class="forminputelement" id="pass3" label="Confirm Password" 
			   validate="IsNotBlnk()&Equals(@pass2)"/>
			</td>
		  </tr>
		  <tr>
			<td align="left" valign="top"></td>
			<td align="left" valign="top">
			  <input name="Submit" type="submit" class="loginbttn" 
			   value="Submit" <?=($cfg['LIFE_CYCLE'] == "DEMO")?'disabled':''?> />
			  &nbsp; 
			  <input type="reset" name="Reset" id="Reset" class="cancelbttn">
			</td>
		  </tr>
		  <tr class="tfooter">
		    <td colspan="2">&nbsp;</td>
		  </tr>
		</table>
	  </form>
	</div>
<?
	}
	
	function admin_changePasswordProcess($cfg, $mycms)
	{
		if(isset($_REQUEST['id']) && ($_REQUEST['id']==$mycms->getLoggedUserId())){
			$pass1 = $mycms->encoded($_REQUEST['pass1']);
			$pass2 = $mycms->encoded($_REQUEST['pass2']);
			
			$user_check_quary    = array();
			$user_check_quary['QUERY'] = "SELECT * 
											FROM "._DB_CONF_USER_." 
										   WHERE `password` = ?
										     AND `a_id`= ?";
			$user_check_quary['PARAM'][] =	array('FILD' => 'password' ,  'DATA' => $pass1 ,       					  'TYP' => 's');	
			$user_check_quary['PARAM'][] =	array('FILD' => 'a_id' , 	  'DATA' => $mycms->getLoggedUserId() ,       'TYP' => 's');

			$change_pass    = array();
			$change_pass['QUERY'] = "UPDATE "._DB_CONF_USER_." 
							   SET `password` 	= ? , 
							       `modifiedBy` = ?
							 WHERE `a_id` 		= ? ";
			
			$change_pass['PARAM'][] =	array('FILD' => 'password' ,  'DATA' => $pass2 ,     				  	  'TYP' => 's');	
			$change_pass['PARAM'][] =	array('FILD' => 'modifiedBy' ,'DATA' => $mycms->getLoggedUserId() ,       'TYP' => 's');				
			$change_pass['PARAM'][] =	array('FILD' => 'a_id' , 	  'DATA' => $mycms->getLoggedUserId() ,       'TYP' => 's');				
			$user_check_result = $mycms->sql_select($user_check_quary);
			if ($user_check_result){					
				$change_result = $mycms->sql_update($change_pass);
				$mycms->redirect('admin.php?m=Password Changed Successfully');
				exit();
			}else{
				$msg = "Please enter correct existing password.";
			}
			
		}
	}

/////////////////////////////////////////////////////////////////////////////////////
// DISPLAY RELATED FUNCTIONS
/////////////////////////////////////////////////////////////////////////////////////		
	
	function webmaster_adminDisplay()
	{
		global $cfg,$mycms;	
		switch($cfg['SECTION']){
			default :
				$i=1;
	?>
				<table width="100%" border="0" cellpadding="10" cellspacing="10">
					<tr>
	<? 
				if($cfg['SECTION']=='Settings' && $cfg['LIFE_CYCLE'] != "DEMO"){ 
	?>
					<td align="left" valign="top" class="dashTd" width="25%">
					<b><span class="dashboardHeader">Account</span></b><br />
					<div style="height:2px;"></div>
					<a href="<?=$pth?>changpass.php" class="dashboardLink">&bull;&nbsp;&nbsp;Change Password</a>
					</td>
	<? 
					$i++; 
				}
				
				$accessArr = getAccess($cfg['SECTION']);
				foreach($accessArr as $module => $pages){
	?>
					<td align="left" valign="top" class="dashTd" height="100px" width="25%">
					<b><span class="dashboardHeader"><?=$module?></span></b><br/>
					<div style="height:2px;"></div>
	<?
					foreach($pages as $pageName => $file){
	?>
					  <a href="<?=$file?>" class="dashboardLink">&bull;&nbsp;&nbsp;<?=$pageName?></a><br/>
	<?
					}
	?>
					</td>	
	<?	
					if($i%4==0) echo "</tr><tr>";
					$i++;
				}
	?>
					</tr>
				</table>
	<?
				break;
		}
	}
	
	// Int To Float Converter
	function intToFloat($amount)
	{
		$paid_amount = number_format((float)($amount), 2, '.', '');
		return $paid_amount;
	}
	
	// SEARCH STATUS METHOD
	function searchStatus($queryString="")
	{
		global $cfg, $mycms, $searchArray;
		
		$pageName    = $mycms->getPageName();
		$returnValue = 0;
		
		foreach($searchArray as $searchKey=>$searchVal)
		{
			if($searchVal!="")
			{
				$returnValue = 1;
			}
		}
		
		if($returnValue==1)
		{
?>
			<script language="javascript">
				$(document).ready(function(){
					$(".tsearch").css("display","block");
				});
			</script>
			<input type="button" name="clearBttn" id="clearBttn" class="btn btn-small btn-red" 
			 value="Clear" onclick="window.location.href='<?=$pageName.$queryString?>'" />
<?php
		}
	}
	
	function daysCountInMonth($mn, $yr)
	{
		switch($mn){
			case '1':
				return 31;
				break;
			case '2':
				if(($yr%4)==0)
					return 29;
				else 
					return 28;
				break;
			case '3':
				return 31;
				break;
			case '4':
				return 30;
				break;
			case '5':
				return 31;
				break;
			case '6':
				return 30;
				break;
			case '7':
				return 31;
				break;
			case '8':
				return 31;
				break;
			case '9':
				return 30;
				break;
			case '10':
				return 31;
				break;
			case '11':
				return 30;
				break;
			case '12':
				return 31;
				break;
		}
	}
	
	/****************************************************/
	/*                SET DATE TIME FORMAT              */
	/****************************************************/
	function setDateTimeFormat($date, $type='D')
	{
		$returnDate = "";
		
		if($date!="" && $date!="0000-00-00" && $date!="00:00:00" && $date!="0000-00-00 00:00:00")
		{
			switch($type) {
				case'T':
					
					$returnDate = date('d-F-Y h:i A', strtotime($date));
					break;
					
				case'D':
					
					$returnDate = date('d-F-Y', strtotime($date));
					break;
								
				default:
				
					$returnDate = date('d-F-Y', strtotime($date));
					break;
			}
		}
		return $returnDate; 
	}
	
	function setTimeFormat($time, $type='24')
	{
		$returnTime = "";
		
		if($time!="")
		{
			switch($type) {
				
				case'12':
					
					$returnTime = date('h:i A', strtotime($time));
					break;
					
				case'24':
					
					$returnTime = date('H:i', strtotime($time));
					break;
								
				default:
				
					$returnTime = date('h:i A', strtotime($time));
					break;
			}
		}
		return $returnTime; 
	}
	
	function setDateTimeFormat2($date, $type='D')
	{
		$returnDate = "";
		
		if($date!="" && $date!="0000-00-00" && $date!="00:00:00" && $date!="0000-00-00 00:00:00")
		{
			switch($type) {
				case'T':
					
					$returnDate = date('d/m/y h:i: A', strtotime($date));
					break;
					
				case'D':
					
					$returnDate = date('d/m/y', strtotime($date));
					break;
								
				default:
				
					$returnDate = date('d/m/y', strtotime($date));
					break;
			}
		}
		return $returnDate; 
	}
	
	function setDateTimeFormat3($date, $type='D')
	{
		$returnDate = "";
		
		if($date!="" && $date!="0000-00-00" && $date!="00:00:00" && $date!="0000-00-00 00:00:00")
		{
			switch($type) {
				case'T':
					
					$returnDate = date('d-m-Y h:i: A', strtotime($date));
					break;
					
				case'D':
					
					$returnDate = date('d-m-Y', strtotime($date));
					break;
								
				default:
				
					$returnDate = date('d-m-Y', strtotime($date));
					break;
			}
		}
		return $returnDate; 
	}
	
	/****************************************************/
	/*                 GENERATE NEXT CODE               */
	/****************************************************/
	function generateNextCode($field, $table, $prefix,$digit=6)
	{
		global $cfg, $mycms;
		
		$code            = 0;
		
		$sql['QUERY']             = "SELECT MAX(".$field.") AS maxId 
		                     		 FROM ".$table."";
		
		$result          = $mycms->sql_select($sql);
		$row             = $result[0];
		$seqNumber       = $row['maxId'] + 1;
		$seqNumber       = number_pad($seqNumber, $digit);
		$code            = $prefix.$seqNumber;
		
		return $code;
	}
	
	/****************************************************/
	/*         FORWARD / BACKWARD DATE CALCULATOR       */
	/****************************************************/
	function moveDateByYear($interval = "-1")
	{
		$currentDate = date('Y-m-d');
		return date('Y-m-d', strtotime($currentDate.$interval." years"));
	}
	
	function post_redirect($redirectUrl)
	{
		global $cfg, $mycms;
		
		$randomKey                  = $mycms->getRandom(6, 'alphanum');
		
		if(strpos($redirectUrl, '?'))
		{
			$redirectUrlArray		= array();
			$redirectUrlArray		= explode("?", $redirectUrl);
			
			$redirectPage           = $redirectUrlArray[0];
			$queryString            = $redirectUrlArray[1];
		?>
			<form id="redirectForm_<?=$randomKey?>" action="<?=$redirectPage?>" method="post">
			<?php
			$queryStringArray       = array();
			$queryStringArray       = explode("&", $queryString);
			
			foreach($queryStringArray as $keyString=>$valueString)
			{
				$valueSetArray      = array();
				$valueSetArray      = explode("=", $valueString);
				
				$key                = $valueSetArray[0];
				$value              = $valueSetArray[1];
			?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?php
			}
			?>
			</form>
		<?php
		}
		else
		{
		?>
			<form id="redirectForm_<?=$randomKey?>" action="<?=$redirectUrl?>" method="post"></form>
		<?php	
		}
	?>
		<script language="javascript">
			document.getElementById("redirectForm_<?=$randomKey?>").submit();
		</script>
	<?php
	}
?>
