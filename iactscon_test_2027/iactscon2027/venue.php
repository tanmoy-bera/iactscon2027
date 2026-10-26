<?php
include_once("./includes/source.php");
include_once('./includes/init.php');
include_once('./includes/function.workshop.php');
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Venue Setup</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $show = $_REQUEST['show'] ?? 'venue';
        ?>

        <div class="app_tab_content" id="venue_setup">
            <?php
            switch ($show) {

                case 'venue':
                    venueList($cfg, $mycms);
                    break;

                case 'addVenue':
                    addVenueFormWindow($cfg, $mycms);
                    break;

                case 'editVenue':
                    editVenueFormWindow($cfg, $mycms);
                    break;
            }
            ?>
        </div>
    </div>
    <?php include_once("includes/app_popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>
<?php

/****************************************************************************/
/*                              VENUE                                       */
/****************************************************************************/
function venueList($cfg, $mycms)
{
    global $cfg, $mycms;

    $sqlVenueListing         = array();
    $sqlVenueListing['QUERY'] = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_VENUE_ . " WHERE `status` != ?";
    $sqlVenueListing['PARAM'][] = array('FILD' => 'status', 'DATA' => 'D', 'TYP' => 's');

    $resultVenueListing = $mycms->sql_select($sqlVenueListing);
    ?>

    <div class="venue_hall_top">
        <h5>
            <n><i class="fal fa-map-marker-alt"></i>Conference Venues & Hall Locations</n>
            <l>Configure primary convention grounds, banquet halls, floor counts & nearby transport links.</l>
        </h5>
        <a href="venue.php?show=addVenue" class="badge_default popup-btn" data-tab="appaddvenue"><?php add() ?>Add Venue</a>
    </div>

    <div class="venue_grid">
        <?php
        if ($resultVenueListing) {
            foreach ($resultVenueListing as $rowVenueListing) {
                $isPrimary = ($rowVenueListing['status'] == 'A');
        ?>
        <div class="venue_box <?= $isPrimary ? 'active' : '' ?>">
            <div class="venue_box_top">
                <h5>
                    <n><?= htmlspecialchars($rowVenueListing['program_venue']) ?></n>
                    <l><?= htmlspecialchars($rowVenueListing['city']) ?></l>
                </h5>
                <div class="venue_box_top_right">
                    <?php if ($isPrimary) { ?>
                        <button class="badge_success">Primary Venue</button>
                    <?php } else { ?>
                        <a href="additional_data.process.php?act=ActiveVenue&id=<?= $rowVenueListing['id'] ?>" class="badge_dark">Set Primary</a>
                    <?php } ?>
                    <div class="action_div action">
                        <a href="additional_data.process.php?act=removeVenue&id=<?= $rowVenueListing['id'] ?>"
                           class="icon_hover badge_danger br-5 w-auto action-transparent"
                           onclick="return confirm('Do you really want to remove this record?')">
                            <i class="fal fa-trash-alt"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="venue_box_mid">
                <?php if (!empty($rowVenueListing['background_img'])) { ?>
                    <img src="<?php echo '../../' . $cfg['SP.PARTICIPANT.DOC'] . $rowVenueListing['background_img']; ?>" alt="<?= htmlspecialchars($rowVenueListing['program_venue']) ?>">
                <?php } ?>
            </div>

            <div class="venue_box_bottom">
                <p>Premier convention facility with halls, networking zones and modern amenities.</p>
                <ul>
                    <?php if (!empty($rowVenueListing['airport_distance'])) { ?>
                        <li><i class="fal fa-plane"></i>Airport: <?= htmlspecialchars($rowVenueListing['airport_distance']) ?></li>
                    <?php } ?>
                    <?php if (!empty($rowVenueListing['railway_distance'])) { ?>
                        <li><i class="fal fa-train"></i>Railway: <?= htmlspecialchars($rowVenueListing['railway_distance']) ?></li>
                    <?php } ?>
                    <?php if (!empty($rowVenueListing['city'])) { ?>
                        <li><i class="fal fa-map-marker-alt"></i><?= htmlspecialchars($rowVenueListing['city']) ?></li>
                    <?php } ?>
                </ul>
                <?php if (!empty($rowVenueListing['gmap_url'])) { ?>
                    <a href="<?= htmlspecialchars($rowVenueListing['gmap_url']) ?>" target="_blank" class="badge_dark">
                        <i class="fal fa-map-marker-alt"></i>Open in Google Maps
                    </a>
                <?php } ?>
                <a href="venue.php?show=editVenue&id=<?= $rowVenueListing['id'] ?>" class="badge_secondary mt-2">
                    <i class="fal fa-edit"></i>Edit Venue
                </a>
            </div>
        </div>
        <?php
            }
        } else {
        ?>
            <div class="venue_box" style="width:100%;text-align:center;">
                <span class="mandatory">No Venue Record.</span>
            </div>
        <?php } ?>
    </div>

<?php
}

/****************************************************************************/
/*                          ADD VENUE                                       */
/****************************************************************************/
function addVenueFormWindow($cfg, $mycms)
{
?>
    <div class="venue_hall_top">
        <h5>
            <n><i class="fal fa-map-marker-alt"></i>Add Venue</n>
            <l>Add a new conference venue with location details and map link.</l>
        </h5>
        <div class="page_top_wrap_right">
            <a href="venue.php" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <form name="frmInsertSession" id="frmInsertSession" action="additional_data.process.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="act" value="insertVenue" />

        <div class="form_grid g_3">
            <div class="frm_grp">
                <p class="frm-head">Venue Title <span class="mandatory">*</span></p>
                <input type="text" name="session_venue_add" id="session_venue_add" required>
            </div>
            <div class="frm_grp">
                <p class="frm-head">City <span class="mandatory">*</span></p>
                <input type="text" name="city" id="city" required>
            </div>
            <div class="frm_grp">
                <p class="frm-head">Distance from Airport</p>
                <input type="text" name="airport_distance" id="airport_distance">
            </div>
            <div class="frm_grp">
                <p class="frm-head">Distance from Railway Station</p>
                <input type="text" name="railway_distance" id="railway_distance">
            </div>
            <div class="frm_grp">
                <p class="frm-head">Map URL <span class="mandatory">*</span></p>
                <input type="text" name="gmap_url" id="gmap_url" required>
            </div>
            <div class="frm_grp">
                <p class="frm-head">Background Image <span class="mandatory">*</span></p>
                <input type="file" name="image" id="background_img" required>
            </div>
        </div>

        <div class="page_top_wrap mt-3">
            <div class="page_top_wrap_left">
                <button type="submit" name="Save" id="Save" class="badge_success">
                    <i class="fal fa-save"></i> Insert Venue
                </button>
                <a href="venue.php" class="badge_dark"><i class="fal fa-times"></i> Cancel</a>
            </div>
        </div>
    </form>
<?php
}

/****************************************************************************/
/*                          EDIT VENUE                                      */
/****************************************************************************/
function editVenueFormWindow($cfg, $mycms)
{
    $sessionId = addslashes(trim($_REQUEST['id']));

    $sqlHallListing            = array();
    $sqlHallListing['QUERY']   = "SELECT * FROM " . _DB_PROGRAM_SCHEDULE_VENUE_ . " WHERE `id` = '" . $sessionId . "'";
    $resultHallListing         = $mycms->sql_select($sqlHallListing);
    $rowProgramSession         = $resultHallListing[0];
?>
    <div class="venue_hall_top">
        <h5>
            <n><i class="fal fa-map-marker-alt"></i>Edit Venue</n>
            <l>Update venue details or replace the background image.</l>
        </h5>
        <div class="page_top_wrap_right">
            <a href="venue.php" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <form name="frmUpdateSession" id="frmUpdateSession" action="additional_data.process.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="act" value="updateVenue" />
        <input type="hidden" name="venue_id" value="<?= $rowProgramSession['id'] ?>" />

        <div class="form_grid g_3">
            <div class="frm_grp">
                <p class="frm-head">Venue Title <span class="mandatory">*</span></p>
                <input type="text" id="venuetitle" name="venuetitle" value="<?= htmlspecialchars($rowProgramSession['program_venue']) ?>" required>
            </div>
            <div class="frm_grp">
                <p class="frm-head">City <span class="mandatory">*</span></p>
                <input type="text" name="city" id="city" required value="<?= htmlspecialchars($rowProgramSession['city']) ?>">
            </div>
            <div class="frm_grp">
                <p class="frm-head">Distance from Airport</p>
                <input type="text" name="airport_distance" id="airport_distance" value="<?= htmlspecialchars($rowProgramSession['airport_distance']) ?>">
            </div>
            <div class="frm_grp">
                <p class="frm-head">Distance from Railway Station</p>
                <input type="text" name="railway_distance" id="railway_distance" value="<?= htmlspecialchars($rowProgramSession['railway_distance']) ?>">
            </div>
            <div class="frm_grp">
                <p class="frm-head">Map URL <span class="mandatory">*</span></p>
                <input type="text" name="gmap_url" id="gmap_url" required value="<?= htmlspecialchars($rowProgramSession['gmap_url']) ?>">
            </div>
            <div class="frm_grp">
                <p class="frm-head">Background Image</p>
                <input type="file" name="image" id="background_img">
                <input type="hidden" name="prev_image" id="prev_image" value="<?= $rowProgramSession['background_img'] ?>">
                <?php if (!empty($rowProgramSession['background_img'])) { ?>
                    <br><img src="<?php echo '../../' . $cfg['SP.PARTICIPANT.DOC'] . $rowProgramSession['background_img']; ?>" height="70">
                <?php } ?>
            </div>
        </div>

        <div class="page_top_wrap mt-3">
            <div class="page_top_wrap_left">
                <button type="submit" name="Save" id="Save" class="badge_success">
                    <i class="fal fa-save"></i> Update Venue
                </button>
                <a href="venue.php" class="badge_dark"><i class="fal fa-times"></i> Cancel</a>
            </div>
        </div>
    </form>
<?php
}
?>