<?php
function getTariffCutoffId($cutoffId="",$currentDate="")
{		
	global $cfg, $mycms;		
	$cutoffValue         = "0";		
	if($cutoffId=="")
	{
		$sqlcutoff['QUERY']	 	= "SELECT * FROM "._DB_TARIFF_CUTOFF_." WHERE status = ?";
		$sqlcutoff['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
		$rescutoff		    	= $mycms->sql_select($sqlcutoff);
		
		if($currentDate=='')
		{
			$currentDate   = date('Y-m-d');
		}
		foreach($rescutoff as $keycutoff=>$valcutoff)
		{
			if(intVal(strtotime($currentDate))>=intVal(strtotime($valcutoff['start_date'])) && intVal(strtotime($currentDate)<=strtotime($valcutoff['end_date'])))
			{
				$cutoffValue = $valcutoff['id'];
			}
		}

	}
	else if($cutoffId!=""){
	
		$cutoffValue     = $cutoffId;
	}	
		
	return $cutoffValue;
}

function getAllRegistrationTariffs($cutoffId="",$reviewOperational=true)
{
	global $cfg, $mycms;
	
	$displayData 	 = array();
	
	$sqlRegClasf			= array();
	$sqlRegClasf['QUERY']	= "SELECT `classification_title`,`title_description`,`Checkbox_description`,`id`,`currency`,`type` ,`isOffer`,icon, residential_hotel_id
								 FROM "._DB_REGISTRATION_CLASSIFICATION_." 
								WHERE status = ? ".
								  (($reviewOperational)?" AND is_operational = 'Y' ":"")
						  ." ORDER BY (CASE WHEN `type` = 'DELEGATE' THEN 1
											WHEN `type`	= 'ACCOMPANY' THEN 2
											ELSE 9999 END) ASC, sequence_by ASC";
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');			
	$resRegClasf			 = $mycms->sql_select($sqlRegClasf);	
	//echo '<pre>'; print_r($resRegClasf);

	foreach($resRegClasf as $key=>$rowRegClasf)
	{
		$sqlcutoff				= array();
		$sqlcutoff['QUERY']		= "SELECT * FROM "._DB_TARIFF_CUTOFF_." WHERE status = ?";
		$sqlcutoff['PARAM'][]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');						 
		$rescutoff				= $mycms->sql_select($sqlcutoff);
	
		foreach($rescutoff as $keycutoff=>$cutoffvalue)
		{
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['REG_CLASSIFICATION_ID'] = $rowRegClasf['id'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CLASSIFICATION_TITTLE'] = $rowRegClasf['classification_title'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['TITTLE_DESCRIPTION'] = $rowRegClasf['title_description'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_TITTLE'] = $cutoffvalue['cutoff_title'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_ID'] = $cutoffvalue['id'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CURRENCY'] = $rowRegClasf['currency'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['TYPE'] = $rowRegClasf['type'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['HOTEL'] = $rowRegClasf['hotel'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['HOTEL_ID'] = $rowRegClasf['residential_hotel_id'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['ISOFFER'] = $rowRegClasf['isOffer'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CHECKBOX_DESCRIPTION'] = $rowRegClasf['Checkbox_description'];

			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['ICON'] = $rowRegClasf['icon'];
			
			$sqlTarrif				= array();
			$sqlTarrif['QUERY'] 	= "SELECT *
									 	 FROM "._DB_TARIFF_REGISTRATION_." 
										WHERE tariff_classification_id = ?
									  	  AND tariff_cutoff_id = ?";
			$sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_classification_id',  	'DATA' =>$rowRegClasf['id'],  'TYP' => 's');		
			$sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',  			'DATA' =>$cutoffvalue['id'],  'TYP' => 's');		
			$resTarrif				= $mycms->sql_select($sqlTarrif);
			
			if($resTarrif)
			{
				$rowTarrif		= $resTarrif[0];
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['AMOUNT'] = $rowTarrif['amount'];
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['DISPLAY_AMOUNT'] = $rowTarrif['display_amount'];
			}
			else
			{
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['DISPLAY_AMOUNT'] = "0.00";
			}
		}
	}
	
	if($cutoffId!='')
	{
		$arr = array();
		foreach($displayData as $classId=>$tariffs)
		{
			$arr[$classId] = $tariffs[$cutoffId];
		}
		$displayData = $arr;
	}
	
	return $displayData;
}

function getAllRegistrationComboTariffs($cutoffId = "", $reviewOperational = true)
{
	global $cfg, $mycms;

	$displayData 	 = array();
	$filter = "";
	if ($cutoffId != "") {
		$filter = "AND cutoff_id='" .$cutoffId."'";
	}

	$sqlRegClasf			= array();
	$sqlRegClasf['QUERY']	= "SELECT *
								 FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . "
								WHERE status = 'A' " . $filter;
	// $sqlRegClasf['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
	// $sqlRegClasf['PARAM'][]  = array('FILD' => 'cutoff_id',  'DATA' => $cutoffId,  'TYP' => 's');

	//print_r($sqlRegClasf);		
	$resRegClasf			 = $mycms->sql_select($sqlRegClasf);



	return $resRegClasf;
}



function getAllRegistrationComboTariffsPage($cutoffId="",$reviewOperational=true)
{
	global $cfg, $mycms;
	
	$displayData 	 = array();
	
	$sqlRegClasf			= array();
	$sqlRegClasf['QUERY']	= "SELECT RC.`classification_title`,RC.`id`,RC.`currency`,RC.residential_hotel_id, RC.total_round_price
								 FROM "._DB_REGISTRATION_COMBO_CLASSIFICATION_." RC INNER JOIN "._DB_TARIFF_COMBO_REGISTRATION_." CO ON RC.id = CO.tariff_classification_id
								WHERE RC.status = ?  ";
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'RC.status',  'DATA' =>'A',  'TYP' => 's');			
	$resRegClasf			 = $mycms->sql_select($sqlRegClasf);	
	//echo '<pre>'; print_r($resRegClasf);

	foreach($resRegClasf as $key=>$rowRegClasf)
	{
		//echo $rowRegClasf['classification_title'];
		$sqlcutoff				= array();
		$sqlcutoff['QUERY']		= "SELECT * FROM "._DB_TARIFF_CUTOFF_." WHERE status = ?";
		$sqlcutoff['PARAM'][]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');						 
		$rescutoff				= $mycms->sql_select($sqlcutoff);
	
		foreach($rescutoff as $keycutoff=>$cutoffvalue)
		{
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['REG_CLASSIFICATION_ID'] = $rowRegClasf['id'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CLASSIFICATION_TITTLE'] = $rowRegClasf['classification_title'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_TITTLE'] = $cutoffvalue['cutoff_title'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_ID'] = $cutoffvalue['id'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CURRENCY'] = $rowRegClasf['currency'];
			
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['HOTEL'] = $rowRegClasf['hotel'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['HOTEL_ID'] = $rowRegClasf['residential_hotel_id'];
			
			
			$sqlTarrif				= array();
			$sqlTarrif['QUERY'] 	= "SELECT *
									 	 FROM "._DB_TARIFF_COMBO_REGISTRATION_." 
										WHERE tariff_classification_id = ?
									  	  AND tariff_cutoff_id = ?";
			$sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_classification_id',  	'DATA' =>$rowRegClasf['id'],  'TYP' => 's');		
			$sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',  			'DATA' =>$cutoffvalue['id'],  'TYP' => 's');		
			$resTarrif				= $mycms->sql_select($sqlTarrif);
			
			if($resTarrif)
			{
				$rowTarrif		= $resTarrif[0];
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['AMOUNT'] = $rowTarrif['amount'];
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['DISPLAY_AMOUNT'] = $rowTarrif['display_amount'];
			}
			else
			{
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['DISPLAY_AMOUNT'] = "0.00";
			}
		}
	}

	
	
	if($cutoffId!='')
	{
		$arr = array();
		foreach($displayData as $classId=>$tariffs)
		{
			$arr[$classId] = $tariffs[$cutoffId];
		}
		$displayData = $arr;
	}
	//echo '<pre>'; print_r($displayData);
	return $displayData;
}

function getClassificationComboPrice($classification_id = "", $combo_package_type = "")
{
	global $cfg, $mycms;

	$displayData 	 = array();

	$sqlRegClasf			= array();
	$sqlRegClasf['QUERY']	= "SELECT *
								 FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . "
								WHERE status = ?  AND id=?";
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'id',  'DATA' => $classification_id,  'TYP' => 's');
	$resRegClasf			 = $mycms->sql_select($sqlRegClasf);
	if ($combo_package_type == 'shared') {
		return $resRegClasf[0]['total_round_price_shared'];
	} else {
		return $resRegClasf[0]['total_round_price'];
	}
}

// new combo-package 
function getComboPackageDetails($combo_classification_id = "", $accommodation_date_id = "")
{
	global $cfg, $mycms;

	$comboPackageDetailsArray = array();
	$sqlRegClasf['QUERY']	  = "SELECT comboCls.*,hotel.*
							  	   FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " comboCls LEFT JOIN " .
		_DB_MASTER_HOTEL_ . " hotel on comboCls.residential_hotel_id = hotel.id 
							 	  WHERE comboCls.id ='" . $combo_classification_id . "' AND comboCls.status= 'A' ";

	$resRegClasf		 = $mycms->sql_select($sqlRegClasf);

	$rowRegCLasf = $resRegClasf[0];

	$ws_id = json_decode($rowRegCLasf['workshop_classification']);
	$comboPackageDetailsArray['WORKSHOP_NAME'] = getWorkshopName($ws_id[0]);
	$comboPackageDetailsArray['PACKAGE_NAME'] = $rowRegCLasf['classification_title'];
	$comboPackageDetailsArray['HOTEL_NAME'] = $rowRegCLasf['hotel_name'];
	$comboPackageDetailsArray['hotel_address'] = $rowRegCLasf['hotel_address'];
	$comboPackageDetailsArray['ROOM_TYPE'] = $rowRegCLasf['room_type'];
	$comboPackageDetailsArray['registration_price'] = $rowRegCLasf['registration_price'];
	$comboPackageDetailsArray['workshop_price'] = $rowRegCLasf['workshop_price'];
	$comboPackageDetailsArray['workshop_classification'] = $rowRegCLasf['workshop_classification'];
	$comboPackageDetailsArray['accommodation_price_individual'] = $rowRegCLasf['accommodation_price_individual'];
	$comboPackageDetailsArray['accommodation_price_shared'] = $rowRegCLasf['accommodation_price_shared'];
	$comboPackageDetailsArray['no_of_night'] = $rowRegCLasf['no_of_night'];
	$comboPackageDetailsArray['total_round_price'] = $rowRegCLasf['total_round_price'];
	$comboPackageDetailsArray['total_round_price_shared'] = $rowRegCLasf['total_round_price_shared'];
	$comboPackageDetailsArray['PACKAGE_NAME'] = $rowRegCLasf['classification_title'];

	if ($accommodation_date_id != "") {
		$sqlAccDate['QUERY'] = "  SELECT * FROM  " . _DB_TARIFF_COMBO_ACCOMODATION_ . " WHERE id='" . $accommodation_date_id . "'";
		$ResAccDate		 = $mycms->sql_select($sqlAccDate);


		$sqlAccCheckinDate['QUERY'] = "  SELECT check_in_date FROM  " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
										WHERE id='" . $ResAccDate[0]['checkin_date_id'] . "'
										AND hotel_id='" . $ResAccDate[0]['hotel_id'] . "' AND status='A'";
		$ResAccCheckinDate		 = $mycms->sql_select($sqlAccCheckinDate);

		$sqlAccCheckoutDate['QUERY'] = "  SELECT check_out_date FROM  " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " 
										WHERE id='" . $ResAccDate[0]['checkout_date_id'] . "'
										AND hotel_id='" . $ResAccDate[0]['hotel_id'] . "' AND status='A'";
		$ResAccCheckoutDate		 = $mycms->sql_select($sqlAccCheckoutDate);

		$comboPackageDetailsArray['CHECKIN_DATE'] = date('d/m/Y', strtotime($ResAccCheckinDate[0]['check_in_date']));
		$comboPackageDetailsArray['CHECKOUT_DATE'] = date('d/m/Y', strtotime($ResAccCheckoutDate[0]['check_out_date']));

		// echo '<pre>';
		// print_r($resRegClasf);
		// die;
	}


	return $comboPackageDetailsArray;
}



function getAllAccompanyTariffs($cutoffId="",$reviewOperational=true)
{
	global $cfg, $mycms;
	
	$displayData 	 = array();
	
	$sqlRegClasf			= array();
	$sqlRegClasf['QUERY']	= "SELECT `classification_title`,id
								 FROM "._DB_ACCOMPANY_CLASSIFICATION_." 
								WHERE status = ? ";
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');			
	$resRegClasf			 = $mycms->sql_select($sqlRegClasf);			
	foreach($resRegClasf as $key=>$rowRegClasf)
	{
		$sqlcutoff				= array();
		$sqlcutoff['QUERY']		= "SELECT * FROM "._DB_TARIFF_CUTOFF_." WHERE status = ?";
		$sqlcutoff['PARAM'][]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');						 
		$rescutoff				= $mycms->sql_select($sqlcutoff);
	
		foreach($rescutoff as $keycutoff=>$cutoffvalue)
		{
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['REG_CLASSIFICATION_ID'] = $rowRegClasf['id'];
			$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['CLASSIFICATION_TITTLE'] = $rowRegClasf['classification_title'];
			
			
			$sqlTarrif				= array();
			$sqlTarrif['QUERY'] 	= "SELECT *
									 	 FROM "._DB_TARIFF_ACCOMPANY_." 
										WHERE tariff_classification_id = ?
									  	  AND tariff_cutoff_id = ?";
			$sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_classification_id',  	'DATA' =>$rowRegClasf['id'],  'TYP' => 's');		
			$sqlTarrif['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',  			'DATA' =>$cutoffvalue['id'],  'TYP' => 's');		
			$resTarrif				= $mycms->sql_select($sqlTarrif);
			//echo '<pre>'; print_r($sqlTarrif);
			if($resTarrif)
			{
				$rowTarrif		= $resTarrif[0];
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['AMOUNT'] = $rowTarrif['amount'];
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['DISPLAY_AMOUNT'] = $rowTarrif['display_amount'];
			}
			else
			{
				$displayData[$rowRegClasf['id']][$cutoffvalue['id']]['DISPLAY_AMOUNT'] = "0.00";
			}
		}
	}
	
	if($cutoffId!='')
	{
		$arr = array();
		foreach($displayData as $classId=>$tariffs)
		{
			$arr[$classId] = $tariffs[$cutoffId];
		}
		$displayData = $arr;
	}
	//echo '<pre>'; print_r($displayData);
	
	return $displayData;
}

function getACCClsfName($regClasfId, $status=false)
{
	global $cfg, $mycms;
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'C',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	$sqlRegClasf['QUERY']	  = "SELECT `classification_title`,`id` 
							  	   FROM "._DB_ACCOMPANY_CLASSIFICATION_."  
							 	  WHERE `id` = ? ".$searchCondition."";
	$sqlRegClasf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$regClasfId,  'TYP' => 's');
	foreach($searchConditionPARAM as $k=>$val)
	{
		$sqlRegClasf['PARAM'][] = $val;
	}
	$resRegClasf		 = $mycms->sql_select($sqlRegClasf);
	
	return strip_tags($resRegClasf[0]['classification_title']);
}

function getCutoffTariffAmnt($cutOffId="")
{		
	global $cfg, $mycms;
	
	$sqlcutoff['QUERY'] = "SELECT TA.amount 
						    FROM "._DB_TARIFF_ACCOMPANY_." TA INNER JOIN "._DB_ACCOMPANY_CLASSIFICATION_." AC
						    ON AC.id=TA.tariff_classification_id
							WHERE TA.status = 'A' 
								AND AC.status='A'
							  AND TA.`tariff_cutoff_id` = '".$cutOffId."'";

	$rescutoff		    = $mycms->sql_select($sqlcutoff);

	return strip_tags($rescutoff[0]['amount']);
}

function getWorkshopTariffCutoffId($cutoffId="",$currentDate="")
{		
	global $cfg, $mycms;		
	$cutoffValue         = "0";		
	if($cutoffId=="")
	{
		$sqlcutoff['QUERY']	 	= "SELECT * FROM "._DB_WORKSHOP_CUTOFF_." WHERE status = ?";
		$sqlcutoff['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
		$rescutoff		    	= $mycms->sql_select($sqlcutoff);
		
		if($currentDate=='')
		{
			$currentDate   = date('Y-m-d');
		}
		foreach($rescutoff as $keycutoff=>$valcutoff)
		{
			if(intVal(strtotime($currentDate))>=intVal(strtotime($valcutoff['start_date'])) && intVal(strtotime($currentDate)<=strtotime($valcutoff['end_date'])))
			{
				$cutoffValue = $valcutoff['id'];
			}
		}

	}
	else if($cutoffId!=""){
	
		$cutoffValue     = $cutoffId;
	}	
		
	return $cutoffValue;
}

function getAllWorkshopTariffs($cutoffId="",$status="A")
{
	global $cfg, $mycms;
		
	$displayData 	 = array();	
	
	$statusList = explode(",",$status);

	foreach($statusList as $statusVal)
	{
		$qMarks[] = "?";
		//$statusConditionPARAM[]   = array('FILD' => 'status_in_'.$i,  'DATA' =>$statusVal,  'TYP' => 's');	
		$statusConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>$statusVal,  'TYP' => 's');	
	}
	$statusCondition 		  	  = "AND status IN(".implode(",",$qMarks).")";
	
	$sqlWorkshopclsf			= array();	
	$sqlWorkshopclsf['QUERY'] 	= "SELECT *, IFNULL(type,'GENERAL') as type  
									 FROM "._DB_WORKSHOP_CLASSIFICATION_." 
									WHERE display = ? ".$statusCondition." 
								 ORDER BY sequence_by ASC, workshop_date ASC, id ASC";
	$sqlWorkshopclsf['PARAM'][] = array('FILD' => 'display',  'DATA' =>'Y',  'TYP' => 's');	

	foreach($statusConditionPARAM as $k=>$val)
	{
		$sqlWorkshopclsf['PARAM'][] = $val;
	}

	
	$resWorkshopclsf		 	= $mycms->sql_select($sqlWorkshopclsf);
	
	foreach($resWorkshopclsf as $key=>$rowWorkshopclsf)
	{
		// add status to query on 14.07.2022 so that it's returns only active records
		
		$sqlRegClasf['QUERY']	= "SELECT `classification_title`,`id`,`currency` 
									 FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE status = 'A'";
		$resRegClasf			= $mycms->sql_select($sqlRegClasf);			
				// Add dummy classification (id=0, title='')
		$dummyClass = array(
			'id' => 0,
			'classification_title' => '',
			'currency' => 'INR'
		);
		// Add dummy at the beginning of the array
		array_unshift($resRegClasf, $dummyClass);
		foreach($resRegClasf as $key=>$rowRegClasf)
		{
			$sqlcutoff['QUERY']	= "SELECT * FROM "._DB_WORKSHOP_CUTOFF_." WHERE status = 'A' ";
								 
			$rescutoff		  	= $mycms->sql_select($sqlcutoff);
			
	
			foreach($rescutoff as $keycutoff=>$cutoffvalue)
			{
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_ID'] = $rowWorkshopclsf['id'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['DISPLAY'] = $rowWorkshopclsf['display'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_NAME'] = $rowWorkshopclsf['classification_title'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_TYPE'] = $rowWorkshopclsf['type'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_GRP'] = $rowWorkshopclsf['sequence_by'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_TITTLE'] = $cutoffvalue['cutoff_title'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_ID'] = $cutoffvalue['id'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['REG_CLASSIFICATION'] = $rowRegClasf['classification_title'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['REG_ID'] = $rowRegClasf['id'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['CURRENCY'] = $rowRegClasf['currency'];
				// workshop related work by weavers start
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_DATE'] = $rowWorkshopclsf['workshop_date'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['commonTariff'] = $rowWorkshopclsf['commonTariff'];

				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['VENUE'] = $rowWorkshopclsf['venue'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['VENUE_ADDRESS'] = $rowWorkshopclsf['venue_address'];
				// workshop related work by weavers end

				
				$sqlTarrif['QUERY']= "SELECT *
										FROM "._DB_TARIFF_WORKSHOP_." 
									   WHERE workshop_id = '".$rowWorkshopclsf['id']."'
										 AND tariff_cutoff_id = '".$cutoffvalue['id']."'
										 AND registration_classification_id = '".$rowRegClasf['id']."'
										 AND (OnlyWorkshop_clssId = '0'
										 OR OnlyWorkshop_clssId IS NULL)";
									 
				$resTarrif			= $mycms->sql_select($sqlTarrif);
				
				if($resTarrif)
				{
					$rowTarrif		= $resTarrif[0];
					
					$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['INR'] = $rowTarrif['inr_amount'];
					$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['USD'] = $rowTarrif['usd_amount'];
				}
				else
				{					
					$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['INR'] = 0.00;
					$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['USD'] = 0.00;
				}
				
			}

		}

	}	
	
	if($cutoffId!='')
	{
		$arr = array();
		foreach($displayData as $workspId=>$tariffs)
		{
			foreach($tariffs as $classId=>$tariff)
			{
				$arr[$workspId][$classId] = $tariff[$cutoffId];
			}
		}
		$displayData = $arr;
	}
		
	return $displayData;
}

function getOnlyWorkshopTariffs($cutoffId="",$status="A")
{
	global $cfg, $mycms;
		
	$displayData 	 = array();	
	
	$statusList = explode(",",$status);

	foreach($statusList as $statusVal)
	{
		$qMarks[] = "?";
		//$statusConditionPARAM[]   = array('FILD' => 'status_in_'.$i,  'DATA' =>$statusVal,  'TYP' => 's');	
		$statusConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>$statusVal,  'TYP' => 's');	
	}
	$statusCondition 		  	  = "AND status IN(".implode(",",$qMarks).")";
	
	$sqlWorkshopclsf			= array();	
	$sqlWorkshopclsf['QUERY'] 	= "SELECT *, IFNULL(type,'GENERAL') as type  
									 FROM "._DB_WORKSHOP_CLASSIFICATION_." 
									WHERE display = ? ".$statusCondition." 
								 ORDER BY sequence_by ASC, workshop_date ASC, id ASC";
	$sqlWorkshopclsf['PARAM'][] = array('FILD' => 'display',  'DATA' =>'Y',  'TYP' => 's');	

	foreach($statusConditionPARAM as $k=>$val)
	{
		$sqlWorkshopclsf['PARAM'][] = $val;
	}

	
	$resWorkshopclsf		 	= $mycms->sql_select($sqlWorkshopclsf);
	
	foreach($resWorkshopclsf as $key=>$rowWorkshopclsf)
	{
		// add status to query on 14.07.2022 so that it's returns only active records
		
		$sqlRegClasf['QUERY']	= "SELECT `classification_title`,`id`,`currency` 
									 FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE status = 'A'
									 limit 1";
		$resRegClasf			= $mycms->sql_select($sqlRegClasf);			
		foreach($resRegClasf as $key=>$rowRegClasf)
		{
			$rowRegClasf['id'] = 0;
			$rowRegClasf['classification_title'] = '';
			$sqlcutoff['QUERY']	= "SELECT * FROM "._DB_WORKSHOP_CUTOFF_." WHERE status = 'A' ";
								 
			$rescutoff		  	= $mycms->sql_select($sqlcutoff);
			
	
			foreach($rescutoff as $keycutoff=>$cutoffvalue)
			{
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_ID'] = $rowWorkshopclsf['id'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['DISPLAY'] = $rowWorkshopclsf['display'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_NAME'] = $rowWorkshopclsf['classification_title'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_TYPE'] = $rowWorkshopclsf['type'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_GRP'] = $rowWorkshopclsf['sequence_by'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_TITTLE'] = $cutoffvalue['cutoff_title'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['CUTOFF_ID'] = $cutoffvalue['id'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['REG_CLASSIFICATION'] = $rowRegClasf['classification_title'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['REG_ID'] = $rowRegClasf['id'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['CURRENCY'] = $rowRegClasf['currency'];
				// workshop related work by weavers start
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['WORKSHOP_DATE'] = $rowWorkshopclsf['workshop_date'];

				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['VENUE'] = $rowWorkshopclsf['venue'];
				$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['VENUE_ADDRESS'] = $rowWorkshopclsf['venue_address'];
				// workshop related work by weavers end

				
				$sqlTarrif['QUERY']= "SELECT *
										FROM "._DB_TARIFF_WORKSHOP_." 
									   WHERE workshop_id = '".$rowWorkshopclsf['id']."'
										 AND tariff_cutoff_id = '".$cutoffvalue['id']."'
										 AND registration_classification_id = '".$rowRegClasf['id']."'";
									 
				$resTarrif			= $mycms->sql_select($sqlTarrif);
				
				if($resTarrif)
				{
					$rowTarrif		= $resTarrif[0];
					
					$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['INR'] = $rowTarrif['inr_amount'];
					$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['USD'] = $rowTarrif['usd_amount'];
				}
				else
				{					
					$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['INR'] = 0.00;
					$displayData[$rowWorkshopclsf['id']][$rowRegClasf['id']][$cutoffvalue['id']]['USD'] = 0.00;
				}
				
			}

		}

	}	
	
	if($cutoffId!='')
	{
		$arr = array();
		foreach($displayData as $workspId=>$tariffs)
		{
			foreach($tariffs as $classId=>$tariff)
			{
				$arr[$workspId][$classId] = $tariff[$cutoffId];
			}
		}
		$displayData = $arr;
	}
		
	return $displayData;
}
function getRegClsfName($regClasfId, $status=false)
{
	global $cfg, $mycms;
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'C',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	$sqlRegClasf['QUERY']	  = "SELECT `classification_title`,`id` 
							  	   FROM "._DB_REGISTRATION_CLASSIFICATION_."  
							 	  WHERE `id` = ? ".$searchCondition."";
	$sqlRegClasf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$regClasfId,  'TYP' => 's');
	foreach($searchConditionPARAM as $k=>$val)
	{
		$sqlRegClasf['PARAM'][] = $val;
	}
	$resRegClasf		 = $mycms->sql_select($sqlRegClasf);
	
	return strip_tags($resRegClasf[0]['classification_title']);
}

function getRegClsfComboName($regClasfId, $status=false)
{
	global $cfg, $mycms;
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'C',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	$sqlRegClasf['QUERY']	  = "SELECT `classification_title`,`id` 
							  	   FROM "._DB_REGISTRATION_COMBO_CLASSIFICATION_."  
							 	  WHERE `id` = ? ".$searchCondition."";
	$sqlRegClasf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$regClasfId,  'TYP' => 's');
	foreach($searchConditionPARAM as $k=>$val)
	{
		$sqlRegClasf['PARAM'][] = $val;
	}
	$resRegClasf		 = $mycms->sql_select($sqlRegClasf);
	
	return strip_tags($resRegClasf[0]['classification_title']);
}
	
function getRegClsfType($regClasfId, $status=false)
{
	global $cfg, $mycms;
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'C',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	$sqlRegClasf['QUERY']	  = "SELECT `type`,`id` 
							  	   FROM "._DB_REGISTRATION_CLASSIFICATION_."  
							 	  WHERE `id` = ? ".$searchCondition."";
	$sqlRegClasf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$regClasfId,  'TYP' => 's');
	foreach($searchConditionPARAM as $k=>$val)
	{
		$sqlRegClasf['PARAM'][] = $val;
	}
	//print_r($sqlRegClasf);
	$resRegClasf		 = $mycms->sql_select($sqlRegClasf);
	//echo $resRegClasf[0]['type'];
	return strip_tags($resRegClasf[0]['type']);
}

function getRegClsfCurrency($regClasfId, $status=false)
{
	global $cfg, $mycms;
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'C',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	$sqlRegClasf['QUERY']	  = "SELECT `currency`,`id` 
							  	   FROM "._DB_REGISTRATION_CLASSIFICATION_."  
							 	  WHERE `id` = ? ".$searchCondition."";
	$sqlRegClasf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$regClasfId,  'TYP' => 's');
	foreach($searchConditionPARAM as $k=>$val)
	{
		$sqlRegClasf['PARAM'][] = $val;
	}
	$resRegClasf		 = $mycms->sql_select($sqlRegClasf);
	
	return strip_tags($resRegClasf[0]['currency']);
}

function getRegClsfDetails($regClasfId, $status=false)
{
	global $cfg, $mycms;
	if($status)
	{
		$searchCondition 		  = "AND status IN(?,?)";
		$searchConditionPARAM[]   = array('FILD' => 'status_in_1',  'DATA' =>'A',  'TYP' => 's');		
		$searchConditionPARAM[]   = array('FILD' => 'status_in_2',  'DATA' =>'C',  'TYP' => 's');		
	}
	else
	{
		$searchCondition 		  = "AND status = ?";
		$searchConditionPARAM[]   = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');		
	}
	
	$sqlRegClasf['QUERY']	  = "SELECT *
							  	   FROM "._DB_REGISTRATION_CLASSIFICATION_."  
							 	  WHERE `id` = ? ".$searchCondition."";
	$sqlRegClasf['PARAM'][]   = array('FILD' => 'id', 'DATA' =>$regClasfId,  'TYP' => 's');
	foreach($searchConditionPARAM as $k=>$val)
	{
		$sqlRegClasf['PARAM'][] = $val;
	}
	$resRegClasf		 = $mycms->sql_select($sqlRegClasf);
	
	return $resRegClasf[0];
}

// function emailIdValidationProcess()
// {
// 	global $cfg, $mycms;
	
// 	$email                  	= trim($_REQUEST['email']);
		
// 	$availabilityStatus 		= "AVAILABLE";
	
// 	$sqlFetchUser            = array();
// 	$sqlFetchUser['QUERY']	  = "SELECT `id` 
// 							  	   FROM "._DB_USER_REGISTRATION_."  
// 							 	  WHERE `user_email_id` = ? 
// 								  AND `status`          = ?";
								  
// 	$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' =>$email,  'TYP' => 's');
// 	$sqlFetchUser['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
	
// 	$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
// 	$maxRowsUser                = $mycms->sql_numrows($resultFetchUser);
	
// 	if($maxRowsUser==0){	
// 		$availabilityStatus 	= "AVAILABLE";
		
// 		$sqlFetchUser  = array();
// 		$sqlFetchUser['QUERY']        = "SELECT * 
// 										 FROM "._DB_COMN_USER_DATA_."
// 										WHERE `user_email_id` = ? 
// 										  AND `status` = ?";
		
// 		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' =>$email, 'TYP' => 's');	
// 		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' =>'A',    'TYP' => 's');
							
// 		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
// 		$dataString                 = ""; 				
// 		if($resultFetchUser){
		
// 			$rowFetchUser				= $resultFetchUser[0];			
// 			$dataString                = '"DATA" : {';
			
// 			$dataString                .= '"TITLE": "'.$rowFetchUser['user_title'].'",';
// 			$dataString                .= '"FIRST_NAME": "'.$rowFetchUser['user_first_name'].'",';
// 			$dataString                .= '"MIDDLE_NAME": "'.$rowFetchUser['user_middle_name'].'",';	
// 			$dataString        		   .= '"LAST_NAME": "'.$rowFetchUser['user_last_name'].'",';
// 			$dataString                .= '"FULL_NAME": "'.$rowFetchUser['user_full_name'].'",';
			
// 			$dataString                .= '"MOBILE_ISD_CODE": "'.$rowFetchUser['user_mobile_isd_code'].'",';
// 			$dataString                .= '"MOBILE_NO": "'.$rowFetchUser['user_mobile_no'].'",';
			
// 			$dataString                .= '"ADDRESS": "'.$rowFetchUser['user_address'].'",';
			
// 			$dataString                .= '"COUNTRY_ID": "'.$rowFetchUser['user_country_id'].'",';
// 			$dataString                .= '"STATE_ID": "'.$rowFetchUser['user_state_id'].'",';
// 			$dataString                .= '"CITY": "'.$rowFetchUser['user_city_name'].'",';
// 			$dataString                .= '"PIN_CODE": "'.$rowFetchUser['user_pincode'].'",';
			
// 			$dataString                .= '"GENDER": "'.$rowFetchUser['user_gender'].'",';
// 			$dataString                .= '"FOOD_PREFERENCE": "'.$rowFetchUser['user_food_preference'].'"';	
// 			$dataString                .= '}';
			
// 		}
// 		$availabilityStatus .= (trim($dataString)=='')?'}':','.$dataString.'}';
// 	}
// 	else
// 	{
		
// 		$availabilityStatus 	= "IN_USE";
// 	}
	
// 	echo $availabilityStatus;
// }

function emailIdValidationProcess()
{
	global $cfg, $mycms;
	
	$email                  	= trim($_REQUEST['email']);
		
	$availabilityStatus 		= "AVAILABLE";
	
	$sqlFetchUser            = array();
	$sqlFetchUser['QUERY']	  = "SELECT `id` 
							  	   FROM "._DB_USER_REGISTRATION_."  
							 	  WHERE `user_email_id` = ? 
								  AND `status`          = ? 
								   AND `registration_request`          = ?";
								  
	$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' =>$email,  'TYP' => 's');
	$sqlFetchUser['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
	$sqlFetchUser['PARAM'][]   = array('FILD' => 'registration_request', 'DATA' =>'GENERAL',  'TYP' => 's');
	
	$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
	$maxRowsUser                = $mycms->sql_numrows($resultFetchUser);
	
	if($maxRowsUser==0){	

		header('Content-type: application/json');
		
		$availabilityStatus 	= '{"STATUS" : "AVAILABLE"';
		
		$sqlFetchUser  = array();
		$sqlFetchUser['QUERY']        = "SELECT * 
										 FROM "._DB_USER_REGISTRATION_."
										WHERE `user_email_id` = ? 
										  AND `status` = ?";
		
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_email_id', 'DATA' =>$email, 'TYP' => 's');	
		$sqlFetchUser['PARAM'][]   = array('FILD' => 'status',        'DATA' =>'A',    'TYP' => 's');
							
		$resultFetchUser    		= $mycms->sql_select($sqlFetchUser);
		$dataString                 = ""; 	

		if($resultFetchUser){
		
			$rowFetchUser				= $resultFetchUser[0];			
			$dataString                = '"DATA" : {';
			$dataString                .= '"ID": "'.$rowFetchUser['id'].'",';
			$dataString                .= '"REG_REQUEST": "'.$rowFetchUser['registration_request'].'",';

			$dataString                .= '"TITLE": "'.$rowFetchUser['user_title'].'",';
			$dataString                .= '"FIRST_NAME": "'.$rowFetchUser['user_first_name'].'",';
			$dataString                .= '"MIDDLE_NAME": "'.$rowFetchUser['user_middle_name'].'",';	
			$dataString        		   .= '"LAST_NAME": "'.$rowFetchUser['user_last_name'].'",';
			$dataString                .= '"FULL_NAME": "'.$rowFetchUser['user_full_name'].'",';
			
			$dataString                .= '"MOBILE_ISD_CODE": "'.$rowFetchUser['user_mobile_isd_code'].'",';
			$dataString                .= '"MOBILE_NO": "'.$rowFetchUser['user_mobile_no'].'",';
			
			$dataString                .= '"ADDRESS": "'.$rowFetchUser['user_address'].'",';
			$dataString                .= '"COUNTRY_ID": "'.$rowFetchUser['user_country_id'].'",';
			$dataString                .= '"STATE_ID": "'.$rowFetchUser['user_state_id'].'",';
			$dataString                .= '"CITY": "'.$rowFetchUser['user_city'].'",';
			$dataString                .= '"PIN_CODE": "'.$rowFetchUser['user_pincode'].'",';
			$dataString                .= '"GENDER": "'.$rowFetchUser['user_gender'].'",';
			$dataString                .= '"FOOD_PREFERENCE": "'.$rowFetchUser['user_food_preference'].'"';	
			$dataString                .= '}';
			
		}

		$availabilityStatus .= (trim($dataString)=='')?'}':','.$dataString.'}';
	}
	else
	{
		
		//$availabilityStatus 	= "IN_USE";
		$availabilityStatus 	= '{"STATUS" : "IN_USE"}';
	}
	
	echo $availabilityStatus;
}


function mobileValidationProcess()
{
	global $cfg, $mycms;
	
	$mobile                  	= trim($_REQUEST['mobile']);
		
	$availabilityStatus 		= "AVAILABLE";
	
	$sqlFetchUser['QUERY']	  = "SELECT `id` 
							  	   FROM "._DB_USER_REGISTRATION_."  
							 	  WHERE `user_mobile_no` = ? 
								  AND `status`          = ? 
								  AND `registration_request`          = ?";
								  
	$sqlFetchUser['PARAM'][]   = array('FILD' => 'user_mobile_no', 'DATA' =>$mobile,  'TYP' => 's');
	$sqlFetchUser['PARAM'][]   = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
	$sqlFetchUser['PARAM'][]   = array('FILD' => 'registration_request', 'DATA' =>'GENERAL',  'TYP' => 's');
	
	$resultFetchUser            = $mycms->sql_select($sqlFetchUser);
	$maxRowsUser                = $mycms->sql_numrows($resultFetchUser);
	
	if($maxRowsUser==0){	
		$availabilityStatus 	= "AVAILABLE";
	}
	else
	{
		
		$availabilityStatus 	= "IN_USE";
	}
	
	echo $availabilityStatus;
}

function getRegistrationTariffId($clsfId,$cutoffId)
{
	
	global $cfg, $mycms;
	
	$sqlRegclsf['QUERY'] 	 = "SELECT * 
								  FROM "._DB_TARIFF_REGISTRATION_." 
								 WHERE status = ? 
								   AND `tariff_cutoff_id` = ?
								   AND `tariff_classification_id` = ?";
								   
	$sqlRegclsf['PARAM'][]   = array('FILD' => 'status',                    'DATA' =>'A', 'TYP' => 's');
	$sqlRegclsf['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',          'DATA' =>$cutoffId, 'TYP' => 's');
	$sqlRegclsf['PARAM'][]   = array('FILD' => 'tariff_classification_id',  'DATA' =>$clsfId, 'TYP' => 's');
	
	
	$resRegclsf = $mycms->sql_select($sqlRegclsf);
	
	return $resRegclsf[0]['id'];
}

function getRegistrationTariffAmount($clsfId,$cutoffId)
{
	
	global $cfg, $mycms;
	$sqlRegclsf = array();
	$sqlRegclsf['QUERY'] 	 = "SELECT * 
								  FROM "._DB_TARIFF_REGISTRATION_." 
								 WHERE status = ? 
								   AND `tariff_cutoff_id` = ?
								   AND `tariff_classification_id` = ?";
	$sqlRegclsf['PARAM'][]   = array('FILD' => 'status',      				'DATA' =>'A',   						'TYP' => 's');	
	$sqlRegclsf['PARAM'][]   = array('FILD' => 'tariff_cutoff_id',      	'DATA' =>$cutoffId,   					'TYP' => 's');	
	$sqlRegclsf['PARAM'][]   = array('FILD' => 'tariff_classification_id',  'DATA' =>$clsfId,   					'TYP' => 's');								   
	$resRegclsf = $mycms->sql_select($sqlRegclsf);
	
	return $resRegclsf[0]['amount'];
}

function getCutoffName($cutOffId="")
{		
	global $cfg, $mycms;
	
	$sqlcutoff['QUERY'] = "SELECT * 
						    FROM "._DB_TARIFF_CUTOFF_." 
							WHERE status = 'A' 
							  AND `id` = '".$cutOffId."'";
	$rescutoff		    = $mycms->sql_select($sqlcutoff);
	
	return strip_tags($rescutoff[0]['cutoff_title']);
}

function getCutoffEndDate($cutOffId="")
{		
	global $cfg, $mycms;
	
	$sqlcutoff['QUERY'] = "SELECT * 
						    FROM "._DB_TARIFF_CUTOFF_." 
							WHERE status = 'A' 
							  AND `id` = '".$cutOffId."'";
	$rescutoff		    = $mycms->sql_select($sqlcutoff);
	
	return strip_tags($rescutoff[0]['end_date']);
}

function getRegistrationCurrency($regClasfId="")
{
	global $cfg, $mycms;
	
	$sqlRegClasf['QUERY']= "SELECT `currency`,`id` FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE status = 'A' AND `id` = '".$regClasfId."'";
	$resRegClasf			= $mycms->sql_select($sqlRegClasf);
	
	return strip_tags($resRegClasf[0]['currency']);
}

function fullCutoffArray()
{
	global $cfg, $mycms;
	$cutoffArray  = array();
	$sqlCutoff['QUERY']    = "SELECT * 
								FROM "._DB_TARIFF_CUTOFF_." 
							   WHERE `status` = 'A' 
							ORDER BY `cutoff_sequence` ASC";	
											  
	$resCutoff = $mycms->sql_select($sqlCutoff);
	
	if($resCutoff)
	{
		foreach($resCutoff as $i=>$rowCutoff) 
		{
			$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
		}
	}
	return $cutoffArray;
}

function registrationPaymentStatus($delegateId,$type="")
{
	global $cfg, $mycms;
	
	if($delegateId==""){
		
		$mycms->kill("Please Select Delegate Id");
	}
	$paymentStatus = array();
	if($type =="CONFERENCE")
	{
		$searchCondition = "AND `service_type` = 'DELEGATE_CONFERENCE_REGISTRATION'";
		$tittle			 = "Conference";
	}
	if($type =="RESIDENTIAL")
	{
		$searchCondition = "AND `service_type` = 'DELEGATE_RESIDENTIAL_REGISTRATION'";
		$tittle			 = "Residential";
	}
	if($type =="WORKSHOP")
	{
		$searchCondition = "AND `service_type` = 'DELEGATE_WORKSHOP_REGISTRATION'";
		$tittle			 = "Workshop";
	}
	if($type =="ACCOMPANY")
	{
		$searchCondition = "AND `service_type` = 'ACCOMPANY_CONFERENCE_REGISTRATION'";
		$tittle			 = "Accompany";
	}
	if($type =="ACCOMMODATION")
	{
		$searchCondition = "AND `service_type` = 'DELEGATE_ACCOMMODATION_REQUEST'";
		$tittle			 = "Accommodation";
	}
	if($type =="TOUR")
	{
		$searchCondition = "AND `service_type` = 'DELEGATE_TOUR_REQUEST'";
		$tittle			 = "Tour";
	}
	$invoiceDetailsArr = invoiceDetailsOfDelegate($delegateId,$searchCondition);
	if($invoiceDetailsArr)
	{
		foreach($invoiceDetailsArr as $key =>$invoiceDetails)
		{
			$paymentStatus[] =	$invoiceDetails['payment_status'];
		}
		
		if(in_array("UNPAID", $paymentStatus))
		{
			$detailsArray['FULL_SPAN'] 		= '<span class="paymentDtls">'.$tittle.':</span><span style="float: left;"><span class="unpaidStatus">UNPAID</span></span>';
			$detailsArray['STATUS_SPAN']	= '<span class="unpaidStatus">UNPAID</span>';
			$detailsArray['TITLE']			= $tittle;
			$detailsArray['PAYMENT_STATUS'] = 'UNPAID';
			$detailsArray['paymentStatus'] 	= 'UNPAID';
			return $detailsArray;
		}
		else if(in_array("PAID", $paymentStatus))
		{
			$detailsArray['FULL_SPAN'] 		= '<span class="paymentDtls">'.$tittle.':</span><span style="float: left;"><span class="paidStatus">PAID</span></span>';
			$detailsArray['STATUS_SPAN']	= '<span class="paidStatus">PAID</span>';
			$detailsArray['TITLE'] 			= $tittle;
			$detailsArray['PAYMENT_STATUS'] = 'PAID';
			$detailsArray['paymentStatus'] 	= 'PAID';
			return $detailsArray;
		}
		else if(in_array("ZERO_VALUE", $paymentStatus))
		{
			$detailsArray['FULL_SPAN'] 		= '<span class="paymentDtls">'.$tittle.':</span><span style="float: left;"><span class="paidStatus">ZERO VALUE</span></span>';
			$detailsArray['STATUS_SPAN']	= '<span class="paidStatus">ZERO VALUE</span>';
			$detailsArray['TITLE'] 			= $tittle;
			$detailsArray['PAYMENT_STATUS'] = 'ZERO VALUE';
			$detailsArray['paymentStatus'] 	= 'ZERO_VALUE';
			return $detailsArray;
		}
		else if(in_array("COMPLIMENTARY", $paymentStatus))
		{
			$detailsArray['FULL_SPAN'] 		= '<span class="paymentDtls">'.$tittle.':</span><span style="float: left;"><span class="paidStatus">COMPLIMENTARY</span></span>';
			$detailsArray['STATUS_SPAN']	= '<span class="paidStatus">COMPLIMENTARY</span>';
			$detailsArray['TITLE'] 			= $tittle;
			$detailsArray['PAYMENT_STATUS'] = 'COMPLIMENTARY';
			$detailsArray['paymentStatus'] 	= 'COMPLIMENTARY';
			return $detailsArray;
		}
		
	}
}

function complementaryUserDetailsInseringProcess()
{
	global $cfg, $mycms;
	
	$rowProcessFlow 	= $resProcessFlow[0];
	
	$userDetails		= $_REQUEST;
	
	
	$clsfId				= $userDetails['registration_classification_id'][0];
	if($userDetails)
	{
		$userDetailsArray['user_email_id']                        = addslashes(trim($userDetails['user_email_id']));
		$userDetailsArray['user_password_raw']                    = $userDetails['user_password'];
		$userDetailsArray['user_password']                        = $mycms->encoded($userDetails['user_password']);
		$userDetailsArray['user_initial_title']   				  = addslashes(trim(strtoupper($userDetails['user_initial_title'])));
		$userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($userDetails['user_first_name'])));
		$userDetailsArray['user_middle_name']               	  = addslashes(trim(strtoupper($userDetails['user_middle_name'])));
		$userDetailsArray['user_last_name']                       = addslashes(trim(strtoupper($userDetails['user_last_name'])));
		$userDetailsArray['user_full_name']                       = $userDetailsArray['user_initial_title']." ".$userDetailsArray['user_first_name']." ".$userDetailsArray['user_middle_name']." ".$userDetailsArray['user_last_name'];
		$userDetailsArray['user_full_name']                       = preg_replace('/\s+/', ' ', $userDetailsArray['user_full_name']);
		$userDetailsArray['user_mobile_isd_code']                 = addslashes(trim(strtoupper($userDetails['user_usd_code'])));
		$userDetailsArray['user_mobile_no']                       = addslashes(trim(strtoupper($userDetails['user_mobile'])));
		$userDetailsArray['user_phone_no']                        = addslashes(trim(strtoupper($userDetails['user_phone'])));
		$userDetailsArray['user_address']                         = addslashes(trim(strtoupper($userDetails['user_address'])));
		$userDetailsArray['user_country']                         = addslashes(trim(strtoupper($userDetails['user_country']?$userDetails['user_country']:0)));
		$userDetailsArray['user_state']                           = addslashes(trim(strtoupper($userDetails['user_state']?$userDetails['user_state']:0)));
		$userDetailsArray['user_city']                            = addslashes(trim(strtoupper($userDetails['user_city'])));
		$userDetailsArray['user_postal_code']                     = addslashes(trim(strtoupper($userDetails['user_postal_code'])));
		$userDetailsArray['user_dob_year']                        = addslashes(trim(strtoupper($userDetails['user_dob_year'])));
		$userDetailsArray['user_dob_month']                       = addslashes(trim(strtoupper($userDetails['user_dob_month'])));
		$userDetailsArray['user_dob_day']                         = addslashes(trim(strtoupper($userDetails['user_dob_day'])));
		$userDetailsArray['user_dob']                             = $userDetailsArray['user_dob_year']."-".number_pad($userDetailsArray['user_dob_month'], 2)."-".number_pad($userDetailsArray['user_dob_day'], 2);
		$userDetailsArray['user_gender']                          = $userDetails['user_gender'];
		$userDetailsArray['user_designation']                     = addslashes(trim(strtoupper($userDetails['user_designation'])));
		$userDetailsArray['user_depertment']                      = addslashes(trim(strtoupper($userDetails['user_depertment'])));
		$userDetailsArray['user_institution_name']                = addslashes(trim(strtoupper($userDetails['user_institution'])));
		$userDetailsArray['user_food_preference']                 = addslashes(trim(strtoupper($userDetails['user_food_preference'])));
		$userDetailsArray['user_other_food_details']              = addslashes(trim(strtoupper($userDetails['user_food_details'])));
		$userDetailsArray['passport_no']                      	  = addslashes(trim(strtoupper($userDetails['user_pasport_no'])));
		$userDetailsArray['passport_expiry_date']                 = number_pad($userDetails['user_pasport_exp_year'], 4)."-".number_pad($userDetails['user_pasport_exp_month'], 2)."-".number_pad($userDetails['user_pasport_exp_day'], 2);
		$userDetailsArray['isRegistration']						  = 'Y';
		$userDetailsArray['isConference']						  = 'Y';
		$userDetailsArray['isWorkshop']							  = 'N';
		$userDetailsArray['isAccommodation']                      = 'N';
		$userDetailsArray['isTour']								  = 'N';
		$userDetailsArray['IsAbstract']							  = 'N';
		
		$userDetailsArray['registration_classification_id']		  = $userDetails['registration_classification_id'][0];
		$userDetailsArray['registration_tariff_cutoff_id']        = getTariffCutoffId();
		$userDetailsArray['workshop_tariff_cutoff_id']        	  = getWorkshopTariffCutoffId();
		$userDetailsArray['registration_request']       		  = $userDetails['registration_request'];
		$userDetailsArray['operational_area']   	    		  = $userDetails['registration_request'];
		$userDetailsArray['registration_payment_status']		  = 'COMPLIMENTARY';
		$userDetailsArray['registration_mode']					  = "OFFLINE";
		$userDetailsArray['account_status']						  = 'REGISTERED';
		$userDetailsArray['reg_type']              				  = addslashes(trim(strtoupper($userDetails['reg_area'])));
		
		$delegateId			= insertingUserDetails($userDetailsArray);
		
		$accDetails 		= getUserTypeAndRoomType($userDetailsArray['registration_classification_id']);
		
		insertingSlipDetails($delegateId,"OFFLINE");
		$slipId = $mycms->getSession('SLIP_ID');
		$sqlUpdate['QUERY']	                      = "UPDATE "._DB_SLIP_."
												SET `payment_status` = 'COMPLIMENTARY'
											  WHERE `id` = '".$slipId."'";
							 
		$mycms->sql_update($sqlUpdate, false);
		
		$invoiceId 			= insertingComplementaryInvoiceDetails($delegateId);
		
		if($accDetails['TYPE']=='COMBO')
		{
			$workshopComboDetails  = array(1,2,3,4);
			foreach($workshopComboDetails as $key => $workshopId)
			{
				$workshopDetailArray[$workshopId]['delegate_id']        			= $delegateId;
				$workshopDetailArray[$workshopId]['workshop_id']      				= $workshopId;
				$workshopDetailArray[$workshopId]['tariff_cutoff_id']      			= getWorkshopTariffCutoffId();;
				$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= -1;
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
				$workshopDetailArray[$workshopId]['booking_mode']        			= $userDetailsArray['registration_mode'];
				$workshopDetailArray[$workshopId]['registration_type']       		= $userDetails['registration_request'];
				$workshopDetailArray[$workshopId]['refference_invoice_id']       	= $invoiceId; // Need To Edit
				$workshopDetailArray[$workshopId]['refference_slip_id']       		= $mycms->getSession('SLIP_ID');
				$workshopDetailArray[$workshopId]['payment_status']        			= 'COMPLIMENTARY';
			}
			
			
			$workshopReqId	 = insertingWorkshopDetails($workshopDetailArray);

			
			$accomodationDetails['user_id']											 = $delegateId;
			$accomodationDetails['hotel_id']										 = 1;
			
			$accomodationDetails['roomType_id']										 = $accDetails['ROOM_TYPE'];
			$accomodationDetails['package_id']										 = $accDetails['ROOM_TYPE'];
			$accomodationDetails['tariff_cutoff_id']								 = getTariffCutoffId();
			$accomodationDetails['checkin_date']									 = 1;
			$accomodationDetails['checkout_date']									 = 3;
			$accomodationDetails['booking_quantity']								 = 1;
			$accomodationDetails['refference_invoice_id']							 = $invoiceId;
			$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
			$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];
			$accomodationDetails['payment_status']									 = 'COMPLIMENTARY';
			
			$accompReqId	 = insertingAccomodationDetails($accomodationDetails);
			
			//insertingInvoiceDetails($accompReqId,'ACCOMMODATION');
		}
	}
	
	offline_conference_complimentry_registration_confirmation_message($delegateId,'',$slipId , 'SEND');
}

