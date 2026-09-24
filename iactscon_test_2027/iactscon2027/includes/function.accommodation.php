<?php
function getTotalAccommodationCount($delegatesId)
{
	global $cfg, $mycms;
	
	$sqlFetch['QUERY']			  = "SELECT COUNT(*) AS totalCount
									   FROM "._DB_REQUEST_ACCOMMODATION_."
									  WHERE status = 'A'
										AND user_id = '".$delegatesId."'";
	
	$result			      = $mycms->sql_select($sqlFetch);
	return $result[0]['totalCount'];
}

function getTotalAccommodationWithoutCombo($delegatesId)
{
	global $cfg, $mycms;
	
	$sqlFetch['QUERY']			  = "SELECT ACC.id,ACC.accommodation_details,ACC.checkin_date,ACC.checkout_date,ACC.checkout_date, 
	ACC.hotel_id, ACC.package_id
									   FROM "._DB_REQUEST_ACCOMMODATION_." ACC INNER JOIN "._DB_USER_REGISTRATION_." U ON ACC.user_id=U.id
									  WHERE U.status = 'A' AND ACC.status = 'A' 
										AND ACC.user_id = '".$delegatesId."'";
	
	$result		= $mycms->sql_select($sqlFetch);
	
	return $result;
}

function getAccommodationMaxRoom($delegateId)
{

	
	global $cfg, $mycms;
	$sqlFetch['QUERY']			  = "SELECT max(rooms_no) AS rooms_no
									   FROM "._DB_REQUEST_ACCOMMODATION_." WHERE status = 'A' 
										AND user_id = '".$delegateId."'";
	
	

	$result		= $mycms->sql_select($sqlFetch);

	if(!empty($result[0]['rooms_no']))
	{
		$accomodation_room = $result[0]['rooms_no'];
	}
	else
	{
		$accomodation_room = 0;
	}
	
	return $accomodation_room;
}

function getAccommodationRoomExist($delegateId)
{
	global $cfg, $mycms;
	$sqlFetch['QUERY']			  = "SELECT COUNT(*) AS COUNTDATA
									   FROM "._DB_REQUEST_ACCOMMODATION_." WHERE status = 'A' 
										AND user_id = '".$delegateId."'";
	
	

	$result		= $mycms->sql_select($sqlFetch);

	return $result[0]['COUNTDATA'];
}


function getRoomsByID($acc_id, $delegateId)
{

	
	global $cfg, $mycms;
	$sqlFetch['QUERY']			  = "SELECT room_id
									   FROM "._DB_MASTER_ROOM_." WHERE status = 'A' 
										AND user_id = '".$delegateId."' AND request_accommodation_id='".$acc_id."'";
	
	

	$result		= $mycms->sql_select($sqlFetch);

	if(!empty($result[0]['room_id']))
	{
		$accomodation_room = $result[0]['room_id'];
	}

	return $accomodation_room;
}

function getAllCheckInId($hotel_id, $checkin_date)
{
	global $cfg, $mycms;
	$sql['QUERY'] = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." WHERE check_in_date='".$checkin_date."' AND hotel_id= '".$hotel_id."' AND status='A'";

	$result		= $mycms->sql_select($sql);
	return $result;
}

function getVariableCheckInId($hotel_id, $checkin_date, $checkout_date)
{
 	$explodeCheckIn = explode('-',$checkin_date);

    $explodeCheckInDay = $explodeCheckIn[2];

    $newCheckInDate = $explodeCheckIn[0]."-".$explodeCheckIn[1]."-".$explodeCheckInDay;

 	$explodeCheckOut = explode('-',$checkout_date);
    $explodeCheckOutDay = $explodeCheckOut[2] - 1;
    $newCheckOutDate = $explodeCheckIn[0]."-".$explodeCheckIn[1]."-".$explodeCheckOutDay;

  

    	
    	global $cfg, $mycms;
		$sql['QUERY'] = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." WHERE check_in_date BETWEEN '".$newCheckInDate."' AND '".$newCheckOutDate."' AND hotel_id= '".$hotel_id."' AND status='A'";

		$result		= $mycms->sql_select($sql);
		
   
     return $result;
}

