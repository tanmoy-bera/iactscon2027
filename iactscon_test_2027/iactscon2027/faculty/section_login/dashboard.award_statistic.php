<?php
	include_once('includes/init.php');
	
	$awardArray         = array();
	
	$sqlFetchAward['QUERY']		= "SELECT mainTAB.award_category,
	                              IFNULL(totalAwardCount, 0) AS totalAwardCount
							 
							 FROM (	  
									   SELECT award_category,
											  COUNT(*) AS totalAwardCount	
				
										 FROM "._DB_AWARD_REQUEST_." awardRequest 
										WHERE awardRequest.status = 'A' 
										  AND (awardRequest.award_category = 'AWARD_PG_STUDENT' 
										       OR awardRequest.award_category = 'AWARD_TECHNOLOGIST' 
											   OR awardRequest.award_category = 'AWARD_DR_JG_JOLLY')
									
									 GROUP BY awardRequest.award_category
						          ) mainTAB ";
						 
	$resultAwardRequest = $mycms->sql_select($sqlFetchAward);
	
	if($resultAwardRequest)
	{
		foreach($resultAwardRequest as $keyAwardRequest=>$rowAwardRequest)
		{
			$awardArray[$keyAwardRequest] = "{award: '".$rowAwardRequest['award_category']."', val: ".$rowAwardRequest['totalAwardCount']."}";
		}
	}
?>
	<html>
		<head>
			<title>:: Abstract ::</title>
			<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/style.css" type="text/css" />
			<script language="javascript" src="<?=_BASE_URL_?>js/jquery.js"></script>
		</head>
		<body style="background-color:#DEDEDC; margin: 0px; padding: 0px;">
			<div style="padding:62px 20px 0 20px;">
				<script type="text/javascript">
					$(function (){
						
						var dataSource = [
							<?=implode(",",$awardArray)?>
						];
					
						$("#chartContainer").dxPieChart({
							dataSource: dataSource,
							legend: {
								visible: false,
								horizontalAlignment: "right",
								verticalAlignment: "bottom",
								margin: 0
							},
							palette: ['#FFFFFF', '#AF251B', '#000000'],
							series: [{
										argumentField: "award",
										label: {
											visible: false,
											format: "largeNumber",
											connector: {
												visible: false
											}
										} 
									}]
						});
					
					});
				</script>
				<div id="chartContainer" style="width: 100%; height: 160px;"></div>
			</div>
			
			<script src="<?=_BASE_URL_?>js/adminPanel/all.js"></script>
			<script src="<?=_BASE_URL_?>js/table/stupidtable.js"></script>
			<script src="<?=_BASE_URL_?>js/graph/dx.chartjs.js"></script>
			<script src="<?=_BASE_URL_?>js/graph/globalize.min.js"></script>
			<script src="<?=_BASE_URL_?>js/graph/knockout-2.2.1.js"></script>
		</body>
	</html>