<?php
include_once('includes/init.php');
page_header("Hotel");

$pageKey        = "_pgn1_";

$pageKeyVal     = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString  = "";
$searchArray    = array();

$searchArray[$pageKey]                     = $pageKeyVal;
$searchArray['src_hotel_name']             = addslashes(trim($_REQUEST['src_hotel_name']));

foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}
?>
<script type="text/javascript" language="javascript" src="scripts/hotel_listing.js" />
</script>
<div class="body_wrap">
	<?php
	switch ($show) {

		// HOTEL ADD FORM LAYOUT
		case 'add':

			hotelAddFormLayout($cfg, $mycms);
			break;

		// HOTEL EDIT FORM LAYOUT
		case 'edit':

			hotelEditFormLayout($cfg, $mycms);
			break;

		case 'updateSeatLimit':
			updateSeat($cfg, $mycms);
			break;


		// HOTEL VIEW FORM LAYOUT	
		case 'view':

			hotelViewFormLayout($cfg, $mycms);
			break;

		// HOTEL LISTING LAYOUT
		default:

			hotelListingLayout($cfg, $mycms);
			break;
	}
	?>
</div>
<?php

page_footer();

/**************************************************************/
/*                       HOTEL & ROOM LISTING                 */
/**************************************************************/
function hotelListingLayout($cfg, $mycms)
{
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmSearch" method="post" action="hotel_listing.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_hotel" />
			<div class="form-group">
				<h2>Hotel Listing
					<span>
						<a onclick="$('.search_wrap').slideToggle();"><i class="fas fa-search"></i></a>
					</span>
				</h2>
			</div>
			<div class="search_wrap" style="overflow: hidden;">
				<div class="search_wrap_inner">
					<div>
						<span>Hotel Name</span>
						<input type="text" name="src_hotel_name" id="src_hotel_name" value="<?= $_REQUEST['src_hotel_name'] ?>" />
					</div>
					<?php
					searchStatus();
					?>

					<button type="submit" name="SaveSearch" id="SaveSearch">Search</button>
				</div>
			</div>
			<div class="table_wrap">

			
			<table width="100%">
				<thead>
					<tr class="theader">
						<th class="sl">Sl No</th>
						<th align="left">Hotel Name</th>
						<th align="left">Seat Limit</th>
						<th width="120" align="center">Phone No</th>
						<th class="action">Status</th>
						<th class="action">Action</th>
					</tr>
				</thead>
				<?php
				$searchCondition      = "";

				if ($_REQUEST['src_hotel_name'] != '') {
					$searchCondition .= " AND hotel_name LIKE '%" . $_REQUEST['src_hotel_name'] . "%'";
				}

				$hotelCounter        		= 0;

				$sqlFetchHotel				=	array();
				$sqlFetchHotel['QUERY']		=	"SELECT * 
														   FROM " . _DB_MASTER_HOTEL_ . "
														  WHERE `status` 		!= 	 ? " . $searchCondition;

				$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');
				$resultFetchHotel    		= $mycms->sql_select_paginated(1, $sqlFetchHotel, 50, $restrt);
				if ($resultFetchHotel) {
					foreach ($resultFetchHotel as $keyHotel => $rowFetchHotel) {
						$hotelCounter = $hotelCounter + 1;
				?>
						<tr class="tlisting">
							<td class="sl"><?= $hotelCounter ?></td>
							<td align="left" valign="top"><?= $rowFetchHotel['hotel_name'] ?></td>

							<?php
							$sql 		    	  =	array();
							$sql['QUERY'] = "SELECT *								
																	  FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
																	  WHERE hotel_id = ?
																		AND status != ?";
							$sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $rowFetchHotel['id'], 'TYP' => 's');
							$sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
							$result = $mycms->sql_select($sql);
							$seat_limit_text = "";
							// $rowSeatLimit          = $result[0];
							foreach ($result as $key => $rowSeatLimit) {
								$seat_limit_text .= $rowSeatLimit['check_in_date'] . " -> " . $rowSeatLimit['seat_limit'] . "<br>";
							}
							?>

							<td align="left" valign="top"><?= $seat_limit_text ?></td>
							<td align="center" valign="top"><?= $rowFetchHotel['hotel_phone_no'] ?></td>
							<td class="action">

								<a href="hotel_listing.process.php?act=<?= ($rowFetchHotel['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $rowFetchHotel['id'] ?><?= $searchString ?>" class="<?= ($rowFetchHotel['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowFetchHotel['status'] == 'A') ? 'Active' : 'Inactive' ?></a>

							</td>
							<td class="action">

								<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="hotel_listing.php?show=view&id=<?= $rowFetchHotel['id'] ?>">
									<span title="View Hotel" class="icon-eye" /></a>

								<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="hotel_listing.php?show=edit&id=<?= $rowFetchHotel['id'] ?>">
									<span alt="Edit" title="Edit Hotel" class="icon-pen" /></a>

								<?php
								if ($rowFetchHotel['totalRoomAllocated'] <= 0 && $rowFetchHotel['totalBulkRoom'] <= 0) {
								?>
									<a href="hotel_listing.process.php?act=Remove&id=<?= $rowFetchHotel['id'] ?>">
										<span alt="Remove" title="Remove Hotel" class="icon-x-alt" onclick="return confirm('Do You Really Want To Remove The Hotel ?');" /></a>
								<?php
								}
								?>
								<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="add_accomodation_date.php?show=edit&id=<?= $rowFetchHotel['id'] ?>">
									<span alt="Edit" title="Edit Dates & Seat Limit" class="icon-pen"></span></a>

							</td>
						</tr>
					<?php
					}
				} else {
					?>
					<tr>
						<td colspan="13" align="center">
							<span class="mandatory">No Record Present.</span>
						</td>
					</tr>
				<?php
				}
				?>
			</table>
			<div class="bbp-pagination">
				<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
				<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
			</div>
			</div>
		</form>
	</div>
