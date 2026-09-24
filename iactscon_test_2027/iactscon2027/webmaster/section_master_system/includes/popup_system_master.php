<script language="javascript" src="<?= $cfg['SECTION_BASE_URL'] ?>scripts/select2.min.js"></script>
<link rel="stylesheet" href="<?= $cfg['SECTION_BASE_URL'] ?>css/select2.min.css">
<div class="pop_up_wrap">
    <div class="pop_up_inner">

        <!-- New combo pop up -->
        <div class="pop_up_body" id="newcombo">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Registration Combo Classification</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Regsitration Classification</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Package Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Cutoff <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Registration Classification</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Registration Price <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Workshop Price<i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Workshop Classification <i class="mandatory">*</i></p>
                                    <select class="mySelect for" multiple="multiple" style="width: 100%;">
                                        <option>TB & Critical Care Workshop</option>
                                        <option>TB & Critical Care Workshop</option>
                                        <option>TB & Critical Care Workshop</option>
                                        <option>TB & Critical Care Workshop</option>
                                    </select>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Dinner Price</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Lunch Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Conference Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Scientific Halls</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Exhibition Area</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Tea/Coffee during the Conference</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Conference Kit</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="food">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Hotel Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Hotel</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Room Type <i class="mandatory">*</i></p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Accommodation Price (Individual)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Accommodation Price (Shared)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2 accm_add_box pl-0 pt-0 pb-0 bg-transparent">
                                    <p class="frm-head">No. Of Night</p>
                                    <input>
                                    <a href="#" class="accm_delet icon_hover badge_primary action-transparent"><i class="fal fa-paper-plane"></i></a>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Seat limit</p>
                                    <input>
                                </div>
                                <div class="span_4 accm_add_box badge_padding">
                                    <div class="table_wrap">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Check In Date</th>
                                                    <th>Check Out Date</th>
                                                    <th>INR Rate</th>
                                                    <th>USD Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2026-01-17</td>
                                                    <td>2026-01-18</td>
                                                    <td>5000.00</td>
                                                    <td>16.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="frm_grp span_2">
                                    <p class="frm-head">Sequence By</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Currency</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Round Of price (Individual)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Round Of price (Shared)</p>
                                    <input>
                                </div>
                            </div>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_danger py-2 px-2">Total Price (Individual)<span class="text-white mt-0">₹ 12,980</span></h6>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_danger py-2 px-2">Total Price (Shared)<span class="text-white mt-0">₹ 12,980</span></h6>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_success py-2 px-2">Total Round Of Price (Individual)<span class="text-white mt-0">₹ 12,980</span></h6>
                            <h6 class="due_balance mt-3 d-flex justify-content-between align-items-center badge_success py-2 px-2">Total Round Of Price (Shared)<span class="text-white mt-0">₹ 12,980</span></h6>
                        </div>

                    </div>

                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New combo pop up -->

        <!-- Add registration Cutoff pop up -->
        <div class="pop_up_body" id="addregistrationcutoff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Registration Cutoff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent">
                            <?php close(); ?>
                        </a>
                    </p>
                </div>

                <form id="cutoffForm">
                    <input type="hidden" name="act" value="insert">
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Registration Cutoff Title</p>
                                        <input type="text" name="title" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Date</p>
                                        <input type="date" name="start_date" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Date</p>
                                        <input type="date" name="end_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script>
            $('#cutoffForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: 'manage_cutoff.process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            toastr.success(res.message);
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function() {
                        toastr.error('Server error. Please try again.');
                    }
                });
            });
        </script>
        <!-- Add registration Cutoff pop up -->

        <!-- edit registration Cutoff pop up -->
        <div class="pop_up_body" id="editregistrationcutoff" style="display:none;">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Registration Cutoff</span>
                    <p>
                        <a href="javascript:void(0)" class="popup_close icon_hover badge_danger action-transparent" data-close-popup><?php close(); ?></a>
                    </p>
                </div>

                <form id="editCutoffForm">
                    <input type="hidden" name="act" value="update">
                    <input type="hidden" name="cutoff_id" id="edit_cutoff_id" value="">
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Registration Cutoff Title</p>
                                        <input type="text" name="cutoff_title" id="edit_cutoff_title" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Date</p>
                                        <input type="date" name="start_date" id="edit_start_date" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Date</p>
                                        <input type="date" name="end_date" id="edit_end_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark" data-close-popup>Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function loadDataOnEditCutoff(id) {
                $.getJSON('manage_cutoff.process.php', {
                    act: 'getCutoff',
                    id: id
                }, function(res) {
                    if (res && res.status) {
                        var d = res.data;
                        $('#edit_cutoff_id').val(d.id);
                        $('#edit_cutoff_title').val(d.cutoff_title);
                        $('#edit_start_date').val(d.start_date);
                        $('#edit_end_date').val(d.end_date);
                        // show popup (adjust depending on your popup display mechanism)
                        $('#editregistrationcutoff').show();
                    } else {
                        toastr.error(res.message || 'Could not fetch record');
                    }
                }).fail(function() {
                    toastr.error('Server error while fetching record');
                });
            }

            $('#editCutoffForm').on('submit', function(e) {
                e.preventDefault();
                var $f = $(this);
                $.ajax({
                    url: 'manage_cutoff.process.php',
                    type: 'POST',
                    data: $f.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Updated');
                            $('#editregistrationcutoff').hide();
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            toastr.error(res.message || 'Update failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error. Please try again.');
                    }
                });
            });

            // $(document).on('click', '.open-edit-cutoff', function(e) {
            //     e.preventDefault();
            //     var id = $(this).data('id');
            //     if (id) openEditCutoff(id);
            // });
        </script>
        <!-- edit registration Cutoff pop up -->

        <!-- Add workshop Cutoff pop up -->
        <div class="pop_up_body" id="addworkshopcutoff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Workshop Cutoff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>

                <form id="workshopCutoffForm">
                    <input type="hidden" name="act" value="insertWorkshop">
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Cutoff Title</p>
                                        <input type="text" name="workshop_add" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Date</p>
                                        <input type="date" name="start_date" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Date</p>
                                        <input type="date" name="end_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            $('#workshopCutoffForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'manage_cutoff.process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Workshop cutoff added');
                            setTimeout(function() {
                                location.reload();
                            }, 1200);
                        } else {
                            toastr.error(res.message || 'Save failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error. Please try again.');
                    }
                });
            });
        </script>
        <!-- Add workshop Cutoff pop up -->

        <!-- edit workshop Cutoff pop up -->
        <div class="pop_up_body" id="editworkshopcutoff" style="display:none;">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Workshop Cutoff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>

                <form id="editWorkshopCutoffForm">
                    <input type="hidden" name="act" value="updateWorkshop">
                    <input type="hidden" name="cutoff_id" id="edit_workshop_cutoff_id" value="">
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Cutoff Title</p>
                                        <input type="text" name="cutoff_title" id="edit_workshop_cutoff_title" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Start Date</p>
                                        <input type="date" name="start_date" id="edit_workshop_start_date" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">End Date</p>
                                        <input type="date" name="end_date" id="edit_workshop_end_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark" data-close-popup>Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function loadDataOnEditWorkshopCutoff(id) {
                $.getJSON('manage_cutoff.process.php', {
                    act: 'getWorkshop',
                    id: id
                }, function(res) {
                    if (res && res.status) {
                        var d = res.data;
                        $('#edit_workshop_cutoff_id').val(d.id);
                        $('#edit_workshop_cutoff_title').val(d.cutoff_title);
                        $('#edit_workshop_start_date').val(d.start_date);
                        $('#edit_workshop_end_date').val(d.end_date);
                        $('#editworkshopcutoff').show();
                    } else {
                        toastr.error(res.message || 'Could not fetch record');
                    }
                }).fail(function() {
                    toastr.error('Server error while fetching record');
                });
            }

            $('#editWorkshopCutoffForm').on('submit', function(e) {
                e.preventDefault();
                var $f = $(this);
                $.ajax({
                    url: 'manage_cutoff.process.php',
                    type: 'POST',
                    data: $f.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Updated');
                            $('#editworkshopcutoff').hide();
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(res.message || 'Update failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error. Please try again.');
                    }
                });
            });

            // close popup
            // $(document).on('click', '[data-close-popup]', function() {
            //     $(this).closest('.pop_up_body').hide();
            // });

            // delegate open handlers
            // $(document).on('click', '.open-edit-workshop', function(e) {
            //     e.preventDefault();
            //     var id = $(this).data('id');
            //     if (id) loadDataOnEditWorkshopCutoff(id);
            // });
        </script>
        <!-- edit workshop Cutoff pop up -->

        <!-- Add conference date pop up -->
        <div class="pop_up_body" id="addconferencedate" style="display:none;">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Conference Date</span>
                    <p>
                        <a href="javascript:void(0)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form id="addConferenceDateForm">
                    <input type="hidden" name="act" value="insertDate">
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Conference Date</p>
                                        <input type="date" name="conf_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit conference date pop up -->
        <div class="pop_up_body" id="editconferencedate" style="display:none;">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Conference Date</span>
                    <p>
                        <a href="javascript:void(0)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form id="editConferenceDateForm">
                    <input type="hidden" name="act" value="updateDate">
                    <input type="hidden" name="date_id" id="edit_date_id" value="">
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Conference Date</p>
                                        <input type="date" name="conf_date" id="edit_conf_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Add conference date via AJAX
            $('#addConferenceDateForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'manage_cutoff.process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Conference date added');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(res.message || 'Save failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error. Please try again.');
                    }
                });
            });

            // Load single date into edit popup
            function loadDataOnEditConference(id) {
                $.getJSON('manage_cutoff.process.php', {
                    act: 'getDate',
                    id: id
                }, function(res) {
                    if (res && res.status) {
                        $('#edit_date_id').val(res.data.id);
                        $('#edit_conf_date').val(res.data.conf_date);
                        $('#editconferencedate').show();
                    } else {
                        toastr.error(res.message || 'Could not fetch record');
                    }
                }).fail(function() {
                    toastr.error('Server error while fetching record');
                });
            }

            // Update conference date via AJAX
            $('#editConferenceDateForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'manage_cutoff.process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Updated');
                            $('#editconferencedate').hide();
                            setTimeout(function() {
                                location.reload();
                            }, 800);
                        } else {
                            toastr.error(res.message || 'Update failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error. Please try again.');
                    }
                });
            });
        </script>
        <!-- Add conference date pop up -->

        <!-- Add workshop type pop up -->
        <div class="pop_up_body" id="addworkshoptype">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Workshop Type</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Workshop Type</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Status</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Active</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Inactive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add workshop type pop up -->

        <!-- Add workshop pop up -->
        <form>
            <div class="pop_up_body" id="addworkshop">
                <div class="registration_pop_up">
                    <div class="registration-pop_heading">
                        <span>Add Workshop</span>
                        <p>
                            <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                        </p>
                    </div>
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Title</p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Workshop Type</p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_3">
                                        <p class="frm-head">Venue</p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_1">
                                        <p class="frm-head">Seat Limit</p>
                                        <input>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">WorkShop Date</p>
                                        <input type="date">
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Status</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="workshopstatus">
                                                <span class="checkmark">Active</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="workshopstatus">
                                                <span class="checkmark">Inactive</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- Add workshop pop up -->

        <!-- Edit workshop pop up -->
        <div class="pop_up_body" id="editworkshop">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Workshop</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Workshop Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Workshop Type</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Venue</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_1">
                                    <p class="frm-head">Seat Limit</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">WorkShop Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Status</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Active</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Inactive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit workshop pop up -->

        <!-- Edit workshop tariff pop up -->
        <div class="pop_up_body" id="editworkshoptariff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Update Workshop Tariff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Workshop Title</p>
                                    <p class="typed_data">TB & Critical Care Workshop</p>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Workshop Type</p>
                                    <p class="typed_data">Delegate</p>
                                </div>
                                <div class="registration-pop_body_box_inner span_2">
                                    <h4 class="registration-pop_body_box_heading">
                                        <span>Early Bird</span>
                                    </h4>
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">INR</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">USD</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                                <div class="registration-pop_body_box_inner span_2">
                                    <h4 class="registration-pop_body_box_heading">
                                        <span>Early Bird</span>
                                    </h4>
                                    <div class="form_grid">

                                        <div class="frm_grp span_2">
                                            <p class="frm-head">INR</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">USD</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit workshop tariff pop up -->

        <!-- ======================================= 3. REGISTRATION ====================================== -->
        <!-- New registration classification pop up -->
        <div class="pop_up_body" id="newregistrationclassification" style="display:none;">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Registration Classification</span>
                    <p><a href="javascript:void(0)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a></p>
                </div>
                <form id="addClassificationForm" autosubmit="off">
                    <input type="hidden" name="act" value="add">
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Classification Title</p>
                                        <input type="text" name="classification_title" id="classification_title" required />
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Classification Type <i class="mandatory">*</i></p>
                                        <select name="type" required="">
                                            <option value="DELEGATE">DELEGATE</option>
                                            <option value="ACCOMPANY">ACCOMPANY</option>
                                            <option value="COMBO">COMBO</option>
                                            <option value="FULL_ACCESS">FULL ACCESS</option>
                                            <option value="GUEST">GUEST</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Seat limit</p>
                                        <input type="text" name="seat_limit_add" id="seat_limit_add" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Sequence By <i class="mandatory">*</i></p>
                                        <input type="text" name="sequence_by" id="sequence_by" required />
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Currency<i class="mandatory">*</i></p>
                                        <select name="currency" style="width:80%;" required="">
                                            <option value="INR">INR</option>
                                            <option value="USD">USD</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Icon</p>
                                        <input style="display: none;" id="clsIcon" name="icon">
                                        <label for="clsIcon" class="frm-image"><i class="fal fa-upload"></i></label>
                                        <div class="frm_img_preview">
                                            <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Inclusion Lunch Date</p>
                                        <select name="inclusion_lunch_date[]" id="inclusion_lunch_date" multiple="multiple">
                                            <?php
                                            $sql_cal = array();
                                            $sql_cal['QUERY']    =    "SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Lunch'";
                                            $res_cal            =    $mycms->sql_select($sql_cal);
                                            $i = 1;

                                            foreach ($res_cal as $key => $rowsl) {
                                            ?>
                                                <option value="<?= $rowsl['date'] ?>"><?= $rowsl['date'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Inclusion Conference Dinner Date</p>
                                        <select name="inclusion_dinner_date[]" id="inclusion_dinner_date" multiple="multiple" style="width:85%">
                                            <?php
                                            $sql_cal = array();
                                            $sql_cal['QUERY']    =    "SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Dinner'";
                                            $res_cal            =    $mycms->sql_select($sql_cal);
                                            $i = 1;

                                            foreach ($res_cal as $key => $rowsl) {
                                            ?>
                                                <option value="<?= $rowsl['date'] ?>"><?= $rowsl['date'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Entry to Scientific Halls</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="inclusion_sci_hall" value="Y" checked />
                                                <span class="checkmark">Yes</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="inclusion_sci_hall" value="N" />
                                                <span class="checkmark">No</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Entry to Exhibition Area</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="inclusion_exb_area" value="Y" checked />
                                                <span class="checkmark">Yes</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="inclusion_exb_area" value="N" />
                                                <span class="checkmark">No</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Tea/Coffee during the Conference</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="inclusion_tea_coffee" value="Y" checked />
                                                <span class="checkmark">Yes</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="inclusion_tea_coffee" value="N" />
                                                <span class="checkmark">No</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Inclusion Conference Kit</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="inclusion_conference_kit" value="Y" checked />
                                                <span class="checkmark">Yes</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" name="inclusion_conference_kit" value="N" />
                                                <span class="checkmark">No</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!--=============================== 3 Edit registration classification pop up ===================================================-->
        <div class="pop_up_body" id="editregistrationclassification" style="display:none;">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Registration Classification</span>
                    <p><a href="javascript:void(0)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a></p>
                </div>
                <form id="editClassificationForm">
                    <input type="hidden" name="act" value="update">
                    <input type="hidden" name="id" id="edit_class_id" value="">
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                            <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                                <div class="form_grid">
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Classification Title</p>
                                        <input type="text" name="classification_title" id="edit_class_title" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Type</p>
                                        <select name="type" id="edit_class_type" required>
                                            <option value="DELEGATE">DELEGATE</option>
                                            <option value="ACCOMPANY">ACCOMPANY</option>
                                            <option value="COMBO">COMBO</option>
                                            <option value="FULL_ACCESS">FULL ACCESS</option>
                                            <option value="GUEST">GUEST</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Seat limit</p>
                                        <input type="number" name="seat_limit_add" id="edit_class_seat" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Sequence By</p>
                                        <input type="number" name="sequence_by" id="edit_class_sequence" required>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Currency</p>
                                        <select name="currency" id="edit_class_currency" required>
                                            <option value="INR">INR</option>
                                            <option value="USD">USD</option>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_4">
                                        <p class="frm-head">Icon</p>
                                        <input style="display: none;" id="clsIcon" name="icon">
                                        <label for="clsIcon" class="frm-image"><i class="fal fa-upload"></i></label>
                                        <div class="frm_img_preview">
                                            <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                            <button><i class="fal fa-trash-alt"></i></button>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Inclusion Lunch Date</p>
                                        <select name="inclusion_lunch_date[]" id="edit_inclusion_lunch_date" multiple="multiple">
                                            <?php
                                            $sql_cal = array();
                                            $sql_cal['QUERY']    =    "SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Lunch'";
                                            $res_cal            =    $mycms->sql_select($sql_cal);
                                            $i = 1;

                                            foreach ($res_cal as $key => $rowsl) {
                                            ?>
                                                <option value="<?= $rowsl['date'] ?>"><?= $rowsl['date'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Inclusion Conference Dinner Date</p>
                                        <select name="inclusion_dinner_date[]" id="edit_inclusion_dinner_date" multiple="multiple">
                                            <?php
                                            $sql_cal = array();
                                            $sql_cal['QUERY']    =    "SELECT *  
														FROM " . _DB_INCLUSION_DATE_ . " 
														WHERE status != 'D' AND `purpose`='Dinner'";
                                            $res_cal            =    $mycms->sql_select($sql_cal);
                                            $i = 1;

                                            foreach ($res_cal as $key => $rowsl) {
                                            ?>
                                                <option value="<?= $rowsl['date'] ?>"><?= $rowsl['date'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Entry to Scientific Halls</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" class="edit_inclusion_sci_hall" name="inclusion_sci_hall" value="Y" />
                                                <span class="checkmark">Yes</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" class="edit_inclusion_sci_hall" name="inclusion_sci_hall" value="N" />
                                                <span class="checkmark">No</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Entry to Exhibition Area</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" class="edit_inclusion_exb_area" name="inclusion_exb_area" value="Y" />
                                                <span class="checkmark">Yes</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" class="edit_inclusion_exb_area" name="inclusion_exb_area" value="N" />
                                                <span class="checkmark">No</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Tea/Coffee during the Conference</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" class="edit_inclusion_tea_coffee" name="inclusion_tea_coffee" value="Y" />
                                                <span class="checkmark">Yes</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" class="edit_inclusion_tea_coffee" name="inclusion_tea_coffee" value="N" />
                                                <span class="checkmark">No</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Inclusion Conference Kit</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check gender_check">
                                                <input type="radio" class="edit_inclusion_conference_kit" name="inclusion_conference_kit" value="Y" />
                                                <span class="checkmark">Yes</span>
                                            </label>
                                            <label class="cus_check gender_check">
                                                <input type="radio" class="edit_inclusion_conference_kit" name="inclusion_conference_kit" value="N" />
                                                <span class="checkmark">No</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button type="button" class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Add registration classification
            $('#addClassificationForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'manage_reg_classification.process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Added');
                            setTimeout(function() {
                                location.reload();
                            }, 900);
                        } else {
                            toastr.error(res.message || 'Save failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error.');
                    }
                });
            });

            function loadDataOnEditClassification(id) {
                $.getJSON('manage_reg_classification.process.php', {
                    act: 'getRegClassification',
                    id: id
                }, function(res) {
                    if (res && res.status) {
                        var d = res.data;
                        $('#edit_class_id').val(d.id);
                        $('#edit_class_title').val(d.classification_title);
                        $('#edit_class_type').val(d.type);
                        $('#edit_class_seat').val(d.seat_limit);
                        $('#edit_class_sequence').val(d.sequence_by);
                        $('#edit_class_currency').val(d.currency);

                        var form = $('#editClassificationForm');
                        var selected_inclusion_lunch_date = JSON.parse(d.inclusion_lunch_date);
                        var selected_inclusion_dinner_date = JSON.parse(d.inclusion_dinner_date);

                        $.each(selected_inclusion_lunch_date, function(i, val) {
                            $('#edit_inclusion_lunch_date').val(selected_inclusion_lunch_date).trigger('click');
                            // form.find('input[name="inclusion_lunch_date[]"][value="' + val + '"]').prop('selected', true);
                        });
                        $.each(selected_inclusion_dinner_date, function(i, val) {
                            $('#edit_inclusion_dinner_date').val(selected_inclusion_dinner_date).trigger('change');
                            // form.find('input[name="inclusion_dinner_date[]"][value="' + val + '"]').prop('selected', true);
                        });
                        // form.find('input[name="inclusion_lunch_date"][value="' + selected_inclusion_lunch_date + '"]').prop('selected', true);


                        form.find('input[name="inclusion_sci_hall"][value="' + d.inclusion_sci_hall + '"]').prop('checked', true);
                        form.find('input[name="inclusion_exb_area"][value="' + d.inclusion_exb_area + '"]').prop('checked', true);
                        form.find('input[name="inclusion_tea_coffee"][value="' + d.inclusion_tea_coffee + '"]').prop('checked', true);
                        form.find('input[name="inclusion_conference_kit"][value="' + d.inclusion_conference_kit + '"]').prop('checked', true);

                        $('#editregistrationclassification').show();

                    } else {
                        toastr.error(res.message || 'Could not fetch record');
                    }
                }).fail(function() {
                    toastr.error('Server error while fetching record');
                });
            }

            $('#editClassificationForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'manage_reg_classification.process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Updated');
                            $('#editregistrationclassification').hide();
                            setTimeout(function() {
                                location.reload();
                            }, 900);
                        } else {
                            toastr.error(res.message || 'Update failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error.');
                    }
                });
            });


            jQuery("#inclusion_lunch_date, #edit_inclusion_lunch_date").select2({
                placeholder: 'Add Lunch Date (dd-mm-yyyy)',
                tags: true
            });
            jQuery("#inclusion_dinner_date, #edit_inclusion_dinner_date").select2({
                placeholder: 'Add Conference Dinner Date',
                tags: true
            });
        </script>
        <script type="text/javascript">

        </script>

        <!-- Edit registration tariff pop up -->
        <div class="pop_up_body" id="editRegistrationTariff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Update Registration Tariff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form id="updateRegTariffForm" autosubmit="off">
                    <input type="hidden" name="act" id="act" value="updateRegTariff" />
                    <input type="hidden" name="classification_id" id="classification_id" />

                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                          <!-- ajax -->
                        </div>
                    </div>

                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function loadDataOnEditRegTariff(id) {
                $.getJSON('registration_tariff.process.php', {
                    act: 'getRegTariff',
                    id: id
                }, function(res) {
                    if (res && res.status) {
                        var d = res.data;

                        // target container inside the form
                        var $box = $('#updateRegTariffForm .registration-pop_body_box');
                        $box.empty();

                        // group by cutoff title
                        var groups = {};
                        $.each(d, function(k, item) {
                            // skip non-object keys (safety)
                            if (!item || typeof item !== 'object') return;
                            var cutoff = item.CUTOFF_TITTLE || ('Cutoff ' + (item.CUTOFF_ID || k));
                            groups[cutoff] = groups[cutoff] || [];
                            groups[cutoff].push(item);
                        });
                        $('#classification_id').val(id);
                        // build HTML for each cutoff group
                        $.each(groups, function(cutoffTitle, items) {
                            var groupHtml = '<div class="registration-pop_body_box_inner span_2">';
                            groupHtml += '<h4 class="registration-pop_body_box_heading"><span>' + cutoffTitle + '</span></h4>';
                            groupHtml += '<div class="form_grid">';

                            // for each tariff row under this cutoff, render fields
                            $.each(items, function(i, row) {
                                // show classification + type
                                groupHtml += '<div class="frm_grp span_2">';
                                groupHtml += '<p class="frm-head">Classification</p>';
                                groupHtml += '<p class="typed_data">' + (row.CLASSIFICATION_TITTLE || '') + '</p>';
                                groupHtml += '</div>';

                                // amount input (currency-aware)
                                groupHtml += '<div class="frm_grp span_2">';
                                groupHtml += '<p class="frm-head">Amount (' + (row.CURRENCY || '') + ')</p>';
                                groupHtml += '<input type="hidden" name="currency[' + (row.CUTOFF_ID || '') + ']" id="currency_' + (row.CUTOFF_ID || '') + '" value="' + (row.CURRENCY || '') + '" />';

                                groupHtml += '<input type="number" name="amount_for_cutoff[' + (row.CUTOFF_ID || '') + ']" value="' + (row.AMOUNT || '') + '"/>';
                                groupHtml += '</div>';

                                // optional display amount / offer / hotel info (if present)
                                if (row.DISPLAY_AMOUNT) {
                                    groupHtml += '<div class="frm_grp span_4">';
                                    groupHtml += '<p class="frm-head">Info</p>';
                                    var info = [];
                                    if (row.DISPLAY_AMOUNT) info.push('Display: ' + row.DISPLAY_AMOUNT);

                                    groupHtml += '<p class="typed_data">' + info.join(' | ') + '</p>';
                                    groupHtml += '</div>';
                                }

                            });

                            groupHtml += '</div>'; // .form_grid
                            groupHtml += '</div>'; // .registration-pop_body_box_inner

                            $box.append(groupHtml);
                        });

                        // show the popup
                        $('#editRegistrationTariff').show();

                    } else {
                        toastr.error(res.message || 'Could not fetch record');
                    }
                }).fail(function() {
                    toastr.error('Server error while fetching record');
                });
            }

            $('#updateRegTariffForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'registration_tariff.process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Updated');
                            $('#editRegistrationTariff').hide();
                            setTimeout(function() {
                                location.reload();
                            }, 900);
                        } else {
                            toastr.error(res.message || 'Update failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error.');
                    }
                });
            });
        </script>
        <!-- Edit registration tariff pop up -->

        <!-- Add section tariff pop up -->
        <div class="pop_up_body" id="addsection">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Architecture</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Section Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_1">
                                    <p class="frm-head">Ref No</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_1">
                                    <p class="frm-head">Path</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_1 d-none">
                                    <p class="frm-head">Ref No</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_1 d-none">
                                    <p class="frm-head">Path</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4 d-none">
                                    <p class="frm-head">Page Info</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4 d-flex align-items-center">
                                    <p class="frm-head mb-0">Set Section As Page</p>
                                    <label class="toggleswitch toggleswitchswap ml-2">
                                        <input class="toggleswitch-checkbox" type="checkbox">
                                        <div class="toggleswitch-switch"></div>
                                    </label>
                                </div>
                                <div class="registration-pop_body_box_inner span_4">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Module Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Ref No</p>
                                            <input>
                                        </div>
                                        <div class="registration-pop_body_box_inner span_4">
                                            <div class="form_grid">
                                                <div class="frm_grp span_2">
                                                    <p class="frm-head">Page Name</p>
                                                    <input>
                                                </div>
                                                <div class="frm_grp span_1">
                                                    <p class="frm-head">Ref No</p>
                                                    <input>
                                                </div>
                                                <div class="frm_grp span_1">
                                                    <p class="frm-head">Path</p>
                                                    <input>
                                                </div>
                                                <div class="frm_grp span_4">
                                                    <p class="frm-head">Page Info</p>
                                                    <input>
                                                </div>
                                                <div class="registration-pop_body_box_inner span_4">
                                                    <div class="form_grid">
                                                        <div class="frm_grp span_2">
                                                            <p class="frm-head">Page Name</p>
                                                            <input>
                                                        </div>
                                                        <div class="frm_grp span_1">
                                                            <p class="frm-head">Ref No</p>
                                                            <input>
                                                        </div>
                                                        <div class="frm_grp span_1">
                                                            <p class="frm-head">Path</p>
                                                            <input>
                                                        </div>
                                                        <div class="frm_grp span_4">
                                                            <p class="frm-head">Page Info</p>
                                                            <input>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button class="span_4 add_architech">Add Sub-page</button>
                                            </div>
                                        </div>
                                        <button class="span_4 add_architech">Add Page</button>
                                    </div>

                                </div>
                                <button class="span_4 add_architech">Add Module</button>
                                <div class="registration-pop_body_box_inner span_4">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Page Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Ref No</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Path</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Page Info</p>
                                            <input>
                                        </div>
                                        <div class="registration-pop_body_box_inner span_4">
                                            <div class="form_grid">
                                                <div class="frm_grp span_2">
                                                    <p class="frm-head">Page Name</p>
                                                    <input>
                                                </div>
                                                <div class="frm_grp span_1">
                                                    <p class="frm-head">Ref No</p>
                                                    <input>
                                                </div>
                                                <div class="frm_grp span_1">
                                                    <p class="frm-head">Path</p>
                                                    <input>
                                                </div>
                                                <div class="frm_grp span_4">
                                                    <p class="frm-head">Page Info</p>
                                                    <input>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="span_4 add_architech">Add Sub-page</button>
                                    </div>
                                </div>
                                <button class="span_4 add_architech">Add Page</button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add section tariff pop up -->

        <!-- New hotel pop up -->
        <div class="pop_up_body" id="newhotel">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Hotel</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Hotel Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Hotel Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Hotel Phone No</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Distance From Venue (Km)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Check In Date</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Check Out Date</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Address</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Packages</span>
                                <a class="add mi-1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="cus_check_wrap">
                                <label class="cus_check gender_check">
                                    <input type="radio" name="packagetype">
                                    <span class="checkmark">Individual</span>
                                </label>
                                <label class="cus_check gender_check">
                                    <input type="radio" name="packagetype">
                                    <span class="checkmark">Sharing</span>
                                </label>
                                <label class="cus_check gender_check">
                                    <input type="radio" name="packagetype">
                                    <span class="checkmark">Triple</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Aminites</span>
                                <a class="add mi-1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="accm_add_wrap">
                                <h6 class="accm_add_empty">No Aminity Available</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_3">
                                            <p class="frm-head">Aminity Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Icon</p>
                                            <input style="display: none;" id="accessoriesicon">
                                            <label for="accessoriesicon" class="frm-image"><?php upload() ?></label>
                                            <div class="frm_img_preview">
                                                <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button><?php delete() ?></button>
                                            </div>
                                        </div>

                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_3">
                                            <p class="frm-head">Aminity Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Icon</p>
                                            <input style="display: none;" id="accessoriesicon">
                                            <label for="accessoriesicon" class="frm-image" style="display: none;"><?php upload() ?></label>
                                            <div class="frm_img_preview" style="display: block;">
                                                <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button><?php delete() ?></button>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Room Type<label class="toggleswitch">
                                        <input class="toggleswitch-checkbox" type="checkbox">
                                        <div class="toggleswitch-switch"></div>
                                    </label></span>
                                <a class="add mi-1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="accm_add_wrap">
                                <h6 class="accm_add_empty">No Room Type Available</h6>
                                <h6 class="accm_add_empty">Room Type Not Available</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Type</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Image</p>
                                            <input style="display: none;" id="roomimage">
                                            <label for="roomimage" class="frm-image"><?php upload() ?></label>
                                            <div class="frm_img_preview">
                                                <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button><?php delete() ?></button>
                                            </div>
                                        </div>
                                        <div class="form_grid span_4">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Date</p>
                                                <p class="typed_data">17/01/2025</p>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Seat</p>
                                                <input>
                                            </div>
                                        </div>
                                        <div class="form_grid span_4">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Date</p>
                                                <p class="typed_data">17/01/2025</p>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Seat</p>
                                                <input>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Type</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Image</p>
                                            <input style="display: none;" id="roomimage">
                                            <label for="roomimage" class="frm-image"><?php upload() ?></label>
                                            <div class="frm_img_preview">
                                                <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button><?php delete() ?></button>
                                            </div>
                                        </div>
                                        <div class="form_grid span_4">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Date</p>
                                                <p class="typed_data">17/01/2025</p>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Seat</p>
                                                <input>
                                            </div>
                                        </div>
                                        <div class="form_grid span_4">
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Date</p>
                                                <p class="typed_data">17/01/2025</p>
                                            </div>
                                            <div class="frm_grp span_2">
                                                <p class="frm-head">Seat</p>
                                                <input>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Seat Limits</span>
                            </h4>
                            <div class="form_grid">
                                <div class="registration-pop_body_box_inner span_4">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <p class="typed_data">17/01/2025</p>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Seat</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                                <div class="registration-pop_body_box_inner span_4">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <p class="typed_data">17/01/2025</p>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Seat</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Hotel Images</span>
                            </h4>
                            <div class="com_info_branding_wrap form_grid g_2">
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Add Hotel</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New hotel pop up -->

        <!-- edit hotel pop up -->
        <div class="pop_up_body" id="edithotel">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Hotel</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Hotel Details</span>
                            </h4>
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Hotel Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Hotel Phone No</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Distance From Venue (Km)</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Check In Date</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Check Out Date</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Address</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Packages</span>
                                <a class="add mi-1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="accm_add_wrap">
                                <h6 class="accm_add_empty">No Package Available</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Package Name</p>
                                            <input>
                                        </div>

                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <p class="frm-head">Package Name</p>
                                            <input>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Aminites</span>
                                <a class="add mi-1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="accm_add_wrap">
                                <h6 class="accm_add_empty">No Aminity Available</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_3">
                                            <p class="frm-head">Aminity Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Icon</p>
                                            <input style="display: none;" id="accessoriesicon">
                                            <label for="accessoriesicon" class="frm-image"><?php upload() ?></label>
                                            <div class="frm_img_preview">
                                                <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button><?php delete() ?></button>
                                            </div>
                                        </div>

                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>

                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_3">
                                            <p class="frm-head">Aminity Name</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_1">
                                            <p class="frm-head">Icon</p>
                                            <input style="display: none;" id="accessoriesicon">
                                            <label for="accessoriesicon" class="frm-image" style="display: none;"><?php upload() ?></label>
                                            <div class="frm_img_preview" style="display: block;">
                                                <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button><?php delete() ?></button>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Room Type</span>
                                <a class="add mi-1"><?php add(); ?>Add</a>
                            </h4>
                            <div class="accm_add_wrap">
                                <h6 class="accm_add_empty">No Room Type Available</h6>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Type</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Image</p>
                                            <input style="display: none;" id="roomimage">
                                            <label for="roomimage" class="frm-image"><?php upload() ?></label>
                                            <div class="frm_img_preview">
                                                <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button><?php delete() ?></button>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                                <div class="accm_add_box">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Type</p>
                                            <input>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Room Image</p>
                                            <input style="display: none;" id="roomimage">
                                            <label for="roomimage" class="frm-image"><?php upload() ?></label>
                                            <div class="frm_img_preview">
                                                <img src="https://ruedakolkata.com/natcon2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/HOTELICON_0_0001_250107125112.png">
                                                <button><?php delete() ?></button>
                                            </div>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_danger action-transparent"><?php delete(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Seat Limits</span>
                            </h4>
                            <div class="form_grid">
                                <div class="registration-pop_body_box_inner span_4">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <p class="typed_data">17/01/2025</p>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Seat</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                                <div class="registration-pop_body_box_inner span_4">
                                    <div class="form_grid">
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Date</p>
                                            <p class="typed_data">17/01/2025</p>
                                        </div>
                                        <div class="frm_grp span_2">
                                            <p class="frm-head">Seat</p>
                                            <input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="registration-pop_body_box_inner">
                            <h4 class="registration-pop_body_box_heading">
                                <span>Hotel Images</span>
                            </h4>
                            <div class="com_info_branding_wrap form_grid g_2">
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="com_info_branding_box">
                                    <div class="branding_image_preview">
                                        <img src="images/Banner KTC BG.png" alt="">
                                        <button><i class="fal fa-trash-alt"></i></button>
                                    </div>
                                    <div class="branding_image_upload">
                                        <input style="display: none;" id="webmaster_background" type="file">
                                        <label for="webmaster_background">
                                            <span><?php upload() ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update Hotel</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- edit hotel pop up -->

        <!-- view hotel pop up -->
        <div class="pop_up_body" id="viewhotel">
            <div class="profile_pop_up">
                <div class="profile_pop_left">
                    <div class="profile_left_box text-center ">
                        <h5>The Westin Kolkata</h5>
                        <h6 class="star fivestar"><?php star(); ?><?php star(); ?><?php star(); ?><?php star(); ?><?php star(); ?><?php star(); ?></h6>
                        <!-- class name varied like "fivestar" "fourstar" "threestar" -->
                        <div class="regi_type justify-content-center">
                            <span class="badge_padding badge_default">Package 1</span>
                            <span class="badge_padding badge_default">Package 2</span>
                        </div>
                    </div>
                    <div class="profile_left_box">
                        <ul>
                            <li>
                                <?php call(); ?>
                                <p>
                                    <b>Phone</b>
                                    <span>9674833617</span>
                                </p>
                            </li>
                            <li>
                                <?php calendar(); ?>
                                <p>
                                    <b>Check In Date</b>
                                    <span>19/11/2025</span>
                                </p>
                            </li>
                            <li>
                                <?php calendar(); ?>
                                <p>
                                    <b>Check Out Date</b>
                                    <span>19/11/2025</span>
                                </p>
                            </li>
                            <li>
                                <?php address(); ?>
                                <p>
                                    <b>Address</b>
                                    <span>GSVM MEDICAL COLLEGE KANPUR, KANPUR, Uttar Pradesh, 208002, INDIA</span>
                                </p>
                            </li>
                            <li>
                                <?php address(); ?>
                                <p>
                                    <b>Distance From Venue (Km)</b>
                                    <span>2</span>
                                </p>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="profile_pop_right">
                    <div class="profile_pop_right_heading">
                        <span>Hotel Details</span>
                        <p>
                            <a href="javascript:void(null)" class="icon_hover badge_primary action-transparent"><?php export(); ?></a>
                            <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                        </p>
                    </div>
                    <div class="profile_pop_right_body">
                        <ul class="profile_payments_grid_ul">
                            <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li>
                            <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li>
                            <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li>
                            <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li>
                            <li class="p-0 overflow-hidden">
                                <img class="w-100" src="https://ruedakolkata.com/natcon_2025/uploads/EMAIL.HEADER.FOOTER.IMAGE/MAILER_LOGO_0001_250526185918.png">
                            </li>
                        </ul>
                        <div class="service_breakdown_wrap">
                            <h4>Seat Limit</h4>
                            <div class="table_wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="text-center">17/11/2025</th>
                                            <th class="text-center">18/11/2025</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">50</td>
                                            <td class="text-center">100</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="service_breakdown_wrap">
                            <h4>Aminities</h4>
                            <ul class="accm_ul aminity_ul">
                                <li><img src="" alt="">Aminity 1</li>
                                <li><img src="" alt="">Aminity 2</li>
                                <li><img src="" alt="">Aminity 2</li>
                            </ul>
                        </div>
                        <div class="service_breakdown_wrap">
                            <h4>Room Type</h4>
                            <ul class="accm_ul room_ul">
                                <li><img src="" alt="">Aminity 1</li>
                                <li><img src="" alt="">Aminity 2</li>
                                <li><img src="" alt="">Aminity 2</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- view hotel pop up -->

        <!-- Hotel Tariff pop up -->
        <div class="pop_up_body" id="hoteltariff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Accommodation Tariff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Hotel Name</p>
                                    <p class="typed_data">Hotel Sonar Bangla, Mandarmani</p>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Room Type</p>
                                    <p class="typed_data">Premium</p>
                                </div>
                                <div class="accm_add_wrap span_4">
                                    <div class="accm_add_box">
                                        <h4 class="registration-pop_body_box_heading">
                                            <span>Early Bird<span>
                                        </h4>
                                        <div class="registration-pop_body p-0">
                                            <div class="registration-pop_body_box_inner">
                                                <h4 class="registration-pop_body_box_heading">
                                                    <span>Single<span>
                                                </h4>
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(INR)</p>
                                                        <input>
                                                    </div>
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(USD)</p>
                                                        <input>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="registration-pop_body_box_inner">
                                                <h4 class="registration-pop_body_box_heading">
                                                    <span>Twin<span>
                                                </h4>
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(INR)</p>
                                                        <input>
                                                    </div>
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(USD)</p>
                                                        <input>
                                                    </div>

                                                </div>

                                            </div>
                                            <div class="registration-pop_body_box_inner">
                                                <h4 class="registration-pop_body_box_heading">
                                                    <span>Triple<span>
                                                </h4>
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(INR)</p>
                                                        <input>
                                                    </div>
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(USD)</p>
                                                        <input>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="table_wrap mt-3">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Check In Date</th>
                                                        <th>Check Out Date</th>
                                                        <th>Package</th>
                                                        <th>INR Rate</th>
                                                        <th>USD Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td rowspan="3">2026-01-17</td>
                                                        <td rowspan="3">2026-01-18</td>
                                                        <td>Single</td>
                                                        <td>5000.00</td>
                                                        <td>16.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Twin</td>
                                                        <td>5000.00</td>
                                                        <td>16.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Triple</td>
                                                        <td>5000.00</td>
                                                        <td>16.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_primary action-transparent"><i class="fal fa-paper-plane"></i></a>
                                    </div>
                                    <div class="accm_add_box">
                                        <h4 class="registration-pop_body_box_heading">
                                            <span>Regular<span>
                                        </h4>
                                        <div class="registration-pop_body p-0">
                                            <div class="registration-pop_body_box_inner">
                                                <h4 class="registration-pop_body_box_heading">
                                                    <span>Single<span>
                                                </h4>
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(INR)</p>
                                                        <input>
                                                    </div>
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(USD)</p>
                                                        <input>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="registration-pop_body_box_inner">
                                                <h4 class="registration-pop_body_box_heading">
                                                    <span>Twin<span>
                                                </h4>
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(INR)</p>
                                                        <input>
                                                    </div>
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(USD)</p>
                                                        <input>
                                                    </div>

                                                </div>

                                            </div>
                                            <div class="registration-pop_body_box_inner">
                                                <h4 class="registration-pop_body_box_heading">
                                                    <span>Triple<span>
                                                </h4>
                                                <div class="form_grid">
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(INR)</p>
                                                        <input>
                                                    </div>
                                                    <div class="frm_grp span_2">
                                                        <p class="frm-head">Rate/Night(USD)</p>
                                                        <input>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="table_wrap mt-3">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Check In Date</th>
                                                        <th>Check Out Date</th>
                                                        <th>Package</th>
                                                        <th>INR Rate</th>
                                                        <th>USD Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td rowspan="3">2026-01-17</td>
                                                        <td rowspan="3">2026-01-18</td>
                                                        <td>Single</td>
                                                        <td>5000.00</td>
                                                        <td>16.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Twin</td>
                                                        <td>5000.00</td>
                                                        <td>16.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Triple</td>
                                                        <td>5000.00</td>
                                                        <td>16.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <a href="#" class="accm_delet icon_hover badge_primary action-transparent"><i class="fal fa-paper-plane"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update Tariff</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hotel Tariff pop up -->

        <!-- New dinner pop up -->
        <div class="pop_up_body" id="adddinner">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Dinner</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Select Hotel</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dinner Classification Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Link</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- New dinner pop up -->

        <!-- edit dinner pop up -->
        <div class="pop_up_body" id="editdinner">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Edit Dinner</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Select Hotel</p>
                                    <select></select>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dinner Classification Name</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Link</p>
                                    <input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- edit dinner pop up -->

        <!-- Add accompany classification pop up -->
        <div class="pop_up_body" id="addaccompany">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Add Accompany</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <div class="registration-pop_body">
                    <div class="registration-pop_body_box">
                        <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                            <div class="form_grid">
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Classification Title</p>
                                    <input>
                                </div>
                                <div class="frm_grp span_4">
                                    <p class="frm-head">Inclusion Lunch Date</p>
                                    <input type="datce">
                                </div>
                                <div class="frm_grp span_3">
                                    <p class="frm-head">Inclusion Conference Dinner Date</p>
                                    <input type="date">
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Scientific Halls</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Entry to Exhibition Area</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Tea/Coffee during the Conference</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="frm_grp span_2">
                                    <p class="frm-head">Inclusion Conference Kit</p>
                                    <div class="cus_check_wrap">
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">Yes</span>
                                        </label>
                                        <label class="cus_check gender_check">
                                            <input type="radio" name="workshopstatus">
                                            <span class="checkmark">No</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="registration-pop_footer">
                    <div class="registration_btn_wrap">
                        <button class="popup_close badge_dark">Cancel</button>
                        <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add accompany classification pop up -->

        <!-- accompany Tariff pop up -->
        <div class="pop_up_body" id="editAccompanyTariff">
            <div class="registration_pop_up">
                <div class="registration-pop_heading">
                    <span>Accompany Tariff</span>
                    <p>
                        <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                    </p>
                </div>
                <form id="updateAccompanyTariffForm" autosubmit="off">
                    <input type="hidden" name="act" id="act" value="updateAccompanyTariff" />
                    <input type="hidden" name="classification_id" id="classification_id" />
                    <div class="registration-pop_body">
                        <div class="registration-pop_body_box">
                           <!-- ajax -->
                        </div>
                    </div>
                    <div class="registration-pop_footer">
                        <div class="registration_btn_wrap">
                            <button class="popup_close badge_dark">Cancel</button>
                            <button type="submit" class="mi-1 badge_success"><?php save(); ?>Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function loadDataOnEditAccompTariff(id) {
                $.getJSON('accompany_tariff.process.php', {
                    act: 'getAccompanyTariff',
                    id: id
                }, function(res) {
                    if (res && res.status) {
                        var d = res.data;

                        // target container inside the form
                        var $box = $('#updateAccompanyTariffForm .registration-pop_body_box');
                        $box.empty();

                        // group by cutoff title
                        var groups = {};
                        $.each(d, function(k, item) {
                            // skip non-object keys (safety)
                            if (!item || typeof item !== 'object') return;
                            var cutoff = item.CUTOFF_TITTLE || ('Cutoff ' + (item.CUTOFF_ID || k));
                            groups[cutoff] = groups[cutoff] || [];
                            groups[cutoff].push(item);
                        });
                        $('#updateAccompanyTariffForm').find('#classification_id').val(id);
                        // build HTML for each cutoff group
                        $.each(groups, function(cutoffTitle, items) {
                            var groupHtml = '<div class="registration-pop_body_box_inner span_2">';
                            groupHtml += '<h4 class="registration-pop_body_box_heading"><span>' + cutoffTitle + '</span></h4>';
                            groupHtml += '<div class="form_grid">';

                            // for each tariff row under this cutoff, render fields
                            $.each(items, function(i, row) {
                                // show classification + type
                                groupHtml += '<div class="frm_grp span_2">';
                                groupHtml += '<p class="frm-head">Classification</p>';
                                groupHtml += '<p class="typed_data">' + (row.CLASSIFICATION_TITTLE || '') + '</p>';
                                groupHtml += '</div>';

                                // amount input (currency-aware)
                                groupHtml += '<div class="frm_grp span_2">';
                                groupHtml += '<p class="frm-head">Amount (INR)</p>';
                                groupHtml += '<input type="hidden" name="currency[' + (row.CUTOFF_ID || '') + ']" id="currency_' + (row.CUTOFF_ID || '') + '" value="' + (row.CURRENCY || '') + '" />';

                                groupHtml += '<input type="number" name="tariff_cutoff_id[' + (row.CUTOFF_ID || '') + ']" value="' + (row.AMOUNT || '') + '"/>';
                                groupHtml += '</div>';

                                // optional display amount / offer / hotel info (if present)
                                if (row.DISPLAY_AMOUNT) {
                                    groupHtml += '<div class="frm_grp span_4">';
                                    groupHtml += '<p class="frm-head">Info</p>';
                                    var info = [];
                                    if (row.DISPLAY_AMOUNT) info.push('Display: ' + row.DISPLAY_AMOUNT);

                                    groupHtml += '<p class="typed_data">' + info.join(' | ') + '</p>';
                                    groupHtml += '</div>';
                                }

                            });

                            groupHtml += '</div>'; // .form_grid
                            groupHtml += '</div>'; // .registration-pop_body_box_inner

                            $box.append(groupHtml);
                        });

                        // show the popup
                        $('#editAccompanyTariff').show();

                    } else {
                        toastr.error(res.message || 'Could not fetch record');
                    }
                }).fail(function() {
                    toastr.error('Server error while fetching record');
                });
            }

            $('#updateAccompanyTariffForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'accompany_tariff.process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            toastr.success(res.message || 'Updated');
                            $('#editAccompanyTariff').hide();
                            setTimeout(function() {
                                location.reload();
                            }, 900);
                        } else {
                            toastr.error(res.message || 'Update failed');
                        }
                    },
                    error: function() {
                        toastr.error('Server error.');
                    }
                });
            });
        </script>
        <!-- accompany Tariff pop up -->
    </div>
</div>