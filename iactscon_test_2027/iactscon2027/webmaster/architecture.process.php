<?php
include_once('includes/init.php');
$act = @$_REQUEST['act'];

switch ($act) {
    case 'insert':
        insertArchitecture($cfg, $mycms);
        exit();
	break;
     case 'update':
        updateArchitecture($cfg, $mycms);
        exit();
	break;
}
function insertArchitecture($cfg, $mycms)
{
    $loggedUserID = 'system'; // Replace with actual logged-in user
    $data = $_POST['sections'] ?? [];
    //  echo "<pre>";
    //  print_r($_POST);
    //  die();
   
    foreach ($data as $section) {
        $secCode     = $section['secCode'] ?? '';
        $sectionName = $section['sectionName'] ?? '';
        $fileName    = $section['fileName'] ?? '';
        $seq         = $section['seq'] ?? 0;
        $is_page     = $section['is_page'] ?? 'no'; 
        $is_tab      = $section['is_tab'] ?? 'no';
        $path        = $section['paths'] ?? '';
        $pageInfo        = $section['pageInfo'] ?? '';
        // Only insert section if sectionName is not empty
        if (!empty(trim($sectionName))) {
            $sql = array();
            $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SECTIONS_." 
                            (domainId, secCode, sectionName, seq, path, img, is_page, is_tab, modifiedBy,pageInfo) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sql['PARAM'][] = array('FILD'=>'domainId', 'DATA'=>1, 'TYP'=>'i');
            $sql['PARAM'][] = array('FILD'=>'secCode', 'DATA'=>$secCode, 'TYP'=>'s');
            $sql['PARAM'][] = array('FILD'=>'sectionName', 'DATA'=>$sectionName, 'TYP'=>'s');
            $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$seq, 'TYP'=>'i');
            $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$path, 'TYP'=>'s');
            $sql['PARAM'][] = array('FILD'=>'img', 'DATA'=>$fileName, 'TYP'=>'s');
            $sql['PARAM'][] = array('FILD'=>'is_page', 'DATA'=>$is_page, 'TYP'=>'s');
            $sql['PARAM'][] = array('FILD'=>'is_tab', 'DATA'=>$is_tab, 'TYP'=>'s');
            $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
            $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$pageInfo, 'TYP'=>'s');

            $sectionId = $mycms->sql_insert($sql);

            // ---------------- MODULES ----------------
            foreach ($section['modules'] ?? [] as $module) {
                $moduleName = $module['name'] ?? '';
                if (empty(trim($moduleName))) continue; // skip empty names

                $moduleSeq  = $module['sequence'] ?? 0;
                $module_is_page = $module['is_page'] ?? 'no';
                $module_is_tab  = $module['is_tab'] ?? 'no';
                $module_path    = $module['paths'] ?? '';
                $modulepageInfo = $module['pageInfo'] ?? '';
                $sql = array();
                $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_MODULE_." 
                                (sectionId, moduleName, seq, is_page, is_tab, path, modifiedBy,pageInfo) 
                                VALUES (?, ?, ?, ?, ?, ?, ?,?)";
                $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                $sql['PARAM'][] = array('FILD'=>'moduleName', 'DATA'=>$moduleName, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$moduleSeq, 'TYP'=>'i');
                $sql['PARAM'][] = array('FILD'=>'is_page', 'DATA'=>$module_is_page, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'is_tab', 'DATA'=>$module_is_tab, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$module_path, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$modulepageInfo, 'TYP'=>'s');

                $moduleId = $mycms->sql_insert($sql);

                // ---------------- PAGES ----------------
                foreach ($module['pages'] ?? [] as $page) {
                    $pageName = $page['name'] ?? '';
                    if (empty(trim($pageName))) continue; // skip empty pages

                    $fileName = $page['fileName'] ?? '';
                    $seqPage  = $page['Sequence'] ?? 0;
                    $page_is_tab = $page['is_tab'] ?? 'no';
                    $isSubpage = !empty($page['subpages']) ? 'yes' : 'no';
                     $pagepageInfo = $page['pageInfo'] ?? '';

                    $sql = array();
                    $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_PAGES_." 
                                    (sectionId, moduleId, pageName, fileName, seq, is_tab, is_subPage, modifiedBy,pageInfo) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?,?,?)";
                    $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'pageName', 'DATA'=>$pageName, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'fileName', 'DATA'=>$fileName, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$seqPage, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'is_tab', 'DATA'=>$page_is_tab, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'is_subPage', 'DATA'=>$isSubpage, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$pagepageInfo, 'TYP'=>'s');

                    $pageId = $mycms->sql_insert($sql);

                    // ---------------- SUBPAGES ----------------
                    foreach ($page['subpages'] ?? [] as $subpage) {
                        $subpageName = $subpage['name'] ?? '';
                        if (empty(trim($subpageName))) continue; // skip empty subpages

                        $subfileName = $subpage['fileName'] ?? '';
                        $seqSub      = $subpage['Sequence'] ?? 0;
                        $subpagepageInfo = $subpage['pageInfo'] ?? '';

                        $sql = array();
                        $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SUBPAGE_." 
                                        (sectionId, moduleId, pageId, subpageName, fileName, seq, modifiedBy,pageInfo) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?,?)";
                        $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'pageId', 'DATA'=>$pageId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'subpageName', 'DATA'=>$subpageName, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'fileName', 'DATA'=>$subfileName, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$seqSub, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$subpagepageInfo, 'TYP'=>'s');

                        $subpageId = $mycms->sql_insert($sql);
                    }

                    // ---------------- TABS ----------------
                    foreach ($page['tabs'] ?? [] as $tab) {
                        $tabName = $tab['name'] ?? '';
                        if (empty(trim($tabName))) continue; // skip empty tabs
                        $isSubtab = !empty($tab['subtabs']) ? 'yes' : 'no';
                        $tabseq = $tab['sequence'] ?? '';
                        $tabpath = $tab['path'] ?? '';
                        $tabpageInfo = $tab['pageInfo'] ?? '';

                        $sql = array();
                        $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_TAB_." 
                                        (sectionId, moduleId, pageId, tabName, is_subtab,seq,path, modifiedBy,pageInfo) 
                                        VALUES (?, ?, ?, ?, ?, ?,?,?,?)";
                        $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'pageId', 'DATA'=>$pageId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'tabName', 'DATA'=>$tabName, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'is_subtab', 'DATA'=>$isSubtab, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$tabseq, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$tabpath, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$tabpageInfo, 'TYP'=>'s');

                        $tabId = $mycms->sql_insert($sql);

                        // ---------------- SUBTABS ----------------
                        foreach ($tab['subtabs'] ?? [] as $subtab) {
                            $subtabName = $subtab['name'] ?? '';
                            if (empty(trim($subtabName))) continue; // skip empty subtabs
                            $subtabseq = $subtab['sequence'] ?? '';
                            $subtabpath = $subtab['path'] ?? '';
                            $subtabpageInfo = $subtab['pageInfo'] ?? '';

                            $sql = array();
                            $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SUBTAB_." 
                                            (tabId, sectionId, moduleId, pageId, subtabName,seq,path, modifiedBy,pageInfo) 
                                            VALUES (?, ?, ?, ?, ?, ?,?,?,?)";
                            $sql['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                            $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                            $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                            $sql['PARAM'][] = array('FILD'=>'pageId', 'DATA'=>$pageId, 'TYP'=>'i');
                            $sql['PARAM'][] = array('FILD'=>'subtabName', 'DATA'=>$subtabName, 'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$subtabseq, 'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$subtabpath, 'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$subtabpageInfo, 'TYP'=>'s');

                            $subtabId = $mycms->sql_insert($sql);
                        }
                    }
                }
                    // ---------------- Module TABS ----------------
                foreach ($module['tabs'] ?? [] as $tab) {
                    $tabName = $tab['name'] ?? '';
                    $tabseq = $tab['sequence'] ?? '';
                    $tabpath = $tab['path'] ?? '';
                    if (empty(trim($tabName))) continue; // skip empty section tabs
                    $isSubtab = !empty($tab['subtabs']) ? 'yes' : 'no';
                    $tabpageInfo = $tab['pageInfo'] ?? '';

                    $sql = array();
                    $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_TAB_." 
                                    (sectionId,moduleId, tabName, is_subtab,seq,path, modifiedBy,pageInfo) 
                                    VALUES (?, ?, ?, ?,?,?,?,?)";
                    $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'tabName', 'DATA'=>$tabName, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'is_subtab', 'DATA'=>$isSubtab, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$tabseq, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$tabpath, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$tabpageInfo, 'TYP'=>'s');

                    $tabId = $mycms->sql_insert($sql);

                    foreach ($tab['subtabs'] ?? [] as $subtab) {
                        $subtabName = $subtab['name'] ?? '';
                        if (empty(trim($subtabName))) continue; // skip empty subtabs
                        $subtabseq = $subtab['sequence'] ?? '';
                        $subtabpath = $subtab['path'] ?? '';
                        $subtabpageInfo = $subtab['pageInfo'] ?? '';

                        $sql = array();
                        $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SUBTAB_." 
                                        (tabId, sectionId,moduleId, subtabName,seq,path, modifiedBy,pageInfo) 
                                        VALUES (?, ?,?, ?, ?,?,?,?)";
                        $sql['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'subtabName', 'DATA'=>$subtabName, 'TYP'=>'s');
                         $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$subtabseq, 'TYP'=>'s');
                         $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$subtabpath, 'TYP'=>'s');
                         $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                         $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$subtabpageInfo, 'TYP'=>'s');

                        $subtabId = $mycms->sql_insert($sql);
                    }
                }
            }

            // ---------------- SECTION TABS ----------------
            foreach ($section['tabs'] ?? [] as $tab) {
                $tabName = $tab['name'] ?? '';
                $tabseq = $tab['sequence'] ?? '';
                $tabpath = $tab['path'] ?? '';
                $tabpageInfo = $tab['pageInfo'] ?? '';

                if (empty(trim($tabName))) continue; // skip empty section tabs
                $isSubtab = !empty($tab['subtabs']) ? 'yes' : 'no';
                $sql = array();
                $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_TAB_." 
                                (sectionId, tabName, is_subtab,seq,path, modifiedBy,pageInfo) 
                                VALUES (?, ?, ?, ?,?,?,?)";
                $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                $sql['PARAM'][] = array('FILD'=>'tabName', 'DATA'=>$tabName, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'is_subtab', 'DATA'=>$isSubtab, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$tabseq, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$tabpath, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$tabpageInfo, 'TYP'=>'s');

                $tabId = $mycms->sql_insert($sql);

                foreach ($tab['subtabs'] ?? [] as $subtab) {
                    $subtabName = $subtab['name'] ?? '';
                    if (empty(trim($subtabName))) continue; // skip empty subtabs
                    $subtabseq = $subtab['sequence'] ?? '';
                    $subtabpath = $subtab['path'] ?? '';
                    $subtabpageInfo = $subtab['pageInfo'] ?? '';

                    $sql = array();
                    $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SUBTAB_." 
                                    (tabId, sectionId, subtabName,seq,path, modifiedBy,pageInfo) 
                                    VALUES (?, ?, ?, ?,?,?,?)";
                    $sql['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'subtabName', 'DATA'=>$subtabName, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$subtabseq, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$subtabpath, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$subtabpageInfo, 'TYP'=>'s');

                    $subtabId = $mycms->sql_insert($sql);
                }
            }
        }
    }
    $_SESSION['toaster'] = [
        'type' => 'success', // 'success' or 'error'
        'message' => 'Data Inserted successfully!' // dynamic message
    ];
    $mycms->redirect("architecture.php");
}


