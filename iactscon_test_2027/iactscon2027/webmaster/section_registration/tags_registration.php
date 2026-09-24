<?php
include_once('includes/init.php');

page_header("Tag Registration");

$indexVal          = 1;
$pageKey           = "_pgn" . $indexVal . "_";

$pageKeyVal        = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString     = "";
$searchArray       = array();

$searchArray[$pageKey]                     = $pageKeyVal;
$searchArray['src_email_id']               = addslashes(trim($_REQUEST['src_email_id']));
$searchArray['src_access_key']             = addslashes(trim($_REQUEST['src_access_key'], '#'));
$searchArray['src_mobile_no']              = addslashes(trim($_REQUEST['src_mobile_no']));
$searchArray['src_user_full_name']         = addslashes(trim($_REQUEST['src_user_full_name']));
$searchArray['src_registration_tag']       = addslashes(trim($_REQUEST['src_registration_tag']));
$searchArray['src_atom_transaction_ids']   = trim($_REQUEST['src_atom_transaction_ids']);
$searchArray['src_transaction_ids']        = trim($_REQUEST['src_transaction_ids']);
$searchArray['src_conf_reg_category']      = trim($_REQUEST['src_conf_reg_category']);
$searchArray['src_registration_id']        = trim($_REQUEST['src_registration_id']);

foreach ($searchArray as $searchKey => $searchVal) {
	if ($searchVal != "") {
		$searchString .= "&" . $searchKey . "=" . $searchVal;
	}
}
?>

<div class="container">
	<?php
	switch ($show) {

		// VIEW GENERAL REGISTRATION WINDOW
		case 'view':

			viewGeneralRegistration($cfg, $mycms);
			break;

		// EDIT GENERAL REGISTRATION WINDOW
		case 'edit':

			editUserProfile($cfg, $mycms);
			break;

		//////////////////////////////////////
		case 'tagMaster':

			tagMasterListing($cfg, $mycms);
			break;

		case 'addTag':
			addTagFormWindow($cfg, $mycms);
			break;

		case 'editTag':
			editTagFormWindow($cfg, $mycms);
			break;

		//////////////////////////////////////

		// SHOW ALL GENERAL REGISTRATION WINDOW
		default:

			viewAllGeneralRegistration($cfg, $mycms);
			break;
	}
	?>
</div>
<?php

page_footer();

/****************************************************************************/
	/*                      SHOW ALL GENERAL REGISTRATION                       */
