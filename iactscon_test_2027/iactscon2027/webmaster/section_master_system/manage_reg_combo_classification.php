<?php
include_once('includes/init.php');

page_header("Registration Combo Classification.");

$pageKey                       		       = "_pgn_";
$pageKeyVal                    		       = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString                 		       = "";
$searchArray                   		       = array();

$searchArray[$pageKey]         		       = $pageKeyVal;
$searchArray['src_tariff_classification']  = trim($_REQUEST['src_tariff_classification']);

foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}
?>
<div class="body_wrap">
	<div class="body_content_box">
		<?php
		switch ($show) {
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
</div>
<?php

page_footer();

/**********************************************************************/
/*                TARIFF CLASSIFICATION LISTING LAYOUT                */
/**********************************************************************/
function tariffClassificationListingLayout($cfg, $mycms)
{
?>
	<form class="con_box-grd row" name="frmSearch" id="frmSearch" action="registration_tariff.process.php" method="post">
		<input type="hidden" name="act" value="search_classification" />
		<div class="form-group">
			<h2>Manage Combo</h2>
		</div>
		<div class="table_wrap">

			<?
			$counter                 = 0;
			$searchCondition         = "";

			if ($_REQUEST['src_tariff_classification'] != "") {
				$searchCondition    .= " AND tariffClassification.classification_title LIKE '%" . $_REQUEST['src_tariff_classification'] . "%'";
			}

			$displaydata 	= array();
			$titleheaeder 	= array();



			//echo'<pre>';print_r($titleheaeder);echo'</pre>';

			?>
			<table width="100%">
				<thead>
					<tr class="theader">
						<th class="sl">Sl No</th>
						<th width="90">Package Name</th>
						<th width="90">Hotel Name</th>
						<th width="70">Room Type</th>
						<th width="90" align="center">Cut Off Title</th>
						<th width="50" align="center">No. of Night</th>
						<th width="50" align="center">Seat Limit</th>
						<th width="60" align="center">Created Date</th>
						<th class="action">Status</th>
						<th class="action">Action</th>
					</tr>
				</thead>
				<tbody>
					
					<?php
					$sql_cal			=	array();
					$sql_cal['QUERY']	=	"SELECT * 
													FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . "
													WHERE `status` != ?
													ORDER BY `id` DESC";

					$sql_cal['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');

					$res_cal = $mycms->sql_select($sql_cal);

					$i = 1;
					if ($res_cal) {
						foreach ($res_cal as $key => $rowsl) {
							$sqlCutoff['QUERY'] = array();
							$sqlCutoff['QUERY']    = " SELECT * 
									 FROM " . _DB_TARIFF_CUTOFF_ . " 
									WHERE `status`= 'A' 
									AND `id`='" . $rowsl['cutoff_id'] . "'
								 ORDER BY `cutoff_sequence` ASC";
							//$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');
							//$sqlCutoff['PARAM'][]  = array('FILD' => 'id',  'DATA' =>$rowsl['cutoff_id'],  'TYP' => 's');
							$resCutoff = $mycms->sql_select($sqlCutoff);
							//echo '<pre>'; print_r($sqlCutoff);	

							$sql_hotel	=	array();
							$sql_hotel['QUERY']	=	"SELECT * 
									FROM " . _DB_MASTER_HOTEL_ . "
									WHERE `id`= ? AND `status` 	= 		?
									ORDER BY `id` ASC";

							$sql_hotel['PARAM'][]	=	array('FILD' => 'id', 		'DATA' => $rowsl['residential_hotel_id'], 		'TYP' => 's');
							$sql_hotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');

							$res_hotel = $mycms->sql_select($sql_hotel);
							$hotelName = $res_hotel[0]['hotel_name'];
					?>
							<tr class="tlisting" style=" <?= ($rowsl['display'] == 'N') ? 'background:bisque;' : '' ?>">
								<td class="sl"><?= $i ?></td>
								<td align="left"><?= $rowsl['classification_title'] ?> </td>
								<td align="left"><?= $hotelName ?></td>
								<td align="left"><?= $rowsl['room_type'] ?></td>

								<td align="center"><?= $resCutoff[0]['cutoff_title'] ?></td>
								<td align="center"><?= $rowsl['no_of_night'] ?></td>
								<td align="center"><?= $rowsl['seat_limit'] ?></td>
								<td align="center"><?= displayDateFormat($rowsl['created_dateTime']) ?></td>

								<td class="action">

									<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_combo_classification.process.php?act=<?= ($rowsl['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $rowsl['id']; ?>&cutoff_id=<?= $rowsl['cutoff_id']; ?><?= $searchString ?>" class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>

								</td>
								<td class="action">

									<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="manage_reg_combo_classification.php?show=edit&id=<?= $rowsl['id'] ?>&cutoff_id=<?= $rowsl['cutoff_id']; ?>">
										<span alt="Edit" title="Edit Record" class="icon-pen" /></a>

									<a href="manage_reg_combo_classification.process.php?act=Remove&id=<?= $rowsl['id'] ?>&cutoff_id=<?= $rowsl['cutoff_id']; ?>">
										<span alt="Remove" title="Remove Combo" class="icon-x-alt" onclick="return confirm('Do You Really Want To Remove The Combo ?');" /></a>
								</td>
							</tr>
						<?
							$i++;
						}
					} else {
						?>
						<tr>
							<td colspan="10" align="center" class="mandatory">No Record Present.</td>
						</tr>

					<?php } ?>
				</tbody>
			</table>
			<div class="bbp-pagination">
				<div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
				<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
			</div>
			<a title="add" class="stick_add" href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>"><i class="fas fa-plus"></i></a>
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
									FROM " . _DB_MASTER_HOTEL_ . "
									WHERE `status` 	= 		?
									ORDER BY `id` ASC";

	$sql_hotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');

	$res_hotel = $mycms->sql_select($sql_hotel);

	$cutoffArray  = array();
	$sqlCutoff['QUERY']    = " SELECT * 
									 FROM " . _DB_TARIFF_CUTOFF_ . " 
									WHERE `status` = ? 
								 ORDER BY `cutoff_sequence` ASC";
	$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
	$resCutoff = $mycms->sql_select($sqlCutoff);
	if ($resCutoff) {
		foreach ($resCutoff as $i => $rowCutoff) {
			$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
		}
	}

	$sqlRegClasf['QUERY']	  = "SELECT `classification_title`,`id` 
							  	   FROM " . _DB_REGISTRATION_CLASSIFICATION_ . "  
							 	  WHERE `status` = 'A' ";
	$resRegClasf		 = $mycms->sql_select($sqlRegClasf);

	$sql_workshop_classification	=	array();
	$sql_workshop_classification['QUERY']	=	"SELECT classification_title, id 
									FROM " . _DB_WORKSHOP_CLASSIFICATION_ . "
									WHERE `status` 	= 		?
									ORDER BY `id` ASC";

	$sql_workshop_classification['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');

	$res_workshop_classification = $mycms->sql_select($sql_workshop_classification);


?>


	<script language="javascript" src="<?= $cfg['SECTION_BASE_URL'] ?>scripts/select2.min.js"></script>
	<link rel="stylesheet" href="<?= $cfg['SECTION_BASE_URL'] ?>css/select2.min.css">
	<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_combo_classification.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="add" />
		<div class="form-group">
			<h2>Add Registration Combo Classification</h2>
		</div>

		<!-- <div class="form-group">

			<h2 colspan="2" align="left">Regsitration Classification</h2>
		</div> -->
		<div class="form-group">
			<label class="frm-head">Package Name <span class="mandatory">*</span></label>
			<input type="text" name="classification_title" id="classification_title" class="validate[required]" onblur="checkClassificationTitle(this)" style="width:80%;" required />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Cutoff <span class="mandatory">*</span>
			</label>

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
		</div>
		<div class="form-group">
			<label class="frm-head">
				Registration Classification <span class="mandatory">*</span>
			</label>

			<select name="registration_classification_id" style="width:84%;" required="">
				<option value="">Choose classification</option>
				<?php

				foreach ($resRegClasf as $key => $value) {
				?>
					<option value="<?php echo $value['id'] ?>"><?php echo $value['classification_title']; ?></option>
				<?php
				}
				?>


			</select>
		</div>
		<div class="form-group">
			<label class="frm-head">
				Hotel <span class="mandatory">*</span>
			</label>

			<select name="type" id="hotel_id" style="width:84%;" required="" room_type="room_type" onchange="hotelRoomRetriver(this);">
				<option value="">Choose hotel</option>
				<?php

				foreach ($res_hotel as $key => $value) {
				?>
					<option value="<?php echo $value['id'] ?>"><?php echo $value['hotel_name'] ?></option>
				<?php
				}
				?>


			</select>
		</div>

		<div class="form-group">
			<label class="frm-head">
				Room Type <span class="mandatory"></span>
			</label>

			<div use='roomContainer' class="roomContainer" style="width: 85% !important;">
				<select name="room_type" id="room_type" style="width:100% !important;" forType="room" required>
					<option value="">-- Select Hotel First --</option>
				</select>
			</div>
			<!-- <input type="text" name="room_type" id="room_type" class="validate[required]" style="width:80%;" /> -->

		</div>
		<div class="form-group">
			<label class="frm-head">
				Registration Price <span class="mandatory"></span>
			</label>

			<input type="text" name="registration_price" id="registration_price" class="validate[required]" onkeypress='return isNumber(event)' style="width:80%;" />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Workshop Price <span class="mandatory"></span>
			</label>

			<input type="text" name="workshop_price" id="workshop_price" class="validate[required]" onkeypress='return isNumber(event)' style="width:80%;" />
		</div>

		<div class="form-group">
			<label class="frm-head">
				Workshop Classification <span class="mandatory"></span>
			</label>

			<select name="workshop_classification[]" id="workshop_classification" multiple="multiple" style="width:85%">

				<?php
				if (count($res_workshop_classification) > 0) {

					foreach ($res_workshop_classification as $key => $value) {
				?>
						echo $value;
						<option value="<?php echo $value['id']; ?>"><?php echo $value['classification_title']; ?></option>
				<?php
					}
				}
				?>
			</select>

		</div>
		<div class="form-group">
			<label class="frm-head">
				Dinner Price <span class="mandatory"></span>
			</label>

			<input type="text" name="dinner_price" id="dinner_price" class="validate[required]" style="width:80%;" onkeypress='return isNumber(event)' />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Inclusion Lunch Date
			</label>

			<select name="inclusion_lunch_date[]" id="inclusion_lunch_date" multiple="multiple" style="width:85%">
				<?php
				$sql_cal = array();
				$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Lunch'";
				$res_cal			=	$mycms->sql_select($sql_cal);
				$i = 1;

				foreach ($res_cal as $key => $rowsl) {
				?>
					<option value="<?= $rowsl['date'] ?>"><?= $rowsl['date'] ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="form-group">
			<label class="frm-head">
				Inclusion Conference Dinner Date
			</label>

			<select name="inclusion_dinner_date[]" id="inclusion_dinner_date" multiple="multiple" style="width:85%">
				<?php
				$sql_cal = array();
				$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Dinner'";
				$res_cal			=	$mycms->sql_select($sql_cal);
				$i = 1;

				foreach ($res_cal as $key => $rowsl) {
				?>
					<option value="<?= $rowsl['date'] ?>"><?= $rowsl['date'] ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="form-group">
			<label class="frm-head">
				Entry to Scientific Halls
			</label>
			<ul class="cus-check-wrap">
				<li>
					<p class="sub-check">

						<?php
						?>
						<label class="cus-check">Yes
							<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="Y" checked />
							<span class="checkmark"></span>
						</label>
						<label class="cus-check">No
							<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="N" /><span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>

		</div>
		<div class="form-group">
			<label class="frm-head">
				Entry to Exhibition Area
			</label>

			<ul class="cus-check-wrap">
				<li>
					<p class="sub-check">

						<?php
						?>
						<label class="cus-check">Yes
							<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="Y" checked />
							<span class="checkmark"></span>
						</label>
						<label class="cus-check">No
							<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="N" /><span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>

		</div>
		<div class="form-group">
			<label class="frm-head">
				Tea/Coffee during the Conference
			</label>

			<ul class="cus-check-wrap">
				<li>
					<p class="sub-check">

						<?php
						?>
						<label class="cus-check">Yes
							<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="Y" checked />
							<span class="checkmark"></span>
						</label>
						<label class="cus-check">No
							<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="N" /><span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>

		</div>
		<div class="form-group">
			<label class="frm-head">
				Inclusion Conference Kit <span class="mandatory">*</span>
			</label>
			<ul class="cus-check-wrap">
				<li>
					<p class="sub-check">

						<?php
						?>
						<label class="cus-check">Yes
							<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="Y" checked />
							<span class="checkmark"></span>
						</label>
						<label class="cus-check">No
							<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="N" /><span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>

		</div>
		<div class="form-group">
			<label class="frm-head">
				Accommodation Price (Individual) <span class="mandatory"></span>
			</label>

			<input type="text" name="accommodation_price_individual" id="accommodation_price_individual" class="validate[required]" style="width:80%;" onkeypress='return isNumber(event)' />


		</div>
		<div class="form-group">
			<label class="frm-head">
				Accommodation Price (Shared) <span class="mandatory"></span>
			</label>

			<input type="text" name="accommodation_price_shared" id="accommodation_price_shared" class="validate[required]" style="width:80%;" onkeypress='return isNumber(event)' />


		</div>
		<div class="form-group">
			<label class="frm-head">
				No. Of Night <span class="mandatory"></span>
			</label>

			<input type="number" name="no_of_night" id="no_of_night" style="calc(85% - 135px) !important;" value="<?= $row['no_of_night'] ?>" required>

			<input type="button" name="compose" id="compose" class="back" style="    width: 125px !important;background: #666666;margin-left: 10px;" value="Compose">
		</div>
		<div class="form-group">
			<label class="frm-head">
				Seat limit <span class="mandatory">*</span>
			</label>

			<input type="text" name="seat_limit_add" id="seat_limit_add" class="validate[required]" value="" style="width:80%;" required />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Sequence By
			</label>
			<input type="number" name="sequence_by" id="sequence_by" value="<?= $row['sequence_by'] ?>" style="width:80%;" rel="splDate" required />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Currency<span class="mandatory">*</span>
			</label>
			<select name="currency" style="width:84%;" required="">
				<option value="INR">INR</option>
				<option value="USD">USD</option>
			</select>
		</div>

		<div class="form-group">
			<label class="frm-head">
				Round Of price (Individual)<span class="mandatory">*</span>
			</label>


			<input type="text" name="round_of_price_individual" id="round_of_price_individual" style="width:84%;" required="">
		</div>
		<div class="form-group">
			<label class="frm-head">
				Round Of price (Shared)<span class="mandatory">*</span>
			</label>
			<input type="text" name="round_of_price_shared" id="round_of_price_shared" style="width:84%;" required="">
		</div>


		<div class="form-group">
			<h4 style="color:rgb(199, 240, 167);display: flex; justify-content: space-between;">
				<div>
					Total Price (Individual): <span class="" id="totalgrand">0.00</span>

					<input type="hidden" name="total_price" id="total_price">
				</div>
				<div>
					Total Price (Shared): <span class="" id="totalgrandShared">0.00</span>
					<input type="hidden" name="total_price_shared" id="total_price_shared">
				</div>
			</h4>

		</div>

		<div class="form-group">
			<h4 style="color:rgb(199, 240, 167);display: flex; justify-content: space-between;">
				<div>
					Total Round Of Price (Individual): <span class="" id="totalRoundPrice">0.00</span>
					<input type="hidden" name="total_round_price" id="total_round_price">
				</div>
				<div>Total Round Of Price (Shared): <span class="" id="totalRoundPriceShared">0.00</span>
					<input type="hidden" name="total_round_price_shared" id="total_round_price_shared">
				</div>
			</h4>
		</div>

		<tr>
			<table use="rateDes" id="rateDes" style="width: 50%; border: 1px solid rgb(0, 0, 0); margin: 10px; text-align: center; border-collapse: collapse; display: none;">
				<tr>
					<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check In Date</th>
					<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check Out Date</th>
					<!-- <th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">INR Rate</th>
								 -->

				</tr>
				<tr use='rateVal'>
					<td style=' border: 1px solid rgb(0, 0, 0);'></td>
					<td style=' border: 1px solid rgb(0, 0, 0);'></td>
					<!-- <td style=' border: 1px solid rgb(0, 0, 0);'></td> -->

				</tr>
			</table>
			<!-- <tr>
			<td width="40%"></td>
			<td align="left">
				<a href="<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>">
					<input type="button" name="BackAdd" id="BackAdd" value="Back" class="btn btn-small btn-red" /></a>
				&nbsp;
				<input type="submit" name="Save2" id="Save2" value="Save" class="btn btn-small btn-blue" disabled="" />
				<span class="gen-error" style="color: red; display: block;"></span>
			</td>
		</tr> -->
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>'" type="button" class="back" id="BackAdd">Back</button>
					
					<button class="submit" id="Save2">Add</button>
					<span class="gen-error" style="color: red; display: block;"></span>
				</div>
			</div>

			</tbody>
			</table>
	</form>
	<script type="text/javascript">
		jQuery("#inclusion_lunch_date").select2({
			placeholder: 'Add Lunch Date (dd-mm-yyyy)',
			tags: true
		});
		jQuery("#inclusion_dinner_date").select2({
			placeholder: 'Add Conference Dinner Date',
			tags: true
		});
	</script>
	<script type="text/javascript">
		function hotelRoomRetriver(obj) {


			// var sequenceVal = $(obj).attr("sequence");
			var hotelId = $(obj).val();
			// alert(hotelId);
			var room_control = $(obj).attr("room_type");
			// var roomControlDiv = $('#' + roomControl).parent().closest("div[use=roomContainer]");

			if (hotelId != "") {
				var act = 'getHotelRoomType';
				$.ajax({
					type: "POST",
					url: "<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_combo_classification.process.php",
					data: 'act=getHotelRoomType&hotelId=' + hotelId + '&room_control=' + room_control,
					dataType: "html",
					async: false,
					success: function(message) {

						if (message != "") {
							// $(roomControlDiv).html('');
							// $(roomControlDiv).html(message);
							$('.roomContainer').html('');
							$('.roomContainer').html(message);
						}
					}
				});
			}
		}

		function checkClassificationTitle(argument) {
			var Title = $.trim($(argument).val());
			if (Title == "") {
				return false;
			} else {
				$.ajax({
					url: "<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_combo_classification.process.php",
					data: {
						act: 'checktitle',
						title: Title
					},
					success: function(result) {

						if ($.trim(result) === 'error') {
							$('.gen-error').text('Title already exist. Please try with another title.')
							$('input[type=submit]').hide()
						} else {
							$('.gen-error').text('')
							$('input[type=submit]').show()
						}

						console.log(result)
					},
					error(xhr, status, error) {
						console.log('error==>' + error)
					}
				});
			}

		}

		function isNumber(evt) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode == 46) {
				return true;
			} else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
				return false;
			}
			return true;
		}





		$("#compose").click(function() {
			//alert(21);
			var INRrate = $('#accommodation_price').val();
			var hotel_id = $('#hotel_id').val();
			var no_of_night = $('#no_of_night').val();
			//alert(hotel_id);
			var flag = 0;
			if (hotel_id == '') {
				alert("Please select hotel");
				flag = 1;
			} else if (INRrate == '') {
				alert("Please enter accomodation price");
				flag = 1;
			}
			if (flag == 0) {

				$("#Save2").prop('disabled', false);
				$('#rateDes').show();
				$.ajax({
					type: "POST",
					url: "manage_reg_combo_classification.process.php",
					data: 'act=compose_entry&hotel_id=' + hotel_id + '&no_of_night=' + no_of_night,
					dataType: "json",
					async: false,
					success: function(JSONObject) {
						if (JSONObject.length > 0) {
							var rateTable = $("table[use=rateDes]");

							$(rateTable).find("tr[use=rateVal]").remove();
							$.each(JSONObject, function(key, value) {
								console.log(value.DAYDIFF);
								var INRAmount = parseFloat(INRrate) * parseInt(value.DAYDIFF);
								// var USDAmount = parseFloat(USDrate)*parseInt(value.days); 
								var trString = "<tr use='rateVal'>";
								trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
								trString += value.CHECKIN;
								trString += '<input type="hidden" name="checkin_date[' + key + ']" value="' + value.CHECKINID + '" />';
								trString += "</td>";
								trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
								trString += value.CHECKOUT;
								trString += '<input type="hidden" name="checkout_date[' + key + ']" value="' + value.CHECKOUTID + '" />';
								trString += '<input type="hidden" name="INRAmt[' + key + ']" value="' + INRAmount + '" />';
								trString += "</td>";
								/*trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
								trString += 'INR '+(INRAmount).toFixed(2);
								trString += '<input type="hidden" name="INRAmt['+key+']" value="'+INRAmount+'" />';
								trString += "</td>";*/
								// trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";

								trString += "</td>";
								trString += "</tr>";

								$(rateTable).append(trString);
							});
						} else {
							alert("No record found!");
						}
						//console.log(JSONObject);
						/*$.each(JSONObject, function (key, value) {
							console.log(value.checkin); 
						});*/

					}
				});

			}

		});

		/*function getAccomodationRoundPrice(val)
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
		}*/





		/*$('#round_of_price').on('input',function() {
			var total_price = parseFloat($('#total_price').val());
			var round_of_price = parseFloat($('#round_of_price').val());

			if(total_price!='' && round_of_price!='')
			{

			 $('#totalRoundPrice').text((total_price + round_of_price ? total_price + round_of_price : 0).toFixed(2));
			  $('#total_round_price').val((total_price + round_of_price ? total_price + round_of_price : 0).toFixed(2));
			 
			}

		});	*/
		$('#round_of_price_individual,#round_of_price_shared, #registration_price, #workshop_price,#dinner_price,#accommodation_price_individual,#accommodation_price_shared,#no_of_night').on('input', function() {

			var round_of_price_individual = parseFloat($('#round_of_price_individual').val());
			var round_of_price_shared = parseFloat($('#round_of_price_shared').val());

			var total = 0;
			var total_shared = 0;


			var registration_price = $('#registration_price').val();
			var workshop_price = $('#workshop_price').val();
			var dinner_price = $('#dinner_price').val();
			var accommodation_price_individual = $('#accommodation_price_individual').val();
			var accommodation_price_shared = $('#accommodation_price_shared').val();

			var no_of_night = $('#no_of_night').val();
			var total_accomodation = 0;
			if (accommodation_price_individual != '' && no_of_night != '') {
				total_accomodation_individual = (parseFloat(accommodation_price_individual) * no_of_night);
			}
			if (accommodation_price_shared != '' && no_of_night != '') {
				total_accomodation_shared = (parseFloat(accommodation_price_shared) * no_of_night);
			}

			if (workshop_price != '') {
				newWorkshop_price = workshop_price;
			} else {
				newWorkshop_price = 0;
			}
			if (dinner_price != '') {
				newDinner_price = dinner_price;
			} else {
				newDinner_price = 0;
			}
			if (round_of_price_individual != '') {
				newround_of_price_individual = round_of_price_individual;
			} else {
				newround_of_price_individual = 0;
			}
			if (round_of_price_shared != '') {
				newround_of_price_shared = round_of_price_shared;
			} else {
				newround_of_price_shared = 0;
			}
			//alert(round_of_price);

			if (registration_price != '' || newWorkshop_price != '' || newDinner_price != '') {
				//alert('wrk='+newWorkshop_price+'din='+newWorkshop_price+'accom='+total_accomodation);
				total += parseInt(registration_price) + parseInt(newWorkshop_price) + parseInt(newDinner_price) + parseInt(total_accomodation_individual);
				total_shared += parseInt(registration_price) + parseInt(newWorkshop_price) + parseInt(newDinner_price) + parseInt(total_accomodation_shared);
			}


			//alert(total);
			if (isNaN(total)) {

				$('#totalgrand').text('0.00');
			} else {
				$('#totalgrand').text(total);

				$('#total_price').val(total);
			}

			if (isNaN(total_shared)) {

				$('#totalgrandShared').text('0.00');
			} else {
				$('#totalgrandShared').text(total_shared);

				$('#total_price_shared').val(total_shared);
			}

			if (total != '' || newround_of_price_individual > 0) {
				//alert(total);
				$('#totalRoundPrice').text((total + newround_of_price_individual ? total + newround_of_price_individual : 0).toFixed(2));
				$('#total_round_price').val((total + newround_of_price_individual ? total + newround_of_price_individual : 0).toFixed(2));
			}
			if (total_shared != '' || newround_of_price_shared > 0) {
				$('#totalRoundPriceShared').text((total_shared + newround_of_price_shared ? total_shared + newround_of_price_shared : 0).toFixed(2));
				$('#total_round_price_shared').val((total_shared + newround_of_price_shared ? total_shared + newround_of_price_shared : 0).toFixed(2));

			}

		});

		jQuery("#workshop_classification").select2({
			placeholder: 'Select Classification',
			tags: true
		});
	</script>
<?php
}

/**************************************************************/
/*                       EDIT REGISTRATION CLASSIFICATION                     */
/**************************************************************/
function editWorkshop($cfg, $mycms)
{

	include_once('includes/function.delegate.php');
	global $searchArray, $searchString;

	$cutoffId = $_REQUEST['cutoff_id'];
	$classification_id = $_REQUEST['id'];
	$sql_hotel	=	array();
	$sql_hotel['QUERY']	=	"SELECT * 
									FROM " . _DB_MASTER_HOTEL_ . "
									WHERE `status` 	= 		?
									ORDER BY `id` ASC";

	$sql_hotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');

	$res_hotel = $mycms->sql_select($sql_hotel);

	$sql_workshop_classification	=	array();
	$sql_workshop_classification['QUERY']	=	"SELECT classification_title, id 
									FROM " . _DB_WORKSHOP_CLASSIFICATION_ . "
									WHERE `status` 	= 		?
									ORDER BY `id` ASC";

	$sql_workshop_classification['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');

	$res_workshop_classification = $mycms->sql_select($sql_workshop_classification);


	$currentCutoffId = getTariffCutoffId();
?>
	<script language="javascript" src="<?= $cfg['SECTION_BASE_URL'] ?>scripts/select2.min.js"></script>
	<link rel="stylesheet" href="<?= $cfg['SECTION_BASE_URL'] ?>css/select2.min.css">
	<form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_combo_classification.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" value="update" />
		<input type="hidden" name="id" id="id" value="<?= $_REQUEST['id'] ?>" />

		<?php
		$sql 	=	array();
		$sql['QUERY']	=	"SELECT * 
								   FROM " . _DB_REGISTRATION_COMBO_CLASSIFICATION_ . " 
								  WHERE `id` = ? ";
		$sql['PARAM'][]		=	array('FILD' => 'id', 		  'DATA' => $_REQUEST['id'],				   'TYP' => 's');
		$res_cal = $mycms->sql_select($sql);
		$row    = $res_cal[0];

		$hotelId = $row['residential_hotel_id'];

		$cutoffArray  = array();
		$sqlCutoff['QUERY']    = " SELECT * 
									 FROM " . _DB_TARIFF_CUTOFF_ . " 
									WHERE `status` = ? 
								 ORDER BY `cutoff_sequence` ASC";
		$sqlCutoff['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
		$resCutoff = $mycms->sql_select($sqlCutoff);
		if ($resCutoff) {
			foreach ($resCutoff as $i => $rowCutoff) {
				$cutoffArray[$rowCutoff['id']] = $rowCutoff['cutoff_title'];
			}
		}

		$sqlRegClasf['QUERY']	  = "SELECT `classification_title`,`id` 
							  	   FROM " . _DB_REGISTRATION_CLASSIFICATION_ . "  
							 	  WHERE `status` = 'A' ";
		$resRegClasf		 = $mycms->sql_select($sqlRegClasf);

		$sqlComboDate  = array();
		$sqlComboDate['QUERY'] = "SELECT CC.check_in_date, co.check_out_date,TA.inr_amount_individual FROM " . _DB_TARIFF_COMBO_ACCOMODATION_ . " TA 
					INNER JOIN " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " CC ON
						 TA.checkin_date_id = CC.id 
					INNER JOIN " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " co 
						ON TA.checkout_date_id = co.id 
						WHERE TA.classification_id=? AND CC.status=? AND co.status=? AND TA.status=? ";

		$sqlComboDate['PARAM'][]  = array('FILD' => 'TA.classification_id',  'DATA' => $_REQUEST['id'],  'TYP' => 's');
		$sqlComboDate['PARAM'][]  = array('FILD' => 'CC.status',  'DATA' => 'A',  'TYP' => 's');
		$sqlComboDate['PARAM'][]  = array('FILD' => 'co.status',  'DATA' => 'A',  'TYP' => 's');
		$sqlComboDate['PARAM'][]  = array('FILD' => 'TA.status',  'DATA' => 'A',  'TYP' => 's');

		$resComboDate = $mycms->sql_select($sqlComboDate);



		$workshop_title = json_decode($row['workshop_classification']);


		// $sqlHotel				= array();
		// $sqlHotel['QUERY']	 	= "SELECT
		// 											tracm.id as ACCOMMODATION_TARIFF_ID,
		// 											tracm.hotel_id as HOTEL_ID,
		// 											tracm.package_id as ACCOMMODATION_PACKAGE_ID, 
		// 											chkindate.check_in_date as CHECKIN_DATE,
		// 											tracm.checkin_date_id as CHECKIN_DATE_ID,
		// 											tracm.checkout_date_id as CHECKOUT_DATE_ID,
		// 											chkoutdate.check_out_date as CHECKOUT_DATE,
		// 											DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS 
		// 											FROM " . _DB_TARIFF_ACCOMMODATION_ . " as tracm
		// 											INNER JOIN " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " as chkindate
		// 											on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
		// 											INNER JOIN " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " as chkoutdate
		// 											on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
		// 											WHERE tracm.status = ? AND tracm.type = ? AND tracm.created_dateTime != ? AND tracm.tariff_cutoff_id = ? 
		// 											AND tracm.hotel_id ='" . $hotelId . "' ORDER BY CHECKIN_DATE ASC, CHECKOUT_DATE ASC"; // HAVING (DAYS) < 4 // remove on 21.09.2022 (user can select hotels more then 3 days)
		// $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.status', 'DATA' => 'A',  'TYP' => 's');
		// $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.type', 'DATA' => 'new',  'TYP' => 's');
		// $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');
		// $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $currentCutoffId,  'TYP' => 's');
		// $resultHotel = $mycms->sql_select($sqlHotel);

		$sqlHotel				= array();
		$sqlHotel['QUERY']	 	= "SELECT
													tracm.id as ACCOMMODATION_TARIFF_ID,
													tracm.hotel_id as HOTEL_ID,
													
													chkindate.check_in_date as CHECKIN_DATE,
													tracm.checkin_date_id as CHECKIN_DATE_ID,
													tracm.checkout_date_id as CHECKOUT_DATE_ID,
													chkoutdate.check_out_date as CHECKOUT_DATE,
													DATEDIFF(chkoutdate.check_out_date , chkindate.check_in_date) AS DAYS 
													FROM " . _DB_TARIFF_COMBO_ACCOMODATION_ . " as tracm
													INNER JOIN " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " as chkindate
													on chkindate.id = tracm.checkin_date_id AND chkindate.hotel_id = tracm.hotel_id AND chkindate.status = 'A'
													INNER JOIN " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . " as chkoutdate
													on chkoutdate.id = tracm.checkout_date_id AND chkoutdate.hotel_id = tracm.hotel_id AND chkoutdate.status = 'A'
													WHERE tracm.status = ? AND tracm.tariff_cutoff_id = ?  AND tracm.classification_id=?
													AND tracm.hotel_id ='" . $hotelId . "' ORDER BY CHECKIN_DATE ASC, CHECKOUT_DATE ASC"; // HAVING (DAYS) < 4 // remove on 21.09.2022 (user can select hotels more then 3 days)
		$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.status', 'DATA' => 'A',  'TYP' => 's');
		// $sqlHotel['PARAM'][]    = array('FILD' => 'tracm.created_dateTime', 'DATA' => 'Null',  'TYP' => 's');classification_id
		$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.tariff_cutoff_id', 'DATA' => $cutoffId,  'TYP' => 's');
		$sqlHotel['PARAM'][]    = array('FILD' => 'tracm.classification_id', 'DATA' => $classification_id,  'TYP' => 's');
		$resultHotel = $mycms->sql_select($sqlHotel);


		$accommodationDetails = array();
		foreach ($resultHotel as $key => $value) {

			$accommodationDetails[$value['HOTEL_ID']][] = $value;
		}
		// echo '<pre>'; print_r($currentCutoffId);die;

		?>
		<input type="hidden" name="previous_hotel_id" value="<?= $row['residential_hotel_id'] ?>">
		<table width="65%" class="tborder">
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
								<td colspan="2" align="left">Combo Classification</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Package Name <span class="mandatory">*</span>
									<!-- Classification Title <span class="mandatory">*</span> -->
								</td>
								<td width="60%" align="left">
									<input type="text" name="classification_title" id="classification_title" class="validate[required]" value="<?= $row['classification_title'] ?>" style="width:80%;" required />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Cutoff <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="cutoff_id" style="width:84%;" required="" disabled="">
										<option value="">Choose cutoff</option>
										<?php

										foreach ($cutoffArray as $key => $value) {
										?>
											<option value="<?php echo $key ?>" <?php if ($row['cutoff_id'] == $key) {
																					echo 'selected';
																				} ?>><?php echo $value; ?></option>
										<?php
										}
										?>


									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Registration Classification <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="registration_classification_id" style="width:84%;" required="" disabled="">
										<option value="">Choose Classification</option>
										<?php

										foreach ($resRegClasf as $key => $value) {
										?>
											<option value="<?php echo $value['id'] ?>" <?php if ($row['registration_classification_id'] == $value['id']) {
																							echo 'selected';
																						} ?>><?php echo getRegClsfName($value['id']); ?></option>
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
									<select name="residential_hotel_id" id="hotel_id_edit" style="width:84%;" required="" room_type="room_type" onchange="hotelRoomRetriver(this);">
										<option value="">Choose hotel</option>
										<?php

										foreach ($res_hotel as $key => $value) {
										?>
											<option value="<?php echo $value['id'] ?>" <?php if ($row['residential_hotel_id'] == $value['id']) {
																							echo 'selected';
																						} ?>><?php echo $value['hotel_name'] ?></option>
										<?php
										}
										?>


									</select>
									<?php
									if ($row['residential_hotel_id'] != '') {
										// if ($userRec['user_country_id'] == '') {
									?>
										<script>
											$(document).ready(function() {
												hotelRoomRetriver(document.getElementById('hotel_id_edit'));
												jBaseUrl = jsBASE_URL;
												// getHotelRoomList(<?= $row['residential_hotel_id'] ?>);
												$('#room_type option[value="<?= $row['room_type'] ?>"]').prop('selected', true);
												//------- FOR DEFAULT WEST BENGAL -------
												// generateSateList(1, jBaseUrl);
												// $('#user_state option[value="730"]').prop('selected', true);
											});
										</script>
									<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<td align="left">
									Room Type <span class="mandatory"></span>
								</td>
								<td align="left">
									<div use='roomContainer' class="roomContainer">
										<select name="room_type" id="room_type" style="width:80%;" class="validate[required]" required>
											<option value="">-- Select Hotel First --</option>
											<!-- <option value="730">WEST BENGAL</option> -->
										</select>
									</div>
									<!-- <input type="text" name="room_type" id="room_type" class="validate[required]" style="width:80%;" value="<?= $row['room_type'] ?>" /> -->
								</td>
							</tr>

							<tr>
								<td align="left">
									Registration Price <span class="mandatory"></span>
								</td>
								<td align="left">
									<input type="text" name="registration_price" id="registration_price" class="validate[required]" onkeypress='return isNumber(event)' style="width:80%;" value="<?= $row['registration_price'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="left">
									Workshop Price <span class="mandatory"></span>
								</td>
								<td align="left">
									<input type="text" name="workshop_price" id="workshop_price" class="validate[required]" value="<?= $row['workshop_price'] ?>" onkeypress='return isNumber(event)' style="width:80%;" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Workshop Classification <span class="mandatory"></span>
								</td>
								<td width="60%" align="left">
									<select name="workshop_classification[]" id="workshop_classification" multiple="multiple" style="width:82%">

										<?php
										if (count($res_workshop_classification) > 0) {

											foreach ($res_workshop_classification as $key => $value) {

												if (in_array($value['id'], $workshop_title)) {
													$selected = 'selected';
												} else {
													$selected = '';
												}
										?>

												<option value="<?php echo $value['id']; ?>" <?= $selected ?>><?php echo $value['classification_title']; ?></option>
										<?php
											}
										}
										?>
									</select>

								</td>
							</tr>
							<tr>
								<td align="left">
									Dinner Price <span class="mandatory"></span>
								</td>
								<td align="left">
									<input type="text" name="dinner_price" id="dinner_price" class="validate[required]" value="<?= $row['dinner_price'] ?>" style="width:80%;" onkeypress='return isNumber(event)' />
								</td>
							</tr>
							<tr>
								<td align="left">
									Inclusion Lunch Date
								</td>
								<td align="left">
									<select name="inclusion_lunch_date[]" id="inclusion_lunch_date" multiple="multiple" style="width:84%">
										<?php
										$selected_inclusion_lunch_date = json_decode($row['inclusion_lunch_date']);

										$sql_cal = array();
										$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Lunch'";
										$res_cal			=	$mycms->sql_select($sql_cal);
										$i = 1;

										foreach ($res_cal as $key => $rowsl) {
											if (in_array(date('d-m-Y', strtotime($rowsl['date'])), $selected_inclusion_lunch_date)) {
												$selected = "selected";
											} else {
												$selected = "";
											}
										?>
											<option value="<?= date('d-m-Y', strtotime($rowsl['date'])) ?>" <?= $selected ?>><?= date('d-m-Y', strtotime($rowsl['date'])) ?></option>
										<?php } ?>

									</select>

								</td>

							</tr>
							<tr>
								<td align="left">
									Inclusion Conference Dinner Date
								</td>
								<td align="left">
									<select name="inclusion_dinner_date[]" id="inclusion_dinner_date" multiple="multiple" style="width:85%">
										<?php
										$selected_dinner_date = json_decode($row['inclusion_dinner_date']);

										$sql_cal = array();
										$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Dinner'";
										$res_cal			=	$mycms->sql_select($sql_cal);
										$i = 1;

										foreach ($res_cal as $key => $rowsl) {

											if (in_array(date('d-m-Y', strtotime($rowsl['date'])), $selected_dinner_date)) {
												$selected = "selected";
											} else {
												$selected = "";
											}
										?>
											<option value="<?= date('d-m-Y', strtotime($rowsl['date'])) ?>" <?= $selected ?>><?= date('d-m-Y', strtotime($rowsl['date'])) ?></option>
										<?php } ?>

									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Entry to Scientific Halls
								</td>
								<td width="10%" align="left">
									<?php
									?>
									<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="Y" <? if ($row['inclusion_sci_hall'] == 'Y') {
																														echo "checked";
																													} ?> />YES
									<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="N" <? if ($row['inclusion_sci_hall'] == 'N') {
																														echo "checked";
																													} ?> />No
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Entry to Exhibition Area
								</td>
								<td width="10%" align="left">
									<?php
									?>
									<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="Y" <? if ($row['inclusion_exb_area'] == 'Y') {
																														echo "checked";
																													} ?> />YES
									<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="N" <? if ($row['inclusion_exb_area'] == 'N') {
																														echo "checked";
																													} ?> />No
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Tea/Coffee during the Conference
								</td>
								<td width="10%" align="left">
									<?php
									?>
									<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="Y" <? if ($row['inclusion_tea_coffee'] == 'Y') {
																															echo "checked";
																														} ?> />YES
									<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="N" <? if ($row['inclusion_tea_coffee'] == 'N') {
																															echo "checked";
																														} ?> />No
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Inclusion Conference Kit<span class="mandatory">*</span>
								</td>
								<td width="10%" align="left">
									<?php
									?>
									<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="Y" <? if ($row['inclusion_conference_kit'] == 'Y') {
																																	echo "checked";
																																} ?> required />YES
									<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="N" <? if ($row['inclusion_conference_kit'] == 'N') {
																																	echo "checked";
																																} ?> required />No
								</td>
							</tr>
							<tr>
								<td align="left">
									Accommodation Price (Individual)<span class="mandatory"></span>
								</td>
								<td align="left">
									<input type="text" name="accommodation_price_individual" id="accommodation_price_individual" class="validate[required]" value="<?= $row['accommodation_price_individual'] ?>" style="width:80%;" onkeypress='return isNumber(event)' />
								</td>
							</tr>
							<tr>
								<td align="left">
									Accommodation Price (Shared)<span class="mandatory"></span>
								</td>
								<td align="left">
									<input type="text" name="accommodation_price_shared" id="accommodation_price_shared" class="validate[required]" value="<?= $row['accommodation_price_shared'] ?>" style="width:80%;" onkeypress='return isNumber(event)' />
								</td>
							</tr>
							<tr>
								<td align="left">
									No. Of Night <span class="mandatory"></span>
								</td>
								<td align="left">
									<?php
									/*
										?>
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
										</select> <?php*/ ?>
									<input type="number" name="no_of_night" class="no_of_night" id="no_of_night" style="width:59%;" value="<?= $row['no_of_night'] ?>" required>

									<input type="button" name="compose" style="width:20%;" id="composeEdit" class="btn btn-small btn-red" value="Compose">
									<!-- <input type="hidden" name="no_of_night" id="no_of_night" style="width:80%;" value="<?= $row['no_of_night'] ?>" required> -->
									<!-- <?= $row['no_of_night'] ?> -->
								</td>
							</tr>
							<tr>
								<td align="left">
									Seat limit <span class="mandatory">*</span>
								</td>
								<td align="left">
									<input type="text" name="seat_limit_add" id="seat_limit_add" class="validate[required]" onblur="countryAvailabilityAdd(this.value)" value="<?= $row['seat_limit'] ?>" style="width:80%;" required />
								</td>
							</tr>
							<tr>
								<td align="left">
									Sequence By
								</td>
								<td align="left">
									<input type="number" name="sequence_by" id="sequence_by" value="<?= $row['sequence_by'] ?>" style="width:80%;" rel="splDate" required />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Currency<span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<select name="currency" style="width:84%;" required="">
										<option <? if ($row['type'] == 'INR') {
													echo "selected";
												} ?> value="INR">INR</option>
										<option <? if ($row['type'] == 'USD') {
													echo "selected";
												} ?> value="USD">USD</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Round Of price (Individual)<span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">

									<input type="text" name="round_of_price_individual" id="round_of_price_individual" style="width:81%;" required="" value="<?= $row['round_of_price_individual'] ?>">
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Round Of price (Shared)<span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">

									<input type="text" name="round_of_price_shared" id="round_of_price_shared" style="width:81%;" required="" value="<?= $row['round_of_price_shared'] ?>">
								</td>
							</tr>
							<tr>&nbsp;</tr>
							<tr style="color: #2f5c0d;font-weight:bolder">
								<td width="40%" align="left">
									Total Price (Individual): <span class="" id="totalgrand"><?= $row['total_price'] ?></span>
									<input type="hidden" name="total_price" id="total_price" value="<?= $row['total_price'] ?>">
								</td>
								<td width="40%" align="left">
									Total Price (Shared): <span class="" id="totalgrandShared"><?= $row['total_price_shared'] ?></span>
									<input type="hidden" name="total_price_shared" id="total_price_shared" value="<?= $row['total_price_shared'] ?>">
								</td>

							</tr>

							<tr style="color: #2f5c0d;font-weight:bolder">
								<td width="40%" align="left">
									Total Round Of Price (Individual): <span class="" id="totalRoundPrice"><?= $row['total_round_price'] ?></span>
									<input type="hidden" name="total_round_price" id="total_round_price" value="<?= $row['total_round_price'] ?>">

									<input type="hidden" name="para_cutoff_id" value="<?= $_REQUEST['cutoff_id'] ?>">
								</td>
								<td width="40%" align="left">
									Total Round Of Price (Shared): <span class="" id="totalRoundPriceShared"><?= $row['total_round_price_shared'] ?></span>
									<input type="hidden" name="total_round_price_shared" id="total_round_price_shared" value="<?= $row['total_round_price_shared'] ?>">

									<input type="hidden" name="para_cutoff_id" value="<?= $_REQUEST['cutoff_id'] ?>">
								</td>

							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<table use="rateDes" style="width: 50%; border: 1px solid rgb(0, 0, 0); margin: 10px; text-align: center; border-collapse: collapse;">
						<?

						?>
						<tr>
							<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check In Date</th>
							<th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">Check Out Date</th>
							<!-- <th style="border: 1px solid rgb(0, 0, 0); font-weight: bold;">INR Rate</th> -->

						</tr>
						<?php foreach ($resultHotel as $key => $accomodationTariff) {

						?>
							<tr use='rateVal'>
								<td style=' border: 1px solid rgb(0, 0, 0);'><?= $accomodationTariff['CHECKIN_DATE'] ?></td>
								<td style=' border: 1px solid rgb(0, 0, 0);'><?= $accomodationTariff['CHECKOUT_DATE'] ?></td>
								<!-- <td style=' border: 1px solid rgb(0, 0, 0);'><?= $accomodationTariff['inr_amount_individual'] ?></td> -->

							</tr>
						<? } ?>
					</table>
				</tr>
				<tr>
					<td width="40%"></td>
					<td align="left">
						<a href="<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>">
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
		jQuery("#inclusion_lunch_date").select2({
			placeholder: 'Add Lunch Date (dd-mm-yyyy)',
			tags: true
		});
		jQuery("#inclusion_dinner_date").select2({
			placeholder: 'Add Conference Dinner Date',
			tags: true
		});
	</script>
	<script type="text/javascript">
		function hotelRoomRetriver(obj) {


			// var sequenceVal = $(obj).attr("sequence");
			var hotelId = $(obj).val();
			// alert(hotelId);
			var room_control = $(obj).attr("room_type");
			// var roomControlDiv = $('#' + roomControl).parent().closest("div[use=roomContainer]");

			if (hotelId != "") {
				var act = 'getHotelRoomType';
				$.ajax({
					type: "POST",
					url: "<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_combo_classification.process.php",
					data: 'act=getHotelRoomType&hotelId=' + hotelId + '&room_control=' + room_control,
					dataType: "html",
					async: false,
					success: function(message) {

						if (message != "") {
							// $(roomControlDiv).html('');
							// $(roomControlDiv).html(message);
							$('.roomContainer').html('');
							$('.roomContainer').html(message);
						}
					}
				});
			}
		}

		function getAccomodationRoundPrice(val) {
			if (val != '') {
				var total = 0;
				var registration_price = $('#registration_price').val();
				var workshop_price = $('#workshop_price').val();
				var dinner_price = $('#dinner_price').val();
				var accommodation_price = $('#accommodation_price').val();

				var total_accomodation = (parseFloat(accommodation_price) * val);

				if (registration_price != '' && workshop_price != '' && dinner_price != '' && accommodation_price != '') {
					total += parseFloat(registration_price) + parseFloat(workshop_price) + parseFloat(dinner_price) + parseFloat(total_accomodation);
				}

				//alert(total);
				if (isNaN(total)) {

					$('#totalgrand').text('0.00');
				} else {
					$('#totalgrand').text(total);

					$('#total_price').val(total);
				}

			}
		}

		$("#composeEdit").click(function() {
			//alert(21);
			var INRrate = $('#accommodation_price').val();
			var hotel_id = $('#hotel_id_edit').val();
			var no_of_night = $('.no_of_night').val();
			//alert(hotel_id);
			var flag = 0;
			if (hotel_id == '') {
				alert("Please select hotel");
				flag = 1;
			} else if (INRrate == '') {
				alert("Please enter accomodation price");
				flag = 1;
			}
			if (flag == 0) {

				$("#Save2").prop('disabled', false);
				$('#rateDes').show();
				$.ajax({
					type: "POST",
					url: "manage_reg_combo_classification.process.php",
					data: 'act=compose_entry&hotel_id=' + hotel_id + '&no_of_night=' + no_of_night,
					dataType: "json",
					async: false,
					success: function(JSONObject) {
						if (JSONObject.length > 0) {
							var rateTable = $("table[use=rateDes]");

							$(rateTable).find("tr[use=rateVal]").remove();
							$.each(JSONObject, function(key, value) {
								console.log(value.DAYDIFF);
								var INRAmount = parseFloat(INRrate) * parseInt(value.DAYDIFF);
								// var USDAmount = parseFloat(USDrate)*parseInt(value.days); 
								var trString = "<tr use='rateVal'>";
								trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
								trString += value.CHECKIN;
								trString += '<input type="hidden" name="checkin_date[' + key + ']" value="' + value.CHECKINID + '" />';
								trString += "</td>";
								trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
								trString += value.CHECKOUT;
								trString += '<input type="hidden" name="checkout_date[' + key + ']" value="' + value.CHECKOUTID + '" />';
								trString += '<input type="hidden" name="INRAmt[' + key + ']" value="' + INRAmount + '" />';
								trString += "</td>";
								/*trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";
								trString += 'INR '+(INRAmount).toFixed(2);
								trString += '<input type="hidden" name="INRAmt['+key+']" value="'+INRAmount+'" />';
								trString += "</td>";*/
								// trString += "<td style=' border: 1px solid rgb(0, 0, 0);'>";

								trString += "</td>";
								trString += "</tr>";

								$(rateTable).append(trString);
							});
						} else {
							alert("No record found!");
						}
						//console.log(JSONObject);
						/*$.each(JSONObject, function (key, value) {
							console.log(value.checkin); 
						});*/

					}
				});

			}

		});

		$('#round_of_price_individual,#round_of_price_shared, #registration_price, #workshop_price,#dinner_price,#accommodation_price_individual,#accommodation_price_shared,#no_of_night').on('input', function() {
			//var total_price = parseFloat($('#total_price').val());
			var round_of_price_individual = parseFloat($('#round_of_price_individual').val());
			var round_of_price_shared = parseFloat($('#round_of_price_shared').val());

			var total = 0;
			var total_shared = 0;
			var registration_price = $('#registration_price').val();
			var workshop_price = $('#workshop_price').val();
			var dinner_price = $('#dinner_price').val();
			var accommodation_price_individual = $('#accommodation_price_individual').val();
			var accommodation_price_shared = $('#accommodation_price_shared').val();

			var no_of_night = $('#no_of_night').val();

			var total_accomodation_individual = (parseFloat(accommodation_price_individual) * no_of_night);
			var total_accomodation_shared = (parseFloat(accommodation_price_shared) * no_of_night);

			if (registration_price != '' || workshop_price != '' || dinner_price != '' || total_accomodation_individual != '') {
				total += parseFloat(registration_price) + parseFloat(workshop_price) + parseFloat(dinner_price) + parseFloat(total_accomodation_individual);
			}
			if (registration_price != '' || workshop_price != '' || dinner_price != '' || accommodation_price_shared != '') {
				total_shared += parseFloat(registration_price) + parseFloat(workshop_price) + parseFloat(dinner_price) + parseFloat(total_accomodation_shared);
			}

			//alert(total);
			if (isNaN(total)) {

				$('#totalgrand').text('0.00');
			} else {
				$('#totalgrand').text(total);

				$('#total_price').val(total);
			}

			if (isNaN(total_shared)) {

				$('#totalgrandShared').text('0.00');
			} else {
				$('#totalgrandShared').text(total_shared);

				$('#total_price_shared').val(total_shared);
			}

			if (total != '' || round_of_price_individual > 0) {

				$('#totalRoundPrice').text((total + round_of_price_individual ? total + round_of_price_individual : 0).toFixed(2));
				$('#total_round_price').val((total + round_of_price_individual ? total + round_of_price_individual : 0).toFixed(2));

			}

			if (total_shared != '' || round_of_price_shared > 0) {

				$('#totalRoundPriceShared').text((total + round_of_price_shared ? total_shared + round_of_price_shared : 0).toFixed(2));
				$('#total_round_price_shared').val((total + round_of_price_shared ? total_shared + round_of_price_shared : 0).toFixed(2));

			}

		});
		jQuery("#workshop_classification").select2({
			placeholder: 'Select Classification',
			tags: true
		});
	</script>
<?php
}
?>