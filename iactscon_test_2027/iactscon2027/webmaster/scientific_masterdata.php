<?php ob_start();
include_once("includes/source.php"); 
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once("../../includes/function.registration.php");
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.invoice.php');
include_once('../../includes/function.workshop.php');
include_once('../../includes/function.dinner.php');
include_once('../../includes/function.accompany.php');
include_once('../../includes/function.accommodation.php');
include_once('../../includes/function.abstract.php');
include_once('includes/function.php');

/*============== FLOOR MASTER HANDLER (handled inside this page) ================*/
function smd_floor_redirect($type = 'success', $message = '')
{
    if ($message !== '') {
        $_SESSION['floor_alert'] = ['type' => $type, 'message' => $message];
    }
    while (ob_get_level()) { ob_end_clean(); }
    header("Location: scientific_masterdata.php#floor_master");
    exit();
}

function smd_floor_name_exists($mycms, $name, $excludeId = 0)
{
    $sql = [];
    $sql['QUERY'] = "SELECT `floor_id` FROM `rcg_floor_master`
                     WHERE LOWER(`floor_name`) = LOWER(?) AND `status` != 'D' AND `floor_id` != ?
                     LIMIT 1";
    $sql['PARAM'] = [
        ['FILD' => 'floor_name', 'DATA' => $name,           'TYP' => 's'],
        ['FILD' => 'floor_id',   'DATA' => (int)$excludeId, 'TYP' => 'i']
    ];
    $res = $mycms->sql_select($sql);
    return !empty($res);
}

if (!empty($_REQUEST['floor_act'])) {
    $floorAct    = $_REQUEST['floor_act'];
    $floorUserId = $mycms->getLoggedUserId();

    try {
        if ($floorAct === 'add' || $floorAct === 'edit') {
            $floorName = trim($_POST['floor_name'] ?? '');
            $floorName = function_exists('mb_substr') ? mb_substr($floorName, 0, 255) : substr($floorName, 0, 255);
            $floorId   = (int)($_POST['floor_id'] ?? 0);

            if ($floorName === '') {
                throw new Exception("Floor name is required.");
            }
            if (smd_floor_name_exists($mycms, $floorName, ($floorAct === 'edit') ? $floorId : 0)) {
                throw new Exception("This floor already exists.");
            }

            if ($floorAct === 'add') {
                $sqlFloorSave = [];
                $sqlFloorSave['QUERY'] = "INSERT INTO `rcg_floor_master` SET floor_name=?, status='A', created_by=?";
                $sqlFloorSave['PARAM'] = [
                    ['FILD' => 'floor_name', 'DATA' => $floorName,   'TYP' => 's'],
                    ['FILD' => 'created_by', 'DATA' => $floorUserId, 'TYP' => 'i']
                ];
                $mycms->sql_insert($sqlFloorSave);
                smd_floor_redirect('success', 'Floor added successfully.');
            } else {
                if ($floorId <= 0) {
                    throw new Exception("Floor ID is required.");
                }
                $sqlFloorSave = [];
                $sqlFloorSave['QUERY'] = "UPDATE `rcg_floor_master`
                                          SET floor_name=?, updated_by=?, updated_at=NOW()
                                          WHERE floor_id=? AND status != 'D'";
                $sqlFloorSave['PARAM'] = [
                    ['FILD' => 'floor_name', 'DATA' => $floorName,   'TYP' => 's'],
                    ['FILD' => 'updated_by', 'DATA' => $floorUserId, 'TYP' => 'i'],
                    ['FILD' => 'floor_id',   'DATA' => $floorId,     'TYP' => 'i']
                ];
                $mycms->sql_update($sqlFloorSave);
                smd_floor_redirect('success', 'Floor updated successfully.');
            }
        }

        if ($floorAct === 'status' || $floorAct === 'delete') {
            $floorId = (int)($_GET['id'] ?? 0);
            if ($floorId <= 0) {
                throw new Exception("Floor ID is required.");
            }
            if ($floorAct === 'delete') {
                $floorNewStatus = 'D';   // soft delete
                $floorDoneMsg   = 'Floor deleted successfully.';
            } else {
                $floorNewStatus = (($_GET['to'] ?? '') === 'I') ? 'I' : 'A';
                $floorDoneMsg   = 'Floor marked as ' . (($floorNewStatus === 'A') ? 'active' : 'inactive') . ' successfully.';
            }
            $sqlFloorSave = [];
            $sqlFloorSave['QUERY'] = "UPDATE `rcg_floor_master`
                                      SET status=?, updated_by=?, updated_at=NOW()
                                      WHERE floor_id=? AND status != 'D'";
            $sqlFloorSave['PARAM'] = [
                ['FILD' => 'status',     'DATA' => $floorNewStatus, 'TYP' => 's'],
                ['FILD' => 'updated_by', 'DATA' => $floorUserId,    'TYP' => 'i'],
                ['FILD' => 'floor_id',   'DATA' => $floorId,        'TYP' => 'i']
            ];
            $mycms->sql_update($sqlFloorSave);
            smd_floor_redirect('success', $floorDoneMsg);
        }
    } catch (Exception $e) {
        smd_floor_redirect('error', $e->getMessage());
    }
}

/*============== VENUE MASTER HANDLER (add / edit with capacity rows) ================*/
function smd_venue_redirect($type = 'success', $message = '')
{
    if ($message !== '') {
        $_SESSION['floor_alert'] = ['type' => $type, 'message' => $message];
    }
    while (ob_get_level()) { ob_end_clean(); }
    header("Location: scientific_masterdata.php#venue_master");
    exit();
}

