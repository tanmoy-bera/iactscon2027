<?php
include_once('includes/init.php');
$loggedUserID = $mycms->getLoggedUserId();

switch ($_REQUEST['act']) {

    /***************** STEP 1 *****************/
    case 'upload_csv':
        // echo "<pre>"; print_r($_REQUEST);die;
        if ($_FILES['csv_file']['name']) {
            $dbName = addslashes(str_replace(" ", "_", $_REQUEST['database_name']));
            $tableName = "circulation_" . $dbName . date("Ymd_His");

            // Create table with static structure
            $sqlCreate = "CREATE TABLE IF NOT EXISTS `$tableName` ( 
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200),
            email VARCHAR(200),
            phone VARCHAR(100),
            status VARCHAR(10) DEFAULT 'I',
            isBlackListed VARCHAR(10) DEFAULT 'N'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $mycms->sql_query($sqlCreate);

            // Insert CSV data
            if (($handle = fopen($_FILES['csv_file']['tmp_name'], "r")) !== FALSE) {
                $row = 0;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;
                    if ($row == 1) continue;

                    $delegate_name = $data[0] == '' ? null : $data[0];

                    $sqlInsert['QUERY'] = "INSERT INTO `$tableName` (name, email, phone, status, isBlackListed ) VALUES (?, ?, ?, ?, ?)";

                    $sqlInsert['PARAM'] = [];
                    $sqlInsert['PARAM'][] = array('FILD' => 'name', 'DATA' => $delegate_name, 'TYP' => 's');
                    $sqlInsert['PARAM'][] = array('FILD' => 'email', 'DATA' => $data[1], 'TYP' => 's');
                    $sqlInsert['PARAM'][] = array('FILD' => 'phone', 'DATA' => null, 'TYP' => 's');
                    $sqlInsert['PARAM'][] = array('FILD' => 'status', 'DATA' => 'I', 'TYP' => 's');
                    $sqlInsert['PARAM'][] = array('FILD' => 'isBlackListed', 'DATA' => 'N', 'TYP' => 's');
                    $mycms->sql_insert($sqlInsert);
                }
                fclose($handle);
            }
        } else {
            // Use existing database
            $tableName = $_REQUEST['existing_db'];
        }
        // Insert into mailer_structure
        $sqlMailer['QUERY'] = "INSERT INTO `rcg_mailer_circulation_details` 
                                SET data_table = ?, 
                                    sender_email = ?, 
                                    sender_name = ?, 
                                    reply_to = ?, 
                                    created_ip = ?, 
                                    created_dateTime = ?";

        $sqlMailer['PARAM'][] = array('FILD' => 'data_table', 'DATA' => $tableName, 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'sender_email', 'DATA' => $_REQUEST['sender_email'], 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'sender_name', 'DATA' => $_REQUEST['sender_name'], 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'reply_to', 'DATA' => $_REQUEST['reply_to'], 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'created_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'created_dateTime', 'DATA' => date("Y-m-d H:i:s"), 'TYP' => 's');
        $id = $mycms->sql_insert($sqlMailer);
        $mycms->setSession("CIRCULATION_STEP", 2);
        $mycms->redirect('mail_circulation.php?show=template_resource&key=' . base64_encode($id));
        break;

    /***************** STEP 2 *****************/
    case 'insert_template_resource':
        $uploadDir = __DIR__ . "/../../uploads/MAIL.CIRCULATION.RESOURCES/";

        $logo = "";
        if (!empty($_FILES['logo']['name'])) {
            $logo_title = uniqid("logo_") . "_" . basename(str_replace(" ", "_", $_FILES['logo']['name']));
            $path = $uploadDir . $logo_title;
            move_uploaded_file($_FILES['logo']['tmp_name'], $path);
        }

        // Handle up to 12 flyer images
        $flyerImagesArr = [];
        // echo "<pre>";print_r($_FILES['flyer_images']); die;
        if (!empty($_FILES['flyer_images']['name'])) {
            foreach ($_FILES['flyer_images']['name'] as $key => $name) {
                if ($name == "") continue;
                $title = uniqid("flyer") . "_" . basename(str_replace(" ", "_", $name));
                $path = $uploadDir . $title;
                if (move_uploaded_file($_FILES['flyer_images']['tmp_name'][$key], $path)) {
                } else {
                    echo "Error: Unable to move file to destination directory.<br>";
                    echo "Upload tmp: " . $_FILES['flyer_images']['tmp_name'][$key] . "<br>";
                    echo "Destination: " . $path . "<br>";
                    echo "is_writable(uploadDir)? " . (is_writable($uploadDir) ? 'yes' : 'no');
                    die;
                };
                $flyerImagesArr[] = $title;
            }
        }
        $flyerImages = implode(",", $flyerImagesArr);

        $icons = "";
        if (!empty($_FILES['icons']['name'])) {
            $icons_title = $uploadDir . uniqid("icon_") . "_" . basename(str_replace(" ", "_", $_FILES['icons']['name']));
            $icons_path = $uploadDir . $icons_title;
            move_uploaded_file($_FILES['icons']['tmp_name'], $icons_path);
        }

        $sqlInsert['QUERY'] = "INSERT INTO `rcg_mailer_circulation_resources` 
            (logo, flyer_images, flyer_link, icons, icon_links, template)
            VALUES (?, ?, ?, ?, ?, ?)";
        $sqlInsert['PARAM'][] = array('FILD' => 'logo', 'DATA' => $logo_title, 'TYP' => 's');
        $sqlInsert['PARAM'][] = array('FILD' => 'flyer_images', 'DATA' => json_encode($flyerImagesArr), 'TYP' => 's');
        $sqlInsert['PARAM'][] = array('FILD' => 'flyer_link', 'DATA' => $_REQUEST['flyer_link'], 'TYP' => 's');
        $sqlInsert['PARAM'][] = array('FILD' => 'icons', 'DATA' => $icons, 'TYP' => 's');
        $sqlInsert['PARAM'][] = array('FILD' => 'icon_links', 'DATA' => $_REQUEST['icon_links'], 'TYP' => 's');
        $sqlInsert['PARAM'][] = array('FILD' => 'template', 'DATA' => $_REQUEST['template'], 'TYP' => 's');
        $resource_id = $mycms->sql_insert($sqlInsert);
        $mycms->setSession("CIRCULATION_STEP", 3);
        $mycms->redirect('mail_circulation.php?show=layout&resource_id=' . $resource_id . '&key=' . $_REQUEST['key']);
        break;

    /***************** STEP 3 *****************/
    case 'insert_mailer_layout':

        $encodedContent = htmlspecialchars($_REQUEST['html'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $subject = addslashes(trim($_REQUEST['subject']));
        // Save HTML in mailer_templates_layout
        $sqlInsertHTML     =    array();
        $sqlInsertHTML['QUERY'] = "INSERT INTO `rcg_mailer_circulation_layout`
        					   SET `html` = ?,
                               `subject` = ?,
                               `resource_id` = ?,
                               `created_dateTime` = ? ";
        $sqlInsertHTML['PARAM'][]    =    array('FILD' => 'html',         'DATA' => $encodedContent,                                'TYP' => 's');
        $sqlInsertHTML['PARAM'][]    =    array('FILD' => 'subject',         'DATA' => $subject,                                'TYP' => 's');
        $sqlInsertHTML['PARAM'][]    =    array('FILD' => 'resource_id',         'DATA' => $_REQUEST['resource_id'],                                'TYP' => 's');
        $sqlInsertHTML['PARAM'][]    =    array('FILD' => 'created_dateTime',         'DATA' => date("Y-m-d H:i:s"),                                'TYP' => 's');
        $layout_id = $mycms->sql_insert($sqlInsertHTML);


        $sqlUpdate     =    array();
        $sqlUpdate['QUERY'] = "UPDATE `rcg_mailer_circulation_details`
        					   SET `layout_id` = ?
        					 WHERE `id` = ?";
        $sqlUpdate['PARAM'][]    =    array('FILD' => 'layout_id',         'DATA' => $layout_id,                                'TYP' => 's');
        $sqlUpdate['PARAM'][]    =    array('FILD' => 'id',         'DATA' => base64_decode($_REQUEST['key']),            'TYP' => 's');
        $mycms->sql_update($sqlUpdate);
        $mycms->setSession("CIRCULATION_STEP", 4);
        $mycms->redirect('mail_circulation.php?show=final&key=' . $_REQUEST['key']);
        break;
    // ******************** STEP 4 ***************************8

    case 'send_test_mail':

        $sqlDetails     =    array();
        $sqlDetails['QUERY']    = "SELECT * FROM `rcg_mailer_circulation_details`
						   WHERE `id` = ?";
        $sqlDetails['PARAM'][]    =    array('FILD' => 'id',              'DATA' => $_REQUEST['mailer_id'],                      'TYP' => 's');
        $resultDetails             = $mycms->sql_select($sqlDetails);
        $rowMailDetails             = $resultDetails[0];

        $layout_id = $rowMailDetails['layout_id'];
        $sql     =    array();
        $sql['QUERY']    = "SELECT * FROM `rcg_mailer_circulation_layout`
						   WHERE `id` = ?";
        $sql['PARAM'][]    =    array('FILD' => 'id',              'DATA' => $layout_id,                      'TYP' => 's');
        $result             = $mycms->sql_select($sql);
        $row             = $result[0];

        $toEmail = $_REQUEST['test_email_id'];
        $toName = $_REQUEST['test_name'];

        $fromName = $rowMailDetails['sender_name'];
        $fromEmail = $rowMailDetails['sender_email'];
        $subject = $row['subject'];
        $reply_to = $rowMailDetails['reply_to'];
        $message = htmlspecialchars_decode($row['html']);
        $response =  $mycms->sendgridMailSendV3DiffEmail($toName, $toEmail, $fromName, $fromEmail, $subject, $message, '', $reply_to);
        // echo $response;die;
        if ($response == "Success") {
            echo "Success";
        }
        break;

    case 'start_circulation':


        $sqlDetails     =    array();
        $sqlDetails['QUERY']    = "SELECT * FROM `rcg_mailer_circulation_details`
						   WHERE `id` = ?";
        $sqlDetails['PARAM'][]    =    array('FILD' => 'id',              'DATA' => $_REQUEST['mailer_id'],                      'TYP' => 's');
        $resultDetails             = $mycms->sql_select($sqlDetails);
        $rowMailDetails             = $resultDetails[0];

        $layout_id = $rowMailDetails['layout_id'];
        $sql     =    array();
        $sql['QUERY']    = "SELECT * FROM `rcg_mailer_circulation_layout`
						   WHERE `id` = ?";
        $sql['PARAM'][]    =    array('FILD' => 'id',              'DATA' => $layout_id,                      'TYP' => 's');
        $result             = $mycms->sql_select($sql);
        $layout             = htmlspecialchars_decode($result[0]['html']);
        // echo $rowMailDetails['data_table'];die;
        $databaseName = $rowMailDetails['data_table']; //'circulation_test_ics20251007_113414'; //$rowMailDetails['data_table'];
        $layoutId     = $rowMailDetails['layout_id'];
        $subject      = $result[0]['subject'];
        $fromEmail    = $rowMailDetails['sender_email'];
        $fromName     = $rowMailDetails['sender_name'];
        $offset       = intval($_POST['offset']);

        $batchSize = 40; // how many emails per batch

        // Get template HTML

        $templateHtml = $layout;
        // Count total emails
        $countResult = $mycms->sql_select([
            'QUERY' => "SELECT COUNT(*) as total FROM `$databaseName` WHERE `status`='A' AND `isBlackListed`='N'"
        ]);
        $totalEmails = $countResult[0]['total'] ?? 0;

        // Fetch batch
        $sqlFetch = array();
        $sqlFetch['QUERY'] = "SELECT * FROM `$databaseName` 
                          WHERE `status`='A' AND `isBlackListed`='N' 
                          ORDER BY id DESC LIMIT $batchSize OFFSET $offset";
        $batchData = $mycms->sql_select($sqlFetch);

        $sent = 0;
        foreach ($batchData as $row) {
            if (!empty($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $message = str_replace(['{{name}}', '{{email}}'], [$row['name'], $row['email']], $templateHtml);
                $mycms->sendgridMailSendV3DiffEmail($row['name'], $row['email'], $fromName, $fromEmail, $subject, $message);

                // Update record
                $update = [
                    'QUERY' => "UPDATE `$databaseName` SET status='D' WHERE id=?",
                    'PARAM' => [['FILD' => 'id', 'DATA' => $row['id'], 'TYP' => 'i']]
                ];
                $mycms->sql_update($update, false);
                $sent++;
            }
        }



        $activeCountResult = $mycms->sql_select([
            'QUERY' => "SELECT COUNT(*) as 'total' FROM `$databaseName` WHERE `isBlackListed`='N'"
        ]);
        $deactiveCountResult = $mycms->sql_select([
            'QUERY' => "SELECT COUNT(*) as 'D' FROM `$databaseName` WHERE `status`='D' AND `isBlackListed`='N'"
        ]);
        $nextOffset = $offset + $batchSize;
        $completed = ($nextOffset >= $totalEmails);
        $progress = $totalEmails > 0 ? round(($deactiveCountResult[0]['D'] / $totalEmails) * 100, 2) : 100;
        echo json_encode([
            'completed' => $completed,
            'next_offset' => $nextOffset,
            'progress' => $progress,
            'total_sent' => min($nextOffset, $totalEmails),
            // 'message' => "📨 Sent $sent emails (Batch " . ($offset / $batchSize + 1) . ")"
            'message' => "📨 Sent " . $deactiveCountResult[0]['D'] . "/" . $activeCountResult[0]['total'] . " emails "
        ]);
        exit;
        break;


    case 'activeDatabase':
        $tableName = $_REQUEST['table'];
        $sqlUpdateHtml = array();
        $sqlUpdateHtml['QUERY']       = "UPDATE `" . $tableName . "` 
											 SET `status` 		= ?";
        $sqlUpdateHtml['PARAM'][]   = array('FILD' => 'html',          'DATA' => 'A',                              'TYP' => 's');
        $mycms->sql_update($sqlUpdateHtml);
        $mycms->redirect('mail_circulation.php?show=final&key=' . $_REQUEST['key']);
        break;

    case 'InactiveDatabase':
        $tableName = $_REQUEST['table'];
        $sqlUpdateHtml = array();
        $sqlUpdateHtml['QUERY']       = "UPDATE `" . $tableName . "` 
											 SET `status` 		= ?";
        $sqlUpdateHtml['PARAM'][]   = array('FILD' => 'html',          'DATA' => 'I',                              'TYP' => 's');
        $mycms->sql_update($sqlUpdateHtml);
        $mycms->redirect('mail_circulation.php?show=final&key=' . $_REQUEST['key']);
        break;

    case 'edit_mailer_layout':

        $encodedContent = htmlspecialchars($_REQUEST['edit_html'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $subject = addslashes(trim($_REQUEST['subject']));


        $sqlUpdateHtml = array();
        $sqlUpdateHtml['QUERY']       = "UPDATE `rcg_mailer_circulation_layout`
											 SET `html` 		= ?,
                                                 `subject` = ?
											 WHERE `id` = ?";
        $sqlUpdateHtml['PARAM'][]   = array('FILD' => 'html',          'DATA' => $encodedContent,                              'TYP' => 's');
        $sqlUpdateHtml['PARAM'][]   = array('FILD' => 'subject',          'DATA' => $subject,                              'TYP' => 's');
        $sqlUpdateHtml['PARAM'][]   = array('FILD' => 'id',               'DATA' => $_REQUEST['layout_id'],    'TYP' => 's');
        $mycms->sql_update($sqlUpdateHtml);

        $mycms->redirect('mail_circulation.php?show=final&key=' . $_REQUEST['key']);
        break;

    case 'deleteMailer':
        $sqlMailerDetails['QUERY'] = "SELECT `data_table` FROM `rcg_mailer_circulation_details` WHERE id = '" . $_REQUEST['id'] . "' ";
        $res_details = $mycms->sql_select($sqlMailerDetails);

        $databaseName = $res_details[0]['data_table'];

        $sqlFindDatabaseUsage['QUERY'] = "SELECT COUNT(*) as total FROM `rcg_mailer_circulation_details`
                                         WHERE data_table = '" . $databaseName . "' AND  status= 'A' ";
        $res_total = $mycms->sql_select($sqlFindDatabaseUsage);
        if($res_total[0]['total'] <= 1) {
            $mycms->sql_query("DROP TABLE IF EXISTS " . $databaseName);
        }

        $sqlMailer['QUERY'] = "UPDATE  `rcg_mailer_circulation_details` 
                                SET status = 'D'  
                                WHERE `id` = '" . $_REQUEST['id'] . "' ";

        $mycms->sql_update($sqlMailer);
        $mycms->redirect('mail_circulation.php');
        break;
    case 'edit_database':
        $dbName = addslashes(str_replace(" ", "_", $_REQUEST['database_name']));
        $tableName = "circulation_" . $dbName . date("Ymd_His");

        $sqlMailerDetails['QUERY'] = "SELECT `data_table` FROM `rcg_mailer_circulation_details` WHERE id = '" . $_REQUEST['id'] . "' ";
        $res_details = $mycms->sql_select($sqlMailerDetails);


        // Create table with static structure
        $sqlCreate = "CREATE TABLE IF NOT EXISTS `$tableName` ( 
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200),
            email VARCHAR(200),
            phone VARCHAR(100),
            status VARCHAR(10) DEFAULT 'I',
            isBlackListed VARCHAR(10) DEFAULT 'N'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $mycms->sql_query($sqlCreate);

        // Insert CSV data
        if (($handle = fopen($_FILES['csv_file']['tmp_name'], "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                if ($row == 1) continue;

                $delegate_name = $data[0] == '' ? null : $data[0];

                $sqlInsert['QUERY'] = "INSERT INTO `$tableName` (name, email, phone, status, isBlackListed ) VALUES (?, ?, ?, ?, ?)";

                $sqlInsert['PARAM'] = [];
                $sqlInsert['PARAM'][] = array('FILD' => 'name', 'DATA' => $delegate_name, 'TYP' => 's');
                $sqlInsert['PARAM'][] = array('FILD' => 'email', 'DATA' => $data[1], 'TYP' => 's');
                $sqlInsert['PARAM'][] = array('FILD' => 'phone', 'DATA' => null, 'TYP' => 's');
                $sqlInsert['PARAM'][] = array('FILD' => 'status', 'DATA' => 'I', 'TYP' => 's');
                $sqlInsert['PARAM'][] = array('FILD' => 'isBlackListed', 'DATA' => 'N', 'TYP' => 's');
                $mycms->sql_insert($sqlInsert);
            }
            fclose($handle);

            // DELETE PREVIOUS 
            $mycms->sql_query("DROP TABLE IF EXISTS " . $res_details[0]['data_table']);
        }

        // Insert into mailer_structure
        $sqlMailer['QUERY'] = "UPDATE  `rcg_mailer_circulation_details` 
                                SET data_table = ?, 
                                    sender_email = ?, 
                                    sender_name = ?, 
                                    reply_to = ?, 
                                    created_ip = ?, 
                                    created_dateTime = ? 
                                    WHERE `id` = '" . $_REQUEST['id'] . "' ";

        $sqlMailer['PARAM'][] = array('FILD' => 'data_table', 'DATA' => $tableName, 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'sender_email', 'DATA' => $_REQUEST['sender_email'], 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'sender_name', 'DATA' => $_REQUEST['sender_name'], 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'reply_to', 'DATA' => $_REQUEST['reply_to'], 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'created_ip', 'DATA' => $_SERVER['REMOTE_ADDR'], 'TYP' => 's');
        $sqlMailer['PARAM'][] = array('FILD' => 'modified_dateTime', 'DATA' => date("Y-m-d H:i:s"), 'TYP' => 's');
        $id = $mycms->sql_update($sqlMailer);
        $mycms->redirect('mail_circulation.php?show=final&key=' . $_REQUEST['key']);
        break;

    case 'add_image':
        $uploadDir = __DIR__ . "/../../uploads/MAIL.CIRCULATION.RESOURCES/";

        $image = "";
        if (!empty($_FILES['image']['name'])) {
            $title = uniqid("add_") . "_" . basename(str_replace(" ", "_", $_FILES['image']['name']));
            $path = $uploadDir . $title;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {

                $sqlImages = array();
                $sqlImages['QUERY']       = "SELECT `flyer_images` FROM `rcg_mailer_circulation_resources`
											
											 WHERE `id` = ?";

                $sqlImages['PARAM'][]   = array('FILD' => 'id',               'DATA' => $_REQUEST['resource_id'],    'TYP' => 's');
                $result_images =  $mycms->sql_select($sqlImages);
                $flyer_iamges_arr = json_decode($result_images[0]['flyer_images']);
                array_push($flyer_iamges_arr, $title);
                // print_r($flyer_iamges_arr);
                // die;


                $sqlUpdateHtml = array();
                $sqlUpdateHtml['QUERY']       = "UPDATE `rcg_mailer_circulation_resources`
											 SET `flyer_images` 		= ?
                                               
											 WHERE `id` = ?";
                $sqlUpdateHtml['PARAM'][]   = array('FILD' => 'flyer_images',     'DATA' => json_encode($flyer_iamges_arr),       'TYP' => 's');
                $sqlUpdateHtml['PARAM'][]   = array('FILD' => 'id',               'DATA' => $_REQUEST['resource_id'],    'TYP' => 's');
                $mycms->sql_update($sqlUpdateHtml);
            }
        }

        $mycms->redirect('mail_circulation.php?show=edit_layout&id=' . $_REQUEST['id'] . '&key=' . $_REQUEST['key']);
        break;
}

function pageRedirection($fileName, $messageCode, $additionalString = "")
{
    global $mycms, $cfg;

    $pageKey                                      = "_pgn_";
    $pageKeyVal                                   = ($_REQUEST[$pageKey] == "") ? 0 : $_REQUEST[$pageKey];

    @$searchString                                = "";
    $searchArray                                  = array();

    $searchArray[$pageKey]                        = $pageKeyVal;
    $searchArray['src_tariff_classification']  = trim($_REQUEST['src_tariff_classification']);

    foreach ($searchArray as $searchKey => $searchVal) {
        if ($searchVal != "") {
            $searchString .= "&" . $searchKey . "=" . $searchVal;
        }
    }

    $mycms->redirect($fileName . "?m=" . $messageCode . $additionalString . $searchString);
}