function getAllCombinationCheckInID($room_id ,$delegateId, $hotel_id="")
{
		global $cfg, $mycms;
		$sql['QUERY'] = "SELECT * FROM "._DB_MASTER_ROOM_." WHERE user_id = '".$delegateId."' AND room_id ='".$room_id."'  AND status='A'";
		//print_r($sql);
		$result		= $mycms->sql_select($sql);

		 $checkinIdArray = array();
		 $checkoutIdArray = array();

		 foreach ($result as $key => $value) {
		 	$explodeCheckIn = explode('-',$value['checkin_date']);

		    $explodeCheckInDay = $explodeCheckIn[2];

		    $newCheckInDate = $explodeCheckIn[0]."-".$explodeCheckIn[1]."-".$explodeCheckInDay;

		 	$explodeCheckOut = explode('-',$value['checkout_date']);
		    $explodeCheckOutDay = $explodeCheckOut[2] - 1;
		    $newCheckOutDate = $explodeCheckIn[0]."-".$explodeCheckIn[1]."-".$explodeCheckOutDay;

		    $diff = strtotime($value['checkout_date']) - strtotime($value['checkin_date']);
  			$days = abs(round($diff / 86400));
  			if($days>1)
  			{
  				$sql['QUERY'] = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." WHERE check_in_date BETWEEN '".$newCheckInDate."' AND '".$newCheckOutDate."' AND hotel_id= '".$hotel_id."' AND status='A'";

				$result		= $mycms->sql_select($sql);

				foreach ($result as $key => $val) {
					array_push($checkinIdArray,$val['id']);
				}
  			}
  			else
  			{
  				array_push($checkinIdArray,$value['checkin_id']);
  			}
		 	
		 }

		return $checkinIdArray;
}

function getAllCombinationCheckOutID($room_id ,$delegateId, $hotel_id="")
{
		global $cfg, $mycms;
		$sql['QUERY'] = "SELECT * FROM "._DB_MASTER_ROOM_." WHERE user_id = '".$delegateId."' AND room_id ='".$room_id."'  AND status='A'";
		//print_r($sql);
		$result		= $mycms->sql_select($sql);

		 $checkinIdArray = array();
		 $checkoutIdArray = array();

		 foreach ($result as $key => $value) {

		 	

		 	$diff = strtotime($value['checkout_date']) - strtotime($value['checkin_date']);
  			$days = abs(round($diff / 86400));
  			if($days>1)
  			{
  				$explodeCheckIn = explode('-',$value['checkin_date']);

			    $explodeCheckInDay = $explodeCheckIn[2] + 1;

			   $newCheckInDate = $explodeCheckIn[0]."-".$explodeCheckIn[1]."-".$explodeCheckInDay;

			 	$explodeCheckOut = explode('-',$value['checkout_date']);
			    $explodeCheckOutDay = $explodeCheckOut[2];
			    $newCheckOutDate = $explodeCheckIn[0]."-".$explodeCheckIn[1]."-".$explodeCheckOutDay;

			    $sql['QUERY'] = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." WHERE check_out_date BETWEEN '".$newCheckInDate."' AND '".$newCheckOutDate."' AND hotel_id= '".$hotel_id."' AND status='A'";

				$result = $mycms->sql_select($sql);

				foreach ($result as $key => $val) {
					array_push($checkoutIdArray,$val['id']);
				}

				//print_r($sql);

				
  			}
  			else
  			{
  				array_push($checkoutIdArray,$value['checkout_id']);
  			}
		 	
		 }

		return $checkoutIdArray;
}




function getVariableCheckOutId($hotel_id, $checkin_date, $checkout_date)
{
 	$explodeCheckIn = explode('-',$checkin_date);

    $explodeCheckInDay = $explodeCheckIn[2] + 1;

   $newCheckInDate = $explodeCheckIn[0]."-".$explodeCheckIn[1]."-".$explodeCheckInDay;

 	$explodeCheckOut = explode('-',$checkout_date);
    $explodeCheckOutDay = $explodeCheckOut[2];
    $newCheckOutDate = $explodeCheckIn[0]."-".$explodeCheckIn[1]."-".$explodeCheckOutDay;

	global $cfg, $mycms;
	$sql['QUERY'] = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." WHERE check_out_date BETWEEN '".$newCheckInDate."' AND '".$newCheckOutDate."' AND hotel_id= '".$hotel_id."' AND status='A'";

	$result		= $mycms->sql_select($sql);
	//print_r($result);

 return $result;
}



function getAllCheckOutId($hotel_id, $checkin_date)
{
	global $cfg, $mycms;
	$sql['QUERY'] = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." WHERE check_out_date='".$checkin_date."' AND hotel_id= '".$hotel_id."' AND status='A'";

	$result		= $mycms->sql_select($sql);
	return $result;
}

