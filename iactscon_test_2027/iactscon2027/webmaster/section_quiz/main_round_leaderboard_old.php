<?php
	include_once('includes/init.php');
	
	page_header("Main Round Leaderboard");
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
						<span style="float:left">Round 1 Level 1</span>
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
								<td width="30" align="center">
									<input name="check_all" id="check_all" targetElement="checkBoxListing" 
									 type="checkbox" onclick="checkall(this);"/>
								</td>
								<td width="100" align="center">Rank</td>
								<td align="left">Name</td>
								<td width="100" align="center">Total Questions</td>
								<td width="100" align="center">Correct</td>
								<td width="100" align="center">Incorrect</td>
								<td width="150" align="center">Total Time Taken</td>
							</tr>
							<tr>
								<td align="center">
                                    <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing" 
                                        value="" />
                                </td>
                                <td align="center">1</td>
                                <td><a href="https://ruedakolkata.com/ccicon_2025/webmaster/section_quiz/player_details.php?player=John Doe">John Doe</a></td>
                                <td width="100" align="center">5</td>
                                <td width="100" align="center" >4</td>
                                <td width="100" align="center" >1</td>
                                <td width="150" align="center">0.50 min.</td>
                            </tr>
							<tr>
								<td align="center">
                                    <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing" 
                                        value="" />
                                </td>
                                <td align="center">2</td>
                                <td><a href="https://ruedakolkata.com/ccicon_2025/webmaster/section_quiz/player_details.php?player=Virat">Virat</a></td>
                                <td width="100" align="center">5</td>
                                <td width="100"  align="center">3</td>
                                <td width="100"  align="center">2</td>
                                <td width="150" align="center">1.10 min.</td>
                            </tr>
							<tr>
								<td align="center">
                                    <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing" 
                                        value="" />
                                </td>
                                <td align="center">3</td>
                                <td><a href="https://ruedakolkata.com/ccicon_2025/webmaster/section_quiz/player_details.php?player=Akshita">Akshita</a></td>
                                <td width="100" align="center">5</td>
                                <td width="100"  align="center">3</td>
                                <td width="100"  align="center">2</td>
                                <td width="150" align="center">0.30 sec.</td>
                            </tr>
                            <!-- <tr>
                                <td colspan="5" align="center">
                                    <span class="mandatory">No Record.</span>
                                </td>
                            </tr>   -->
						</table>
					
					</td>
				</tr>
				<tr>
					<td width="100%" colspan="2" align="left">
						<select name="multiOperationSelector" id="multiOperationSelector">
							<option value="">Choose an action...</option>
							<option value="Next Round">Promote To Next Round</option>							
						</select>
						<input value="Apply to selected"  name="submit" type="button" 
						  class="btn btn-small btn-blue"/>
					</td>
				</tr>
			</table> 
		</form>
	<?php
	}
?>