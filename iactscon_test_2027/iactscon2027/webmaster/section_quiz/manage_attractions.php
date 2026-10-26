<?php
include_once("../includes/source.php");
include_once('../includes/init.php');
include_once('../includes/function.workshop.php');
?>

<body>
    <?php include_once("../includes/left-menu.php"); ?>
    <header>
        <h2>Nearby Attractions</h2>
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
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Nearby Attractions</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'add') ? 'Create Nearby Attraction' : (($show == 'edit') ? 'Edit Nearby Attraction' : 'Nearby Attraction Listing') ?></h2>
                <h6>Manage nearby attractions, distances and travel times.</h6>
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
                        addAttraction($cfg, $mycms);
                        break;
                    case 'edit':
                        editAttraction($cfg, $mycms);
                        break;
                    default:
                        showAllAttractions($cfg, $mycms);
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>
<?php include_once("../includes/js-source.php"); ?>
<script>
    $(document).on('click', '.deleteAttractionBtn', function () {
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
                url: 'manage_attraction.process.php',
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

/*============== SHOW ALL ATTRACTIONS ================*/
function showAllAttractions($cfg, $mycms)
{
    global $searchArray, $searchString;

    $con    = "";
    $params = [];
    if (!empty($_REQUEST['name'])) {
        $con .= " AND LOWER(`name`) LIKE ?";
        $params[] = [
            'FILD' => 'name',
            'DATA' => '%' . strtolower(trim($_REQUEST['name'])) . '%',
            'TYP'  => 's'
        ];
    }

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
    ?>
    <form name="frmCountryListing" id="frmCountryListing" action="manage_attraction.php" method="get">
        <input type="hidden" name="act" id="act" value="search_poll" />
        <input type="hidden" id="excelPrintVal" name="excelPrintVal" />

        <div class="com_info_box active" id="attractionListing">
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-map-marker-alt"></i></span> Nearby Attraction Listing</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="table_wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="sl">
                                            <input name="check_all" id="check_all" type="checkbox" />
                                        </th>
                                        <th class="sl">#</th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Distance from venue</th>
                                        <th>Time to reach</th>
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
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['location_url']) ?></td>
                                        <td><?= htmlspecialchars($row['distance_from_venue']) ?></td>
                                        <td><?= htmlspecialchars($row['time_to_reach']) ?></td>
                                        <td><?= htmlspecialchars($row['image']) ?></td>
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
                                                    <a href="javascript:void(0);" class="deleteAttractionBtn icon_hover badge_danger action-transparent br-5 w-auto" data-href="manage_attraction.process.php?act=Remove&id=<?= $row['id'] ?><?= $searchString ?>"><?php delete(); ?>Remove</a>
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

/*================= ADD ATTRACTION ===============*/
function addAttraction($cfg, $mycms)
{
    global $searchArray, $searchString;
    ?>
    <div class="com_info_box active" id="addAttraction">
        <form name="frmtypeadd" method="post" action="manage_attraction.process.php" id="frmtypeadd" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
            <input type="hidden" name="act" value="store" />
            <?php
            foreach ($searchArray as $key => $val) {
            ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
            <?php
            }
            ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-map-marker-alt"></i></span> Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save2" name="Save2" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Name <span class="mandatory">*</span></p>
                                <input type="text" name="name" id="name" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Location Url <span class="mandatory">*</span></p>
                                <input type="text" name="location_url" id="location_url" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Distance from venue <span class="mandatory">*</span></p>
                                <input type="text" name="distance_from_venue" id="distance_from_venue" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Time to reach <span class="mandatory">*</span></p>
                                <input type="text" name="time_to_reach" id="time_to_reach" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Image <span class="mandatory">*</span></p>
                                <input type="file" name="image" id="image" class="validate[required]" required />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/*================= EDIT ATTRACTION ==============*/
function editAttraction($cfg, $mycms)
{
    global $searchArray, $searchString;

    $sql            = array();
    $sql['QUERY']   = "SELECT * FROM `rcg_nearby_attractions` WHERE `id` = ?";
    $sql['PARAM'][] = array('FILD' => 'id', 'DATA' => $_REQUEST['id'], 'TYP' => 's');
    $result         = $mycms->sql_select($sql);
    $row            = $result[0];
    ?>
    <div class="com_info_box active" id="editAttraction">
        <form name="frmtypeedit" method="post" action="manage_attraction.process.php" id="frmtypeedit" onsubmit="return onSubmitAction();" enctype="multipart/form-data">
            <input type="hidden" name="act" value="edit" />
            <input type="hidden" name="id" id="id" value="<?= $_REQUEST['id'] ?>" />
            <?php
            foreach ($searchArray as $key => $val) {
            ?>
            <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $val ?>" />
            <?php
            }
            ?>
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-map-marker-alt"></i></span> Details</n>
                        <div class="page_top_wrap_right">
                            <a href="<?= $_SERVER['PHP_SELF'] ?>?<?= ltrim($searchString, '&') ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" id="Save" name="Save" class="badge_success"><i class="fal fa-save"></i> Save</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Name <span class="mandatory">*</span></p>
                                <input type="text" name="name" id="name" class="validate[required]" value="<?= htmlspecialchars($row['name']); ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Location URL <span class="mandatory">*</span></p>
                                <input type="text" name="location_url" id="location_url" class="validate[required]" value="<?= htmlspecialchars($row['location_url']); ?>" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Distance from venue <span class="mandatory">*</span></p>
                                <input type="text" name="distance_from_venue" value="<?= htmlspecialchars($row['distance_from_venue']); ?>" id="distance_from_venue" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Time to reach <span class="mandatory">*</span></p>
                                <input type="text" name="time_to_reach" id="time_to_reach" value="<?= htmlspecialchars($row['time_to_reach']); ?>" class="validate[required]" required />
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Image <span class="mandatory">*</span></p>
                                <input type="file" name="image" id="image" value="" class="validate[required]" required />
                                <input type="hidden" name="prev_image" id="prev_image" value="<?= $row['image']; ?>" />
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