<?php
include_once('includes/init.php');

page_header("Registration Tariff");

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
?>
<div class="body_content_box">
	<form class="con_box-grd row" name="frmSearch" id="frmSearch" action="registration_tariff.process.php" method="post">
		<input type="hidden" name="act" value="search_classification" />
		<div class="form-group">
			<h2>Registration Tariff</h2>
		</div>
		<div class="table_wrap">

			<?

			$sql['QUERY']	=	"SELECT cutoff.cutoff_title  
											   FROM " . _DB_TARIFF_CUTOFF_ . " cutoff
										      WHERE status = 'A'";
			$res = $mycms->sql_select($sql);

			$registrationDetails = getAllRegistrationTariffs();
			//echo'<pre>';print_r($registrationDetails);echo'</pre>';						
			?>
			<table width="100%">
				<thead>
					<tr class="theader">
						<th >Registration Classification</th>
						<?
						foreach ($res as $k => $title) {
						?>
							<th style="width: 200px;"><?= strip_tags($title['cutoff_title']) ?></th>
						<?
						}
						?>
						<th class="action">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ($registrationDetails) {
						foreach ($registrationDetails as $key => $registrationDetailsVal) {

					?>
							<tr class="tlisting">
								<td ><?= getRegClsfName($key) ?></td>
								<?
								foreach ($registrationDetailsVal as $keyCutoff => $rowCutoff) {
								?>
									<td><?= $rowCutoff['CURRENCY'] ?> <?= $rowCutoff['AMOUNT'] ?></td>
								<?php
								}
								?>
								<td class="action">
									<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="registration_tariff.php?show=edit&id=<?= $key ?>">
										<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
								</td>
							</tr>
						<?
						}
					} else {
						?>
						<tr>
							<td colspan="7" align="center">
								<span class="mandatory">No Record Present.</span>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<div class="bbp-pagination">
				<div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
				<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
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
	$regClasfId    		 = $_REQUEST['id'];

	$registrationDetails = getAllRegistrationTariffs();

	$regClasfDetails	 = $registrationDetails[$regClasfId];
?>
<div class="body_content_box">
	<form class="con_box-grd row" name="frmTariffEdit" id="frmTariffEdit" method="post" action="registration_tariff.process.php" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" id="act" value="update" />
		<input type="hidden" name="classification_id" id="classification_id" value="<?= $regClasfId ?>" />
		<div class="form-group">
			<h2>Update Tariff</h2>
		</div>
		<div class="form-group">
			<h4>
				Conference Registration - &nbsp;
			<?= getRegClsfName($regClasfId) ?></h4>
		</div>
		<?
		foreach ($regClasfDetails as $key => $valRegClasfDetails) {
			$title 		= getCutoffName($key);
			$cutoffId 	= $key;
		?>
			<div class="form-group">
				<label class="frm-head"><?= $title ?></label>
				<span style="color: #fff;padding-right: 10px; line-height: 54px; "><?= $valRegClasfDetails['CURRENCY'] ?></span>
				<input type="hidden" name="currency[<?= $cutoffId ?>]" id="currency_<?= $cutoffId ?>" value="<?= $valRegClasfDetails['CURRENCY'] ?>" />
				<input type="text" name="amount_for_cutoff[<?= $cutoffId ?>]" id="tariff_first_cutoff_id_<?= $cutoffId ?>"
					value="<?= ($valRegClasfDetails['AMOUNT'] != '') ? $valRegClasfDetails['AMOUNT'] : '0.00'; ?>" style="width:80%;" required />
			</div>
		<?
		}
		?>
		<div class="form-group">
			<div class="frm-btm-wrap">
				<button type="button" class="back" onclick="window.location.href='registration_tariff.php';" id="back">Back</button>
				<button class="submit" id="Save">Update Tariff</button>
			</div>
		</div>

		<!-- <tr>
			<td align="right"></td>
			<td align="left">
				<input name="back" type="button" class="btn btn-small btn-red" id="Back" value="Back"
					onClick="location.href='registration_tariff.php';" />
				&nbsp;
				<input type="submit" name="Save" id="Save" value="Update Tariff" class="btn btn-small btn-blue">
			</td>
		</tr> -->


	</form>
</div>
<?php
}
?>