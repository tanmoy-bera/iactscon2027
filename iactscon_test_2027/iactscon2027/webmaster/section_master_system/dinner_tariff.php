<?php
include_once('includes/init.php');
include_once('../../includes/function.workshop.php');
page_header("Dinner Tariff");

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
	$dataArray = array();
	$counter                 = 0;
	$searchCondition         = "";
	$workshopDetails 		 = getAllWorkshopTariffs();
	$sql_cal	=	array();
	$sql_cal['QUERY']		 =	"SELECT * 
									   FROM " . _DB_DINNER_CLASSIFICATION_ . " 
									  WHERE status != ? 
								   ORDER BY `id` ASC";
	$sql_cal['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'D',  'TYP' => 's');
	$res_cal = $mycms->sql_select($sql_cal);
	foreach ($res_cal as $key => $rowDinner) {
		$dataArray[$counter]['dinner_classification_name'] = $rowDinner['dinner_classification_name'];
		$dataArray[$counter]['classi_id'] = $rowDinner['id'];
		$dataArray[$counter]['service_type'] = $rowDinner['service_type'];

		//query in cutoff table
		$sqlcutoff	=	array();
		$sqlcutoff['QUERY'] = "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " cutof WHERE status = ?";
		$sqlcutoff['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
		$resCutoff = $mycms->sql_select($sqlcutoff);
		foreach ($resCutoff as $keyCutoff => $rowCutoff) {
			$dataArray[$counter]['CUTOFF'][$rowCutoff['id']]['TITLE'] = $rowCutoff['cutoff_title'];
			$dataArray[$counter]['CUTOFF'][$rowCutoff['id']]['cutoffId'] = $rowCutoff['id'];

			$sqlPackageCheckoutDate	=	array();
			// query in tariff table
			$sqlPackageCheckoutDate['QUERY'] = "select * 
													  FROM " . _DB_DINNER_TARIFF_ . " accomodation
													 WHERE status = ?
													   AND cutoff_id = ?
													   AND dinner_classification_id = ?
													   AND status = ?";

			$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 					 'DATA' => 'A', 					'TYP' => 's');
			$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'cutoff_id',				 'DATA' => $rowCutoff['id'], 		'TYP' => 's');
			$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'dinner_classification_id', 'DATA' => $rowDinner['id'], 		'TYP' => 's');
			$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 					 'DATA' => 'A', 		'TYP' => 's');
			$resPackageCheckoutDate = $mycms->sql_select($sqlPackageCheckoutDate);
			foreach ($resPackageCheckoutDate as $keyPrice => $price) {
				$dataArray[$counter]['CUTOFF'][$rowCutoff['id']]['INR'] = $price['inr_amount'];
				$dataArray[$counter]['CUTOFF'][$rowCutoff['id']]['USD'] = $price['usd_amount'];
			}
		}
		$counter++;
		//echo '<pre>';print_r($dataArray);die();
	}
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmSearch" id="frmSearch" action="dinner_tariff.process.php" method="post">
			<input type="hidden" name="act" value="search_classification" />
			<div class="form-group">
				<h2>Dinner Registration</h2>
			</div>
			<div class="table_wrap">
				<table width="100%" class="tborder">
					<thead>
						<tr class="theader">

							<th rowspan="2" align="center">Dinner</th>

							<?
							$sql	=	array();
							$sql['QUERY'] = "SELECT cutoff.cutoff_title  
		  FROM " . _DB_TARIFF_CUTOFF_ . " cutoff
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
						<tr class="theader">
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
						if (sizeof($res_cal) > 0) {
							foreach ($dataArray as $key => $AccomodationArray) {
						?>
								<tr>
									<td align="left"><?= $AccomodationArray['dinner_classification_name']; ?></td>
									<?
									$cutoffPriceArray = $AccomodationArray['CUTOFF'];
									foreach ($resCutoff as $key => $cutoffvalue) {
									?>
										<td align="right"><?= isset($cutoffPriceArray[$cutoffvalue['id']]['INR']) ? number_format($cutoffPriceArray[$cutoffvalue['id']]['INR'], 2) : '0.00' ?></td>
										<td align="right"><?= isset($cutoffPriceArray[$cutoffvalue['id']]['USD']) ? number_format($cutoffPriceArray[$cutoffvalue['id']]['USD'], 2) : '0.00' ?></td>
									<?
									}
									?>
									<td class="action">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="dinner_tariff.php?show=edit&classi_Id=<?= $AccomodationArray['classi_id'] ?>&regClsfId=<?= $cutoffvalue['id'] ?>">
											<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
									</td>
								<tr>
								<?
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
	$displayData 	=	array();
	$regClsfId                      = $_REQUEST['regClsfId'];
	$classi_Id                       = $_REQUEST['classi_Id'];
	$sql_cal	=	array();
	$sql_cal['QUERY']		 =	"SELECT * 
									   FROM " . _DB_DINNER_CLASSIFICATION_ . " 
									  WHERE status != ?
									  	AND id = ? 
								   ORDER BY `id` ASC";
	$sql_cal['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'D',  		'TYP' => 's');
	$sql_cal['PARAM'][]  = array('FILD' => 'id',  	  'DATA' => $classi_Id,  'TYP' => 's');
	$res_cal = $mycms->sql_select($sql_cal);
	$rowDinner =	$res_cal[0];

	$sqlcutoff	=	array();
	$sqlcutoff['QUERY'] = "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " cutof WHERE status = ?";
	$sqlcutoff['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'A', 		'TYP' => 's');
	$resCutoff = $mycms->sql_select($sqlcutoff);
	$rowCount	= 0;
	foreach ($resCutoff as $keyCutoff => $rowCutoff) {
		$displayData[$rowCount]['classification_id'] = $rowDinner['id'];
		$displayData[$rowCount]['cutoff_title'] 	 = $rowCutoff['cutoff_title'];
		$displayData[$rowCount]['cutoff_id']		 = $rowCutoff['id'];

		$sqlPackageCheckoutDate	=	array();
		// query in tariff table
		$sqlPackageCheckoutDate['QUERY'] = "select * 
												  FROM " . _DB_DINNER_TARIFF_ . " accomodation
												 WHERE status = ?
												   AND cutoff_id = ?
												   AND dinner_classification_id = ?";

		$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'status', 					 'DATA' => 'A', 					'TYP' => 's');
		$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'cutoff_id',				 'DATA' => $rowCutoff['id'], 		'TYP' => 's');
		$sqlPackageCheckoutDate['PARAM'][]	=	array('FILD' => 'dinner_classification_id', 'DATA' => $rowDinner['id'], 		'TYP' => 's');
		$resPackageCheckoutDate = $mycms->sql_select($sqlPackageCheckoutDate);
		foreach ($resPackageCheckoutDate as $keyPrice => $price) {
			$displayData[$rowCount]['INR'] = $price['inr_amount'];
			$displayData[$rowCount]['USD'] = $price['usd_amount'];
		}
		$rowCount++;
	}
	//echo '<pre>';print_r($displayData);die();
	$workshopClassTitle = getWorkshopName($Workshopid);
	$regisClassTitle    = getRegClsfName($Regclassfid);
	$workshopDetails    = getAllWorkshopTariffs();

	$editDetails		= $workshopDetails[$Workshopid][$Regclassfid];
?>
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmTariffEdit" id="frmTariffEdit" method="post" action="dinner_tariff.process.php" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" id="act" value="update" />
			<input type="hidden" name="dinner_classification_id" id="dinner_classification_id" value="<?= $rowDinner['id'] ?>" />
			<div class="form-group">
				<h2>Update Tariff</h2>
			</div>
			<div class="form-group"><label class="frm-head">Dinner Name</label>
				<input disabled value="<?= $rowDinner['dinner_classification_name'] ?>">
			</div>
			<div class="form-group"><label class="frm-head">Service Type</label>
				<input disabled value="<?= $rowDinner['service_type'] ?>">
			</div>
			<?
			//echo '<pre>'; print_r($displayData);
			foreach ($displayData as $key => $rowDinnerData) {
			?>
				<input type="hidden" name="cutoff_id[<?= $rowDinnerData['cutoff_id'] ?>]" id="cutoff_id<?= $rowDinnerData['cutoff_id'] ?>" value="<?= $rowDinnerData['cutoff_id'] ?>" />
				<div class="w-100 p-0 tlisting">
					<div class="form-group">
						<h4><?= $rowDinnerData['cutoff_title'] ?></h4>
					</div>

					<div class="form-group"><label class="frm-head">INR</label>
						<input type="text" name="tariff_inr_cutoff_id_edit[<?= $rowDinnerData['cutoff_id'] ?>]" id="tariff_inr_first_cutoff_id_edit_<?= $rowDinnerData['cutoff_id'] ?>"
							value="<?= ($rowDinnerData['INR'] != '') ? $rowDinnerData['INR'] : '0.00' ?>" />
					</div>
					<div class="form-group"><label class="frm-head">USD</label>
						<input type="text" name="tariff_usd_cutoff_id_edit[<?= $rowDinnerData['cutoff_id'] ?>]" id="tariff_usd_first_cutoff_id_edit_<?= $key ?>"
							value="<?= ($rowDinnerData['USD'] != '') ? $rowDinnerData['USD'] : '0.00' ?>" />
					</div>
				</div>
			<?

			}
			?>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" type="button" id="back" onclick="location.href='dinner_tariff.php';">Back</button>
					<button class="submit" id="Save">Update</button>
				</div>
			</div>

		</form>
	</div>
<?php
}




?>