function detailsInseringProcess($processFlowId, $clasfPayMode='', $counter='')
{
	global $cfg, $mycms;
	include_once("function.accommodation.php");
	$sqlProcessFlow   = array();
	$sqlProcessFlow['QUERY']	= "SELECT * 
									 FROM "._DB_PROCESS_STEP_." 
								 	WHERE `id` = ?";
									
	$sqlProcessFlow['PARAM'][]   = array('FILD' => 'id',   'DATA' =>$processFlowId,  'TYP' => 's');
			
	$resProcessFlow			= $mycms->sql_select($sqlProcessFlow);
		

	if($resProcessFlow)
	{
		$rowProcessFlow 	= $resProcessFlow[0];
		$userDetails		= unserialize($rowProcessFlow['step1']);
		$workshopDetails	= $userDetails['workshop_id'];
		$accompanyDetails	= unserialize($rowProcessFlow['step3']);
		$accommDetails		= unserialize($rowProcessFlow['step4']);
		$tourDetails		= unserialize($rowProcessFlow['step5']);
		$dinnerDetails		= $userDetails['dinner_value'];
		
		// $fileDetails		= $rowProcessFlow['step10'];
		$fileDetails		= unserialize($rowProcessFlow['step0']);
		$user_document_name = $fileDetails['user_document_name'];

		$hotel_id			= $userDetails['hotel_id'];
		$cutoffId			= $userDetails['registration_cutoff'];
		$workshopCutoffId	= $userDetails['workshop_cutoff'];
		$clsfId				= $userDetails['registration_classification_id'][0];

		$paymentStatusPre =$userDetails['paymentstatus'];
		if($paymentStatusPre==''){
		 $registration_payment_status='UNPAID';

		}else{
		 $registration_payment_status=$paymentStatusPre;

		}
		$clasfDetails	    = getRegClsfDetails($clsfId);
		if($clasfPayMode == '')
		{
			$clasfPayMode		= $clasfDetails['payment_type'];
		}
		
		$clasfType			= $clasfDetails['type'];
		
		$date				= $userDetails['date'];

		if($userDetails)
		{ 
			$userDetailsArray['user_email_id']                        = addslashes(trim(strtolower($userDetails['user_email_id'])));
			$userDetailsArray['comunication_email']                   = addslashes(trim(strtolower($userDetails['comunication_email'])));
			$userDetailsArray['user_password_raw']                    = $userDetails['user_password'];
			$userDetailsArray['user_password']                        = $mycms->encoded($userDetails['user_password']);
			$userDetailsArray['membership_number']                    = addslashes(trim($userDetails['membership_number']));
			$userDetailsArray['user_initial_title']   				  = addslashes(trim(strtoupper($userDetails['user_initial_title'])));
			$userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($userDetails['user_first_name'])));
			$userDetailsArray['user_middle_name']               	  = addslashes(trim(strtoupper($userDetails['user_middle_name'])));
			$userDetailsArray['user_last_name']                       = addslashes(trim(strtoupper($userDetails['user_last_name'])));
			$userDetailsArray['user_full_name']                       = $userDetailsArray['user_initial_title']." ".$userDetailsArray['user_first_name']." ".$userDetailsArray['user_middle_name']." ".$userDetailsArray['user_last_name'];
			$userDetailsArray['user_full_name']                       = preg_replace('/\s+/', ' ', $userDetailsArray['user_full_name']);
			$userDetailsArray['user_mobile_isd_code']                 = addslashes(trim(strtoupper($userDetails['user_usd_code'])));
			$userDetailsArray['user_mobile_no']                       = addslashes(trim(strtoupper($userDetails['user_mobile'])));
			$userDetailsArray['user_phone_no']                        = addslashes(trim(strtoupper($userDetails['user_phone'])));
			$userDetailsArray['user_address']                         = addslashes(trim(strtoupper($userDetails['user_address'])));
			$userDetailsArray['user_country']                         = $userDetails['user_country']==''?'0':addslashes(trim(strtoupper($userDetails['user_country'])));
			$userDetailsArray['user_state']                           = $userDetails['user_state']==''?'0':addslashes(trim(strtoupper($userDetails['user_state'])));
			$userDetailsArray['user_city']                            = $userDetails['user_city']==''?'0':addslashes(trim(strtoupper($userDetails['user_city'])));
			$userDetailsArray['user_postal_code']                     = addslashes(trim(strtoupper($userDetails['user_postal_code'])));
			$userDetailsArray['user_dob_year']                        = addslashes(trim(strtoupper($userDetails['user_dob_year'])));
			$userDetailsArray['user_dob_month']                       = addslashes(trim(strtoupper($userDetails['user_dob_month'])));
			$userDetailsArray['user_dob_day']                         = addslashes(trim(strtoupper($userDetails['user_dob_day'])));

			$userDetailsArray['combo_registration']                            = $userDetails['combo_registration']==''?'0':addslashes(trim($userDetails['combo_registration']));
			
			if($userDetailsArray['user_dob_year'] !="" && $userDetailsArray['user_dob_month'] !="" && $userDetailsArray['user_dob_day'] !="")
			{
				$userDetailsArray['user_dob']                         = number_pad($userDetailsArray['user_dob_year'],4)."-".number_pad($userDetailsArray['user_dob_month'], 2)."-".number_pad($userDetailsArray['user_dob_day'], 2);


			}
			
			$userDetailsArray['user_gender']                          = ($userDetails['user_gender']=='')?'NA':$userDetails['user_gender'];
			$userDetailsArray['user_designation']                     = addslashes(trim(strtoupper($userDetails['user_designation'])));
			$userDetailsArray['user_depertment']                      = addslashes(trim(strtoupper($userDetails['user_depertment'])));
			$userDetailsArray['user_institution_name']                = addslashes(trim(strtoupper($userDetails['user_institution'])));
			$userDetailsArray['user_food_preference']                 = $userDetails['user_food_preference'];
			$userDetailsArray['user_other_food_details']              = addslashes(trim(strtoupper($userDetails['user_food_details'])));
			$userDetailsArray['passport_no']                      	  = addslashes(trim(strtoupper($userDetails['user_pasport_no'])));
			
			$userDetailsArray['user_document']						  = $user_document_name;
			$userDetailsArray['isRegistration']						  = 'Y';
			$userDetailsArray['isConference']						  = 'Y';
			$userDetailsArray['isWorkshop']							  = 'N';
			$userDetailsArray['isAccommodation']                      = 'N';
			$userDetailsArray['isTour']								  = 'N';
			$userDetailsArray['IsAbstract']							  = 'N';
			
			$userDetailsArray['registration_classification_id']		  = $userDetails['registration_classification_id'][0];
			$userDetailsArray['registration_tariff_cutoff_id']        = $userDetails['registration_cutoff'];
			$userDetailsArray['workshop_tariff_cutoff_id']       	  = $userDetails['workshop_cutoff']==''?$userDetails['registration_cutoff']:$userDetails['workshop_cutoff'];
			$userDetailsArray['registration_request']       		  = $userDetails['registration_request'];
			$userDetailsArray['operational_area']   	    		  = $userDetails['registration_request'];
			$userDetailsArray['registration_payment_status']		  = $registration_payment_status;
			$userDetailsArray['registration_mode']					  = ($mycms->getSession('REGISTRATION_MODE')=="ONLINE")?"ONLINE":"OFFLINE";
			$userDetailsArray['account_status']						  = 'REGISTERED';
			$userDetailsArray['reg_type']              				  = addslashes(trim(strtoupper($userDetails['reg_area'])));
			//$userDetailsArray['regsitaion_mode']   	    		      = $regsitaion_mode;
			$userDetailsArray['regsitaion_mode']=$userDetails['registration_Pay_mode'];
			if(!empty($userDetails['delegateId']) && $userDetails['delegateId']!='')
			{
				$userDetailsArray['abstractDelegateId']  = $userDetails['delegateId'];
			}
			else
			{
				$userDetailsArray['abstractDelegateId']  = $userDetails['abstractDelegateId'];
			}
			

			if(!empty($userDetails['accomodation_package_checkin_id']) && $userDetails['accomodation_package_checkin_id']!='')
			{
				$explodeCheckIn = explode('/',$userDetails['accomodation_package_checkin_id']);
				//echo 'checkin='. $accoCheckInID = $explodeCheckIn[0];
				$userDetailsArray['accoCheckInID'] = $explodeCheckIn[0];
			}

			if(!empty($userDetails['accomodation_package_checkout_id']) && $userDetails['accomodation_package_checkout_id']!='')
			{
				$explodeCheckOut = explode('/',$userDetails['accomodation_package_checkout_id']);
				//echo 'checkout='. $accoCheckOutID = $explodeCheckOut[0];
				$userDetailsArray['accoCheckOutID'] = $explodeCheckOut[0];
			}

			if(!empty($userDetails['hotel_select_acco_id']) && $userDetails['hotel_select_acco_id']!='')
			{
				$userDetailsArray['accoHotelID'] = $userDetails['hotel_select_acco_id'];
			}

			if(!empty($userDetails['package_id']) && $userDetails['package_id']!='')
			{
				$userDetailsArray['accoPackageID'] = $userDetails['package_id'];
			}

			if(!empty($userDetailsArray['combo_registration']))
			{
				$userDetailsArray['isCombo'] = 'Y';
				 $comboClassiTitle = getClassificationComboTitle($userDetailsArray['registration_classification_id']);

				if(count($userDetails['accDateCombo'])>0)
				{
					$userDetailsArray['accDateCombo'] = $userDetails['accDateCombo'][0];
					
				}
				else
				{
					$userDetailsArray['accDateCombo'] = '';
				}


			}
			else
			{
				$userDetailsArray['isCombo'] = 'N';
			}
			
			$accDetails = getUserTypeAndRoomType($userDetailsArray['registration_classification_id']);

		// 	echo 'checkIN='. $userDetailsArray['accoCheckInID'];

		// 	echo '<pre>'; print_r($userDetailsArray);
		// 	echo 'userId='. $userDetailsArray['abstractDelegateId'];
			
		//    die();			

			if($userDetailsArray['abstractDelegateId']!='' && $userDetailsArray['abstractDelegateId']>0)
			{		
				$delegateId	= insertingExistingUserDetails($userDetailsArray['abstractDelegateId'], $userDetailsArray, $date);
			}
			else
			{
				$delegateId	= insertingUserDetails($userDetailsArray,$date);
			}

			
					
			if($mycms->getSession('SLIP_ID') =="")
			{
				$mycms->setSession('LOGGED.USER.ID',$delegateId);
				insertingSlipDetails($delegateId,$userDetailsArray['registration_mode'],$userDetails['registration_request'], $date, $userDetailsArray['reg_type'],$paymentStatusPre);
			}
			
			if(!empty($userDetailsArray['combo_registration']))
			{
				$invoiceIdConf = insertingInvoiceDetails($delegateId,'CONFERENCE',$userDetails['registration_request'], $date,'',$paymentStatusPre);
			}
			else
			{
				$invoiceIdConf = insertingInvoiceDetails($delegateId,'CONFERENCE',$userDetails['registration_request'], $date,'',$paymentStatusPre);
			}
			
			
			$current_SlipAmount	= invoiceAmountOfSlip($mycms->getSession('SLIP_ID'));
						
			if($clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE" || $current_SlipAmount == 0) // 
			{
				if($clasfPayMode == "COMPLIMENTARY")
				{
					complimentarySlipUpdate($mycms->getSession('SLIP_ID'));
					zeroValueInvoiceUpdate($invoiceIdConf,'CONFERENCE', $mycms->getSession('SLIP_ID'));
				}
				else if($clasfPayMode == "ZERO_VALUE")
				{
					zeroValueSlipUpdate($mycms->getSession('SLIP_ID'));
					zeroValueInvoiceUpdate($invoiceIdConf,'CONFERENCE', $mycms->getSession('SLIP_ID'));
				}
				
				$sqlUpdate = array();
				$sqlUpdate['QUERY']	 = "UPDATE "._DB_INVOICE_."
											SET `payment_status` = ?
										  WHERE `id` = ?";
													  
				$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'COMPLIMENTARY',  'TYP' => 's');
				$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$invoiceIdConf,   'TYP' => 's');		
									 
				$mycms->sql_update($sqlUpdate, false);
				
				$sqlUpdateUserReg = array();
				$sqlUpdateUserReg['QUERY']	   = "UPDATE "._DB_USER_REGISTRATION_."
													 SET `registration_payment_status` = ?
											  	   WHERE `id` = ?";
											  
				$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'registration_payment_status',   'DATA' =>'COMPLIMENTARY', 'TYP' => 's');
				$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'id',                            'DATA' =>$delegateId	,   'TYP' => 's');
				
				$mycms->sql_update($sqlUpdateUserReg, false);
			}
			
			if($clasfType=='COMBO')
			{
				$dinnerComboDetails  = array(2);
				foreach($dinnerComboDetails as $key => $val)
				{
					$dinnerDetailsArray[$val]['delegate_id']           = $delegateId;
					$dinnerDetailsArray[$val]['refference_id']         = $delegateId;
					$dinnerDetailsArray[$val]['package_id']            = $val;
					$dinnerDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
					$dinnerDetailsArray[$val]['booking_quantity']      = 1;
					$dinnerDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
					$dinnerDetailsArray[$val]['refference_invoice_id'] = $invoiceIdConf; // Need To Edit
					$dinnerDetailsArray[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray[$val]['payment_status']        = 'ZERO_VALUE';
				}
				
				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray);

				if($clsfId != $cfg['INAUGURAL_OFFER_CLASF_ID'])
				{
					$accomodationDetails['user_id']											 = $delegateId;
					$accomodationDetails['hotel_id']										 = $hotel_id;
					$accomodationDetails['package_id']										 = $userDetails['accommodation_package_id'];
					$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
					$accomodationDetails['checkin_date']									 = getCheckInDateById($userDetails['accommodation_checkIn'],1);
					$accomodationDetails['checkout_date']									 = getCheckOutDateById($userDetails['accommodation_checkOut'],1);
					$accomodationDetails['booking_quantity']								 = 1;
					$accomodationDetails['type']								 			 = "COMBO";
					$accomodationDetails['refference_invoice_id']							 = $invoiceIdConf;
					$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
					$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];
					
					$accomodationDetails['preffered_accommpany_name']						 = $userDetails['preffered_accommpany_name'];
					$accomodationDetails['preffered_accommpany_email']						 = $userDetails['preffered_accommpany_email'];
					$accomodationDetails['preffered_accommpany_mobile']						 = $userDetails['preffered_accommpany_mobile'];
										
					$accomodationDetails['payment_status']									 = 'ZERO_VALUE';
					
					$accompReqId	 														 = insertingAccomodationDetails($accomodationDetails);
				}
			}
		}

        // if(!empty($userDetailsArray['accDateCombo']) && count($userDetailsArray['accDateCombo'])>0)
        // {
        // 	    $explodeAccDateCombo = explode('-',$userDetailsArray['accDateCombo']);
					
		// 		$check_in_date_id    = $explodeAccDateCombo[0];
		// 		$check_out_date_id    = $explodeAccDateCombo[1];
		// 		$accommodation_hotel_id    = $explodeAccDateCombo[2];

		// 		$totalRoom = 0;
		// 		$totalGuestCounter                 = 0;
				
		// 		$sqlAccommodationDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
		// 										   WHERE `id` = '".$check_in_date_id."'";
																
		// 		$resultAccommodationDate        = $mycms->sql_select($sqlAccommodationDate); 
		// 		$rowAccommodationDate           = $resultAccommodationDate[0];

		// 		$check_in_date              = $rowAccommodationDate['check_in_date'];

		// 		// GET ACCOMMODATION OUT DATE
		// 		$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_."
		// 												   WHERE `id` = '".$check_out_date_id."'";
																		
		// 		$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate); 
		// 		$rowAccommodationOutDate           = $resultAccommodationOutDate[0];

		// 		$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];

					
		// 		$sqlFetchHotel['QUERY']    = "SELECT id 
		// 							   FROM "._DB_PACKAGE_ACCOMMODATION_."  
		// 							  WHERE  `hotel_id` = '".$accommodation_hotel_id."'
		// 								  AND `status` = 'A'";  //  AND `roomType_id` = '".$accommodation_hotel_type_id."'
										  
		// 		$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
		// 		$resultfetch 	  = $resultFetchHotel[0];
		// 		$packageId 	      = $resultfetch['id'];
		// 		$accTariffId = getAccommodationTariffId($packageId,$check_in_date_id,$check_out_date_id,$cutoffId);

		// 		$accomodationDetails['user_id']											 = $delegateId;
		// 		//$accomodationDetails['accompany_name']									 = $accommDetails['accmName'];
		// 		$accomodationDetails['accommodation_details']							 = $comboClassiTitle;
		// 		$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
		// 		//$accomodationDetails['guest_counter']									 = $accommDetails['room_guest_counter'][0];
		// 		//$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
		// 		$accomodationDetails['package_id']										 = $packageId;
		// 		$accomodationDetails['tariff_ref_id']								     = $accTariffId;
		// 		$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
		// 		$accomodationDetails['checkin_date']									 = $check_in_date;
		// 		$accomodationDetails['checkout_date']									 = $check_out_date;
		// 		$accomodationDetails['booking_quantity']								 = 1;//$accommDetails['booking_quantity'];
		// 		$accomodationDetails['refference_invoice_id']							 = 0;
		// 		$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
		// 		$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];
		// 		$accomodationDetails['payment_status']									 = 'UNPAID';

		// 		$accompReqId	 = insertingAccomodationDetails($accomodationDetails);

		// 		$accommRoomDetails['user_id']										 = $delegateId;
		// 		$accommRoomDetails['request_accommodation_id']						 = $accompReqId;
		// 		$accommRoomDetails['room_id']										 = 1;
		// 		$accommRoomDetails['checkin_id']								     = $check_in_date_id;
		// 		$accommRoomDetails['checkout_id']								     = $check_out_date_id;
		// 		$accommRoomDetails['checkin_date']								     = $check_in_date;
		// 		$accommRoomDetails['checkout_date']									 = $check_out_date;



		// 		$accompReqRoomId	 = insertingAccomodationRoomDetails($accommRoomDetails);

		// 		$sqlProcessUpdateRoom['QUERY']  = " UPDATE  "._DB_USER_REGISTRATION_."
		// 										   SET `accommodation_room` = '1'
		// 										 WHERE `id` = '".$delegateId."' AND status='A'";													 
		// 		$mycms->sql_update($sqlProcessUpdateRoom, false);
				
        // }
		
		
		if($workshopDetails)
		{

			//echo '<pre>'; print_r($workshopDetails);
			//die();
				foreach($workshopDetails as $key => $workshopId)
				{
					$workshopDetailArray[$workshopId]['delegate_id']        			= $delegateId;
					$workshopDetailArray[$workshopId]['workshop_id']      				= $workshopId;
					$workshopDetailArray[$workshopId]['tariff_cutoff_id']      			= $workshopCutoffId;
					$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getWorkshopTariffId($workshopId,$workshopCutoffId,$clsfId);
					$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
					$workshopDetailArray[$workshopId]['booking_mode']        			= $userDetailsArray['registration_mode'];
					$workshopDetailArray[$workshopId]['registration_type']       		= $userDetails['registration_request'];
					$workshopDetailArray[$workshopId]['refference_invoice_id']       	= 0; // Need To Edit
					$workshopDetailArray[$workshopId]['refference_slip_id']       		= $mycms->getSession('SLIP_ID');
					$workshopDetailArray[$workshopId]['payment_status']        			= 'UNPAID';
				}
				$workshopReqId	 = insertingWorkshopDetails($workshopDetailArray);
				if(!empty($userDetailsArray['combo_registration']))
				{
				}
				else
				{
					foreach($workshopReqId as $key => $reqId)
					{	
						$invoiceIdWrkshp = insertingInvoiceDetails($reqId,'WORKSHOP',$userDetails['registration_request'], $date,'',$paymentStatusPre);

						// complimentry invoice related work for workshop by weavers start
						$current_Invoice_details	= getInvoiceDetailsquery($invoiceIdWrkshp,$delegateId,$mycms->getSession('SLIP_ID'));
						$current_InvoiceAmount = $current_Invoice_details[0]['service_roundoff_price'];

						// if invoice amount = 0 and user registration classification tarrif must be member type 
						if($current_InvoiceAmount == 0 && $clsfId == 1)
						{
							// update invoice payment status
							$sqlUpdate = array();
							$sqlUpdate['QUERY']	 = "UPDATE "._DB_INVOICE_."
														SET `payment_status` = ?
													  WHERE `id` = ?";
																  
							$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'COMPLIMENTARY',  'TYP' => 's');
							$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$invoiceIdWrkshp,   'TYP' => 's');		
												 
							$mycms->sql_update($sqlUpdate, false);

							// update user workshop status

							$sqlUpdate = array();
							$sqlUpdate['QUERY']	 = "UPDATE "._DB_REQUEST_WORKSHOP_."
														SET `payment_status` = ?
													  WHERE `id` = ?";
													  
							$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'COMPLIMENTARY',  'TYP' => 's');
							$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$reqId,   'TYP' => 's');	
							$mycms->sql_update($sqlUpdate, false);

						}

						// complimentry invoice related work for workshop by weavers end

						if($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE" || $clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE")
						{
							zeroValueInvoiceUpdate($invoiceIdWrkshp,'WORKSHOP', $mycms->getSession('SLIP_ID'));
							$sqlUpdate = array();
							$sqlUpdate['QUERY']	 = "UPDATE "._DB_REQUEST_WORKSHOP_."
														SET `payment_status` = ?
													  WHERE `id` = ?";
													  
							$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'ZERO_VALUE',  'TYP' => 's');
							$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$reqId,   'TYP' => 's');	
							$mycms->sql_update($sqlUpdate, false);
						}
					}
				}
			
			
		}
				
		if($accompanyDetails)
		{
			foreach($accompanyDetails['accompany_selected_add'] as $key => $val)
			{ 
				$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
				$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
				$accompanyDetailsArray[$val]['user_age']                    		 = addslashes(trim(strtoupper($accompanyDetails['accompany_age_add'][$val])));
				$accompanyDetailsArray[$val]['user_food_preference']                 = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
				$accompanyDetailsArray[$val]['user_food_details']                    = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
				$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper('ACCOMPANY')));
				
				$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
				$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
				$accompanyDetailsArray[$val]['registration_classification_id']		 = addslashes(trim(strtoupper($accompanyDetails['accompanyClasfId'])));
				$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $cutoffId;
				$accompanyDetailsArray[$val]['registration_request']       		 	 = $userDetails['registration_request'];
				$accompanyDetailsArray[$val]['operational_area']   	    		 	 = $userDetails['registration_request'];
				$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
				$accompanyDetailsArray[$val]['registration_mode']					 = $userDetailsArray['registration_mode'];
				$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
				$accompanyDetailsArray[$val]['reg_type']              				 = addslashes(trim(strtoupper($userDetails['reg_area'])));			
			}

			
						
			$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray, $date, $accompanyDetails['registration_acc_cutoff']); 
			
			$accDinnerDetails = $accompanyDetails['dinner_value'];
			
			if($accDinnerDetails)
			{

				foreach($accDinnerDetails as $key => $val)
				{
					$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;
					$dinnerDetailsArray1[$key]['refference_id']         = $accompanyReqId[$key];
					$dinnerDetailsArray1[$key]['package_id']            = $val;
					$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $cutoffId;
					$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
					$dinnerDetailsArray1[$key]['booking_mode']          = $userDetailsArray['registration_mode'];
					$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
					$dinnerDetailsArray1[$key]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';
				}
					
				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);
				
				foreach($dinerReqId as $key => $reqId)
				{
					
					insertingInvoiceDetails($reqId,'DINNER',$userDetails['registration_request'], $date,'',$paymentStatusPre);

					if($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE" || $clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE")
						{
							zeroValueInvoiceUpdate($invoiceIdDinner,'DINNER', $mycms->getSession('SLIP_ID'));
							$sqlUpdate = array();
							$sqlUpdate['QUERY']	 = "UPDATE "._DB_REQUEST_DINNER_."
														SET `payment_status` = ?
													  WHERE `id` = ?";
													  
							$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'ZERO_VALUE',  'TYP' => 's');
							$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$reqId,   'TYP' => 's');	
							$mycms->sql_update($sqlUpdate, false);
						}
				}
				
			}			
		
			foreach($accompanyReqId as $key =>$reqId)
			{
				if($counter == 'Y'){
					$invoiceIdAcompany = insertingInvoiceDetails($reqId,'ACCOMPANY',$userDetails['registration_request'], $date,$counter,$paymentStatusPre);
				}
				else{
					$invoiceIdAcompany = insertingInvoiceDetails($reqId,'ACCOMPANY',$userDetails['registration_request'], $date,'',$paymentStatusPre);
				}
				
				if($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE" || $clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE")
				{
					zeroValueInvoiceUpdate($invoiceIdAcompany,'ACCOMPANY', $mycms->getSession('SLIP_ID'));
					$sqlUpdate = array();
					$sqlUpdate['QUERY']	 = "UPDATE "._DB_USER_REGISTRATION_."
												SET `registration_payment_status` = ?
											  WHERE `id` = ?";
														  
					$sqlUpdate['PARAM'][]   = array('FILD' => 'registration_payment_status',    'DATA' =>'ZERO_VALUE',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'id',                             'DATA' =>$reqId,        'TYP' => 's');
					$mycms->sql_update($sqlUpdate, false);
				}
				
			}
			
		}
		

		//if($accommDetails)
		// if(!empty($accommDetails['check_in_date']) && !empty($accommDetails['check_out_date']) && !empty($accommDetails['hotel_id']))
		// {
		// 	$check_in_date_id                 = $accommDetails['check_in_date'];
		// 	$check_out_date_id                = $accommDetails['check_out_date'];
		// 	//$accommodation_hotel_id           = $accommDetails['accommodation_hotel_id'];
		// 	$accommodation_hotel_id           = $accommDetails['hotel_id'];
		// 	//$accommodation_hotel_type_id      = $accommDetails['accommodation_roomType_id'];
		// 	$totalRoom = 0;
		// 	$totalGuestCounter                 = 0;
		// 	/*foreach($accommDetails['room_guest_counter'] as $key=>$resDetails )
		// 	{
		// 		$totalRoom++;
		// 		if($resDetails!=""){
							
		// 			$totalGuestCounter        += $resDetails;
		// 		}
		// 	}*/
		// 	$sqlAccommodationDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
		// 									   WHERE `id` = '".$check_in_date_id."'";
															
		// 	$resultAccommodationDate        = $mycms->sql_select($sqlAccommodationDate); 
		// 	$rowAccommodationDate           = $resultAccommodationDate[0];
			
		// 	$check_in_date              = $rowAccommodationDate['check_in_date'];
			
		// 	// GET ACCOMMODATION OUT DATE
		// 	$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_."
		// 											   WHERE `id` = '".$check_out_date_id."'";
																	
		// 	$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate); 
		// 	$rowAccommodationOutDate           = $resultAccommodationOutDate[0];
			
		// 	$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];
			
				
		// 	$sqlFetchHotel['QUERY']    = "SELECT id 
		// 						   FROM "._DB_PACKAGE_ACCOMMODATION_."  
		// 						  WHERE  `hotel_id` = '".$accommodation_hotel_id."'
		// 							  AND `status` = 'A'";  //  AND `roomType_id` = '".$accommodation_hotel_type_id."'
									  
		// 	$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
		// 	$resultfetch 	  = $resultFetchHotel[0];
		// 	$packageId 	      = $resultfetch['id'];
		// 	$accTariffId = getAccommodationTariffId($packageId,$check_in_date_id,$check_out_date_id,$cutoffId);
			
		// 	$accomodationDetails['user_id']											 = $delegateId;
		// 	//$accomodationDetails['accompany_name']									 = $accommDetails['accmName'];
		// 	$accomodationDetails['accommodation_details']							 = $accommDetails['accmName'];
		// 	$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
		// 	//$accomodationDetails['guest_counter']									 = $accommDetails['room_guest_counter'][0];
		// 	//$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
		// 	$accomodationDetails['package_id']										 = $packageId;
		// 	$accomodationDetails['tariff_ref_id']								     = $accTariffId;
		// 	$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
		// 	$accomodationDetails['checkin_date']									 = $check_in_date;
		// 	$accomodationDetails['checkout_date']									 = $check_out_date;
		// 	$accomodationDetails['booking_quantity']								 = 1;//$accommDetails['booking_quantity'];
		// 	$accomodationDetails['refference_invoice_id']							 = 0;
		// 	$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
		// 	$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];
		// 	$accomodationDetails['payment_status']									 = 'UNPAID';
			
		// 	$accompReqId	 = insertingAccomodationDetails($accomodationDetails);


		// 	$accommRoomDetails['user_id']										 = $delegateId;
		// 	$accommRoomDetails['request_accommodation_id']						 = $accompReqId;
		// 	$accommRoomDetails['room_id']										 = 1;
		// 	$accommRoomDetails['checkin_id']								     = $check_in_date_id;
		// 	$accommRoomDetails['checkout_id']								     = $check_out_date_id;
		// 	$accommRoomDetails['checkin_date']								     = $check_in_date;
		// 	$accommRoomDetails['checkout_date']									 = $check_out_date;



		// 	$accompReqRoomId	 = insertingAccomodationRoomDetails($accommRoomDetails);

		// 	insertingInvoiceDetails($accompReqId,'ACCOMMODATION');

		// 	$sqlProcessUpdateRoom['QUERY']  = " UPDATE  "._DB_USER_REGISTRATION_."
		// 										   SET `accommodation_room` = '1'
		// 										 WHERE `id` = '".$delegateId."' AND status='A'";													 
		// 	$mycms->sql_update($sqlProcessUpdateRoom, false);
		
		// }
       if($accommDetails && $userDetails['isCombo']=='Y')
		{
			
			$sqlPackageCheckoutDate1  =  array();
			// query in tariff table
			$sqlPackageCheckoutDate1['QUERY'] = "select * 
														FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " accomodation
														WHERE status = ?
														AND id = ?";
			$sqlPackageCheckoutDate1['PARAM'][]  =  array('FILD' => 'status',       'DATA' => 'A',           'TYP' => 's');
			$sqlPackageCheckoutDate1['PARAM'][]  =  array('FILD' => 'id',     'DATA' => $userDetailsArray['combo_classification_id'],     'TYP' => 's');
		
			$resPackageCheckoutDate1 = $mycms->sql_select($sqlPackageCheckoutDate1);
		
			$totalRoomVal = $userDetails['accommodation_room'];
			if($totalRoomVal==""){
				$totalRoomVal = 0;
			}
		        
			//$accommodation_hotel_id           = $accommDetails['accommodation_hotel_id'];
			$accommodation_hotel_id           = $resPackageCheckoutDate1[0]['residential_hotel_id'];
			//$accommodation_hotel_type_id      = $accommDetails['accommodation_roomType_id'];
			$explodeCheckin = explode('/',$accommDetails['hotel_dates'][$accommodation_hotel_id]['checkin']);
			$explodeCheckout = explode('/',$accommDetails['hotel_dates'][$accommodation_hotel_id]['checkout']);

			$check_in_date_id                 = $explodeCheckin[0];
			$check_out_date_id                = $explodeCheckout[0];
			$totalRoom = 0;
			$totalGuestCounter                 = 0;

			if($resPackageCheckoutDate1[0]['room_type']==''){
				$room_type_id = '0';
			}else{
			$room_type_id =$resPackageCheckoutDate1[0]['room_type'];
			}
            if($accommDetails['package_id'][0]==''){
				$respackage_id = '0';
			}else{
			   $respackage_id =$accommDetails['package_id'][0];
			}
			
						
			$sqlAccommodationDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
											WHERE `id` = '".$check_in_date_id."'";
															
			$resultAccommodationDate        = $mycms->sql_select($sqlAccommodationDate); 
			$rowAccommodationDate           = $resultAccommodationDate[0];
			
			$check_in_date              = $rowAccommodationDate['check_in_date'];
			
			// GET ACCOMMODATION OUT DATE
			$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_."
													WHERE `id` = '".$check_out_date_id."'";
																	
			$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate); 
			$rowAccommodationOutDate           = $resultAccommodationOutDate[0];
			
			$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];
			
				
			$sqlFetchHotel['QUERY']    = "SELECT id ,package_name
								FROM "._DB_PACKAGE_ACCOMMODATION_."  
								WHERE  `hotel_id` = '".$accommodation_hotel_id."'
								AND `package_name` = '".$accommodation_hotel_id."'
									AND `status` = 'A'";  //  AND `roomType_id` = '".$accommodation_hotel_type_id."'
									
			$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
			$resultfetch 	  = $resultFetchHotel[0];
			$packageId 	      = $resultfetch['id'];
			
			$accTariffId = getAccommodationTariffId($respackage_id,$room_type_id,$check_in_date_id,$check_out_date_id,$cutoffId);
					

			$accomodationDetails['user_id']											 = $delegateId;
			//$accomodationDetails['accompany_name']									 = $accommDetails['accmName'];
			$accomodationDetails['accommodation_details']							 = $accommDetails['accmName'];
			$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
			//$accomodationDetails['guest_counter']									 = $accommDetails['room_guest_counter'][0];
			//$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
			$accomodationDetails['package_id']										 =$respackage_id;
			$accomodationDetails['tariff_ref_id']								     = $accTariffId;
			$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
			$accomodationDetails['checkin_date']									 = $explodeCheckin[1];
			$accomodationDetails['checkout_date']									 = $explodeCheckout[1];
			$accomodationDetails['booking_quantity']								 = 1;
			$accomodationDetails['refference_invoice_id']							 = 0;
			$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
			$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];
			$accomodationDetails['payment_status']									 = 'UNPAID';
			$accomodationDetails['roomTypeId']									 = $resPackageCheckoutDate1[0]['room_type'];
            if($accommDetails['package_id']=='Sharing'){
				$accomodationDetails['booking_quantity_insert']								 = 0.5;

			}else{
				$accomodationDetails['booking_quantity_insert']								 =1;

			}
			$accompReqId	 = insertingAccomodationDetails($accomodationDetails);

			$accommRoomDetails['user_id']										 = $delegateId;
			$accommRoomDetails['request_accommodation_id']						 = $accompReqId;
			$accommRoomDetails['room_id']										 = 1;
			$accommRoomDetails['checkin_id']								     = $check_in_date_id;
			$accommRoomDetails['checkout_id']								     = $check_out_date_id;
			$accommRoomDetails['checkin_date']								     = $check_in_date;
			$accommRoomDetails['checkout_date']									 = $check_out_date;

					
		    $accompReqRoomId	 = insertingAccomodationRoomDetails($accommRoomDetails);
			
			insertingInvoiceDetails($accompReqId,'ACCOMMODATION',$userDetails['registration_request'],'','',$paymentStatusPre,$userDetails['isCombo']);
				

			$sqlProcessUpdateRoom['QUERY']  = " UPDATE  "._DB_USER_REGISTRATION_."
													SET `accommodation_room` = '".$totalRoomVal."'
													WHERE `id` = '".$delegateId."' AND status='A'";													 
			$mycms->sql_update($sqlProcessUpdateRoom, false);
					
				
		}
		
        if($accommDetails && $userDetails['isCombo']=='N')
		{
			
			
			if(!empty($accommDetails['package_id']))
			{
 
				foreach($accommDetails['package_id'] as $key =>$package_id)
			    {
					
					  $sqlPackageCheckoutDate1  =  array();
						// query in tariff table
						$sqlPackageCheckoutDate1['QUERY'] = "select * 
																	FROM " . _DB_TARIFF_ACCOMMODATION_ . " accomodation
																	WHERE status = ?
																	AND id = ?";
						$sqlPackageCheckoutDate1['PARAM'][]  =  array('FILD' => 'status',       'DATA' => 'A',           'TYP' => 's');
						$sqlPackageCheckoutDate1['PARAM'][]  =  array('FILD' => 'id',     'DATA' => $package_id,     'TYP' => 's');
					
						// die();
						$resPackageCheckoutDate1 = $mycms->sql_select($sqlPackageCheckoutDate1);
						
						$totalRoomVal = $userDetails['accommodation_room'];
					    if($totalRoomVal==""){
							$totalRoomVal = 0;
						}
					
						//$accommodation_hotel_id           = $accommDetails['accommodation_hotel_id'];
						$accommodation_hotel_id           = $resPackageCheckoutDate1[0]['hotel_id'];
						//$accommodation_hotel_type_id      = $accommDetails['accommodation_roomType_id'];
						$explodeCheckin = explode('/',$accommDetails['hotel_dates'][$accommodation_hotel_id]['checkin']);
						$explodeCheckout = explode('/',$accommDetails['hotel_dates'][$accommodation_hotel_id]['checkout']);

						$check_in_date_id                 = $explodeCheckin[0];
						$check_out_date_id                = $explodeCheckout[0];
						$totalRoom = 0;
						$totalGuestCounter                 = 0;

						
						$sqlAccommodationDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
														WHERE `id` = '".$check_in_date_id."'";
																		
						$resultAccommodationDate        = $mycms->sql_select($sqlAccommodationDate); 
						$rowAccommodationDate           = $resultAccommodationDate[0];
						
						$check_in_date              = $rowAccommodationDate['check_in_date'];
						
						// GET ACCOMMODATION OUT DATE
						$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_."
																WHERE `id` = '".$check_out_date_id."'";
																				
						$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate); 
						$rowAccommodationOutDate           = $resultAccommodationOutDate[0];
						
						$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];
						
							
						$sqlFetchHotel['QUERY']    = "SELECT id ,package_name
											FROM "._DB_PACKAGE_ACCOMMODATION_."  
											WHERE  `id` = '".$resPackageCheckoutDate1[0]['package_id']."'
												AND `status` = 'A'";  //  AND `roomType_id` = '".$accommodation_hotel_type_id."'
												
						$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
						$resultfetch 	  = $resultFetchHotel[0];
						$packageId 	      = $resultfetch['id'];
						if($resPackageCheckoutDate1[0]['package_id']==''){
							$respackage_id = '0';
						}else{
						   $respackage_id =$resPackageCheckoutDate1[0]['package_id'];
						}
						$accTariffId = getAccommodationTariffId($respackage_id,$resPackageCheckoutDate1[0]['roomTypeId'],$check_in_date_id,$check_out_date_id,$cutoffId);
								

						$accomodationDetails['user_id']											 = $delegateId;
						//$accomodationDetails['accompany_name']									 = $accommDetails['accmName'];
						$accomodationDetails['accommodation_details']							 = $accommDetails['accmName'];
						$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
						//$accomodationDetails['guest_counter']									 = $accommDetails['room_guest_counter'][0];
						//$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
						$accomodationDetails['package_id']										 = $resPackageCheckoutDate1[0]['package_id'];
						$accomodationDetails['tariff_ref_id']								     = $accTariffId;
						$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
						$accomodationDetails['checkin_date']									 = $explodeCheckin[1];
						$accomodationDetails['checkout_date']									 = $explodeCheckout[1];
						if($resultfetch['package_name']=="Sharing"){
						$accomodationDetails['booking_quantity']                                 = 1;
						$accomodationDetails['booking_quantity_insert']								 = 0.5;
						}else{
						$accomodationDetails['booking_quantity']								 = $accommDetails['accmomdation-qty'][$accommodation_hotel_id][$resPackageCheckoutDate1[0]['roomTypeId']];
					    $accomodationDetails['booking_quantity_insert']						     = $accommDetails['accmomdation-qty'][$accommodation_hotel_id][$resPackageCheckoutDate1[0]['roomTypeId']];
						}
						$accomodationDetails['refference_invoice_id']							 = 0;
						$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
						$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];
						$accomodationDetails['payment_status']									 = 'UNPAID';
						$accomodationDetails['roomTypeId']									 = $resPackageCheckoutDate1[0]['roomTypeId'];

						$accompReqId	 = insertingAccomodationDetails($accomodationDetails);

						$accommRoomDetails['user_id']										 = $delegateId;
						$accommRoomDetails['request_accommodation_id']						 = $accompReqId;
						$accommRoomDetails['room_id']										 = 1;
						$accommRoomDetails['checkin_id']								     = $check_in_date_id;
						$accommRoomDetails['checkout_id']								     = $check_out_date_id;
						$accommRoomDetails['checkin_date']								     = $check_in_date;
						$accommRoomDetails['checkout_date']									 = $check_out_date;

								
						$accompReqRoomId	 = insertingAccomodationRoomDetails($accommRoomDetails);
						
						insertingInvoiceDetails($accompReqId,'ACCOMMODATION',$userDetails['registration_request'],'','',$paymentStatusPre);

						
					// }

					$sqlProcessUpdateRoom['QUERY']  = " UPDATE  "._DB_USER_REGISTRATION_."
															SET `accommodation_room` = '".$totalRoomVal."'
															WHERE `id` = '".$delegateId."' AND status='A'";													 
					$mycms->sql_update($sqlProcessUpdateRoom, false);
					
				
				}
					
			}
		}
		if($tourDetails)
		{

			foreach($tourDetails['tour_id'] as $key => $val)
			{
				$tourDetailsArray[$val]['user_id']               = $delegateId;
				$tourDetailsArray[$val]['package_id']            = $val;
				$tourDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
				$tourDetailsArray[$val]['booking_date']          = getTourDate($val);
				$tourDetailsArray[$val]['booking_quantity']      = $tourDetails['number_of_person'][$val];
				$tourDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
				$tourDetailsArray[$val]['refference_invoice_id'] = 0; // Need To Edit
				$tourDetailsArray[$val]['refference_slip_id']	 = $mycms->getSession('SLIP_ID');
				$tourDetailsArray[$val]['payment_status']        = 'UNPAID';
			}
			
			$tourReqId    	= insertingTourDetails($tourDetailsArray);
			foreach($tourReqId as $key => $reqId)
			{
				insertingInvoiceDetails($reqId,'TOUR','','','',$paymentStatusPre);
			}
		}
		
		if($dinnerDetails)
		{

			foreach($dinnerDetails as $key => $val)
			{
				$dinnerDetailsArray[$val]['delegate_id']           = $delegateId;
				$dinnerDetailsArray[$val]['refference_id']         = $delegateId;
				$dinnerDetailsArray[$val]['package_id']            = $val;
				$dinnerDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
				$dinnerDetailsArray[$val]['booking_quantity']      = 1;
				$dinnerDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
				$dinnerDetailsArray[$val]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
				$dinnerDetailsArray[$val]['payment_status']        = 'UNPAID';
			}
			
			$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray);
			foreach($dinerReqId as $key => $reqId)
			{
				if($counter == 'Y'){
					$invoiceIdDinner  =  insertingInvoiceDetails($reqId,'DINNER',$userDetails['registration_request'], $date,$counter,$paymentStatusPre);
				}
				else
				{
					$invoiceIdDinner  =  insertingInvoiceDetails($reqId,'DINNER',$userDetails['registration_request'], $date,'',$paymentStatusPre);
				}
				
				if($userDetailsArray['regsitaion_mode'] == "COMPLIMENTARY" || $userDetailsArray['regsitaion_mode'] == "ZERO_VALUE" || $clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE")
				{
					zeroValueInvoiceUpdate($invoiceIdDinner,'DINNER', $mycms->getSession('SLIP_ID'));
					$sqlUpdate = array();
					$sqlUpdate['QUERY']	 = "UPDATE "._DB_REQUEST_DINNER_."
												SET `payment_status` = ?
											  WHERE `id` = ?";
											  
					$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'ZERO_VALUE',  'TYP' => 's');
					$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$reqId,   'TYP' => 's');	
					$mycms->sql_update($sqlUpdate, false);
				}
			}
		}
		
		setUserPaymentStatus($delegateId);
		
		//registration_acknowledgement_message($delegateId, $mycms->getSession('SLIP_ID'), 'SEND');
		
		if($userDetails['reg_area'] == "FRONT")
		{
			online_welcome_message($delegateId,'SEND');
		}
		
		if($userDetails['reg_area'] == "BACK")
		{
			$mycms->removeSession('PROCESS_FLOW_ID');
			$mycms->removeSession('USER_DETAILS');				
			$mycms->getSession('USER_DETAILS',array());
			$mycms->removeSession('PROCESS_FLOW_ID_FRONT');
			$mycms->removeSession('CUTOFF_ID_FRONT');
			$mycms->removeSession('CLSF_ID_FRONT');
			$mycms->removeSession('USER_DETAILS_FRONT');
			$mycms->setSession('USER_DETAILS_FRONT',array());
		}
		else if($userDetails['reg_area'] == "FRONT")
		{
			$mycms->removeSession('PROCESS_FLOW_ID_FRONT');
			$mycms->removeSession('CUTOFF_ID_FRONT');
			$mycms->removeSession('CLSF_ID_FRONT');
			$mycms->removeSession('USER_DETAILS_FRONT');
			$mycms->setSession('USER_DETAILS_FRONT',array());
		}
	}
	return $delegateId;
}


