<?php
	include_once('includes/init.php');
	include_once('../includes/backend.function.section.php');
	
	page_header("Sections");
	
	$pageKey          = "_pgn_";
	$pageKeyVal       = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString    = "";
	$searchArray      = array();
	
	$searchArray[$pageKey]                   = $pageKeyVal;
	$searchArray['src_filter_text']          = trim($_REQUEST['src_filter_text']);
	$searchArray['src_filter_selection']     = trim($_REQUEST['src_filter_selection']);
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
	
	includeSectionJavascript($cfg, $mycms);
?>
	<div class="container">
		<?php 
		switch($show){
		
			/*******************************************************/
			/*                 SECTION INSERT WINDOW               */
			/*******************************************************/
			case'add':
				
				sectionAddFormLayout($cfg, $mycms);
				break;	
				
			/*******************************************************/
			/*                  SECTION EDIT WINDOW                */
			/*******************************************************/
			case'edit':
			
				sectionEditFormLayout($cfg, $mycms);
				break;
									
			/*******************************************************/
			/*               ALL SECTION LISTING WINDOW            */
			/*******************************************************/
			default:
				
				sectionListingDisplay($cfg, $mycms); 
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
	function includeSectionJavascript($cfg, $mycms)
	{
	?>
		<SCRIPT language="javascript" src="scripts/page.section.js"></SCRIPT>
	<?php
	}
	
	/**************************************************************/
	/*                     SECTION LISTING DISPLAY                */
	/**************************************************************/
	function sectionListingDisplay($cfg, $mycms)
	{
		global $searchArray, $searchString;
	?>
		<form action="section.process.php" name="frmSearch" method="post" onsubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Section</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch">
							<table width="100%">
								<tr>
									<td align="right">
										Search &nbsp;
										
										<input type="text" name="src_filter_text" id="src_filter_text" validate="IsNotBlnk()" 
										 value="<?=$_REQUEST['src_filter_text']?>" />
										
										&nbsp; in &nbsp;
										
										<select name="src_filter_selection" id="src_filter_selection" validate="IsSel()" >
											<option value="s.secCode" <?=($_REQUEST['src_filter_selection']=="s.secCode")?'selected="selected"':''?>>Section Code</option>
											<option value="s.sectionName" <?=($_REQUEST['src_filter_selection']=="s.sectionName")?'selected="selected"':''?>>Section Name</option>
											<option value="s.path" <?=($_REQUEST['src_filter_selection']=="s.path")?'selected="selected"':''?>>Section Path</option>
										</select>
										
										&nbsp;
										
										<?php 
										searchStatus();
										?>
										<input type="submit" name="goSearch" value="Go" class="btn btn-small btn-blue" />
									
									</td>
								</tr>
							</table>
						</div>
								
						<table width="100%">
							<tr class="theader">
								<td width="30" align="center">Sl No</td>
								<td width="150" align="left">Section Code</td>
								<td align="left">Section Name</td>
								<td align="left">Path</td>
								<td width="70" align="center">Icon</td>
								<td width="70" align="center">Action</td>
							</tr>
							<?php
							$sectionCounter     = 0;	
							
							$searchText         = trim($_REQUEST['src_filter_text']);
							$searchSelection    = trim($_REQUEST['src_filter_selection']);
							
							if($searchText!='')
							{
								$whereClause    = " AND ".$searchSelection." LIKE '%".$searchText."'";
							}
							
							$sqlFetchSection = array();
							$sqlFetchSection['QUERY']    = "SELECT s.sectionId AS sectionId, 
															      s.sectionName AS sectionName, 
															      s.secCode AS secCode,
															      s.seq AS seq, 
															      s.path AS path, 
															      s.img AS img	 
														     FROM "._DB_CONF_SECTION_." s  
													   INNER JOIN "._DB_CONF_DOMAIN_." d 
															   ON (s.domainId = d.id 
																   OR s.domainId = 0)  
														    WHERE d.tag = ?	  
														      AND s.status!=? ".$whereClause."
													     ORDER BY s.seq";
							
							$sqlFetchSection['PARAM'][]	=	array('FILD' => 'd.tag' , 'DATA' => $cfg['DOMAIN_TAG'] , 'TYP' => 's');	  
							$sqlFetchSection['PARAM'][]	=	array('FILD' => 's.status' ,'DATA' => 'D' ,	  			 'TYP' => 's');	  
							$resultFetchSection = $mycms->sql_select_paginated(1, $sqlFetchSection, 50, $restrt);	
							if($resultFetchSection)
							{
								foreach($resultFetchSection as $i=>$rowFetchSection) 
								{
									$sectionCounter = $sectionCounter + 1;
							?>
									<tr class="tlisting">
										<td align="center"><?=$sectionCounter?></td>
										<td align="left"><?=$rowFetchSection['secCode']?></td>	
										<td align="left"><?=$rowFetchSection['sectionName']?></td>	
										<td align="left"><?=$rowFetchSection['path']?></td>	
										<td align="center">
											<span alt="<?=$rowFetchSection['sectionName']?>" class="<?=$rowFetchSection['img']?>" />
										</td>
										<td align="center">
										
											<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="section.php?show=edit&id=<?=$rowFetchSection['sectionId']?>">
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
									<td colspan="6" align="center">
										<span class="mandatory">No Record Present.</span>
									</td>
								</tr>  
							<?php 
							} 
							?>
							<tr>
								<td colspan="6" align="right"><a href="section.php?show=add">Add Section</a></td>
							</tr>
						</table>
						
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>
			</table>
		</form>
	<?php
	}
	
	/**************************************************************/
	/*                        ADD SECTION FORM                    */
	/**************************************************************/
	function sectionAddFormLayout($cfg, $mycms)
	{
	?>
		<form action="section.process.php" method="post" name="frmadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="insert" />		
			<table width="50%" class="tborder">
				<thead>
					<tr>
						<td class="tcat" colspan="2" align="left">Add Section</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="40%" align="left">
							<span class="mandatory">*</span>
							Section Code
						</td>
						<td width="60%" align="left">
							#&nbsp;
							<input type="text" name="secCode" id="secCode" validate="IsNotBlnk()" 
							 style="width:86%;" required="" />
						</td>
					</tr>
					<tr>
						<td align="left">
							<span class="mandatory">*</span>
							Section Name
						</td>
						<td align="left">
							<input type="text" name="sectionName" id="sectionName" validate="IsNotBlnk()" 
							 style="width:90%;" required="" />
						</td>
					</tr>
					<tr>
						<td align="left">
							<span class="mandatory">*</span>
							Path
						</td>
						<td align="left">
							<input type="text" name="path" id="path" validate="IsNotBlnk()" 
							 style="width:90%;" required="" />
						</td>
					</tr>
					<tr>
						<td align="left">
							<span class="mandatory">*</span>
							Image File Name 
						</td>
						<td align="left">
							<input type="text" name="img" id="img" validate="IsNotBlnk()" 
							 style="width:90%;" required="" />
						</td>
					</tr>
					<tr>
						<td align="left">
							<span class="mandatory">*</span>
							Sequence
						</td>
						<td align="left">
							<select name="seq" id="seq" validate="IsSel()" style="width:90%;" required="">
							<?php
							$sqlFetchSequence 	=	array();
							$sqlFetchSequence['QUERY']   = 	 "SELECT (IFNULL(MAX(s.seq), 0)+1) AS max 
																FROM "._DB_CONF_SECTION_." s 
														  INNER JOIN "._DB_CONF_DOMAIN_." d 
																  ON s.domainId = d.id 
															   WHERE d.tag = ?";
							$sqlFetchSequence['PARAM'][]	=	array('FILD' => 'd.tag' , 	'DATA' => $cfg['DOMAIN_TAG'] ,	  'TYP' => 's');
							$resultFetchSequence  		= $mycms->sql_select($sqlFetchSequence);	
							$rowFetchSequence    		= $resultFetchSequence[0]['max']; 
							for($i=1; $i<=$rowFetchSequence+1; $i++)
							{
							?>
								<option value="<?=$i?>" <?=($i==$rowFetchSequence)?'selected="selected"':''?>><?=$i?></option>
							<?php
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"></td>
						<td align="left">
						
							<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  
							 onClick="location.href='section.php';"/>
							&nbsp;
							<input type="submit" name="Save" id="Save" value="Add Section" class="btn btn-small btn-blue">
						
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
	/*                       EDIT SECTION FORM                    */
	/**************************************************************/
	function sectionEditFormLayout($cfg, $mycms)
	{
		$sectionId            		 = $_REQUEST['id'];
		
		$sqlFetchSection 	=	array();
		$sqlFetchSection['QUERY']    = "SELECT * 
										  FROM "._DB_CONF_SECTION_." 
		                                  WHERE `sectionId` = ?"; 
		$sqlFetchSection['PARAM'][]	=	array('FILD' => 'sectionId' , 	'DATA' => $sectionId ,	  'TYP' => 's');
		$resultFetchSection   		 = $mycms->sql_select($sqlFetchSection);
		$rowFetchSection       		 = $resultFetchSection[0];
	?>
		<form action="section.process.php" name="frmEdit" method="post" onsubmit="return onSubmitAction();" >
			<input type="hidden" name="act" id="act" value="update" />
			<input type="hidden" name="id" id="id" value="<?=$sectionId?>"/>
			<table width="50%" class="tborder">
				<thead>
					<tr>
						<th class="tcat" colspan="2" align="left">Edit Section</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="40%" align="left">
							Section Code 
						</td>
						<td width="60%" align="left"><?=$rowFetchSection['secCode']?></td>
					</tr>
					<tr>
						<td align="left">
							<span class="mandatory">*</span>
							Section Name 
						</td>
						<td align="left">
							<input type="text" name="sectionName" id="sectionName" value="<?= $rowFetchSection['sectionName'] ?>" 
							 validate="IsNotBlnk()" style="width:90%;" required/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<span class="mandatory">*</span>
							Section Path
						</td>
						<td>
							<input type="text" name="path" id="path" value="<?= $rowFetchSection['path'] ?>" 
							 validate="IsNotBlnk()" style="width:90%;" required/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<span class="mandatory">*</span>
							Image File Name 
						</td>
						<td align="left">
							<input type="text" name="img" id="img" value="<?= $rowFetchSection['img']?>" 
							 validate="IsNotBlnk()" style="width:90%;" required/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<span class="mandatory">*</span>
							Sequence 
						</td>
						<td align="left">
							<input type="hidden" name="presentSeq" value="<?=$rowFetchSection['seq']?>" />
							<select name="seq" id="seq" label="Sequence" validate="IsSel()" style="width:90%;" required="">
							<?php

							$sqlFetchSequence  = array();
							$sqlFetchSequence['QUERY']      = "SELECT (IFNULL(MAX(s.seq), 0)+1) AS max 
																FROM "._DB_CONF_SECTION_." s 
														  INNER JOIN "._DB_CONF_DOMAIN_." d 
																  ON s.domainId = d.id 
															   WHERE d.tag = ?";
							$sqlFetchSequence['PARAM'][]	=	array('FILD' => 'd.tag' , 	'DATA' => $cfg['DOMAIN_TAG'] ,	  'TYP' => 's');
							$resultFetchSequence  			 = $mycms->sql_select($sqlFetchSequence);	
							$rowFetchSequence     			 = $resultFetchSequence[0]['max']; 
							for($i=1; $i<=$rowFetchSequence+1; $i++)
							{
							?>
								<option value="<?=$i?>" <?=($i==$rowFetchSection['seq'])?'selected':''?>><?=$i?></option>
							<?php
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td align="left">
						
							<input name="back" type="button" class="btn btn-small btn-red" id="back" 
							 value="Back" onClick="location.href='section.php';"/>
							&nbsp;
							<input type="submit" name="Save" id="Save" value="Update Section" class="btn btn-small btn-blue">
						
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