function getPackageNameById($package_id)
{
	global $cfg, $mycms;
	$sql['QUERY'] = "SELECT package_name FROM "._DB_ACCOMMODATION_PACKAGE_." WHERE id='".$package_id."'  AND status='A'";

	$result		= $mycms->sql_select($sql);
	return $result[0]['package_name'];
}

function accommodationsDetailsofDelegate($user_id)
{
	global $cfg, $mycms;
	 $sqlFetchWorkshopBooking = array();
	 $sqlFetchWorkshopBooking['QUERY'] 	  = "  SELECT accomod.*, packageAcc.package_name, 
													hotel.hotel_name as hotel_name		  
												 FROM "._DB_REQUEST_ACCOMMODATION_." accomod
																   
									LEFT OUTER JOIN "._DB_PACKAGE_ACCOMMODATION_." packageAcc
												 ON packageAcc.id =  accomod.package_id
									LEFT OUTER JOIN "._DB_MASTER_HOTEL_." hotel
												 ON hotel.id= accomod.hotel_id	
											WHERE accomod.user_id	= ".$user_id."
											AND accomod.status = 'A'";
		$result			      = $mycms->sql_select($sqlFetchWorkshopBooking);
		return 	$result[0];								
}

function getCheckInDate($hotelId="")
{
	global $cfg, $mycms;
	?>
	<option value="">-- Choose Check In Date --</option>
	<?php
	
	$sqlDates['QUERY']	 		  = "SELECT hotel.id AS hid,
									hotel.hotel_name AS hotelName ,
									hotel.status AS hotelStatus ,
									accomodation.*
							   FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." accomodation
						 INNER JOIN "._DB_MASTER_HOTEL_." hotel
								 ON hotel.id= accomodation.hotel_id
							  WHERE hotel.status !='D'
								AND accomodation.hotel_id ='".$hotelId."'
						   ORDER BY  accomodation.hotel_id ASC , accomodation.check_in_date ASC";
	
	$resDates		    	= $mycms->sql_select($sqlDates);
	
	$sqlMaxDates['QUERY']	 		  = "SELECT MAX(accomodation.check_in_date) AS maxDate
								   FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." accomodation
							 INNER JOIN "._DB_MASTER_HOTEL_." hotel
									 ON hotel.id= accomodation.hotel_id
								  WHERE hotel.status !='D'
									AND accomodation.hotel_id ='".$hotelId."'
							   ORDER BY  accomodation.hotel_id ASC , accomodation.check_in_date ASC";
	$resMaxDates		   	= $mycms->sql_select($sqlMaxDates);	
	$maxDate				= $resMaxDates[0]['maxDate'];
						   
	if($resDates)
	{
		foreach($resDates as $keyDates=>$rowDates)
		{
			if(strtotime($maxDate) > strtotime($rowDates['check_in_date']))
			{
			?>
				<option value="<?= $rowDates['id']?>"><?=$rowDates['check_in_date']?></option>
			<?php
			}
		}
	}
}

function getCheckOutDate($dateId="",$hotelId="")
{
	global $cfg, $mycms;
	?>
	<option value="">-- Choose Check Out Date --</option>
	<?php
	$date = getAccomDate($dateId);
	$sqlDates['QUERY']	 		  = "SELECT hotel.id AS hid,
									hotel.hotel_name AS hotelName ,
									hotel.status AS hotelStatus ,
									accomodation.*
							   FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." accomodation
						 INNER JOIN "._DB_MASTER_HOTEL_." hotel
								 ON hotel.id= accomodation.hotel_id
							  WHERE hotel.status !='D'
								AND accomodation.hotel_id ='".$hotelId."'
						   ORDER BY  accomodation.hotel_id ASC , accomodation.check_in_date ASC";
	
	$resDates		    	= $mycms->sql_select($sqlDates);
	if($resDates)
	{
		foreach($resDates as $keyDates=>$rowDates)
		{
			if(strtotime($date) < strtotime($rowDates['check_in_date']))
			{
			?>
			<option value="<?=$rowDates['id']?>"><?=$rowDates['check_in_date']?></option>
			<?php
			}
		}
	}
}