function detailsUpdatingProcessForAbstract($processFlowId, $delegateId, $clasfPayMode='')
{
	
	global $cfg, $mycms;
	$sqlProcessFlow['QUERY']			= "SELECT * FROM "._DB_PROCESS_STEP_." WHERE `id` = '".$processFlowId."'";
	
	$resProcessFlow			= $mycms->sql_select($sqlProcessFlow);
	
	if($resProcessFlow)
	{
		$rowProcessFlow 	= $resProcessFlow[0];
		
		$userDetails		= unserialize($rowProcessFlow['step1']);
		
		$workshopDetails	= $userDetails['workshop_id'];
		$accompanyDetails	= unserialize($rowProcessFlow['step3']);
		$accommDetails		= unserialize($rowProcessFlow['step4']);
		$tourDetails		= unserialize($rowProcessFlow['step5']);
		$dinnerDetails		= $userDetails['dinner_value'];
		$fileDetails		= $rowProcessFlow['step10'];
		
	
		$cutoffId			= $userDetails['registration_cutoff'];
		$workshopCutoffId	= $userDetails['workshop_cutoff'];
		$clsfId				= $userDetails['registration_classification_id'][0];
		
		$date				= $userDetails['date'];
		
		if($userDetails)
		{
			$delegateId		 = $_REQUEST['abstractDelegateId'];
			$userRec 		= getUserDetails($delegateId);
			$userDetailsArray['user_email_id']                        = addslashes(trim(strtolower($userDetails['user_email_id'])));
			$userDetailsArray['comunication_email']                   = addslashes(trim(strtolower($userDetails['comunication_email'])));
			$userDetailsArray['user_password_raw']                    = $userDetails['user_password'];
			$userDetailsArray['user_password']                        = $mycms->encoded($userDetails['user_password']);
			$userDetailsArray['user_initial_title']   				  = addslashes(trim(strtoupper($userDetails['user_initial_title'])));
			$userDetailsArray['user_first_name']       				  = addslashes(trim(strtoupper($userDetails['user_first_name'])));
			$userDetailsArray['user_middle_name']               	  = addslashes(trim(strtoupper($userDetails['user_middle_name'])));
			$userDetailsArray['user_last_name']                       = addslashes(trim(strtoupper($userDetails['user_last_name'])));
			$userDetailsArray['user_full_name']                       = $userDetailsArray['user_initial_title']." ".$userDetailsArray['user_first_name']." ".$userDetailsArray['user_middle_name']." ".$userDetailsArray['user_last_name'];
			$userDetailsArray['user_full_name']                       = preg_replace('/\s+/', ' ', $userDetailsArray['user_full_name']);
			$userDetailsArray['user_mobile_isd_code']                 = addslashes(trim(strtoupper($userDetails['user_usd_code'])));
			$userDetailsArray['user_mobile_no']                       = addslashes(trim(strtoupper($userDetails['user_mobile'])));
			$userDetailsArray['user_phone_no']                        = addslashes(trim(strtoupper($userDetails['user_phone'])));
			$userDetailsArray['user_address']                         = addslashes(trim(strtoupper($userDetails['user_address'])));
			$userDetailsArray['user_country']                         = addslashes(trim(strtoupper($userDetails['user_country'])));
			$userDetailsArray['user_state']                           = addslashes(trim(strtoupper($userDetails['user_state'])));
			$userDetailsArray['user_city']                            = addslashes(trim(strtoupper($userDetails['user_city'])));
			$userDetailsArray['user_postal_code']                     = addslashes(trim(strtoupper($userDetails['user_postal_code'])));
			$userDetailsArray['user_dob_year']                        = addslashes(trim(strtoupper($userDetails['user_dob_year'])));
			$userDetailsArray['user_dob_month']                       = addslashes(trim(strtoupper($userDetails['user_dob_month'])));
			$userDetailsArray['user_dob_day']                         = addslashes(trim(strtoupper($userDetails['user_dob_day'])));
			if($userDetailsArray['user_dob_year'] !="" && $userDetailsArray['user_dob_month'] !="" && $userDetailsArray['user_dob_day'] !="")
			{
				$userDetailsArray['user_dob']                         = number_pad($userDetailsArray['user_dob_year'],4)."-".number_pad($userDetailsArray['user_dob_month'], 2)."-".number_pad($userDetailsArray['user_dob_day'], 2);
			}
			
			$userDetailsArray['user_gender']                          = ($userDetails['user_gender']=='')?'NA':$userDetails['user_gender'];
			$userDetailsArray['user_designation']                     = addslashes(trim(strtoupper($userDetails['user_designation'])));
			$userDetailsArray['user_depertment']                      = addslashes(trim(strtoupper($userDetails['user_depertment'])));
			$userDetailsArray['user_institution_name']                = addslashes(trim(strtoupper($userDetails['user_institution'])));
			$userDetailsArray['user_other_food_details']              = addslashes(trim(strtoupper($userDetails['user_food_details'])));
			$userDetailsArray['passport_no']                      	  = addslashes(trim(strtoupper($userDetails['user_pasport_no'])));
			$userDetailsArray['passport_expiry_date']                 = number_pad($userDetails['user_pasport_exp_year'], 4)."-".number_pad($userDetails['user_pasport_exp_month'], 2)."-".number_pad($userDetails['user_pasport_exp_day'], 2);
			
			$userDetailsArray['user_document']						  = $fileDetails;
			
			$userDetailsArray['membership_number']                    = addslashes(trim($userDetails['membership_number']));
			$userDetailsArray['isRegistration']						  = 'Y';
			$userDetailsArray['isConference']						  = 'Y';
			$userDetailsArray['isWorkshop']							  = 'N';
			$userDetailsArray['isAccommodation']                      = 'N';
			$userDetailsArray['isTour']								  = 'N';
			$userDetailsArray['IsAbstract']							  = 'N';
			$userDetailsArray['user_food_preference']                 = $userDetails['user_food_preference'];
			
			$userDetailsArray['registration_classification_id']		  = $userDetails['registration_classification_id'][0];
			$userDetailsArray['registration_tariff_cutoff_id']        = $userDetails['registration_cutoff'];
			$userDetailsArray['registration_request']       		  = $userDetails['registration_request'];
			$userDetailsArray['operational_area']   	    		  = $userDetails['registration_request'];
			$userDetailsArray['registration_payment_status']		  = 'UNPAID';
			$userDetailsArray['registration_mode']					  = ($mycms->getSession('REGISTRATION_MODE')=="ONLINE")?"ONLINE":"OFFLINE";
			$userDetailsArray['account_status']						  = 'REGISTERED';
			$userDetailsArray['reg_type']              				  = addslashes(trim(strtoupper($userDetails['reg_area'])));
			
			$accDetails = getUserTypeAndRoomType($userDetailsArray['registration_classification_id']);
			
			$sqlUpdateUser['QUERY']  = "UPDATE "._DB_USER_REGISTRATION_."
										  SET `refference_delegate_id`		   = '0',
											  `user_type` 	        		   = 'DELEGATE',
											  `user_email_id`        		   = '".$userDetailsArray['user_email_id']."',
											  `user_first_name`       		   = '".$userDetailsArray['user_first_name']."',
											  `user_middle_name`     		   = '".$userDetailsArray['user_middle_name']."',
											  `user_last_name`       		   = '".$userDetailsArray['user_last_name']."',
											  `user_full_name` 				   = '".$userDetailsArray['user_full_name']."',		
											  `user_mobile_isd_code`  		   = '".$userDetailsArray['user_mobile_isd_code']."',
											  `user_mobile_no`				   = '".$userDetailsArray['user_mobile_no']."',
											  `user_phone_no`				   = '".$userDetailsArray['user_phone_no']."',
											  `user_address`			       = '".$userDetailsArray['user_address'] ."',
											  `user_city`				   	   = '".$userDetailsArray['user_city']."',
											  `user_pincode`				   = '".$userDetailsArray['user_postal_code']."',
											  `isRegistration`       		   = '".$userDetailsArray['isRegistration']."',
											  `isConference`         		   = '".$userDetailsArray['isConference']."',
											  `isWorkshop` 					   = '".$userDetailsArray['isWorkshop']."',	
											  `user_food_preference`		   = '".$userDetailsArray['user_food_preference']."',										 
											  `registration_classification_id` = '".$userDetailsArray['registration_classification_id']."',
											  `registration_tariff_cutoff_id`  = '".$userDetailsArray['registration_tariff_cutoff_id']."',
											  `membership_number`  		  	   = '".$userDetailsArray['membership_number']."',
											  `registration_request`  		   = '".$userDetailsArray['registration_request']."',
											  `operational_area`        	   = '".$userDetailsArray['operational_area']."',
											  `registration_payment_status`    = '".$userDetailsArray['registration_payment_status']."',
											  `registration_mode`    		   = '".$userDetailsArray['registration_mode']."',
											  `account_status`				   = '".$userDetailsArray['account_status']."',
											  `reg_type`					   = '".$userDetailsArray['reg_type']."'
										WHERE `id` 					 		   = '".$delegateId."'";
			$mycms->sql_update($sqlUpdateUser, false);
			
			if($mycms->getSession('SLIP_ID') =="")
			{
				$mycms->setSession('LOGGED.USER.ID',$delegateId);
				
				$slip_id = insertingSlipDetails($delegateId,$userDetailsArray['registration_mode'],$userDetails['registration_request'], $date);
			}
			
			$invoiceIdConf = insertingInvoiceDetails($delegateId,'CONFERENCE',$userDetails['registration_request'], $date);
			
			if($clasfPayMode == "COMPLIMENTARY" || $clasfPayMode == "ZERO_VALUE")
			{
				if($clasfPayMode == "COMPLIMENTARY")
				{
					complimentarySlipUpdate($mycms->getSession('SLIP_ID'));
					zeroValueInvoiceUpdate($invoiceIdConf,'CONFERENCE', $mycms->getSession('SLIP_ID'));
				}
				else if($clasfPayMode == "ZERO_VALUE")
				{
					zeroValueSlipUpdate($mycms->getSession('SLIP_ID'));
					zeroValueInvoiceUpdate($invoiceIdConf,'CONFERENCE', $mycms->getSession('SLIP_ID'));
				}
				
				$sqlUpdate = array();
				$sqlUpdate['QUERY']	 = "UPDATE "._DB_INVOICE_."
											SET `payment_status` = ?
										  WHERE `id` = ?";
													  
				$sqlUpdate['PARAM'][]   = array('FILD' => 'payment_status',   'DATA' =>'COMPLIMENTARY',  'TYP' => 's');
				$sqlUpdate['PARAM'][]   = array('FILD' => 'id',               'DATA' =>$invoiceIdConf,   'TYP' => 's');		
									 
				$mycms->sql_update($sqlUpdate, false);
				
				$sqlUpdateUserReg = array();
				$sqlUpdateUserReg['QUERY']	   = "UPDATE "._DB_USER_REGISTRATION_."
													 SET `registration_payment_status` = ?
											  	   WHERE `id` = ?";
											  
				$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'registration_payment_status',   'DATA' =>'COMPLIMENTARY', 'TYP' => 's');
				$sqlUpdateUserReg['PARAM'][]   = array('FILD' => 'id',                            'DATA' =>$delegateId	,   'TYP' => 's');
				
				$mycms->sql_update($sqlUpdateUserReg, false);
			}
			
			if($clsfId==14)
			{
				$invoiceId 			= zeroValueInvoiceUpdate($invoiceIdConf,'CONFERENCE',$mycms->getSession('SLIP_ID')); //UPDATING COMPLIMENTARY INVOICE
			}
			
			if($accDetails['TYPE']=='COMBO')
			{								
				$accomodationDetails['user_id']											 = $delegateId;
				$accomodationDetails['hotel_id']										 = 1;
				
				$accomodationDetails['roomType_id']										 = $accDetails['ROOM_TYPE'];
				$accomodationDetails['package_id']										 = $accDetails['ROOM_TYPE'];
				$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
				$accomodationDetails['checkin_date']									 = 1;
				$accomodationDetails['checkout_date']									 = 4;
				$accomodationDetails['booking_quantity']								 = 1;
				$accomodationDetails['type']								 			 = "COMBO";
				$accomodationDetails['refference_invoice_id']							 = $invoiceIdConf;
				$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
				$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];
				$accomodationDetails['payment_status']									 = 'UNPAID';
				
				$accompReqId	 = insertingAccomodationDetails($accomodationDetails);				
			}
		}
		
		if($workshopDetails)
		{
			foreach($workshopDetails as $key => $workshopId)
			{
				$workshopDetailArray[$workshopId]['delegate_id']        			= $delegateId;
				$workshopDetailArray[$workshopId]['workshop_id']      				= $workshopId;
				$workshopDetailArray[$workshopId]['tariff_cutoff_id']      			= $workshopCutoffId;
				$workshopDetailArray[$workshopId]['workshop_tarrif_id']       		= getWorkshopTariffId($workshopId,$workshopCutoffId,$clsfId);
				$workshopDetailArray[$workshopId]['registration_classification_id'] = $clsfId;
				$workshopDetailArray[$workshopId]['booking_mode']        			= $userDetailsArray['registration_mode'];
				$workshopDetailArray[$workshopId]['registration_type']       		= $userDetails['registration_request'];
				$workshopDetailArray[$workshopId]['refference_invoice_id']       	= 0; // Need To Edit
				$workshopDetailArray[$workshopId]['refference_slip_id']       		= $mycms->getSession('SLIP_ID');
				$workshopDetailArray[$workshopId]['payment_status']        			= 'UNPAID';
			}
			
			$workshopReqId	 = insertingWorkshopDetails($workshopDetailArray);
			
			foreach($workshopReqId as $key => $reqId)
			{	
				$invoiceIdWrkshp = insertingInvoiceDetails($reqId,'WORKSHOP',$userDetails['registration_request'], $date);
				if($clsfId==11 && $userDetails['registration_request'] != 'COUNTER') // Free Workshop for PGT
				{
					$invoiceId = zeroValueInvoiceUpdate($invoiceIdWrkshp,'WORKSHOP',$mycms->getSession('SLIP_ID')); //UPDATING COMPLIMENTARY INVOICE
				}
				
			}
		}
		
		if($accompanyDetails)
		{
			foreach($accompanyDetails['accompany_selected_add'] as $key => $val)
			{ 
				$accompanyDetailsArray[$val]['refference_delegate_id']               = $delegateId;
				$accompanyDetailsArray[$val]['user_full_name']                       = addslashes(trim(strtoupper($accompanyDetails['accompany_name_add'][$val])));
				$accompanyDetailsArray[$val]['user_age']                    		 = addslashes(trim(strtoupper($accompanyDetails['accompany_age_add'][$val])));
				$accompanyDetailsArray[$val]['user_food_preference']                 = addslashes(trim(strtoupper($accompanyDetails['accompany_food_choice'][$val])));
				$accompanyDetailsArray[$val]['user_food_details']                    = addslashes(trim(strtoupper($accompanyDetails['accompany_food_details_add'][$val])));
				$accompanyDetailsArray[$val]['accompany_relationship']               = addslashes(trim(strtoupper('ACCOMPANY')));
				
				$accompanyDetailsArray[$val]['isRegistration']              		 = 'Y';
				$accompanyDetailsArray[$val]['isConference']            	  		 = 'Y';
				$accompanyDetailsArray[$val]['registration_classification_id']		 = addslashes(trim(strtoupper($accompanyDetails['accompanyClasfId'])));
				$accompanyDetailsArray[$val]['registration_tariff_cutoff_id']        = $cutoffId;
				$accompanyDetailsArray[$val]['registration_request']       		 	 = $userDetails['registration_request'];
				$accompanyDetailsArray[$val]['operational_area']   	    		 	 = $userDetails['registration_request'];
				$accompanyDetailsArray[$val]['registration_payment_status']			 = 'UNPAID';
				$accompanyDetailsArray[$val]['registration_mode']					 = $userDetailsArray['registration_mode'];
				$accompanyDetailsArray[$val]['account_status']						 = 'REGISTERED';
				$accompanyDetailsArray[$val]['reg_type']              				 = addslashes(trim(strtoupper($userDetails['reg_area'])));
			
			}
			
			
			$accompanyReqId	 = insertingAccompanyDetails($accompanyDetailsArray, $date);
			
			$accDinnerDetails = $accompanyDetails['banquet_dinner_add'];
						
			if($accDinnerDetails)
			{

				foreach($accDinnerDetails as $key => $val)
				{
					$dinnerDetailsArray1[$key]['refference_id']         = $accompanyReqId[$key-1];
					$dinnerDetailsArray1[$key]['delegate_id']           = $delegateId;
					$dinnerDetailsArray1[$key]['package_id']            = 1;
					$dinnerDetailsArray1[$key]['tariff_cutoff_id']      = $cutoffId;
					$dinnerDetailsArray1[$key]['booking_quantity']      = 1;
					$dinnerDetailsArray1[$key]['booking_mode']          = $userDetailsArray['registration_mode'];
					$dinnerDetailsArray1[$key]['refference_invoice_id'] = 0; // Need To Edit
					$dinnerDetailsArray1[$key]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
					$dinnerDetailsArray1[$key]['payment_status']        = 'UNPAID';
				}
				
					
				$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray1);
				
				foreach($dinerReqId as $key => $reqId)
				{
					
					insertingInvoiceDetails($reqId,'DINNER',$userDetails['registration_request'], $date);
				}
				
			}
		
			foreach($accompanyReqId as $key =>$reqId)
			{
				insertingInvoiceDetails($reqId,'ACCOMPANY',$userDetails['registration_request'], $date);
			}
			
		}
		
		if($accommDetails)
		{
			$check_in_date_id                = $accommDetails['check_in_date'];
			$check_out_date_id               = $accommDetails['check_out_date'];
			$accommodation_hotel_id       = $accommDetails['accommodation_hotel_id'];
			$accommodation_hotel_type_id  = $accommDetails['accommodation_roomType_id'];
			$totalRoom = 0;
			$totalGuestCounter                 = 0;
			foreach($accommDetails['room_guest_counter'] as $key=>$resDetails )
			{
				$totalRoom++;
				if($resDetails!=""){
							
					$totalGuestCounter        += $resDetails;
				}
			}
			$sqlAccommodationDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKIN_DATE_." 
											   WHERE `id` = '".$check_in_date_id."'";
															
			$resultAccommodationDate        = $mycms->sql_select($sqlAccommodationDate); 
			$rowAccommodationDate           = $resultAccommodationDate[0];
			
			$check_in_date              = $rowAccommodationDate['check_in_date'];
			
			// GET ACCOMMODATION OUT DATE
			$sqlAccommodationOutDate['QUERY']           = "SELECT * FROM "._DB_ACCOMMODATION_CHECKOUT_DATE_."
													   WHERE `id` = '".$check_out_date_id."'";
																	
			$resultAccommodationOutDate        = $mycms->sql_select($sqlAccommodationOutDate); 
			$rowAccommodationOutDate           = $resultAccommodationOutDate[0];
			
			$check_out_date             	   = $rowAccommodationOutDate['check_out_date'];
			
				
			$sqlFetchHotel['QUERY']    = "SELECT id 
								   FROM "._DB_PACKAGE_ACCOMMODATION_."  
								  WHERE  `hotel_id` = '".$accommodation_hotel_id."'
									  AND `roomType_id` = '".$accommodation_hotel_type_id."'
									  AND `status` = 'A'"; 
									  
			$resultFetchHotel = $mycms->sql_select($sqlFetchHotel);	
			$resultfetch 	  = $resultFetchHotel[0];
			$packageId 	      = $resultfetch['id'];
			$accTariffId = getAccommodationTariffId($packageId,$check_in_date_id,$check_out_date_id,$cutoffId);
			
			$accomodationDetails['user_id']											 = $delegateId;
			$accomodationDetails['accompany_name']									 = $accommDetails['accmName'];
			$accomodationDetails['hotel_id']										 = $accommodation_hotel_id;
			$accomodationDetails['guest_counter']									 = $accommDetails['room_guest_counter'][0];
			$accomodationDetails['roomType_id']										 = $accommodation_hotel_type_id;
			$accomodationDetails['package_id']										 = $packageId;
			$accomodationDetails['tariff_ref_id']								     = $accTariffId;
			$accomodationDetails['tariff_cutoff_id']								 = $cutoffId;
			$accomodationDetails['checkin_date']									 = $check_in_date;
			$accomodationDetails['checkout_date']									 = $check_out_date;
			$accomodationDetails['booking_quantity']								 = $accommDetails['booking_quantity'];
			$accomodationDetails['refference_invoice_id']							 = 0;
			$accomodationDetails['refference_slip_id']								 = $mycms->getSession('SLIP_ID');
			$accomodationDetails['booking_mode']									 = $userDetailsArray['registration_mode'];
			$accomodationDetails['payment_status']									 = 'UNPAID';
			
			$accompReqId	 = insertingAccomodationDetails($accomodationDetails);
			insertingInvoiceDetails($accompReqId,'ACCOMMODATION');
		
		}
				
		if($tourDetails)
		{

			foreach($tourDetails['tour_id'] as $key => $val)
			{
				$tourDetailsArray[$val]['user_id']               = $delegateId;
				$tourDetailsArray[$val]['package_id']            = $val;
				$tourDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
				$tourDetailsArray[$val]['booking_date']          = getTourDate($val);
				$tourDetailsArray[$val]['booking_quantity']      = $tourDetails['number_of_person'][$val];
				$tourDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
				$tourDetailsArray[$val]['refference_invoice_id'] = 0; // Need To Edit
				$tourDetailsArray[$val]['refference_slip_id']	 = $mycms->getSession('SLIP_ID');
				$tourDetailsArray[$val]['payment_status']        = 'UNPAID';
			}
			
			$tourReqId    	= insertingTourDetails($tourDetailsArray);
			foreach($tourReqId as $key => $reqId)
			{
				insertingInvoiceDetails($reqId,'TOUR');
			}
		}
		
		if($dinnerDetails)
		{

			foreach($dinnerDetails as $key => $val)
			{
				$dinnerDetailsArray[$val]['delegate_id']           = $delegateId;
				$dinnerDetailsArray[$val]['refference_id']         = $delegateId;
				$dinnerDetailsArray[$val]['package_id']            = $val;
				$dinnerDetailsArray[$val]['tariff_cutoff_id']      = $cutoffId;
				$dinnerDetailsArray[$val]['booking_quantity']      = 1;
				$dinnerDetailsArray[$val]['booking_mode']          = $userDetailsArray['registration_mode'];
				$dinnerDetailsArray[$val]['refference_invoice_id'] = 0; // Need To Edit
				$dinnerDetailsArray[$val]['refference_slip_id']	   = $mycms->getSession('SLIP_ID');
				$dinnerDetailsArray[$val]['payment_status']        = 'UNPAID';
			}
			
			$dinerReqId    	= insertingDinnerDetails($dinnerDetailsArray);
			foreach($dinerReqId as $key => $reqId)
			{
				insertingInvoiceDetails($reqId,'DINNER',$userDetails['registration_request'], $date);
			}
		}
		
		setUserPaymentStatus($delegateId);
		
		//registration_acknowledgement_message($delegateId, $mycms->getSession('SLIP_ID'), 'SEND');
		if($userDetails['registration_request']!='COUNTER' && $clsfId!='14' && $userDetails['reg_area'] == "FRONT")
		{
			welcome_message($delegateId,'SEND');
		}
		if($userDetailsArray['registration_mode']=="OFFLINE")
		{
			//offline_registration_acknowledgement_message($delegateId, $mycms->getSession('SLIP_ID'), 'SEND');
		}		
		
		if($userDetails['reg_area'] == "BACK")
		{
			$mycms->removeSession('PROCESS_FLOW_ID');
			$mycms->removeSession('USER_DETAILS');				
			$mycms->getSession('USER_DETAILS',array());
			
		}
		else if($userDetails['reg_area'] == "FRONT")
		{
			$mycms->removeSession('PROCESS_FLOW_ID_FRONT');
			$mycms->removeSession('CUTOFF_ID_FRONT');
			$mycms->removeSession('CLSF_ID_FRONT');
			//$mycms->removeSession('REGISTRATION_MODE');
			$mycms->removeSession('USER_DETAILS_FRONT');
			$mycms->setSession('USER_DETAILS_FRONT',array());
		}
		
	}
	return $slip_id;
}

