<?php
	include_once('includes/init.php');
	
	page_header("Registration Combo Classification.");
	
	$pageKey                       		       = "_pgn_";
	$pageKeyVal                    		       = ($_REQUEST[$pageKey]=="")?0:$_REQUEST[$pageKey];
	
	@$searchString                 		       = "";
	$searchArray                   		       = array();
	
	$searchArray[$pageKey]         		       = $pageKeyVal;
	$searchArray['src_tariff_classification']  = trim($_REQUEST['src_tariff_classification']);
	
	foreach($searchArray as $searchKey=>$searchVal)
	{
		if($searchVal!="")
		{
			$searchString .= "&".$searchKey."=".$searchVal;
		}
	}
?>
	<div class="container">
		<?php 
		switch($show){
			case 'add':	
					addTariffClassification($cfg, $mycms);
					break;
			// TARIFF CLASSIFICATION EDIT FORM LAYOUT
				case 'edit':
				  editWorkshop($cfg, $mycms);
					break;	
														
			// TARIFF CLASSIFICATION LISTING LAYOUT
			default:
				
				tariffClassificationListingLayout($cfg, $mycms); 
				break;				
		
		}
		?>
	</div>
<?php

	page_footer();

	/**********************************************************************/
	/*                TARIFF CLASSIFICATION LISTING LAYOUT                */
	/**********************************************************************/
	function tariffClassificationListingLayout($cfg, $mycms)
	{
	?>
		<form name="frmSearch" id="frmSearch" action="registration_tariff.process.php" method="post">
			<input type="hidden" name="act" value="search_classification" />
			<table width="100%" class="tborder">	
				<tr>
					<td class="tcat" colspan="2" align="left">
						<span style="float:left">Manage Combo</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						<? 
						$counter                 = 0;
						$searchCondition         = "";
						
						if($_REQUEST['src_tariff_classification']!="")
						{
							$searchCondition    .= " AND tariffClassification.classification_title LIKE '%".$_REQUEST['src_tariff_classification']."%'";
						}
						
						$displaydata 	= array();
						$titleheaeder 	= array();											
						
						
												
						//echo'<pre>';print_r($titleheaeder);echo'</pre>';
														
						?>
						<table width="100%">
							<tr class="theader">
								<td width="10">Sl No.</td>
								<td  width="90" align="center">Classification Title</td>
								<td  width="90" align="center">Cut Off Title</td>
								<td  width="90" align="center">Created Date</td>
								<td  width="70" align="center">Status</td>
								<td width="70" align="center">Action</td>
							</tr>
					<tr class="theader">
					
					
					</tr>		
					<?php	
						$sql_cal			=	array();
						$sql_cal['QUERY']	=	"SELECT * 
													FROM "._DB_REGISTRATION_COMBO_CLASSIFICATION_."
													WHERE `status` = ?
													ORDER BY `sequence_by` ASC";
														
						$sql_cal['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'A' , 		'TYP' => 's');
								
						$res_cal=$mycms->sql_select($sql_cal);

						$i=1;
						
						foreach($res_cal as $key=>$rowsl)
						{
							$sqlCutoff['QUERY'] = array();
							$sqlCutoff['QUERY']    = " SELECT * 
									 FROM "._DB_TARIFF_CUTOFF_." 
									WHERE `status`= 'A' 
									AND `id`='".$rowsl['cutoff_id']."'
								 ORDER BY `cutoff_sequence` ASC";		
								//$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');
								//$sqlCutoff['PARAM'][]  = array('FILD' => 'id',  'DATA' =>$rowsl['cutoff_id'],  'TYP' => 's');
								$resCutoff = $mycms->sql_select($sqlCutoff);
							//echo '<pre>'; print_r($resCutoff[0]['cutoff_title']);	
					?>
						<tr class="tlisting" style=" <?=($rowsl['display']=='N')?'background:bisque;':''?>">
							<td align="center"><?=$i?></td>
							<td align="left"><?=$rowsl['classification_title']?> </td>
							<td align="center"><?=$resCutoff[0]['cutoff_title']?></td>
						    <td align="center"><?=displayDateFormat($rowsl['created_dateTime'])?></td>
								
							<td align="center">
								
									<a href="<?=$cfg['SECTION_BASE_URL']?>manage_reg_process.php?act=<?=($rowsl['status']=='A')?'Inactive':'Active'?>&id=<?=$rowsl['id'];?>&cutoff_id=<?=$rowsl['cutoff_id'];?><?=$searchString?>" 
									 class="<?=($rowsl['status']=='A')?'ticket ticket-success':'ticket ticket-important'?>"><?=($rowsl['status']=='A')?'Active':'Inactive'?></a>
								
							</td>
							<td align="center">

									<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="manage_reg_combo_classification.php?show=edit&id=<?=$rowsl['id']?>&cutoff_id=<?=$rowsl['cutoff_id'];?>">
									<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
							</td>
						</tr>
						<?
						$i++;		
						}
						?>	
						</table>						
					</td>
				</tr>
				<tr class="tfooter">
				<td align="right" colspan="2">
						<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?=$_SERVER['PHP_SELF']?>?show=add<?=$searchString?>">+Add More Classification</a><br>
						<span class="paginationRecDisplay"><?=$mycms->paginateRecInfo(1)?></span>
						<span class="paginationDisplay"><?=$mycms->paginate(1,'pagination')?></span>
				</td>	
				</tr>	
			</table>
		</form>
	<?php
	}


	/**************************************************************/
	/*                       ADD TARIFF FORM                     */
	/**************************************************************/	

	function addTariffClassification($cfg, $mycms)
	{
		global $searchArray, $searchString;

		$sql_hotel	=	array();
		$sql_hotel['QUERY']	=	"SELECT * 
									FROM "._DB_MASTER_HOTEL_."
									WHERE `status` 	= 		?
									ORDER BY `id` ASC";
										
		$sql_hotel['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'A' , 		'TYP' => 's');
				
		$res_hotel=$mycms->sql_select($sql_hotel);
		
		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = " SELECT * 
									 FROM "._DB_TARIFF_CUTOFF_." 
									WHERE `status` != ? 
								 ORDER BY `cutoff_sequence` ASC";		
			$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'D',  'TYP' => 's');
			$resCutoff = $mycms->sql_select($sqlCutoff);		
			if($resCutoff)
			{
				foreach($resCutoff as $i=>$rowCutoff) 
				{
					$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
				}
			}
	
	?>	
	

		
		<form name="frmtypeadd" method="post" action="<?=$cfg['SECTION_BASE_URL']?>manage_reg_combo_classification.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="add" />
			<table width="50%" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Add Registration Combo Classification</td> 
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" align="center" style="margin:0px; padding:0px;">
						
							<table width="100%">
								<tr class="thighlight">
									<td colspan="2" align="left">Regsitration Classification</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Classification Title <span class="mandatory">*</span>
									</td>
									<td width="80%" align="left">
										<input type="text" name="classification_title" id="classification_title" 
										 class="validate[required]" onblur="checkClassificationTitle(this)" style="width:80%;" required/>
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Cutoff <span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										 	<select name="cutoff_id" style="width:84%;" required="">
										 		<option value="">Choose cutoff</option>
										 		<?php
										 		
										 		foreach ($cutoffArray as $key => $value) {
										 		 	?>
										 				<option value="<?php echo $key ?>"><?php echo $value; ?></option>
										 		 	<?php
										 		 } 
										 		?>
										 	
										 	
										</select> 
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Hotel <span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										 	<select name="type" id="hotel_id" style="width:84%;" required="">
										 		<option value="">Choose hotel</option>
										 		<?php
										 		
										 		foreach ($res_hotel as $key => $value) {
										 		 	?>
										 		 	<option value="<?php echo $value['id'] ?>"><?php echo $value['hotel_name'] ?></option>
										 		 	<?php
										 		 } 
										 		?>
										 	
										 	
										</select> 
									</td>
								</tr>
								<tr>
									<td align="left">
										Registration Price <span class="mandatory"></span>
									</td>
									<td align="left">
										<input type="text" name="registration_price" id="registration_price" 
										 class="validate[required]" onkeypress='return isNumber(event)' style="width:80%;"/>
									</td>
								</tr>
								<tr>
									<td align="left">
										Workshop Price <span class="mandatory"></span>
									</td>
									<td align="left">
										<input type="text" name="workshop_price" id="workshop_price" 
										 class="validate[required]" onkeypress='return isNumber(event)' style="width:80%;" />
									</td>
								</tr>
								<tr>
									<td align="left">
										Dinner Price <span class="mandatory"></span>
									</td>
									<td align="left">
										<input type="text" name="dinner_price" id="dinner_price" 
										 class="validate[required]"  style="width:80%;" onkeypress='return isNumber(event)' />
									</td>
								</tr>
								<tr>
									<td align="left">
										Accommodation Price <span class="mandatory"></span>
									</td>
									<td align="left">
										<input type="text" name="accommodation_price" id="accommodation_price" 
										 class="validate[required]"  style="width:59%;" onkeypress='return isNumber(event)'  />

										 <input type="button" name="compose" style="width:20%;" id="compose" class="btn btn-small btn-red" value="Compose"></td>
									</td>
								</tr>
								<tr>
									<td align="left">
										No. Of Night <span class="mandatory"></span>
									</td>
									<td align="left">
										<select name="no_of_night" id="no_of_night" style="width:84%;" required onchange="getAccomodationRoundPrice(this.value)">
											<option value="" selected="">Select</option>
										<?php
										 for($i=1;$i<=10;$i++)
										 {
										?>
										  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php
										}
										?>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left">
										Seat limit <span class="mandatory">*</span>
									</td>
									<td align="left">
										<input type="text" name="seat_limit_add" id="seat_limit_add" 
										 class="validate[required]" value="" style="width:80%;" required/>
									</td>
								</tr>
								<tr>
									<td align="left">
										Sequence By
									</td>
									<td align="left">
										<input type="number" name="sequence_by" id="sequence_by" value="<?=$row['sequence_by']?>" style="width:80%;" rel="splDate" required/>
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Currency<span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										 	<select name="currency" style="width:84%;" required="">
										 	<option value="INR">INR</option>
										 	<option value="USD">USD</option>
										</select> 
									</td>
								</tr>

								<tr>
									<td width="40%" align="left">
										Round Of price<span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										 
										<input type="text" name="round_of_price" id="round_of_price" style="width:84%;" required="" >
									</td>
								</tr>

								<tr>
									<td width="40%" align="left">
										Total Price: <span class="" id="totalgrand">0.00</span>
										<input type="hidden" name="total_price" id="total_price">
									</td>
									
								</tr>

								<tr>
									<td width="40%" align="left">
										Total Round Of Price: <span class="" id="totalRoundPrice">0.00</span>
										<input type="hidden" name="total_round_price" id="total_round_price">
									</td>
									
								</tr>


							</table>
						
						</td>
					</tr>
					<tr>	
						<table use="rateDes" id="rateDes" style="width: 50%; border: 1px solid rgb(0, 0, 0); margin: 10px; text-align: center; border-collapse: collapse; display: none;">
							<tr>
								<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check In Date</th>
								<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check Out Date</th>
								<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">INR Rate</th>
								

							</tr>
							<tr use='rateVal'>
								<td style=' border: 1px solid rgb(0, 0, 0);'></td>
								<td style=' border: 1px solid rgb(0, 0, 0);'></td>
								<td style=' border: 1px solid rgb(0, 0, 0);'></td>
								
						</tr>
					</table>
					</tr>		
					<tr>
						<td width="40%"></td>
						<td align="left">
							<a href="<?=$_SERVER['PHP_SELF']?>?pageno=<?=$_REQUEST['pageno']?>">
							<input type="button" name="BackAdd" id="BackAdd" value="Back" class="btn btn-small btn-red" /></a>
							&nbsp;
							<input type="submit" name="Save2" id="Save2" value="Save" class="btn btn-small btn-blue" />
							<span class="gen-error" style="color: red; display: block;"></span>
						</td>
					</tr>
					<tr class="tfooter">
						<td colspan="2">&nbsp;</td>
					</tr> 
				</tbody> 
			</table> 
		</form>
		<script type="text/javascript">
			function checkClassificationTitle(argument) {
				var Title = $.trim($(argument).val());
				if(Title == "")
				{
					return false;
				}
				else
				{
					$.ajax({
						url: "<?=$cfg['SECTION_BASE_URL']?>manage_reg_combo_classification.process.php", 
						data:{act:'checktitle',title:Title},
						success: function(result){
							
					    	if($.trim(result) === 'error')
					    	{
					    		$('.gen-error').text('Title already exist. Please try with another title.')
					    		$('input[type=submit]').hide()
					    	}
					    	else
					    	{
					    		$('.gen-error').text('')
					    		$('input[type=submit]').show()
					    	}

					    	console.log(result)
					  	},
					  	error(xhr,status,error){
					  		console.log('error==>'+error)
					  	}
					});
				}
				
			}

		function isNumber(evt)
		{
	        evt = (evt) ? evt : window.event;
	        var charCode = (evt.which) ? evt.which : evt.keyCode;
	        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
	        return false;
	        }
	        return true;
		}


		
		$("#compose").click(function(){
		  //alert(21);
		  var INRrate = $('#accommodation_price').val();
		  var hotel_id = $('#hotel_id').val();
		  //alert(hotel_id);
		  var flag=0;
		  if(hotel_id=='')
		  {
		  	alert("Please select hotel");
		  	flag=1;
		  }
		  else if(INRrate=='')
		  {
		  	alert("Please enter accomodation price");
		  	flag=1;
		  }
		  if(flag==0)
		  {


		  			$('#rateDes').show();
				  	$.ajax({
							type: "POST",
							url: "manage_reg_combo_classification.process.php",
							data: 'act=compose_entry&hotel_id='+hotel_id,
							dataType: "json",
							async: false,
							success: function(JSONObject){
								if(JSONObject.length>0)
								{
									var rateTable =  $("table[use=rateDes]");

									$(rateTable).find("tr[use=rateVal]").remove();
									$.each(JSONObject, function (key, value) {
										console.log(value.DAYDIFF); 
										var INRAmount = parseFloat(INRrate)*parseInt(value.DAYDIFF); 
							   // var USDAmount = parseFloat(USDrate)*parseInt(value.days); 
									    var trString = "<tr use='rateVal'>";
									    trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
									    trString += value.CHECKIN;
									    trString += '<input type="hidden" name="checkin_date['+key+']" value="'+value.CHECKINID+'" />';
									    trString += "</td>";
									    trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
									    trString += value.CHECKOUT;
									    trString += '<input type="hidden" name="checkout_date['+key+']" value="'+value.CHECKOUTID+'" />';
									    trString += "</td>";
									    trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
									    trString += 'INR '+(INRAmount).toFixed(2);
									    trString += '<input type="hidden" name="INRAmt['+key+']" value="'+INRAmount+'" />';
									    trString += "</td>";
									   // trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
									  
									    trString += "</td>";
									    trString += "</tr>";

									    $(rateTable).append(trString);	
									});	
								}
								//console.log(JSONObject);
								/*$.each(JSONObject, function (key, value) {
									console.log(value.checkin); 
								});*/
								
							}
					  });
			  	 
		   } 
		  
		});	

		function getAccomodationRoundPrice(val)
		{
			if(val!='')
			{
				var total = 0;
				var registration_price = $('#registration_price').val();
				var workshop_price = $('#workshop_price').val();
				var dinner_price = $('#dinner_price').val();
				var accommodation_price = $('#accommodation_price').val();

				var total_accomodation = (parseFloat(accommodation_price) * val);

				if(registration_price!='' && workshop_price!='' && dinner_price!='' && accommodation_price!='')
				{
					total+= parseFloat(registration_price) + parseFloat(workshop_price) + parseFloat(dinner_price) + parseFloat(total_accomodation);
				}

				//alert(total);
				if(isNaN(total))
				{
					
					$('#totalgrand').text('0.00');
				}
				else
				{
					$('#totalgrand').text(total);
					
					$('#total_price').val(total);
				}
				
			}
		}




		
		$('#round_of_price').on('input',function() {
			var total_price = parseFloat($('#total_price').val());
			var round_of_price = parseFloat($('#round_of_price').val());

			if(total_price!='' && round_of_price!='')
			{

			 $('#totalRoundPrice').text((total_price + round_of_price ? total_price + round_of_price : 0).toFixed(2));
			  $('#total_round_price').val((total_price + round_of_price ? total_price + round_of_price : 0).toFixed(2));
			 
			}

		});	
		

		</script>
		<?php
	}
	
	/**************************************************************/
	/*                       EDIT REGISTRATION CLASSIFICATION                     */
	/**************************************************************/
	function editWorkshop($cfg, $mycms)
	{
		global $searchArray, $searchString;

		$sql_hotel	=	array();
		$sql_hotel['QUERY']	=	"SELECT * 
									FROM "._DB_MASTER_HOTEL_."
									WHERE `status` 	= 		?
									ORDER BY `id` ASC";
										
		$sql_hotel['PARAM'][]	=	array('FILD' => 'status' , 		'DATA' => 'A' , 		'TYP' => 's');
				
		$res_hotel=$mycms->sql_select($sql_hotel);
	?>
		<form name="frmtypeadd" method="post" action="<?=$cfg['SECTION_BASE_URL']?>manage_reg_combo_classification.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="update" />
			<input type="hidden" name="id" id="id" value="<?=$_REQUEST['id']?>" />
			<?php
			$sql 	=	array();
			$sql['QUERY']	=	"SELECT * 
								   FROM "._DB_REGISTRATION_COMBO_CLASSIFICATION_." 
								  WHERE `id` = ? ";
			$sql['PARAM'][]		=	array('FILD' => 'id' , 		  'DATA' => $_REQUEST['id'] ,				   'TYP' => 's');
				$res_cal=$mycms->sql_select($sql);
				$row    = $res_cal[0];
				//print_r($row);
				//echo $row['residential_hotel_id'];
			$cutoffArray  = array();
			$sqlCutoff['QUERY']    = " SELECT * 
									 FROM "._DB_TARIFF_CUTOFF_." 
									WHERE `status` != ? 
								 ORDER BY `cutoff_sequence` ASC";		
			$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'D',  'TYP' => 's');
			$resCutoff = $mycms->sql_select($sqlCutoff);		
			if($resCutoff)
			{
				foreach($resCutoff as $i=>$rowCutoff) 
				{
					$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
				}
			}	

			$dates = array();	
			$dCount = 0;		
			$packageCheckDate = array();	
			$packageCheckDate['QUERY'] = "SELECT * FROM "._DB_COMBO_CHECKIN_DATE_." 
										   WHERE `hotel_id` = ?
											 AND `status` = ?
									    ORDER BY  check_in_date";
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $row['residential_hotel_id'] , 	'TYP' => 's');
			$packageCheckDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 		'TYP' => 's');									    
			$resCheckIns = $mycms->sql_select($packageCheckDate);

			foreach ($resCheckIns as $key => $rowCheckIn) {
			$packageCheckoutDate = array();
			$packageCheckoutDate['QUERY'] = "SELECT *, TIMESTAMPDIFF(DAY,'".$rowCheckIn['check_in_date']."',`check_out_date`) AS dayDiff
											   FROM "._DB_COMBO_CHECKOUT_DATE_." 
											  WHERE `hotel_id` = ?
											    AND `status` = ?
											    AND `check_out_date` > ?
										   ORDER BY check_out_date";
			$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'hotel_id' , 		'DATA' => $row['residential_hotel_id'] , 	    'TYP' => 's');
			$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'status' , 			'DATA' => 'A' , 			'TYP' => 's');
			$packageCheckoutDate['PARAM'][]	=	array('FILD' => 'check_out_date' ,	'DATA' => $rowCheckIn['check_in_date'] , 			'TYP' => 's');

			//echo '<pre>'; print_r($packageCheckoutDate);
			$resCheckOut = $mycms->sql_select($packageCheckoutDate);
			?> 
			<table width="50%" class="tborder"> 
				<thead> 
					<tr> 
						<td colspan="2" align="left" class="tcat">Edit Combo Classification</td> 
					</tr> 
				</thead> 
				<tbody>
					<tr>
						<td colspan="2" align="center" style="margin:0px; padding:0px;">
						
							<table width="100%">
								<tr class="thighlight">
									<td colspan="2" align="left">Regsitration Classification</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Classification Title <span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										<input type="text" name="classification_title" id="classification_title" 
										 class="validate[required]" value="<?=$row['classification_title']?>" style="width:80%;" required/>
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Cutoff <span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										 	<select name="cutoff_id" style="width:84%;" required="">
										 		<option value="">Choose cutoff</option>
										 		<?php
										 		
										 		foreach ($cutoffArray as $key => $value) {
										 		 	?>
										 				<option value="<?php echo $key ?>"<?php if($row['cutoff_id']==$key){ echo 'selected'; } ?>><?php echo $value; ?></option>
										 		 	<?php
										 		 } 
										 		?>
										 	
										 	
										</select> 
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Hotel <span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										 	<select name="residential_hotel_id" style="width:84%;" required="">
										 		<option value="">Choose hotel</option>
										 		<?php
										 		
										 		foreach ($res_hotel as $key => $value) {
										 		 	?>
										 				<option value="<?php echo $value['id'] ?>"<?php if($row['residential_hotel_id']==$value['id']){ echo 'selected'; } ?>><?php echo $value['hotel_name'] ?></option>
										 		 	<?php
										 		 } 
										 		?>
										 	
										 	
										</select> 
									</td>
								</tr>

								<tr>
									<td align="left">
										Registration Price <span class="mandatory"></span>
									</td>
									<td align="left">
										<input type="text" name="registration_price" id="registration_price" 
										 class="validate[required]" onkeypress='return isNumber(event)' style="width:80%;" value="<?=$row['registration_price']?>" />
									</td>
								</tr>
								<tr>
									<td align="left">
										Workshop Price <span class="mandatory"></span>
									</td>
									<td align="left">
										<input type="text" name="workshop_price" id="workshop_price" 
										 class="validate[required]" value="<?=$row['workshop_price']?>" onkeypress='return isNumber(event)' style="width:80%;" />
									</td>
								</tr>
								<tr>
									<td align="left">
										Dinner Price <span class="mandatory"></span>
									</td>
									<td align="left">
										<input type="text" name="dinner_price" id="dinner_price" 
										 class="validate[required]" value="<?=$row['dinner_price']?>" style="width:80%;" onkeypress='return isNumber(event)' />
									</td>
								</tr>
								<tr>
									<td align="left">
										Accommodation Price <span class="mandatory"></span>
									</td>
									<td align="left">
										<input type="text" name="accommodation_price" id="accommodation_price" 
										 class="validate[required]" value="<?=$row['accomodation_price']?>" style="width:80%;" onkeypress='return isNumber(event)' />
									</td>
								</tr>
								<tr>
									<td align="left">
										No. Of Night <span class="mandatory"></span>
									</td>
									<td align="left">
										<select name="no_of_night" id="no_of_night" style="width:84%;" required onchange="getAccomodationRoundPrice(this.value)">
											<option value="" selected="">Select</option>
										<?php
										 for($i=1;$i<=10;$i++)
										 {
										?>
										  <option value="<?php echo $i; ?>"<?php if($row['no_of_night']==$i){ echo 'selected'; } ?>><?php echo $i; ?></option>
										<?php
										}
										?>
										</select>
									</td>
								</tr>
								<tr>
									<td align="left">
										Seat limit <span class="mandatory">*</span>
									</td>
									<td align="left">
										<input type="text" name="seat_limit_add" id="seat_limit_add" 
										 class="validate[required]" onblur="countryAvailabilityAdd(this.value)" value="<?=$row['seat_limit']?>" style="width:80%;" required/>
									</td>
								</tr>
								<tr>
									<td align="left">
										Sequence By
									</td>
									<td align="left">
										<input type="number" name="sequence_by" id="sequence_by" value="<?=$row['sequence_by']?>" style="width:80%;" rel="splDate" required/>
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Currency<span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										 	<select name="currency" style="width:84%;" required="">
										 	<option <? if($row['type']=='INR'){  echo "selected"; }?> value="INR">INR</option>
										 	<option <? if($row['type']=='USD'){  echo "selected"; }?> value="USD">USD</option>
										</select> 
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Round Of price<span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										 
										<input type="text" name="round_of_price" id="round_of_price" style="width:84%;" required="" value="<?=$row['round_of_price']?>" >
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Total Price: <span class="" id="totalgrand"><?=$row['total_price']?></span>
										<input type="hidden" name="total_price" id="total_price" value="<?=$row['total_price']?>">
									</td>
									
								</tr>

								<tr>
									<td width="40%" align="left">
										Total Round Of Price: <span class="" id="totalRoundPrice"><?=$row['total_round_price']?></span>
										<input type="hidden" name="total_round_price" id="total_round_price" value="<?=$row['total_round_price']?>">
									</td>
									
								</tr>
							</table>
						
						</td>
					</tr>
					<tr>	
						<table use="rateDes" id="rateDes" style="width: 50%; border: 1px solid rgb(0, 0, 0); margin: 10px; text-align: center; border-collapse: collapse;">
							<tr>
								<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check In Date</th>
								<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check Out Date</th>
								<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">INR Rate</th>
								

							</tr>
							<tr use='rateVal'>
								<td style=' border: 1px solid rgb(0, 0, 0);'></td>
								<td style=' border: 1px solid rgb(0, 0, 0);'></td>
								<td style=' border: 1px solid rgb(0, 0, 0);'></td>
								
						</tr>
					</table>
					</tr>
					<tr>
						<td width="40%"></td>
						<td align="left">
							<a href="<?=$_SERVER['PHP_SELF']?>?pageno=<?=$_REQUEST['pageno']?>">
							<input type="button" name="BackAdd" id="BackAdd" value="Back" class="btn btn-small btn-red" /></a>
							&nbsp;
							<input type="submit" name="Save2" id="Save2" value="Save" class="btn btn-small btn-blue" />
						</td>
					</tr>
					<tr class="tfooter">
						<td colspan="2">&nbsp;</td>
					</tr> 
				</tbody> 
			</table> 
		</form>
		<script type="text/javascript">
		function getAccomodationRoundPrice(val)
		{
			if(val!='')
			{
				var total = 0;
				var registration_price = $('#registration_price').val();
				var workshop_price = $('#workshop_price').val();
				var dinner_price = $('#dinner_price').val();
				var accommodation_price = $('#accommodation_price').val();

				var total_accomodation = (parseFloat(accommodation_price) * val);

				if(registration_price!='' && workshop_price!='' && dinner_price!='' && accommodation_price!='')
				{
					total+= parseFloat(registration_price) + parseFloat(workshop_price) + parseFloat(dinner_price) + parseFloat(total_accomodation);
				}

				//alert(total);
				if(isNaN(total))
				{
					
					$('#totalgrand').text('0.00');
				}
				else
				{
					$('#totalgrand').text(total);
					
					$('#total_price').val(total);
				}
				
			}
		}

		$('#round_of_price').on('input',function() {
			var total_price = parseFloat($('#total_price').val());
			var round_of_price = parseFloat($('#round_of_price').val());

			if(total_price!='' && round_of_price!='')
			{

			 $('#totalRoundPrice').text((total_price + round_of_price ? total_price + round_of_price : 0).toFixed(2));
			  $('#total_round_price').val((total_price + round_of_price ? total_price + round_of_price : 0).toFixed(2));
			 
			}

		});	
	</script>
	<?php
	}
	?>