<?php
}

/**************************************************************/
/*                       ADD HOTEL & ROOM FORM                */
/**************************************************************/
function hotelAddFormLayout($cfg, $mycms)
{
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmInsert" id="frmInsert" action="hotel_listing.process.php" method="post" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="insert" />
			<div class="form-group">
				<h2>Add New Hotel</h2>
			</div>
			<div class="form-group">
				<h4>Hotel Details</h4>
			</div>
			<div class="form-group">
				<label class="frm-head">Hotel Name <span class="mandatory">*</span></label>
				<input name="hotel_name_add" id="hotel_name_add" required="" />
			</div>
			<div class="form-group">
				<label class="frm-head">Address <span class="mandatory">*</span></label>
				<textarea name="hotel_address_add" id="hotel_address_add" required=""></textarea>
			</div>
			<div class="form-group">
				<label class="frm-head">Hotel Phone No <span class="mandatory">*</span></label>
				<input type="number" name="hotel_phone_add" id="hotel_phone_add" required="" />
			</div>
			<div class="form-group">
				<label class="frm-head">Distance From Venue (Km) <span class="mandatory">*</span></label>
				<input type="number" name="distance_from_venue_add" id="distance_from_venue_add" required="" />
			</div>
			<div class="form-group">
				<label class="frm-head">Check In Date <span class="mandatory">*</span></label>
				<input type="date" name="check_in" id="check_in" required="" onchange="return checkInValid();" />
			</div>
			<div class="form-group">
				<label class="frm-head">Check Out Date <span class="mandatory">*</span></label>
				<input type="date" name="check_out" id="check_out" required="" onchange="return checkInValid();" />
			</div>
			<div class="form-group">
				<label class="frm-head">Animation Image (383x407px)</label>
				<div class="input_img_wrap">
					<span class="image-prew">
						<img class="header-img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0001_240920112601.jpeg" alt="">
						<a href="">Remove</a>
					</span>
					<input type="file" style="display: none;" name="hotel_animation_image" id="hotel_animation_image">
					<label class="file-label" for="hotel_animation_image">Choose file</label>
				</div>
			</div>
			<div class="form-group">
				<label class="frm-head">Slider Image (165X233px)</label>
				<div class="input_img_wrap">
					<span class="image-prew">
						<img class="header-img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0001_240920112601.jpeg" alt="">
						<a href="">Remove</a>
					</span>
					<input type="file" style="display: none;" name="hotel_image" id="hotel_image">
					<label class="file-label" for="hotel_imag">Choose file</label>
				</div>
			</div>
			<div class="form-group">
				<label class="frm-head">Background Image (795x595px)</label>
				<div class="input_img_wrap">
					<span class="image-prew">
						<img class="header-img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0001_240920112601.jpeg" alt="">
						<a href="">Remove</a>
					</span>
					<input type="file" style="display: none;" name="hotel_back_image" id="hotel_back_image">
					<label class="file-label" for="hotel_back_image">Choose file</label>
				</div>
			</div>
			<div class="form-group">
				<label class="frm-head">Accessories</label>
				<div class="multy_input_add">
					<div class="multy_input">
						<div class="multy_input_box">
							<span for="name">Name</span>
							<input type="text" name="accessories_name[]" />
						</div>
						<div class="multy_input_box">
							<span for="image">Icon</span>
							<div class="input_img_wrap">
								<span class="image-prew">
									<img class="header-img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0001_240920112601.jpeg" alt="">
									<a href="">Remove</a>
								</span>
								<input type="file" style="display: none;" name="accessories_icon[]" id="accessories_icon">
								<label class="file-label" for="accessories_icon">Choose file</label>
							</div>
						</div>
					</div>
					<div id="additionalFields" class="additionfield"></div>
				</div>

			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button type="button" id="addField" class="add-more">Add More Accessories</button>
				</div>
			</div>
			<div class="form-group">
				<label class="frm-head">Room Type</label>
				<div class="multy_input_add">
					<div class="multy_input">
						<div class="multy_input_box">
							<span for="name">Room Name</span>
							<input type="text" name="room_type[]" />
						</div>
						<div class="multy_input_box">
							<span for="image">Icon</span>
							<div class="input_img_wrap">
								<span class="image-prew">
									<img class="header-img" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HEADER_0001_240920112601.jpeg" alt="">
									<a href="">Remove</a>
								</span>
								<input type="file" style="display: none;" name="room_type_image[]" id="room_type_image">
								<label class="file-label" for="room_type_image">Choose file</label>
							</div>
						</div>
					</div>
					<div id="additionalRoomFields" class="additionfield"></div>
				</div>

			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button type="button" id="addRoom" class="add-more">Add More Accessories</button>
				</div>
			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" onclick="window.location.href='hotel_listing.php';" id="back">Back</button>
					<button type="submit" class="submit">Save</button>
				</div>
			</div>
			<table width="100%" class="tborder d-none">
				<tr>
					<td class="tcat" colspan="2" align="left">Add New Hotel</td>
				</tr>
				<tr>
					<td colspan="2" style="margin:0px; padding:0px;">
						<table width="100%">
							<tr class="thighlight">
								<td align="left" colspan="12">Hotel Details</td>
							</tr>
							<tr>
								<td width="20%" align="left" valign="top">Hotel Name <span class="mandatory">*</span></td>
								<td width="30%" valign="top">
									<input name="hotel_name_add" id="hotel_name_add" style="width:90%;" required="" />
								</td>
								<td width="20%" align="left" valign="top">Address</td>
								<td width="20%" valign="top" rowspan="4">

								</td>
							</tr>
							<tr>
								<td align="left" width="20%">Hotel Phone No</td>
								<td align="left" width="20%">
									<input type="number" name="hotel_phone_add" id="hotel_phone_add" style="width:90%;" required="" />
								</td>
								<td width="20%"></td>
								<td width="20%"></td>
							</tr>
							<!-- <tr>
							<td align="left" width="20%">Room Type</td>
							<td align="left" width="20%">
								<input type="text" name="room_type" id="room_type" style="width:90%;" required="" />
							</td>
							<td width="20%"></td>
							<td width="20%"></td>
						</tr> -->
							<tr>
								<td align="left" width="20%">Distance From Venue (Km)</td>
								<td align="left" width="20%">
									<input type="number" name="distance_from_venue_add" id="distance_from_venue_add" style="width:90%;" required="" />
								</td>
								<!-- <td align="left" width="20%">Seat Limit</td>
								<td align="left" width="20%">
									<input type="text" name="seat_limit" id="seat_limit" style="width:90%;" required="" />					
								</td> -->

							</tr>
							<tr>
								<td align="left">Check In Date</td>
								<td align="left">
									<input type="date" name="check_in" id="check_in" style="width:90%;" required="" onchange="return checkInValid();" />
								</td>
								<td></td>
							</tr>
							<tr>
								<td align="left">Check Out Date</td>
								<td align="left">
									<input type="date" name="check_out" id="check_out" style="width:90%;" required="" onchange="return checkInValid();" />
								</td>
								<td></td>
							</tr>
							<table>
								<tr>
									<td align="left">Animation Image (383x407px)</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Slider Image (165X233px)</td>
									<td align="left"></td>
								</tr>
								<tr>
									<td align="left">Background Image (795x595px)</td>
									<td align="left"></td>
								</tr>

							</table>


							<tr>
								<td colspan="5">
									<hr>
								</td>
							</tr>
							<tr>
								<td>Accessories</td>
								<td>
									<div style="display:flex;">
										<div style="padding:11px;">
											<label for="name">Name:</label>
											<input type="text" name="accessories_name[]" style="margin-left: 12px;" />
										</div>
										<div style="padding:11px;">
											<label for="image">Icon:</label>
											<input type="file" name="accessories_icon[]" style="margin-left: 12px;" />
										</div>
									</div>

									<div id="additionalFields"></div>

									<button type="button" id="addField">Add More Accessories</button>
								</td>
							</tr>
							<tr>
								<td colspan="5">
									<hr>
								</td>
							</tr>
							<tr>
								<td>Room Type</td>
								<td>
									<div style="display:flex;">
										<div style="padding:11px;">
											<label for="name">Name:</label>
											<input type="text" name="room_type[]" style="margin-left: 12px;" />
										</div>
										<div style="padding:11px;">
											<label for="image">Room Image:</label>
											<input type="file" name="room_type_image[]" style="margin-left: 12px;" />
										</div>
									</div>

									<div id="additionalRoomFields"></div>

									<button type="button" id="addRoom">Add More Room</button>
								</td>
							</tr>

						</table>

					</td>
				</tr>
				<tr>
					<td width="20%"></td>
					<td align="left">
						<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back" onClick="location.href='hotel_listing.php';" />
						&nbsp;
						<input type="submit" name="Save" id="Save" value="Save & Next" class="btn btn-small btn-blue">
					</td>
				</tr>
				<tr class="tfooter">
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
		</form>

	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			var maxFields = 10; // Set the maximum number of fields allowed
			var addButton = $('#addField');
			var wrapper = $('#additionalFields');
			var fieldHTML = '<div class="multy_input"><div class="multy_input_box"><span for=" name">Name</span><input type="text" name="accessories_name[]"/></div><div class="multy_input_box"><span for="image">Icon</span><div class="input_img_wrap"><input type="file" style="display: none;" name="accessories_icon[]"><label class="file-label">Choose file</label></div></div><a href="#" class="removeField"><i class="fas fa-times"></i></a></div>';

			var x = 1; // Initial field counter

			// Add new fields dynamically
			$(addButton).click(function(e) {
				e.preventDefault();
				if (x < maxFields) {
					x++;
					$(wrapper).append(fieldHTML);
				}
			});

			// Remove field
			$(wrapper).on('click', '.removeField', function(e) {
				e.preventDefault();
				$(this).parent('div').remove();
				x--;
			});

			// Submit form (you can handle form submission as per your requirement)


			var maxRoomFields = 10; // Set the maximum number of fields allowed
			var addRoomButton = $('#addRoom');
			var wrapperRoom = $('#additionalRoomFields');
			var fieldHTMLRoom = '<div class="multy_input"><div class="multy_input_box"><span for=" name">Room Name</span><input type="text" name="room_type[]"/></div><div class="multy_input_box"><span for="image">Icon</span><div class="input_img_wrap"><input type="file" style="display: none;" name="room_type_image[]"><label class="file-label">Choose file</label></div></div><a href="#" class="removeField"><i class="fas fa-times"></i></a></div>';

			var x = 1; // Initial field counter

			// Add new fields dynamically
			$(addRoomButton).click(function(e) {
				e.preventDefault();
				if (x < maxRoomFields) {
					x++;
					$(wrapperRoom).append(fieldHTMLRoom);
				}
			});

			// Remove field
			$(wrapperRoom).on('click', '.removeField', function(e) {
				e.preventDefault();
				$(this).parent('div').remove();
				x--;
			});
		});

		function checkInValid() {
			var check_in = $('#check_in').val();
			var checkout = $('#check_out').val();
			if (checkout != "") {
				if (check_in > checkout) {
					alert('Checkin date should be less than checkout date!!');
					$('#check_out').val('');
				} else {
					//return true;
				}
			}
		}
	</script>