function getUserTypeAndRoomType($regClasfId="")
{
	global $cfg, $mycms;
	
	$sqlRegClasf            = array();
	$sqlRegClasf['QUERY']	= "SELECT * FROM "._DB_REGISTRATION_CLASSIFICATION_." 
								 WHERE status = ?
								 AND `id` =?";
								 
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',             'TYP' => 's');
	$sqlRegClasf['PARAM'][]  = array('FILD' => 'id',     'DATA' =>$regClasfId,     'TYP' => 's');
	
	$resRegClasf			= $mycms->sql_select($sqlRegClasf);
	
	$arr['TYPE']			= $resRegClasf[0]['type'];
	if($resRegClasf[0]['id']== 8)
	{
		$arr['ROOM_TYPE']			= 1;
	}
	if($resRegClasf[0]['id']== 9)
	{
		$arr['ROOM_TYPE']			= 2;
	}
	
	return $arr;
}

function getSeatlimitToClassificationID($classification_id)
{

	global $cfg, $mycms;
	$sqlRegClasf            = array();
	$sqlRegClasf['QUERY']	= "SELECT seat_limit FROM "._DB_REGISTRATION_CLASSIFICATION_." 
								 WHERE status = 'A'
								 AND `id` ='".$classification_id."'";
	$resRegClasf			= $mycms->sql_select($sqlRegClasf);	

	$presentSeatLimit = $resRegClasf[0]['seat_limit'];

	$seatExistSql = array();
	
	$seatExistSql['QUERY']	= "SELECT count(*) AS COUNTDATA FROM "._DB_USER_REGISTRATION_." U INNER JOIN
								"._DB_INVOICE_."  I ON U.id = I.delegate_id
								 WHERE U.status = 'A' AND I.status = 'A'
								 AND U.`registration_classification_id` ='".$classification_id."' AND I.service_type = 'DELEGATE_CONFERENCE_REGISTRATION' AND (I.payment_status = 'PAID' OR I.payment_status = 'COMPLIMENTARY')";

	$row = $mycms->sql_select($seatExistSql);

	$seatLimit = $presentSeatLimit -  $row[0]['COUNTDATA'];						 

	
	return $seatLimit;					 

}