function getCheckInDateById($dateId,$hotelId)
{
	global $cfg, $mycms;
	
     $sqlTarrif      = array();
	 $sqlTarrif['QUERY']		   = "SELECT *
										FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
									   WHERE id = ?
									   AND `hotel_id` =?";
									   
	$sqlTarrif['PARAM'][]  = array('FILD' => 'id', 'DATA' =>$dateId,     'TYP' => 's');
	$sqlTarrif['PARAM'][]  = array('FILD' => 'id', 'DATA' =>$hotelId,     'TYP' => 's');

	$resRegclsf = $mycms->sql_select($sqlTarrif);
	
	return $resRegclsf[0]['check_in_date'];
}

function getCheckOutDateById($dateId,$hotelId)
{
	global $cfg, $mycms;
	
     $sqlTarrif      = array();
	 $sqlTarrif['QUERY']		   = "SELECT *
										FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." 
									   WHERE id = ?
									   AND `hotel_id` =?";
									   
	$sqlTarrif['PARAM'][]  = array('FILD' => 'id', 'DATA' =>$dateId,     'TYP' => 's');
	$sqlTarrif['PARAM'][]  = array('FILD' => 'id', 'DATA' =>$hotelId,     'TYP' => 's');

	$resRegclsf = $mycms->sql_select($sqlTarrif);
	
	return $resRegclsf[0]['check_out_date'];
}

function getAccommodationTariffId($packageId,$roomTypeId="",$checkInDate,$checkOutDate,$cutoffId)
{
	
	global $cfg, $mycms;
	$sqlTarrif = array();
	$sqlTarrif['QUERY']		   = "SELECT *
							FROM "._DB_TARIFF_ACCOMMODATION_." 
						   WHERE package_id = '".$packageId."'
							 AND roomTypeId = '".$roomTypeId."'
							  AND checkin_date_id = '".$checkInDate."'
							 AND checkout_date_id = '".$checkOutDate."'
							 AND tariff_cutoff_id = '".$cutoffId."' AND status='A'";
   
	$resRegclsf = $mycms->sql_select($sqlTarrif);
	
	return $resRegclsf[0]['id'];
}

function getAccommodationTariff($hotelId,$packageId,$checkInDate,$checkOutDate,$cutoffId)
{
	
	global $cfg, $mycms;
	$sqlTarrif = array();
	$sqlTarrif['QUERY']		   = "SELECT *
									FROM "._DB_TARIFF_ACCOMMODATION_." 
								   WHERE hotel_id = '".$hotelId."'
									 AND package_id = '".$packageId."'
									 AND checkin_date_id = '".$checkInDate."'
									 AND checkout_date_id = '".$checkOutDate."'
									 AND tariff_cutoff_id = '".$cutoffId."' AND status='A'";

	$resRegclsf = $mycms->sql_select($sqlTarrif);
	
	return $resRegclsf[0];
}

function getAccomPackageName($packageId="")
{
	
	global $cfg, $mycms;
	
	$sqlPackage['QUERY']	= " SELECT package.*, package.package_name as package_name,
									   hotel.hotel_name
									   
								  FROM "._DB_ACCOMMODATION_PACKAGE_." package 
								  
							INNER JOIN "._DB_MASTER_HOTEL_." hotel
									ON package.hotel_id = hotel.id
								 WHERE hotel.status = 'A'
								   AND package.id = '".$packageId."'";
									
	$resPackage		    	= $mycms->sql_select($sqlPackage);
	$rowPackage				= $resPackage[0];
	if(($rowPackage['hotel_name'] != '') && ($rowPackage['package_name'] != ''))
	{
		$packageName			= $rowPackage['hotel_name'].", ".$rowPackage['package_name'];
	}
	else if(($rowPackage['hotel_name'] != '') && ($rowPackage['package_name'] == ''))
	{
		$packageName			= $rowPackage['hotel_name'];
	}
	else
	{
		$packageName           ="-";
	}
	return strip_tags($packageName);
}

function getAccomDate($dateId="")
{		
	global $cfg, $mycms;
	
	$sqlDates['QUERY']	 		  = "SELECT hotel.id AS hid,
									hotel.hotel_name AS hotelName ,
									hotel.status AS hotelStatus ,
									accomodation.*
							   FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." accomodation
						 INNER JOIN "._DB_MASTER_HOTEL_." hotel
								 ON hotel.id= accomodation.hotel_id
							  WHERE hotel.status !='D'
								AND accomodation.id ='".$dateId."'
						   ORDER BY  accomodation.hotel_id ASC , accomodation.check_in_date ASC";
	
	$resDates		    	= $mycms->sql_select($sqlDates);
	$rowDates				= $resDates[0];
	
	return $rowDates['check_in_date'];
}

