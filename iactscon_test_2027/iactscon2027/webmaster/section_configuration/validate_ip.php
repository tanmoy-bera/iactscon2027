<?php
	include_once('includes/init.php');
	include_once('../includes/backend.function.pages.php');
	
	page_header("IP Validation");
	
	includePageJavascript($cfg, $mycms);
?>	
	<div class="container">
	<?php 
		switch($show){
			// show insert window
			case 'add':	
				addPage($cfg, $mycms);
				break;	
			// show edit window	
			case 'edit':
				editPage($cfg, $mycms);
				break;						
			//show all record	
			default:	
				webPageListingDisplay($cfg, $mycms); 
				break;				
		} 
	?>
	</div>
<?php
	page_footer();
?>
<?php
	/**************************************************************/
	/*                       INCLUDE JAVASCRIPT                   */
	/**************************************************************/
	function includePageJavascript($cfg, $mycms)
	{
?>
		<script language="javascript" src="scripts/page.page.js"></script>
<?php
	}
	
	/**************************************************************/
	/*                  WEB PAGE LISTING DISPLAY LAYOUT           */
	/**************************************************************/
	function webPageListingDisplay($cfg, $mycms)
	{
		$sqlBanner			=	array();
		$sqlBanner['QUERY']	=	"SELECT * 
								FROM "._DB_IP_."
								WHERE `status` 		= 		?";
										
		$sqlBanner['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'A' , 		'TYP' => 's');
				
		$resBanner = $mycms->sql_select_paginated($indexVal, $sqlBanner, 50, $restrt);
		$maxRow = $mycms->sql_numrows($resBanner);
?>
		<form name="frmSearch" method="post" action="page.php" onsubmit="return FormValidator.validate(this);">	
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" align="left">
						<span style="float:left;">Listed Ip</span>
						<span style="float:right; margin-right:10px;"><a onClick="location.href='validate_ip.php?show=add'">Add New Ip</a></span>
						
					</td>
				</tr>
				<tr>
					<td style="margin:0px; padding:0px;">
						
						<table width="100%">
							<tr class="theader">
								<td width="100" align="center">Sl. No.</td>
								<td align="left">IP</td>
								<td width="70" align="center">Action</td>
							</tr>
							<?php
							
							if($resBanner)
							{
								foreach($resBanner as $i=>$rowBanner) 
								{
									$counter      = $counter + 1;
							?>
									<tr class="tlisting">
										<td align="center"><?=$counter + ($_REQUEST['_pgn1_']*10)?></td>
										<td align="left">
											<?=$rowBanner['ip']?>
										</td>
										<td align="center">
											<a href="validate.ip.process.php?act=delFeatures&id=<?=$rowBanner['id'] ?>">
											<span alt="Remove" title="Remove" class="icon-x-alt"
											onclick="return confirm('Do you really want to delete this record ?');" /></a>
										
										</td>
									</tr>
							<?php
								}
							} 
							else 
							{
							?>
								<tr>
									<td colspan="4" align="center" class="msg">No Record Present.</td>
								</tr>  
							<?php 
							} 
							?>
							<tr>
								<td colspan="4" align="right">
									<a onClick="location.href='validate_ip.php?show=add'">Add New Ip</a>
								</td>
							</tr>
						</table>
						
					</td>
				</tr>
				<tr class="tfooter">
					<td>
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo($indexVal)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate($indexVal,'pagination')?></span>
					</td>
				</tr>
			</table>
		</form>
<?php
	}
	
	/**************************************************************/
	/*                      WEB PAGE ADD FORM LAYOUT              */
	/**************************************************************/
	function addPage($cfg, $mycms)
	{
?>
		<form action="validate.ip.process.php" method="post" name="frmadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="insert" />		
			<table width="52%" cellSpacing=1 class="tborder">
				<thead>
					<tr>
						<th class="tcat" colspan="2" align="left">Add New Ip</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="left">
							Valid Ip <span class="mandatory">*</span>
						</td>
						<td>
							<input name="newIp" type="text" id="newIp" size="45" label="New Ip" required />
						</td>
					</tr>	
					<tr>
						<td align="right"></td>
						<td align="left">
							<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  onClick="location.href='validate_ip.php';"/>
							&nbsp;
							<input type="submit" name="Save" id="Save" value="Add New" class="btn btn-small btn-blue">
						</td>
					</tr>
					<tr class="tfooter">
						<td colspan="2">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</form>
<?php
	}
	
	/**************************************************************/
	/*                     WEB PAGE EDIT FORM LAYOUT              */
	/**************************************************************/
	function editPage($cfg, $mycms)
	{	
		$pageId = $_REQUEST['id'];
		
		$sql 	=	array();
		$sql['QUERY']   = "SELECT p.pageId AS pageId, 
								  p.moduleId AS moduleId, 
							   
								  m.sectionId AS sectionId, 
							   
								  p.pageName AS pageName, 
								  p.fileName AS fileName, 
								  p.seq AS seq 
							   
							 FROM "._DB_CONF_PAGE_." p
					
					   INNER JOIN "._DB_CONF_MODULE_." m 
							   ON m.moduleId = p.moduleId
						 
							WHERE pageId = ?";
		$sql['PARAM'][]	=	array('FILD' => 'pageId' , 	  'DATA' => $pageId ,       'TYP' => 's');		 
		$result   = $mycms->sql_select($sql);
		$row_edit = $result[0];
?>
		<form action="page.process.php" method="post" name="frmadd" onsubmit="return onSubmitAction();" >
			<input type="hidden" name="act" id="act" value="update" />
			<input type="hidden" name="id" id="id" value="<?=$pageId?>" />
			<table width="52%" cellSpacing=1 class="tborder">
				<thead>
					<tr>
						<th class="tcat" colspan="2" align="left">Edit Page</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="left">
							Ref. No. <span class="mandatory">*</span>
						</td>
						<td><strong>#<?=number_pad($pageId,5)?></strong></td>
					</tr>
					<tr>
						<td align="left">
							Section Name <span class="mandatory">*</span>
						</td>
						<td>
							<select name="sectionId" id="sectionId" label="Section Name" validate="IsSel()" onchange="getModule(this.form);" >
								<option value="">-- Select Section --</option>
								<?php 
								$sql 	=	array();
								$sql['QUERY'] = "SELECT s.sectionId, s.sectionName 								
								         		   FROM "._DB_CONF_SECTION_." s  								
								 		     INNER JOIN "._DB_CONF_DOMAIN_." d 
								            		ON (s.domainId = d.id 
								               			OR s.domainId = 0) 
								        		  WHERE d.tag = ?";
								$sql['PARAM'][]	=	array('FILD' => 'd.tag' , 	  'DATA' => $cfg['DOMAIN_TAG'] ,       'TYP' => 's');        
								$result = $mycms->sql_select($sql);	 
								foreach($result as $rowsecI=>$row_sec)
								{
								?>
									<option value="<?=$row_sec['sectionId']?>" <?=($row_sec['sectionId']==$row_edit['sectionId'])?"selected":""?>><?=$row_sec['sectionName']?></option>
								<?php 
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">
							Module Name <span class="mandatory">*</span>
						</td>						
						<td id="pgMod">							
							<input type="hidden" name="presentModule" id="presentModule" value="<?=$row_edit['moduleId']?>" />
							<select name="moduleId" id="moduleId" required="" label="Module Name" validate="IsSel()" onchange="getSequence(this.form)" >
								<option value="">Select Module</option>
								<?php 

								$sql 	=	array();
								$sql['QUERY']    = "SELECT * 
													  FROM "._DB_CONF_MODULE_." 
								                  	  WHERE `sectionId` = ? ";

								$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  'DATA' => $row_edit['sectionId'] ,       'TYP' => 's');                  	  
								$result = $mycms->sql_select($sql);	 
								foreach($result as $rowModI=>$row_mod){
								?>
									<option value="<?=$row_mod['moduleId']?>" <?=($row_mod['moduleId']==$row_edit['moduleId'])?'selected="selected"':''?>><?=$row_mod['moduleName']?></option>
								<?php 
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left">
							Page Name <span class="mandatory">*</span>
						</td>
						<td>
							<input name="pageName" value="<?= $row_edit['pageName'] ?>" type="text" 
							 id="pageName" size="45" label="Page Name" validate="IsNotBlnk()"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							File Name <span class="mandatory">*</span>
						</td>
						<td> 
							<input name="fileName" value="<?= $row_edit['fileName'] ?>" type="text" 
							 id="fileName" size="45" label="File Name" validate="IsNotBlnk()" />
						</td>
					</tr>
					<tr>
						<td align="left">
							Sequence <span class="mandatory">*</span>
						</td>						
						<td id="pgSeq">							
							<input type="hidden" name="presentSeq" id="presentSeq" value="<?=$row_edit['seq']?>" />
							<select name="seq" id="seq" label="Sequence" validate="IsSel()" required="">
								<option value="">None</option>
								<?php

								$sql 	=	array();
								$sql['QUERY']       = "SELECT IFNULL(MAX(seq),0) AS max 
														 FROM "._DB_CONF_PAGE_." 
													    WHERE `moduleID` = ? ";

								$sql['PARAM'][]	=	array('FILD' => 'moduleID' , 	  'DATA' => $row_edit['moduleId'] ,       'TYP' => 's');   					    
								$resultCnt = $mycms->sql_select($sql);	
								$row_cnt   = $resultCnt[0]; 
								for($i=1; $i <= $row_cnt['max']; $i++)
								{
								?>
									<option value="<?=$i?>" <?=($i==$row_edit['seq'])?"selected":""?> ><?=$i?></option>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"></td>
						<td align="left">
							<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  onClick="location.href='page.php';"/>
							&nbsp;
							<input type="submit" name="Save" id="Save" value="Update Page" class="btn btn-small btn-blue">
						</td>
					</tr>
					<tr class="tfooter">
						<td colspan="2">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</form>
<?php
	}
?>