<?php
}

function updateSeat($cfg, $mycms)
{
	$hotel_id               = addslashes(trim($_REQUEST['id']));
?>
	<h4 style="color:black">Update Hotel Seat Limit</h4>
	<form name="frmUpdate" id="frmUpdate" action="hotel_listing.process.php" method="post" enctype="multipart/form-data" onsubmit="return updateConfirmation();">
		<input type="hidden" name="act" id="act" value="updateSeat" />
		<input type="hidden" name="id" id="id" value="<?= $hotel_id ?>" />
		<table width="40%" class="tborder">
			<tr>
				<td width="20%" class="tcat" align="center">Dates</td>
				<td width="30%" class="tcat" align="center">Seat Limit</td>
			</tr>
			<?php
			$sql 		    	  =	array();
			$sql['QUERY'] = "SELECT *								
							        		  FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
							        		  WHERE hotel_id = ?
							        		    AND status != ?";
			$sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $hotel_id, 'TYP' => 's');
			$sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
			$result = $mycms->sql_select($sql);
			// $rowSeatLimit          = $result[0];
			foreach ($result as $key => $rowSeatLimit) {
			?>
				<input type="hidden" name="id[]" id="" value=<?= $rowSeatLimit['id'] ?>>

				<tr>
					<td align="center"><?= $rowSeatLimit['check_in_date'] ?></td>
					<td align="center"><input type="number" name="seat_limit[]" id="" value=<?= $rowSeatLimit['seat_limit'] ?>></td>
				</tr>
			<?php
			}
			?>

			<tr>
				<td width="20%"></td>
				<td align="left">
					<input name="back" type="button" class="btn btn-small btn-red" id="back" value="close" onClick="location.href='hotel_listing.php';" />
					&nbsp;
					<input type="submit" name="Save" id="Save" value="Set" class="btn btn-small btn-blue">
				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
	</form>