/****************************************************************************/
function viewAllGeneralRegistration($cfg, $mycms)
{
	global $searchArray, $searchString;
	include_once('../../includes/function.delegate.php');
	include_once('../../includes/function.accompany.php');
	$loggedUserId		= $mycms->getLoggedUserId();

	// FETCHING LOGGED USER DETAILS

	$sqlSystemUser				 =	array();
	$sqlSystemUser['QUERY']      = "SELECT * 
										  FROM " . _DB_CONF_USER_ . " 
		                                 WHERE `a_id` = ?";
	$sqlSystemUser['PARAM'][]   = array('FILD' => 'a_id',             'DATA' => $loggedUserId,   	'TYP' => 's');
	$resultSystemUser   = $mycms->sql_select($sqlSystemUser);
	$rowSystemUser      = $resultSystemUser[0];

	$searchApplication  = 0;
?>
	<form name="frmSearch" method="post" action="tags_registration.process.php" onSubmit="return FormValidator.validate(this);">
		<input type="hidden" name="act" value="search_registration_tag" />
		<table width="100%" class="tborder" align="center">
			<tr>
				<td class="tcat" colspan="2" align="left">
					<span style="float:left">Tag Registration</span>
					<span class="tsearchTool" forType="tsearchTool"></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">

					<div class="tsearch" style="display:block;">
						<table width="100%">
							<tr>
								<td align="left" width="150">User Full Name:</td>
								<td align="left" width="250">
									<input type="text" name="src_user_full_name" id="src_user_full_name"
										style="width:90%; text-transform:uppercase;" value="<?= $_REQUEST['src_user_full_name'] ?>" />
								</td>
								<td align="left" width="150">Unique Sequence:</td>
								<td align="left" width="250">
									<input type="text" name="src_access_key" id="src_access_key"
										style="width:90%; text-transform:uppercase;" value="<?= $_REQUEST['src_access_key'] ?>" />
								</td>
								<td align="right" rowspan="5">
									<?php
									searchStatus();
									?>
									<input type="submit" name="goSearch" value="Search"
										class="btn btn-small btn-blue" />
								</td>
							</tr>
							<tr>
								<td align="left">Email Id:</td>
								<td align="left">
									<input type="text" name="src_email_id" id="src_email_id"
										style="width:90%;" value="<?= $_REQUEST['src_email_id'] ?>" />
								</td>
								<td align="left">Mobile No:</td>
								<td align="left">
									<input type="text" name="src_mobile_no" id="src_mobile_no"
										style="width:90%; text-transform:uppercase;" value="<?= $_REQUEST['src_mobile_no'] ?>" />
								</td>
							</tr>
						</table>
					</div>

					<table width="100%" shortData="on">
						<thead>
							<tr style="background-color:antiquewhite;">
								<th width="40" align="center" data-sort="int">Sl No</th>
								<th align="left">Name & Contact</th>
								<th width="120" align="center" data-sort="int">Unique Sequence </th>
								<th width="110" align="left">Registration Type</th>
								<th width="130" align="left">Registration Details</th>
								<th width="250" align="center">Tag Details</th>
								<th width="90" align="center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							@$searchCondition       = "";
							$searchCondition       .= " AND delegate.operational_area != 'EXHIBITOR'
								 							AND delegate.registration_request != 'GUEST'
															AND delegate.account_status='REGISTERED'
															AND delegate.status = 'A'
														    AND delegate.isRegistration = 'Y'";

							if ($_REQUEST['src_email_id'] != '') {
								$searchCondition   .= " AND delegate.user_email_id LIKE '%" . $_REQUEST['src_email_id'] . "%'";
							}
							if ($_REQUEST['src_access_key'] != '') {
								$searchCondition   .= " AND delegate.user_unique_sequence LIKE '%" . $_REQUEST['src_access_key'] . "%'";
							}
							if ($_REQUEST['src_mobile_no'] != '') {
								$searchCondition   .= " AND delegate.user_mobile_no LIKE '%" . $_REQUEST['src_mobile_no'] . "%'";
							}
							if ($_REQUEST['src_user_full_name'] != '') {
								$searchCondition   .= " AND delegate.user_full_name LIKE '%" . $_REQUEST['src_user_full_name'] . "%'";
							}
							if ($_REQUEST['src_registration_tag'] != '') {
								$searchCondition   .= " AND delegate.tags LIKE '" . $_REQUEST['src_registration_tag'] . "'";
							}
							if ($_REQUEST['src_transaction_ids'] != '') {
								$searchApplication	= 1;
								$searchCondition   .= " AND LOCATE('" . $_REQUEST['src_transaction_ids'] . "', totalInvoicePayment.atomTransactionIds) > 0";
							}
							if ($_REQUEST['src_atom_transaction_ids'] != '') {
								$searchApplication	= 1;
								$searchCondition   .= " AND LOCATE('" . $_REQUEST['src_atom_transaction_ids'] . "', totalInvoicePayment.atomAtomTransactionIds) > 0";
							}
							if ($_REQUEST['src_conf_reg_category'] != '') {
								$searchCondition   .= " AND delegate.registration_classification_id = '" . $_REQUEST['src_conf_reg_category'] . "'";
							}
							if ($_REQUEST['src_registration_id'] != '') {
								$searchCondition   .= " AND (delegate.user_registration_id LIKE '%" . $_REQUEST['src_registration_id'] . "%' 
									                             AND (delegate.registration_payment_status = 'ZERO_VALUE' 
															          OR delegate.registration_payment_status = 'COMPLIMENTARY'
																	  OR delegate.registration_payment_status = 'PAID'))";
							}
							$sqlFetchUser         = "";

							$idArr = getAllDelegates("", "", $searchCondition);
							if ($idArr) {
								foreach ($idArr as $i => $id) {
									$status = true;
									$rowFetchUser = getUserDetails($id);
									$counter             = $counter + 1;

									$totalAccompanyCount = 0;
									$totalAccompanyCount = getTotalAccompanyCount($rowFetchUser['id']);
									//if($rowFetchUser['classification_title']=='Delegates'){

							?>
									<tr class="tlisting">
										<td align="center" valign="top"><?= $counter + ($_REQUEST['_pgn1_'] * 10) ?></td>
										<td align="left" valign="top">
											<?= strtoupper($rowFetchUser['user_title']) ?>
											<?= strtoupper($rowFetchUser['user_first_name']) ?>
											<?= strtoupper($rowFetchUser['user_middle_name']) ?>
											<?= strtoupper($rowFetchUser['user_last_name']) ?>

											<br />
											<?= $rowFetchUser['user_mobile_isd_code'] . $rowFetchUser['user_mobile_no'] ?>
											<br />
											<span style="color:#3300FF;"><?= $rowFetchUser['user_email_id'] ?></span>
										</td>
										<td align="center" valign="top"><?php
																		if (
																			$rowFetchUser['registration_payment_status'] == "PAID"
																			|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
																			|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
																		) {
																			echo $rowFetchUser['user_unique_sequence'];

																			echo "<br />";
																		} else {
																			echo "-";
																			echo "<br />";
																		}
																		?>
										</td>
										<td align="left" valign="top">
											<?php
											if ($rowFetchUser['isRegistration'] == "Y") {
												echo $rowFetchUser['classification_title'];
												echo "<br />";
												echo $rowFetchUser['cutoffTitle'];
											}
											?>
										</td>
										<td align="left" valign="top">
											<?php
											if (
												$rowFetchUser['registration_payment_status'] == "PAID"
												|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
												|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
											) {
												echo $rowFetchUser['user_registration_id'];
												echo "<br />";
											} else {
												echo "-";
												echo "<br />";
											}
											?>
											<?= date('d/m/Y h:i A', strtotime($rowFetchUser['created_dateTime'])) ?>
										</td>
										<td align="center" valign="top">
											<?
											$array = $rowFetchUser['tags'];
											//print_r($array);
											$var = (explode(",", $array));
											foreach ($var as $key => $val) {
												$sqlTagListing			  = array();
												$sqlTagListing['QUERY']  = "SELECT * FROM " . _DB_TAG_MASTER_ . " WHERE `tag_name` = '" . $val . "'";
												$resultTagListing   	  = $mycms->sql_select($sqlTagListing);
												$rowTag 		  = $resultTagListing[0];
												// if ($val == 'IAA Member') {
											?>
													<span style="color:<?= $rowTag['color'] ?>"><b><?= $val ?></b></span>
													<br>
												<?
												
												
											}
											?>
										</td>

										<td align="center" valign="top">
											<a href="tags_registration.php?show=view&id=<?= $rowFetchUser['id'] ?>">
												<span title="View" class="icon-eye" /></a>
										</td>
									</tr>
								<?php
								}
							}
							//}
							else {
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

				</td>
			</tr>
			<tr class="tfooter">
				<td colspan="2">
					<span class="paginationRecDisplay"><?= $mycms->paginateRecInfo(1) ?></span>
					<span class="paginationDisplay"><?= $mycms->paginate(1, 'pagination') ?></span>
				</td>
			</tr>
		</table>
	</form>
<?php
}



