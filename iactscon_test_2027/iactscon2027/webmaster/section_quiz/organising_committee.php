<?php
include_once("../includes/source.php");
include_once('../includes/init.php');
include_once('../includes/function.workshop.php');
?>

<body>
    <?php include_once("../includes/left-menu.php"); ?>
    <header>
        <h2>Organising Committee</h2>
        <?php include_once("../includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $pageKey    = "_pgn_";
        $pageKeyVal = (empty($_REQUEST[$pageKey])) ? 0 : $_REQUEST[$pageKey];

        $searchString = "";
        $searchArray  = array();
        $searchArray[$pageKey] = $pageKeyVal;
        $searchArray['name']   = $_REQUEST['name'] ?? '';
        $searchArray['type']   = $_REQUEST['type'] ?? '';

        foreach ($searchArray as $searchKey => $searchVal) {
            $searchString .= "&" . $searchKey . "=" . $searchVal;
        }

        $show = $_REQUEST['show'] ?? '';
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Quiz</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Organising Committee</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'add') ? 'Add Committee Member' : (($show == 'edit') ? 'Edit Committee Member' : 'Organising Committee Listing') ?></h2>
                <h6>Manage patrons, committees and advisory board members.</h6>
            </div>
            <?php if ($show == '') { ?>
            <div class="page_top_wrap_right">
                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>" class="badge_primary"><i class="fal fa-plus"></i> Create New Record</a>
            </div>
            <?php } ?>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
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
        </div>
    </div>
</body>
<?php include_once("../includes/js-source.php"); ?>
<script>
    $(document).on('click', '.deleteCommitteeBtn', function () {
        if (confirm('Do you really want to remove this record?')) {
            window.location.href = $(this).data('href');
        }
    });
    $('#applyMultiActionBtn').on('click', function () {
        let action = $('#multiOperationSelector').val();
        let selected = $('.checkBoxListing:checked').map(function () {
            return $(this).val();
        }).get();

        if (action === '') {
            alert('Please choose an action.');
            return;
        }
        if (selected.length === 0) {
            alert('Please select at least one record.');
            return;
        }
        if (confirm(`Are you sure you want to ${action} the selected records?`)) {
            $.ajax({
                url: 'organising_committee.process.php',
                type: 'POST',
                data: {
                    act: 'MultiAction',
                    action: action,
                    ids: selected
                },
                success: function () {
                    location.reload();
                }
            });
        }
    });
    $('#check_all').on('click', function () {
        $('.checkBoxListing').prop('checked', this.checked);
    });
</script>
</html>
<?php

/* ======================================================
   LISTING
   ====================================================== */
function showAllCommittee($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
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
    ?>
    <form name="frmCommListing" action="organising_committee.php" method="get">
        <input type="hidden" name="act" id="act" value="search" />

        <div class="com_info_box active" id="committeeListing">
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-users"></i></span> Organising Committee Listing</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3 mb-3">
                            <div class="frm_grp">
                                <p class="frm-head">Type</p>
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
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Name</p>
                                <input type="text" name="name" id="name" value="<?= htmlspecialchars($_REQUEST['name'] ?? '') ?>" />
                            </div>
                            <div class="frm_grp" style="align-self:flex-end;">
                                <button type="submit" class="badge_primary"><i class="fal fa-search"></i> Search</button>
                            </div>
                        </div>

                        <div class="table_wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="sl">
                                            <input name="check_all" id="check_all" type="checkbox" />
                                        </th>
                                        <th class="sl">#</th>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Country</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th class="action text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result) {
                                        $i = 0;
                                        foreach ($result as $row) {
                                            $i++;
                                    ?>
                                    <tr>
                                        <td class="sl">
                                            <input type="checkbox" name="checkvalue[]" class="checkBoxListing" value="<?= $row['id']; ?>" />
                                        </td>
                                        <td class="sl"><?= $i + ($_REQUEST['_pgn_'] * 20) ?></td>
                                        <td><?= ucfirst($row['type']) ?></td>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['designation']) ?></td>
                                        <td><?= htmlspecialchars($row['country_name']) ?></td>
                                      <td>
    <?php if ($row['image']) { ?>
        <img src="<?= $row['image'] ?>" width="60" />
    <?php } ?>
</td>
                                        <td>
                                            <?php if ($row['status'] == 'A') { ?>
                                                <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                            <?php } else { ?>
                                                <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                            <?php } ?>
                                        </td>
                                        <td class="action">
                                            <div class="action_div dropdown" role="menu" aria-label="Actions">
                                                <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?php ellips(); ?></button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&id=<?= $row['id'] ?><?= $searchString ?>" class="icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                    <a href="javascript:void(0);" class="deleteCommitteeBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="organising_committee.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>"><?php delete(); ?>Remove</a>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="9" align="center">
                                            <span class="mandatory">No Record.</span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="page_top_wrap mt-3">
                            <div class="page_top_wrap_left">
                                <select name="multiOperationSelector" id="multiOperationSelector">
                                    <option value="">Choose an action...</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Remove">Remove</option>
                                </select>
                                <button type="button" id="applyMultiActionBtn" class="badge_dark">Apply to selected</button>
                            </div>
                        </div>

                        <div class="paginationDisplay">
                            <span class="paginationRecDisplay"><?= $mycms->paginateRecInfo($indexVal) ?></span>
                            <span class="paginationDisplay"><?= $mycms->paginate($indexVal, 'pagination') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
}


