<?php
include_once('includes/init.php');

page_header("Nearby Attractions");

$pageKey = "_pgn_";

$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]          = $pageKeyVal;
$searchArray['name']       		= $_REQUEST['name'];

foreach ($searchArray as $searchKey => $searchVal) {
	$searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>
<div class="container">
	<?php
	switch ($show) {
		// show add window
		case 'add':
			addAttraction($cfg, $mycms);
			break;
		// show edit window	
		case 'edit':
			editAttraction($cfg, $mycms);
			break;
		// view details record
		case 'view':
			viewAttraction($cfg, $mycms);
			break;
		// show all record	
		default:
			showAllAttractions($cfg, $mycms);
			break;
	}
	?>
</div>
<?php
page_footer();

/*============== SHOW ALL COUNTRY WINDOW =============*/
function showAllAttractions($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmCountryListing" id="frmCountryListing" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_attraction.process.php" method="post">
		<input type="hidden" name="act" id="act" value="search_poll" />
		<input type="hidden" id="excelPrintVal" name="excelPrintVal" />
		<table width="100%" align="center" class="tborder">
			<tr>
				<td colspan="2" align="left" class="tcat">
					<span style="float:left">Nearby Attraction Listing</span>
					<span class="tsearchTool" forType="tsearchTool"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">

					<div class="tsearch">
                        <table width="100%">
                            <tr>
                                <td align="right">
                                    Name:
                                    <input type="text" name="name" id="name" 
                                        value="<?= $_REQUEST['name'] ?>" />                        
                                    &nbsp;
                                    <?php
                                    searchStatus();
                                    ?>
                                    <input type="submit" name="searchBtn" id="searchBtn" value="Search" class="btn btn-small btn-blue" />
                                </td>
                            </tr>
                        </table>
                    </div>

					<table width="100%">
						<tr class="theader">
							<td width="30" align="center">
								<input name="check_all" id="check_all" targetElement="checkBoxListing"
									type="checkbox" onclick="checkall(this);" />
							</td>
							<td width="40" align="center">Sl No </td>
							<td width="100" align="left">Name</td>
							<td width="50" align="center">Location</td>
							<td width="60" align="center">Distance from venue</td>
							<td width="70" align="center">Time to reach</td>
							<td width="70" align="center">Image</td>
							<td width="70" align="center">Status</td>
							<td width="40" align="center">Action</td>
						</tr>
						<?php
						$con = "";
						$params = [];
						if (!empty($_REQUEST['name'])) {
                            $con .= " AND LOWER(`name`) LIKE ?";
                            $params[] = [
                                'FILD' => 'name',
                                'DATA' => '%' . strtolower(trim($_REQUEST['name'])) . '%',
                                'TYP'  => 's'
                            ];
                        }

						$i = 0;

						$sql = [];
						$sql['QUERY'] = "
							SELECT *
							FROM rcg_nearby_attractions
							WHERE status != 'D'
							$con
						";

						foreach ($params as $p) {
							$sql['PARAM'][] = $p;
						}

						$result = $mycms->sql_select_paginated($indexVal, $sql, 20, $restrt);


						$result = $mycms->sql_select_paginated($indexVal, $sql, 20, $restrt);

						if ($result) {
							foreach ($result as $i => $row) {
								@$i++;
						?>
								<tr class="tlisting">
									<td align="center">
										<input type="checkbox" name="checkvalue" id="checkvalue" forType="checkBoxListing"
											value="<?= $row['id']; ?>" />
									</td>
									<td align="center"><?= $i + ($_REQUEST['_pgn_'] * 20) ?></td>
									<td><?= $row['name'] ?></td>
									<td><?= $row['location_url'] ?></td>
									<td><?= $row['distance_from_venue'] ?></td>
									<td><?= $row['time_to_reach'] ?></td>
									<td><?= $row['image'] ?></td>
									<td><?= ($row['status'] == 'A') ? 'Active' : 'Inactive'; ?></td>

									<td align="center">
										<a href="javascript:void(null);" onClick="redirectionOfLink(this)" ehref="<?= $_SERVER['PHP_SELF'] ?>?show=edit&id=<?= $row['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'edit.png' ?>" title="Edit" class="icon-pen">
										</a>
										&nbsp;
										<a href="<?= $cfg['SECTION_BASE_URL'] ?>manage_attraction.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>">
											<span src="<?= $cfg['THEME_IMAGE_PATH'] . 'drop.png' ?>" title="Remove" class="icon-trash-stroke"
												onClick="return confirm('Do you really want to remove this record');">
										</a>
										<br>
									</td>
								</tr>
							<?php }
						} else { ?>
							<tr>
								<td colspan="8" align="center">
									<span class="mandatory">No Record.</span>
								</td>
							</tr>
						<?php } ?>
					</table>
				</td>
			</tr>

			<tr>
				<td width="50%" align="left">
					<select name="multiOperationSelector" id="multiOperationSelector">
						<option value="">Choose an action...</option>
						<option value="Active">Active</option>
						<option value="Inactive">Inactive</option>
						<option value="Remove">Remove</option>
					</select>
					<input value="Apply to selected"  name="submit" type="button" 
						 onclick="return multiTaskValidation('<?= $cfg['SECTION_BASE_URL'] ?>manage_attraction.process.php','<?= $searchString ?>');" class="btn btn-small btn-blue"/> 
				</td>
				<td width="50%" align="right">
					<a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>">Create New Record</a>
				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
				</td>
			</tr>
		</table>
	</form>
<?php
}

/*================= ADD COUNTRY WINDOW ===============*/
function addAttraction($cfg, $mycms)
{
	global $searchArray, $searchString;
?>
	<form name="frmtypeadd" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_attraction.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
		<input type="hidden" name="act" value="store" />
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
					<td colspan="2" align="left" class="tcat">Create Nearby Attraction</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="2" align="left">Details</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Name <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="name" id="name"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Location Url <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="location_url" id="location_url"
										class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Distance from venue <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="distance_from_venue" id="distance_from_venue" class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Time to reach <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="time_to_reach" id="time_to_reach" class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Image <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="file" name="image" id="image" class="validate[required]" style="width:80%;" required="" />
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
<?php
}

/*================= EDIT COUNTRY WINDOW ==============*/
function editAttraction($cfg, $mycms)
{
	global $searchArray, $searchString;

	$sql 	=	array();
	$sql['QUERY']   = "SELECT * FROM `rcg_nearby_attractions`  
						   WHERE `id` = ?";
	$sql['PARAM'][]	=	array('FILD' => 'id', 'DATA' => $_REQUEST['id'], 'TYP' => 's');
	$result			= $mycms->sql_select($sql);
	$row    		= $result[0];
    // rcg_nearby_attractions
?>
	<form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>manage_attraction.process.php" id="frmtypeedit" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
		<input type="hidden" name="act" value="edit" />
		<input type="hidden" name="id" id="id" value="<?= $_REQUEST['id'] ?>" />
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
					<td colspan="2" align="left" class="tcat">Edit Nearby Attraction</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2" align="center" style="margin:0px; padding:0px;">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="2" align="left">Details</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Name <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="name" id="name"
										class="validate[required]" style="width:80%;" value="<?= $row['name']; ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Location URL <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="location_url" id="location_url"
										class="validate[required]" style="width:80%;" value="<?= $row['location_url']; ?>" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Distance from venue <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="distance_from_venue" value="<?= $row['distance_from_venue']; ?>" id="distance_from_venue" class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Time to reach <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="text" name="time_to_reach" id="time_to_reach" value="<?= $row['time_to_reach']; ?>" class="validate[required]" style="width:80%;" required="" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="left">
									Image <span class="mandatory">*</span>
								</td>
								<td width="60%" align="left">
									<input type="file" name="image" id="image" value="" class="validate[required]" style="width:80%;" required="" />
									<input type="hidden" name="prev_image" id="prev_image" value="<?= $row['image']; ?>" style="width:80%;"/>
								</td>
							</tr>
						</table>

					</td>
				</tr>
				<tr>
					<td width="40%"></td>
					<td align="left">
						<a href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>">
							<input type="button" name="BackEdit" id="BackEdit" value="Back" class="btn btn-small btn-red" /></a>
						&nbsp;
						<input type="submit" name="Save" id="Save" value="Save" class="btn btn-small btn-blue" />
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
?>