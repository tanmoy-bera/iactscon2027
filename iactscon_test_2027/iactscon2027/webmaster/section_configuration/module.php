<?php
	include_once('includes/init.php');
	include_once('../includes/backend.function.module.php');
	
	page_header("Module");
	
	includeModuleJavascript($cfg, $mycms);
?>
	<div class="container">
	<?php 
		switch($show){
			// show insert window
			case 'add':	
				addModuleFormLayout($cfg, $mycms);
				break;	
			// show edit window	
			case 'edit':
				editModuleFormLayout($cfg, $mycms);
				break;						
			//show all record	
			default:	
				moduleListingDisplay($cfg, $mycms); 
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
	function includeModuleJavascript($cfg, $mycms)
	{
?>
		<SCRIPT language="javascript" src="scripts/page.module.js"></SCRIPT>
<?php
	}
	
	/**************************************************************/
	/*                  MODULE LISTING DISPLAY LAYOUT             */
	/**************************************************************/
	function moduleListingDisplay($cfg, $mycms)
	{
?>
		<form name="frmSearch" method="post" action="module.php">	
			<table width="100%" class="tborder" align="center" >	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Module</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch">
							<table width="100%">
								<tr>
									<td align="right">
										Search
										&nbsp;
										<input name="searchTxt" type="text" id="searchTxt" 
										 size="20" label="Search Text" validate="IsNotBlnk()" required="" />
										&nbsp;
										<strong>in</strong>
										&nbsp;
										<select id="searchSel" name="searchSel"  label="Search In" validate="IsSel()" required="">		
											<option value="m.moduleName">Module Name</option>
											<option value="s.sectionName">Section Name</option>
										</select>
										&nbsp;
										<input type="submit" name="goSearch" value="Go" class="btn btn-small btn-blue" />
										<?php
										if(trim($_REQUEST['searchTxt'])!='')
										{
										?>
											<a href="module.php" class="redElements">Clear filter</a>
										<?php
										}
										?>
									</td>
								</tr>
							</table>
						</div>
					
						<table width="100%">
							<tr class="theader">
								<td width="100" align="center">Ref. No.</td>
								<td align="left">Module Name</td>
								<td width="70" align="center">Action</td>
							</tr>
							<?php
							$searchTxt = trim($_REQUEST['searchTxt']);
							$searchSel = trim($_REQUEST['searchSel']);
							if($searchTxt!='')
							{
								$whereClause = " WHERE ".$searchSel." = '".$searchTxt."' 
													OR ".$searchSel." LIKE '%".$searchTxt."'
													OR ".$searchSel." LIKE '".$searchTxt."%'
													OR ".$searchSel." LIKE '%".$searchTxt."%' ";
							}
							$sql =	array();
							$sql['QUERY'] = "SELECT m.moduleId AS moduleId, 
												   m.moduleName AS moduleName, 
												   s.sectionName AS sectionName
											  FROM "._DB_CONF_MODULE_." m 
										INNER JOIN "._DB_CONF_SECTION_." s 
												ON m.sectionId = s.sectionId AND s.status != 'D'
										INNER JOIN "._DB_CONF_DOMAIN_." d 
												ON (s.domainId = d.id 
													OR s.domainId = 0)  
											   AND d.tag = ? ".$whereClause." 
										  ORDER BY m.sectionId, m.seq";
							$sql['PARAM'][]	=	array('FILD' => 'd.tag' , 	  'DATA' => $cfg['DOMAIN_TAG'] ,       'TYP' => 's');
							$result = $mycms->sql_select_paginated($indexVal, $sql, 50, $restrt);		
							if($result) 
							{
								$sectionName = "";
								foreach ($result as $i=>$row) 
								{
									if($sectionName!=$row['sectionName'])
									{
										$sectionName=$row['sectionName'];
							?>
										<tr class="thighlight">
											<td align="left" colspan="3"><?=$sectionName?></td>
										</tr>
							<?php
									}
							?>
									<tr class="tlisting">
										<td align="center"><?=number_pad($row['moduleId'],3)?></td>
										<td align="left"><?=$row['moduleName']?></td>	
										<td align="center">
											<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="module.php?show=edit&id=<?= $row['moduleId'] ?>">
											<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
										</td>
									</tr>
							<?php
								}
							} 
							else 
							{
							?>
								<tr>
									<td colspan="3" align="center" class="msg">No Record Present.</td>
								</tr>  
							<?php 
							} 
							?>
							<tr>
								<td colspan="3" align="right">
									<a onClick="location.href='module.php?show=add'">Add Module</a>
								</td>
							</tr>
						</table>
						
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo($indexVal)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate($indexVal,'pagination')?></span>
					</td>
				</tr>
			</table>
		</form>
<?php
	}
	
	/**************************************************************/
	/*                      ADD MODULE FORM LAYOUT                */
	/**************************************************************/
	function addModuleFormLayout($cfg, $mycms)
	{
?>
		<form action="module.process.php" method="post" name="frmadd" onsubmit="return onSubmitAction();" >
			<input type="hidden" name="act" value="insert" />		
			<table width="52%" cellpadding="6"  cellSpacing=1 class="tborder">
				<thead>
					<tr>
						<th class="tcat" colspan="2" align="left">Add Module</th>
					</tr>
				</thead>
				<tbody>				
					<tr>
						<td align="left">
							Section Name <span class="mandatory">*</span>
						</td>
						<td>
							<select name="sectionId" id="sectionId" label="Section Name" 
							 validate="IsSel()" onchange="getSequence(this.form);" required="">
								<option value="">-- Select Section --</option>
								<?php 
								$sql 	= array();
							   $sql['QUERY'] = "SELECT s.sectionId, s.sectionName 
										          FROM "._DB_CONF_SECTION_." s  								
										    INNER JOIN "._DB_CONF_DOMAIN_." d 
										            ON (s.domainId = d.id 
										                OR s.domainId = 0) 								
										         WHERE d.tag = ?
										           AND s.status != ? ";
							$sql['PARAM'][]	=	array('FILD' => 'd.tag' , 	  'DATA' => $cfg['DOMAIN_TAG'] ,       'TYP' => 's');
							$sql['PARAM'][]	=	array('FILD' => 's.status' ,  'DATA' => 'D' ,       			   'TYP' => 's');
										
								$result = $mycms->sql_select($sql);	 
								foreach($result as $ind=>$row_mod)
								{
								?>
									<option value="<?=$row_mod['sectionId']?>"><?=$row_mod['sectionName']?></option>
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
						<td>
							<input name="moduleName" id="moduleName" type="text" 
							 size="45" label="Module Name" validate="IsNotBlnk()" required="" />
						</td>
					</tr>
					<tr>
						<td align="left">
							Sequence <span class="mandatory">*</span>
						</td>
						<td id="modSeq">
							<select name="seq" id="seq" label="Sequence" validate="IsSel()" required="">
								<option value="">None</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"></td>
						<td align="left">
							<input name="back" type="button" class="btn btn-small btn-red" id="back" 
							value="Back" onClick="location.href='module.php';"/>
							&nbsp;
							<input type="submit" name="Save" id="Save" value="Add Module" class="btn btn-small btn-blue">
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
	/*                     EDIT MODULE FORM LAYOUT                */
	/**************************************************************/
	function editModuleFormLayout($cfg, $mycms)
	{
		$moduleId = $_REQUEST['id'];
		$sql 	= array();
		$sql['QUERY']      = "SELECT *
							   FROM "._DB_CONF_MODULE_." 
				              WHERE `moduleId` = ?";
		$sql['PARAM'][]	=	array('FILD' => 'moduleId' , 	  'DATA' => $moduleId ,       'TYP' => 's');		              
		$result   = $mycms->sql_select($sql);
		$row_edit = $result[0];
?>
		<form action="module.process.php" method="post" enctype="multipart/form-data" name="frmEdit"  onsubmit="return onSubmitAction();" >
			<input type="hidden" name="act" id="act" value="update" />
			<input type="hidden" name="id" id="id" value="<?=$moduleId?>" />
			<table width="52%" cellpadding="6"  cellSpacing=1 class="tborder">
				<thead>
					<tr>
						<th class="tcat" colspan="2" align="left">Edit Module</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="left">Ref. No. <span class="mandatory">*</span></td>
						<td><strong>#<?=number_pad($moduleId,5)?></strong></td>
					</tr>
					<tr>
						<td align="left">
							Section Name <span class="mandatory">*</span>
						</td>
						<td>
							<input type="hidden" name="presentSection" id="presentSection" value="<?=$row_edit['sectionId']?>" />							
							<select name="sectionId" id="sectionId" label="Section Name" validate="IsSel()" onchange="getSequence(this.form);" required="">
								<option value="">-- Select Section --</option>
								<?php 
								$sql = array();
								$sql['QUERY'] =    "SELECT s.sectionId, s.sectionName 
											          FROM "._DB_CONF_SECTION_." s  
											    INNER JOIN "._DB_CONF_DOMAIN_." d 
											            ON (s.domainId = d.id 
											                OR s.domainId = 0)  
											         WHERE d.tag = ?
											           AND s.status != ?";
								$sql['PARAM'][]	=	array('FILD' => 'd.tag' , 	  'DATA' => $cfg['DOMAIN_TAG'] ,       'TYP' => 's');
								$sql['PARAM'][]	=	array('FILD' => 's.status' ,  'DATA' => 'D' ,       			   'TYP' => 's');
								$resultSec = $mycms->sql_select($sql);	 
								foreach($resultSec as $secInd=>$row_mod)
								{
								?>
									<option value="<?=$row_mod['sectionId']?>" <?=($row_mod['sectionId']==$row_edit['sectionId'])?"selected":""?>><?=$row_mod['sectionName']?></option>
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
						<td>
							<input name="moduleName" id="moduleName" type="text" value="<?= $row_edit['moduleName'] ?>" 
							 size="45" label="Module Name" validate="IsNotBlnk()" required/>
						</td>
					</tr>
					<tr>
						<td align="left">Sequence<span class="mandatory">*</span></td>
						<td id="modSeq">
							<input type="hidden" name="presentSeq" id="presentSeq" value="<?=$row_edit['seq']?>" />
							<select name="seq" id="seq" label="Sequence" validate="IsSel()" required="">
								<option value="">None</option>
								<?php
								$sql 	= array();
								$sql['QUERY'] = "SELECT IFNULL(MAX(seq),0) AS max 								
												   FROM "._DB_CONF_MODULE_." 
												  WHERE `sectionId` = ?";
								$sql['PARAM'][]	=	array('FILD' => 'sectionId' , 	  'DATA' => $row_edit['sectionId'] ,       'TYP' => 's');	
								$resultCnt = $mycms->sql_select($sql);	
								$row_cnt   = $resultCnt[0]; 
								for($i=1; $i <= $row_cnt['max']; $i++)
								{
								?>
									<option value="<?=$i?>" <?=($i==$row_edit['seq'])?"selected":""?>><?=$i?></option>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td align="left">
							<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  onClick="location.href='module.php';"/>
							&nbsp;
							<input type="submit" name="Save" id="Save" value="Update Module" class="btn btn-small btn-blue">
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