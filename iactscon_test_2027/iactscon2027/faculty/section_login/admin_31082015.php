<?php
	include_once('includes/init.php');
	page_header("Reviewer Panel");
?>
	<div class="container">
	
		<!--<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">5</span> 
				Award - Free Paper Submission
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">2</span> 
				Award - Free Paper Reviewed
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">2</span> 
				Video Presentation Submission
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">1</span> 
				Video Presentation Reviewed
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">2</span> 
				Award - PG Student Submission
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">1</span> 
				Award - PG Student Reviewed
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">4</span> 
				Award - Technologist Submission
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">3</span> 
				Award - Technologist Reviewed
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">4</span> 
				Award - J. G. Jolly Submission
			</div>
		</div>
		
		<div class="dashboard_report defaultState" style="width:22%;">
			<div class="pad">
				<span class="value">3</span> 
				Award - J. G. Jolly Reviewed
			</div>
		</div>-->
		
		<table width="100%">
			<tr>
				<td align="center" valign="top" style="margin:0px; padding:0px;">
					<script type="text/javascript">
						$(function (){
							
							var dataSource = [
								{region: "Abstract - Free Paper Submission", val: 12},
								{region: "Video Presentation Submission", val: 16},
								{region: "Award - PG Student Submission", val: 19},
								{region: "Award - Technologist Submission", val: 28},
								{region: "Award - J. G. Jolly Submission", val: 8}
							];
						
							$("#chartContainer").dxPieChart({
								dataSource: dataSource,
								tooltip: {
									enabled: false,
									format:"largeNumber",
									percentPrecision: 2,
									customizeText: function() { 
										return this.valueText + " - " + this.percentText;
									}
								},
								legend: {
									visible: false,
									horizontalAlignment: "right",
									verticalAlignment: "bottom",
									margin: 0
								},
								palette: ['#D82133', '#000000', '#9E9E9E', '#704942', '#DEB887', '#08315F'],
								series: [{
											type: "doughnut",
											argumentField: "region",
											label: {
												visible: true,
												format: "largeNumber",
												connector: {
													visible: true
												}
											}
										}]
							});
						
						});
					</script>
					<div id="chartContainer" style="width: 100%; height: 469px;"></div>
				</td>
				<td width="350" align="center" valign="top" style="margin:0px; padding:0px;">
					
					<!--<table width="100%">
						<tr style="background-color:#D82133;">
							<td align="left"></td>
							<td width="100" align="right" style="color:#FFFFFF; font-size:13px;">Submission</td>
							<td width="100" align="right" style="color:#FFFFFF; font-size:13px;">Reviewed</td>
						</tr>
						<tr>
							<td align="left"><span style="font-size:13px;">Abstract - Free Paper</span></td>
							<td align="right"></td>
							<td align="right"></td>
						</tr>
						<tr style="background-color:#E5E5E5;">
							<td align="left"><span style="font-size:13px;">Video Presentation Submission</span></td>
							<td align="right"></td>
							<td align="right"></td>
						</tr>
						<tr>
							<td align="left"><span style="font-size:13px;">Award - PG Student Submission</span></td>
							<td align="right"></td>
							<td align="right"></td>
						</tr>
						<tr style="background-color:#E5E5E5;">
							<td align="left"><span style="font-size:13px;">Award - Technologist Submission</span></td>
							<td align="right"></td>
							<td align="right"></td>
						</tr>
						<tr>
							<td align="left"><span style="font-size:13px;">Award - J. G. Jolly Submission</span></td>
							<td align="right"></td>
							<td align="right"></td>
						</tr>
					</table>-->
					
					<!--<div style="border-bottom:1px solid #D5D5D5; width:100%;"></div>-->
					
					<div style="background-color:#D82133; color:#FFFFFF; padding:10px; text-align:left; margin-bottom:7px;">
						<div style="font:Arial, Helvetica, sans-serif; font-size:20px; margin-bottom:7px;">Abstract Free Paper</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Submission: 10</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Reviewed: 5</div>
					</div>
					
					<div style="background-color:#000000; color:#FFFFFF; padding:10px; text-align:left; margin-bottom:7px;">
						<div style="font:Arial, Helvetica, sans-serif; font-size:20px; margin-bottom:7px;">Abstract - Video Presentation</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Submission: 10</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Reviewed: 5</div>
					</div>
					
					<div style="background-color:#9E9E9E; color:#FFFFFF; padding:10px; text-align:left; margin-bottom:7px;">
						<div style="font:Arial, Helvetica, sans-serif; font-size:20px; margin-bottom:7px;">Award - PG Student</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Submission: 10</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Reviewed: 5</div>
					</div>
					
					<div style="background-color:#704942; color:#FFFFFF; padding:10px; text-align:left; margin-bottom:7px;">
						<div style="font:Arial, Helvetica, sans-serif; font-size:20px; margin-bottom:7px;">Award - Technologist</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Submission: 10</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Reviewed: 5</div>
					</div>
					
					<div style="background-color:#DEB887; color:#000000; padding:10px; text-align:left;">
						<div style="font:Arial, Helvetica, sans-serif; font-size:20px; margin-bottom:7px;">Award - J. G. Jolly</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Submission: 10</div>
						<div style="font:Arial, Helvetica, sans-serif; font-size:12px;">Total Reviewed: 5</div>
					</div>
					
				</td>
			</tr>
		</table>
	</div>
<?php
	page_footer();
?>