<?php
}

/**************************************************************/
/*                  EDIT HOTEL & ROOM FORM                    */
/**************************************************************/
function hotelEditFormLayout($cfg, $mycms)
{
	$hotelId             = addslashes(trim($_REQUEST['id']));

	$sqlFetchHotel			=	array();
	$sqlFetchHotel['QUERY']	=	"SELECT * 
									   FROM " . _DB_MASTER_HOTEL_ . "
									  WHERE `status` 		 !=  ? 
									    AND id 				 =   ?";

	$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');
	$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'id', 			'DATA' => $hotelId, 	'TYP' => 's');

	$resultFetchHotel    		 = $mycms->sql_select($sqlFetchHotel);
	$rowFetchHotel               = $resultFetchHotel[0];

	//To fetch checkin checkout date
	$sql 		    	  =	array();
	$sql['QUERY'] = "SELECT min(`check_in_date`) AS checkin								
							        		  FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . "
							        		  WHERE hotel_id = ?
							        		    AND status != ?";
	$sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $rowFetchHotel['id'], 'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
	$result = $mycms->sql_select($sql);
	$rowFetchcheckIn           = $result[0];

	$sql 		    	  =	array();
	$sql['QUERY'] = "SELECT max(`check_out_date`) AS checkout								
							        		  FROM " . _DB_ACCOMMODATION_CHECKOUT_DATE_ . "
							        		  WHERE hotel_id = ?
							        		    AND status !=?";

	$sql['PARAM'][]	=	array('FILD' => 'hotel_id', 	'DATA' => $rowFetchHotel['id'], 'TYP' => 's');
	$sql['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 				 'TYP' => 's');
	$result = $mycms->sql_select($sql);
	$rowFetchcheckOut           = $result[0];

	$hotel_image = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowFetchHotel['hotel_image'];
	$backgroundImage = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowFetchHotel['hotel_background_image'];
	$animationImage = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowFetchHotel['hotel_animation_image'];

	$sqlAcc = array();
	$sqlAcc['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $hotelId . "' AND status='A' AND purpose='aminity'";

	$queryAcc = $mycms->sql_select($sqlAcc, false);

	$sqlRoom = array();
	$sqlRoom['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $hotelId . "' AND status='A' AND purpose='room'";

	$queryRoom = $mycms->sql_select($sqlRoom, false);

	$sqlRoom = array();
	$sqlRoom['QUERY']    = "SELECT * FROM " . _DB_ACCOMMODATION_ACCESSORIES_ . "  WHERE `hotel_id` = '" . $hotelId . "' AND status='A' AND purpose='slider'";

	$querySlider = $mycms->sql_select($sqlRoom, false);

	//echo '<pre>'; print_r($queryAcc);
?>
	<form name="frmUpdate" id="frmUpdate" action="hotel_listing.process.php" method="post" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" id="act" value="update" />
		<input type="hidden" name="id" id="id" value="<?= $rowFetchHotel['id'] ?>" />
		<table width="100%" class="tborder">
			<tr>
				<td class="tcat" colspan="4" align="left">Update Hotel Details</td>
			</tr>
			<tr>
				<td colspan="4" style="margin:0px; padding:0px;">

					<table width="100%">
						<tr class="thighlight">
							<td align="left" colspan="4">Hotel Details</td>
						</tr>
						<tr>
							<td width="20%" align="left" valign="top">Hotel Name <span class="mandatory">*</span></td>
							<td width="30%" valign="top">
								<input name="hotel_name_update" id="hotel_name_update" style="width:90%;" value="<?= $rowFetchHotel['hotel_name'] ?>" required />
							</td>
							<td width="20%" align="left" valign="top">Address</td>
							<td width="30%" valign="top" rowspan="2">
								<textarea name="hotel_address_update" id="hotel_address_update" style="width:90%; height:90px;" required=""><?= $rowFetchHotel['hotel_address'] ?></textarea>
							</td>
						</tr>
						<tr>
							<td align="left">Hotel Phone No</td>
							<td align="left">
								<input name="hotel_phone_update" id="hotel_phone_update" style="width:90%;" value="<?= $rowFetchHotel['hotel_phone_no'] ?>" required />
							</td>
							<td></td>
						</tr>
						<!-- <tr>
							<td align="left">Room Type</td>
							<td align="left">
								<input name="room_type" id="room_type" style="width:90%;" value="<?= $rowFetchHotel['room_type'] ?>" required />
							</td>
							<td></td>
						</tr> -->
						<tr>
							<td align="left">Distance From Venue (Km)</td>
							<td align="left">
								<input name="distance_from_venue_update" id="distance_from_venue_update" style="width:90%;" value="<?= $rowFetchHotel['distance_from_venue'] ?>" required />
							</td>

							<td align="left">Check-in - check-out time</td>
							<td align="left">
								<input name="checkin_checkout_time_update" id="checkin_checkout_time_update" style="width:90%;" value="<?= $rowFetchHotel['checkin_checkout_time'] ?>" required />
							</td>
						</tr>
						<!-- <tr>
								<td align="left">Seat Limit</td>
								<td align="left">
									<input name="seat_limit" id="seat_limit" style="width:90%;" value="<?= $rowFetchHotel['seat_limit'] ?>" required/>					
								</td>
							</tr> -->

						<table>
							<tr>
								<td align="left">Animation Image (383x407px)</td>
								<td align="left"><img src="<?= $animationImage ?>" width="100"></td>
								<td align="left">
									<input type="file" name="hotel_animation_image" id="hotel_animation_image">
								</td>

							</tr>
							<tr>
								<?php
								$i = 1;
								foreach ($querySlider as $k => $row) {
									//echo '<pre>'; print_r($row);

									$icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];
								?>

							<tr>
								<td width="60px" align="left"><?= "Slider-" . $i ?></td>
								<td width="50px" align="left"><img src="<?= $icon ?>" width="100" height="100" style="background: bisque;"></td>
								<td width="70%">
									<div style="display:flex;">
										<div style="padding:11px;">

											<label for="image">Image:</label>

											<input type="file" name="slider_image[]" style="margin-left: 12px;" />
											<input type="hidden" name="slider_exist_icon[]" value="<?= $row['accessories_icon'] ?>">
										</div>
										<a style="margin-top:35px;" id="removeAcc" onclick="deleteAccessories('<?= $row['id'] ?>')">Remove</a>
									</div>

								</td>
							</tr>
						<?php
									$i++;
								}
						?>

						<!-- <td align="left">Slider Image (165X233px)</td>
								<td align="left"><img src="<?= $hotel_image ?>" width="100"></td>
								<td align="left">
									<input type="file" name="slider_image[]" id="hotel_image">
								</td> -->

			</tr>
			<tr>
				<td colspan="2">
					<button type="button" id="addSlider" class="btn btn-secondary">Add slider image</button>
				</td>
			</tr>
			<tr>

				<td align="left">
					<div id="additionalFieldsSlider"></div>
				</td>
			</tr>
			<tr>

			</tr>
			<tr>
				<td align="left">Background Image (795x595px)</td>
				<td align="left"><img src="<?= $backgroundImage ?>" width="100"></td>
				<td align="left">
					<input type="file" name="hotel_back_image" id="hotel_back_image">
				</td>
			</tr>
		</table>
		<table cellpadding="" width="90%">
			<tr>




				<?php
				if ($queryAcc) { ?>
					<td colspan="2" valign="top">
						<table width="40%">
							<tr>
								<td class="tcat" colspan="5" align="left" style="background:#f0a874">Aminity</td>
							</tr>
							<?php
							$i = 1;
							foreach ($queryAcc as $k => $row) {
								//echo '<pre>'; print_r($row);

								$icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];
							?>

								<tr>
									<td width="60px" align="left"><?= "Aminity-" . $i ?></td>
									<td width="50px" align="left"><img src="<?= $icon ?>" width="100" height="100" style="background: bisque;"></td>
									<td width="70%">
										<div style="display:flex;">
											<div style="padding:11px;">
												<label for="name">Name:</label>
												<input type="text" name="accessories_name[]" value="<?= $row['accessories_name'] ?>" style="margin-left: 12px;" />
											</div>

											<div style="padding:11px;">

												<label for="image">Icon:</label>

												<input type="file" name="accessories_icon[]" style="margin-left: 12px;" />
												<input type="hidden" name="accessories_exist_icon[]" value="<?= $row['accessories_icon'] ?>">
											</div>
											<a style="margin-top:35px;" id="removeAcc" onclick="deleteAccessories('<?= $row['id'] ?>')">Remove</a>
										</div>

									</td>
								</tr>
							<?php
								$i++;
							}
							?>
						</table>
					</td>
				<?php
				}

				if ($queryRoom) { ?>
					<td colspan="2" valign="top">
						<table width="40%">
							<tr>
								<td class="tcat" colspan="5" align="left" style="background:#f0a874">Room Type</td>
							</tr>
							<?php
							$i = 1;
							foreach ($queryRoom as $k => $row) {
								//echo '<pre>'; print_r($row);

								$icon = _BASE_URL_ . 'uploads/EMAIL.HEADER.FOOTER.IMAGE/' . $row['accessories_icon'];
							?>

								<tr>
									<td width="60px"><?= "Room-" . $i ?></td>
									<td><img src="<?= $icon ?>" width="100" height="100" style="background: bisque;"></td>
									<td>
										<div style="display:flex;">
											<div style="padding:11px;">
												<label for="name">Room Type:</label>
												<input type="text" name="room_type[]" value="<?= $row['accessories_name'] ?>" style="margin-left: 12px;" />
											</div>

											<div style="padding:11px;">

												<label for="image">Room Image:</label>

												<input type="file" name="room_type_image[]" style="margin-left: 12px;" />
												<input type="hidden" name="room_exist_icon[]" value="<?= $row['accessories_icon'] ?>">
											</div>
											<a style="margin-top:35px;" id="removeAcc" onclick="deleteAccessories('<?= $row['id'] ?>')">Remove</a>
										</div>

									</td>
								</tr>
							<?php
								$i++;
							} ?>
						</table>
					</td>
				<?php
				}
				?>

			</tr>
		</table>
		<tr>
			<td align="left">
				<div id="additionalFields"></div>
			</td>
			<td align="right">
				<div id="additionalRoom"></div>
			</td>


		</tr>
		<tr>
			<td><button type="button" id="addField" class="btn btn-info">Add Accessories</button></td>
			<td><button type="button" id="addRoom" class="btn btn-warning">Add Room Type</button></td>
		</tr>
		<tr><br></tr>
		<tr>



		</tr>
		<tr>

		</tr>
		</td>
		</tr>
		</table>

		</td>
		</tr>
		<tr>
			<td width="20%"></td>
			<td align="left">
				<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back" onClick="location.href='hotel_listing.php';" />
				&nbsp;
				<input type="submit" name="Save" id="Save" value="Update" class="btn btn-small btn-blue">
			</td>
		</tr>
		<tr class="tfooter">
			<td colspan="2">&nbsp;</td>
		</tr>
		</table>
	</form>
	<script type="text/javascript">
		$(document).ready(function() {

			// ***************************** ADD aminities *****************************************8
			var maxFields1 = 10; // Set the maximum number of fields allowed
			var addButton1 = $('#addField');
			var wrapper1 = $('#additionalFields');
			var fieldHTML1 = '<div style="display:flex;"><div style="padding:11px;"><label for="name">Name:</label><input type="text" name="accessories_name[]" style="margin-left: 12px;"/></div><div style="padding:11px;"><label for="image">Icon:</label><input type="file" name="accessories_icon[]" style="margin-left: 12px;"/></div><a style="margin-top:35px;" href="#" class="removeField">Remove</a></div>';

			var x = 1; // Initial field counter

			// Add new fields dynamically
			$(addButton1).click(function(e) {
				e.preventDefault();
				if (x < maxFields1) {
					x++;
					$(wrapper1).append(fieldHTML1);
				}
			});

			// Remove field
			$(wrapper).on('click', '.removeField', function(e) {
				e.preventDefault();
				$(this).parent('div').remove();
				x--;
			});

			// Submit form (you can handle form submission as per your requirement)


			// ************************ ADD Room Type *****************************8
			var maxFields2 = 10; // Set the maximum number of fields allowed
			var addButton2 = $('#addRoom');
			var wrapper2 = $('#additionalRoom');
			var fieldHTML2 = '<div style="display:flex;"><div style="padding:11px;"><label for="name">Room Type:</label><input type="text" name="room_type[]" style="margin-left: 12px;"/></div><div style="padding:11px;"><label for="image">Image:</label><input type="file" name="room_type_image[]" style="margin-left: 12px;"/></div><a style="margin-top:35px;" href="#" class="removeField">Remove</a></div>';

			var i = 1; // Initial field counter

			// Add new fields dynamically
			$(addButton2).click(function(e) {
				e.preventDefault();
				if (i < maxFields2) {
					i++;
					$(wrapper2).append(fieldHTML2);
				}
			});

			// Remove field
			$(wrapper2).on('click', '.removeField', function(e) {
				e.preventDefault();
				$(this).parent('div').remove();
				i--;
			});

			// ***************************** ADD SLider IMAGE *****************************************8
			var maxFields3 = 6; // Set the maximum number of fields allowed
			var addButton3 = $('#addSlider');
			var wrapper3 = $('#additionalFieldsSlider');
			var fieldHTML3 = '<div style="display:flex;"><div style="padding:11px;">Slider </div><div style="padding:11px;"><label >Image:</label><input type="file" name="slider_image[]" style="margin-left: 12px;"/></div><a style="margin-top:35px;" href="#" class="removeField">Remove</a></div>';

			var x = 1; // Initial field counter

			// Add new fields dynamically
			$(addButton3).click(function(e) {
				e.preventDefault();
				if (x < maxFields3) {
					x++;
					$(wrapper3).append(fieldHTML3);
				}
			});

			// Remove field
			$(wrapper3).on('click', '.removeField', function(e) {
				e.preventDefault();
				$(this).parent('div').remove();
				x--;
			});


		});

		function deleteAccessories(id) {

			if (id > 0) {

				if (confirm('Do you want to delete?')) {
					$.ajax({
						type: "POST",
						url: 'hotel_listing.process.php',
						data: 'action=deleteAccessories&id=' + id,
						//dataType: 'json',
						async: false,
						success: function(JSONObject) {
							console.log(JSONObject);
							window.location.reload();
						}
					});
				} else {

					//alert('Why did you press cancel? You should have confirmed');
				}

			}

		}
	</script>
<?php
}