function getAccomodationDetails($reqId,$status=false)
{
	global $cfg, $mycms;
	if($status)
	{
		$searchCondition = "AND status IN('A','C')";
	}
	else
	{
		$searchCondition = "AND status = 'A'";
	}
	
	$sqlDetails['QUERY'] = "SELECT * FROM "._DB_REQUEST_ACCOMMODATION_." WHERE `id` = '".$reqId."' ".$searchCondition." ";
	//print_r($sqlDetails);
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0];
}

function getAccomodationDetailsOfDelegate($delegateId)
{
	global $cfg, $mycms;
	
	$sqlDetails = array();
	$sqlDetails['QUERY'] = "SELECT * FROM "._DB_REQUEST_ACCOMMODATION_." 
							WHERE status = ?  
							 AND `user_id` = ?";
							 
	$sqlDetails['PARAM'][]   = array('FILD' => 'status',    'DATA' =>'A',         'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'user_id',   'DATA' =>$delegateId, 'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails;
}

function getHotelNameByID($hotelID)
{
	global $cfg, $mycms;
	
	$sqlDetails = array();
	$sqlDetails['QUERY'] = "SELECT hotel_name FROM "._DB_MASTER_HOTEL_." 
							WHERE status = ?  
							 AND `id` = ?";
							 
	$sqlDetails['PARAM'][]   = array('FILD' => 'status',    'DATA' =>'A',         'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'id',   'DATA' =>$hotelID, 'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0]['hotel_name'];
}

function minCheckInDateByHotelID($hotel_id)
{
	global $cfg, $mycms;
	
	$sqlDetails = array();
	$sqlDetails['QUERY'] = "SELECT min(check_in_date) AS check_in_date FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
							WHERE status = ?  
							 AND `hotel_id` = ?";
							 
	$sqlDetails['PARAM'][]   = array('FILD' => 'status',    'DATA' =>'A',         'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'hotel_id',   'DATA' =>$hotel_id, 'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0]['check_in_date'];
}

function maxCheckOutDateByHotelID($hotel_id)
{
	global $cfg, $mycms;
	
	$sqlDetails = array();
	$sqlDetails['QUERY'] = "SELECT max(check_out_date) AS check_out_date FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_." 
							WHERE status = ?  
							 AND `hotel_id` = ?";
							 
	$sqlDetails['PARAM'][]   = array('FILD' => 'status',    'DATA' =>'A',         'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'hotel_id',   'DATA' =>$hotel_id, 'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0]['check_out_date'];
}

function countMinCheckIN($room_id, $minCheckin, $delegateId)
{
	global $cfg, $mycms;
	$sqlDetails['QUERY'] = "SELECT min(checkin_date) AS checkin_date FROM "._DB_MASTER_ROOM_." 
							WHERE status = ?  
							AND room_id = ?
							 AND `user_id` = ?";
							 
	$sqlDetails['PARAM'][]   = array('FILD' => 'status',    'DATA' =>'A',         'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'room_id',    'DATA' =>$room_id,         'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'user_id',   'DATA' =>$delegateId, 'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);
	
	return $resDetails[0]['checkin_date'];
}

function countMaxCheckIN($room_id, $minCheckin, $delegateId)
{
	global $cfg, $mycms;
	$sqlDetails['QUERY'] = "SELECT max(checkout_date) AS checkout_date FROM "._DB_MASTER_ROOM_." 
							WHERE status = ?  
							AND room_id = ?
							 AND `user_id` = ?";
							 
	$sqlDetails['PARAM'][]   = array('FILD' => 'status',    'DATA' =>'A',         'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'room_id',    'DATA' =>$room_id,         'TYP' => 's');
	$sqlDetails['PARAM'][]   = array('FILD' => 'user_id',   'DATA' =>$delegateId, 'TYP' => 's');
	
	$resDetails = $mycms->sql_select($sqlDetails);

	
	return $resDetails[0]['checkout_date'];
}

function getNightValidate($room_id, $minCheckin, $maxCheckin, $delegateId, &$count)
{

	//$count = 0;
	if($room_id==1)
	{

		 $countMinCheckIN = countMinCheckIN($room_id, $minCheckin, $delegateId);
		 $countMaxCheckIN = countMaxCheckIN($room_id, $maxCheckin, $delegateId);

		 if($countMinCheckIN==$minCheckin && $countMaxCheckIN==$maxCheckin)
		 {
		 	
		 	$count++;
		 }
	}
	if($room_id==2)
	{

		 $countMinCheckIN = countMinCheckIN($room_id, $minCheckin, $delegateId);
		 $countMaxCheckIN = countMaxCheckIN($room_id, $maxCheckin, $delegateId);

		 if($countMinCheckIN==$minCheckin && $countMaxCheckIN==$maxCheckin)
		 {
		 	
		 	$count++;
		 }
	}
	if($room_id==3)
	{

		 $countMinCheckIN = countMinCheckIN($room_id, $minCheckin, $delegateId);
		 $countMaxCheckIN = countMaxCheckIN($room_id, $maxCheckin, $delegateId);

		 if($countMinCheckIN==$minCheckin && $countMaxCheckIN==$maxCheckin)
		 {
		 	
		 	$count++;
		 }
	}

	return $count;
	
}



function getDaysBetweenDates($checkinDate, $checkoutDate)
{
  $origin = date_create($checkinDate);
  $target = date_create($checkoutDate);
  $interval = date_diff($origin, $target);
 
  if($interval->format('%a')>1)
  {
  	return $interval->format('%R%a Nights');
  }
  else
  {
  	return $interval->format('%R%a Night');
  }
  
}

function insertingAccomodationDetails($accomodationDetails)
{
	global $cfg, $mycms;

 
	if(!empty($accomodationDetails['more_rooms']) && $accomodationDetails['more_rooms']=='Y')
	{
		$roomStatus = 'Y';
	}
	else
	{
		$roomStatus = 'N';
	}
	if(empty($accomodationDetails['booking_quantity_insert'])){
		$accomodationDetails['booking_quantity_insert'] = '1';
	}
	if(empty($accomodationDetails['package_id'])){
		$accomodationDetails['package_id'] = 0;
	}	
if(empty($accomodationDetails['roomTypeId'])){
		$accomodationDetails['roomTypeId'] = 0;
	}	
	$sqlInsertAccommodationRequest = array();
	$sqlInsertAccommodationRequest['QUERY'] = "INSERT INTO "._DB_REQUEST_ACCOMMODATION_." 
												  SET `user_id`	   	 		  			= ?,
													  `accompany_name`        			= ?,
													  `accommodation_details`        	= ?,
													  `hotel_id` 			  			= ?, 
													  `package_id` 	 	 	  			= ?, 
													  `tariff_cutoff_id` 	  			= ?, 
													  `tariff_ref_id`         			= ?, 
													  `checkin_date`		  			= ?,
													  `checkout_date`		  			= ?,
													  `booking_quantity`	  			= ?,
													   `roomTypeId`	  			       = ?,
													  `rooms_no`	  					= ?,
													  `more_rooms`	  					= ?,
													  `booking_mode` 	 	  			= ?,
													  `payment_status` 		  			= ?,
													  `refference_invoice_id` 			= ?,
													  `refference_slip_id` 	  			= ?,
													  `guest_counter` 	      			= ?,
													  `preffered_accommpany_name`      	= ?,
													  `preffered_accommpany_email`     	= ?,
													  `preffered_accommpany_mobile`    	= ?,	
													  `status` 				  			= ?,
													  `created_ip`			  			= ?,
													  `created_sessionId`  	  			= ?,
													  `created_browser` 	  			= ?,
													  `created_dateTime`	  			= ?";
													  
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'user_id',          			'DATA' =>$accomodationDetails['user_id'],         			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'accompany_name',   			'DATA' =>$accomodationDetails['accompany_name'],  			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'accommodation_details',   	'DATA' =>$accomodationDetails['accommodation_details'],  			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'hotel_id',         			'DATA' =>$accomodationDetails['hotel_id'],        			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'package_id',       			'DATA' =>$accomodationDetails['package_id'],      			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'tariff_cutoff_id', 			'DATA' =>$accomodationDetails['tariff_cutoff_id'],			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'tariff_ref_id',    			'DATA' =>$accomodationDetails['tariff_ref_id'],   			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'checkin_date',     			'DATA' =>$accomodationDetails['checkin_date'],    			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'checkout_date',    			'DATA' =>$accomodationDetails['checkout_date'],   			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'booking_quantity', 			'DATA' =>$accomodationDetails['booking_quantity_insert'],			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'roomTypeId', 			'DATA' =>$accomodationDetails['roomTypeId'],			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'rooms_no', 			'DATA' =>$accomodationDetails['rooms_no'],			'TYP' => 's');

	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'more_rooms', 			'DATA' =>$roomStatus,			'TYP' => 's');

	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'booking_mode',     			'DATA' =>$accomodationDetails['booking_mode'],    			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'payment_status',   			'DATA' =>$accomodationDetails['payment_status'],  			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'refference_invoice_id', 		'DATA' =>$accomodationDetails['refference_invoice_id'],  	'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'refference_slip_id',    		'DATA' =>$accomodationDetails['refference_slip_id'],     	'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'guest_counter',         		'DATA' =>$accomodationDetails['guest_counter'],          	'TYP' => 's');
	
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'preffered_accommpany_name', 	'DATA' =>$accomodationDetails['preffered_accommpany_name'],	'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'preffered_accommpany_email',	'DATA' =>$accomodationDetails['preffered_accommpany_email'],'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'preffered_accommpany_mobile','DATA' =>$accomodationDetails['preffered_accommpany_mobile'],'TYP' => 's');
	
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'status',                		'DATA' =>'A',                                            	'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'created_ip',            		'DATA' =>$_SERVER['REMOTE_ADDR'],                        	'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'created_sessionId',     		'DATA' =>session_id(),                                  	'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'created_browser',       		'DATA' =>$_SERVER['HTTP_USER_AGENT'],                   	'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'created_dateTime',      		'DATA' =>date('Y-m-d H:i:s'),                            	'TYP' => 's');

	// echo '<pre>'; print_r($sqlInsertAccommodationRequest);
	// 		die();			   
	$accommodationRequestId        = $mycms->sql_insert($sqlInsertAccommodationRequest, false);	
	
	// UPDATING USER TABLE
	$sqlUpdateUser               = array();
	$sqlUpdateUser['QUERY']       = "UPDATE "._DB_USER_REGISTRATION_." 
										SET `isAccommodation` = ?
									  WHERE `id` = ?";
									  
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'isAccommodation',  'DATA' =>'Y',                            'TYP' => 's');
	$sqlUpdateUser['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$accomodationDetails['user_id'],'TYP' => 's');
										  
	$mycms->sql_update($sqlUpdateUser, false);
	
	return $accommodationRequestId;
}	

