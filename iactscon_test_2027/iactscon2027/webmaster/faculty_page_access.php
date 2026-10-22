<?php
    include_once("./includes/source.php");
    include_once('./includes/init.php');
?>

<body>
    <?php include_once("./includes/left-menu.php"); ?>
    <header>
        <h2>Faculty Page Access</h2>
        <?php include_once("./includes/header_right.php"); ?>
    </header>
    <div class="body_wrap">
        <?php
        $show = $_REQUEST['show'] ?? '';
        ?>

        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Faculty Page Access</a></li>
                    </ol>
                </nav>
                <h2><?= ($show == 'edit') ? 'Update Faculty Details' : 'Page Access Section' ?></h2>
                <h6>Manage faculty module/page access permissions.</h6>
            </div>
        </div>

        <div class="com_info_wrap">
            <div class="com_info_right" style="width:100%;">
                <?php
                switch ($show) {
                    case 'edit':
                        facultyAccessRoleEditWindow($cfg, $mycms);
                        break;
                    default:
                        facultyAccessRoleListingWindow($cfg, $mycms);
                        break;
                }
                ?>
            </div>
        </div>
    </div>
    <?php include_once("./includes/app_popup.php"); ?>
</body>
<?php include_once("./includes/js-source.php"); ?>
<script language="javascript" src="scripts/faculty_page_access.js"></script>
</html>
<?php

/****************************************************************************************/
/*                          FACULTY ACCESS ROLE LISTING WINDOW                          */
/****************************************************************************************/
function facultyAccessRoleListingWindow($cfg, $mycms)
{
    ?>
    <form name="frmSearch" method="post">
        <div id="faculty_master">
            <div class="venue_hall_top">
                <h5>
                    <n><i class="fal fa-award"></i>Faculty & Keynote Speakers Master</n>
                    <l>Manage international & national faculty, session allocations, speaker badge colors & bulk CSV
                        import.</l>
                </h5>
                <div class="venue_hall_top_right">
                    <a class="badge_info"><i class="fal fa-file-import"></i>Bulk Import</a>
                    <a class="badge_default popup-btn" data-tab="appaddfaculty"><?php add() ?>Add Faculty</a>
                </div>
            </div>
            <div class="table_wrap app_faculty_table">
                <table>
                    <thead>
                        <tr>
                            <th>Faculty</th>
                            <th>Specialization & Hospital</th>
                            <th>Session Allocations</th>
                            <th>Badge Theme</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-start">
                                    <div class="regi_img_circle">
                                        <!-- <img src="" alt="" class="w-100 h-100"> -->
                                        <span>AM</span>
                                    </div>
                                    <div>
                                        <div class="regi_name">Dr. Asim Kumar Majumdar</div>
                                        <div class="regi_contact mt-0">
                                            <span>MD, FRCP (London), FCCP</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="regi_contact mt-0">
                                    <span>
                                        Interventional Pulmonology & Cryobiopsy
                                    </span>
                                    <span>
                                        Royal Brompton Hospital, London (United Kingdom)
                                    </span>
                                </div>
                            </td>
                            <td>
                                <ul class="service_ul">
                                    <li class="badge_padding badge_dark">Keynote 1: Future of Airway Interventions</li>
                                    <li class="badge_padding badge_dark">Workshop A: Cryobiopsy Masterclass</li>
                                </ul>
                            </td>

                            <td>
                                <small></small>
                                #3b82f6
                            </td>
                            <td>
                                <button><i class="fal fa-star"></i></button>
                            </td>
                            <td>
                                <div class="action_div">
                                    <a href="javascript:void(null)"
                                        class="icon_hover badge_danger br-5 w-auto action-transparent"><i
                                            class="fal fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <?php
}

