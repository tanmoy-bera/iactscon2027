<?php
include_once('includes/init.php');

page_header("Workshop");

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

	<?php
	switch ($show) {
		// show add window
		case 'add':
			addWorkshop($cfg, $mycms);
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
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmSearch" id="frmSearch" action="registration_tariff.process.php" method="post">
			<input type="hidden" name="act" value="search_classification" />
			<div class="form-group">
				<h2>Manage Workshop</h2>
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
							<th class="action">Sl No.</th>
							<th>Workshop</th>
							<th>Seat limit</th>
							<th>Venue Address</th>
							<th>Workshop Date</th>
							<th class="action">Status</th>
							<th class="action">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php

						$sql_cal['QUERY']	=	"SELECT * 
												   FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
												   WHERE status != 'D' 
												ORDER BY `display` ASC";
						$res_cal = $mycms->sql_select($sql_cal);
						$i = 1;
						if ($res_cal) {
							foreach ($res_cal as $key => $rowsl) {
						?>
								<tr class="tlisting" style=" <?= ($rowsl['display'] == 'N') ? 'background:bisque;' : '' ?>">
									<td class="action"><?= $i ?></td>
									<td><?= $rowsl['classification_title'] ?> - <?= $rowsl['type'] ?></td>
									<td><?= $rowsl['seat_limit'] ?></td>
									<td><?= $rowsl['venue'] ?></td>
									<td><?= displayDateFormat($rowsl['workshop_date']) ?></td>

									<td class="action">

										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_workshop.process.php?act=<?= ($rowsl['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>"
											class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>

									</td>
									<td class="action">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="manage_workshop.php?show=edit&id=<?= $rowsl['id'] ?>">
											<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $cfg['SECTION_BASE_URL'] ?>manage_workshop.process.php?act=delete&id=<?= $rowsl['id'] ?>">
											<span src="https://ruedakolkata.com/isnezcon2024/images/skyCanvas/drop.png" title="Delete" class="icon-trash-stroke" onclick="return confirm('Do you really want to remove this workshop');"></span></a>
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
			</div>

			<a title="Add Workshop" class="stick_add" href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>"><i class="fas fa-plus"></i></a>

		</form>
	</div>
<?php
}

/**************************************************************/
/*                       EDIT TARIFF FORM                     */
/**************************************************************/
function editWorkshop($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_workshop.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="update" />
			<input type="hidden" name="workshop_id" id="workshop_id" value="<?= $_REQUEST['id'] ?>" />
			<?php
			$sql_cal['QUERY']	=	"SELECT * 
									   FROM " . _DB_WORKSHOP_CLASSIFICATION_ . " 
									  WHERE `id` = " . $_REQUEST['id'] . "";
			$res_cal = $mycms->sql_select($sql_cal);
			$row    = $res_cal[0];
			//echo $row;
			?>
			<div class="form-group">
				<h2>Edit Workshop</h2>
			</div>
			<div class="form-group">
				<label class="frm-head">Workshop <span class="mandatory">*</span></label>
				<input type="text" name="workshop_add" id="workshop_add"
					class="validate[required]" value="<?= $row['classification_title'] ?>" required />
			</div>
			<div class="form-group">
				<label class="frm-head">Workshop Type<span class="mandatory">*</span></label>
				<select name="workshop_type" required="">
					<option <? if ($row['type'] == 'MASTER CLASS') {
								echo "selected";
							} ?> value="MASTER CLASS">MASTER CLASS</option>
					<option <? if ($row['type'] == 'WORKSHOP') {
								echo "selected";
							} ?> value="WORKSHOP">WORKSHOP</option>
					<option <? if ($row['type'] == 'NORMAL') {
								echo "selected";
							} ?> value="NORMAL">NORMAL</option>
					<option <? if ($row['type'] == 'BREAKFAST') {
								echo "selected";
							} ?> value="BREAKFAST">BREAKFAST</option>
					<option <? if ($row['type'] == 'CADAVER') {
								echo "selected";
							} ?> value="CADAVER">CADAVER</option>
				</select>
			</div>
			<div class="form-group">
				<label class="frm-head">Venue <span class="mandatory">*</span></label>
				<?php
				$sql 	=	array();
				$sql['QUERY'] = "SELECT * FROM " . _DB_ICON_SETTING_ . " 
																	WHERE `id`!='' AND status='A'";
				//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
				$result 	 = $mycms->sql_select($sql);
				?>
				<select name="icon_id" required="">
					<option value="" selected>Select Icon</option>
					<?php
					foreach ($result as $k => $data) {
					?>
						<option value="<?= $data['id'] ?>" <?= ($data['id'] == $row['icon_id']) ? 'selected' : ''; ?>><?= $data['title'] ?></option>
					<?php
					}
					?>

				</select>
			</div>
			<div class="form-group">
				<label class="frm-head">Workshop <span class="mandatory">*</span></label>
				<input type="text" name="venue" id="venue"
					value="<?= $row['venue'] ?>" required />
			</div>
			<div class="form-group">
				<label class="frm-head">Seat limit <span class="mandatory">*</span></label>
				<input type="text" name="seat_limit_add" id="seat_limit_add"
					class="validate[required]" onblur="countryAvailabilityAdd(this.value)" value="<?= $row['seat_limit'] ?>" required />
			</div>
			<div class="form-group">
				<label class="frm-head">Workshop Date <span class="mandatory">*</span></label>
				<input type="date" name="workshop_date_add" id="workshop_date_add" value="<?= $row['workshop_date'] ?>" rel="splDate" required />
			</div>
			<div class="form-group">
				<label class="frm-head">Workshop Display<span class="mandatory">*</span></label>
				<select name="workshop_display" required="">
					<option value="">--select display--</option>
					<option <? if ($row['display'] == 'Y') {
								echo "selected";
							} ?> value="Y">YES</option>
					<option <? if ($row['display'] == 'N') {
								echo "selected";
							} ?> value="N">NO</option>
				</select>
			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>';" id="back">Back</button>
					<button type="submit" class="submit">Save</button>
				</div>
			</div>

		</form>
	</div>
<?php
}


/*================= ADD WORKHOP WINDOW ===============*/
function addWorkshop($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_workshop.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="insert" />
			<input type="hidden" name="chk_country_add" id="chk_country_add" value="0" />
			<?php
			foreach ($searchArray as $key => $val) {
			?>
				<input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
			<?php
			}
			?>
			<div class="form-group">
				<h2>Add Workshop</h2>
			</div>

			<div class="form-group">
				<label class="frm-head">
					Workshop <span class="mandatory">*</span>
				</label>
				<input type="text" name="workshop_add" id="workshop_add"
					class="validate[required]" required="" />
			</div>


			<div class="form-group">
				<label class="frm-head">
					Workshop Type<span class="mandatory">*</span>
				</label>
				<select name="workshop_type" required="">
					<option value="">--Select Type--</option>
					<option value="MASTER CLASS">MASTER CLASS</option>
					<option value="WORKSHOP">WORKSHOP</option>
					<option value="NORMAL">NORMAL</option>
					<option value="BREAKFAST">BREAKFAST</option>
					<option value="CADAVER">CADAVER</option>
				</select>
			</div>
			<div class="form-group">
				<label class="frm-head">
					Venue <span class="mandatory">*</span>
				</label>
				<input type="text" name="venue" id="venue"
					required="" />
			</div>
			<div class="form-group">
				<label class="frm-head">
					Seat limit <span class="mandatory">*</span>
				</label>
				<input type="text" name="seat_limit_add" id="seat_limit_add"
					class="validate[required]" onblur="countryAvailabilityAdd(this.value)" required="" />
			</div>
			<div class="form-group">
				<label class="frm-head">
					Workshop Date
				</label>
				<input type="date" name="workshop_date_add" id="workshop_date_add" rel="splDate" required="" />
			</div>
			<div class="form-group">
				<label class="frm-head">
					Workshop Display<span class="mandatory">*</span>
				</label>
				<select name="workshop_display" required="">
					<option value="">--Select Display--</option>
					<option value="Y">YES</option>
					<option value="N">NO</option>
				</select>
			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>'" type="button" class="back" id="BackAdd">Back</button>
					<button class="submit">Save</button>
				</div>
			</div>


		</form>
	</div>
<?php
}
?>