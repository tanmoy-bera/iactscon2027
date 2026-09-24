<?php 
include_once("includes/source.php");
include_once('includes/init.php');
include_once('includes/function.workshop.php'); 
?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Settings</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Architecture</li>
                    </ol>
                </nav>
                <h2>Architecture</h2>
                <h6>Manage section, modul & page details.</h6>
            </div>
        </div>
        <div class="regi_search_wrap mb-3">
            <div class="regi_search">
                <?php search(); ?>
                <input  id="searchInput" name="q"  placeholder="Search by Section Name..."  value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
            <div class="regi_search_wrap_btn_box">
                <a href="javascript:void(null)" class="popup-btn add" data-tab="addsection"><?php add(); ?>New Section</a>
            </div>
        </div>
        <ul class="add_section architc_wrap">
            <?
             if (!empty($_GET['q'])) {

                    $search = trim($_GET['q']);
                    $search = addslashes($search); // (works, but see note below)

                    $searchCondition = " AND (
                        sectionName LIKE '%$search%'
                      
                    )";
                }
            $sectionQuery = array();
            $sectionQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_SECTIONS_ . " WHERE status = ?   " . $searchCondition . "  ORDER BY seq ASC";
            $sectionQuery['PARAM'][] = array('FILD'=>'status', 'DATA'=>'A', 'TYP'=>'s');
            $sectionsResult = $mycms->sql_select_paginated(1,$sectionQuery, 5, $restrt);
            if($sectionsResult){
            foreach ($sectionsResult as $section) {
              $sectionId = $section['sectionId'];
            ?>
            <li>
                <div class="registration-pop_body_box_inner architec_section_box accm_top">
                    <div class="spot_name d-flex align-items-center">
                        <div class="regi_img_circle">
                            <span class="menu_icon"><i class="<?=$section['img']?>"></i></span>
                        </div>
                        <div>
                            <div class="regi_contact mt-0">
                                <span>Section</span>
                            </div>
                            <div class="regi_name"><?= htmlspecialchars($section['sectionName']) ?></div>
                        </div>
                    </div>
                    <div class="accm_details">
                        <div class="accm_details_box">
                            <h5>Section Code</h5>
                            <h6><?= htmlspecialchars($section['secCode']) ?></h6>
                        </div>
                        <div class="accm_details_box">
                            <h5>Path</h5>
                            <h6><?= htmlspecialchars($section['path']) ?></h6>
                        </div>
                         <div class="accm_details_box">
                            <h5>Page Info</h5>
                            <h6><?=$section['pageInfo']?></h6>
                        </div>
                        <div class="accm_details_box action" style="flex:unset;">
                            <a href="#" data-tab="editSection"   data-section_id="<?=$section['sectionId']?>" class="popup-btn icon_hover badge_secondary action-transparent drp justify-content-center editSectionBtn"><?php edit() ?>Edit</a>
                        </div>
                    </div>
                </div>
                <?php
                    // --- MODULES ---
                $moduleQuery = array();
                $moduleQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_MODULE_ . " WHERE sectionId = ? ORDER BY seq ASC";
                $moduleQuery['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                $modules = $mycms->sql_select($moduleQuery);
                if (!empty($modules)) {
                ?>
                <ul class="add_module_wrap">
                    <?php
                  
                    foreach ($modules as $module) {
                    $moduleId = $module['moduleId'];
                    ?>
                    <li class="add_module">
                        <div class="registration-pop_body_box_inner module_box architec_module_box accm_top">
                            <div class="spot_name">
                                <div>
                                    <div class="regi_contact mt-0">
                                        <span>Module</span>
                                    </div>
                                    <div class="regi_name"><?= htmlspecialchars($module['moduleName']) ?></div>
                                </div>
                            </div>
                            <div class="accm_details">
                                <div class="accm_details_box">
                                    <h5>Path</h5>
                                    <h6><?= $module['path']?></h6>
                                </div>
                                 <div class="accm_details_box">
                                    <h5>Page Info</h5>
                                    <h6><?=$module['pageInfo']?></h6>
                                </div>
                            </div>
                        </div>
                        <?php
                        // --- PAGES ---
                        $pageQuery = array();
                        $pageQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_PAGES_ . " WHERE moduleId = ? ORDER BY seq ASC";
                        $pageQuery['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                        $pages = $mycms->sql_select($pageQuery);

                        if (!empty($pages)) {

                        ?>
                        <ul class="add_page_wrap">
                            <?php
                                foreach ($pages as $page) {
                                    $pageId = $page['pageId'];
                                ?>
                            <li class="add_page">
                                <div class="registration-pop_body_box_inner page_box architec_page_box accm_top">
                                    <div class="spot_name d-flex">
                                        <div>
                                            <div class="regi_contact mt-0">
                                                <span>Page</span>
                                            </div>
                                            <div class="regi_name"><?= htmlspecialchars($page['pageName']) ?></div>
                                        </div>
                                    </div>
                                    <div class="accm_details">
                                        <div class="accm_details_box">
                                            <h5>File Name</h5>
                                            <h6><?= htmlspecialchars($page['fileName']) ?></h6>
                                        </div>
                                         <div class="accm_details_box">
                                            <h5>Page Info</h5>
                                            <h6><?=$page['pageInfo']?></h6>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                // --- SUB PAGES ---
                                $subPageQuery = array();
                                $subPageQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_SUBPAGE_ . " WHERE pageId = ? ORDER BY seq ASC";
                                $subPageQuery['PARAM'][] = array('FILD'=>'pageId', 'DATA'=>$pageId, 'TYP'=>'i');
                                $subPages = $mycms->sql_select($subPageQuery);

                                 if (!empty($subPages)) {
                                    ?>
                                <ul class="add_page_wrap">
                                    <?php
                                   
                                        foreach ($subPages as $subPage) {

                                    ?>
                                    <li class="add_page">
                                        <div class="registration-pop_body_box_inner page_box architec_page_box accm_top">
                                            <div class="spot_name d-flex">
                                                <div>
                                                    <div class="regi_contact mt-0">
                                                        <span>Sub Page</span>
                                                    </div>
                                                    <div class="regi_name"><?= htmlspecialchars($subPage['subpageName']) ?></div>
                                                </div>
                                            </div>
                                            <div class="accm_details">
                                                <div class="accm_details_box">
                                                    <h5>File Name</h5>
                                                    <h6><?= $subPage['fileName']?></h6>
                                                </div>
                                                <div class="accm_details_box">
                                                    <h5>Page Info</h5>
                                                    <h6><?=$subPage['pageInfo']?></h6>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </li>
                                      <?  } ?>
                                </ul>
                                 <?  } ?>
                                <?
                                  // --- SUB PAGES ---
                                $PageTabQuery = array();
                                $PageTabQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_TAB_ . " WHERE pageId = ? ORDER BY seq ASC";
                                $PageTabQuery['PARAM'][] = array('FILD'=>'pageId', 'DATA'=>$pageId, 'TYP'=>'i');
                                $PageTab = $mycms->sql_select($PageTabQuery);

                                if (!empty($PageTab)) {
                                    ?>
                               <ul class="add_page_wrap">
                                <?php
                               
                                        foreach ($PageTab as $tab) {
                                       $tabId = $tab['tabId'];
                                    ?>
                                    <li class="add_page">
                                        <div class="registration-pop_body_box_inner page_box architec_page_box accm_top">
                                            <div class="spot_name d-flex">
                                                <div>
                                                    <div class="regi_contact mt-0">
                                                        <span>Tab Name</span>
                                                    </div>
                                                    <div class="regi_name"><?= htmlspecialchars($tab['tabName']) ?></div>
                                                </div>
                                            </div>
                                            <div class="accm_details">
                                                <div class="accm_details_box">
                                                    <h5>File Name</h5>
                                                    <h6><?= $tab['path']?></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <?
                                           $PageSubTabQuery = array();
                                            $PageSubTabQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_SUBTAB_ . " WHERE pageId = ? ORDER BY seq ASC";
                                            $PageSubTabQuery['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                                            $PageSubTab = $mycms->sql_select($PageSubTabQuery);
                                           if (!empty($PageSubTab)) {
                                                ?>        
                                        <ul class="add_page_wrap">
                                            <?
                                            
                                                    foreach ($PageSubTab as $subtab) {
                                                ?>        
                                            <li class="add_page">
                                                <div class="registration-pop_body_box_inner page_box architec_page_box accm_top">
                                                    <div class="spot_name d-flex">
                                                        <div>
                                                            <div class="regi_contact mt-0">
                                                                <span>Sub Tab Name</span>
                                                            </div>
                                                            <div class="regi_name"><?= htmlspecialchars($subtab['subtabName']) ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="accm_details">
                                                        <div class="accm_details_box">
                                                            <h5>File Name</h5>
                                                            <h6><?= $subtab['path']?></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                             <? }  ?>
                                        </ul>
                                         <? }  ?>
                                    </li>
                                    <? }  ?>
                                </ul>
                                 <? }  ?>
                            </li>
                             <? } ?>
                        </ul>
                         <? } ?>
                        <?
                                // --- SUB PAGES ---
                            $PageTabQuery = array();
                            $PageTabQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_TAB_ . " WHERE moduleId = ? AND pageId IS NULL AND subpageId IS NULL  ORDER BY seq ASC";
                            $PageTabQuery['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                            $PageTab = $mycms->sql_select($PageTabQuery);
                            if (!empty($PageTab)) {
                            ?>

                             <ul class="add_page_wrap">
                                <?php
                                
                                    foreach ($PageTab as $tab) {
                                    $tabId = $tab['tabId'];
                                ?>
                                <li class="add_page">
                                    <div class="registration-pop_body_box_inner page_box architec_page_box accm_top">
                                        <div class="spot_name d-flex">
                                            <div>
                                                <div class="regi_contact mt-0">
                                                    <span>Tab Name</span>
                                                </div>
                                                <div class="regi_name"><?= htmlspecialchars($tab['tabName']) ?></div>
                                            </div>
                                        </div>
                                        <div class="accm_details">
                                            <div class="accm_details_box">
                                                <h5>File Name</h5>
                                                <h6><?= $tab['path']?></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                        $PageSubTabQuery = array();
                                        $PageSubTabQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_SUBTAB_ . " WHERE tabId = ? ORDER BY seq ASC";
                                        $PageSubTabQuery['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                                        $PageSubTab = $mycms->sql_select($PageSubTabQuery);
                                          if (!empty($PageSubTab)) {
                                    ?>
                                    
                                    <ul class="add_page_wrap">
                                        <?
                                      
                                                foreach ($PageSubTab as $subtab) {
                                            ?>        
                                        <li class="add_page">
                                            <div class="registration-pop_body_box_inner page_box architec_page_box accm_top">
                                                <div class="spot_name d-flex">
                                                    <div>
                                                        <div class="regi_contact mt-0">
                                                            <span>Sub Tab Name</span>
                                                        </div>
                                                        <div class="regi_name"><?= htmlspecialchars($subtab['subtabName']) ?></div>
                                                    </div>
                                                </div>
                                                <div class="accm_details">
                                                    <div class="accm_details_box">
                                                        <h5>File Name</h5>
                                                        <h6><?= $subtab['path']?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                          <? } ?>
                                    </ul>
                                   <? } ?>
                                </li>
                                 <? } ?>
                            </ul>
                             <?  } ?>
                       
                    </li>
                     <? }  ?>
                </ul>
                  <? }  ?>
            </li>
             <?
                // --- SUB PAGES ---
            $PageTabQuery = array();
            $PageTabQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_TAB_ . " WHERE sectionId = ? AND moduleId IS NULL AND pageId IS NULL AND subpageId IS NULL  ORDER BY seq ASC";
            $PageTabQuery['PARAM'][] = array('FILD'=>'sectionId	', 'DATA'=>$sectionId, 'TYP'=>'i');
            $PageTab = $mycms->sql_select($PageTabQuery);
            if (!empty($PageTab)) {
           ?>
                <ul class="add_page_wrap">
                    <?php
                    
                    foreach ($PageTab as $tab) {
                    $tabId = $tab['tabId'];
                ?>
                <li class="add_page">
                    <div class="registration-pop_body_box_inner page_box architec_page_box accm_top">
                        <div class="spot_name d-flex">
                            <div>
                                <div class="regi_contact mt-0">
                                    <span>Tab Name</span>
                                </div>
                                <div class="regi_name"><?= htmlspecialchars($tab['tabName']) ?></div>
                            </div>
                        </div>
                        <div class="accm_details">
                            <div class="accm_details_box">
                                <h5>File Name</h5>
                                <h6><?= $tab['path']?></h6>
                            </div>
                        </div>
                    </div>
                    <?
                        $PageSubTabQuery = array();
                        $PageSubTabQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_SUBTAB_ . " WHERE tabId = ? ORDER BY seq ASC";
                        $PageSubTabQuery['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                        $PageSubTab = $mycms->sql_select($PageSubTabQuery);
                          if (!empty($PageSubTab)) {
                       ?>    
                    <ul class="add_page_wrap">
                        <?
                        
                                foreach ($PageSubTab as $subtab) {
                            ?>    
                        <li class="add_page">
                            <div class="registration-pop_body_box_inner page_box architec_page_box accm_top">
                                <div class="spot_name d-flex">
                                    <div>
                                        <div class="regi_contact mt-0">
                                            <span>Sub Tab Name</span>
                                        </div>
                                        <div class="regi_name"><?= htmlspecialchars($subtab['subtabName']) ?></div>
                                    </div>
                                </div>
                                <div class="accm_details">
                                    <div class="accm_details_box">
                                        <h5>File Name</h5>
                                        <h6><?= $subtab['path']?></h6>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?  } ?>

                    </ul>
                      <?  } ?>
                </li>
                 <? } } ?>
                    <? } }else{ ?>
                        <span class="mandatory" align="center">No Record Present.</span>
                <? } ?>
            </ul>
           
        </ul>
        <div class="bbp-pagination">
            <div class="bbp-pagination-count"><?= $mycms->paginateRecInfo(1) ?></div>
            <span class="paginationDisplay">
                <div class="pagination"><a><?= $mycms->paginate(1, 'pagination') ?></a></div>
            </span>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>
<script>
    $('.open_module_btn').click(function() {
        if ($(this).hasClass("active")) {
            $(".open_module_btn").removeClass('active');
            $(".architec_section_inner").slideUp();
            $(".open_page_btn").removeClass('active');
            $(".architec_module_inner").slideUp();
            $(".open_subpage_btn").removeClass('active');
            $(".architec_sub_page_inner").slideUp();
        } else {
            $(".open_module_btn").removeClass('active');
            $(".architec_section_inner").slideUp();
            $(".open_page_btn").removeClass('active');
            $(".architec_module_inner").slideUp();
            $(".open_subpage_btn").removeClass('active');
            $(".architec_sub_page_inner").slideUp();
            $(this).parent().parent().parent().parent().find('.architec_section_inner').slideToggle();
            $(this).toggleClass('active');
        }
    });
    $('.open_page_btn').click(function() {
        if ($(this).hasClass("active")) {
            $(".open_page_btn").removeClass('active');
            $(".architec_module_inner").slideUp();
        } else {
            $(".open_page_btn").removeClass('active');
            $(".architec_module_inner").slideUp();
            $(this).parent().parent().parent().parent().find('.architec_module_inner').slideToggle();
            $(this).toggleClass('active');
        }
    });
    $('.open_subpage_btn').click(function() {
        if ($(this).hasClass("active")) {
            $(".open_subpage_btn").removeClass('active');
            $(".architec_sub_page_inner").slideUp();
        } else {
            $(".open_subpage_btn").removeClass('active');
            $(".architec_sub_page_inner").slideUp();
            $(this).parent().parent().parent().parent().find('.architec_sub_page_inner').slideToggle();
            $(this).toggleClass('active');
        }
    });
    const searchInput = document.getElementById("searchInput");
    let typingTimer;
    const typingDelay = 800; // wait 0.8s after last keystroke

    searchInput.addEventListener("keyup", function() {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(() => {
            const query = this.value.trim();

            const url = new URL(window.location.href);

            if (query.length > 0) {
                url.searchParams.set('q', query);
            } else {
                url.searchParams.delete('q');
            }

            url.searchParams.delete('_pgn1_'); // reset pagination
            window.location.href = url.toString(); // reload page with new query
        }, typingDelay);
    });

    searchInput.addEventListener("keydown", () => clearTimeout(typingTimer));
     ///////////////////Section Edit///////////////////////////
   $(document).on('click', '.editSectionBtn', function () {

    let Id = $(this).data('section_id');

    $.ajax({
        url: 'includes/popup.php',
        type: 'POST',
        data: { sectionId: Id },
        success: function (response) {

            $('#editSection').html($(response).find('#editSection').html());

            // Re-initialize after DOM replacement
            initEditsection();
               $('#editSection').fadeIn();

        },
        error: function(xhr) {
            console.error('AJAX error', xhr.responseText);
        }
    });

});
       ///////////////////Section edit end///////////////////////////
</script>


</html>