function insertingAccomodationRoomDetails($accomodationDetails)
{
	global $cfg, $mycms;
	
	$sqlInsertAccommodationRequest = array();
	$sqlInsertAccommodationRequest['QUERY'] = "INSERT INTO "._DB_MASTER_ROOM_." 
												  SET `user_id`	   	 		  			= ?,
													  `request_accommodation_id`        = ?,
													  `room_id`        					= ?,
													  `checkin_id` 			  			= ?, 
													  `checkout_id` 	 	 	  		= ?, 
													  `checkin_date` 	  			    = ?, 
													  `checkout_date`         			= ? ";
													  
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'user_id',          			'DATA' =>$accomodationDetails['user_id'], 'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'request_accommodation_id', 'DATA' =>$accomodationDetails['request_accommodation_id'],  			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'room_id', 'DATA' =>$accomodationDetails['room_id'], 'TYP' => 's');

	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'checkin_id', 'DATA' =>$accomodationDetails['checkin_id'],'TYP' => 's');

	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'checkout_id', 'DATA' =>$accomodationDetails['checkout_id'], 'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'checkin_date', 			'DATA' =>$accomodationDetails['checkin_date'],			'TYP' => 's');
	$sqlInsertAccommodationRequest['PARAM'][]   = array('FILD' => 'checkout_date',    			'DATA' =>$accomodationDetails['checkout_date'],   			'TYP' => 's');
	
						   
	$accommodationRequestId        = $mycms->sql_insert($sqlInsertAccommodationRequest, false);	

	
	return $accommodationRequestId;
}	

