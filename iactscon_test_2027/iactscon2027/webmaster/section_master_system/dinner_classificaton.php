<?php
include_once('includes/init.php');
page_header("Dinner Classification");
?>
<script type="text/javascript" language="javascript" src="scripts/hotel_listing.js" />
</script>
<div class="body_wrap">
	<?php
	switch ($show) {

		// HOTEL ADD FORM LAYOUT
		case 'add':

			dinnereClassification($cfg, $mycms);
			break;

		// HOTEL EDIT FORM LAYOUT
		case 'edit':

			dinnerEditFormLayout($cfg, $mycms);
			break;

		// HOTEL LISTING LAYOUT
		default:

			dinnerListingLayout($cfg, $mycms);
			break;
	}
	?>
</div>
<?php

page_footer();

/**************************************************************/
/*                       HOTEL & ROOM LISTING                 */
/**************************************************************/
function dinnerListingLayout($cfg, $mycms)
{
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmSearch" method="post" action="dinner_classificaton.process.php" onSubmit="return FormValidator.validate(this);">
			<input type="hidden" name="act" value="search_hotel" />
			<div class="form-group">
				<h2>Dinner Listing</h2>
			</div>
			<div class="table_wrap">
				<table width="100%" class="tborder" align="center">
					<?
					$sqlFetchDinner				=	array();
					$sqlFetchDinner['QUERY']    = "SELECT * 
														      FROM " . _DB_DINNER_CLASSIFICATION_ . "
														     WHERE `status` != ?";
					$sqlFetchDinner['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');
					$resultFetchDinner    		= $mycms->sql_select($sqlFetchDinner);
					$dinnerCounter	=	1;
					if ($resultFetchDinner) {
					?>
						<thead>
							<tr class="theader">
								<th class="action">Sl No</th>
								<th align="left">Dinner Name</th>
								<!-- <td width="120" align="center">Service type</td>-->
								<th align="center">Date</th>
								<th class="action">Status</th>
								<!-- <td width="60" align="center">Action</td> -->
							</tr>
						</thead>
						<tbody>
							<? foreach ($resultFetchDinner as $key => $rowFetchDinner) { ?>

								<tr class="tlisting">
									<td align="center" valign="top"><?= $dinnerCounter ?></td>
									<td align="left" valign="top"><?= $rowFetchDinner['dinner_classification_name'] ?></td>
									<!-- <td align="center" valign="top"><?= $rowFetchDinner['service_type'] ?></td>-->
									<td align="center" valign="top"><?= $rowFetchDinner['date'] ?></td>
									<td align="center" valign="top">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="dinner_classificaton.php?show=edit&id=<?= $rowFetchDinner['id'] ?>">
											<span alt="Edit" title="Edit Hotel" class="icon-pen"></span></a>
										<a href="dinner_classificaton.process.php?act=Remove&id=<?= $rowFetchDinner['id'] ?>">
											<span alt="Remove" title="Remove Hotel" class="icon-x-alt" onclick="return confirm('Do You Really Want To Remove The Hotel ?');" /></a>
									</td>
								</tr>
							<?
								$dinnerCounter++;
							}
						} else {
							?>
							<tr>
								<td colspan="13" align="center">
									<span class="mandatory">No Record Present.</span>
								</td>
							</tr>
						<?
						}

						?>
						</tbody>
				</table>
				<div class="bbp-pagination">
					<div class="bbp-pagination-count"><?= $mycms->paginateRecInfo($indexVal) ?></div>
					<span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
				</div>
			</div>
			<a title="add" class="stick_add" href="dinner_classificaton.php?show=add"><i class="fas fa-plus"></i></a>
		</form>
	</div>
<?php
}

