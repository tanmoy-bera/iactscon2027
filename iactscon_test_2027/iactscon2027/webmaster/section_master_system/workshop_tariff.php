<?php
include_once('includes/init.php');
include_once('../../includes/function.workshop.php');
page_header("Workshop Tariff");

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

		// TARIFF CLASSIFICATION EDIT FORM LAYOUT
		case 'edit':

			tariffClassificationEditFormLayout($cfg, $mycms);
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

	$counter                 = 0;
	$searchCondition         = "";
	$workshopDetails 		 = getAllWorkshopTariffs();
	$sql_cal	=	array();
	$sql_cal['QUERY']		 =	"SELECT * 
									   FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " 
									  WHERE status != ? 
								   ORDER BY `sequence_by` ASC";
	$sql_cal['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'D',  'TYP' => 's');
	$res_cal = $mycms->sql_select($sql_cal);
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmSearch" id="frmSearch" action="registration_tariff.process.php" method="post">
			<input type="hidden" name="act" value="search_classification" />
			<div class="table_wrap">
				<div class="form-group">
					<h2>Workshop Registration</h2>
				</div>
				<table width="100%" class="tborder">
					<thead>
						<tr>
							<th rowspan="2">Workshop</th>

							<?
							$sql	=	array();
							$sql['QUERY'] = "SELECT cutoff.cutoff_title  
		  FROM " . _DB_WORKSHOP_CUTOFF_ . " cutoff
		 WHERE status = ?";
							$sql['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');
							$res = $mycms->sql_select($sql);
							foreach ($res as $key => $title) {
							?>
								<th align="right" colspan="2"><?= strip_tags($title['cutoff_title']) ?></th>

							<?
							}
							?>
							<th rowspan="2" class="action">Action</th>
						</tr>
						<tr>
							<?
							foreach ($res as $key => $title) {
							?>
								<th align="right">INR</th>
								<th align="right">USD</th>
							<?
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						if (sizeof($workshopDetails) > 0) {
							foreach ($workshopDetails as $keyWorkshopclsf => $rowWorkshopclsf) {
						?>
								<tr>
									<td align="left" colspan="10" class="thighlight"><?= getWorkshopName($keyWorkshopclsf) . ' (' . $mycms->cDate('d-m-y', getWorkshopDate($keyWorkshopclsf)) . ')' ?></td>
								<tr>
									<?
									foreach ($res_cal as $keyRCf => $rowsl) {
										if ($rowsl['type'] != 'ACCOMPANY') {
											$rowRegClasf = $rowWorkshopclsf[$rowsl['id']];
									?>
								<tr>
									<td align="left"><?= $rowsl['classification_title'] ?></td>
									<?
											foreach ($rowRegClasf as $key => $cutoffvalue) {
									?>
										<td align="right"><?= $cutoffvalue['INR'] ?></td>
										<td align="right"><?= $cutoffvalue['USD'] ?></td>
									<?
											}
									?>
									<td class="action">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="workshop_tariff.php?show=edit&workshopId=<?= $keyWorkshopclsf ?>&regClsfId=<?= $rowsl['id'] ?>">
											<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
									</td>
								<tr>
						<?
										}
									}
								}
							} else {
						?>
								<tr>
									<td colspan="8" align="center">
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
		</form>
	</div>
<?php
}

/**************************************************************/
/*                       EDIT TARIFF FORM                     */
/**************************************************************/
function tariffClassificationEditFormLayout($cfg, $mycms)
{
	$Workshopid                      = $_REQUEST['workshopId'];
	$Regclassfid                     = $_REQUEST['regClsfId'];

	$workshopClassTitle = getWorkshopName($Workshopid);
	$regisClassTitle    = getRegClsfName($Regclassfid);
	$workshopDetails    = getAllWorkshopTariffs();

	$editDetails		= $workshopDetails[$Workshopid][$Regclassfid];
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmTariffEdit" id="frmTariffEdit" method="post" action="workshop_tariff.process.php" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" id="act" value="update" />
			<input type="hidden" name="workshop_classification_id" id="workshop_classification_id" value="<?= $Workshopid ?>" />
			<input type="hidden" name="registration_classification_id" id="registration_classification_id" value="<?= $Regclassfid ?>" />
			<div class="form-group">
				<h2>Update Tariff</h2>
			</div>
			<div class="form-group">
				<label class="frm-head">Workshop Name</label>
				<input type="text"
					value="<?= $workshopClassTitle ?>" />
			</div>
			<div class="form-group">
				<label class="frm-head">User Type</label>
				<input type="text"
					value="<?= $regisClassTitle ?>" />
			</div>
			<?
			foreach ($editDetails as $key => $rowWorkshopHeaders) {
			?>
				<div class="form-group">
					<label class="frm-head"><?= getCutoffName($key) ?></label>
					<input type="text"
						value="<?= $regisClassTitle ?>" />
				</div>
				<div class="form-group">
					<label class="frm-head">INR</label>
					<input type="text" name="tariff_inr_cutoff_id_edit[<?= $key ?>]" id="tariff_inr_first_cutoff_id_edit_<?= $key ?>"
							value="<?= ($rowWorkshopHeaders['INR'] != '') ? $rowWorkshopHeaders['INR'] : '0.00' ?>" />
				</div>
				<div class="form-group">
					<label class="frm-head">USD</label>
					<input type="text" name="tariff_usd_cutoff_id_edit[<?= $key ?>]" id="tariff_usd_first_cutoff_id_edit_<?= $key ?>"
							value="<?= ($rowWorkshopHeaders['USD'] != '') ? $rowWorkshopHeaders['USD'] : '0.00' ?>" />
				</div>
			<?
			}
			?>

			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" onClick="location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>';" id="back">Back</button>
					<button type="submit" class="submit">Save</button>
				</div>
			</div>
			<!-- <table width="80%" class="tborder d-none">
				<tr>
					<td class="tcat" align="left">Update Tariff</td>
				</tr>
				<tr>
					<td align="center" style="margin:0px; padding:0px;">
						<table width="100%">
							<tr class="tlisting">
								<td width="50%" align="left" valign="top"><b>Workshop Name</b></td>
								<td align="left" valign="top" colspan="4"><b><?= $workshopClassTitle ?></b></td>
							</tr>
							<tr class="tlisting">
								<td width="50%" align="left" valign="top"><b>User Type</b></td>
								<td align="left" valign="top" colspan="4"><b><?= $regisClassTitle ?></b></td>
							</tr>
							<?
							foreach ($editDetails as $key => $rowWorkshopHeaders) {
							?>
								<input type="hidden" name="tariff_cutoff_id_edit[]" id="tariff_cutoff_id_edit_<?= $key ?>" value="<?= $key ?>" />
								<input type="hidden" name="currency[<?= $key ?>]" id="currency_<?= $key ?>" value="<?= $rowWorkshopHeaders['CURRENCY'] ?>" />
								<tr class="tlisting">
									<td align="left" valign="top"><?= getCutoffName($key) ?></td>

									<td align="right" valign="top">INR</td>
									<td align="left" valign="top">

										<input type="text" name="tariff_inr_cutoff_id_edit[<?= $key ?>]" id="tariff_inr_first_cutoff_id_edit_<?= $key ?>" style="text-align: right; width:50%;"
											value="<?= ($rowWorkshopHeaders['INR'] != '') ? $rowWorkshopHeaders['INR'] : '0.00' ?>" />
									</td>
									<td align="left" valign="top">USD</td>
									<td align="left" valign="top">
										<input type="text" name="tariff_usd_cutoff_id_edit[<?= $key ?>]" id="tariff_usd_first_cutoff_id_edit_<?= $key ?>" style="text-align: right; width:50%;"
											value="<?= ($rowWorkshopHeaders['USD'] != '') ? $rowWorkshopHeaders['USD'] : '0.00' ?>" />
									</td>
								</tr>
							<?
							}
							?>
							<tr>
								<td align="right"></td>
								<td align="left" colspan="2">
									<input name="back" type="button" class="btn btn-small btn-red" id="Back" value="Back"
										onClick="location.href='workshop_tariff.php';" />&nbsp;
									<input type="submit" name="Save" id="Save" value="Update Tariff" class="btn btn-small btn-blue">
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="tfooter">
					<td>&nbsp;</td>
				</tr>
			</table> -->
		</form>
	</div>
<?php
}




?>