function smd_capacity_table_exists($mycms)
{
    $sql = [];
    $sql['QUERY'] = "SELECT `TABLE_NAME` FROM `information_schema`.`TABLES`
                     WHERE `TABLE_SCHEMA` = DATABASE() AND `TABLE_NAME` = 'rcg_venue_capacity'
                     LIMIT 1";
    $res = $mycms->sql_select($sql);
    return !empty($res);
}

if (!empty($_POST['venue_act']) && in_array($_POST['venue_act'], ['add', 'edit'], true)) {
    $venueAct    = $_POST['venue_act'];
    $venueUserId = $mycms->getLoggedUserId();

    try {
        $venueId     = (int)($_POST['venue_id'] ?? 0);
        $vTitle      = trim($_POST['venue_title'] ?? '');
        $vCity       = trim($_POST['city'] ?? '');
        $vAirport    = trim($_POST['airport_distance'] ?? '');
        $vRailway    = trim($_POST['railway_distance'] ?? '');
        $vGmap       = trim($_POST['gmap_url'] ?? '');

        if ($venueAct === 'edit' && $venueId <= 0) {
            throw new Exception("Venue ID is required.");
        }
        if ($vTitle === '') {
            throw new Exception("Venue title is required.");
        }
        if ($vCity === '') {
            throw new Exception("City is required.");
        }
        if ($vGmap === '' || !filter_var($vGmap, FILTER_VALIDATE_URL)) {
            throw new Exception("A valid map URL is required.");
        }

        // ---- capacity rows (validated before anything is written) ----
        $capHalls  = (isset($_POST['cap_hall_id'])  && is_array($_POST['cap_hall_id']))  ? $_POST['cap_hall_id']  : [];
        $capFloors = (isset($_POST['cap_floor_id']) && is_array($_POST['cap_floor_id'])) ? $_POST['cap_floor_id'] : [];
        $capValues = (isset($_POST['cap_capacity']) && is_array($_POST['cap_capacity'])) ? $_POST['cap_capacity'] : [];
        $capRows   = [];
        $capSeen   = [];
        $capCount  = max(count($capHalls), count($capFloors), count($capValues));

        for ($i = 0; $i < $capCount; $i++) {
            $cHall  = (int)($capHalls[$i]  ?? 0);
            $cFloor = (int)($capFloors[$i] ?? 0);
            $cValue = (int)($capValues[$i] ?? 0);

            if ($cHall === 0 && $cFloor === 0 && $cValue === 0) {
                continue; // blank row
            }
            if ($cHall === 0 && $cFloor === 0) {
                throw new Exception("Select a hall or a floor for every capacity row.");
            }
            if ($cValue <= 0) {
                throw new Exception("Capacity must be greater than 0.");
            }
            $key = $cHall . '-' . $cFloor;
            if (isset($capSeen[$key])) {
                throw new Exception("Duplicate hall/floor combination in capacity rows.");
            }
            $capSeen[$key] = true;
            $capRows[] = ['hall' => $cHall, 'floor' => $cFloor, 'cap' => $cValue];
        }

        $capTableOk = smd_capacity_table_exists($mycms);
        if (!empty($capRows) && !$capTableOk) {
            throw new Exception("Table rcg_venue_capacity does not exist yet. Please create it first.");
        }

        // ---- background image ----
        $vImage = ($venueAct === 'edit') ? trim($_POST['prev_image'] ?? '') : '';
        $upErr  = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($upErr === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/uploads/venue/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext        = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'svg'];
            if (!in_array($ext, $allowedExt, true)) {
                throw new Exception("Invalid image format. Allowed: jpg, jpeg, png, svg");
            }
            $newFilename = uniqid('venue_') . "." . $ext;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                throw new Exception("Failed to upload image.");
            }
            $vImage = "uploads/venue/" . $newFilename;
        } elseif ($upErr !== UPLOAD_ERR_NO_FILE) {
            throw new Exception("Image upload failed. Please try again.");
        } elseif ($venueAct === 'add') {
            throw new Exception("Background image is required.");
        }

        // ---- save venue ----
        if ($venueAct === 'add') {
            $sqlVenueSave = [];
            $sqlVenueSave['QUERY'] = "INSERT INTO " . _DB_PROGRAM_SCHEDULE_VENUE_ . "
                                      SET program_venue=?, status='A', city=?, airport_distance=?,
                                          railway_distance=?, background_img=?, gmap_url=?";
            $sqlVenueSave['PARAM'] = [
                ['FILD' => 'program_venue',    'DATA' => $vTitle,   'TYP' => 's'],
                ['FILD' => 'city',             'DATA' => $vCity,    'TYP' => 's'],
                ['FILD' => 'airport_distance', 'DATA' => $vAirport, 'TYP' => 's'],
                ['FILD' => 'railway_distance', 'DATA' => $vRailway, 'TYP' => 's'],
                ['FILD' => 'background_img',   'DATA' => $vImage,   'TYP' => 's'],
                ['FILD' => 'gmap_url',         'DATA' => $vGmap,    'TYP' => 's']
            ];
            $venueId = (int)$mycms->sql_insert($sqlVenueSave);
        } else {
            $sqlVenueSave = [];
            $sqlVenueSave['QUERY'] = "UPDATE " . _DB_PROGRAM_SCHEDULE_VENUE_ . "
                                      SET program_venue=?, city=?, airport_distance=?,
                                          railway_distance=?, background_img=?, gmap_url=?
                                      WHERE id=? AND status != 'D'";
            $sqlVenueSave['PARAM'] = [
                ['FILD' => 'program_venue',    'DATA' => $vTitle,   'TYP' => 's'],
                ['FILD' => 'city',             'DATA' => $vCity,    'TYP' => 's'],
                ['FILD' => 'airport_distance', 'DATA' => $vAirport, 'TYP' => 's'],
                ['FILD' => 'railway_distance', 'DATA' => $vRailway, 'TYP' => 's'],
                ['FILD' => 'background_img',   'DATA' => $vImage,   'TYP' => 's'],
                ['FILD' => 'gmap_url',         'DATA' => $vGmap,    'TYP' => 's'],
                ['FILD' => 'id',               'DATA' => $venueId,  'TYP' => 'i']
            ];
            $mycms->sql_update($sqlVenueSave);
        }

        // ---- save capacity rows: retire the old ones, insert the submitted set ----
        if ($capTableOk && $venueId > 0) {
            $sqlCapOld = [];
            $sqlCapOld['QUERY'] = "UPDATE `rcg_venue_capacity`
                                   SET status='D', updated_by=?, updated_at=NOW()
                                   WHERE venue_id=? AND status != 'D'";
            $sqlCapOld['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $venueUserId, 'TYP' => 'i'],
                ['FILD' => 'venue_id',   'DATA' => $venueId,     'TYP' => 'i']
            ];
            $mycms->sql_update($sqlCapOld);

            foreach ($capRows as $cr) {
                $sqlCapNew = [];
                $sqlCapNew['QUERY'] = "INSERT INTO `rcg_venue_capacity`
                                       SET venue_id=?, hall_id=?, floor_id=?, capacity=?, status='A', created_by=?";
                $sqlCapNew['PARAM'] = [
                    ['FILD' => 'venue_id',   'DATA' => $venueId,      'TYP' => 'i'],
                    ['FILD' => 'hall_id',    'DATA' => $cr['hall'],   'TYP' => 'i'],
                    ['FILD' => 'floor_id',   'DATA' => $cr['floor'],  'TYP' => 'i'],
                    ['FILD' => 'capacity',   'DATA' => $cr['cap'],    'TYP' => 'i'],
                    ['FILD' => 'created_by', 'DATA' => $venueUserId,  'TYP' => 'i']
                ];
                $mycms->sql_insert($sqlCapNew);
            }
        }

        smd_venue_redirect('success', ($venueAct === 'add') ? 'Venue added successfully.' : 'Venue updated successfully.');
    } catch (Exception $e) {
        smd_venue_redirect('error', $e->getMessage());
    }
}
$floorFlash = $_SESSION['floor_alert'] ?? null;
unset($_SESSION['floor_alert']);