function getWorkshopClassificationSeatLimit($classification_id)
{

	global $cfg, $mycms;
	$sqlRegClasf            = array();
	$sqlRegClasf['QUERY']	= "SELECT seat_limit FROM "._DB_WORKSHOP_CLASSIFICATION_." 
								 WHERE status = 'A' AND display='Y'
								 AND `id` ='".$classification_id."'";
	$resRegClasf			= $mycms->sql_select($sqlRegClasf);	

	//echo '<pre>'; print_r($sqlRegClasf);

	 $presentSeatLimit = $resRegClasf[0]['seat_limit'];

	 $seatExistSql = array();
	$seatExistSql['QUERY']	= "SELECT count(*) AS COUNTDATA FROM "._DB_REQUEST_WORKSHOP_." W INNER JOIN "._DB_USER_REGISTRATION_." U ON W.delegate_id=U.id WHERE W.workshop_id = '".$classification_id."' AND W.status = 'A' AND (W.payment_status='PAID' OR W.payment_status='ZERO_VALUE' OR W.payment_status='COMPLIMENTARY') AND U.status = 'A' ";

	$row = $mycms->sql_select($seatExistSql);

	

	$seatLimit = $presentSeatLimit -  $row[0]['COUNTDATA'];	

	return $seatLimit;
}

