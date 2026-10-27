<?php
include_once('includes/init.php');

page_header("Organising Committee");

$pageKey = "_pgn_";
$pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

@$searchString = "";
$searchArray  = array();

$searchArray[$pageKey]   = $pageKeyVal;
$searchArray['name']     = $_REQUEST['name'];
$searchArray['type']     = $_REQUEST['type'];

foreach ($searchArray as $searchKey => $searchVal) {
    $searchString .= "&" . $searchKey . "=" . $searchVal;
}
?>
<script language="javascript" src="<?= _BASE_URL_ ?>JS/jquery.js"></script>

<div class="container">
    <?php
    switch ($show) {
        case 'add':
            addCommittee($cfg, $mycms);
            break;
        case 'edit':
            editCommittee($cfg, $mycms);
            break;
        default:
            showAllCommittee($cfg, $mycms);
            break;
    }
    ?>
</div>

<?php page_footer(); ?>


<?php
/* ======================================================
   LISTING
   ====================================================== */
function showAllCommittee($cfg, $mycms)
{
    global $searchArray, $searchString;
?>
    <form name="frmCommListing" action="<?= $cfg['SECTION_BASE_URL'] ?>organising_committee.php"
        method="post">
        <input type="hidden" name="act" id="act" value="search" />

        <table width="100%" align="center" class="tborder">
            <tr>
                <td colspan="2" class="tcat">
                    <span style="float:left">Organising Committee Listing</span>
                    <span class="tsearchTool" forType="tsearchTool"></span>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="tsearch">
                        <table width="100%">
                            <tr>
                                <td align="right">
                                    Type:
                                    <select name="type" id="type">
                                        
                                        <option value="">All</option>
                                        <option value="patrons" <?= ($_REQUEST['type'] == "patrons") ? "selected" : "" ?>>Patrons</option>
                                        <option value="core committee" <?= ($_REQUEST['type'] == "core committee") ? "selected" : "" ?>>Core Committee</option>
                                        <option value="steering group" <?= ($_REQUEST['type'] == "steering group") ? "selected" : "" ?>>Steering Group</option>
                                        <option value="central committee" <?= ($_REQUEST['type'] == "central committee") ? "selected" : "" ?>>Central Committee </option>
                                        <option value="scientific committee" <?= ($_REQUEST['type'] == "scientific committee") ? "selected" : "" ?>>Scientific Committee </option>
                                         <option value="national advisory board" <?= ($_REQUEST['type'] == "national advisory board") ? "selected" : "" ?>>NATIONAL ADVISORY BOARD</option>
                                           <option value="local advisory board" <?= ($_REQUEST['type'] == "local advisory board") ? "selected" : "" ?>>LOCAL ADVISORY BOARD:</option>
                                            <option value="residents advisory board" <?= ($_REQUEST['type'] == "residents advisory board") ? "selected" : "" ?>>RESIDENTS Advisory BOARD</option>
                                            <option value="adult cardiac track" <?= ($_REQUEST['type'] == "adult cardiac track") ? "selected" : "" ?>>Local Committe (ADULT CARDIAC TRACK)</option>

<option value="congenital track" <?= ($_REQUEST['type'] == "congenital track") ? "selected" : "" ?>>Local Committe (CONGENITAL TRACK)</option>

<option value="mics/ robotics track" <?= ($_REQUEST['type'] == "mics/ robotics track") ? "selected" : "" ?>>Local Committe (MICS/ ROBOTICS TRACK)</option>

<option value="transplant track" <?= ($_REQUEST['type'] == "transplant track") ? "selected" : "" ?>>Local Committe (TRANSPLANT TRACK)</option>

<option value="aortic track" <?= ($_REQUEST['type'] == "aortic track") ? "selected" : "" ?>>Local Committe (AORTIC TRACK)</option>

<option value="thoracic and vascular track" <?= ($_REQUEST['type'] == "thoracic and vascular track") ? "selected" : "" ?>>Local Committe (THORACIC AND VASCULAR TRACK)</option>

                                           

                                         
                                    </select>

                                    &nbsp; Name:
                                    <input type="text" name="name" id="name" value="<?= $_REQUEST['name'] ?>" />

                                    <?php searchStatus(); ?>
                                    <input type="submit" value="Search" class="btn btn-small btn-blue" />
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
                            <td width="40" align="center">Sl No</td>
                            <td width="100">Type</td>
                            <td width="150">Name</td>
                            <td width="150">Designation</td>
                            <td width="120">Country</td>
                            <td width="100" align="center">Image</td>
                            <td width="70" align="center">Status</td>
                            <td width="40" align="center">Action</td>
                        </tr>

                        <?php
                        $con = "";
                        $params = [];

                        if (!empty($_REQUEST['type'])) {
                            $con .= " AND `a`.`type` = ?";
                            $params[] = ['FILD' => 'type', 'DATA' => $_REQUEST['type'], 'TYP' => 's'];
                        }

                        if (!empty($_REQUEST['name'])) {
                            $con .= " AND LOWER(`a`.`name`) LIKE ?";
                            $params[] = [
                                'FILD' => 'name',
                                'DATA' => '%' . strtolower(trim($_REQUEST['name'])) . '%',
                                'TYP' => 's'
                            ];
                        }

                        $sql = [];
                        $sql['QUERY'] = "
                        SELECT a.*, c.country_name 
                        FROM rcg_organising_committee a
                        LEFT JOIN comn_country c ON c.country_id = a.country_id
                        WHERE a.status != 'D' $con
                    ";

                        foreach ($params as $p) {
                            $sql['PARAM'][] = $p;
                        }

                        $result = $mycms->sql_select_paginated($indexVal, $sql, 20, $restrt);

                        if ($result) {
                            foreach ($result as $i => $row) {
                                $i++;
                        ?>
                                <tr class="tlisting">
                                    <td align="center">
                                        <input type="checkbox" name="checkvalue" forType="checkBoxListing"
                                            value="<?= $row['id']; ?>" />
                                    </td>

                                    <td align="center"><?= $i + ($_REQUEST['_pgn_'] * 20) ?></td>

                                    <td><?= ucfirst($row['type']) ?></td>
                                    <td><?= $row['name'] ?></td>
                                    <td><?= $row['designation'] ?></td>
                                    <td><?= $row['country_name'] ?></td>

                                    <td align="center">
                                        <?php if ($row['image']) { ?>
                                            <img src="<?= $cfg['SECTION_BASE_URL'] ?><?= $row['image'] ?>" width="60" />
                                        <?php } ?>
                                    </td>

                                    <td><?= ($row['status'] == 'A') ? 'Active' : 'Inactive' ?></td>

                                    <td align="center">
                                        <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&id=<?= $row['id'] ?><?= $searchString ?>">
                                            <span class="icon-pen"></span>
                                        </a>
                                        &nbsp;
                                        <a href="<?= $cfg['SECTION_BASE_URL'] ?>organising_committee.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>"
                                            onclick="return confirm('Do you really want to remove this record?');">
                                            <span class="icon-trash-stroke"></span>
                                        </a>
                                    </td>

                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="9" align="center"><span class="mandatory">No Record.</span></td>
                            </tr>
                        <?php } ?>

                    </table>

                </td>
            </tr>

            <tr>
                <td>
                    <select name="multiOperationSelector" id="multiOperationSelector">
                        <option value="">Choose an action...</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Remove">Remove</option>
                    </select>
                    <input value="Apply to selected" type="button"
                        onclick="return multiTaskValidation('<?= $cfg['SECTION_BASE_URL'] ?>organising_committee.process.php','<?= $searchString ?>');"
                        class="btn btn-small btn-blue" />
                </td>

                <td align="right">
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


/* ======================================================
   ADD
   ====================================================== */
function addCommittee($cfg, $mycms)
{
    global $searchArray, $searchString;

    // COUNTRY LIST
    $sqlC = [];
    $sqlC['QUERY'] = "SELECT country_id, country_name FROM comn_country WHERE status='A' ORDER BY country_name";
    $countries = $mycms->sql_select($sqlC);
?>
    <form method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>organising_committee.process.php"
        enctype="multipart/form-data">
        <input type="hidden" name="act" value="store" />

        <?php foreach ($searchArray as $key => $val) { ?>
            <input type="hidden" name="<?= $key ?>" value="<?= $val ?>" />
        <?php } ?>

        <table width="50%" class="tborder">
            <thead>
                <tr>
                    <td colspan="2" align="left" class="tcat">Add Committee Member</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" align="center" style="margin:0px; padding:0px;">

                        <table width="100%">
                            <tr class="thighlight">
                                <td colspan="2" align="left">Member Details</td>
                            </tr>
                            <tr>
                                <td>Type <span class="mandatory">*</span></td>
                                <td>
                                    <select name="type" required style="width:83.6%;">
                                        <option value="">Select</option>
                                        <option value="patrons">Patrons</option>
                                        <option value="core committee">Core Committee</option>
                                         <option value="steering group">Steering Group</option>
                                         <option value="central committee">Central Committee</option>
                                         <option value="scientific committee">Scientific Committee</option>
                                         <option value="national advisory board">Scientific Committee (National Advisory Board)</option>
                                          <option value="local advisory board">Local Advisory Board</option>
                                          <option value="residents advisory board">RESIDENTS Advisory BOARD</option>
                                       <option value="adult cardiac track">Local Committe (ADULT CARDIAC TRACK)</option>
                                        <option value="congenital track">Local Committe (CONGENITAL TRACK)</option>
                                           <option value="mics/ robotics track">Local Committe (MICS/ ROBOTICS TRACK)</option>
                                        <option value="transplant track">Local Committe (TRANSPLANT TRACK)</option>
                                       <option value="aortic track">Local Committe (AORTIC TRACK)</option>
                                       <option value="thoracic and vascular track">Local Committe (THORACIC AND VASCULAR TRACK)</option>
                                             
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Name <span class="mandatory">*</span></td>
                                <td><input type="text" name="name" required maxlength="255" style="width:80%;" /></td>
                            </tr>

                            <tr>
                                <td>Designation</td>
                                <td><input type="text" name="designation" maxlength="255" style="width:80%;" /></td>
                            </tr>

                            <tr>
                                <td>Country</td>
                                <td>
                                    <select name="country_id" style="width:83.6%;">
                                        <option value="">Select</option>
                                        <?php foreach ($countries as $c): ?>
                                            <option value="<?= $c['country_id'] ?>"><?= $c['country_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>Image <span class="mandatory"></span></td>
                                <td><input type="file" name="image"  style="width:80%;" /></td>
                            </tr>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td width="40%"></td>
                    <td align="left">
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= $searchString ?>">
                            <input type="button" value="Back" class="btn btn-small btn-red">
                        </a>
                        &nbsp;
                        <input type="submit" value="Save" class="btn btn-small btn-blue">
                    </td>
                </tr>
                <tr class="tfooter">
                    <td colspan="2">&nbsp;</td>
                </tr> 
        </table>
    </form>
<?php
}


/* ======================================================
   EDIT
   ====================================================== */
function editCommittee($cfg, $mycms)
{
    global $searchArray, $searchString;

    // GET DATA
    $sql = [];
    $sql['QUERY'] = "SELECT * FROM rcg_organising_committee WHERE id = ?";
    $sql['PARAM'][] = ['FILD' => 'id', 'DATA' => $_REQUEST['id'], 'TYP' => 'i'];
    $result = $mycms->sql_select($sql);
    $row = $result[0];

    // COUNTRY LIST
    $sqlC = [];
    $sqlC['QUERY'] = "SELECT country_id, country_name FROM comn_country WHERE status='A' ORDER BY country_name";
    $countries = $mycms->sql_select($sqlC);
?>
    <form method="post" action="<?= $cfg['SECTION_BASE_URL'] ?>organising_committee.process.php"
        enctype="multipart/form-data">

        <input type="hidden" name="act" value="edit" />
        <input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>" />
        <input type="hidden" name="prev_image" value="<?= $row['image'] ?>" />

        <?php foreach ($searchArray as $key => $val) { ?>
            <input type="hidden" name="<?= $key ?>" value="<?= $val ?>" />
        <?php } ?>

        <table width="50%" class="tborder">
            <thead>
                <tr>
                    <td colspan="2" align="left" class="tcat">Edit Committee Member</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" align="center" style="margin:0px; padding:0px;">

                        <table width="100%">
                            <tr class="thighlight">
                                <td colspan="2" align="left">Workshop Details</td>
                            </tr>
                            <tr>
                                <td width="40%" align="left">Type <span class="mandatory">*</span></td>
                                <td width="60%" align="left">
                                    <!-- <select name="type"  style="width:83.6%;">
                                        <option value="">Select</option>
                                        <option value="patrons" <?= ($row['type'] == 'patrons') ? 'selected' : '' ?>>Patrons</option>
                                        <option value="core committee" <?= ($row['type'] == 'core committee') ? 'selected' : '' ?>>Core Committee</option>
                                        <option value="steering group" <?= ($row['type'] == 'steering group') ? 'selected' : '' ?>>Steering Group</option>
                                    </select> -->
                                    <select name="type" style="width:83.6%;">
    <option value="">Select</option>

    <option value="patrons" <?= ($row['type'] == 'patrons') ? 'selected' : '' ?>>
        Patrons
    </option>

    <option value="core committee" <?= ($row['type'] == 'core committee') ? 'selected' : '' ?>>
        Core Committee
    </option>

    <option value="steering group" <?= ($row['type'] == 'steering group') ? 'selected' : '' ?>>
        Steering Group
    </option>

    <option value="central committee" <?= ($row['type'] == 'central committee') ? 'selected' : '' ?>>
        Central Committee
    </option>

    <option value="scientific committee" <?= ($row['type'] == 'scientific committee') ? 'selected' : '' ?>>
        Scientific Committee
    </option>

    <option value="national advisory board" <?= ($row['type'] == 'national advisory board') ? 'selected' : '' ?>>
        Scientific Committee (National Advisory Board)
    </option>

    <option value="local advisory board" <?= ($row['type'] == 'local advisory board') ? 'selected' : '' ?>>
        Local Advisory Board
    </option>

    <option value="residents advisory board" <?= ($row['type'] == 'residents advisory board') ? 'selected' : '' ?>>
        RESIDENTS Advisory BOARD
    </option>

    <option value="adult cardiac track" <?= ($row['type'] == 'adult cardiac track') ? 'selected' : '' ?>>
        Local Committee (ADULT CARDIAC TRACK)
    </option>

    <option value="congenital track" <?= ($row['type'] == 'congenital track') ? 'selected' : '' ?>>
        Local Committee (CONGENITAL TRACK)
    </option>

    <option value="mics/ robotics track" <?= ($row['type'] == 'mics/ robotics track') ? 'selected' : '' ?>>
        Local Committee (MICS/ ROBOTICS TRACK)
    </option>

    <option value="transplant track" <?= ($row['type'] == 'transplant track') ? 'selected' : '' ?>>
        Local Committee (TRANSPLANT TRACK)
    </option>

    <option value="aortic track" <?= ($row['type'] == 'aortic track') ? 'selected' : '' ?>>
        Local Committee (AORTIC TRACK)
    </option>

    <option value="thoracic and vascular track" <?= ($row['type'] == 'thoracic and vascular track') ? 'selected' : '' ?>>
        Local Committee (THORACIC AND VASCULAR TRACK)
    </option>
</select>
                                </td>
                            </tr>

                            <tr>
                                <td width="40%" align="left">Name <span class="mandatory">*</span></td>
                                <td width="60%" align="left">
                                    <input type="text" name="name"  maxlength="255" style="width:80%;" value="<?= $row['name'] ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td width="40%" align="left">Designation</td>
                                <td width="60%" align="left">
                                    <input type="text" name="designation" maxlength="255" style="width:80%;" value="<?= $row['designation'] ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td width="40%" align="left">Country</td>
                                <td width="60%" align="left">
                                    <select name="country_id" style="width:83.6%;">
                                        <option value="">Select</option>
                                        <?php foreach ($countries as $c): ?>
                                            <option value="<?= $c['country_id'] ?>" <?= ($row['country_id'] == $c['country_id']) ? 'selected' : '' ?>>
                                                <?= $c['country_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td width="40%" align="left">Image</td>
                                <td width="60%" align="left">
                                    <input type="file" name="image" style="width:80%;"/>
                                    <br>
                                    <?php if ($row['image']) { ?>
                                        <img src="<?= $row['image'] ?>" width="90" />
                                    <?php } ?>
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
?>