function tagMasterListing($cfg, $mycms) // LIST OF VENUE MASTER
{
	global $cfg, $mycms;
?>
	<div class="body_content_box">
		<div class="con_box-grd row">
			<div class="form-group">
				<h2>Tag List</h2>
			</div>
			<div class="table_wrap">
				<table width="100%" class="tborder">
					<thead>
						<tr class="tcat">
							<th class="action">Sl. No.</th>
							<th>Tag Title</th>
							<th class="action">Status</th>
							<th class="action">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sqlHallListing			 = array();
						$sqlHallListing = array();
						$sqlHallListing['QUERY'] = "SELECT * FROM " . _DB_TAG_MASTER_ . " WHERE `status` != ?";

						$sqlHallListing['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'D',  'TYP' => 's');

						$resultHallListing   	 = $mycms->sql_select($sqlHallListing);
						$Counter			 	 = 0;
						if ($resultHallListing) {
							foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
								$Counter++;
						?>
								<tr class="tlisting">
									<td align="center"><?= $Counter ?></td>
									<td align="center" style="color:<?= $rowHallListing['color'] ?>"><b><?= $rowHallListing['tag_name'] ?></b></td>
									<td align="center"><a href="tags_registration.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'InactiveTag' : 'ActiveTag') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'ticket ticket-success' : 'ticket ticket-important') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a></td>
									<td align="center">
										<a href="tags_registration.php?show=editTag&id=<?= $rowHallListing['id'] ?>">
											<span title="Edit" class="icon-pen" />
										</a>
									</td>
								</tr>
						<?
							}
						}
						?>
						<tr class="tfooter">
							<td align="right" colspan="5"><a href="javascript:void(null);" onclick="redirectionOfLink(this)" ehref="tags_registration.php?show=addTag">+Add New Tag</a><br>

								<span class="paginationRecDisplay"></span>
								<span class="paginationDisplay"></span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- <a title="add" class="stick_add" href="tags_registration.php?show=addTag"><i class="fas fa-plus"></i></a> -->
		</div>
	</div>
<?
}

