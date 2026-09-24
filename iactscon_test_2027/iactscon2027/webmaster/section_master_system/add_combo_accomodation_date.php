<?php
	include_once('includes/init.php');
	page_header("combo accomodation Date");
	
	$pageKey        = "_pgn1_";
	
	$pageKeyVal     = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString  = "";
	$searchArray    = array();
	
	$searchArray[$pageKey]                     = $pageKeyVal;
	$searchArray['src_hotel_name']             = addslashes(trim($_REQUEST['src_hotel_name']));
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
?>
	<script type="text/javascript" language="javascript" src="scripts/hotel_listing.js"/></script>
	<div class="container">
		<?php 
			switch($show){
				
				// HOTEL ADD FORM LAYOUT
				case'add':
				
					accomodationDate($cfg, $mycms);
					break;
					
				// HOTEL EDIT FORM LAYOUT
				case'edit':
				
					hotelEditFormLayout($cfg, $mycms);
					break;
					
				// HOTEL VIEW FORM LAYOUT	
				case'view':
				
					hotelViewFormLayout($cfg, $mycms);
					break;
																						
				// HOTEL LISTING LAYOUT
				default:
					
					hotelListingLayout($cfg, $mycms); 
					break;				
			} 
		?>
	</div>
<?php

	page_footer();
	
	/**************************************************************/
	/*                       HOTEL & ROOM LISTING                 */
	/**************************************************************/
	function hotelListingLayout($cfg, $mycms)
	{
	?>
		<form name="frmSearch" method="post" action="accomodation_combo_date.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_hotel" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Date Listing </span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>				
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
									
						<div class="tsearch">
							<table width="100%">
								<tr>
									<td align="right">
										Hotel Name: 
										
										<input type="text" name="src_hotel_name" id="src_hotel_name" value="<?=$_REQUEST['src_hotel_name']?>" />		
										 
										<?php
										searchStatus();
										?>
										
										<input type="submit" name="SaveSearch" id="SaveSearch" value="Search" class="btn btn-small btn-blue" />
								   </td>
								</tr>
							</table>
						</div>						
						<table width="100%">
						<? 
							$sqlFetchHotel				=	array();
							$sqlFetchHotel['QUERY']     = "SELECT DISTINCT hotel_id 
														      FROM "._DB_COMBO_CHECKIN_DATE_."
														     WHERE `status` != ?";
							$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'D' , 		'TYP' => 's');
							$resultFetchHotel    		= $mycms->sql_select($sqlFetchHotel);
							
							if($resultFetchHotel){
						?>	
							<tr class="theader">
								<td width="30" align="center">Sl No</td>
								<td align="left">Hotel Name</td>
								<td width="120" align="center">Check In Date</td>
								<td width="120" align="center">Check Out Date</td>
								<td width="60" align="center">Status</td>											
								<!-- <td width="60" align="center">Action</td> -->
							</tr>
							<?php
								$searchCondition      = "";
								
								if($_REQUEST['src_hotel_name']!='')
								{
									$searchCondition .= " AND hotel_name LIKE ?";
									$searchParams['hotel_name'] = '%'.$_REQUEST['src_hotel_name'].'%';
								}

								
								$hotelCounter	= 1;
								//echo $hotelCounter."<pre>";print_r($resultFetchHotel);echo "</pre>";
								foreach($resultFetchHotel as $key=>$rowFetchHotel ){
									$sql			=	array();
									$sql['QUERY'] 	= "SELECT *								
									        		     FROM "._DB_MASTER_HOTEL_."
									        		    WHERE id = ?
									        		      AND status = ? ".$searchCondition;

									$sql['PARAM'][]	=	array('FILD' => 'id' , 		'DATA' => $rowFetchHotel['hotel_id'] , 	'TYP' => 's');
									$sql['PARAM'][]	=	array('FILD' => 'status' , 	'DATA' => 'A' , 						'TYP' => 's');
									
									foreach($searchParams as $paramName=>$val)
									{
										$sql['PARAM'][]	=	array('FILD' => $paramName , 	'DATA' => $val , 				'TYP' => 's');
									}
								
									$result = $mycms->sql_select($sql);	 
									
									$rowFetchHotelName           = $result[0];
									
									if($rowFetchHotelName){
										$sql1			=	array();
										$sql1['QUERY'] = "SELECT min(`check_in_date`) AS checkin								
										        		  FROM "._DB_COMBO_CHECKIN_DATE_."
										        		  WHERE hotel_id = ?
										        		    AND status != ?";
										$sql1['PARAM'][]	=	array('FILD' => 'hotel_id' , 'DATA' => $rowFetchHotel['hotel_id'] , 'TYP' => 's');
										$sql1['PARAM'][]	=	array('FILD' => 'status' , 	 'DATA' => 'D' , 						'TYP' => 's');	
										$result = $mycms->sql_select($sql1);	 
										$rowFetchcheckIn           = $result[0];

										$sql2			=	array();
										$sql2['QUERY'] = "SELECT max(`check_out_date`) AS checkout								
										        		  FROM "._DB_COMBO_CHECKOUT_DATE_."
										        		  WHERE hotel_id = ?
										        		    AND status != ?";
										
										$sql2['PARAM'][]	=	array('FILD' => 'hotel_id' , 'DATA' => $rowFetchHotel['hotel_id'] , 'TYP' => 's');
										$sql2['PARAM'][]	=	array('FILD' => 'status' , 	 'DATA' => 'D' , 						'TYP' => 's');	
										$result = $mycms->sql_select($sql2);	 
										$rowFetchcheckOut           = $result[0];
										
									?>
										<tr class="tlisting">
											<td align="center" valign="top"><?=$hotelCounter?></td>
											<td align="left" valign="top"><?=$rowFetchHotelName['hotel_name']?></td>
											<td align="center" valign="top"><?=$rowFetchcheckIn['checkin']?></td>
											<td align="center" valign="top"><?=$rowFetchcheckOut['checkout']?></td>
											<td align="center" valign="top">
												<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="add_combo_accomodation_date.php?show=edit&id=<?=$rowFetchHotel['hotel_id']?>">
												<span alt="Edit" title="Edit Hotel" class="icon-pen"></span></a>
												<a href="accomodation_combo_date.process.php?act=Remove&id=<?=$rowFetchHotel['hotel_id']?>">
												<span alt="Remove" title="Remove Hotel" class="icon-x-alt" onclick="return confirm('Do You Really Want To Remove The Hotel ?');" /></a>	
											</td>
										</tr>								
										<?
										$hotelCounter++;
									}
									/*else{
										echo "kk";
									?>
									<tr>
										<td colspan="13" align="center">
											<span class="mandatory">No Record Present.</span>												
										</td>
									</tr> 
								<?
									}*/
									
							}
						}
						else{
							?>
							<tr>
								<td colspan="13" align="center">
									<span class="mandatory">No Record Present.</span>												
								</td>
							</tr> 
					<?
						}
					?>

						</table>
				<tr>
					<td colspan="5" align="right"><a href="add_combo_accomodation_date.php?show=add">Add new</a></td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
				</tr>
				</td>
				</tr>
			</table>
		</form>
	<?php
	}
	
	/**************************************************************/
	/*                       ADD HOTEL & ROOM FORM                */
	/**************************************************************/
	function accomodationDate($cfg, $mycms)
	{
	?>
		<form name="frmInsert" id="frmInsert" action="accomodation_combo_date.process.php" method="post" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="insert" />		
			<table width="100%" class="tborder">
				<tr>
					<td class="tcat" colspan="2" align="left">Add New Date</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<table width="100%">
							
							<tr class="thighlight">
								<td align="left">Hotel Name</td>
								<td>
									<select name="hotel_id" id="hotel_id" label="Section Name" 
									 validate="IsSel()" style="width:90%;" required="">
									<option value="">-- Select Section --</option>
									<?php 
									$sql 		    =	array();
									$sql['QUERY']   = "SELECT *								
									        		  FROM "._DB_MASTER_HOTEL_."
									        		  WHERE `status` != ? ";
									$sql['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'D' , 		'TYP' => 's');
									$result = $mycms->sql_select($sql);	 
									foreach($result as $rMidI=>$row_mod)
									{
									?>
										<option value="<?=$row_mod['id']?>"><?=$row_mod['hotel_name']?></option>
									<?php 
									}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="left">Check In Date</td>
								<td align="left">
									<input type="date" name="check_in" id="check_in" style="width:90%;" required="" onchange="return checkInValid();" />					
								</td>
								<td></td>
							</tr>
							<tr>
								<td align="left">Check Out Date</td>
								<td align="left">
									<input type="date" name="check_out" id="check_out" style="width:90%;" required="" onchange="return checkInValid();" />					
								</td>
								<td></td>
							</tr>
						</table>
						
					</td>
				</tr>
				<tr>
					<td width="20%"></td>
					<td align="left">
						<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  
						 onClick="location.href='add_accomodation_date.php';"/>
						&nbsp;
						<input type="submit" name="Save" id="Save" value="Add" class="btn btn-small btn-blue">						
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
		</form>
	<?php
	}
		
	/**************************************************************/
	/*                  EDIT HOTEL & ROOM FORM                    */
	/**************************************************************/
	function hotelEditFormLayout($cfg, $mycms)
	{
		$hotelId             		  = addslashes(trim($_REQUEST['id']));
		
		$sqlFetchHotel 		    	  =	array();
		$sqlFetchHotel['QUERY']       = "SELECT hotel.* 
									      FROM "._DB_MASTER_HOTEL_." hotel 
									     WHERE `status` != ? 
										   AND `id` = ? ";
		$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'D' , 			'TYP' => 's');
		$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'id' , 		    'DATA' => $hotelId , 		'TYP' => 's');
		$resultFetchHotel    = $mycms->sql_select($sqlFetchHotel);
		$rowFetchHotel       = $resultFetchHotel[0];
	?>
	<script>
		function updateConfirmation(){
			return onSubmitAction(function(){
				var result = confirm("Want to Update?");
				if (result) {
				    return true;
				}
				else{
					return false;
				}
			});
		}
	</script>
		<form name="frmUpdate" id="frmUpdate" action="accomodation_combo_date.process.php" method="post" enctype="multipart/form-data" onsubmit="return updateConfirmation();">
			<input type="hidden" name="act" id="act" value="update" />
			<input type="hidden" name="id" id="id" value="<?=$rowFetchHotel['id']?>" />
			<table width="100%" class="tborder">
				<tr>
					<td class="tcat" colspan="2" align="left">Update Hotel Details</td>
				</tr>		
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<table width="100%">
							<tr class="thighlight">
								<td align="left" colspan="4">Hotel Details</td>
							</tr>
							<?php
							$sql 		    	  =	array();
							$sql['QUERY'] = "SELECT min(`check_in_date`) AS checkin								
							        		  FROM "._DB_COMBO_CHECKIN_DATE_."
							        		  WHERE hotel_id = ?
							        		    AND status != ?";
							$sql['PARAM'][]	=	array('FILD' => 'hotel_id' , 	'DATA' => $rowFetchHotel['id'] , 'TYP' => 's');
							$sql['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'D' , 				 'TYP' => 's');	
							$result = $mycms->sql_select($sql);	 
							$rowFetchcheckIn           = $result[0];

							$sql 		    	  =	array();
							$sql['QUERY'] = "SELECT max(`check_out_date`) AS checkout								
							        		  FROM "._DB_COMBO_CHECKOUT_DATE_."
							        		  WHERE hotel_id = ?
							        		    AND status !=?";
							
							$sql['PARAM'][]	=	array('FILD' => 'hotel_id' , 	'DATA' => $rowFetchHotel['id'] , 'TYP' => 's');
							$sql['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'D' , 				 'TYP' => 's');		
							$result = $mycms->sql_select($sql);	 
							$rowFetchcheckOut           = $result[0];
										
						?>
							<tr>
								<td width="20%" align="left" valign="top">Hotel Name <span class="mandatory">*</span></td>
								<td width="30%" valign="top">								
									<select name="hotel_id" id="hotel_id" label="Section Name" 
									 validate="IsSel()" style="width:90%;" required="">									
										<option value="<?=$rowFetchHotel['id']?>"><?=$rowFetchHotel['hotel_name']?></option>	
									</select>								
								</td>
							</tr>
							<tr>
								<td align="left">Check In Date</td>
								<td align="left">
									<input type="date" name="check_in" id="check_in" style="width:90%;" required=""  value="<?=$rowFetchcheckIn['checkin']?>" onchange="checkInValid();"/>					
								</td>
								<td></td>
							</tr>
							<tr>
								<td align="left">Check Out Date</td>
								<td align="left">
									<input type="date" name="check_out" id="check_out" style="width:90%;" required=""  value="<?=$rowFetchcheckOut['checkout']?>" onchange="checkInValid();"/>					
								</td>
								<td></td>
							</tr>
						</table>						
					</td>
				</tr>
				<tr>
					<td width="20%"></td>
					<td align="left">
						<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  
						 onClick="location.href='hotel_listing.php';"/>
						&nbsp;
						<input type="submit" name="Save" id="Save" value="Update" class="btn btn-small btn-blue">						
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
		</form>
	<?php
	}
	?>
	<script>
		function checkInValid(){
			var check_in = $('#check_in').val();
			var checkout = $('#check_out').val();
			if(checkout !=""){
				if(check_in > checkout){
					alert('Checkin date should be less than checkout date!!');
					$('#check_out').val('');
				}
			else{
				//return true;
				}
			}
		}
	</script>