/* ======================================================
   ADD
   ====================================================== */
function addCommittee($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sqlC = [];
    $sqlC['QUERY'] = "SELECT country_id, country_name FROM comn_country WHERE status='A' ORDER BY country_name";
    $countries = $mycms->sql_select($sqlC);
    ?>
    <div class="com_info_box active" id="addCommittee">
        <form method="post" action="organising_committee.process.php" enctype="multipart/form-data">
            <input type="hidden" name="act" value="store" />
            <?php foreach ($searchArray as $key => $val) { ?>
                <input type="hidden" name="<?= $key ?>" value="<?= $val ?>" />
            <?php } ?>

            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-user-plus"></i></span> Member Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Type <span class="mandatory">*</span></p>
                                <select name="type" required>
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
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Name <span class="mandatory">*</span></p>
                                <input type="text" name="name" required maxlength="255" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Designation</p>
                                <input type="text" name="designation" maxlength="255" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Country</p>
                                <select name="country_id">
                                    <option value="">Select</option>
                                    <?php foreach ($countries as $c): ?>
                                        <option value="<?= $c['country_id'] ?>"><?= htmlspecialchars($c['country_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Image</p>
                                <input type="file" name="image" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}


/* ======================================================
   EDIT
   ====================================================== */
function editCommittee($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sql = [];
    $sql['QUERY'] = "SELECT * FROM rcg_organising_committee WHERE id = ?";
    $sql['PARAM'][] = ['FILD' => 'id', 'DATA' => $_REQUEST['id'], 'TYP' => 'i'];
    $result = $mycms->sql_select($sql);
    $row = $result[0];

    $sqlC = [];
    $sqlC['QUERY'] = "SELECT country_id, country_name FROM comn_country WHERE status='A' ORDER BY country_name";
    $countries = $mycms->sql_select($sqlC);
    ?>
    <div class="com_info_box active" id="editCommittee">
        <form method="post" action="organising_committee.process.php" enctype="multipart/form-data">
            <input type="hidden" name="act" value="edit" />
            <input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>" />
            <input type="hidden" name="prev_image" value="<?= $row['image'] ?>" />
            <?php foreach ($searchArray as $key => $val) { ?>
                <input type="hidden" name="<?= $key ?>" value="<?= $val ?>" />
            <?php } ?>

            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-user-edit"></i></span> Member Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Type <span class="mandatory">*</span></p>
                                <select name="type">
                                    <option value="">Select</option>
                                    <option value="patrons" <?= ($row['type'] == 'patrons') ? 'selected' : '' ?>>Patrons</option>
                                    <option value="core committee" <?= ($row['type'] == 'core committee') ? 'selected' : '' ?>>Core Committee</option>
                                    <option value="steering group" <?= ($row['type'] == 'steering group') ? 'selected' : '' ?>>Steering Group</option>
                                    <option value="central committee" <?= ($row['type'] == 'central committee') ? 'selected' : '' ?>>Central Committee</option>
                                    <option value="scientific committee" <?= ($row['type'] == 'scientific committee') ? 'selected' : '' ?>>Scientific Committee</option>
                                    <option value="national advisory board" <?= ($row['type'] == 'national advisory board') ? 'selected' : '' ?>>Scientific Committee (National Advisory Board)</option>
                                    <option value="local advisory board" <?= ($row['type'] == 'local advisory board') ? 'selected' : '' ?>>Local Advisory Board</option>
                                    <option value="residents advisory board" <?= ($row['type'] == 'residents advisory board') ? 'selected' : '' ?>>RESIDENTS Advisory BOARD</option>
                                    <option value="adult cardiac track" <?= ($row['type'] == 'adult cardiac track') ? 'selected' : '' ?>>Local Committee (ADULT CARDIAC TRACK)</option>
                                    <option value="congenital track" <?= ($row['type'] == 'congenital track') ? 'selected' : '' ?>>Local Committee (CONGENITAL TRACK)</option>
                                    <option value="mics/ robotics track" <?= ($row['type'] == 'mics/ robotics track') ? 'selected' : '' ?>>Local Committee (MICS/ ROBOTICS TRACK)</option>
                                    <option value="transplant track" <?= ($row['type'] == 'transplant track') ? 'selected' : '' ?>>Local Committee (TRANSPLANT TRACK)</option>
                                    <option value="aortic track" <?= ($row['type'] == 'aortic track') ? 'selected' : '' ?>>Local Committee (AORTIC TRACK)</option>
                                    <option value="thoracic and vascular track" <?= ($row['type'] == 'thoracic and vascular track') ? 'selected' : '' ?>>Local Committee (THORACIC AND VASCULAR TRACK)</option>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Name <span class="mandatory">*</span></p>
                                <input type="text" name="name" maxlength="255" value="<?= htmlspecialchars($row['name']) ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Designation</p>
                                <input type="text" name="designation" maxlength="255" value="<?= htmlspecialchars($row['designation']) ?>" />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Country</p>
                                <select name="country_id">
                                    <option value="">Select</option>
                                    <?php foreach ($countries as $c): ?>
                                        <option value="<?= $c['country_id'] ?>" <?= ($row['country_id'] == $c['country_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['country_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Image</p>
                                <input type="file" name="image" />
                                <?php if ($row['image']) { ?>
                                    <br><img src="<?= $row['image'] ?>" width="90" />
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}
?>