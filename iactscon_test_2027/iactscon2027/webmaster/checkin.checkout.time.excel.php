<?php
	include_once('includes/init.php');
	ini_set('max_execution_time', 1000);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/octet-stream");
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=userexcelreport".time().".xls");
	include_once(__DIR__. "/../includes/function.accommodation.php");

	?>
	<table width="100%" border="1">
			<tr>
				<td colspan="2" class="tcat">Delegate Check-In and Check-Out Time
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">
					<table width="100%" border="1">
						<tr >
							<td width="50" align="center">Sl No.</td>
							<td align="left"  width="100">Delegate Name</td>
							<td width="100" align="center">User Details</td>
							<td width="150" align="center">Hotel</td>
							<td width="150" align="center">Room Type</td>
							<td width="150" align="center">Package</td>
							<td width="150" align="center">Cheack-in Date</td>
							<td width="150" align="center">Cheack-out Date</td>
							<td width="150" align="center">Invoice Mode</td>
							
						</tr>
						<?php					
						
										
						
						$delegateCounter              = 0;
						  $sqlFetchWorkshopBooking = array();
						 $sqlFetchWorkshopBooking['QUERY'] 	  = "SELECT req.*, delegate.user_full_name, delegate.user_unique_sequence, 
																   delegate.user_registration_id, delegate.user_mobile_no, delegate.user_email_id,pckg.package_name,inv.payment_status
																    FROM "._DB_REQUEST_ACCOMMODATION_." req
																	LEFT OUTER JOIN "._DB_ACCOMMODATION_PACKAGE_." pckg
																		ON pckg.id = req.package_id
																	INNER JOIN "._DB_INVOICE_." inv 
																		ON req.refference_invoice_id = inv.id
																	INNER JOIN "._DB_USER_REGISTRATION_." delegate
																		ON delegate.id = req.user_id
																		WHERE delegate.status = 'A'
																			 AND inv.status = 'A'
																			 AND inv.service_type IN ('DELEGATE_ACCOMMODATION_REQUEST','DELEGATE_RESIDENTIAL_REGISTRATION')  ORDER BY req.checkin_date";
							
						
			$resultWorkshopBooking        = $mycms->sql_select($sqlFetchWorkshopBooking);
		
			if($resultWorkshopBooking)
			{
				$delegateCounter=1;
				foreach($resultWorkshopBooking as $key=>$rowfetch)
				{
					 $sqlRoom = array();
					$sqlRoom['QUERY'] = "SELECT `accessories_name` FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . " 
										WHERE `id` = ? AND status='A' AND purpose='room' ORDER BY `id` ASC";
					$sqlRoom['PARAM'][] = array('FILD' => 'id', 'DATA' => $rowfetch['roomTypeId'], 'TYP' => 's');
						$querySlider = $mycms->sql_select($sqlRoom, false);
			?>
					<tr class="tlisting">
						<td align="center"><?=$delegateCounter?></td>
						<td align="left"><?=strtoupper($rowfetch['user_full_name'])?> </td>
						<td align="center">
						<?=strtoupper($rowfetch['user_unique_sequence'])?><br />
						<?=strtoupper($rowfetch['user_registration_id'])?><br />
						<?=strtoupper($rowfetch['user_email_id'])?>
						</td>
						<td align="center"><?=getHotelNameByID($rowfetch['hotel_id'])?></td>
						<td align="center"><?=$querySlider[0]['accessories_name']?></td>
						<td align="center"><?=strtoupper($rowfetch['package_name'])?></td>
						<td align="center"><span  style="color:#000099;"><?=strtoupper($rowfetch['checkin_date'])?></span></td>
						<td align="center"><span  style="color:#000099;"><?=strtoupper($rowfetch['checkout_date'])?></span></td>
						<td align="center">
							<span  style="color:<?=$rowfetch['payment_status']=='PAID'?'#5E8A26':'#f81e1e'?>;">	<?=strtoupper($rowfetch['payment_status'])?></span>
						</td>
					</tr>
					<?
					$delegateCounter++;
					}
			}
			else
			{
			?>
				<tr>
					<td colspan="7" align="center">
						<span class="mandatory">No Record(s) Found !!</span>
					</td>
				</tr>
			<?php
			}
			?>
					</table>
					
				</td>
			</tr>
			
			
		</table>
<?php
exit();
	
	
?>			
