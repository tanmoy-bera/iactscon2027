<?php
	include_once('includes/init.php');
	
	page_header("Fastest Finger Leaderboard");
?>
	<script language="javascript" src="<?=_BASE_URL_?>JS/jquery.js"></script>
	<div class="container">
		<?php 
			switch($show){
				// show add window
				// case 'add':	
				// 	addQuiz($cfg, $mycms);
				// 	break;	
				// // show edit window	
				// case 'edit':
				// 	editQuiz($cfg, $mycms);
				// 	break;	
				// // view details record
				// case'view':
		        //     viewQuiz($cfg, $mycms);
		        //     break;						
				// show all record	
				default:	
					showAllQuiz($cfg, $mycms); 
					break;				
			} 
		?>
	</div>
<?php
	page_footer();
	
	/*============== SHOW ALL COUNTRY WINDOW =============*/
	function showAllQuiz($cfg, $mycms)
	{     
		 global $searchArray, $searchString;
	?>
		<form name="frmCountryListing" id="frmCountryListing" action="<?=$cfg['SECTION_BASE_URL']?>manage_country.process.php" method="post">
			<input type="hidden" name="act" id="act" value="search_country" />
			<input type="hidden" id="excelPrintVal" name="excelPrintVal"/>
			<table width="100%" align="center" class="tborder">
				<tr>
					<td colspan="2" align="left" class="tcat">
						<span style="float:left">Leaderboard</span>
						<span class="tsearchTool" forType="tsearchTool"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
							
						<!-- <div class="tsearch">
							<table width="100%">
								<tr>
									<td align="right">
										Country Name:
										<input type="text" name="src_country_name" id="src_country_name" 
										 value="<?=$_REQUEST['src_country_name']?>" />                        
										&nbsp;
										<?php
											searchStatus();
										?>
										<input type="submit" name="searchBtn" id="searchBtn" value="Search" class="btn btn-small btn-blue" />
									</td>
								</tr>
							</table>
						</div> -->
						
						<table width="100%">
							<tr class="theader">
								<td width="50" align="center">Rank</td>
								<td align="left">Name</td>
								<td align="left">Time Taken (in sec.)</td>
							</tr>
							<tr>
                                <td>1</td>
                                <td>Akshita</td>
                                <td>1.29</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>John Doe</td>
                                <td>1.54</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Virat</td>
                                <td>2.31</td>
                            </tr>
                            <!-- <tr>
                                <td colspan="3" align="center">
                                    <span class="mandatory">No Record.</span>
                                </td>
                            </tr>   -->
						</table>
					
					</td>
				</tr>
			</table> 
		</form>
	<?php
	}
?>