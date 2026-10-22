<?php

include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();
$act = @$_REQUEST['act'];

switch ($act) {

    /* ============================================================
        STORE (ADD)
    ============================================================ */
    case 'store':
        try {

            date_default_timezone_set('Asia/Kolkata');

            // Sanitize & validate
            $type           = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : null;
            $name           = addslashes(trim($_REQUEST['name']));
            $designation    = addslashes(trim($_REQUEST['designation']));
            $country_id     = isset($_REQUEST['country_id']) ? (int)$_REQUEST['country_id'] : null;

              
    //         if ($type !== 'patrons' && $type !== 'core committee' && $type !== 'steering group' && $type !== 'central committee' && $type !== 'scientific committee' && $type !== 'local advisory board' && $type !== 'residents advisory board' &&  $type !== 'adult cardiac track' &&
    // $type !== 'congenital track' &&
    // $type !== 'mics/ robotics track' &&
    // $type !== 'transplant track' &&
    // $type !== 'aortic track' &&
    // $type !== 'thoracic and vascular track') {
    //             throw new Exception("Invalid committee type");
    //         }

            $allowedTypes = [
                'patrons',
                'core committee',
                'steering group',
                'central committee',
                'scientific committee',
                'national advisory board',
                'local advisory board',
                'residents advisory board',
                'adult cardiac track',
                'congenital track',
                'mics/ robotics track',
                'transplant track',
                'aortic track',
                'thoracic and vascular track',
            ];

            if (!in_array($type, $allowedTypes, true)) {
                throw new Exception("Invalid committee type");
            }
            

            if (empty($name)) {
                throw new Exception("Name is required");
            }

            // Image upload
            $final_media_url = '';

            if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                $uploadDir = __DIR__ . "/upload/committee/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $ext        = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array(strtolower($ext), $allowedExt)) {
                    throw new Exception("Invalid image format. Allowed: jpg, jpeg, png, gif, webp");
                }

                $newFilename = uniqid('committee_') . "." . $ext;
                $uploadPath  = $uploadDir . $newFilename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    throw new Exception("Image upload failed.");
                }

                $final_media_url = "upload/committee/" . $newFilename;
            }

            // DB Insert
            $sql = [];
            $sql['QUERY'] = "INSERT INTO `rcg_organising_committee`
                    SET type=?, name=?, image=?, designation=?, 
                        country_id=?, created_by=?";

            $sql['PARAM'] = [
                ['FILD' => 'type',         'DATA' => $type,            'TYP' => 's'],
                ['FILD' => 'name',         'DATA' => $name,            'TYP' => 's'],
                ['FILD' => 'image',        'DATA' => $final_media_url, 'TYP' => 's'],
                ['FILD' => 'designation',  'DATA' => $designation,     'TYP' => 's'],
                ['FILD' => 'country_id',   'DATA' => $country_id,      'TYP' => 'i'],
                ['FILD' => 'created_by',   'DATA' => $loggedUserID,    'TYP' => 'i']
            ];

            $mycms->sql_insert($sql);

            pageRedirection("organising_committee.php", 1);

        } catch (Exception $e) {
            pageRedirection("organising_committee.php?show=add", "Error: " . $e->getMessage());
        }
        exit();

    /* ============================================================
        EDIT
    ============================================================ */
    case 'edit':
        try {

            $id             = (int)$_POST['id'];
            $type           = trim($_POST['type']);
            $name           = addslashes(trim($_POST['name']));
            $designation    = addslashes(trim($_POST['designation']));
            $country_id     = isset($_POST['country_id']) ? (int)$_POST['country_id'] : null;

            // if ($type !== 'patrons' && $type !== 'core committee' && $type !== 'steering group') {
            //     throw new Exception("Invalid committee type");
            // }

            $allowedTypes = [
                'patrons',
                'core committee',
                'steering group',
                'central committee',
                'scientific committee',
                'national advisory board',
                'local advisory board',
                'residents advisory board',
                'adult cardiac track',
                'congenital track',
                'mics/ robotics track',
                'transplant track',
                'aortic track',
                'thoracic and vascular track',
            ];

            if (!in_array($type, $allowedTypes, true)) {
                throw new Exception("Invalid committee type");
            }

            $final_media_url = '';

            // If new image uploaded
            if (isset($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                $uploadDir = __DIR__ . "/upload/committee/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array(strtolower($ext), $allowedExt)) {
                    throw new Exception("Invalid image format.");
                }

                $newFilename = uniqid('committee_') . "." . $ext;
                $uploadPath  = $uploadDir . $newFilename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    throw new Exception("Image upload failed.");
                }

                $final_media_url = "upload/committee/" . $newFilename;

            } else {
                // Keep old image
                $final_media_url = $_POST['prev_image'] ?? null;
            }

            // Update
            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_organising_committee`
                        SET type=?, name=?, image=?, designation=?, 
                            country_id=?, updated_by=? 
                        WHERE id=? AND status!='D'";

            $sql['PARAM'] = [
                ['FILD' => 'type',         'DATA' => $type,            'TYP' => 's'],
                ['FILD' => 'name',         'DATA' => $name,            'TYP' => 's'],
                ['FILD' => 'image',        'DATA' => $final_media_url, 'TYP' => 's'],
                ['FILD' => 'designation',  'DATA' => $designation,     'TYP' => 's'],
                ['FILD' => 'country_id',   'DATA' => $country_id,      'TYP' => 'i'],
                ['FILD' => 'updated_by',   'DATA' => $loggedUserID,    'TYP' => 'i'],
                ['FILD' => 'id',           'DATA' => $id,              'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("organising_committee.php", 2);

        } catch (Exception $e) {
            pageRedirection("organising_committee.php", "Error: " . $e->getMessage());
        }
        exit();

    /* ============================================================
        REMOVE (Soft delete)
    ============================================================ */
    case 'Remove':
        try {

            $id = (int)$_GET['id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_organising_committee` 
                         SET status='D', updated_by=?, updated_at=NOW()
                         WHERE id=?";

            $sql['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("organising_committee.php", 3);

        } catch (Exception $e) {
            pageRedirection("organising_committee.php", "Error: " . $e->getMessage());
        }
        exit();

    /* ============================================================
        ACTIVE
    ============================================================ */
    case 'Active':
        try {
            $id = (int)$_REQUEST['id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_organising_committee`
                             SET status='A', updated_by=?, updated_at=NOW()
                             WHERE id=?";

            $sql['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("organising_committee.php", 2);

        } catch (Exception $e) {
            pageRedirection("organising_committee.php", "Error: " . $e->getMessage());
        }
        exit();

    /* ============================================================
        INACTIVE
    ============================================================ */
    case 'Inactive':
        try {
            $id = (int)$_REQUEST['id'];

            $sql = [];
            $sql['QUERY'] = "UPDATE `rcg_organising_committee`
                             SET status='I', updated_by=?, updated_at=NOW()
                             WHERE id=?";

            $sql['PARAM'] = [
                ['FILD' => 'updated_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i']
            ];

            $mycms->sql_update($sql);

            pageRedirection("organising_committee.php", 2);

        } catch (Exception $e) {
            pageRedirection("organising_committee.php", "Error: " . $e->getMessage());
        }
        exit();

    /* ============================================================
        MULTI ACTION
    ============================================================ */
    case 'MultiAction':
        try {

            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception("No items selected.");
            }

            $ids    = array_map('intval', $_POST['ids']);
            $action = $_POST['action'];

            foreach ($ids as $id) {

                $sql = [];

                if ($action === 'Active') {
                    $sql['QUERY'] = "
                        UPDATE `rcg_organising_committee`
                        SET status='A', updated_by=?, updated_at=NOW()
                        WHERE id=?";
                    $sql['PARAM'] = [
                        ['FILD' => 'updated_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                        ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i']
                    ];
                }

                elseif ($action === 'Inactive') {
                    $sql['QUERY'] = "
                        UPDATE `rcg_organising_committee`
                        SET status='I', updated_by=?, updated_at=NOW()
                        WHERE id=?";
                    $sql['PARAM'] = [
                        ['FILD' => 'updated_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                        ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i']
                    ];
                }

                elseif ($action === 'Remove') {
                    $sql['QUERY'] = "
                        UPDATE `rcg_organising_committee`
                        SET status='D', updated_by=?, updated_at=NOW()
                        WHERE id=?";
                    $sql['PARAM'] = [
                        ['FILD' => 'updated_by', 'DATA' => $loggedUserID, 'TYP' => 'i'],
                        ['FILD' => 'id',         'DATA' => $id,           'TYP' => 'i']
                    ];
                }

                $mycms->sql_update($sql);
            }

            // final redirect
            if ($action === 'Remove') {
                pageRedirection("organising_committee.php", 3);
            } else {
                pageRedirection("organising_committee.php", 2);
            }

        } catch (Exception $e) {
            pageRedirection("organising_committee.php", "Error: " . $e->getMessage());
        }
        exit();
}


/* ============================================================
    REDIRECT FUNCTION
============================================================ */
function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms;

    $pageKey = "_pgn_";
    $pageKeyVal = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

    $searchString = "&" . $pageKey . "=" . $pageKeyVal;

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}