/**************************************************************/
/*                  VIEW HOTEL & ROOM FORM                    */
/**************************************************************/
function hotelViewFormLayout($cfg, $mycms)
{
	$hotelId           			  = addslashes(trim($_REQUEST['id']));


	$sqlFetchHotel			=	array();
	$sqlFetchHotel['QUERY']	=	"SELECT * 
									   FROM " . _DB_MASTER_HOTEL_ . "
									  WHERE `status` 		!= 	 ? 
									    AND id 				 = ?";

	$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');
	$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'id', 			'DATA' => $hotelId, 	'TYP' => 's');

	$resultFetchHotel    		 = $mycms->sql_select($sqlFetchHotel);
	$rowFetchHotel               = $resultFetchHotel[0];
?>
	<table width="100%" class="tborder">
		<tr>
			<td class="tcat" colspan="2" align="left">View Hotel Details</td>
		</tr>
		<tr>
			<td colspan="2" style="margin:0px; padding:0px;">

				<table width="100%">
					<tr class="thighlight">
						<td align="left" colspan="4">Hotel Details</td>
					</tr>
					<tr>
						<td width="20%" align="left" valign="top">Hotel Name</td>
						<td width="30%" valign="top"><?= $rowFetchHotel['hotel_name'] ?></td>
						<td width="20%" align="left" valign="top">Address</td>
						<td width="30%" valign="top" rowspan="4"><?= nl2br($rowFetchHotel['hotel_address']) ?></td>
					</tr>
					<tr>
						<td align="left">Hotel Phone No</td>
						<td align="left"><?= $rowFetchHotel['hotel_phone_no'] ?></td>
						<td></td>
					</tr>

					<tr>
						<td align="left">Distance From Venue (Km)</td>
						<td align="left"><?= $rowFetchHotel['distance_from_venue'] ?></td>
						<td></td>
					</tr>
					<tr>
						<td align="left">Pickup Availability</td>
						<td align="left"><?= ucfirst(strtolower($rowFetchHotel['available_pickup'])) ?></td>
						<td align="left"></td>
					</tr>
					<tr>
						<td align="left">Pickup Complementary</td>
						<td align="left"><?= ucfirst(strtolower($rowFetchHotel['complementary_pickup'])) ?></td>
						<td align="left"></td>
					</tr>
				</table>

			</td>
		</tr>
		<tr>
			<td width="20%"></td>
			<td align="left">
				<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back" onClick="location.href='hotel_listing.php';" />
			</td>
		</tr>
		<tr class="tfooter">
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>
<?php
}
?>