<?php
include_once("includes/source.php");
include_once('includes/init.php');
include_once('includes/function.php');
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Id Card</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Spot</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Counter Master</li>
                    </ol>
                </nav>
                <h2>Counter Master</h2>
                <h6>Manage counter box number.</h6>
            </div>
        </div>

        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input id="searchInput" name="q" placeholder="Search by Box, Counter or Venue..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" id="addBoxBtn" class="add"><?php add(); ?>Add Box</a>
            </div>
        </div>

        <div class="spot_listing" id="spotListing">
            <?php
            $searchCondition = "";
            if (!empty($_GET['q'])) {
                $search = addslashes(trim($_GET['q']));
                $searchCondition = " AND (box_number LIKE '%$search%'
                                       OR counter_number LIKE '%$search%'
                                       OR venue LIKE '%$search%')";
            }

            $sqlBoxes = array();
            $sqlBoxes['QUERY'] = "SELECT * FROM `rcg_id_print_box_master_for_delegate`
                                   WHERE `status` != 'D' " . $searchCondition . "
                                ORDER BY `sequenceBy` ASC";
            $indexVal  = 1;
            $resultBoxes = $mycms->sql_select_paginated($indexVal, $sqlBoxes, 10, $restrt);

            $boxCount = 0;
            if ($resultBoxes) {
                foreach ($resultBoxes as $row) {
                    $boxCount++;
            ?>
            <div class="spot_box">
                <div class="spot_box_top">
                    <div class="spot_name d-flex align-items-start">
                        <div class="regi_img_circle">
                            <span><?= $boxCount + ((int)($_REQUEST['_pgn1_'] ?? 0) * 10) ?></span>
                        </div>
                        <div>
                            <div class="regi_name">Box <?= htmlspecialchars($row['box_number']) ?></div>
                            <div class="regi_type">
                                <span class="badge_padding badge_primary">Counter <?= htmlspecialchars($row['counter_number']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="spot_details">
                        <div class="spot_details_box">
                            <h5>Venue</h5>
                            <h6><?= htmlspecialchars($row['venue'] ?? '') ?: '-' ?></h6>
                        </div>
                        <div class="spot_details_box">
                            <h5>Card Capacity</h5>
                            <h6><?= (int)$row['card_capacity'] ?></h6>
                        </div>
                    </div>
                </div>
                <div class="spot_box_bottom accm_bottom">
                    <div class="spot_box_bottom_left"></div>
                    <div class="spot_box_bottom_right">
                        <a href="javascript:void(0)"
                           class="badge_secondary icon_hover action-transparent editBoxBtn"
                           data-id="<?= (int)$row['id'] ?>"
                           data-box="<?= htmlspecialchars($row['box_number']) ?>"
                           data-counter="<?= htmlspecialchars($row['counter_number']) ?>"
                           data-venue="<?= htmlspecialchars($row['venue'] ?? '') ?>"
                           data-capacity="<?= (int)$row['card_capacity'] ?>"><?php edit(); ?>Edit</a>
                    </div>
                </div>
            </div>
            <?php }
            } else { ?>
                <span class="mandatory" align="center">No Record Present.</span>
            <?php } ?>
        </div>

        <div class="bbp-pagination">
            <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
            <span class="paginationDisplay">
                <div class="pagination"><a><?= $mycms->paginate(1, 'pagination') ?></a></div>
            </span>
        </div>
    </div>
</body>
<?php include_once("includes/js-source.php"); ?>

<!-- Add / Edit pop up -->
<div class="pop_up_wrap">
    <div class="pop_up_inner">
        <div class="pop_up_body" id="counterPopup">
            <form name="frmCounterBox" id="frmCounterBox" method="post">
                <input type="hidden" name="act" id="cm_act" value="add" />
                <input type="hidden" name="id" id="cm_id" value="" />
                <div class="registration_pop_up">
                    <div class="registration-pop_heading">
                        <span id="cm_title">Add Box</span>
                        <p><a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a></p>
                    </div>
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Box Number</p>
                                        <input type="text" id="cm_box_number" disabled>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Counter Number</p>
                                        <input type="text" id="cm_counter_number" disabled>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Card Capacity <i class="mandatory">*</i></p>
                                        <input type="number" name="card_capacity" id="cm_card_capacity" min="1" required>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Venue <i class="mandatory">*</i></p>
                                        <input type="text" name="venue" id="cm_venue" maxlength="255" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success" id="cm_save"><?php save(); ?>Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add / Edit pop up -->

<script>
var CM_URL = 'counter_master.process.php';

function cmNotify(type, msg) {
    if (typeof showToaster === 'function') showToaster(type, msg); else alert(msg);
}
function cmOpen(title) {
    $('#cm_title').text(title);
    $('.pop_up_wrap').show();
    $('#counterPopup').show();
}
function cmClose() {
    $('.pop_up_wrap, .pop_up_body').hide();
}

// ---- ADD: next box / counter are fetched from the table and locked
$('#addBoxBtn').on('click', function () {
    $('#frmCounterBox')[0].reset();
    $('#cm_act').val('add');
    $('#cm_id').val('');
    $('#cm_box_number, #cm_counter_number').val('Loading...');

    $.post(CM_URL, { act: 'nextBox' }, null, 'json')
        .done(function (res) {
            if (res.status !== 'success') { cmNotify('error', res.message); return; }
            $('#cm_box_number').val(res.data.box_number);
            $('#cm_counter_number').val(res.data.counter_number);
            cmOpen('Add Box');
        })
        .fail(function () { cmNotify('error', 'Could not fetch the next box number.'); });
});

// ---- EDIT: only capacity and venue are editable
$(document).on('click', '.editBoxBtn', function () {
    var $b = $(this);
    $('#cm_act').val('edit');
    $('#cm_id').val($b.data('id'));
    $('#cm_box_number').val($b.data('box'));
    $('#cm_counter_number').val($b.data('counter'));
    $('#cm_card_capacity').val($b.data('capacity'));
    $('#cm_venue').val($b.data('venue'));
    cmOpen('Edit Box');
});

$(document).on('click', '.popup_close', function (e) { e.preventDefault(); cmClose(); });

$('#frmCounterBox').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#cm_save'), html = $btn.html();
    $btn.prop('disabled', true).text('Saving...');

    $.post(CM_URL, $(this).serialize(), null, 'json')
        .done(function (res) {
            if (res.status === 'success') {
                cmClose();
                cmNotify('success', res.message);
                setTimeout(function () { location.reload(); }, 600);
            } else {
                cmNotify('error', res.message || 'Something went wrong.');
            }
        })
        .fail(function (xhr) {
            console.error(xhr.responseText);
            cmNotify('error', 'Something went wrong while saving.');
        })
        .always(function () { $btn.prop('disabled', false).html(html); });
});

// ---- search
var typingTimer;
$('#searchInput').on('keyup', function () {
    clearTimeout(typingTimer);
    var q = $(this).val().trim();
    typingTimer = setTimeout(function () {
        var url = new URL(window.location.href);
        if (q) url.searchParams.set('q', q); else url.searchParams.delete('q');
        url.searchParams.delete('_pgn1_');
        window.location.href = url.toString();
    }, 800);
}).on('keydown', function () { clearTimeout(typingTimer); });
</script>
</html>