function getAccommodationTariffDetails($tariffId)
{
	global $cfg, $mycms;
	
	 $sqlTarrif['QUERY']		   = "SELECT *
							FROM "._DB_TARIFF_ACCOMMODATION_." 
						   WHERE id = '".$tariffId."' AND status='A'";


    //print_r($sqlTarrif);
	$resRegclsf = $mycms->sql_select($sqlTarrif);
	//print_r($resRegclsf);
	
	return $resRegclsf;
}

function getAccommodationPackageDetails($cutoff, $hotel_id, $package_id,$roomTypeId)
{

	global $cfg, $mycms;
	
	 $sqlTarrif['QUERY']		   = "SELECT *
							FROM "._DB_ACCOMMODATION_PACKAGE_PRICE_." 
						   WHERE tariff_cutoff_id = '".$cutoff."' AND hotel_id = '".$hotel_id."' AND package_id = '".$package_id."' AND roomTypeId = '".$roomTypeId."' AND status='A'";


    //print_r($sqlTarrif);
	$resRegclsf = $mycms->sql_select($sqlTarrif);
	//print_r($resRegclsf);
	
	return $resRegclsf;

}

function accommodationDetails($searchcondition="")
{
	global $cfg, $mycms;
	
	 $sqlFetchWorkshopBooking['QUERY'] 	  = "  SELECT   delegate.id AS delegateId, delegate.user_full_name, delegate.user_unique_sequence, 
													   delegate.user_registration_id, delegate.user_mobile_no, delegate.user_email_id,accomod.checkin_date,
													   accomod.checkout_date,invoice.payment_status,
													   packageAcc.package_name,delegate.user_country_id,delegate.user_state_id,invoice.id AS invoiceId
												FROM "._DB_REQUEST_ACCOMMODATION_." accomod
														   
										INNER JOIN "._DB_USER_REGISTRATION_." delegate
														ON delegate.id = accomod.user_id
														
										LEFT OUTER JOIN "._DB_PACKAGE_ACCOMMODATION_." packageAcc
														ON packageAcc.id =  accomod.package_id										
												
										INNER JOIN "._DB_SLIP_." slip  
														ON slip.id = accomod.refference_slip_id
														AND slip.status = 'A'
												
										INNER JOIN "._DB_INVOICE_." invoice
													ON invoice.id = accomod.refference_invoice_id
												   AND invoice.service_type = 'DELEGATE_ACCOMMODATION_REQUEST'
												   AND invoice.status = 'A'
											WHERE invoice.payment_status	= 'PAID'
											AND delegate.status = 'A' ".$searchcondition."";	
		return 	$sqlFetchWorkshopBooking ;								
}