/****************************************************************************************/
/*                            FACULTY ACCESS ROLE EDIT WINDOW                           */
/****************************************************************************************/
function facultyAccessRoleEditWindow($cfg, $mycms)
{
    $facultyId                 = addslashes(trim($_REQUEST['id']));

    $searchCondition           = " AND grandTAB.id != '1'";

    $sqlFacultyDetails['QUERY']         = "SELECT grandTAB.* 
                                  
                                  FROM (
                                  
                                            SELECT mainTAB.*,
                                                   REPLACE(CONCAT(facultyTitle, '. ', facultyFirstName, ' ', facultyMiddleName, ' ', facultyLastName), '  ', ' ') AS faculty_full_name
                                             
                                             FROM (
                                                    
                                                        SELECT faculty.*,
                                                               
                                                               IFNULL(faculty.faculty_title, '') AS facultyTitle,
                                                               IFNULL(faculty.faculty_first_name, '') AS facultyFirstName,
                                                               IFNULL(faculty.faculty_middle_name, '') AS facultyMiddleName,
                                                               IFNULL(faculty.faculty_last_name, '') AS facultyLastName,
                                                               
                                                               accessRole.faculty_role  
                                                          
                                                         FROM "._DB_FACULTY_ACCOUNT_." faculty 
                                                          
                                                   INNER JOIN "._DB_FACULTY_ACCESS_ROLE_." accessRole 
                                                           ON accessRole.id = faculty.faculty_access_role_id 
                                                        
                                                        WHERE faculty.status != 'D' 
                                                  
                                                  )    mainTAB 
                                      
                                       ) grandTAB  
                                
                                 WHERE 1 ".$filterCondition." ".$searchCondition."           
                              ORDER BY grandTAB.id DESC ";
    $resultFacultyDetails      = $mycms->sql_select($sqlFacultyDetails);
    $rowFacultyDetails         = $resultFacultyDetails[0];
    ?>
    <form name="frmUpdateFaculty" id="frmUpdateFaculty" action="faculty_page_access.process.php" method="post">
        <input type="hidden" name="act" value="update" />
        <input type="hidden" name="faculty_id" value="<?=$rowFacultyDetails['id']?>" />

        <div class="com_info_box active" id="editFacultyAccess">
            <div class="com_info_box_grid">
                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_success"><i class="fal fa-user-shield"></i></span> Faculty Details</n>
                        <div class="page_top_wrap_right">
                           <a href="<?= $_SERVER['PHP_SELF'] ?>" class="badge_dark"><i class="fal fa-arrow-left"></i> Back</a>
                            <button type="submit" name="submitWindowEdit" id="submitWindowEdit" class="badge_success"><i class="fal fa-save"></i> Update</button>
                        </div>
                    </h5>
                    <div class="com_info_box_inner">
                        <div class="form_grid g_3">
                            <div class="frm_grp">
                                <p class="frm-head">Username</p>
                                <p><?=$rowFacultyDetails['faculty_login_username']?></p>
                            </div>
                            <div class="frm_grp">
                                <p class="frm-head">Name</p>
                                <p><?=$rowFacultyDetails['faculty_full_name']?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="com_info_box_grid_box">
                    <h5 class="com_info_box_head">
                        <n><span class="text_primary"><i class="fal fa-key"></i></span> Access Priviledges</n>
                    </h5>
                    <div class="com_info_box_inner">
                        <?php
                        $webPageCounter          = 0;

                        $sqlFacultyAccessRole['QUERY']    = "SELECT DISTINCT m.moduleId AS moduleId, 
                                                                    m.moduleName AS moduleName, 
                                                                 
                                                                    s.sectionId AS sectionId, 
                                                                    s.sectionName AS sectionName, 
                                                                 
                                                                    p.pageId AS pageId, 
                                                                    p.pageName AS pageName,
                                                                    
                                                                    rd.web_page_id
                                                            
                                                               FROM "._DB_CONF_PAGE_." p
                                                      
                                                         INNER JOIN "._DB_CONF_MODULE_." m 
                                                                 ON m.moduleId = p.moduleId
                                                      
                                                         INNER JOIN "._DB_CONF_SECTION_." s 
                                                                 ON s.sectionId = m.sectionId 
                                                       
                                                         INNER JOIN "._DB_CONF_DOMAIN_." d 
                                                                 ON s.domainId = '2'    
                                                                AND d.tag = '".$cfg['DOMAIN_TAG']."'  
                                                        
                                                        LEFT OUTER JOIN "._DB_FACULTY_ACCESS_DETAILS_." rd 
                                                                 ON rd.web_page_id = p.pageId 
                                                                AND rd.status = 'A' 
                                                                AND rd.faculty_id = '".$rowFacultyDetails['id']."'        
                                                                
                                                           ORDER BY s.sectionId, m.seq, p.seq";

                        $resultFacultyAccessRole = $mycms->sql_select($sqlFacultyAccessRole);
                        foreach($resultFacultyAccessRole as $counter=>$rowFacultyAccessRole)
                        {
                            $webPageCounter++;

                            if($section != $rowFacultyAccessRole['sectionName'])
                            {
                                $section = $rowFacultyAccessRole['sectionName'];
                        ?>
                                <div class="form_grid g_1">
                                    <div class="frm_grp">
                                        <p class="frm-head">
                                            <input type="checkbox" name="checkAllWebPage" id="checkAllWebPage_<?=$webPageCounter?>"
                                             operationMode="checkAllWebPage" sectionId="<?=$rowFacultyAccessRole['sectionId']?>" />
                                            &nbsp;
                                            <?=$rowFacultyAccessRole['sectionName']?>
                                        </p>
                                    </div>
                                </div>
                        <?php
                            }
                            if($module!=$rowFacultyAccessRole['moduleName'])
                            {
                                $module = $rowFacultyAccessRole['moduleName'];
                        ?>
                                <div class="form_grid g_1">
                                    <div class="frm_grp" style="padding-left:15px;">
                                        <p class="frm-head text_primary"><?=$rowFacultyAccessRole['moduleName']?></p>
                                    </div>
                                </div>
                        <?php
                            }
                        ?>
                            <div class="form_grid g_1">
                                <div class="frm_grp" style="padding-left:30px;">
                                    <label>
                                        <input type="checkbox" name="web_page_id_edit[]" operationMode="webPage" sectionId="<?=$rowFacultyAccessRole['sectionId']?>"
                                         value="<?=$rowFacultyAccessRole['pageId']?>" <?=($rowFacultyAccessRole['pageId']==$rowFacultyAccessRole['web_page_id'])?'checked="checked"':''?> />
                                        &nbsp;&nbsp;&nbsp;
                                        <?=$rowFacultyAccessRole['pageName']?>
                                    </label>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
}
?>