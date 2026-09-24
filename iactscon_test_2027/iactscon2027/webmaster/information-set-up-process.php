<?php

include_once('includes/init.php');

// include_once("../includes/function.registration.php");
include_once('../../includes/function.delegate.php');
include_once('../../includes/function.invoice.php');
include_once('../../includes/function.workshop.php');
include_once('../../includes/function.dinner.php');
include_once('../../includes/function.accompany.php');
include_once('../../includes/function.accommodation.php');
include_once('../../includes/function.abstract.php');
include_once("includes/source.php"); 
// Get the full path to the current file's directory
$action = $_REQUEST['act'];
?>
<?php
$loggedUserID = $mycms->getLoggedUserId();

switch ($action) {
  
    case 'saveProfile':

    $speaker_ids     = $_REQUEST['speaker_id'] ?? []; // hidden field for each row, empty = new
    $speaker_names   = $_REQUEST['speaker_name'] ?? [];
    $designations    = $_REQUEST['designation'] ?? [];
    $workInfos       = $_REQUEST['workInfo'] ?? [];
    $tagLines        = $_REQUEST['tagLine'] ?? [];
    $halls        = $_REQUEST['hall'] ?? [];
    $confDateTimes        = $_REQUEST['confDateTime'] ?? [];
  
    if (!empty($speaker_names)) {
        foreach ($speaker_names as $key => $speakerName) {

            $id           = $speaker_ids[$key] ?? '';
            $designation  = $designations[$key] ?? '';
            $workInfo     = $workInfos[$key] ?? '';
            $tagLine      = $tagLines[$key] ?? '';
            $hall      = $halls[$key] ?? '';
            $confDateTime = !empty($_REQUEST['confDateTime'][$key]) ? $_REQUEST['confDateTime'][$key] : null;
            if ($confDateTime) {
                $confDateTime = str_replace('T', ' ', $confDateTime) . ':00'; // adds seconds
            }
            // IMAGE HANDLING
            $profileImage = '';

            if (!empty($_FILES['icon']['name'][$key])) {
                $iconName = $_FILES['icon']['name'][$key];
                $iconTmp  = $_FILES['icon']['tmp_name'][$key];

                $rand = 'HOTELICON_' . $key . '_' . date('ymdHis');
                $ext  = pathinfo($iconName, PATHINFO_EXTENSION);

                $profileImage = $rand . '.' . $ext;
                $path = '../../' . $cfg['EMAIL.HEADER.FOOTER.IMAGE'] . $profileImage;

                move_uploaded_file($iconTmp, $path);
            }

            // NEW RECORD
            if (empty($id)) {
                $insertFaculty = [];
                $insertFaculty['QUERY'] = "INSERT INTO `rcg_keynote_highlight_speaker`
                    SET  `speaker_name`     = ?,
                         `designation`      = ?,
                         `workInfo`         = ?,
                         `hall`             = ?,
                         `confDateTime`     = ?,
                         `tagLine`          = ?,
                         `profileImage`     = ?,
                         `status`           = ?,
                         `created_by`       = ?,
                         `created_ip`       = ?,
                         `created_sessionid`= ?,
                         `created_datetime` = ?";

                $insertFaculty['PARAM'][] = ['FILD' => 'speaker_name', 'DATA' => $speakerName, 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'designation', 'DATA' => $designation, 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'workInfo', 'DATA' => $workInfo, 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'hall', 'DATA' => $hall, 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'confDateTime', 'DATA' => $confDateTime, 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'tagLine', 'DATA' => $tagLine, 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'profileImage', 'DATA' => $profileImage, 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'status', 'DATA' => 'A', 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'created_by', 'DATA' => $loggedUserID, 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'created_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'created_sessionid', 'DATA' => session_id(), 'TYP' => 's'];
                $insertFaculty['PARAM'][] = ['FILD' => 'created_datetime', 'DATA' => date('Y-m-d H:i:s'), 'TYP' => 's'];

                $mycms->sql_insert($insertFaculty);

            } else {
                // UPDATE EXISTING RECORD
                $updateFaculty = [];
                $updateFaculty['QUERY'] = "UPDATE `rcg_keynote_highlight_speaker` SET
                    `speaker_name` = ?,
                    `designation`  = ?,
                    `workInfo`     = ?,
                    `hall`     = ?,
                    `confDateTime`     = ?,
                    `tagLine`      = ?" .
                    (!empty($profileImage) ? ", `profileImage` = ?" : "") .
                    " WHERE `id` = ?";

                $updateFaculty['PARAM'][] = ['FILD' => 'speaker_name', 'DATA' => $speakerName, 'TYP' => 's'];
                $updateFaculty['PARAM'][] = ['FILD' => 'designation', 'DATA' => $designation, 'TYP' => 's'];
                $updateFaculty['PARAM'][] = ['FILD' => 'workInfo', 'DATA' => $workInfo, 'TYP' => 's'];
                $updateFaculty['PARAM'][] = ['FILD' => 'hall', 'DATA' => $hall, 'TYP' => 's'];
                $updateFaculty['PARAM'][] = ['FILD' => 'confDateTime', 'DATA' => $confDateTime, 'TYP' => 's'];
                $updateFaculty['PARAM'][] = ['FILD' => 'tagLine', 'DATA' => $tagLine, 'TYP' => 's'];
                
                if (!empty($profileImage)) {
                    $updateFaculty['PARAM'][] = ['FILD' => 'profileImage', 'DATA' => $profileImage, 'TYP' => 's'];
                }

                $updateFaculty['PARAM'][] = ['FILD' => 'id', 'DATA' => $id, 'TYP' => 's'];

                $mycms->sql_update($updateFaculty);
            }
        }
    }
        $_SESSION['toaster'] = [ 
			'type' => 'success', // 'success' or 'error'
			'message' => 'Data updated successfully!' // dynamic message
		];


		echo '<script>window.location.href="information_setup.php#facultysetup";</script>';

break;
case 'deleteFaculty':
    $faculty_id = $_POST['id'] ?? '';
    $faculty_id = addslashes(trim($faculty_id));
    
    if ($faculty_id) {
        $deleteQuery = array();
        $deleteQuery['QUERY'] = "UPDATE `rcg_keynote_highlight_speaker` 
                                 SET `status` = 'D' 
                                 WHERE `id` = '".$faculty_id."'";
        // $deleteQuery['PARAM'][] = array('FILD' => 'id', 'DATA' => $faculty_id, 'TYP' => 's');

        // Run update
        $result = $mycms->sql_update($deleteQuery);

        if ($result) {
            echo 'success';
            exit; // important: stop outputting any HTML
        } else {
            error_log("Delete failed for ID: $faculty_id");
            echo 'error';
            exit;
        }
    } else {
        echo 'error';
        exit;
    }
break;
case 'toggleSpeakerStatus':
    $speaker_id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';
    $speaker_id = addslashes(trim($speaker_id));
    $status = ($status === 'A') ? 'A' : 'I';
 
    if ($speaker_id) {
        $updateQuery = array();
        $updateQuery['QUERY'] = "UPDATE `rcg_keynote_highlight_speaker` 
                                 SET `status` = '".$status."' 
                                 WHERE `id` = '".$speaker_id."'";
        $result = $mycms->sql_update($updateQuery);

        if ($result) {
            echo 'success';
            exit;
        } else {
            echo 'error';
            exit;
        }
    } else {
        echo 'error';
        exit;
    }
break;
case 'toggleMainStatus':
    $speaker_id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';
    $speaker_id = addslashes(trim($speaker_id));
    $status = ($status === 'A') ? 'A' : 'I';
 
    if ($speaker_id) {
        $updateQuery = array();
        $updateQuery['QUERY'] = "UPDATE ". _DB_COMPANY_INFORMATION_ . "
                                 SET `speakerStatus` = '".$status."' 
                                 WHERE `id` = '".$speaker_id."'";
        $result = $mycms->sql_update($updateQuery);

        if ($result) {
            echo 'success';
            exit;
        } else {
            echo 'error';
            exit;
        }
    } else {
        echo 'error';
        exit;
    }
break;
}
?>