// accommodation work by weavers start
function accommodationList($currentCutoffId)
{
	global $cfg, $mycms;
	
	$sqlHotel				= array();
	$sqlHotel['QUERY']	 	= "SELECT tracm.id as ACCOMMODATION_TARIFF_ID,
								tracm.hotel_id as HOTEL_ID,
								tracm.package_id as ACCOMMODATION_PACKAGE_ID,
								tracm.tariff_cutoff_id as CUTOFF_ID,
								tracm.checkin_date_id as CHECKIN_DATE_ID,
								tracm.checkout_date_id as CHECKOUT_DATE_ID,
								tracm.currency as CURRENCY,
								tracm.inr_amount as AMOUNT,
								tracm.usd_amount as USD_AMOUNT,
								tracm.status as STATUS,
								hotel_master.hotel_name as HOTEL_NAME,
								chkindate.check_in_date as CHECKIN_DATE,
								chkoutdate.check_out_date as CHECKOUT_DATE,
								DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS 
								FROM "._DB_MASTER_HOTEL_." as hotel_master
								INNER JOIN "._DB_TARIFF_ACCOMMODATION_." as tracm 
								on tracm.hotel_id = hotel_master.id
								LEFT JOIN "._DB_ACCOMMODATION_CHECKIN_DATE_." as chkindate
								on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
								LEFT JOIN "._DB_ACCOMMODATION_CHECKOUT_DATE_." as chkoutdate
								on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
								WHERE hotel_master.status = ? AND tracm.type = ? AND tracm.created_dateTime != ? AND tracm.tariff_cutoff_id = ? AND tracm.status = 'A'
								HAVING (DAYS) < 5
								 ORDER BY tracm.hotel_id ASC, DAYS ASC, CHECKIN_DATE_ID ASC"; //GROUP BY DAYS,tracm.hotel_id // HAVING (DAYS) < 4

	$sqlHotel['PARAM'][]    = array('FILD' => 'hotel_master.status', 'DATA' => 'A',  'TYP' => 's');
	$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.type', 'DATA' => 'new',  'TYP' => 's');
	$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');
	$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $currentCutoffId,  'TYP' => 's');
	$resHotel		    	= $mycms->sql_select($sqlHotel);
	return $resHotel;	
}




// accommodation work by weavers end
?>