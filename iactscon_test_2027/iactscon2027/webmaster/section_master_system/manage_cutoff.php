<?php
include_once('includes/init.php');

page_header("Cutoff");

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
			addRegCutoff($cfg, $mycms);
			break;

		// REG TARIFF CLASSIFICATION EDIT FORM LAYOUT
		case 'edit':
			editRegCutoff($cfg, $mycms);
			break;

		// WORKSHOP CUTOFF
		case 'addWorkshopCutoff':
			addWorkshopCutoff($cfg, $mycms);
			break;

		case 'editWorkshopCutoff':
			editWorkshopCutoff($cfg, $mycms);
			break;

		case 'workshop':
			workshopCutoffListingLayout($cfg, $mycms);
			break;

		// CONFERENCE DATE ADD
		case 'addDate':
			addConfDate($cfg, $mycms);
			break;

		// CONFERENCE DATE EDIT
		case 'editDate':
			editConfDate($cfg, $mycms);
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
				<h2>Manage Cutoff</h2>
			</div>


			<!-- <div class="tsearch">
						<table width="100%">
							<tr>
								<td align="right">

									Conference Registration

									&nbsp;

									<input type="text" name="src_tariff_classification" id="src_tariff_classification" value="<?= $_REQUEST['src_tariff_classification'] ?>" />

									&nbsp;

									<?php
									searchStatus();
									?>
									<input type="submit" name="searchSubmit" value="Go" class="btn btn-small btn-blue" />

								</td>
							</tr>
						</table>
					</div> -->
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
			<div class="table_wrap">
				<table width="100%">
					<thead>
						<tr class="theader">
							<th class="action">Sl No.</th>
							<th align="center">Cutoff Title</th>
							<th align="center">Start Date</th>
							<th align="center">End Date</th>
							<th class="action">Status</th>
							<th class="action">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php

						$sql_cal['QUERY']	=	"SELECT *  
											    FROM " . _DB_TARIFF_CUTOFF_ . " 
										       WHERE status != 'D'";
						$res_cal			=	$mycms->sql_select($sql_cal);
						$i = 1;

						foreach ($res_cal as $key => $rowsl) {
						?>
							<tr class="tlisting">
								<td class="action"><?= $i ?></td>
								<td><?= $rowsl['cutoff_title'] ?></td>
								<td><?= displayDateFormat($rowsl['start_date']) ?></td>
								<td><?= displayDateFormat($rowsl['end_date']) ?></td>

								<td class="action">

									<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?= ($rowsl['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>" class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>

								</td>
								<td class="action">
									<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="manage_cutoff.php?show=edit&id=<?= $rowsl['id'] ?>">
										<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
									<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=deleteCutoff&id=<?= $rowsl['id']; ?>">
										<span title="Remove" class="icon-trash-stroke" onclick="return confirm('Do you really want to remove this record');"></span></a>
								</td>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
				</table>
				<div class="bbp-pagination">
					<div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
					<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
				</div>

				<!-- <tr class="tfooter">
				<td align="right" colspan="2"><a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>">+Add More Cutoff</a><br>

					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
				</td>
			</tr> -->
				<a title="Add Cutoff" class="stick_add" href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>"><i class="fas fa-plus"></i></a>
		</form>
	</div>
	<div class="body_content_box">
		<form class="con_box-grd row">
			<div class="form-group">
				<h2>Manage Conference Date</h2>
				<!--<span class="tsearchTool" forType="tsearchTool"></span>-->
			</div>
			<div class="table_wrap">
				<?
				$counter                 = 0;
				//echo'<pre>';print_r($titleheaeder);echo'</pre>';
				?>
				<table width="100%">
					<thead>
						<tr>
							<th class="action">Sl No.</th>
							<th>Conference Dates</th>
							<th class="action">Status</th>
							<th class="action">Action</th>
						</tr>
					</thead>
					<tbody>

						<?php

						$sql_cal['QUERY']	=	"SELECT *  
											FROM " . _DB_CONFERENCE_DATE_ . " 
											WHERE status != 'D'";
						$res_cal			=	$mycms->sql_select($sql_cal);
						$i = 1;

						foreach ($res_cal as $key => $rowsl) {
						?>
							<tr class="tlisting">
								<td class="action"><?= $i ?></td>
								<td><?= $rowsl['conf_date'] ?></td>
								<td class="action">
									<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?= ($rowsl['status'] == 'A') ? 'InactiveDate' : 'ActiveDate' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>" class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>
								</td>
								<td class="action">
									<a href="manage_cutoff.process.php?act=deleteDate&id=<?= $rowsl['id']; ?>">
										<span title="Remove" class="icon-trash-stroke" onclick="return confirm('Do you really want to remove this record');"></span></a>
								</td>
							</tr>
						<?
							$i++;
						}
						?>


						<tr class="tfooter">
							<td align="right" colspan="10"><a href="javascript:void(null);" onClick="redirectionOfLink(this)" class="mandatory" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=addDate">+Add More Conference Date</a><br>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
	<?php
}

/**************************************************************/
/*                       EDIT TARIFF FORM                     */
/**************************************************************/
function editRegCutoff($cfg, $mycms)
{
	global $searchArray, $searchString;
	?>
		<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="update" />

			<?php
			$sql_cal['QUERY']	= "SELECT * FROM " . _DB_TARIFF_CUTOFF_ . " WHERE `id` = " . $_REQUEST['id'] . "";
			$res_cal			= $mycms->sql_select($sql_cal);
			$row    			= $res_cal[0];
			?>
			<input type="hidden" name="cutoff_id" id="cutoff_id" value="<?= $row['id'] ?>" />
			<div class="form-group">
				<h2>Edit Cutoff</h2>
			</div>



			<div class="form-group">
				<label class="frm-head">Cutoff Title <span class="mandatory">*</span> </label>

				<input type="text" name="cutoff_title" id="cutoff_title" value="<?= $row['cutoff_title'] ?>" style="width:80%;" required />

			</div>
			<div class="form-group">
				<label class="frm-head">Start Date <span class="mandatory">*</span> </label>

				<input type="date" name="start_date" id="start_date" value="<?= $row['start_date'] ?>" style="width:80%;" rel="splDate" required />

			</div>
			<div class="form-group">
				<label class="frm-head">End Date <span class="mandatory">*</span> </label>

				<input type="date" name="end_date" id="end_date" value="<?= $row['end_date'] ?>" style="width:80%;" rel="splDate" required />

			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<a href="<?= $_SERVER['PHP_SELF'] ?>"><button type="button" class="back" id="BackAdd">Back</button></a>
					&nbsp;
					<button class="submit" id="Save2">Save</button>
				</div>
			</div>
		</form>
	<?php
}


/*================= ADD WORKHOP WINDOW ===============*/
function addRegCutoff($cfg, $mycms)
{
	global $searchArray, $searchString;
	?>
		<div class="body_content_box">
			<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
				<input type="hidden" name="act" value="insert" />
				<?php
				foreach ($searchArray as $key => $val) {
				?>
					<input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
				<?php
				}
				?>
				<div class="form-group">
					<h2>Add Registration Cutoff</h2>
				</div>
				<div class="form-group">
					<label class="frm-head">Cutoff Title <span class="mandatory">*</span></label>
					<input type="text" name="title" id="workshop_add"  required="" />
				</div>
				<div class="form-group">
					<label class="frm-head">Start Date <span class="mandatory">*</span></label>
					<input type="date" name="start_date" id="seat_limit_add"   rel="splDate" required="" />
				</div>
				<div class="form-group">
					<label class="frm-head">End Date <span class="mandatory">*</span></label>
					<input type="date" name="end_date" id="seat_limit_add"   rel="splDate" required="" />
				</div>
				<div class="form-group">
					<div class="frm-btm-wrap">
						<button class="back" onClick="location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>';" id="back">Back</button>
						<button type="submit" class="submit">Save</button>
					</div>
				</div>
			
			</form>
		</div>
	<?php
}

/**************************************************************/
/*                       WORKSHOP CUTOFF FORM                 */
/**************************************************************/

function workshopCutoffListingLayout($cfg, $mycms)
{
	?>
		<div class="body_content_box">
			<div class="table_wrap">
				<table width="100%" class="tborder">
					<tr>
						<td class="tcat" colspan="2" align="left">
							<span style="float:left">Manage Workshop Cutoff</span>
							<!--<span class="tsearchTool" forType="tsearchTool"></span>-->
						</td>
					</tr>
					<tr>
						<td colspan="2" style="margin:0px; padding:0px;">
							<table width="100%">
								<tr class="theader">
									<td width="10">Sl No.</td>
									<td width="90" align="center">Cutoff Title</td>
									<td width="90" align="center">Start Date</td>
									<td width="90" align="center">End Date</td>
									<td width="70" align="center">Status</td>
									<td width="70" align="center">Action</td>
								</tr>
								<tr class="theader">


								</tr>
								<?php

								$sql_cal['QUERY']	=	"SELECT *  
											FROM " . _DB_WORKSHOP_CUTOFF_ . " 
											WHERE status != 'D'";
								$res_cal			=	$mycms->sql_select($sql_cal);
								$i = 1;

								foreach ($res_cal as $key => $rowsl) {
								?>
									<tr class="tlisting">
										<td align="center"><?= $i ?></td>
										<td align="left"><?= $rowsl['cutoff_title'] ?></td>
										<td align="center"><?= displayDateFormat($rowsl['start_date']) ?></td>
										<td align="center"><?= displayDateFormat($rowsl['end_date']) ?></td>
										<td align="center">
											<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=<?= ($rowsl['status'] == 'A') ? 'InactiveWorkshop' : 'ActiveWorkshop' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>" class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>
										</td>
										<td align="center">
											<a href="manage_cutoff.php?show=editWorkshopCutoff&id=<?= $rowsl['id'] ?>">
												<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
											<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php?act=deleteWorkshopCutoff&id=<?= $rowsl['id']; ?>">
												<span title="Remove" class="icon-trash-stroke" onclick="return confirm('Do you really want to remove this record');"></span></a>
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
						<td align="right" colspan="2"><a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=addWorkshopCutoff">+Add More Workshop Cutoff</a><br>

							<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
							<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
	<?php
}

function editWorkshopCutoff($cfg, $mycms)
{
	global $searchArray, $searchString;
	?>
		<form name="frmtypeadd" class="con_box-grd row" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="updateWorkshop" />

			<?php
			$sql_cal['QUERY']	= "SELECT * FROM " . _DB_WORKSHOP_CUTOFF_ . " WHERE `id` = " . $_REQUEST['id'] . "";
			$res_cal			= $mycms->sql_select($sql_cal);
			$row    			= $res_cal[0];
			?>
			<input type="hidden" name="cutoff_id" id="cutoff_id" value="<?= $row['id'] ?>" />

			<div class="form-group">
				<h2>Edit Workshop Cutoff</h2>
			</div>
			<div class="form-group">
				<label class="frm-head">Cutoff Title <span class="mandatory">*</span> </label>
				<input type="text" name="cutoff_title" id="cutoff_title" value="<?= $row['cutoff_title'] ?>" style="width:80%;" required />
			</div>
			<div class="form-group">
				<label class="frm-head">Start Date <span class="mandatory">*</span> </label>
				<input type="date" name="start_date" id="start_date" value="<?= $row['start_date'] ?>" style="width:80%;" rel="splDate" required />
			</div>
			<div class="form-group">
				<label class="frm-head">End Date <span class="mandatory">*</span> </label>

				<input type="date" name="end_date" id="end_date" value="<?= $row['end_date'] ?>" style="width:80%;" rel="splDate" required />
			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<a href="<?= $_SERVER['PHP_SELF'] ?>?show=workshop"><button type="button" class="back" id="BackAdd">Back</button></a>
					&nbsp;
					<button class="submit" id="Save2">Save</button>
				</div>
			</div>
		</form>
	<?php
}


/*================= ADD WORKHOP WINDOW ===============*/
function addWorkshopCutoff($cfg, $mycms)
{
	global $searchArray, $searchString;
	?>
		<form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="insertWorkshop" />
			<input type="hidden" name="chk_country_add" id="chk_country_add" value="0" />
			<?php
			foreach ($searchArray as $key => $val) {
			?>
				<input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
			<?php
			}
			?>
			<table width="50%" class="tborder">
				<thead>
					<tr>
						<td colspan="2" align="left" class="tcat">Add Workshop Cutoff</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" align="center" style="margin:0px; padding:0px;">

							<table width="100%">
								<tr class="thighlight">
									<td colspan="2" align="left">Cutoff Details</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Cutoff Title <span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										<input type="text" name="workshop_add" id="workshop_add" class="validate[required]" style="width:80%;" required="" />
									</td>
								</tr>
								<tr>
									<td align="left">
										Start Date <span class="mandatory">*</span>
									</td>
									<td align="left">
										<input type="date" name="seat_limit_add" id="seat_limit_add" class="validate[required]" onblur="countryAvailabilityAdd(this.value)" style="width:80%;" rel="splDate" required="" />
									</td>
								</tr>
								<tr>
									<td align="left">
										End Date
									</td>
									<td align="left">
										<input type="date" name="workshop_date_add" id="workshop_date_add" style="width:80%;" rel="splDate" required="" />
									</td>
								</tr>

							</table>

						</td>
					</tr>
					<tr>
						<td width="40%"></td>
						<td align="left">
							<a href="<?= $_SERVER['PHP_SELF'] ?>?show=workshop">
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
	<?php
}


function addConfDate($cfg, $mycms)
{
	global $searchArray, $searchString;
	?>
		<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_cutoff.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="insertDate" />
			<input type="hidden" name="chk_country_add" id="chk_country_add" value="0" />
			<?php
			foreach ($searchArray as $key => $val) {
			?>
				<input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
			<?php
			}
			?>
			<div class="form-group">
				<h2>Add Conference Date</h2>
			</div>
			<div class="form-group">
				<label class="frm-head">Conference Date <span class="mandatory">*</span></label>

				<input type="date" name="conf_date" id="conf_date" class="validate[required]" style="width:80%;" required="" />

			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<a href="<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>">
						<button type="button" class="back" onClick="location.href='page.php';" id="BackAdd">Back</button></a>
					&nbsp;
					<button class="submit" id="Save2">Save</button>
				</div>
			</div>




		</form>
	<?php
}
	?>