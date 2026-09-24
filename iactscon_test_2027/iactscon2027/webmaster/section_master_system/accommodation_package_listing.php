<?php
include_once('includes/init.php');
page_header("Accommodation Package");

?>
<script language="javascript" src="scripts/accommodation_package_listing.js"></script>
<div class="body_wrap">
	<?php
	switch ($show) {

		// ACCOMMODATION PACKAGE ADD WINDOW
		case 'add':

			accommodationPackageAddWindow($cfg, $mycms);
			break;

		// ACCOMMODATION PACKAGE EDIT WINDOW
		case 'edit':

			accommodationPackageEditWindow($cfg, $mycms);
			break;

		// SHOW ALL ACCOMMODATION PACKAGE WINDOW
		default:

			showAllAccommodationPackageWindow($cfg, $mycms);
			break;
	}
	?>
</div>
<?php

page_footer();

/************************************************************************/
/*                  SHOW ALL ACCOMMODATION PACKAGE WINDOW               */
/************************************************************************/
function showAllAccommodationPackageWindow($cfg, $mycms)
{
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmSearch" method="post" action="accommodation_package_listing.php">
			<div class="form-group">
				<h2>Accommodation Package</h2>
			</div>
			<div class="table_wrap">
				<table width="100%" class="tborder" align="center">
					<thead>
						<tr>
							<th class="action">Sl No</th>
							<th>Package Title</th>
							<th class="action">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$counter                = 0;

						$sqlHotel['QUERY']			    = "SELECT package.*,
																   hotel.hotel_name
															  FROM " . _DB_ACCOMMODATION_PACKAGE_ . " package 
															  
														INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
																ON package.hotel_id = hotel.id
															 WHERE hotel.status = 'A'";

						$fetchhotelName			= $mycms->sql_select($sqlHotel);
						$rowfetch				= $fetchhotelName[0];
						if ($fetchhotelName) {
							foreach ($fetchhotelName as $key => $rowsval) {


								$counter = $counter + 1;
						?>
								<tr class="tlisting">
									<td class="action"><?= $counter ?></td>
									<td><?= $rowsval['hotel_name'] . ", " . $rowsval['package_name'] ?></td>
									<td class="action">

										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="accommodation_package_listing.php?show=edit&id=<?= $rowsval['id'] ?>">
											<span alt="Edit" title="Edit Record" class="icon-pen" /></a>

									</td>
								</tr>
							<?php

							}
						} else {
							?>
							<tr>
								<td colspan="3" align="center">
									<span class="mandatory">No Record Present.</span>
								</td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>
				<div class="bbp-pagination">
					<div class="bbp-pagination-count"><?= $mycms->paginateRecInfo($indexVal) ?></div>
					<span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
				</div>
			</div>
			<a title="add" class="stick_add" href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="accommodation_package_listing.php?show=add"><i class="fas fa-plus"></i></a>
		</form>
	</div>
<?php
}

/************************************************************************/
/*                  ACCOMMODATION PACKAGE ADD WINDOW                    */
/************************************************************************/
function accommodationPackageAddWindow($cfg, $mycms)
{
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmInsert" id="frmInsert" action="accommodation_package_listing.process.php" method="post" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" id="act" value="insert" />
			<div class="form-group">
				<h2>Create Accommodation Package</h2>
			</div>
			<div class="form-group">
				<label class="frm-head">Select Hotel <span class="mandatory">*</span></label>
				<select name="hotel_id_add" id="hotel_id_add" style="width:90%;">
					<option value="">-- Select Hotel --</option>
					<?php
					$hotelCounter         = 0;

					$sqlFetchHotel			=	array();
					$sqlFetchHotel['QUERY']	=	"SELECT * 
																	   FROM " . _DB_MASTER_HOTEL_ . "
																	  WHERE `status` 		!= 	 ? " . $searchCondition;

					$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');
					$resultFetchHotel     = $mycms->sql_select($sqlFetchHotel);
					if ($resultFetchHotel) {
						foreach ($resultFetchHotel as $keyHotel => $rowFetchHotel) {
					?>
							<option value="<?= $rowFetchHotel['id'] ?>"><?= $rowFetchHotel['hotel_name'] ?></option>
					<?php
						}
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label class="frm-head">Package Name <span class="mandatory">*</span></label>
				<input type="text" name="package_name" id="package_name" value="" />
			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" onClick="location.href='accommodation_package_listing.php';" id="back">Back</button>
					<button type="submit" class="submit">Save</button>
				</div>
			</div>
			
		</form>
	</div>
<?php
}

/************************************************************************/
/*                 ACCOMMODATION PACKAGE EDIT WINDOW                    */
/************************************************************************/
function accommodationPackageEditWindow($cfg, $mycms)
{
	$id                 = addslashes(trim($_REQUEST['id']));

	$sqlHotel	=	array();
	$sqlHotel['QUERY']			    = "SELECT package.*,
											   hotel.hotel_name
											   
										  FROM " . _DB_ACCOMMODATION_PACKAGE_ . " package 
										  
									INNER JOIN " . _DB_MASTER_HOTEL_ . " hotel
											ON package.hotel_id = hotel.id
										 WHERE hotel.status = ?
										   AND package.id = ? ";
	$sqlHotel['PARAM'][]	=	array('FILD' => 'hotel.status', 		'DATA' => 'A', 		'TYP' => 's');
	$sqlHotel['PARAM'][]	=	array('FILD' => 'package.id', 			'DATA' => $id, 		'TYP' => 's');

	$fetchhotelName			= $mycms->sql_select($sqlHotel);
	$rowfetch				= $fetchhotelName[0];
?>
	<form name="frmUpdate" id="frmUpdate" action="accommodation_package_listing.process.php" method="post" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" id="act" value="update" />
		<input type="hidden" name="package_id" id="package_id" value="<?= $rowfetch['id'] ?>" />
		<table width="60%" class="tborder">
			<tr>
				<td class="tcat" colspan="2" align="left">Update Existing Package</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">

					<table width="100%">
						<tr>
							<td width="25%" align="left">Select Hotel <span class="mandatory">*</span></td>
							<td align="left">
								<select name="hotel_id_update" id="hotel_id_update" style="width:90%;">
									<option value="">-- Select Hotel --</option>
									<?php
									$sqlFetchHotel			=	array();
									$sqlFetchHotel['QUERY']	=	"SELECT * 
																	   FROM " . _DB_MASTER_HOTEL_ . "
																	  WHERE `status` 		!= 	 ? " . $searchCondition;

									$sqlFetchHotel['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');
									$resultFetchHotel     = $mycms->sql_select($sqlFetchHotel);
									if ($resultFetchHotel) {
										foreach ($resultFetchHotel as $keyHotel => $rowFetchHotel) {
									?>
											<option value="<?= $rowFetchHotel['id'] ?>" <?= ($rowFetchHotel['id'] == $rowfetch['hotel_id']) ? 'selected="selected"' : '' ?>><?= $rowFetchHotel['hotel_name'] ?></option>
									<?php
										}
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="left">Package Name <span class="mandatory">*</span></td>
							<td align="left">
								<input type="text" name="package_name" id="package_name" value="<?= $rowfetch['package_name'] ?>" />
							</td>
						</tr>
					</table>

				</td>
			</tr>
			<tr>
				<td width="25%"></td>
				<td align="left">

					<input name="back" type="button" class="btn btn-small btn-red" id="back" value="Back"
						onClick="location.href='accommodation_package_listing.php';" />
					&nbsp;
					<input type="submit" name="Save" id="Save" value="Update" class="btn btn-small btn-blue">

				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
	</form>
<?php
}
?>