function updateArchitecture($cfg, $mycms)
{

    $loggedUserID = 'system';
    $data = $_POST['sections'] ?? [];

    //   echo "<pre>";
    //  print_r($data);
    //  die();
   
    foreach ($data as $section) {

        $existingSectionId = $section['sectionId'] ?? '';

        // ✅ ================= SOFT DELETE BLOCK =================
        if (!empty($existingSectionId)) {

            // SECTION
            $mycms->sql_update([
                "QUERY" => "UPDATE "._DB_WEBMASTER_SECTIONS_." SET status='D' WHERE sectionId=?",
                "PARAM" => [
                    ['FILD'=>'sectionId','DATA'=>$existingSectionId,'TYP'=>'i']
                ]
            ]);

            // MODULES
            $mycms->sql_update([
                "QUERY" => "UPDATE "._DB_WEBMASTER_MODULE_." SET status='D' WHERE sectionId=?",
                "PARAM" => [
                    ['FILD'=>'sectionId','DATA'=>$existingSectionId,'TYP'=>'i']
                ]
            ]);

            // PAGES
            $mycms->sql_update([
                "QUERY" => "UPDATE "._DB_WEBMASTER_PAGES_." SET status='D' WHERE sectionId=?",
                "PARAM" => [
                    ['FILD'=>'sectionId','DATA'=>$existingSectionId,'TYP'=>'i']
                ]
            ]);

            // SUBPAGES
            $mycms->sql_update([
                "QUERY" => "UPDATE "._DB_WEBMASTER_SUBPAGE_." SET status='D' WHERE sectionId=?",
                "PARAM" => [
                    ['FILD'=>'sectionId','DATA'=>$existingSectionId,'TYP'=>'i']
                ]
            ]);

            // TABS
            $mycms->sql_update([
                "QUERY" => "UPDATE "._DB_WEBMASTER_TAB_." SET status='D' WHERE sectionId=?",
                "PARAM" => [
                    ['FILD'=>'sectionId','DATA'=>$existingSectionId,'TYP'=>'i']
                ]
            ]);

            // SUBTABS
            $mycms->sql_update([
                "QUERY" => "UPDATE "._DB_WEBMASTER_SUBTAB_." SET status='D' WHERE sectionId=?",
                "PARAM" => [
                    ['FILD'=>'sectionId','DATA'=>$existingSectionId,'TYP'=>'i']
                ]
            ]);
        }
        // ✅ =====================================================


        $secCode     = $section['secCode'] ?? '';
        $sectionName = $section['sectionName'] ?? '';
        $fileName    = $section['fileName'] ?? '';
        $seq         = $section['seq'] ?? 0;
        $is_page     = $section['is_page'] ?? 'no'; 
        $is_tab      = $section['is_tab'] ?? 'no';
        $path        = $section['paths'] ?? '';
        $pageInfo    = $section['pageInfo'] ?? '';

        if (!empty(trim($sectionName))) {

            $sql = array();
            $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SECTIONS_." 
                            (domainId, secCode, sectionName, seq, path, img, is_page, is_tab, modifiedBy,pageInfo) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $sql['PARAM'][] = ['FILD'=>'domainId','DATA'=>1,'TYP'=>'i'];
            $sql['PARAM'][] = ['FILD'=>'secCode','DATA'=>$secCode,'TYP'=>'s'];
            $sql['PARAM'][] = ['FILD'=>'sectionName','DATA'=>$sectionName,'TYP'=>'s'];
            $sql['PARAM'][] = ['FILD'=>'seq','DATA'=>$seq,'TYP'=>'i'];
            $sql['PARAM'][] = ['FILD'=>'path','DATA'=>$path,'TYP'=>'s'];
            $sql['PARAM'][] = ['FILD'=>'img','DATA'=>$fileName,'TYP'=>'s'];
            $sql['PARAM'][] = ['FILD'=>'is_page','DATA'=>$is_page,'TYP'=>'s'];
            $sql['PARAM'][] = ['FILD'=>'is_tab','DATA'=>$is_tab,'TYP'=>'s'];
            $sql['PARAM'][] = ['FILD'=>'modifiedBy','DATA'=>$loggedUserID,'TYP'=>'s'];
            $sql['PARAM'][] = ['FILD'=>'pageInfo','DATA'=>$pageInfo,'TYP'=>'s'];

            $sectionId = $mycms->sql_insert($sql);

            // 🔥 EVERYTHING BELOW REMAINS EXACT SAME (UNCHANGED)

            foreach ($section['modules'] ?? [] as $module) {

                $moduleName = $module['name'] ?? '';
                if (empty(trim($moduleName))) continue;

                $moduleSeq  = $module['sequence'] ?? 0;
                $module_is_page = $module['is_page'] ?? 'no';
                $module_is_tab  = $module['is_tab'] ?? 'no';
                $module_path    = $module['paths'] ?? '';
                $modulepageInfo = $module['pageInfo'] ?? '';

                $sql = array();
                $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_MODULE_." 
                                (sectionId, moduleName, seq, is_page, is_tab, path, modifiedBy,pageInfo) 
                                VALUES (?, ?, ?, ?, ?, ?, ?,?)";

                $sql['PARAM'][] = ['FILD'=>'sectionId','DATA'=>$sectionId,'TYP'=>'i'];
                $sql['PARAM'][] = ['FILD'=>'moduleName','DATA'=>$moduleName,'TYP'=>'s'];
                $sql['PARAM'][] = ['FILD'=>'seq','DATA'=>$moduleSeq,'TYP'=>'i'];
                $sql['PARAM'][] = ['FILD'=>'is_page','DATA'=>$module_is_page,'TYP'=>'s'];
                $sql['PARAM'][] = ['FILD'=>'is_tab','DATA'=>$module_is_tab,'TYP'=>'s'];
                $sql['PARAM'][] = ['FILD'=>'path','DATA'=>$module_path,'TYP'=>'s'];
                $sql['PARAM'][] = ['FILD'=>'modifiedBy','DATA'=>$loggedUserID,'TYP'=>'s'];
                $sql['PARAM'][] = ['FILD'=>'pageInfo','DATA'=>$modulepageInfo,'TYP'=>'s'];

                $moduleId = $mycms->sql_insert($sql);

                         // ---------------- PAGES ----------------
                foreach ($module['pages'] ?? [] as $page) {
                    $pageName = $page['name'] ?? '';
                    if (empty(trim($pageName))) continue; // skip empty pages

                    $fileName = $page['fileName'] ?? '';
                    $seqPage  = $page['Sequence'] ?? 0;
                    $page_is_tab = $page['is_tab'] ?? 'no';
                    $isSubpage = !empty($page['subpages']) ? 'yes' : 'no';
                     $pagepageInfo = $page['pageInfo'] ?? '';

                    $sql = array();
                    $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_PAGES_." 
                                    (sectionId, moduleId, pageName, fileName, seq, is_tab, is_subPage, modifiedBy,pageInfo) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?,?,?)";
                    $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'pageName', 'DATA'=>$pageName, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'fileName', 'DATA'=>$fileName, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$seqPage, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'is_tab', 'DATA'=>$page_is_tab, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'is_subPage', 'DATA'=>$isSubpage, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$pagepageInfo, 'TYP'=>'s');

                    $pageId = $mycms->sql_insert($sql);

                    // ---------------- SUBPAGES ----------------
                    foreach ($page['subpages'] ?? [] as $subpage) {
                        $subpageName = $subpage['name'] ?? '';
                        if (empty(trim($subpageName))) continue; // skip empty subpages

                        $subfileName = $subpage['fileName'] ?? '';
                        $seqSub      = $subpage['Sequence'] ?? 0;
                        $subpagepageInfo = $subpage['pageInfo'] ?? '';

                        $sql = array();
                        $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SUBPAGE_." 
                                        (sectionId, moduleId, pageId, subpageName, fileName, seq, modifiedBy,pageInfo) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?,?)";
                        $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'pageId', 'DATA'=>$pageId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'subpageName', 'DATA'=>$subpageName, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'fileName', 'DATA'=>$subfileName, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$seqSub, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$subpagepageInfo, 'TYP'=>'s');

                        $subpageId = $mycms->sql_insert($sql);
                    }

                    // ---------------- TABS ----------------
                    foreach ($page['tabs'] ?? [] as $tab) {
                        $tabName = $tab['name'] ?? '';
                        if (empty(trim($tabName))) continue; // skip empty tabs
                        $isSubtab = !empty($tab['subtabs']) ? 'yes' : 'no';
                        $tabseq = $tab['sequence'] ?? '';
                        $tabpath = $tab['path'] ?? '';
                        $tabpageInfo = $tab['pageInfo'] ?? '';

                        $sql = array();
                        $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_TAB_." 
                                        (sectionId, moduleId, pageId, tabName, is_subtab,seq,path, modifiedBy,pageInfo) 
                                        VALUES (?, ?, ?, ?, ?, ?,?,?,?)";
                        $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'pageId', 'DATA'=>$pageId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'tabName', 'DATA'=>$tabName, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'is_subtab', 'DATA'=>$isSubtab, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$tabseq, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$tabpath, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                        $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$tabpageInfo, 'TYP'=>'s');

                        $tabId = $mycms->sql_insert($sql);

                        // ---------------- SUBTABS ----------------
                        foreach ($tab['subtabs'] ?? [] as $subtab) {
                            $subtabName = $subtab['name'] ?? '';
                            if (empty(trim($subtabName))) continue; // skip empty subtabs
                            $subtabseq = $subtab['sequence'] ?? '';
                            $subtabpath = $subtab['path'] ?? '';
                            $subtabpageInfo = $subtab['pageInfo'] ?? '';

                            $sql = array();
                            $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SUBTAB_." 
                                            (tabId, sectionId, moduleId, pageId, subtabName,seq,path, modifiedBy,pageInfo) 
                                            VALUES (?, ?, ?, ?, ?, ?,?,?,?)";
                            $sql['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                            $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                            $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                            $sql['PARAM'][] = array('FILD'=>'pageId', 'DATA'=>$pageId, 'TYP'=>'i');
                            $sql['PARAM'][] = array('FILD'=>'subtabName', 'DATA'=>$subtabName, 'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$subtabseq, 'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$subtabpath, 'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                            $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$subtabpageInfo, 'TYP'=>'s');

                            $subtabId = $mycms->sql_insert($sql);
                        }
                    }
                }
                    // ---------------- Module TABS ----------------
                foreach ($module['tabs'] ?? [] as $tab) {
                    $tabName = $tab['name'] ?? '';
                    $tabseq = $tab['sequence'] ?? '';
                    $tabpath = $tab['path'] ?? '';
                    if (empty(trim($tabName))) continue; // skip empty section tabs
                    $isSubtab = !empty($tab['subtabs']) ? 'yes' : 'no';
                    $tabpageInfo = $tab['pageInfo'] ?? '';

                    $sql = array();
                    $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_TAB_." 
                                    (sectionId,moduleId, tabName, is_subtab,seq,path, modifiedBy,pageInfo) 
                                    VALUES (?, ?, ?, ?,?,?,?,?)";
                    $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'tabName', 'DATA'=>$tabName, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'is_subtab', 'DATA'=>$isSubtab, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$tabseq, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$tabpath, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$tabpageInfo, 'TYP'=>'s');

                    $tabId = $mycms->sql_insert($sql);

                    foreach ($tab['subtabs'] ?? [] as $subtab) {
                        $subtabName = $subtab['name'] ?? '';
                        if (empty(trim($subtabName))) continue; // skip empty subtabs
                        $subtabseq = $subtab['sequence'] ?? '';
                        $subtabpath = $subtab['path'] ?? '';
                        $subtabpageInfo = $subtab['pageInfo'] ?? '';

                        $sql = array();
                        $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SUBTAB_." 
                                        (tabId, sectionId,moduleId, subtabName,seq,path, modifiedBy,pageInfo) 
                                        VALUES (?, ?,?, ?, ?,?,?,?)";
                        $sql['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'moduleId', 'DATA'=>$moduleId, 'TYP'=>'i');
                        $sql['PARAM'][] = array('FILD'=>'subtabName', 'DATA'=>$subtabName, 'TYP'=>'s');
                         $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$subtabseq, 'TYP'=>'s');
                         $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$subtabpath, 'TYP'=>'s');
                         $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                         $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$subtabpageInfo, 'TYP'=>'s');

                        $subtabId = $mycms->sql_insert($sql);
                    }
                }
            }

            // ---------------- SECTION TABS ----------------
            foreach ($section['tabs'] ?? [] as $tab) {
                $tabName = $tab['name'] ?? '';
                $tabseq = $tab['sequence'] ?? '';
                $tabpath = $tab['path'] ?? '';
                $tabpageInfo = $tab['pageInfo'] ?? '';

                if (empty(trim($tabName))) continue; // skip empty section tabs
                $isSubtab = !empty($tab['subtabs']) ? 'yes' : 'no';
                $sql = array();
                $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_TAB_." 
                                (sectionId, tabName, is_subtab,seq,path, modifiedBy,pageInfo) 
                                VALUES (?, ?, ?, ?,?,?,?)";
                $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                $sql['PARAM'][] = array('FILD'=>'tabName', 'DATA'=>$tabName, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'is_subtab', 'DATA'=>$isSubtab, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$tabseq, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$tabpath, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$tabpageInfo, 'TYP'=>'s');

                $tabId = $mycms->sql_insert($sql);

                foreach ($tab['subtabs'] ?? [] as $subtab) {
                    $subtabName = $subtab['name'] ?? '';
                    if (empty(trim($subtabName))) continue; // skip empty subtabs
                    $subtabseq = $subtab['sequence'] ?? '';
                    $subtabpath = $subtab['path'] ?? '';
                    $subtabpageInfo = $subtab['pageInfo'] ?? '';

                    $sql = array();
                    $sql['QUERY'] = "INSERT INTO "._DB_WEBMASTER_SUBTAB_." 
                                    (tabId, sectionId, subtabName,seq,path, modifiedBy,pageInfo) 
                                    VALUES (?, ?, ?, ?,?,?,?)";
                    $sql['PARAM'][] = array('FILD'=>'tabId', 'DATA'=>$tabId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'sectionId', 'DATA'=>$sectionId, 'TYP'=>'i');
                    $sql['PARAM'][] = array('FILD'=>'subtabName', 'DATA'=>$subtabName, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'seq', 'DATA'=>$subtabseq, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'path', 'DATA'=>$subtabpath, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'modifiedBy', 'DATA'=>$loggedUserID, 'TYP'=>'s');
                    $sql['PARAM'][] = array('FILD'=>'pageInfo', 'DATA'=>$subtabpageInfo, 'TYP'=>'s');

                    $subtabId = $mycms->sql_insert($sql);
                }
            }
        }
    }

    $_SESSION['toaster'] = [
        'type' => 'success',
        'message' => 'Data Updated successfully!'
    ];

    $mycms->redirect("architecture.php");
}