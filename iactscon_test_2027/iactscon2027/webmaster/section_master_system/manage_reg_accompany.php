<?php
include_once('includes/init.php');

page_header("Accompany Classification.");

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

		// TARIFF CLASSIFICATION ADD FORM LAYOUT
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
				<h2>Manage Accompany Classification</h2>
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
							<th align="center">Classification Title</th>

							<th align="center">Created Date</th>
							<th class="action">Status</th>
							<th class="action">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sql_cal			=	array();
						$sql_cal['QUERY']	=	"SELECT * 
													FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . "
													WHERE `status` 	!= 		?
													ORDER BY `id` ASC";

						$sql_cal['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');

						$res_cal = $mycms->sql_select($sql_cal);

						$i = 1;

						foreach ($res_cal as $key => $rowsl) {
						?>
							<tr class="tlisting" style=" <?= ($rowsl['display'] == 'N') ? 'background:bisque;' : '' ?>">
								<td class="action"><?= $i ?></td>
								<td align="left"><?= $rowsl['classification_title'] ?> - <?= $rowsl['type'] ?></td>

								<td align="center"><?= displayDateFormat($rowsl['created_dateTime']) ?></td>

								<td class="action">

									<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php?act=<?= ($rowsl['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>" class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>

								</td>
								<td class="action">
									<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="manage_reg_accompany.php?show=edit&id=<?= $rowsl['id'] ?>">
										<span alt="Edit" title="Edit Record" class="icon-pen" /></a>
								</td>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<a title="add" class="stick_add" href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>"><i class="fas fa-plus"></i></a>
		</form>
	</div>
	<div class="body_content_box">
		<div class="con_box-grd row">
			<div class="form-group">
				<h2>Manage Input Field for Accompany</h2>
			</div>
			<div class="table_wrap">
				<table width="100%" class="tborder">
					<tbody>
						<tr>
							<td>
								Food Preference
							</td>
							<td class="action">
								<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php?act=<?= ($res_cal[0]['food_preference'] == 'A') ? 'foodPrefInactive' : 'foodPrefActive' ?>&id=<?= $res_cal[0]['id']; ?>" class="<?= ($res_cal[0]['food_preference'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($res_cal[0]['food_preference'] == 'A') ? 'Active' : 'Inactive' ?></a>
							</td>

						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php
}


/**************************************************************/
/*                       ADD TARIFF FORM                     */
/**************************************************************/

function addTariffClassification($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<script language="javascript" src="<?= $cfg['SECTION_BASE_URL'] ?>scripts/select2.min.js"></script>
	<link rel="stylesheet" href="<?= $cfg['SECTION_BASE_URL'] ?>css/select2.min.css">
	<div class="body_content_box">
		<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php" id="frmtypeadd" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="add" />
			<div class="form-group">
				<h2>Add Accompany Classification</h2>
			</div>
			<div class="form-group">
				<label class="frm-head">Classification Title <span class="mandatory">*</span></label>
				<input type="text" name="classification_title" id="classification_title" class="validate[required]" onblur="checkClassificationTitle(this)" required />
			</div>
			<div class="form-group">
				<label class="frm-head">Inclusion Lunch Date <span class="mandatory">*</span></label>
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
						<option value="<?= date('d-m-Y', strtotime($rowsl['date'])) ?>"><?= date('d-m-Y', strtotime($rowsl['date'])) ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
				<label class="frm-head">Inclusion Conference Dinner Date <span class="mandatory">*</span></label>
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
						<option value="<?= date('d-m-Y', strtotime($rowsl['date'])) ?>"><?= date('d-m-Y', strtotime($rowsl['date'])) ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
				<label class="frm-head">Entry to Scientific Halls</label>
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
								<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="N" />
								<span class="checkmark"></span>
							</label>
						</p>
					</li>
				</ul>
			</div>
			<div class="form-group">
				<label class="frm-head">Entry to Exhibition Area</label>
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
								<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="N" />
								<span class="checkmark"></span>
							</label>
						</p>
					</li>
				</ul>
			</div>
			<div class="form-group">
				<label class="frm-head">Tea/Coffee during the Conference</label>
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
								<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="N" />
								<span class="checkmark"></span>
							</label>
						</p>
					</li>
				</ul>
			</div>
			<div class="form-group">
				<label class="frm-head">Inclusion Conference Kit <span class="mandatory">*</span></label>
				<ul class="cus-check-wrap">
					<li>
						<p class="sub-check">
							<?php
							?>
							<label class="cus-check">Yes
								<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="Y" checked required />
								<span class="checkmark"></span>
							</label>
							<label class="cus-check">No
								<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="N" required />
								<span class="checkmark"></span>
							</label>
						</p>
					</li>
				</ul>
			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>'" type="button" name="BackAdd" id="BackAdd">Back</button>
					<button type="submit" name="Save2" id="Save2" class="submit">Save</button>
				</div>
			</div>
			<table width="50%" class="tborder d-none">
				<thead>
					<tr>
						<td colspan="2" align="left" class="tcat"></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" align="center" style="margin:0px; padding:0px;">

							<table width="100%">
								<tr>
									<td width="40%" align="left">
										Entry to Scientific Halls
									</td>
									<td width="10%" align="left">
										<?php
										?>
										<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="Y" checked />YES
										<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="N" />No
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Entry to Exhibition Area
									</td>
									<td width="10%" align="left">
										<?php
										?>
										<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="Y" checked />YES
										<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="N" />No
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Tea/Coffee during the Conference
									</td>
									<td width="10%" align="left">
										<?php
										?>
										<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="Y" checked />YES
										<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="N" />No
									</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Inclusion Conference Kit <span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="Y" checked required />YES
										<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="N" required />No
									</td>
								</tr>


							</table>

						</td>
					</tr>
					<tr>
						<td width="40%"></td>
						<td align="left">
							<a href="<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>">
								<input type="button" name="BackAdd" id="BackAdd" value="Back" class="btn btn-small btn-red" /></a>
							&nbsp;
							<input type="submit" name="Save2" id="Save2" value="Save" class="btn btn-small btn-blue" />
							<span class="gen-error" style="color: red; display: block;"></span>
						</td>
					</tr>
					<tr class="tfooter">
						<td colspan="2">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<script type="text/javascript">
		jQuery("#inclusion_lunch_date").select2({
			placeholder: 'Add Lunch Date (dd-mm-yyyy)',
			tags: true
		});
		jQuery("#inclusion_dinner_date").select2({
			placeholder: 'Add Dinner Date (dd-mm-yyyy)',
			tags: true
		});
	</script>
	<script type="text/javascript">
		function checkClassificationTitle(argument) {
			var Title = $.trim($(argument).val());
			if (Title == "") {
				return false;
			} else {
				$.ajax({
					url: "<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php",
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
	</script>
<?php
}

/**************************************************************/
/*                       EDIT REGISTRATION CLASSIFICATION                     */
/**************************************************************/
function editWorkshop($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<script language="javascript" src="<?= $cfg['SECTION_BASE_URL'] ?>scripts/select2.min.js"></script>
	<link rel="stylesheet" href="<?= $cfg['SECTION_BASE_URL'] ?>css/select2.min.css">

	<div class="body_content_box">
		<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_accompany.process.php" id="frmtypeadd" enctype="multipart/form-data" onsubmit="return onSubmitAction();">
			<input type="hidden" name="act" value="update" />
			<input type="hidden" name="id" id="id" value="<?= $_REQUEST['id'] ?>" />
			<?php
			$sql 	=	array();
			$sql['QUERY']	=	"SELECT * 
								   FROM " . _DB_ACCOMPANY_CLASSIFICATION_ . " 
								  WHERE `id` = ? ";
			$sql['PARAM'][]		=	array('FILD' => 'id', 		  'DATA' => $_REQUEST['id'],				   'TYP' => 's');
			$res_cal = $mycms->sql_select($sql);
			$row    = $res_cal[0];

			$inclusion_lunch_icon = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['inclusion_lunch_icon'];
			$inclusion_conference_kit_icon = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['inclusion_conference_kit_icon'];
			?>
			<div class="form-group">
				<h2>Edit Accompany Classification</h2>
			</div>
			<div class="form-group">
				<label class="frm-head">Classification Title <span class="mandatory">*</span></label>
				<input type="text" name="classification_title" id="classification_title" class="validate[required]" value="<?= $row['classification_title'] ?>" required />
			</div>
			<div class="form-group">
				<label class="frm-head">Inclusion Lunch Date <span class="mandatory">*</span></label>
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
					<!-- <?php
							$selected_inclusion_lunch_date = json_decode($row['inclusion_lunch_date']);
							if (count($selected_inclusion_lunch_date) > 0) {
								foreach ($selected_inclusion_lunch_date as $key => $value) {
							?>
												
												<option value="<?php echo $value; ?>"<?php if ($value != '') {
																							echo ' selected';
																						} ?>><?php echo $value; ?></option>
												<?php
											}
										} else {
										}
												?> -->
				</select>
			</div>
			<div class="form-group">
				<label class="frm-head">Inclusion Conference Dinner Date <span class="mandatory">*</span></label>
				<select name="inclusion_dinner_date[]" id="inclusion_dinner_date" multiple="multiple" style="width:84%">
					<?php
					$selected_inclusion_dinner_date = json_decode($row['inclusion_dinner_date']);

					$sql_cal = array();
					$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Dinner'";
					$res_cal			=	$mycms->sql_select($sql_cal);
					$i = 1;

					foreach ($res_cal as $key => $rowsl) {

						if (in_array(date('d-m-Y', strtotime($rowsl['date'])), $selected_inclusion_dinner_date)) {
							$selected = "selected";
						} else {
							$selected = "";
						}
					?>
						<option value="<?= date('d-m-Y', strtotime($rowsl['date'])) ?>" <?= $selected ?>><?= date('d-m-Y', strtotime($rowsl['date'])) ?></option>
					<?php } ?>


				</select>
			</div>
			<div class="form-group">
				<label class="frm-head">Entry to Scientific Halls</label>
				<ul class="cus-check-wrap">
					<li>
						<p class="sub-check">
							<?php
							?>
							<label class="cus-check">Yes
								<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="Y" <? if ($row['inclusion_sci_hall'] == 'Y') {
																													echo "checked";
																												} ?> />
								<span class="checkmark"></span>
							</label>
							<label class="cus-check">No
								<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="N" <? if ($row['inclusion_sci_hall'] == 'N') {
																													echo "checked";
																												} ?> />
								<span class="checkmark"></span>
							</label>
						</p>
					</li>
				</ul>
			</div>
			<div class="form-group">
				<label class="frm-head">Entry to Exhibition Area</label>
				<ul class="cus-check-wrap">
					<li>
						<p class="sub-check">
							<?php
							?>
							<label class="cus-check">Yes
								<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="Y" <? if ($row['inclusion_exb_area'] == 'Y') {
																													echo "checked";
																												} ?> />
								<span class="checkmark"></span>
							</label>
							<label class="cus-check">No
								<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="N" <? if ($row['inclusion_exb_area'] == 'N') {
																													echo "checked";
																												} ?> />
								<span class="checkmark"></span>
							</label>
						</p>
					</li>
				</ul>
			</div>
			<div class="form-group">
				<label class="frm-head">Tea/Coffee during the Conference</label>
				<ul class="cus-check-wrap">
					<li>
						<p class="sub-check">
							<?php
							?>
							<label class="cus-check">Yes
								<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="Y" <? if ($row['inclusion_tea_coffee'] == 'Y') {
																														echo "checked";
																													} ?> />
								<span class="checkmark"></span>
							</label>
							<label class="cus-check">No
								<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="N" <? if ($row['inclusion_tea_coffee'] == 'N') {
																														echo "checked";
																													} ?> />
								<span class="checkmark"></span>
							</label>
						</p>
					</li>
				</ul>
			</div>
			<div class="form-group">
				<label class="frm-head">Inclusion Conference Kit <span class="mandatory">*</span></label>
				<ul class="cus-check-wrap">
					<li>
						<p class="sub-check">
							<?php
							?>
							<label class="cus-check">Yes
								<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="Y" <? if ($row['inclusion_conference_kit'] == 'Y') {
																																echo "checked";
																															} ?> required />
								<span class="checkmark"></span>
							</label>
							<label class="cus-check">No
								<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="N" <? if ($row['inclusion_conference_kit'] == 'N') {
																																echo "checked";
																															} ?> required />
								<span class="checkmark"></span>
							</label>
						</p>
					</li>
				</ul>
			</div>
			<div class="form-group">
				<div class="frm-btm-wrap">
					<button class="back" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>'" type="button" name="BackAdd" id="BackAdd">Back</button>
					<button type="submit" name="Save2" id="Save2" class="submit">Save</button>
				</div>
			</div>
			<table width="50%" class="tborder d-none">
				<thead>
					<tr>
						<td colspan="2" align="left" class="tcat"></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" align="center" style="margin:0px; padding:0px;">

							<table width="100%">
								<tr class="thighlight">
									<td colspan="2" align="left">Accompany Classification</td>
								</tr>
								<tr>
									<td width="40%" align="left">
										Classification Title <span class="mandatory">*</span>
									</td>
									<td width="60%" align="left">
										<input type="text" name="classification_title" id="classification_title" class="validate[required]" value="<?= $row['classification_title'] ?>" required />
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
											<!-- <?php
													$selected_inclusion_lunch_date = json_decode($row['inclusion_lunch_date']);
													if (count($selected_inclusion_lunch_date) > 0) {
														foreach ($selected_inclusion_lunch_date as $key => $value) {
													?>
												
												<option value="<?php echo $value; ?>"<?php if ($value != '') {
																							echo ' selected';
																						} ?>><?php echo $value; ?></option>
												<?php
														}
													} else {
													}
												?> -->
										</select>

									</td>

								</tr>
								<!-- <tr>
								<td width="40%" align="left">
									<img src="<?php echo $inclusion_lunch_icon; ?>" width="30%" height="25%" style="background: #6e7070;">
								</td>
								<td width="60%" align="left">
									<input type="file" id="inclusion_lunch_icon" name="inclusion_lunch_icon" style="width:80%" onchange="loadFileHeader(event)" />
									<img id="output_header" />
								</td>
							</tr> -->
								<tr>
									<td align="left">
										Inclusion Conference Dinner Date<?php // echo '<pre>';print_r($selected_inclusion_lunch_date)
																		?>
									</td>
									<td align="left">
										<select name="inclusion_dinner_date[]" id="inclusion_dinner_date" multiple="multiple" style="width:84%">
											<?php
											$selected_inclusion_dinner_date = json_decode($row['inclusion_dinner_date']);

											$sql_cal = array();
											$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Dinner'";
											$res_cal			=	$mycms->sql_select($sql_cal);
											$i = 1;

											foreach ($res_cal as $key => $rowsl) {

												if (in_array(date('d-m-Y', strtotime($rowsl['date'])), $selected_inclusion_dinner_date)) {
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
								<!-- <tr>
								<td width="40%" align="left">
									<img src="<?php echo $inclusion_conference_kit_icon; ?>" width="30%" height="25%" style="background: #6e7070;">
								</td>
								<td width="60%" align="left">
									<input type="file" id="inclusion_conference_kit_icon" name="inclusion_conference_kit_icon" style="width:80%" onchange="loadFileHeader(event)" />
									<img id="output_header" />
								</td>
							</tr> -->
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


							</table>

						</td>
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
	</div>
	<script type="text/javascript">
		jQuery("#inclusion_lunch_date").select2({
			placeholder: 'Add Lunch Date(dd-mm-yyyy)',
			tags: true
		});
		jQuery("#inclusion_dinner_date").select2({
			placeholder: 'Add Dinner Date(dd-mm-yyyy)',
			tags: true
		});
	</script>
<?php
}
?>