function addTagFormWindow($cfg, $mycms)
{
?>
	<form name="frmInsertSession" id="frmInsertSession" action="tags_registration.process.php" method="post">
		<input type="hidden" name="act" value="insertTag" />
		<table width="50%" class="tborder">
			<tr>
				<td class="tcat" align="left">Insert Tag</td>
			</tr>
			<tr>
				<td align="center" style="margin:0px; padding:0px;">

					<table width="100%">
						<tr>
							<td align="left" valign="top">
								Tag Title
								<span class="mandatory">*</span>
							</td>
							<td align="left" valign="top">
								<input type="text" name="tag_name" id="tag_name" style="width:60%; " required>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top">
								Color
								<span class="mandatory">*</span>
							</td>
							<td align="left" valign="top">
								<input type="color" name="color" id="color" style="width:60%; ">
							</td>
						</tr>
						<tr>
							<td align="right"></td>
							<td align="left">
								<input name="back" type="button" class="btn btn-small btn-red" id="Back" value="Back" onClick="location.href='tags_registration.php?show=tagMaster';" />
								&nbsp;
								<input type="submit" name="Save" id="Save" value="Insert Venue" class="btn btn-small btn-blue">
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

function editTagFormWindow($cfg, $mycms)
{
	$sessionId				  = addslashes(trim($_REQUEST['id']));

	$sqlHallListing			  = array();
	$sqlHallListing['QUERY']  = "SELECT * FROM " . _DB_TAG_MASTER_ . " WHERE `id` = '" . $sessionId . "'";
	$resultHallListing   	  = $mycms->sql_select($sqlHallListing);
	$rowProgramSession 		  = $resultHallListing[0];
?>
	<form name="frmUpdateSession" id="frmUpdateSession" action="tags_registration.process.php" method="post">
		<input type="hidden" name="act" value="updateTagMaster" />
		<input type="hidden" name="id" value="<?= $rowProgramSession['id'] ?>" />
		<table width="50%" class="tborder">
			<tr>
				<td class="tcat" align="left">Update Tag</td>
			</tr>
			<tr>
				<td align="center" style="margin:0px; padding:0px;">

					<table width="100%">
						<tr>
							<td width="30%" align="left" valign="top">
								Tag Title
								<span class="mandatory">*</span>
							</td>
							<td align="left" valign="top">
								<input type="text" id="tag_name" name="tag_name" value="<?= $rowProgramSession['tag_name'] ?>" required />
							</td>
						</tr>
						<tr>
							<td align="left" valign="top">
								Color
								<span class="mandatory"></span>
							</td>
							<td align="left" valign="top">
								<input type="color" name="color" id="color" value="<?= $rowProgramSession['color'] ?>" style="width:30%; ">
							</td>
						</tr>
						<tr>
							<td align="right"></td>
							<td align="left">
								<input name="back" type="button" class="btn btn-small btn-red" id="Back" value="Back" onClick="location.href='tags_registration.php?show=tagMaster';" />
								&nbsp;
								<input type="submit" name="Save" id="Save" value="Update Tag" class="btn btn-small btn-blue">
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


function viewGeneralRegistration($cfg, $mycms)
{
	$requestPage  = "tags_registration.php";
	$processPage  = "tags_registration.process.php";

	tagsRegistrationViewFormTemplate($requestPage, $processPage);
}


function tagsRegistrationViewFormTemplate($requestPage, $processPage, $showDeleted = false)
{
	global $cfg, $mycms;
	include_once('../../includes/function.delegate.php');
	$delegateId               = addslashes(trim($_REQUEST['id']));

	$rowFetchUser             = registrationDetailsQuery($delegateId);

	$loggedUserId		      = $mycms->getLoggedUserId();

	// FETCHING LOGGED USER DETAILS
	$sqlSystemUser					=	array();
	$sqlSystemUser['QUERY']      	  = "SELECT * FROM " . _DB_CONF_USER_ . " 
		                                     WHERE `a_id` = ?";
	$sqlSystemUser['PARAM'][]	=	array('FILD' => 'a_id', 	  'DATA' => $loggedUserId,             'TYP' => 's');
	$resultSystemUser         = $mycms->sql_select($sqlSystemUser);
	$rowSystemUser            = $resultSystemUser[0];
?>
	<table width="100%" align="center" class="tborder">
		<tbody>

			<tr>
				<td colspan="2" style="margin:0px; padding:0px;">

					<table width="100%">
						<tr class="thighlight">
							<td colspan="4" align="left" style="background-color:#FFCC99; border-bottom-color:#330066; color:#000000;">User Login Details</td>
						</tr>
						<tr>
							<td width="20%" align="left" valign="top">Email Id:</td>
							<td width="30%" align="left" valign="top"><?= $rowFetchUser['user_email_id'] ?></td>
							<td width="20%" align="left" valign="top"></td>
							<td width="30%" align="left" valign="top"></td>
						</tr>
						<tr>
							<td align="left">Registration Id:</td>
							<td align="left">
								<?php


								if (
									$rowFetchUser['registration_payment_status'] == "PAID"
									|| $rowFetchUser['registration_payment_status'] == "COMPIEMENTARY"
									|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
									|| $rowFetchUser['workshop_payment_status'] == "PAID"
									|| $rowFetchUser['workshop_payment_status'] == "COMPLIMENTARY"
									|| $rowFetchUser['workshop_payment_status'] == "ZERO_VALUE"
								) {
									echo $rowFetchUser['user_registration_id'];
								} else {
									echo "-";
								}
								?>
							</td>
							<td align="left">Unique Sequence:</td>
							<td align="left">
								<?
								if (
									$rowFetchUser['registration_payment_status'] == "PAID"
									|| $rowFetchUser['registration_payment_status'] == "COMPLIMENTARY"
									|| $rowFetchUser['registration_payment_status'] == "ZERO_VALUE"
								) {
									echo $rowFetchUser['user_unique_sequence'];
								} else {
									echo "-";
								}
								?>
							</td>
						</tr>
					</table>

					<form name="tagEdit" id="tagEdit" action="<?= $cfg['SECTION_BASE_URL'] . $processPage ?>"
						enctype="multipart/form-data" method="post" onsubmit="return validation.editProfile();">

						<input type="hidden" name="act" value="tag_add" />
						<input type="hidden" name="user_id" id="user_id" value="<?= $rowFetchUser['id'] ?>">

						<table width="100%">
							<tr class="thighlight">
								<td colspan="4" align="left" style="background-color:#FFCC99; border-bottom-color:#330066; color:#000000;">User Details</td>
							</tr>
							<tr>
								<td width="20%" align="left">Name:</td>
								<td width="30%" align="left">
									<?= strtoupper($rowFetchUser['user_first_name']) ?>
									<?= strtoupper($rowFetchUser['user_middle_name']) ?>
									<?= strtoupper($rowFetchUser['user_last_name']) ?>
								</td>
								<td align="left">Address:</td>
								<td align="left" rowspan="2" valign="top"><?= strtoupper($rowFetchUser['user_address']) ?></td>

							</tr>
							<tr>
								<td align="left">Country:</td>
								<td align="left" valign="top"><?= strtoupper($rowFetchUser['country_name']) ?></td>
							</tr>
							<tr>

								<td align="left">State:</td>
								<td align="left"><?= strtoupper($rowFetchUser['state_name']) ?></td>
								<td align="left">&nbsp;</td>
							</tr>
							<tr>

								<td align="left">City:</td>
								<td align="left"><?= strtoupper($rowFetchUser['user_city']) ?></td>
							</tr>
							<tr>

								<td align="left">Postal Code:</td>
								<td align="left"><?= strtoupper($rowFetchUser['user_pincode']) ?></td>
							</tr>
							<tr>
								<td align="left">Mobile:</td>
								<td align="left"><?= $rowFetchUser['user_mobile_isd_code'] . $rowFetchUser['user_mobile_no'] ?></td>

							</tr>
						</table>

						<table width="100%">
							<tr class="thighlight">
								<td colspan="7" align="left" style="background-color:#FFCC99; border-bottom-color:#330066; color:#000000;">Tag Details</td>
							</tr>

							<?php

							$tagArray = explode(",", $rowFetchUser['tags']);

							$sqlTagListing			 = array();
							$sqlTagListing = array();
							$sqlTagListing['QUERY'] = "SELECT * FROM " . _DB_TAG_MASTER_ . " WHERE `status` = ?";

							$sqlTagListing['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'A',  'TYP' => 's');

							$resultTagListing   	 = $mycms->sql_select($sqlTagListing);
							$Counter			 	 = 0;
							if ($resultTagListing) {
							?>
								<tr>
									<td width="20%" align="left">
										<?php

										foreach ($resultTagListing as $keyTagListing => $rowTagListing) {
											$Counter++;

										?>
											<div style="float:left; width:25%; margin-top:3px;">
												<input type="checkbox" id="tag" name="tag[]" value="<?= $rowTagListing['tag_name'] ?>" autocomplete="off" <?= (in_array($rowTagListing['tag_name'], $tagArray)) ? 'checked="checked"' : '' ?>> &nbsp;<?= $rowTagListing['tag_name'] ?>
											</div>
										<?php
										} ?>
										<!-- <div style="float:left; width:25%; margin-top:3px;">
											<input type="checkbox" id="tag" name="tag[]" value="International Faculty" autocomplete="off" <?= (in_array("International Faculty", $tagArray)) ? 'checked="checked"' : '' ?>> &nbsp;International Faculty
										</div>
										<div style="float:left; width:25%; margin-top:3px;">
											<input type="checkbox" id="tag" name="tag[]" value="Organizing Committee Member" autocomplete="off" <?= (in_array("Organizing Committee Member", $tagArray)) ? 'checked="checked"' : '' ?>> &nbsp;Organizing Committee Member
										</div> -->
									</td>
								</tr>
							<?php } ?>
							<tr>
								<?php
								$achievementArray = explode(",", $rowFetchUser['achievement']);


								?>

							</tr>
						</table>
						<table width="100%">
							<tr>
								<td align="left"></td>
								<td align="left">
								</td>
								<td align="left">
									<input type="button" name="Back" id="Back" value="Back"
										class="btn btn-small btn-red" onClick="location.href='<?= $requestPage ?>';" />

									<input type="submit" name="editprofile" id="editprofile" value="Save"
										class="btn btn-small btn-blue" />
								</td>
								<td align="left"></td>
							</tr>
						</table>

					</form>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="tfooter">&nbsp;</td>
			</tr>

		</tbody>
	</table>
<?php
}
?>