// Venue modal data: halls, floors and existing capacity rows (capacity table is optional until created)
$venueHallOptions  = '<option value="0">-- Hall (optional) --</option>';
$venueFloorOptions = '<option value="0">-- Floor (optional) --</option>';
$venueCapMap       = [];
$venueHallNames    = [];
$venueFloorNames   = [];

$sqlVHalls = [];
$sqlVHalls['QUERY'] = "SELECT id, hall_title FROM " . _DB_MASTER_HALL_ . " WHERE status = 'A' ORDER BY hall_title ASC";
$resVHalls = $mycms->sql_select($sqlVHalls);
if ($resVHalls) {
    foreach ($resVHalls as $vh) {
        $venueHallOptions .= '<option value="' . (int)$vh['id'] . '">' . htmlspecialchars($vh['hall_title']) . '</option>';
        $venueHallNames[(int)$vh['id']] = $vh['hall_title'];
    }
}
$sqlVFloors = [];
$sqlVFloors['QUERY'] = "SELECT floor_id, floor_name FROM `rcg_floor_master` WHERE status = 'A' ORDER BY floor_name ASC";
$resVFloors = $mycms->sql_select($sqlVFloors);
if ($resVFloors) {
    foreach ($resVFloors as $vf) {
        $venueFloorOptions .= '<option value="' . (int)$vf['floor_id'] . '">' . htmlspecialchars($vf['floor_name']) . '</option>';
        $venueFloorNames[(int)$vf['floor_id']] = $vf['floor_name'];
    }
}
if (smd_capacity_table_exists($mycms)) {
    $sqlVCaps = [];
    $sqlVCaps['QUERY'] = "SELECT venue_id, hall_id, floor_id, capacity FROM `rcg_venue_capacity` WHERE status = 'A' ORDER BY id ASC";
    $resVCaps = $mycms->sql_select($sqlVCaps);
    if ($resVCaps) {
        foreach ($resVCaps as $vc) {
            $venueCapMap[(int)$vc['venue_id']][] = [
                'hall'  => (int)$vc['hall_id'],
                'floor' => (int)$vc['floor_id'],
                'cap'   => (int)$vc['capacity']
            ];
        }
    }
}
// $cfg['SECTION_BASE_URL'] = "https://ruedakolkata.com/natcon_25/conference_registration/webmaster/";
?>
<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Scientific Program</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <style>
        .int_alert { position: fixed; top: 20px; right: 20px; z-index: 99999; min-width: 280px; max-width: 420px; padding: 14px 18px; border-radius: 10px; border: 1px solid; display: flex; gap: 14px; align-items: center; justify-content: space-between; font-size: 14px; box-shadow: 0 10px 30px rgba(0, 0, 0, .6); }
        .int_alert.success { color: #34d399; background: #0f1f1a; border-color: rgba(16, 185, 129, .45); }
        .int_alert.error   { color: #f87171; background: #221212; border-color: rgba(239, 68, 68, .45); }
        .int_alert button  { background: none; border: 0; color: inherit; font-size: 20px; line-height: 1; cursor: pointer; }
        .floor_modal { position: fixed; inset: 0; z-index: 99998; background: rgba(0, 0, 0, .65); display: none; align-items: center; justify-content: center; }
        .floor_modal.open { display: flex; }
        .floor_modal_box { background: #161616; border: 1px solid rgba(255, 255, 255, .12); border-radius: 12px; padding: 22px; width: 100%; max-width: 440px; box-shadow: 0 10px 40px rgba(0, 0, 0, .7); }
        .floor_modal_box h5 { margin: 0 0 16px; }
        .floor_modal_actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 18px; }
        .venue_modal_box { max-width: 760px; max-height: 90vh; overflow-y: auto; }
        .venue_modal_box .frm_grp { margin-bottom: 12px; }
        .venue_cap_head, .venue_cap_row { display: grid; grid-template-columns: 1fr 1fr 130px 36px; gap: 8px; align-items: center; margin-bottom: 8px; }
        .venue_cap_head span { font-size: 12px; opacity: .75; }
        .venue_cap_row button { background: none; border: 0; color: #f87171; font-size: 20px; cursor: pointer; }
    </style>
    <?php if (!empty($floorFlash['message'])) { ?>
    <div class="int_alert <?= htmlspecialchars($floorFlash['type']) ?>" id="floorAlert">
        <span><?= htmlspecialchars($floorFlash['message']) ?></span>
        <button type="button" id="floorAlertClose" aria-label="Close">&times;</button>
    </div>
    <?php } ?>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Scientific Program</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Master Data</a></li>
                    </ol>
                </nav>
                <h2>Master Data</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Master Menu</h6>
                <button data-tab="date_master" class="com_info_left_click icon_hover badge_secondary active">Date Masters</button>
                <button data-tab="venue_master" class="com_info_left_click icon_hover badge_success action-transparent">Venue Masters</button>
                <button data-tab="hall_master" class="com_info_left_click icon_hover badge_danger action-transparent">Hall Master</button>
                <button data-tab="floor_master" class="com_info_left_click icon_hover badge_info action-transparent">Floor Master</button>
                <button data-tab="participant_master" class="com_info_left_click icon_hover badge_info action-transparent">Participant Types</button>
                <button data-tab="highlight_speakers" class="com_info_left_click icon_hover badge_primary action-transparent">Eminent Speaker</button>
                <button data-tab="session_master" class="com_info_left_click icon_hover badge_dark action-transparent">Session Master</button>
            </div>
            <div class="com_info_right">
                <div class="com_info_box " id="highlight_speakers">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head"> 
                                <n><span class="text_primary">Highlight Speakers</n>
                                <a class="add mi-1 popup-btn" data-tab="newspeaker"><?php add() ?>Add Speaker</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Speaker</th>
                                                <th>Date & Time</th>
                                                <th>Hall</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlHallListing					 = array();
                                            $sqlHallListing['QUERY']		 = "SELECT * FROM " . _DB_PROGRAM_HIGHLIGHT_SPEAKER_ . " WHERE `status` != 'D'";
                                            $resultHallListing  			 = $mycms->sql_select($sqlHallListing);
                                            $Counter			 			 = 0;
                                            if ($resultHallListing) {
                                                foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <!-- <div class="regi_img_circle">
                                                            <span>AM</span>
                                                        </div> -->
                                                        <div>
                                                            <div class="regi_name"><?= $rowHallListing['speaker_name'] ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php calendar() ?> <?= $rowHallListing['conf_datetime'] ?>
                                                   
                                                </td>
                                                <td><?= $rowHallListing['hall_name'] ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'SpeakerInactive' : 'SpeakerActive') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                        <!-- <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                                        <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span> -->
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editspeaker" data-id="<?=$rowHallListing['id']?>?" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editspeakerbtn"><?php edit(); ?></a>
                                                        <!-- <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a> -->
                                                    </div>
                                                </td>
                                            </tr>
                                            <?
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box active" id="date_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary">Date Masters</n>
                                <a class="add mi-1 popup-btn" data-tab="newscientificdate"><?php add() ?>Add Date</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Program Details</th>
                                                <th>Description</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlHallListing					 = array();
                                            $sqlHallListing['QUERY']		 = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_DATE_ . " WHERE `status` != 'D'";
                                            $resultHallListing  			 = $mycms->sql_select($sqlHallListing);
                                            $Counter			 			 = 0;
                                            if ($resultHallListing) {
                                                foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td>
                                                    <div class="regi_name"><?= $rowHallListing['conf_title'] ?></div>
                                                    <div class="regi_contact mt-0">
                                                        <span style="color: #ffffff;!important">
                                                            <?php calendar() ?><?= date('Y-m-d', strtotime($rowHallListing['conf_date'])) ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?= $rowHallListing['conf_desc'] ?>
                                                </td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'DateInactive' : 'DateActive') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                       
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientificdate" data-id="<?=$rowHallListing['id']?>" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editscientificdatebtn"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeConferenceDate&id=<?= $rowHallListing['id'] ?>" class="icon_hover badge_danger action-transparent br-5 w-auto"  onclick="return confirm('Do you really want to remove this record?')"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="venue_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success">Venue Masters</n>
                                <a class="add mi-1" href="javascript:void(0);" id="addVenueBtn"><?php add(); ?>Add Venue</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Venue</th>
                                                <th>City</th>
                                                <th>Airport Distance</th>
                                                <th>Railway Distance</th>
                                                <th>Map</th>
                                                <th>Image</th>
                                                <th>Hall + Floor + Capacity</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlHallListing			 = array();
                                            $sqlHallListing = array();
                                            $sqlHallListing['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_VENUE_ . " WHERE `status` != ?";

                                            $sqlHallListing['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'D',  'TYP' => 's');

                                            $resultHallListing   	 = $mycms->sql_select($sqlHallListing);
                                            $Counter			 	 = 0;
                                            if ($resultHallListing) {
                                                foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= htmlspecialchars($rowHallListing['program_venue']) ?></td>
                                                <td><?= htmlspecialchars($rowHallListing['city'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($rowHallListing['airport_distance'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($rowHallListing['railway_distance'] ?? '') ?></td>
                                                <td>
                                                    <?php if (!empty($rowHallListing['gmap_url']) && preg_match('#^https?://#i', $rowHallListing['gmap_url'])) { ?>
                                                        <a href="<?= htmlspecialchars($rowHallListing['gmap_url']) ?>" target="_blank" rel="noopener noreferrer">View Map</a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($rowHallListing['background_img'])) { ?>
                                                        <a href="<?= htmlspecialchars($rowHallListing['background_img']) ?>" target="_blank" rel="noopener noreferrer">
                                                            <img src="<?= htmlspecialchars($rowHallListing['background_img']) ?>" alt="Venue image" style="height:40px;width:auto;border-radius:6px;" />
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $vCapList  = $venueCapMap[(int)$rowHallListing['id']] ?? [];
                                                    $vCapTotal = 0;
                                                    if (!empty($vCapList)) {
                                                        echo '<table style="width:100%;min-width:260px;border-collapse:collapse;font-size:13px;">';
                                                        echo '<thead><tr><th style="text-align:left;">Hall</th><th style="text-align:left;">Floor</th><th style="text-align:right;">Capacity</th></tr></thead><tbody>';
                                                        foreach ($vCapList as $vCap) {
                                                            $vCapTotal += (int)$vCap['cap'];
                                                            $vHallLabel  = ($vCap['hall'] > 0)  ? htmlspecialchars($venueHallNames[$vCap['hall']]   ?? ('Hall #'  . $vCap['hall']))  : '-';
                                                            $vFloorLabel = ($vCap['floor'] > 0) ? htmlspecialchars($venueFloorNames[$vCap['floor']] ?? ('Floor #' . $vCap['floor'])) : '-';
                                                            echo '<tr><td>' . $vHallLabel . '</td><td>' . $vFloorLabel . '</td><td style="text-align:right;"><strong>' . (int)$vCap['cap'] . '</strong></td></tr>';
                                                        }
                                                        if (count($vCapList) > 1) {
                                                            echo '<tr style="opacity:.75;"><td colspan="2">Total</td><td style="text-align:right;">' . $vCapTotal . '</td></tr>';
                                                        }
                                                        echo '</tbody></table>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'InactiveVenue' : 'ActiveVenue') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                    
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="javascript:void(0);" data-id="<?= (int)$rowHallListing['id'] ?>" data-title="<?= htmlspecialchars($rowHallListing['program_venue']) ?>" data-city="<?= htmlspecialchars($rowHallListing['city'] ?? '') ?>" data-airport="<?= htmlspecialchars($rowHallListing['airport_distance'] ?? '') ?>" data-railway="<?= htmlspecialchars($rowHallListing['railway_distance'] ?? '') ?>" data-gmap="<?= htmlspecialchars($rowHallListing['gmap_url'] ?? '') ?>" data-img="<?= htmlspecialchars($rowHallListing['background_img'] ?? '') ?>" data-caps="<?= htmlspecialchars(json_encode($venueCapMap[(int)$rowHallListing['id']] ?? []), ENT_QUOTES) ?>" class="editVenueBtn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeVenue&id=<?= $rowHallListing['id'] ?>" onclick="return confirm('Do you really want to remove this record?')" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="participant_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info">Participant Types</n>
                                <a class="add mi-1 popup-btn" data-tab="newscientificparticipanttype"><?php add() ?>Add Type</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Participant Types</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlParticipantTypeListing					= array();
                                            $sqlParticipantTypeListing['QUERY']		 	= "SELECT * FROM " . _DB_SP_PARTICIPANT_TYPE_ . " WHERE `status` = 'A' ORDER BY type_name";
                                            $resultParticipantTypeListing  			 	= $mycms->sql_select($sqlParticipantTypeListing);
                                            $Counter			 			 			= 0;
                                            if ($resultParticipantTypeListing) {
                                                foreach ($resultParticipantTypeListing as $kkkk => $rowParticipantTypeListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= $rowParticipantTypeListing['type_name'] ?>

                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientificparticipanttype" data-id="<?=$rowParticipantTypeListing['id']?>" data-type="<?=$rowParticipantTypeListing['type_name']?>" class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto editscientificparticipanttypebtn"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeParticipantType&id=<?= $rowParticipantTypeListing['id'] ?>" class="icon_hover badge_danger action-transparent br-5 w-auto"  onclick="return confirm('Do you really want to remove this record?')"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="hall_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box"> 
                            <h5 class="com_info_box_head">
                            <n><span class="text_info">Hall Master</n>
                            <a class="add mi-1 popup-btn" data-tab="newscientifichall"><?php add(); ?>Add Hall</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Hall Title</th>
                                                <th>Venue</th>
                                                <th>Hall Tag</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlHallListing	= array();
                                            $sqlHallListing['QUERY'] = "SELECT hall.*,
                                                                            venue.program_venue 
                                                                        FROM " . _DB_MASTER_HALL_ . " hall
                                                            LEFT OUTER JOIN " . _DB_PROGRAM_SCHEDULE_VENUE_ . " venue
                                                                            ON hall.hall_venue = venue.id 
                                                                        WHERE hall.status != ?";

                                            $sqlHallListing['PARAM'][]  = array('FILD' => 'hall.status',  'DATA' => 'D',  'TYP' => 's');

                                            $resultHallListing   = $mycms->sql_select($sqlHallListing);
                                            $Counter			 = 0;
                                            if ($resultHallListing) {
                                                foreach ($resultHallListing as $keyHallListing => $rowHallListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= $rowHallListing['hall_title'] ?></td>
                                                <td><?= $rowHallListing['program_venue'] ?></td>
                                                <td><?= $rowHallListing['tag_name'] ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowHallListing['status'] == 'A' ? 'HallInactive' : 'HallActive') ?>&id=<?= $rowHallListing['id'] ?>" class="<?= ($rowHallListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowHallListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientifichall" data-id="<?=$rowHallListing['id']?>"  class="popup-btn editscientifichallbtn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeHall&id=<?= $rowHallListing['id'] ?>" onclick="return confirm('Doyou really want to remove this record?');" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="floor_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info">Floor Master</n>
                                <a class="add mi-1" href="javascript:void(0);" id="addFloorBtn"><?php add(); ?>Add Floor</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Floor Name</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlFloorListing			 = array();
                                            $sqlFloorListing['QUERY']	 = "SELECT * FROM `rcg_floor_master` WHERE `status` != ? ORDER BY `floor_name` ASC";

                                            $sqlFloorListing['PARAM'][]  = array('FILD' => 'status',  'DATA' => 'D',  'TYP' => 's');

                                            $resultFloorListing   		 = $mycms->sql_select($sqlFloorListing);
                                            $Counter			 		 = 0;
                                            if ($resultFloorListing) {
                                                foreach ($resultFloorListing as $keyFloorListing => $rowFloorListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= htmlspecialchars($rowFloorListing['floor_name']) ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="scientific_masterdata.php?floor_act=status&to=<?= ($rowFloorListing['status'] == 'A' ? 'I' : 'A') ?>&id=<?= (int)$rowFloorListing['floor_id'] ?>" class="<?= ($rowFloorListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowFloorListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a href="javascript:void(0);" data-id="<?= (int)$rowFloorListing['floor_id'] ?>" data-title="<?= htmlspecialchars($rowFloorListing['floor_name']) ?>" class="editFloorBtn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="scientific_masterdata.php?floor_act=delete&id=<?= (int)$rowFloorListing['floor_id'] ?>" onclick="return confirm('Do you really want to remove this record?');" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="session_master">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info">Session Master</n>
                                <a class="add mi-1 popup-btn" data-tab="addscientificSession"><?php add() ?>Add Session</a>
                            </h5>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Session Type Name</th>
                                                <th class="action">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sqlSessionListing	= array();
                                            $sqlSessionListing['QUERY'] = "SELECT * FROM " . _DB_SESSION_CLASSIFICATION .
                                                " WHERE status != 'D'";
                                            $resultSessionListing   = $mycms->sql_select($sqlSessionListing);
                                            $Counter			 = 0;
                                            if ($resultSessionListing) {
                                                foreach ($resultSessionListing as $keySessionListing => $rowSessionListing) {
                                                    $Counter++;
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $Counter ?></td>
                                                <td><?= $rowSessionListing['session_classifications'] ?></td>
                                                <td>
                                                    <div class="action_div">
                                                        <a href="additional_data.process.php?act=<?= ($rowSessionListing['status'] == 'A' ? 'sessionInactive' : 'sessionActive') ?>&id=<?= $rowSessionListing['id'] ?>" class="<?= ($rowSessionListing['status'] == 'A' ? 'badge_padding badge_success w-max-con text-uppercase' : 'badge_padding badge_danger w-max-con text-uppercase') ?>"><?= ($rowSessionListing['status'] == 'A' ? 'Active' : 'Inactive') ?></a>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <a data-tab="editscientificSession" data-id="<?=$rowSessionListing['id']?>" data-stype="<?=$rowSessionListing['session_classifications']?>" class="popup-btn editscientificSessionbtn icon_hover badge_secondary action-transparent br-5 w-auto"><?php edit(); ?></a>
                                                        <a href="additional_data.process.php?act=removeSession&id=<?= $rowSessionListing['id'] ?>"  onclick="return confirm('Doyou really want to remove this record?');" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="floor_modal" id="venueModal">
        <div class="floor_modal_box venue_modal_box">
            <form method="post" action="scientific_masterdata.php" id="venueForm" enctype="multipart/form-data">
                <input type="hidden" name="venue_act" id="venue_act_field" value="add" />
                <input type="hidden" name="venue_id" id="venue_id_field" value="" />
                <input type="hidden" name="prev_image" id="venue_prev_image" value="" />
                <h5 id="venueModalTitle">Add Venue</h5>
                <div class="frm_grp">
                    <p class="frm-head">Venue Title <span class="mandatory">*</span></p>
                    <input type="text" name="venue_title" id="venue_title" required />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">City <span class="mandatory">*</span></p>
                    <input type="text" name="city" id="venue_city" required />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Distance from Airport</p>
                    <input type="text" name="airport_distance" id="venue_airport" />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Distance from Railway Station</p>
                    <input type="text" name="railway_distance" id="venue_railway" />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Map URL <span class="mandatory">*</span></p>
                    <input type="text" name="gmap_url" id="venue_gmap" required />
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Background Image <span class="mandatory" id="venueImgStar">*</span></p>
                    <input type="file" name="image" id="venue_image" accept=".jpg,.jpeg,.png,.svg" />
                    <small id="venueCurrentImg" style="display:none;"></small>
                </div>
                <div class="frm_grp">
                    <p class="frm-head">Hall &amp; Floor Capacity</p>
                    <div class="venue_cap_head"><span>Hall</span><span>Floor</span><span>Capacity</span><span></span></div>
                    <div id="venueCapRows"></div>
                    <button type="button" class="badge_primary" id="venueAddCapRow"><i class="fal fa-plus"></i> Add capacity row</button>
                </div>
                <div class="floor_modal_actions">
                    <button type="button" class="badge_dark" id="venueModalCancel">Cancel</button>
                    <button type="submit" class="badge_success">Save</button>
                </div>
            </form>
            <template id="venueCapTemplate">
                <div class="venue_cap_row">
                    <select name="cap_hall_id[]"><?= $venueHallOptions ?></select>
                    <select name="cap_floor_id[]"><?= $venueFloorOptions ?></select>
                    <input type="number" name="cap_capacity[]" min="1" placeholder="Capacity" />
                    <button type="button" class="venueCapRemove" aria-label="Remove row">&times;</button>
                </div>
            </template>
        </div>
    </div>
    <div class="floor_modal" id="floorModal">
        <div class="floor_modal_box">
            <form method="post" action="scientific_masterdata.php" id="floorForm">
                <input type="hidden" name="floor_act" id="floor_act_field" value="add" />
                <input type="hidden" name="floor_id" id="floor_id" value="" />
                <h5 id="floorModalTitle">Add Floor</h5>
                <div class="frm_grp">
                    <p class="frm-head">Floor Name <span class="mandatory">*</span></p>
                    <input type="text" name="floor_name" id="floor_name" maxlength="255" required />
                </div>
                <div class="floor_modal_actions">
                    <button type="button" class="badge_dark" id="floorModalCancel">Cancel</button>
                    <button type="submit" class="badge_success">Save</button>
                </div>
            </form>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    $('.com_info_left_click').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".com_info_box").removeClass("active");
        $(".com_info_left_click").removeClass("active").addClass('action-transparent');
        $('#' + tabId).addClass("active");
        $(this).addClass("active").removeClass('action-transparent');
    });
    $('.com_info_box_content_sec_left_click').click(function() {
        var tabId = $(this).attr('data-tab');
        $(".com_info_box_content_sec_right_box").removeClass("active");
        $(".com_info_box_content_sec_left_click").removeClass("active").addClass('action-transparent');
        $('#' + tabId).addClass("active");
        $(this).addClass("active").removeClass('action-transparent');
    });
    $(document).ready(function() {
        // Check if URL has hash
        var hash = window.location.hash.substring(1); // removes '#'
        if(hash) {
            // Trigger the tab click for that hash
            var btn = $('.com_info_left_click[data-tab="' + hash + '"]');
            if(btn.length) {
                btn.trigger('click');
            }
        }
    });
     ///////////////////speaker edit start///////////////////////////
       $(document).on('click', '.editspeakerbtn', function() {
            //    console.log($(this).data('package'));
        let speakerHId   = $(this).data('id');
       
        
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                speakerHId: speakerHId
               
            },
             success: function(response) {

        $('#editspeaker').html($(response).find('#editspeaker').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initeditspeaker();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
         ///////////////////speaker edit end///////////////////////////
           ///////////////////date edit start///////////////////////////
       $(document).on('click', '.editscientificdatebtn', function() {
            //    console.log($(this).data('package'));
        let dateId   = $(this).data('id');
       
        
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                dateId: dateId
               
            },
             success: function(response) {

        $('#editscientificdate').html($(response).find('#editscientificdate').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initeditscientificdater();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
         ///////////////////date edit end///////////////////////////
         ///////////////////vanue edit start///////////////////////////

     $(document).on('click', '.editscientificvenuebtn', function() {
        var venue_id     = $(this).data('id');
        var title  = $(this).data('title');
       
        $('#venue_id').val(venue_id);
        $('#session_venue_edit').val(title);
     

        // if(status === 'A') {
        //     $('#edit_status_active').prop('checked', true);
        // } else {
        //     $('#edit_status_inactive').prop('checked', true);
        // }

        // Trigger the popup-btn functionality
     $('#editscientificvenue').fadeIn(); // or your popup open function
    });
             ///////////////////vanue edit end///////////////////////////
               ///////////////////participant edit start///////////////////////////

     $(document).on('click', '.editscientificparticipanttypebtn', function() {
        var participant_type_id     = $(this).data('id');
        var type  = $(this).data('type');
       
        $('#participant_type_id').val(participant_type_id);
        $('#type_name_edit').val(type);
     

    
        // Trigger the popup-btn functionality
     $('#editscientificparticipanttype').fadeIn(); // or your popup open function
    });
             ///////////////////participant edit end///////////////////////////
           /////////////////// hall start///////////////////////////
       $(document).on('click', '.editscientifichallbtn', function() {
            //    console.log($(this).data('package'));
        let hallId   = $(this).data('id');
       
        
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                hallId: hallId
               
            },
             success: function(response) {

        $('#editscientifichall').html($(response).find('#editscientifichall').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initeditscientifichall();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });
        // Trigger the popup-btn functionality
   
    });
         ///////////////////hall edit end///////////////////////////
         ///////////////////venue start///////////////////////////
    function venueAddCapRow(hall, floor, cap) {
        var $row = $($.trim($('#venueCapTemplate').html()));
        if (hall) { $row.find('select[name="cap_hall_id[]"]').val(String(hall)); }
        if (floor) { $row.find('select[name="cap_floor_id[]"]').val(String(floor)); }
        if (cap) { $row.find('input[name="cap_capacity[]"]').val(cap); }
        $('#venueCapRows').append($row);
    }
    function venueOpenModal(mode, d) {
        d = d || {};
        $('#venueForm')[0].reset();
        $('#venueCapRows').empty();
        $('#venue_act_field').val(mode);
        $('#venue_id_field').val(d.id || '');
        $('#venue_prev_image').val(d.img || '');
        $('#venue_title').val(d.title || '');
        $('#venue_city').val(d.city || '');
        $('#venue_airport').val(d.airport || '');
        $('#venue_railway').val(d.railway || '');
        $('#venue_gmap').val(d.gmap || '');
        $('#venueModalTitle').text(mode === 'add' ? 'Add Venue' : 'Edit Venue');
        $('#venue_image').prop('required', mode === 'add');
        $('#venueImgStar').toggle(mode === 'add');
        if (mode === 'edit' && d.img) {
            $('#venueCurrentImg').text('Current: ' + d.img + ' (choose a file only to replace it)').show();
        } else {
            $('#venueCurrentImg').hide();
        }
        var caps = d.caps || [];
        if (typeof caps === 'string') { try { caps = JSON.parse(caps); } catch (e) { caps = []; } }
        if (caps.length) {
            $.each(caps, function(i, c) { venueAddCapRow(c.hall, c.floor, c.cap); });
        } else {
            venueAddCapRow();
        }
        $('#venueModal').addClass('open');
        $('#venue_title').trigger('focus');
    }
    $(document).on('click', '#addVenueBtn', function() { venueOpenModal('add'); });
    $(document).on('click', '.editVenueBtn', function() {
        var $b = $(this);
        venueOpenModal('edit', {
            id: $b.data('id'), title: $b.attr('data-title'), city: $b.attr('data-city'),
            airport: $b.attr('data-airport'), railway: $b.attr('data-railway'),
            gmap: $b.attr('data-gmap'), img: $b.attr('data-img'), caps: $b.attr('data-caps')
        });
    });
    $(document).on('click', '#venueAddCapRow', function() { venueAddCapRow(); });
    $(document).on('click', '.venueCapRemove', function() { $(this).closest('.venue_cap_row').remove(); });
    $(document).on('click', '#venueModalCancel', function() { $('#venueModal').removeClass('open'); });
    $(document).on('click', '#venueModal', function(e) { if (e.target === this) { $(this).removeClass('open'); } });
         ///////////////////venue end///////////////////////////
         ///////////////////floor start///////////////////////////
    $(document).on('click', '#addFloorBtn', function() {
        $('#floor_act_field').val('add');
        $('#floor_id').val('');
        $('#floor_name').val('');
        $('#floorModalTitle').text('Add Floor');
        $('#floorModal').addClass('open');
        $('#floor_name').trigger('focus');
    });
    $(document).on('click', '.editFloorBtn', function() {
        $('#floor_act_field').val('edit');
        $('#floor_id').val($(this).data('id'));
        $('#floor_name').val($(this).data('title'));
        $('#floorModalTitle').text('Edit Floor');
        $('#floorModal').addClass('open');
        $('#floor_name').trigger('focus');
    });
    $(document).on('click', '#floorModalCancel', function() { $('#floorModal').removeClass('open'); });
    $(document).on('click', '#floorModal', function(e) { if (e.target === this) { $(this).removeClass('open'); } });
    $(function() { if ($('#floorAlert').length) { setTimeout(function() { $('#floorAlert').fadeOut(300); }, 4000); } });
    $(document).on('click', '#floorAlertClose', function() { $('#floorAlert').fadeOut(200); });
         ///////////////////floor end///////////////////////////
           ///////////////////session edit start///////////////////////////

     $(document).on('click', '.editscientificSessionbtn', function() {
        var sessionId     = $(this).data('id');
        var stype  = $(this).data('stype');
       
        $('#sessionId').val(sessionId);
        $('#session_type_edit').val(stype);
     

    
        // Trigger the popup-btn functionality
     $('#editscientificSession').fadeIn(); // or your popup open function
    });
             ///////////////////session edit end///////////////////////////
</script>

</html>