/**************************************************************/
/*                       ADD HOTEL & ROOM FORM                */
/**************************************************************/
function dinnereClassification($cfg, $mycms)
{
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmInsert" id="frmInsert" action="dinner_classificaton.process.php" method="post" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="insert" />
			<div class="form-group">
				<h2>Add New Dinner</h2>
			</div>
			<div class="form-group">
				<label class="frm-head">Dinner Classification Name</label>
				<input type="text" name="dinner_class_name" id="dinner_class_name" required="" onchange="return checkInValid();" />
			</div>
			<div class="form-group">
				<label class="frm-head">Dinner Date</label>
				<input type="date" name="dinner_date" id="dinner_date" required value="<?= $rowFetchDinner['date'] ?>">
			</div>
			<div class="form-group">
				<label class="frm-head">Hotel Name</label>
				<input type="text" name="dinner_hotel_name" id="dinner_hotel_name" required="" onchange="return checkInValid();" />
			</div>
			<div class="form-group">
				<label class="frm-head">Link</label>
				<input type="text" name="link" id="link" required="" onchange="return checkInValid();" />
			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" onClick="location.href='dinner_classificaton.php';" id="back">Back</button>
					<button type="submit" class="submit">Save</button>
				</div>
			</div>
		</form>
	</div>
<?php
}

/**************************************************************/
/*                  EDIT HOTEL & ROOM FORM                    */
/**************************************************************/
function dinnerEditFormLayout($cfg, $mycms)
{
	$diinerlId             		  = addslashes(trim($_REQUEST['id']));

	$sqlFetchDinner 		    	  =	array();
	$sqlFetchDinner['QUERY']    = "SELECT * 
									      FROM " . _DB_DINNER_CLASSIFICATION_ . "
									     WHERE `status` != ?
										   AND `id` = ? ";
	$sqlFetchDinner['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 			'TYP' => 's');
	$sqlFetchDinner['PARAM'][]	=	array('FILD' => 'id', 		    'DATA' => $diinerlId, 		'TYP' => 's');
	$resultFetchDinner   	    = $mycms->sql_select($sqlFetchDinner);
	$rowFetchDinner     		= $resultFetchDinner[0];
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmUpdate" id="frmUpdate" action="dinner_classificaton.process.php" method="post" enctype="multipart/form-data" onsubmit="return updateConfirmation();">
			<input type="hidden" name="act" id="act" value="update" />
			<input type="hidden" name="id" id="id" value="<?= $rowFetchDinner['id'] ?>" />
			<div class="form-group">
				<h2>Update Dinner Details</h2>
			</div>
			<div class="form-group"><label class="frm-head">Dinner Classification Name</label>
				<input type="text" name="dinner_class_name" id="dinner_class_name" required="" onchange="return checkInValid();" value="<?= $rowFetchDinner['dinner_classification_name'] ?>" />
			</div>
			<div class="form-group"><label class="frm-head">>Dinner Date</label>
				<input type="date" name="dinner_date" id="dinner_date" required value="<?= $rowFetchDinner['date'] ?>">
			</div>
			<div class="form-group"><label class="frm-head">Dinner Hotel Name</label>
				<input type="text" name="dinner_hotel_name" id="dinner_hotel_name" required="" onchange="return checkInValid();" value="<?= $rowFetchDinner['dinner_hotel_name'] ?>" />
			</div>
			<div class="form-group"><label class="frm-head">Link</label>
				<input type="text" name="link" id="link" required="" onchange="return checkInValid();" value="<?= $rowFetchDinner['link'] ?>" />
			</div>
			<!-- <tr class="thighlight">
								<td align="left">Service Type</td>
								<td align="left">
									<input type="text" name="service_type" id="service_type"  required="" onchange="return checkInValid();"  value="<?= $rowFetchDinner['service_type'] ?>"/>					
								</td>
								<td></td>
							</tr>
							<tr class="thighlight">
								<td align="left">Date</td>
								<td align="left">
									<input type="date" name="dinner_date" id="dinner_date"  required="" onchange="return checkInValid();"  value="<?= $rowFetchDinner['date'] ?>"/>					
								</td>
								<td></td>
							</tr> -->

			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" type="button" id="back" onclick="location.href='dinner_classificaton.php';">Back</button>
					<button class="submit" id="Save">Update</button>
				</div>
			</div>

		</form>
	</div>
<?php
}
?>
<script>
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