function weatherApi()
{	global $mycms,$cfg;
	$sql 	=	array();
                $sql['QUERY'] = "SELECT `city_for_weather_api` FROM "._DB_COMPANY_INFORMATION_." 
                                        WHERE `id`!=''";
                $result 	 = $mycms->sql_select($sql);
                if($result)
                {
					$city=$result[0]['city_for_weather_api'];
				}
				else{
					$city='Kolkata';
				}
	$apiEndpoint = 'https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/'.$city;
	$apiKey = 'TQZL9Q98A84EKW4YT8M73XKWH';
	$unitGroup = 'metric';

	// $startDate = date('Y-m-d'); // today
    // $endDate = date('Y-m-d', strtotime('+3 days')); // three days from now

	$startDate = date('Y-m-d',strtotime($cfg['CONF_START_DATE'])); // today
    $endDate = date('Y-m-d', strtotime($cfg['CONF_END_DATE'])); // three days from now

	// Build the URL with parameters
	$url = "{$apiEndpoint}/{$startDate}/{$endDate}?unitGroup={$unitGroup}&key={$apiKey}&contentType=json";
	// $url = "{$apiEndpoint}?unitGroup={$unitGroup}&key={$apiKey}&contentType=json";

	// Initialize cURL session
	$ch = curl_init($url);

	// Set cURL options
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Execute cURL session and get the response
	$response = curl_exec($ch);

	// Check if the request was successful
	if ($response === FALSE) {

	    die('Error occurred while fetching data from the API');
	}

	// Close cURL session
	curl_close($ch);

	// Decode the JSON response
	$data = json_decode($response, true);

	// Output the data (you may want to format or process it based on your requirements)

	 return $data;
}


function getPlaceName($latitude, $longitude) {
    // Google Maps API Key
    $apiKey = 'AIzaSyCN4C6WrCAY1CxMkv5lD6zof3kGIhg8YXU';

    // Build the Reverse Geocoding API URL
    $apiUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

    // Initialize cURL session
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session and get the response
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Check for a valid response
    if (!empty($data['results']) && isset($data['results'][0]['formatted_address'])) {
        return $data['results'][0]['formatted_address']; // Full address
    } else {
        return "Unknown Location"; // Fallback if no address is found
    }
}




?>