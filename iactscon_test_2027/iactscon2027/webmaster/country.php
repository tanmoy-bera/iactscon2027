<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Country Listing</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Country</li>
                    </ol>
                </nav>
                <h2>Country Listing</h2>
                <h6>Manage country name, ISD codes, currency, and status.</h6>
            </div>
        </div>
        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input placeholder="Search by Country, ISD Code, Currency, or Status...">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" class="popup-btn add" data-tab="newcountry"><?php add(); ?>New Country</a>
            </div>
        </div>
        <div class="table_wrap">
            <table>
                <thead>
                    <tr>
                        <th class="check_select">
                            <label class="cus_check category_check workshop_check">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </th>
                        <th class="sl">#</th>
                        <th>Abbreviation</th>
                        <th>Country</th>
                        <th>ISD Code</th>
                        <th>Currency</th>
                        <th class="action">Status</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="check_select">
                            <label class="cus_check category_check workshop_check">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td class="sl">1</td>
                        <td class="sl">AF</td>
                        <td>AFGANISTAN</td>
                        <td></td>
                        <td></td>
                        <td>
                            <div class="action_div">
                                <span class="badge_padding badge_success w-max-con text-uppercase">Active</span>
                                <span class="badge_padding badge_danger w-max-con text-uppercase">Inactive</span>
                            </div>
                        </td>
                        <td class="action">
                            <div class="action_div">
                                <a class="popup-btn icon_hover badge_secondary action-transparent br-5 w-auto" data-tab="editcountry"><?php edit(); ?></a>
                                <a href="#" class="icon_hover badge_danger action-transparent br-5 w-auto"><?php delete(); ?></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="list-action">
            <select name="multiOperationSelector" id="multiOperationSelector">
                <option value="">Choose</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
            <button class="submit" name="submit" type="button">Apply</button>
        </div>
        <div class="bbp-pagination">
            <div class="bbp-pagination-count">Showing 1 to 10 of 150 entries</div>
            <span class="paginationDisplay">
                <div class="pagination"><a>1 of 15 Pages</a><a class="selected">1</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=1">2</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=2">3</a><a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=1">&gt;&gt;</a> <a href="/natcon_2025/webmaster/section_registration/registration.php?_pgnR001_=14">Last</a></div>
            </span>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
</html>