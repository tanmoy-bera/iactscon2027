<?php
	include_once('includes/init.php');
	
	page_header("Quiz Applicants");
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
						<span style="float:left">Applicant List</span>
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
								<td width="50" align="center">SL No</td>
								<td align="left">Name</td>
								<td align="center">Applied On</td>
								<td align="center">Status</td>
								<td align="center" width="150">Action</td>
							</tr>
							<tr>
                                <td align="center">
                                    <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing" 
                                        value="" />
                                </td>
                                <td>1</td>
                                <td>Akshita</td>
                                <td align="center"><?= date('d-m-Y H:i:s', strtotime('2025-08-10 18:32:22')) ?></td>
                                <td align="center">Pending</td>
                                <td align="center">
                                    <button type="button">Accept</button>
                                    
                                    <button type="button">Reject</button>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing" 
                                        value="" />
                                </td>
                                <td>2</td>
                                <td>Group B</td>
                                <td align="center"><?= date('d-m-Y H:i:s', strtotime('2025-08-09 13:52:11')) ?></td>
                                <td align="center">Pending</td>
                                <td align="center">
                                    <button type="button">Accept</button>
                                    
                                    <button type="button">Reject</button>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing" 
                                        value="" />
                                </td>
                                <td>3</td>
                                <td>Virat</td>
                                <td align="center"><?= date('d-m-Y H:i:s', strtotime('2025-08-09 11:03:45')) ?></td>
                                <td align="center">Pending</td>
                                <td align="center">
                                    <button type="button">Accept</button>
                                    
                                    <button type="button">Reject</button>
                                </td>
                            </tr>
                            <!-- <tr>
                                <td colspan="3" align="center">
                                    <span class="mandatory">No Record.</span>
                                </td>
                            </tr>   -->

						</table>
					
					</td>
				</tr>
                 <tr>
                    <td width="100%" colspan="4" align="left">
                        <select name="multiOperationSelector" id="multiOperationSelector">
                            <option value="">Choose an action...</option>
                            <option value="Accept">Accept</option>
                            <option value="Reject">Reject</option>							
                        </select>
                        <!-- <input value="Apply to selected"  name="submit" type="button" 
                        onclick="return multiTaskValidation('<?=$cfg['SECTION_BASE_URL']?>manage_country.process.php','<?=$searchString?>');" class="btn btn-small btn-blue"/>  -->
                    </td>
                </tr>
                <tr class="tfooter">
                    <td colspan="4">
                        <span class="paginationRecDisplay"><?=$mycms->paginateRecInfo($indexVal)?></span>
                        <span class="paginationDisplay"><?=$mycms->paginate($indexVal,'pagination')?></span>
                    </td>
                </tr>
			</table> 
		</form>
	<?php
	}
?>