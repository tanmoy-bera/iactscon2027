<?php 
include_once("includes/source.php");
include_once('includes/init.php');
include_once('includes/function.workshop.php'); 
?>
<script src="includes/dynamic_header.js"></script>
<?
$sql   =  array();
$sql['QUERY'] = "SELECT `logo_image` FROM " . _DB_EMAIL_SETTING_ . " 
											WHERE `status`='A' order by id desc limit 1";
//$sql['PARAM'][]	=	array('FILD' => 'status' ,     		 'DATA' => 'A' ,       	           'TYP' => 's');					 
$result = $mycms->sql_select($sql);

$rowLogoImage         = $result[0];

$header_image = _BASE_URL_ . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $rowLogoImage['logo_image'];
if ($rowLogoImage['logo_image'] != '') {
  $emailHeader  = $header_image;
}
?>
  <div class="dash_lt">
      <div class="logo_wrap">
          <img src="<?=$emailHeader?>" alt="">
          <!-- <a href="javascript:void(0)" class="menu-btn"><i class="fa-solid fa-bars"></i></a> -->
      </div>
      <div id="sidebar" class="menu_wrap">
          <ul id="mainNav" class="main_menu">
                <?
                // if (!empty($_GET['q'])) {

                //         $search = trim($_GET['q']);
                //         $search = addslashes($search); // (works, but see note below)

                //         $searchCondition = " AND (
                //             sectionName LIKE '%$search%'
                        
                //         )";
                //     }
                $sectionQuery = array();
                $sectionQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_SECTIONS_ . " WHERE status = ?   " . $searchCondition . "  ORDER BY seq ASC";
                $sectionQuery['PARAM'][] = array('FILD'=>'status', 'DATA'=>'A', 'TYP'=>'s');
                $sectionsResult = $mycms->sql_select($sectionQuery);
                if($sectionsResult){
                foreach ($sectionsResult as $section) {
                $sectionId = $section['sectionId'];
                if($section['is_page']==yes){
                    $hrefSec = $section['path'];
                    $hassub = "";

                }else{
                     $hrefSec ="javascript:void(null)";
                     $hassub = "has-sub";
                }
                ?>
               <li class="main_menu_li nav ">
                  <a href="<?=$hrefSec?>" class="<?=$hassub?>">
                      <span class="menu_icon"><i class="<?=$section['img']?>"></i></span>
                      <span class="menu_txt_wrap">
                          <span class="menu_txt"><?= htmlspecialchars($section['sectionName']) ?></span>
                      </span>
                  </a>
                  <?php
                        // --- MODULES ---
                    $moduleQuery = array();
                    $moduleQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_MODULE_ . " WHERE sectionId = ? ORDER BY seq ASC";
                    $moduleQuery['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                    $modules = $mycms->sql_select($moduleQuery);
                    if (!empty($modules)) {
                    ?>
                  <ul class="sub_menu">
                        <?php
                    
                        foreach ($modules as $module) {
                        $moduleId = $module['moduleId'];
                        if($module['is_page']==yes){
                            $hrefModule = $module['path'];

                            $hassubSub = "";
                        }else{
                            $hrefModule ="javascript:void(null)";
                             $hassubSub = "sub-has-sub";
                        }
                        ?>
                      <li class="sub_menu_li">
                          <a href="<?=$hrefModule?>" class="<?=$hassubSub?>">
                              <span class="menu_txt_wrap">
                                  <span class="menu_txt"><?= htmlspecialchars($module['moduleName']) ?></span>
                              </span>
                          </a>
                           <?php
                            // --- PAGES ---
                            $pageQuery = array();
                            $pageQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_PAGES_ . " WHERE moduleId = ? ORDER BY seq ASC";
                            $pageQuery['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                            $pages = $mycms->sql_select($pageQuery);

                            if (!empty($pages)) {

                            ?>
                          <ul class="sub_sub_menu">
                             <?php
                                foreach ($pages as $page) {
                                    $pageId = $page['pageId'];
                                ?>
                              <li class="sub_menu_li">
                                  <a href="<?=$page['fileName'] ?>">
                                      <span class="menu_txt_wrap">
                                          <span class="menu_txt"><?= htmlspecialchars($page['pageName']) ?></span>
                                      </span>
                                  </a>
                              </li>
                               <? } ?>
                          </ul>
                           <? } ?>
                      </li>
                      <? } ?>
                     
                  </ul>
                <? } ?>
              </li>
            <? } }?>
          </ul>
      </div>
      <div class="dash_lt_btm">
          MANAGED BY <img style="width: 88px; margin-left: 6px;" src="https://ruedakolkata.com/natcon_2025/webmaster/rueda_logo.png" alt="">
      </div>
    <button class="slide_left_menu">
          <i class="fal fa-grip-lines-vertical"></i>
      </button>
  </div>
  

   <!-- Global Toaster Notifications -->
    <div id="successToaster" class="toaster" style="display: none;">
        <div class="toaster_inner badge_success">
            <span class="badge_success">✔</span>
            <p>Data Added Successfully</p>
        </div>
    </div>

    <div id="errorToaster" class="toaster" style="display: none;">
        <div class="toaster_inner badge_danger">
            <span class="badge_danger">✖</span>
            <p>Data Error</p>
        </div>
    </div>
   <script>
document.addEventListener("DOMContentLoaded", function() {

    function showToaster(type, message = '') {
        const toasterId = type === 'success' ? 'successToaster' : 'errorToaster';
        const toaster = document.getElementById(toasterId);
        if (!toaster) return;

        if (message) {
            toaster.querySelector('p').textContent = message;
        }

        toaster.style.display = 'block';
        setTimeout(() => {
            toaster.style.display = 'none';
        }, 5000); // 5 seconds, adjust as needed
    }

    // Check PHP session for pre-set success/error messages
    <?php if(!empty($_SESSION['toaster'])): 
        $toaster = $_SESSION['toaster'];
        $type = $toaster['type'];
        $message = addslashes($toaster['message']);
        unset($_SESSION['toaster']);
    ?>
        showToaster('<?= $type ?>', '<?= $message ?>');
    <?php endif; ?>
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    function normalize(url) {
        try {
            url = new URL(url, window.location.origin);
        } catch (e) {
            return url;
        }
        // strip trailing slash + lowercase, ignore query string/hash
        return url.pathname.replace(/\/+$/, '').toLowerCase();
    }

    const currentPath = normalize(window.location.href);

    document.querySelectorAll('#mainNav a[href]').forEach(function (link) {
        const href = link.getAttribute('href');
        if (!href || href.indexOf('javascript:') === 0) return;

        if (normalize(link.href) === currentPath) {

            link.classList.add('active');

            // walk up through every parent <li> inside #mainNav
            let li = link.closest('li');
            while (li && li.closest('#mainNav')) {
                li.classList.add('active', 'open');

                const parentUl = li.parentElement; // sub_menu or sub_sub_menu
                if (parentUl && (parentUl.classList.contains('sub_menu') ||
                                  parentUl.classList.contains('sub_sub_menu'))) {
                    parentUl.style.display = 'block'; // force submenu open
                    const parentLink = parentUl.previousElementSibling;
                    if (parentLink && parentLink.tagName === 'A') {
                        parentLink.classList.add('active');
                    }
                }
                li = li.parentElement ? li.parentElement.closest('li') : null;
            }
        }
    });
});
</script>