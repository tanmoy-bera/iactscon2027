<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Organising Committee</h2>
        <?php include_once("./includes/header_right.php"); ?>
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
                        <li class="breadcrumb-item"><a href="#">App Setup</a></li>
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
<?php include_once("./includes/js-source.php"); ?>
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

    // Committee filter buttons (client-side show/hide)
    $(document).on('click', '.appcom_tab button', function () {
        var id = this.id;
        if (id === 'appcomall') {
            $('.appcombox').fadeIn(450);
        } else {
            var $el = $('.' + id).fadeIn(450);
            $('.appcombox').not($el).hide();
        }
        $('.appcom_tab button').removeClass('active');
        $(this).addClass('active');
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
        ORDER BY
            CASE
                WHEN LOWER(TRIM(a.type)) = 'local advisory board' THEN 0
                ELSE 1
            END ASC,
            CASE
                WHEN LOWER(TRIM(a.type)) = 'local advisory board'
                     AND UPPER(TRIM(a.designation)) = 'ADVISOR' THEN 0
                WHEN LOWER(TRIM(a.type)) = 'local advisory board' THEN 1
                ELSE 2
            END ASC,
            a.type_order ASC,
            a.sort_order ASC,
            a.id ASC
    ";

    foreach ($params as $p) {
        $sql['PARAM'][] = $p;
    }

    $result = $mycms->sql_select_paginated($indexVal, $sql, 20, $restrt);

    // Build type->css class map for filtering
    $typeClassMap = [
        'patrons'                    => 'appcompatron',
        'core committee'             => 'appcomchairperson',
        'steering group'             => 'appcomorgsec',
        'central committee'          => 'appcomtreasurer',
        'scientific committee'       => 'appcommember',
        'national advisory board'    => 'appcommember',
        'local advisory board'       => 'appcommember',
        'residents advisory board'   => 'appcommember',
        'adult cardiac track'        => 'appcommember',
        'congenital track'           => 'appcommember',
        'mics/ robotics track'       => 'appcommember',
        'transplant track'           => 'appcommember',
        'aortic track'               => 'appcommember',
        'thoracic and vascular track'=> 'appcommember',
    ];
    ?>
    <form name="frmCommListing" action="organising_committee.php" method="get">
        <input type="hidden" name="act" id="act" value="search" />

        <div class="com_info_box active" id="committeeListing">
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">

                    <div class="venue_hall_top">
                        <h5>
                            <n><i class="fal fa-users"></i>Organizing Committee Directory</n>
                            <l>Manage committee leadership roles, designations, contact numbers & display order.</l>
                        </h5>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?show=add<?= $searchString ?>" class="badge_default"><i class="fal fa-plus"></i> Add Committee Member</a>
                        </div>
                    </div>

                    <div class="committee_filter">
                        <ul class="appcom_tab">
                            <button type="button" class="active" id="appcomall">All</button>
                            <button type="button" id="appcompatron">Patron</button>
                            <button type="button" id="appcomchairperson">Chairperson</button>
                            <button type="button" id="appcomorgsec">Organizing Secretary</button>
                            <button type="button" id="appcomtreasurer">Treasurer</button>
                            <button type="button" id="appcommember">Committee Member</button>
                        </ul>
                        <div class="search_div">
                            <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>">
                                <i class="fal fa-search"></i>
                                <input type="text" name="name" value="<?= htmlspecialchars($_REQUEST['name'] ?? '') ?>" placeholder="Search name...">
                            </form>
                        </div>
                    </div>

                    <div class="committee_wrap">
                        <?php
                        if ($result) {
                            foreach ($result as $row) {
                                $typeKey  = strtolower(trim($row['type'] ?? ''));
                                $typeCss  = $typeClassMap[$typeKey] ?? 'appcommember';
                        ?>
                        <div class="committee_box appcombox <?= $typeCss ?>">
                            <div class="committee_box_top">
                                <span></span>
                                <div class="action_div action">
                                    <a href="javascript:void(0);" class="deleteCommitteeBtn icon_hover badge_danger br-5 w-auto action-transparent"
                                       data-href="organising_committee.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>">
                                        <i class="fal fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="committee_box_mid">
                                <?php if (!empty($row['image'])) { ?>
                                    <div class="committee_img">
                                        <img src="<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                                    </div>
                                <?php } else { ?>
                                    <div class="committee_img">
                                        <div class="no-image-placeholder">No Image</div>
                                    </div>
                                <?php } ?>
                                <span class="badge_padding badge_secondary"><?= ucfirst($row['type']) ?></span>
                                <h5><?= htmlspecialchars($row['name']) ?></h5>
                                <h6><?= htmlspecialchars($row['designation']) ?></h6>
                                <p><?= htmlspecialchars($row['country_name'] ?? '') ?></p>
                            </div>
                            <ul>
                                <?php if (!empty($row['contact'])) { ?>
                                    <li><?php call() ?><?= htmlspecialchars($row['contact']) ?></li>
                                <?php } ?>
                                <?php if (!empty($row['email'])) { ?>
                                    <li><?php email() ?><?= htmlspecialchars($row['email']) ?></li>
                                <?php } ?>
                            </ul>
                            <div class="committee_box_bottom">
                                <a href="<?= $_SERVER['PHP_SELF'] ?>?show=edit&id=<?= $row['id'] ?><?= $searchString ?>" class="badge_secondary"><i class="fal fa-edit"></i> Edit</a>
                                <?php if ($row['status'] == 'A') { ?>
                                    <n class="badge_padding badge_success w-max-con text-uppercase">Active</n>
                                <?php } else { ?>
                                    <n class="badge_padding badge_danger w-max-con text-uppercase">Inactive</n>
                                <?php } ?>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                        ?>
                        <div class="committee_box appcombox appcommember" style="width:100%;text-align:center;">
                            <span class="mandatory">No Record.</span>
                        </div>
                        <?php } ?>
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

            <div class="venue_hall_top">
                <h5>
                    <n><i class="fal fa-user-plus"></i>Add Committee Member</n>
                    <l>Manage patrons, committees and advisory board members.</l>
                </h5>
                <div class="page_top_wrap_right">
                    <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                    <button type="submit" class="badge_success"><i class="fal fa-save"></i> Save</button>
                </div>
            </div>

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
                    <p class="frm-head">Type Title</p>
                    <input type="text" name="type_title" maxlength="255" placeholder="e.g. Local Advisory Board" />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Name <span class="mandatory">*</span></p>
                    <input type="text" name="name" required maxlength="255" />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Designation</p>
                    <input type="text" name="designation" maxlength="255" placeholder="e.g. ADVISOR" />
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
                    <p class="frm-head">Type Order</p>
                    <input type="number" name="type_order" value="0" min="0" />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Sort Order</p>
                    <input type="number" name="sort_order" value="0" min="0" />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Display Style</p>
                    <select name="display_style">
                        <option value="list">List</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Is Track?</p>
                    <select name="is_track">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Image</p>
                    <input type="file" name="image" />
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

            <div class="venue_hall_top">
                <h5>
                    <n><i class="fal fa-user-edit"></i>Edit Committee Member</n>
                    <l>Update role, designation, country, or image.</l>
                </h5>
                <div class="page_top_wrap_right">
                    <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                    <button type="submit" class="badge_success"><i class="fal fa-save"></i> Save</button>
                </div>
            </div>

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
                    <p class="frm-head">Type Title</p>
                    <input type="text" name="type_title" maxlength="255" value="<?= htmlspecialchars($row['type_title'] ?? '') ?>" />
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
                    <p class="frm-head">Type Order</p>
                    <input type="number" name="type_order" value="<?= (int)($row['type_order'] ?? 0) ?>" min="0" />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Sort Order</p>
                    <input type="number" name="sort_order" value="<?= (int)($row['sort_order'] ?? 0) ?>" min="0" />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Display Style</p>
                    <select name="display_style">
                        <option value="list" <?= ($row['display_style'] == 'list') ? 'selected' : '' ?>>List</option>
                        <option value="card" <?= ($row['display_style'] == 'card') ? 'selected' : '' ?>>Card</option>
                    </select>
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Is Track?</p>
                    <select name="is_track">
                        <option value="0" <?= ((int)$row['is_track'] === 0) ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= ((int)$row['is_track'] === 1) ? 'selected' : '' ?>>Yes</option>
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
        </form>
    </div>
    <?php
}
?>