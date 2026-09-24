<?php
/**
 * includes/get_page_meta.php
 *
 * Single shared endpoint. Given the current file, returns section name,
 * module name (+link), and page name as JSON.
 *
 * The site's link hierarchy can bottom out at ANY level:
 *   - a section can be a direct link  (section.is_page = yes, section.path = file)
 *   - a module can be a direct link   (module.is_page  = yes, module.path  = file)
 *   - or a real page can exist in _DB_WEBMASTER_PAGES_
 *
 * So we try, in order: page -> module -> section, and stop at the first match.
 */

include_once("init.php");

header('Content-Type: application/json');

$file = isset($_GET['file']) ? basename($_GET['file']) : '';

$response = array(
    'sectionName' => '',
    'moduleName'  => '',
    'moduleLink'  => '',
    'pageName'    => ''
);

if ($file !== '') {

    // 1) Try PAGE-level match: file belongs to a real page under a module/section
    $q = array();
    $q['QUERY'] = "SELECT
            s.sectionName,
            m.moduleName,
            m.path AS modulePath,
            m.is_page AS moduleIsPage,
            p.pageName
        FROM " . _DB_WEBMASTER_PAGES_ . " p
        INNER JOIN " . _DB_WEBMASTER_MODULE_ . " m ON p.moduleId = m.moduleId
        INNER JOIN " . _DB_WEBMASTER_SECTIONS_ . " s ON m.sectionId = s.sectionId
        WHERE p.fileName = ?
        LIMIT 1";
    $q['PARAM'][] = array('FILD' => 'fileName', 'DATA' => $file, 'TYP' => 's');
    $result = $mycms->sql_select($q);

    if (!empty($result)) {
        $row = $result[0];
        $response['sectionName'] = $row['sectionName'];
        $response['moduleName']  = $row['moduleName'];
        $response['pageName']    = $row['pageName'];

        if (!empty($row['moduleIsPage']) && $row['moduleIsPage'] == yes && !empty($row['modulePath'])) {
            $response['moduleLink'] = $row['modulePath'];
        }
    } else {

        // 2) Try MODULE-level match: file IS a module's direct link (no pages under it)
        $q = array();
        $q['QUERY'] = "SELECT
                s.sectionName,
                m.moduleName,
                m.path AS modulePath
            FROM " . _DB_WEBMASTER_MODULE_ . " m
            INNER JOIN " . _DB_WEBMASTER_SECTIONS_ . " s ON m.sectionId = s.sectionId
            WHERE m.is_page = ? AND m.path = ?
            LIMIT 1";
        $q['PARAM'][] = array('FILD' => 'is_page', 'DATA' => yes, 'TYP' => 's');
        $q['PARAM'][] = array('FILD' => 'path',    'DATA' => $file, 'TYP' => 's');
        $result = $mycms->sql_select($q);

        if (!empty($result)) {
            $row = $result[0];
            $response['sectionName'] = $row['sectionName'];
            $response['moduleName']  = $row['moduleName'];
            $response['moduleLink']  = $row['modulePath'];
            // no pageName - the module itself is the destination
        } else {

            // 3) Try SECTION-level match: file IS a section's direct link (no modules under it)
            $q = array();
            $q['QUERY'] = "SELECT sectionName
                FROM " . _DB_WEBMASTER_SECTIONS_ . "
                WHERE is_page = ? AND path = ?
                LIMIT 1";
            $q['PARAM'][] = array('FILD' => 'is_page', 'DATA' => yes, 'TYP' => 's');
            $q['PARAM'][] = array('FILD' => 'path',    'DATA' => $file, 'TYP' => 's');
            $result = $mycms->sql_select($q);

            if (!empty($result)) {
                $response['sectionName'] = $result[0]['sectionName'];
                // no moduleName/pageName - the section itself is the destination
            }
        }
    }
}

echo json_encode($response);