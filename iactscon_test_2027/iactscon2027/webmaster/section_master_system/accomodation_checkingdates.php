<?php
	include_once('includes/init.php');
	page_header("Accomodation Details");
	
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
				
					hotelAddFormLayout($cfg, $mycms);
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
		<form name="frmSearch" method="post" action="accomodation_checkingdates.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_hotel" />
			<table width="100%" class="tborder" align="center">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Accomodation Listing </span>
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
							<!--<tr class="theader">
								<td width="30" align="center">
									<input name="check_all" id="check_all" targetElement="checkBoxListing" 
									 type="checkbox" onclick="checkall(this);"/>											
								</td>
								<td width="30" align="center">Sl No</td>-->
								<!--<tr class="thighlight">
								<td align="left" colspan="4">Hotel Details</td>
								</tr>-->
								<tr class="theader">
								<td  align="left">Check In Date</td>
								<!--<td width="120" align="center">Check Out Date</td>-->
								<td width="10%" align="center">Status</td>											
								<td width="10%" align="center">Action</td>
							</tr>
							<?php
							$searchCondition      = "";
							
							if($_REQUEST['src_hotel_name']!='')
							{
								$searchCondition .= " AND hotel.hotel_name LIKE '%".$_REQUEST['src_hotel_name']."%'";
							}
							
							$hotelCounter         = 0;
							
						   $sqlFetchHotel['QUERY'] 		  = "SELECT hotel.id AS hid,
																hotel.hotel_name AS hotelName ,
																hotel.status AS hotelStatus ,
																accomodation.*
														   FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." accomodation
													 INNER JOIN "._DB_MASTER_HOTEL_." hotel
															 ON hotel.id= accomodation.hotel_id
														  WHERE hotel.status !='D' ".$searchCondition."
													   ORDER BY  accomodation.hotel_id ASC , accomodation.check_in_date ASC"; 
													  
							$resultFetchHotel     = $mycms->sql_select_paginated(1, $sqlFetchHotel, 50, $restrt);	
							if($resultFetchHotel)
							{
							$var='';
								foreach($resultFetchHotel as $keyHotel=>$rowFetchHotel) 
								{
									$hotelCounter = $hotelCounter + 1;
							?>
									<!--<tr class="tlisting">
										<td align="center" valign="top">
											<input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing" 
											 value="<?=$rowFetchHotel['id']?>" />													
										</td>
										<td align="center" valign="top"><?=$hotelCounter?></td>-->
										<?
										if ($var != $rowFetchHotel['hotelName'])
										{
										$var=$rowFetchHotel['hotelName'];
										?>
									<tr class="thighlight">
										<td align="left" colspan="4"><?=$rowFetchHotel['hotelName']?></td>
									</tr>
									<?
									}
									?>
									<tr >
									
									
									
										
										<td align="left" valign="top"><?=$rowFetchHotel['check_in_date']?></td>
										<td align="center" valign="top">
										
											<a href="accomodation_checkingdates.process.php?act=<?=($rowFetchHotel['status']=='A')?'Inactive':'Active'?>&id=<?=$rowFetchHotel['id']?><?=$searchString?>" 
											 class="<?=($rowFetchHotel['status']=='A')?'ticket ticket-success':'ticket ticket-important'?>"><?=($rowFetchHotel['status']=='A')?'Active':'Inactive'?></a>													
										
										</td>
										
										<td align="center" valign="top">
										
											<a href="accomodation_checkingdates.php?show=view&id=<?=$rowFetchHotel['id']?>">
											<span title="View Hotel" class="icon-eye" /></a>
											
											<a href="accomodation_checkingdates.php?show=edit&id=<?=$rowFetchHotel['id']?>">
											<span alt="Edit" title="Edit Hotel" class="icon-pen" /></a>
										
											<?php
											if($rowFetchHotel['totalRoomAllocated']<=0 && $rowFetchHotel['totalBulkRoom']<=0)
											{
											?>	
												<!--<a href="accomodation_checkingdates.process.php?act=Remove&id=<?=$rowFetchHotel['id']?>">
												<span alt="Remove" title="Remove Hotel" class="icon-x-alt"
												 onclick="return confirm('Do You Really Want To Remove The Hotel ?');" /></a>-->
											<?php
											}
											?>
																						
										</td>
									</tr>
							<?php
								}
							} 
							else 
							{
							?>
								<tr>
									<td colspan="13" align="center">
										<span class="mandatory">No Record Present.</span>												
									</td>
								</tr>  
							<?php 
							} 
							?>
						</table>
						
					</td>
				</tr>
				<!--<tr>
					<td align="left">
					
						<select name="multiOperationSelector" id="multiOperationSelector">
							<option value="">Choose an action...</option>
							<option value="Active">Active</option>
							<option value="Inactive">Inactive</option>
							<option value="Remove">Remove</option>
						</select>
						<input value="Apply to selected"  name="submit" type="button" 
						 onclick="return multiTaskValidation('hotel_listing.process.php','<?=$searchString?>');" class="btn btn-small btn-blue"/>
					
					</td>
					<td align="right"></td>
				</tr>-->
				<tr class="tfooter">
					<td >
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
					</td>
					<td colspan="2" align="right"><a href="accomodation_checkingdates.php?show=add"><b>+Add more</b></a></td>
				</tr>
				
			</table>
		</form>
	<?php
	}
	
	/**************************************************************/
	/*                       ADD HOTEL & ROOM FORM                */
	/**************************************************************/
	function hotelAddFormLayout($cfg, $mycms)
	{
	
	$sqlhoteldetail['QUERY']="SELECT `hotel_name` FROM ".$cfg['DB.MASTER.HOTEL']."";
		$resultFetchHotel    = $mycms->sql_select($sqlhoteldetail);
		$rowFetchHotel       = $resultFetchHotel[0];
	?>
		<form name="frmInsert" id="frmInsert" action="accomodation_checkingdates.process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="act" value="insert" />		
			<table width="100%" class="tborder">
				<tr>
					<td class="tcat" colspan="2" align="left">Add New Accomodation</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						
						<table width="100%">
							<tr class="thighlight">
								<td align="left" colspan="5">Accomodation Checking Dates</td>
							</tr>
							<tr>
								<td width="20%" align="left" valign="top">Hotel Name: <span class="mandatory">*</span></td>
								<td width="30%" valign="top">
								<select name="hotel_name_add" id="hotel_name_add">	
								<?
								$sqlhoteldetail['QUERY']="SELECT * FROM ".$cfg['DB.MASTER.HOTEL']."";
								$resultFetchHotel    = $mycms->sql_select($sqlhoteldetail);
								 foreach($resultFetchHotel as $key=>$rowfetch)
								 {
								?>
								
									<option value="<?=$rowfetch['id']?>"><?=$rowfetch['hotel_name']?></option>
								<?
								}
								?>
								</select>
								<td>
								
								<td width="20%" align="left" valign="top">Check In date:</td>
								<td width="30%" valign="top" rowspan="4">
									<input type="text" name="checkin_date_add" id="checkin_date_add" style="width:80%;" rel="splDate"/>					
								</td>
							</tr>
							<tr>
								<td align="left">Check In time:</td>
								<td align="left">
									10:00am					
								</td>
								<td></td>
							</tr>
							<tr>
								<td align="left">Check Out time:</td>
								<td align="left">
									9:55am					
								</td>
								<td></td>
							</tr>
							<tr>
								<!--<td align="left">Pickup Complementary</td>
								<td align="left">
									<input type="radio" name="pickup_complementary_add" id="pickup_complementary_yes_add" 
									 value="YES" /> Yes
									&nbsp;
									<input type="radio" name="pickup_complementary_add" id="pickup_complementary_yes_add" 
									 value="NO" checked="checked" /> No
								</td>
								<td align="left"></td>-->
							</tr>
						</table>
						
					</td>
				</tr>
				<tr>
					<td width="20%"></td>
					<td align="left">
						<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  
						 onClick="location.href='accomodation_checkingdates.php';"/>
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
		$pkgId             = addslashes(trim($_REQUEST['id']));
		
		$sqlFetchHotel['QUERY'] 		  = "SELECT hotel.id AS hid,
										hotel.hotel_name AS hotelName ,
										hotel.status AS hotelStatus ,
										accomodation_checkout.*
								   FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." accomodation_checkout
							 INNER JOIN "._DB_MASTER_HOTEL_." hotel
									 ON hotel.id= accomodation_checkout.hotel_id
								  WHERE hotel.status !='D' 
									AND accomodation_checkout.id ='".$pkgId."' 
							   ORDER BY accomodation_checkout.hotel_id ASC "; 
									
			
		$resultFetchHotel    = $mycms->sql_select($sqlFetchHotel);
		$rowFetchHotel       = $resultFetchHotel[0];	
	?>
		<form name="frmUpdate" id="frmUpdate" action="accomodation_checkingdates.process.php" method="post" enctype="multipart/form-data">
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
							<tr>
								<td width="20%" align="left" valign="top">Hotel Name: <span class="mandatory">*</span></td>
								<td width="30%" valign="top">
									<input name="hotel_name_update" id="hotel_name_update" style="width:40%;" value="<?=$rowFetchHotel['hotelName']?>" readonly="true"/>
								</td>
								
							</tr>
							<tr>
								<td align="left">Check In date:</td>
								<td align="left">
									<input name="checkin_date_update" id="checkin_date_update" style="width:40%;" value="<?=$rowFetchHotel['check_in_date']?>" rel="splDate"/>					
								</td>
								<!--<td align="right">Check Out date</td>
								<td align="right">
									<input name="checkout_date_update" id="checkout_date_update" style="width:90%;" value="<?=$rowFetchHotel['check_out_date']?>" rel="tcal" />					
								</td>-->
							</tr>
							<!--<tr>
								<td align="left">Check In time</td>
								<td align="left">
									<input name="distance_from_venue_update" id="distance_from_venue_update" style="width:90%;" value="<?=$rowFetchHotel['distance_from_venue']?>" />					
								</td>
								<td align="right">Check Out time</td>
								<td align="right">
									<input name="distance_from_venue_update" id="distance_from_venue_update" style="width:90%;" value="<?=$rowFetchHotel['distance_from_venue']?>" />					
								</td>
							</tr>-->
						</table>
						
					</td>
				</tr>
				<tr>
					<td width="20%"></td>
					<td align="left">
						<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  
						 onClick="location.href='accomodation_checkingdates.php';"/>
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
	
	/**************************************************************/
	/*                  VIEW HOTEL & ROOM FORM                    */
	/**************************************************************/	
	function hotelViewFormLayout($cfg, $mycms)
	{
	$pkgId             = addslashes(trim($_REQUEST['id']));
		
	 $sqlFetchHotel['QUERY']  = "SELECT hotel.id AS hid,
										hotel.hotel_name AS hotelName ,
										hotel.status AS hotelStatus ,
										accomodation_checkin_date.*,
										accomodation_checkout_date.*
								   FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." accomodation_checkin_date

							 INNER JOIN "._DB_MASTER_HOTEL_." hotel
									 ON hotel.id= accomodation_checkin_date.hotel_id

							 INNER JOIN "._DB_ACCOMMODATION_CHECKOUT_DATE_." accomodation_checkout_date
									 ON accomodation_checkout_date.id=  hotel.id

								  WHERE hotel.status !='D' 
								    AND accomodation_checkin_date.id ='".$pkgId."' 
								    AND accomodation_checkout_date.check_out_date > accomodation_checkin_date.check_in_date 
							   ORDER BY accomodation_checkin_date.hotel_id DESC "; 
							    
		
		$resultFetchHotel    = $mycms->sql_select($sqlFetchHotel);
		$rowFetchHotel       = $resultFetchHotel[0];
		//print_r($resultFetchHotel);die();
	?>
		<table width="100%" class="tborder">
			<tr>
				<td class="tcat" colspan="2" align="left">View Hotel Details</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					
					<table width="100%">
						<tr class="thighlight">
							<td align="left" colspan="5">Hotel Details</td>
						</tr>
						<tr>
							<td width="20%" align="left" valign="top">Hotel Name</td>
							<td width="30%" valign="top"><b><?=$rowFetchHotel['hotelName']?></b></td>
							<!--<td width="20%" align="left" valign="top">Check In date</td>
							<td width="30%" valign="top" rowspan="4"><?=nl2br($rowFetchHotel['check_in_date'])?></td>-->
						</tr>
						<tr>
						<td width="20%" align="left" valign="top">Check In date:</td>
							<td width="30%" valign="top" ><?=nl2br($rowFetchHotel['check_in_date'])?></td>
							<td align="left">Check Out date:</td>
							<td align="left"><?=$rowFetchHotel['check_out_date']?></td>
							<td>
						</tr>
					</table>
					
				</td>
			</tr>
			<tr>
				<td width="20%"></td>
				<td align="left">
					<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"  
					 onClick="location.href='accomodation_checkingdates.php';"/>				
				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
	<?php
	}
	?>