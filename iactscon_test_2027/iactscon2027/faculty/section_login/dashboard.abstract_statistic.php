<?php
	include_once('includes/init.php');
	
	$abstractArray      = array();
	
	$sqlFetchAbstract['QUERY']	= "SELECT mainTAB.abstract_parent_type,
	                              IFNULL(totalAbstractCount, 0) AS totalAbstractCount
							 
							 FROM (	  
									   SELECT abstract_parent_type,
											  TIMESTAMPDIFF(DAY, abstractRequest.created_dateTime, '".date('Y-m-d H:i:s')."') AS dayPassedFromSubmit,
											  COUNT(*) AS totalAbstractCount	
				
										 FROM "._DB_ABSTRACT_REQUEST_." abstractRequest 
										WHERE abstractRequest.status = 'A' 
										  AND (abstractRequest.abstract_parent_type = 'FREE_PAPER_SESSION' 
										       OR abstractRequest.abstract_parent_type = 'VIDEO_PRESENTATION')
									
									 GROUP BY abstractRequest.abstract_parent_type
						          ) mainTAB";
						 
	$resultAbstractRequest = $mycms->sql_select($sqlFetchAbstract);
	
	if($resultAbstractRequest)
	{
		foreach($resultAbstractRequest as $keyAbstractRequest=>$rowAbstractRequest)
		{
			$abstractArray[$keyAbstractRequest] = "{abstract: '".$rowAbstractRequest['abstract_parent_type']."', val: ".$rowAbstractRequest['totalAbstractCount']."}";
		}
	}
?>
	<html>
		<head>
			<title>:: Abstract ::</title>
			<link rel="stylesheet" href="<?=_BASE_URL_?>css/adminPanel/<?=$cfg['THEME']?>/style.css" type="text/css" />
			<script language="javascript" src="<?=_BASE_URL_?>js/jquery.js"></script>
		</head>
		<body style="background-color:#838280; margin: 0px; padding: 0px;">
			<div style="padding:63px 20px 0 20px;">
				<script type="text/javascript">
					$(function (){
						
						var dataSource = [
							<?=implode(",", $abstractArray)?>
						];
					
						$("#chartContainer").dxPieChart({
							dataSource: dataSource,
							legend: {
								visible: false,
								horizontalAlignment: "right",
								verticalAlignment: "bottom",
								margin: 0
							},
							palette: ['#FFFFFF', '#AF251B'],
							series: [{
										argumentField: "abstract",
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
			<script src="<?=_BASE_URL_?>js/graph/dx.chartjs.js"></script>
			<script src="<?=_BASE_URL_?>js/graph/globalize.min.js"></script>
			<script src="<?=_BASE_URL_?>js/graph/knockout-2.2.1.js"></script>
		</body>
	</html>