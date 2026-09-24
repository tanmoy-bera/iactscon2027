<?php 
include_once("includes/source.php");
include_once('includes/init.php');
include_once('includes/function.workshop.php');
include_once(__DIR__ . "/../includes/function.registration.php");
include_once(__DIR__. "/../includes/function.delegate.php");
include_once(__DIR__. "/../includes/function.invoice.php");
include_once(__DIR__. "/../includes/function.workshop.php");
include_once(__DIR__. "/../includes/function.dinner.php");
include_once(__DIR__. "/../includes/function.accompany.php");
include_once(__DIR__. "/../includes/function.accommodation.php");
include_once(__DIR__. "/../includes/function.abstract.php");
include_once('includes/function.php');

 ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>System User</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">System User</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Role</li>
                    </ol>
                </nav>
                <h2>User Role</h2>
                <h6>Manage registrations, track payments, and view participant details.</h6>
            </div>
            <div class="page_top_wrap_right">
                <a href="#" class="badge_default"><?php add() ?>Add Role</a>
                <a href="#" class="badge_success"><?php save() ?>Save Changes</a>
            </div>
        </div>
        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>User List</h6>
                <button class="com_info_left_click icon_hover badge_default active">Name</button>
                <button class="com_info_left_click icon_hover badge_default action-transparent">Name</button>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <div class="com_info_box_inner">
                                <div class="page_access_top">
                                    <p>Role Name</p>
                                    <!-- <div class="form_grid">
                                        <div class="frm_grp span_4">
                                            <input>
                                        </div>
                                    </div> -->
                                    <div class="page_access_top_right">
                                        <a class="badge_secondary"><?php edit() ?>Edit</a>
                                        <a class="badge_danger"><?php delete() ?>Delete</a>
                                    </div>
                                </div>
                                <div class="page_access_bottom">
                                    <div class="table_wrap">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Page</th>
                                                    <th class="sl">View</th>
                                                    <th class="sl">Add</th>
                                                    <th class="sl">Edit</th>
                                                    <th class="sl">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbod>
                                                <tr>
                                                    <td>Page Name</td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Page Name</td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Page Name</td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                    <td class="sl">

                                                    </td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                    <td class="sl">
                                                        <label class="toggleswitch toggleswitchswap ml-2">
                                                            <input class="toggleswitch-checkbox" type="checkbox">
                                                            <div class="toggleswitch-switch"></div>
                                                        </label>
                                                    </td>
                                                </tr>
                                            </tbod>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>