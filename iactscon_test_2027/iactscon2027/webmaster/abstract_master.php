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
        <h2>Abstract Registration</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Abstract Registration</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Abstract Master</a></li>
                    </ol>
                </nav>
                <h2>Abstract Master</h2>
                <h6>Manage tariff, dates, packages, and classifications.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_left">
                <h6>Master Menu</h6>
                 <?
                $currentPage = basename($_SERVER['PHP_SELF']);

                // If you want to ensure it's from webmaster folder
                $currentPath = $_SERVER['SCRIPT_NAME'];
                if (strpos($currentPath, '/webmaster/') !== false) {
                    $currentPage = basename($currentPath);
                }
                // --- SUB PAGES ---
                $PageTabQuery = array();
                $PageTabQuery = array();
                $PageTabQuery['QUERY'] = "SELECT * FROM " . _DB_WEBMASTER_TAB_ . " 
                                        WHERE path LIKE '%" . $currentPage . "%' 
                                        AND status='A' 
                                        ORDER BY seq ASC";
                $PageTab = $mycms->sql_select($PageTabQuery);
       
                if (!empty($PageTab)) {
                    $firstButton = true; // Flag to track first button
                     foreach ($PageTab as $tab) {
                     $tabId = $tab['tabId'];
                     $path = $tab['path']; // e.g., "company_info.php#ef"
                     $fragment = parse_url($path, PHP_URL_FRAGMENT);
                                if ($firstButton) {
                        $buttonClass = 'com_info_left_click icon_hover badge_primary active';
                        $firstButton = false; // Set flag to false after first button
                    } else {
                        $buttonClass = 'com_info_left_click icon_hover badge_primary action-transparent';
                    }

                ?>
                <button data-tab="<?=$fragment?>" class="<?=$buttonClass?>"><?=$tab['tabName']?></button>
               <? } } ?>
            </div>
            <div class="com_info_right">
                <div class="com_info_box active" id="md">
                   
                    <form name="frmtypeedit" method="post" action="<?= $cfg['SECTION_BASE_URL']?>manage_company_information.process.php" id="frmtypeedit"
                    enctype="multipart/form-data">
                   
                    <input type="hidden" name="act" value="edit_template_module" />
                    <input type="hidden" name="id" id="id" value="<?= $row['id'] ?>" />
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php invoive() ?></span> Configuration</n>
                                  <div class="page_top_wrap_right">
                                    <a href="#" id="saveChanges"  class="badge_success"><i class="fal fa-save"></i>Save Changes</a>
                                </div>
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid g_3">
                                    <div class="frm_grp">
                                        <p class="frm-head">Submission Start Date</p>
                                        <input type="date" id="abstract_start_date" name="abstract_start_date"
                                            value="<?= $row['abstract_start_date'] ?>">
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Submission Deadline</p>
                                        <input type="date" id="abstract_submission_date" name="abstract_submission_date"
                                            value="<?= $row['abstract_submission_date'] ?>">
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Confirmation Date</p>
                                        <input type="date" id="abstract_confirmation_date" name="abstract_confirmation_date"
                                            value="<?= $row['abstract_confirmation_date'] ?>">
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Abstract Email</p>
                                        <input type="text" id="abstract_sender_email" name="abstract_sender_email"
                                            value="<?= $row['abstract_sender_email'] ?>" required>
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Title Word Limit</p>
                                        <input type="number" id="abstract_title_word_limit" name="abstract_title_word_limit"
                                            value="<?= $row['abstract_title_word_limit'] ?>" />
                                        <select name="abstract_word_title_type" id="abstract_word_title_type">
                                            <option value="" selected="">Select Type</option>
                                            <option value="word" <?php if ($row['abstract_word_title_type'] == 'word') {
                                                echo 'selected';
                                            } ?>>Word</option>
                                            <option value="character" <?php if ($row['abstract_word_title_type'] == 'character') {
                                                echo 'selected';
                                            } ?>>
                                                Character</option>

                                        </select>
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">Abstract Word Limit</p>
                                        <input type="number" id="abstract_total_word_limit" name="abstract_total_word_limit"
                                            value="<?= $row['abstract_total_word_limit'] ?>" />
                                        <select name="abstract_total_word_type" id="abstract_total_word_type">
                                            <option value="" selected="">Select Type</option>
                                            <option value="word" <?php if ($row['abstract_total_word_type'] == 'word') {
                                                echo 'selected';
                                            } ?>>Word</option>
                                            <option value="character" <?php if ($row['abstract_total_word_type'] == 'character') {
                                                echo 'selected';
                                            } ?>>
                                                Character</option>

                                        </select>
                                    </div>
                                    <div class="frm_grp">
                                        <p class="frm-head">HOD Consent Letter</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check outfrm_check">
                                                <input type="checkbox" type="checkbox" id="" name="consent_files[]"
                                                    value="pdf" <?php if (in_array("pdf",json_decode($row['hod_consent_file_types']))) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">PDF</span>
                                            </label>
                                            <label class="cus_check outfrm_check">
                                                <input type="checkbox" id="" name="consent_files[]" style="width: 20px;"
                                                    value="image" <?php if (in_array("image", json_decode($row['hod_consent_file_types']))) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">Image</span>
                                            </label>
                                            <label class="cus_check outfrm_check">
                                                <input type="checkbox" id="" name="consent_files[]" style="width: 20px;"
                                                    value="word" <?php if (in_array("word", json_decode($row['hod_consent_file_types']))) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">Word</span>
                                            </label>
                                        </div>
                                    </div>
                                  
                                    <div class="frm_grp span_1">
                                        <p class="frm-head">Abstract file type</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check outfrm_check">
                                                <input type="checkbox" id="" name="abstract_files[]" style="width: 20px;"
                                                    value="pdf" <?php if (in_array("pdf", json_decode($row['abstract_file_types']))) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">PDF</span>
                                            </label>
                                            <label class="cus_check outfrm_check">
                                                <input type="checkbox" id="" name="abstract_files[]" style="width: 20px;"
                                                    value="image" <?php if (in_array("image", json_decode($row['abstract_file_types']))) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">Image</span>
                                            </label>
                                            <label class="cus_check outfrm_check">
                                                <input type="checkbox" type="checkbox" id="" name="abstract_files[]"
                                                    style="width: 20px;" value="word" <?php if (in_array("word", json_decode($row['abstract_file_types']))) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">Word</span>
                                            </label>
                                        </div>
                                    </div>
                                
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">Abstract Submission Guideline</p>
                                        <div class="cus_check_wrap">
                                            <label class="cus_check outfrm_radio">
                                                <input type="radio" id="html" class="unique-class1" style="width: 30px;"
                                                    name="guideline_pdf_flag" value="1" <?php if ($row['guideline_pdf_flag'] == 1) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">PDF Link</span>
                                            </label>
                                            <label class="cus_check outfrm_radio">
                                                <input type="radio" class="unique-class1" id="css" style="width: 30px;"
                                                    name="guideline_pdf_flag" value="2" <?php if ($row['guideline_pdf_flag'] == 2) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">PDF File</span>
                                            </label>
                                            <label class="cus_check outfrm_radio">
                                                <input type="radio" class="unique-class1" id="css" style="width: 30px;"
                                                    name="guideline_pdf_flag" value="0" <?php if ($row['guideline_pdf_flag'] == 0) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="checkmark">Remove Guideline</span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="frm_grp span_1">
                                        <p class="frm-head">Abstract Guideline Website</p>
                                        <input type="text" id="abstract_guideline_website" name="abstract_guideline_website"
                                            value="<?= $row['abstract_guideline_website'] ?>">
                                    </div>
                                    <div class="frm_grp">

                                        <input type="text" id="abstract_guideline_pdf" name="abstract_guideline_pdf" style="width:60%;display: <?php if (!empty($row['abstract_guideline_pdf'])) {
                                            echo "block";
                                        } else {
                                            echo "none";
                                        } ?>;" value="<?= $row['abstract_guideline_pdf'] ?>" />

                                        <span style="display: <?php if (!empty($row['abstract_guideline_pdf_file'])) {
                                            echo "block";
                                        } else {
                                            echo "none";
                                        } ?>;" id="pdf_file">
                                            <input type="file" id="abstract_guideline_pdf_file"
                                                name="abstract_guideline_pdf_file" style="width:60%;" accept=".pdf" />
                                            <?php
                                            if (!empty($row['abstract_guideline_pdf_file'])) {
                                                ?>
                                                <a href="<?= '../../' . $cfg['FILES.ABSTRACT.REQUEST'] . $row['abstract_guideline_pdf_file']; ?>"
                                                    target="_blank">Download</a>
                                                <?php
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><i class="fal fa-grip-lines"></i></span> Page Headers
                            </h5>
                            <div class="com_info_box_inner">
                                <div class="form_grid">
                                      <div class="frm_grp span_4">
                                            <p class="frm-head">Abstract Header</p>
                                            <textarea name="abstract_header_text" class="txtarea textArea "
                                                id="abstract_header_text"><?php echo htmlspecialchars($row['abstract_header_text']); ?></textarea>
                                        </div>
                                        <script src="<?= _BASE_URL_ ?>webmaster/lib/tinymce/tinymce.min.js"></script>
                                        <script type="text/javascript">
                                            tinymce.init({
                                                selector: '.textArea',
                                                width: 500,
                                                height: 250,
                                                plugins: 'advlist autolink link image lists charmap print preview hr anchor pagebreak ' +
                                                    'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking ' +
                                                    'table emoticons template paste help',
                                                toolbar: 'undo redo | formatselect | bold italic backcolor | ' +
                                                    'alignleft aligncenter alignright alignjustify | ' +
                                                    'bullist numlist outdent indent | removeformat | help | ' +
                                                    'link image media | code preview',
                                                menubar: 'file edit view insert format tools table'
                                            });
                                            
                                        </script>

                                </div>
                            </div>
                        </div>
                    </div>
                   
                 </form>
                </div>
                <div class="com_info_box" id="abstopic">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_primary"><?php credit() ?></span> Topic</n>
                                <a class="add mi-1 popup-btn" data-tab="newabstopic"><?php add() ?>Add Topic</a>
                            </h5>
                            <?php
                            $loggedUserId		= $mycms->getLoggedUserId();

                            $sqlAbstractTopic			  =	array();
                            $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                                WHERE `status` IN ('A','I')
                                                            ORDER BY `id` ASC";

                            //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
                            $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

                             ?>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Topic</th>
                                                <th>Category</th>
                                                <th>Sub Category</th>
                                                <th class="action text-right">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?
                                            if ($resultAbstractTopic) {

                                                foreach ($resultAbstractTopic as $key => $value) {
                                                    if ($value['status'] == 'A') {
                                                        $status = 'Active';
                                                    } elseif ($value['status'] == 'I') {
                                                        $status = 'Inactive';
                                                    }
                                                    $getCategoryName = getCategoryName($value['category']);
                                                    $getSubCategoryName = getSubcatName($value['sub_category']);
                                            ?>
                                            <tr>
                                                <td class="sl"><?= $key + 1 ?></td>
                                                <td><?= $value['abstract_topic'] ?></td>
                                                <td><?= $getCategoryName ?></td>
                                                <td><?= $getSubCategoryName ?></td>
                                                <td class="action">
                                                    <div class="action_div">
                                                      <?php	
                                                        if($value['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'Inactive':'Active'?>&id=<?=$value['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'Inactive':'Active'?>&id=<?=$value['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="javascript:void(null)" data-tab="editabstopic" 
                                                            data-id="<?= $value['id'] ?>"
                                                            data-abstract_topic="<?= $value['abstract_topic'] ?>"
                                                            data-category="<?= $getCategoryName?>"
                                                            data-sub_category="<?= $getSubCategoryName?>"
                                                             data-status="<?= $value['status'] ?>" class="popup-btn icon_hover badge_secondary action-transparent editabstractTopicBtn"><?php edit(); ?>Edit Details</a>
                                                            <a href="javascript:void(0);" 
                                                            class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                            onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                            window.location.href='abstract_master_submission.php?act=deleteTopic&ID=<?= $value['id'] ?>&modified_by=<?= $loggedUserId ?>'; 
                                                                        }">
                                                                <?php delete(); ?>Delete
                                                        </a>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <? } }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="com_info_box" id="abscategory">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_secondary"><?php conregi() ?></span> Category</n>
                                <a class="add mi-1 popup-btn" data-tab="newabscategory"><?php add() ?>Add Category</a>
                            </h5>
                            <?
                                $loggedUserId		= $mycms->getLoggedUserId();

                                $sqlAbstractTopic			  =	array();
                                $sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_TOPIC_CATEGORY_." 
                                                                WHERE `status` IN ('A','I')
                                                            ORDER BY `id` ASC";
                                
                                //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
                                $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

                            ?>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Category</th>
                                                <th>Fields</th>
                                                <th class="action text-right">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?
                                            if($resultAbstractTopic){
                                                
                                                foreach ($resultAbstractTopic as $key => $value) {
                                                      $catId =$value['id'];
                                                    if($value['status'] == 'A'){
                                                        $status = 'Active';
                                                    }
                                                    elseif($value['status'] == 'I'){
                                                        $status = 'Inactive';
                                                    } ?>
                                                  
                                            <tr>
                                                <td class="sl"><?=$key+1?></td>
                                                <td><?=$value['category']?></td>
                                                <td>
                                                    <?
                                                       $category_fields = json_decode($value['category_fields']);

                                                       $field_ids = implode(",", $category_fields);
                                                        if (!empty($field_ids)) {
                                                            $order_by = "ORDER BY FIELD(id, " . $field_ids . ")";
                                                        } else {
                                                            $order_by = "ORDER BY id ASC";
                                                        }
                                                    	$sqlAbstractFields			  =	array();
                                                        $sqlAbstractFields['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_FIELDS_." 
                                                                            WHERE `status` ='A'
                                                                         ".$order_by."";


                                                        $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
                                                          if(count($resultAbstractFields)>0)
                                                            {
                                                                foreach ($resultAbstractFields as $key => $value) {
                                                                    if (in_array($value['id'], $category_fields)){ 
										                            ?>
                                                    <n class="badge_padding badge_dark mx-1"><?=$value['display_name']?></n>
                                                    <? } } } ?>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <?php	
                                                        if($status=='Active'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($status=='Active')?'InactiveCat':'ActiveCat'?>&id=<?=$catId;?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($status=='Active')?'InactiveCat':'ActiveCat'?>&id=<?= $catId;?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="javascript:void(null)" data-tab="editabscategory"  data-id="<?= $catId ?>"
                                                          
                                                             data-status="<?= $status ?>" class="popup-btn icon_hover badge_secondary action-transparent categoryeditBtn"><?php edit(); ?>Edit Details</a>
                                                            <a href="javascript:void(0);" 
                                                                class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                                onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                                window.location.href='abstract_master_submission.php?act=deleteCategory&ID=<?= $catId?>&modified_by=<?=$loggedUserId?>'; 
                                                                            }">
                                                                    <?php delete(); ?>Delete
                                                            </a>                                                        
                                                        </ul>
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
                <div class="com_info_box" id="abssubmission">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php conregi() ?></span> Sub Category 1</n>
                                <a class="add mi-1 popup-btn" data-tab="newabssubmission"><?php add() ?>Add Sub category 1</a>
                            </h5>
                            <?
                                $loggedUserId		= $mycms->getLoggedUserId();

                                $sqlAbstractTopic			  =	array();
                                $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
                                                                    WHERE `status` IN ('A','I')
                                                                ORDER BY `id` ASC";

                                //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
                                $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);


                            ?>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Sub Category Name</th>
                                                <th>Category</th>
                                                <th>Fields</th>
                                                 <th>Description</th>
                                                <th class="action text-right">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?
                                            if ($resultAbstractTopic) {

                                                foreach ($resultAbstractTopic as $key => $value) {

                                                    $sqlAbstractTopicCat			  =	array();
                                                    $sqlAbstractTopicCat['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                                                                                        WHERE `status` ='A'
                                                                                    AND id='" . $value['category'] . "'";


                                                    $resultAbstractTopicCat = $mycms->sql_select($sqlAbstractTopicCat);

                                                    if ($value['status'] == 'A') {
                                                        $status = 'Active';
                                                    } elseif ($value['status'] == 'I') {
                                                        $status = 'Inactive';
                                                    } ?>

                                            <tr>
                                                <td class="sl"><?= $key + 1 ?></td>
                                                <td><?= $value['abstract_submission'] ?></td>
                                                <td><?= $resultAbstractTopicCat[0]['category'] ?></td>
                                                 <td>
                                                    <?
                                                       $category_fields = json_decode($value['category_fields']);

                                                       $field_ids = implode(",", $category_fields);
                                                        if (!empty($field_ids)) {
                                                            $order_by = "ORDER BY FIELD(id, " . $field_ids . ")";
                                                        } else {
                                                            $order_by = "ORDER BY id ASC";
                                                        }
                                                    	$sqlAbstractFields			  =	array();
                                                        $sqlAbstractFields['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_FIELDS_." 
                                                                            WHERE `status` ='A'
                                                                          " . $order_by." ";


                                                        $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
                                                          if(count($resultAbstractFields)>0)
                                                            {
                                                                foreach ($resultAbstractFields as $key => $value1) {
                                                                    if (in_array($value1['id'], $category_fields)){ 
										                            ?>
                                                    <n class="badge_padding badge_dark mx-1"><?=$value1['display_name']?></n>
                                                    <? } } } ?>
                                                </td>
                                                 <td><?= $value['description'] ?></td>
                                                <td class="action">
                                                    <div class="action_div">
                                                         <?php	
                                                        if($value['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'InactivesubCat':'ActivesubCat'?>&id=<?=$value['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'InactivesubCat':'ActivesubCat'?>&id=<?=$value['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="javascript:void(null)" data-tab="editabssubmission"  data-id="<?= $value['id'] ?>"
                                                            data-subcategory="<?=$value['abstract_submission'] ?>" data-description="<?=$value['description'] ?>"
                                                            data-categoryname="<?= $resultAbstractTopicCat[0]['id']?>"  data-status="<?= $value['status'] ?>" class="popup-btn icon_hover badge_secondary action-transparent editSubCatBtn"><?php edit(); ?>Edit Details</a>
                                                            <a href="javascript:void(0);" 
                                                                class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                                onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                                window.location.href='abstract_master_submission.php?act=deleteSubmission&ID=<?= $value['id'] ?>&modified_by=<?= $loggedUserId ?>'; 
                                                                            }">
                                                                    <?php delete(); ?>Delete
                                                            </a>                         
                                                        </ul>
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
                <div class="com_info_box" id="abspresentation">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_info"><?php workshop() ?></span>Sub Category 2</n>
                                <a class="add mi-1 popup-btn" data-tab="newabspresentation"><?php add() ?>Add Sub Category 2</a>
                            </h5>
                            <?
                                $loggedUserId		= $mycms->getLoggedUserId();

                                $sqlAbstractTopic			  =	array();
                                $sqlAbstractTopic['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_PRESENTATION_ . " 
                                                                    WHERE `status` IN ('A','I')
                                                                ORDER BY `id` ASC";

                                //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
                                $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);


                            ?>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Sub Category Name</th>
                                                <th>Category</th>
                                                <th>Fields</th>
                                                <th>Description</th>
                                                <th class="action text-right">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?
                                            if ($resultAbstractTopic) {

                                                foreach ($resultAbstractTopic as $key => $value) {

                                                    $sqlAbstractTopicCat			  =	array();
                                                    $sqlAbstractTopicCat['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                                                                                        WHERE `status` ='A'
                                                                                    AND id='" . $value['category_id'] . "'";


                                                    $resultAbstractTopicCat = $mycms->sql_select($sqlAbstractTopicCat);

                                                    $sqlAbstractTopicSub			  =	array();
                                                    $sqlAbstractTopicSub['QUERY']    = "SELECT * FROM " . _DB_ABSTRACT_SUBMISSION_ . " 
                                                                                        WHERE `status` ='A'
                                                                                    AND id='" . $value['submission_id'] . "'";


                                                    $resultAbstractTopicSub = $mycms->sql_select($sqlAbstractTopicSub);

                                                    if ($value['status'] == 'A') {
                                                        $status = 'Active';
                                                    } elseif ($value['status'] == 'I') {
                                                        $status = 'Inactive';
                                                    } ?>
                                            <tr>
                                                <td class="sl"><?= $key + 1 ?></td>
                                                <td><?= $value['abstract_presentation'] ?></td>
                                                <td><?= $resultAbstractTopicCat[0]['category'] . "-" . $resultAbstractTopicSub[0]['abstract_submission'] ?></td>
                                                <td>
                                                    <?
                                                       $category_fields = json_decode($value['category_fields']);

                                                       $field_ids = implode(",", $category_fields);
                                                        if (!empty($field_ids)) {
                                                            $order_by = "ORDER BY FIELD(id, " . $field_ids . ")";
                                                        } else {
                                                            $order_by = "ORDER BY id ASC";
                                                        }
                                                    	$sqlAbstractFields			  =	array();
                                                        $sqlAbstractFields['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_FIELDS_." 
                                                                            WHERE `status` ='A'
                                                                          " . $order_by." ";


                                                        $resultAbstractFields = $mycms->sql_select($sqlAbstractFields);
                                                          if(count($resultAbstractFields)>0)
                                                            {
                                                                foreach ($resultAbstractFields as $key => $value1) {
                                                                    if (in_array($value1['id'], $category_fields)){ 
										                            ?>
                                                    <n class="badge_padding badge_dark mx-1"><?=$value1['display_name']?></n>
                                                    <? } } } ?>
                                                </td>
                                                <td><?= $value['description'] ?></td>
                                                <td class="action">
                                                    <div class="action_div">
                                                        <?php	
                                                        if($value['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'InactivePresentation':'ActivePresentation'?>&id=<?=$value['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'InactivePresentation':'ActivePresentation'?>&id=<?=$value['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="action">
                                                    <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="javascript:void(null)" data-status="<?= $value['status'] ?>" data-tab="editabspresentation"  data-description="<?=$value['description'] ?>" data-category="<?=$value['category_id'] . "-" . $value['submission_id']?>"  data-presentation="<?= $value['abstract_presentation'] ?>"  data-id="<?= $value['id'] ?>" class="popup-btn icon_hover badge_secondary action-transparent editabspresentationbtn"><?php edit(); ?>Edit Details</a>
                                                              <a href="javascript:void(0);" 
                                                                class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                                onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                                window.location.href='abstract_master_submission.php?act=deletePresentation&ID=<?= $value['id'] ?>&modified_by=<?= $loggedUserId ?>'; 
                                                                            }">
                                                                    <?php delete(); ?>Delete
                                                            </a>                        
                                                        </ul>
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
                <div class="com_info_box" id="absfields">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_danger"><?php hotel() ?></span> Fields</n>
                                 <!-- <a class="add mi-1 popup-btn" data-tab="newabsfield"><?php add() ?>Add Field</a> -->
                            </h5>
                            <? 
                                $loggedUserId		= $mycms->getLoggedUserId();

                                $sqlAbstractTopic			  =	array();
                                $sqlAbstractTopic['QUERY']    = "SELECT * FROM "._DB_ABSTRACT_FIELDS_." 
                                                                WHERE `status` IN ('A','I')
                                                            ORDER BY `id` ASC";
                                
                                //$sqlAbstractTopic['PARAM'][]  = array('FILD' => 'status', 'DATA' =>'A',  'TYP' => 's');
                                $resultAbstractTopic = $mycms->sql_select($sqlAbstractTopic);

                            ?>
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Field Name</th>
                                                <th class="action text-right">Status</th>
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?
                                            if($resultAbstractTopic){
                                                
                                                foreach ($resultAbstractTopic as $key => $value) {
                                                    if($value['status'] == 'A'){
                                                        $status = 'Active';
                                                    }
                                                    elseif($value['status'] == 'I'){
                                                        $status = 'Inactive';
                                                    } ?>

                                            <tr>
                                                <td class="sl"><?=$key+1?></td>
                                                <td><?=$value['display_name']?></td>
                                                <td class="action">
                                                    <div class="action_div">
                                                      <?php	
                                                        if($value['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'InactiveField':'ActiveField'?>&id=<?=$value['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'InactiveField':'ActiveField'?>&id=<?=$value['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="action" align='center'>
                                                    <!-- <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="javascript:void(null)" data-tab="editabsfield" class="popup-btn icon_hover badge_secondary action-transparent"><?php edit(); ?>Edit Details</a>
                                                            <a href="javascript:void(0);" 
                                                                class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                                onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                                window.location.href='abstract_submission.php?show=deletePresentation&ID=<?= $value['id'] ?>&modified_by=<?= $loggedUserId ?>'; 
                                                                            }">
                                                                    <?php delete(); ?>Delete
                                                            </a>                                                           
                                                        </ul>
                                                    </div> -->
                                                    <a href="javascript:void(null)" title="Edit Details" data-tab="editabsfield" data-id="<?=$value['id']?>" data-field="<?=$value['display_name']?>" data-status="<?=$value['status']?>" class="popup-btn icon_hover badge_secondary action-transparent editFieldBtn"><?php edit(); ?></a>

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
                 <div class="com_info_box" id="absnom">
                    <div class="com_info_box_grid">
                        <div class="com_info_box_grid_box">
                            <h5 class="com_info_box_head">
                                <n><span class="text_success"><?php conregi() ?></span>Nomination</n>
                                <a class="add mi-1 popup-btn" data-tab="addNomination"><?php add() ?>Add Nomination</a>
                            </h5>
                           
                            <div class="com_info_box_inner">
                                <!-- <h4 class="com_info_box_inner_sub_head"><span>Manage Lunch Dates</span><a class="add mi-1"><?php add(); ?>Add Date</a></h4> -->
                                <div class="table_wrap">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="sl">#</th>
                                                <th>Nomination Name</th>
                                                <th>Description</th>
                                                <th>Related Category</th>
                                                <th>Related Sub Category</th>
                                                <th>Related Topic</th>
                                                <th>Total Submission</th>
                                                <!-- <th class="action text-right">Status</th> -->
                                                <th class="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $abstractCounter               = 1;
                                                $sqlAbstractAward    		   = array();
                                                $sqlAbstractAward['QUERY']     = "SELECT * 
                                                                                        FROM " . _DB_AWARD_MASTER_ . " 
                                                                                    WHERE `status` != ? 
                                                                                    ORDER BY `award_name` ASC";
                                                $sqlAbstractAward['PARAM'][]   = array('FILD' => 'status',  'DATA' => 'D',  'TYP' => 's');

                                                $resultAbstractAward = $mycms->sql_select($sqlAbstractAward);
                                                if ($resultAbstractAward) {
                                                    foreach ($resultAbstractAward as $keyAbstractAward => $rowAbstractAward) {
                                                        $searchCondition          	   = " AND abstractRequest.id IN ( SELECT submission_id FROM " . _DB_AWARD_REQUEST_ . " WHERE award_id = '" . $rowAbstractAward['id'] . "' AND status = 'A' )";
                                                        $sqlAbstractDetails			   = abstractDetailsQuerySet("", $searchCondition);
                                                        $resultAbstractDetails         = $mycms->sql_select($sqlAbstractDetails);
                                                ?>
                                            <tr>
                                                <td class="sl"><?= $keyAbstractAward + 1 ?></td>
                                                <td><?= $rowAbstractAward['award_name'] ?></td>
                                                <td><?= $rowAbstractAward['award_description'] == '' ? "---" : $rowAbstractAward['award_description'] ?></td>
                                                <?php
                                                $sql  			  = array();
                                                $sql['QUERY']     = " SELECT * FROM " . _DB_ABSTRACT_TOPIC_ . " 
                                                                    WHERE `status` = 'A' AND `id`='" . $rowAbstractAward['related_topic_id'] . "'";
                                                $resultAbstractType = $mycms->sql_select($sql);

                                                $sqlCat  			  = array();
                                                $sqlCat['QUERY']     = " SELECT * FROM " . _DB_ABSTRACT_TOPIC_CATEGORY_ . " 
                                                                    WHERE `status` = 'A' AND `id`='" . $rowAbstractAward['related_category_id'] . "'";
                                                $resultCat = $mycms->sql_select($sqlCat);
                                                $sqlAbstractSubmission['QUERY'] = "SELECT `abstract_submission` FROM "._DB_ABSTRACT_SUBMISSION_."
                                                    WHERE status='A'  AND `id`='" . $rowAbstractAward['related_subcategoryId'] . "' ORDER BY category ASC";
                                                    $resultAbstractSubmission = $mycms->sql_select($sqlAbstractSubmission);

                                                ?>
                                                <td><?php echo $resultCat[0]['category'] ?></td>
                                                <td><?php echo $resultAbstractSubmission[0]['abstract_submission'] ?></td>
                                                <td><?= $rowAbstractAward['related_topic_id'] == '' ?  '-' :  $resultAbstractType[0]['abstract_topic'] ?></td>
                                                <td><?= is_array($resultAbstractDetails) ? count($resultAbstractDetails) : 0 ?></td>
                                                <!-- <td class="action">
                                                    <div class="action_div">
                                                         <?php	
                                                        if($value['status']=='A'){
                                                         ?>
                                                          <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'InactivesubCat':'ActivesubCat'?>&id=<?=$value['id'];?>" class="badge_padding  badge_success w-max-con text-uppercase">Active</a>
                                                        <?php	
                                                        }else{
                                                        ?>
                                                         <a href="<?= $cfg['SECTION_BASE_URL'] ?>abstract_master_submission.php?act=<?=($value['status']=='A')?'InactivesubCat':'ActivesubCat'?>&id=<?=$value['id'];?>" class="badge_padding  badge_danger w-max-con text-uppercase">Inactive</a>
                                                        <?php	
                                                        }
                                                        ?>
                                                    </div>
                                                </td> -->
                                                <td class="action">
                                                    <div class="action_div dropdown" role="menu" aria-label="Actions for ${item.name}">
                                                        <button class="icon_hover badge_dark action-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open actions menu for ${item.name}"><?php ellips(); ?></button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a href="javascript:void(null)" data-tab="editNomination"  data-id="<?= $rowAbstractAward['id'] ?>" class="popup-btn icon_hover badge_secondary action-transparent editNominationBtn"><?php edit(); ?>Edit Details</a>
                                                            <a href="javascript:void(0);" 
                                                                class="icon_hover badge_danger action-transparent br-5 w-auto" 
                                                                onclick="if (confirm('Do you really want to remove this record?')) { 
                                                                                window.location.href='abstract.free_papers.process.php?act=removeNomination&ID=<?= $rowAbstractAward['id'] ?>&modified_by=<?= $loggedUserId ?>'; 
                                                                            }">
                                                                    <?php delete(); ?>Delete
                                                            </a>                         
                                                        </ul>
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
     $('#saveChanges').click(function () {

        // Get active tab ID
        var activeTabId = $('.com_info_box.active').attr('id');

        if (!activeTabId) {
            alert("No active tab found.");
            return;
        }

        // Find form inside active tab
        var activeForm = $('#' + activeTabId).find('form');

        if (activeForm.length) {
            activeForm.submit();
        } else {
            alert("No form found inside this tab.");
        }
    });
</script>
<script>
    /////////////topic  start////////////
    $(document).on('click', '.editabstractTopicBtn', function() {
        var id     = $(this).data('id');
        var abstract_topic  = $(this).data('abstract_topic');
        var category   = $(this).data('category');
        var sub_category  = $(this).data('sub_category');
        var status = $(this).data('status');


        $('#topic_id').val(id);
        $('#abstract_topic').val(abstract_topic);
        $('#category').text(category);
        $('#sub_category').text(sub_category);

        if(status === 'A') {
            $('#topic_status_active').prop('checked', true);
        } else {
            $('#topic_status_inactive').prop('checked', true);
        }

        // Trigger the popup-btn functionality
     $('#editabstopic').fadeIn(); // or your popup open function
    });
        /////////////topic  end////////////
        /////////////Category  start////////////
    $(document).on('click', '.categoryeditBtn', function() {
        var id     = $(this).data('id');
    
        var status = $(this).data('status');
        
        if(status === 'Active') {
            $('#category_status_active').prop('checked', true);
        } else {
            $('#category_status_inactive').prop('checked', true);
        }
        
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                catEditId: id
               
            },
             success: function(response) {


        $('#editabscategory').html($(response).find('#editabscategory').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initEditcategory();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });

    

        // Trigger the popup-btn functionality
     $('#editabscategory').fadeIn(); // or your popup open function
    });
        /////////////Category  end////////////
         /////////////subCategory  start////////////
    $(document).on('click', '.editSubCatBtn', function() {
        var subCatId     = $(this).data('id');
        var subcategory   = $(this).data('subcategory');
        var categoryname  = $(this).data('categoryname');
        var status = $(this).data('status');
        var description  = $(this).data('description');


        $('#subCatId').val(subCatId);
        $('#subcategory').val(subcategory);
        $('#categoryId').val(categoryname);
        $('#subCatdescription').val(description);

        if(status === 'A') {
            $('#subcat_status_active').prop('checked', true);
        } else {
            $('#subcat_status_inactive').prop('checked', true);
        }
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                subCatId: subCatId
               
            },
             success: function(response) {


        $('#editabssubmission').html($(response).find('#editabssubmission').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initeditabssubmission();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });

    

        // Trigger the popup-btn functionality
     $('#editabssubmission').fadeIn(); // or your popup open function
    });
        /////////////subCategory  end////////////
           /////////////presentation  start////////////
    $(document).on('click', '.editabspresentationbtn', function() {
        var presentationId     = $(this).data('id');
        var category   = $(this).data('category');
        var presentation  = $(this).data('presentation');
        var status = $(this).data('status');
        var subCat1description  = $(this).data('description');


        $('#subCat1description').val(subCat1description);
        $('#presentationId').val(presentationId);
        $('#presentation').val(presentation);
        $('#categorySubId').val(category);
        if(status === 'A') {
            $('#presentation_status_active').prop('checked', true);
        } else {
            $('#presentation_status_inactive').prop('checked', true);
        }
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: {
                presentationId: presentationId
               
            },
             success: function(response) {


        $('#editabspresentation').html($(response).find('#editabspresentation').html());
            // Re-initialize after DOM replacement
            document.body.dataset.accmInit = "0"; // allow re-binding
            initeditabspresentation();
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });

    
        // Trigger the popup-btn functionality
     $('#editabspresentation').fadeIn(); // or your popup open function
    });
        /////////////presentation  end////////////
           /////////////field  start////////////
    $(document).on('click', '.editFieldBtn', function() {
        var fieldId     = $(this).data('id');
        var field   = $(this).data('field');
        var status = $(this).data('status');

         
        $('#fieldId').val(fieldId);
        $('#field').val(field);
        if(status === 'A') {
            $('#field_status_active').prop('checked', true);
        } else {
            $('#field_status_inactive').prop('checked', true);
        }

        // Trigger the popup-btn functionality
     $('#editabsfield').fadeIn(); // or your popup open function
    });
        /////////////field  end////////////
  /////////////Nomination  start////////////
  // Nomination edit button click
    $(document).on('click', '.editNominationBtn', function() {

        var id = $(this).data('id');       // Nomination ID
        var status = $(this).data('status'); // Active / Inactive

        // Set status radio
        if(status === 'Active') {
            $('#edit_Nomination_active').prop('checked', true);
        } else {
            $('#edit_Nomination_inactive').prop('checked', true);
        }

        // Send AJAX POST
        $.ajax({
            url: 'includes/popup.php',
            type: 'POST',
            data: { editNominationId: id }, // form-style POST
            success: function(response) {
                // Fill modal content
                $('#editNomination').html($(response).find('#editNomination').html());

                // Re-initialize if needed
                document.body.dataset.accmInit = "0";
                console.log(response);
                initEditNomination(); // your custom init function
            },
            error: function(xhr) {
                console.error('AJAX error', xhr.responseText);
            }
        });

        // Show modal
        $('#editNomination').fadeIn();
    });
</script>
</html>
<?php

function getSubcatName($subcat_id)
{
	global $cfg, $mycms;

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `abstract_submission` 
												   FROM "._DB_ABSTRACT_SUBMISSION_."
												 WHERE `id` = '".$subcat_id."' AND status='A'";
												 
	
									 
	$resultSelectUser          = $mycms->sql_select($sqlSelectUser);

	//echo '<pre>'; print_r($resultSelectUser);
	return $resultSelectUser[0]['abstract_submission'];


}

function getSubSubcatName($id)
{
	global $cfg, $mycms;

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `abstract_presentation` 
												   FROM "._DB_ABSTRACT_PRESENTATION_."
												 WHERE `id` = '".$id."' AND status='A'";
												 
	
									 
	$resultSelectUser          = $mycms->sql_select($sqlSelectUser);

	//echo '<pre>'; print_r($resultSelectUser);
	return $resultSelectUser[0]['abstract_presentation'];
}

function getCategoryName($id)
{
	global $cfg, $mycms;

	$sqlSelectUser				  			  = array();
	$sqlSelectUser['QUERY']         		  = "SELECT `category` 
												   FROM "._DB_ABSTRACT_TOPIC_CATEGORY_."
												 WHERE `id` = '".$id."' AND status='A'";
												 
	
									 
	$resultSelectUser          = $mycms->sql_select($sqlSelectUser);

	//echo '<pre>'; print_r($resultSelectUser);
	return $resultSelectUser[0]['category'];
}
?>
