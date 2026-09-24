<?php
include_once('includes/init.php');

page_header("Accompany Tariff");

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
		<form class="con_box-grd row" name="frmSearch" id="frmSearch" action="accompany_tariff.process.php" method="post">
			<input type="hidden" name="act" value="search_classification" />
			<div class="form-group">
				<h2>Accompany Tariff</h2>
			</div>
			<div class="table_wrap">
				<?

				$sql['QUERY']	=	"SELECT cutoff.cutoff_title  
				   FROM " . _DB_TARIFF_CUTOFF_ . " cutoff
				  WHERE status = 'A'";
				$res = $mycms->sql_select($sql);

				$registrationDetails = getAllAccompanyTariffs();
				//echo'<pre>';print_r($registrationDetails);echo'</pre>';						
				?>
				<table width="100%">
					<thead>
						<tr class="theader">
							<th>Accompany Classification</th>
							<?
							foreach ($res as $k => $title) {
							?>
								<th align="right" style="width: 200px;"><?= strip_tags($title['cutoff_title']) ?></th>
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
									<td><?= getACCClsfName($key) ?></td>
									<?
									foreach ($registrationDetailsVal as $keyCutoff => $rowCutoff) {
									?>
										<td align="right"><?= $rowCutoff['CURRENCY'] ?> <?= $rowCutoff['AMOUNT'] ?></td>
									<?php
									}
									?>
									<td class="action">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="accompany_tariff.php?show=edit&id=<?= $key ?>">
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

	$registrationDetails = getAllAccompanyTariffs();

	$regClasfDetails	 = $registrationDetails[$regClasfId];
?>
	<form name="frmTariffEdit" id="frmTariffEdit" method="post" action="accompany_tariff.process.php" onsubmit="return onSubmitAction();">
		<input type="hidden" name="act" id="act" value="update" />
		<input type="hidden" name="classification_id" id="classification_id" value="<?= $regClasfId ?>" />
		<table width="80%" class="tborder">
			<tr>
				<td class="tcat" align="left">Update Tariff</td>
			</tr>
			<tr>
				<td align="center" style="margin:0px; padding:0px;">

					<table width="100%">
						<tr>
							<td width="50%" align="left" valign="top">Conference Registration</td>
							<td align="left" valign="top"><?= getRegClsfName($regClasfId) ?></td>
						</tr>
						<?
						foreach ($regClasfDetails as $key => $valRegClasfDetails) {
							$title 		= getCutoffName($key);
							$cutoffId 	= $key;
						?>
							<tr>
								<td align="left" valign="top"><?= $title ?></td>
								<td align="left" valign="top">
									<?= $valRegClasfDetails['CURRENCY'] ?> &nbsp;&nbsp;
									<input type="hidden" name="currency[<?= $cutoffId ?>]" id="currency_<?= $cutoffId ?>" value="<?= $valRegClasfDetails['CURRENCY'] ?>" />
									<input type="text" name="tariff_cutoff_id[<?= $cutoffId ?>]" id="tariff_first_cutoff_id_<?= $cutoffId ?>"
										value="<?= ($valRegClasfDetails['AMOUNT'] != '') ? $valRegClasfDetails['AMOUNT'] : '0.00'; ?>" style="width:80%;" required />
								</td>
							</tr>
						<?
						}
						?>
						<tr>
							<td align="right"></td>
							<td align="left">
								<input name="back" type="button" class="btn btn-small btn-red" id="Back" value="Back"
									onClick="location.href='accompany_tariff.php';" />
								&nbsp;
								<input type="submit" name="Save" id="Save" value="Update Tariff" class="btn btn-small btn-blue">
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="tfooter">
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
<?php
}
?>