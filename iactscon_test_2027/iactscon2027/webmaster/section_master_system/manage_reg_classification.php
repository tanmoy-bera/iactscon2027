<?php
include_once('includes/init.php');

page_header("Registration Classification.");

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
						<th class="action" >Sl No.</th>
						<th width="90">Classification Title</th>
						<th width="90">Seat limit</th>
						<th width="90">Created Date</th>
						<th width="90">Icon</th>
						<th class="action">Status</th>
						<th class="action">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sql_cal			=	array();
					$sql_cal['QUERY']	=	"SELECT * 
													FROM " . _DB_REGISTRATION_CLASSIFICATION_ . "
													WHERE `status` 	!= 		?
													ORDER BY `sequence_by` ASC";

					$sql_cal['PARAM'][]	=	array('FILD' => 'status', 		'DATA' => 'D', 		'TYP' => 's');

					$res_cal = $mycms->sql_select($sql_cal);

					$i = 1;
					if ($res_cal) {
						foreach ($res_cal as $key => $rowsl) {

							$icon_image = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowsl['icon'];
					?>
							<tr class="tlisting" style=" <?= ($rowsl['display'] == 'N') ? 'background:bisque;' : '' ?>">
								<td class="action"><?= $i ?></td>
								<td align="left"><?= $rowsl['classification_title'] ?> - <?= $rowsl['type'] ?></td>
								<td><?= $rowsl['seat_limit'] ?></td>
								<td><?= displayDateFormat($rowsl['created_dateTime']) ?></td>
								<td><?php if (!empty($rowsl['icon'])) { ?><img src="<?php echo $icon_image; ?>" class="header-img"> <?php } ?></td>

								<td class="action">

									<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_process.php?act=<?= ($rowsl['status'] == 'A') ? 'Inactive' : 'Active' ?>&id=<?= $rowsl['id']; ?><?= $searchString ?>" class="<?= ($rowsl['status'] == 'A') ? 'ticket ticket-success' : 'ticket ticket-important' ?>"><?= ($rowsl['status'] == 'A') ? 'Active' : 'Inactive' ?></a>

								</td>
								<td class="action">

									<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="manage_reg_classification.php?show=edit&id=<?= $rowsl['id'] ?>">
										<span alt="Edit" title="Edit Record" class="icon-pen" /></a>

									<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_process.php?act=Remove&amp;id=<?= $rowsl['id']; ?>">
										<span alt="Remove" title="Remove Classification" class="icon-x-alt" onclick="return confirm('Do You Really Want To Remove The Classification ?');"></span></a>
								</td>
							</tr>
						<?
							$i++;
						}
					} else {
						?>
						<tr>
							<td colspan="10" class="mandatory">No Record Present.</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<div class="bbp-pagination">
				<div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
				<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
			</div>
			<a title="Add Classification" class="stick_add" href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>"><i class="fas fa-plus"></i></a>

	</form>
	<div class="body_content_box">
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

	<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_classification.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
		<input type="hidden" name="act" value="add" />
		<div class="form-group">
			<h2>Add Registration Classification</h2>
			</tr>
		</div>
		<div class="form-group">
			<label class="frm-head">Classification Title <span class="mandatory">*</span></label>
			<input type="text" name="classification_title" id="classification_title" class="validate[required]" onblur="checkClassificationTitle(this)" style="width:80%;" required />
		</div>

		<div class="form-group">
			<label class="frm-head">
				Classification Type<span class="mandatory">*</span>
			</label>

			<select name="type" style="width:80%;" required="">
				<option value="DELEGATE">DELEGATE</option>
				<option value="ACCOMPANY">ACCOMPANY</option>
				<option value="COMBO">COMBO</option>
				<option value="FULL_ACCESS">FULL ACCESS</option>
				<option value="GUEST">GUEST</option>
			</select>
		</div>
		<div class="form-group">
			<label class="frm-head">
				Seat limit <span class="mandatory">*</span>
			</label>

			<input type="text" name="seat_limit_add" id="seat_limit_add" class="validate[required]" value="" style="width:80%;" required />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Sequence By
			</label>
			<input type="number" name="sequence_by" id="sequence_by" value="<?= $row['sequence_by'] ?>" style="width:80%;" rel="splDate" required />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Currency<span class="mandatory">*</span>
			</label>
			<select name="currency" style="width:80%;" required="">
				<option value="INR">INR</option>
				<option value="USD">USD</option>
			</select>
		</div>
		<div class="form-group">
			<label class="frm-head">
				Icon Image <span class="mandatory"></span>
			</label>
			<input type="file" style="display: none;" id="icon" name="icon" />
			<label class="file-label" for="icon">Choose file</label>
		</div>
		<!-- <tr>
								<td align="left">
									Mail Lunch Details
								</td>
								<td align="left">
									<input type="text" name="mail_lunch_details" id="mail_lunch_details" value="<?= $row['mail_lunch_details'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr>
							<tr>
								<td align="left">
									Mail Dinner Details
								</td>
								<td align="left">
									<input type="text" name="mail_dinner_details" id="mail_dinner_details" value="<?= $row['mail_dinner_details'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr> -->

		<!-- <tr>
								<td align="left">
									Mail Gala Dinner Details
								</td>
								<td align="left">
									<input type="text" name="mail_gala_dinner_details" id="mail_gala_dinner_details" value="<?= $row['mail_gala_dinner_details'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr>
							<tr>
								<td align="left">
									Mail Inaugural Dinner Details
								</td>
								<td align="left">
									<input type="text" name="mail_inaugural_dinner_details" id="mail_inaugural_dinner_details" value="<?= $row['mail_inaugural_dinner_details'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr> -->
		<!-- <tr>
								<td align="left">
									Mail Lunch Details
								</td>
								<td align="left">
									<input type="text" name="mail_lunch_details" id="mail_lunch_details" value="<?= $row['sequence_by'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr> -->
		<div class="form-group">
			<label class="frm-head">
				Inclusion Lunch Date
			</label>
			<select name="inclusion_lunch_date[]" id="inclusion_lunch_date" multiple="multiple">
				<?php
				$sql_cal = array();
				$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Lunch'";
				$res_cal			=	$mycms->sql_select($sql_cal);
				$i = 1;

				foreach ($res_cal as $key => $rowsl) {
				?>
					<option value="<?= $rowsl['date'] ?>"><?= $rowsl['date'] ?></option>
				<?php } ?>
			</select>
		</div>
		<!-- <tr>
								<td width="40%" align="left">
									Inclusion Lunch Icon
								</td>
								<td width="60%" align="left">
									<input type="file" id="inclusion_lunch_icon" name="inclusion_lunch_icon" style="width:50%" />
								</td>
							</tr> -->
		<div class="form-group">
			<label class="frm-head">
				Inclusion Conference Dinner Date
			</label>
			<select name="inclusion_conference_kit_date[]" id="inclusion_conference_kit_date" multiple="multiple" style="width:85%">
				<?php
				$sql_cal = array();
				$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Dinner'";
				$res_cal			=	$mycms->sql_select($sql_cal);
				$i = 1;

				foreach ($res_cal as $key => $rowsl) {
				?>
					<option value="<?= $rowsl['date'] ?>"><?= $rowsl['date'] ?></option>
				<?php } ?>
			</select>
		</div>
		<!-- <tr>
								<td width="40%" align="left">
									Inclusion Conference Dinner Icon
								</td>
								<td width="60%" align="left">
									<input type="file" id="inclusion_conference_kit_icon" name="inclusion_conference_kit_icon" style="width:50%" />
								</td>
							</tr> -->
		<div class="form-group">
			<label class="frm-head">
				Inclusion Kit
			</label>
			<ul class="cus-check-wrap">
				<li>
					<p class="sub-check">

						<?php
						?>
						<label class="cus-check">Yes
							<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="Y" checked />
							<span class="checkmark"></span>
						</label>
						<label class="cus-check">No
							<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="N" /><span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="frm-head">
				Entry to Scientific Halls
			</label>
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
							<input type="radio" id="inclusion_sci_hall" name="inclusion_sci_hall" value="N" /><span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>
		</div>

		<div class="form-group">
			<label class="frm-head">
				Entry to Exhibition Area
			</label>
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
							<input type="radio" id="inclusion_exb_area" name="inclusion_exb_area" value="N" /><span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>
		</div>

		<div class="form-group">
			<label class="frm-head">
				Tea/Coffee during the Conference
			</label>
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
							<input type="radio" id="inclusion_tea_coffee" name="inclusion_tea_coffee" value="N" /><span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>
		</div>
		<div class="form-group">
			<div class="frm-btm-wrap">

					<button type="button" class="back" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>'" id="BackAdd">Back</button>
				<button class="submit">Save</button>
				
			</div>
		</div>

		<!-- <tr>
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
		</table> -->
	</form>

	<script type="text/javascript">
		jQuery("#inclusion_lunch_date").select2({
			placeholder: 'Add Lunch Date (dd-mm-yyyy)',
			tags: true
		});
		jQuery("#inclusion_conference_kit_date").select2({
			placeholder: 'Add Conference Dinner Date',
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
					url: "<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_classification.process.php",
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

	<form class="con_box-grd row" name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_reg_process.php" id="frmtypeadd" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
		<input type="hidden" name="act" value="update" />
		<input type="hidden" name="id" id="id" value="<?= $_REQUEST['id'] ?>" />
		<?php
		$sql 	=	array();
		$sql['QUERY']	=	"SELECT * 
								   FROM " . _DB_REGISTRATION_CLASSIFICATION_ . " 
								  WHERE `id` = ? ";
		$sql['PARAM'][]		=	array('FILD' => 'id', 		  'DATA' => $_REQUEST['id'],				   'TYP' => 's');
		$res_cal = $mycms->sql_select($sql);
		$row    = $res_cal[0];
		// echo '<pre>'; print_r($row);

		$icon_image = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['icon'];
		$inclusion_lunch_icon = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['inclusion_lunch_icon'];
		$inclusion_conference_kit_icon = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $row['inclusion_conference_kit_icon'];
		?>
		<div class="form-group">
			<h2>Edit Registration Classification</h2>
			</tr>
		</div>
		<div class="form-group">
			<label class="frm-head">Classification Title <span class="mandatory">*</span></label>
			<input type="text" name="classification_title" id="classification_title" class="validate[required]" value="<?= $row['classification_title'] ?>" style="width:80%;" required />
		</div>

		<div class="form-group">
			<label class="frm-head">
				Classification Type<span class="mandatory">*</span>
			</label>

			<select name="type" style="width:80%;" required="">
				<option <? if ($row['type'] == 'DELEGATE') {
							echo "selected";
						} ?> value="DELEGATE">DELEGATE</option>
				<option <? if ($row['type'] == 'ACCOMPANY') {
							echo "selected";
						} ?> value="ACCOMPANY">ACCOMPANY</option>
				<option <? if ($row['type'] == 'COMBO') {
							echo "selected";
						} ?> value="COMBO">COMBO</option>
				<option <? if ($row['type'] == 'FULL_ACCESS') {
							echo "selected";
						} ?> value="FULL_ACCESS">FULL ACCESS</option>
				<option <? if ($row['type'] == 'GUEST') {
							echo "selected";
						} ?> value="GUEST">GUEST</option>
			</select>
		</div>
		<div class="form-group">
			<label class="frm-head">
				Seat limit <span class="mandatory">*</span>
			</label>
			<input type="text" name="seat_limit_add" id="seat_limit_add" class="validate[required]" onblur="countryAvailabilityAdd(this.value)" value="<?= $row['seat_limit'] ?>" style="width:80%;" required />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Sequence By</label>

			<input type="number" name="sequence_by" id="sequence_by" value="<?= $row['sequence_by'] ?>" style="width:80%;" rel="splDate" required />
		</div>
		<div class="form-group">
			<label class="frm-head">
				Currency<span class="mandatory">*</span>
			</label>

			<select name="currency" style="width:80%;" required="">
				<option <? if ($row['type'] == 'INR') {
							echo "selected";
						} ?> value="INR">INR</option>
				<option <? if ($row['type'] == 'USD') {
							echo "selected";
						} ?> value="USD">USD</option>
			</select>
		</div>

		<div class="form-group">
			<label class="frm-head">Icon Image
				<!-- <img src="<?php echo $icon_image; ?>" width="50%" height="30%"> -->
			</label>
			<input type="file" style="display: none;" id="icon" name="icon" onchange="loadFileHeader(event)" />
			<label class="file-label" for="icon">Choose file</label>
			<img id="output_header" />
		</div>
		<!-- <tr>
								<td align="left">
									Mail Lunch Details
								</td>
								<td align="left">
									<input type="text" name="mail_lunch_details" id="mail_lunch_details" value="<?= $row['mail_lunch_details'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr>
							<tr>
								<td align="left">
									Mail Dinner Details
								</td>
								<td align="left">
									<input type="text" name="mail_dinner_details" id="mail_dinner_details" value="<?= $row['mail_dinner_details'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr> -->
		<!-- <tr>
								<td align="left">
									Mail Gala Dinner Details
								</td>
								<td align="left">
									<input type="text" name="mail_gala_dinner_details" id="mail_gala_dinner_details" value="<?= $row['mail_gala_dinner_details'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr>
							<tr>
								<td align="left">
									Mail Inaugural Dinner Details
								</td>
								<td align="left">
									<input type="text" name="mail_inaugural_dinner_details" id="mail_inaugural_dinner_details" value="<?= $row['mail_inaugural_dinner_details'] ?>" style="width:80%;" rel="splDate" />
								</td>
							</tr> -->
		<div class="form-group">
			<label class="frm-head">
				Inclusion Lunch Date
			</label>
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
		<!-- <tr>
								<td width="40%" align="left">
									<img src="<?php echo $inclusion_lunch_icon; ?>" width="30%" height="25%" style="background: #6e7070;">
								</td>
								<td width="60%" align="left">
									<input type="file" id="inclusion_lunch_icon" name="inclusion_lunch_icon" style="width:80%" onchange="loadFileHeader(event)" />
									<img id="output_header" />
								</td>
							</tr> -->
		<div class="form-group">
			<label class="frm-head">
				Inclusion Conference Dinner Date
			</label>
			<select name="inclusion_conference_kit_date[]" id="inclusion_conference_kit_date" multiple="multiple" style="width:84%">
				<?php
				$selected_inclusion_conference_kit_date = json_decode($row['inclusion_conference_kit_date']);

				$sql_cal = array();
				$sql_cal['QUERY']	=	"SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Dinner'";
				$res_cal			=	$mycms->sql_select($sql_cal);
				$i = 1;

				foreach ($res_cal as $key => $rowsl) {

					if (in_array(date('d-m-Y', strtotime($rowsl['date'])), $selected_inclusion_conference_kit_date)) {
						$selected = "selected";
					} else {
						$selected = "";
					}
				?>
					<option value="<?= date('d-m-Y', strtotime($rowsl['date'])) ?>" <?= $selected ?>><?= date('d-m-Y', strtotime($rowsl['date'])) ?></option>
				<?php } ?>


			</select>
		</div>
		<!-- <tr>
								<td width="40%" align="left">
									<img src="<?php echo $inclusion_conference_kit_icon; ?>" width="30%" height="25%" style="background: #6e7070;">
								</td>
								<td width="60%" align="left">
									<input type="file" id="inclusion_conference_kit_icon" name="inclusion_conference_kit_icon" style="width:80%" onchange="loadFileHeader(event)" />
									<img id="output_header" />
								</td>
							</tr> -->

		<div class="form-group">
			<label class="frm-head">
				Inclusion Kit
			</label>
			<ul class="cus-check-wrap">
				<li>
					<p class="sub-check">

						<?php
						?>
						<label class="cus-check">Yes
							<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="Y" <? if ($row['inclusion_conference_kit'] == 'Y') {
																															echo "checked";
																														} ?> />
							<span class="checkmark"></span>
						</label>
						<label class="cus-check">No
							<input type="radio" id="inclusion_conference_kit" name="inclusion_conference_kit" value="N" <? if ($row['inclusion_conference_kit'] == 'N') {
																															echo "checked";
																														} ?> />
							<span class="checkmark"></span>
						</label>
					</p>
				</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="frm-head">
				Entry to Scientific Halls
			</label>
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
			<label class="frm-head">
				Entry to Exhibition Area
			</label>
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
			<label class="frm-head">
				Tea/Coffee during the Conference
			</label>
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
			<div class="frm-btm-wrap">
				<button type="button" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>'" class="back" id="back">Back</button>
				<button class="submit" id="Save">Save</button>
			</div>
		</div>


		<!-- <tr>
				<td width="40%"></td>
				<td align="left">
					<a href="<?= $_SERVER['PHP_SELF'] ?>?pageno=<?= $_REQUEST['pageno'] ?>">
						<input type="button" name="BackAdd" id="BackAdd" value="Back" class="btn btn-small btn-red" /></a>
					&nbsp;
					<input type="submit" name="Save2" id="Save2" value="Save" class="btn btn-small btn-blue" />
				</td>
			</tr> -->

	</form>
	<script type="text/javascript">
		jQuery("#inclusion_lunch_date").select2({
			placeholder: 'Add Lunch Date(dd-mm-yyyy)',
			tags: true
		});
		jQuery("#inclusion_conference_kit_date").select2({
			placeholder: 'Add Conference Dinner Date',
			tags: true
		});
	</script>
	<script type="text/javascript">
		var loadFileHeader = function(event) {
			var output = document.getElementById('output_header');
			output.style.width = '200px';
			output.style.height = '100px';
			output.style.marginInlineStart = "15px"
			output.src = URL.createObjectURL(event.target.files[0]);
			output.onload = function() {
				URL.revokeObjectURL(output.src) // free memory
			}
		};
	</script>
<?php
}
?>