<?php
include_once('init.php');
include_once("icons.php"); 
 $WorkshopId   = isset($_POST['workshop_id']) ? $_POST['workshop_id'] : null;
 $RegClassId   = isset($_POST['registration_classification_id']) ? $_POST['registration_classification_id'] : null;
 $title   = isset($_POST['title']) ? $_POST['title'] : null;
 $classification   = isset($_POST['classification']) ? $_POST['classification'] : null;
// // Enable error reporting for debugging
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// // Safely get POST variables
// $WorkshopId   = isset($_POST['workshop_id']) ? $_POST['workshop_id'] : null;
// $RegClassId   = isset($_POST['registration_classification_id']) ? $_POST['registration_classification_id'] : null;

// // Debugging: check if the data is coming through
// echo "<pre>POST received:\n";
// var_dump($_POST);
// var_dump($WorkshopId, $RegClassId);
?>
<div class="pop_up_wrap">
  <div class="pop_up_inner">
    <!-- Edit workshop tariff pop up -->
    <div class="pop_up_body" id="editworkshoptariff">
        <div class="registration_pop_up">
            <div class="registration-pop_heading">
                <span>Update Workshop Tariff</span>
                <p>
                    <a href="javascript:void(null)" class="popup_close icon_hover badge_danger action-transparent"><?php close(); ?></a>
                </p>
            </div>
            <form name="frmTariffEdit" id="frmTariffEdit" method="post" action="workshop_tariff.process.php" onsubmit="return onSubmitAction();">
                <input type="hidden" name="act" id="act" value="update" />
                <input type="hidden" name="workshop_classification_id" value="<?= $WorkshopId?>"/>
                <input type="hidden" name="registration_classification_id" value="<?= $RegClassId?>" />
                
            <div class="registration-pop_body">
                <div class="registration-pop_body_box">
                    <div class="registration-pop_body_box_inner p-0 bg-transparent border-0">
                        <div class="form_grid">
                            <div class="frm_grp span_2">
                                <p class="frm-head">Workshop Title</p>
                                <p class="typed_data" ><?= $title?></p>
                            </div>
                            <div class="frm_grp span_2">
                                <p class="frm-head">Workshop Type</p>
                                <p class="typed_data" ><?= $classification?></p>
                            </div>
                            <?
                            $sql	=	array();
                            $sql['QUERY'] = "SELECT cutoff.cutoff_title,cutoff.id 
                                            FROM "._DB_WORKSHOP_CUTOFF_." cutoff
                                            WHERE status = ?";
                            $sql['PARAM'][]  = array('FILD' => 'status',  'DATA' =>'A',  'TYP' => 's');					 
                            $res = $mycms->sql_select($sql);
                             
                            foreach($res as $key=>$title)
                            {	
                                  $rowsTariffAmount = [
                                    'inr_amount' => '',
                                    'usd_amount' => ''
                                ];
                            ?>
                            <div class="registration-pop_body_box_inner span_2">
                            
                                <h4 class="registration-pop_body_box_heading">
                                    <span><?=strip_tags($title['cutoff_title'])?></span>
                                </h4>
                                <?
                              
                               
                                $sqlFetchTariffAmount1 = array();
                                $sqlFetchTariffAmount1['QUERY'] = "SELECT * 
                                                                FROM "._DB_TARIFF_WORKSHOP_." 
                                                                WHERE `workshop_id` = ? 
                                                                AND `tariff_cutoff_id` = ?
                                                                AND `registration_classification_id` = ?";
                                $sqlFetchTariffAmount1['PARAM'][] = array('FILD'=>'workshop_id', 'DATA'=>$WorkshopId, 'TYP'=>'s');
                                $sqlFetchTariffAmount1['PARAM'][] = array('FILD'=>'tariff_cutoff_id', 'DATA'=>$title['id'], 'TYP'=>'s');
                                $sqlFetchTariffAmount1['PARAM'][] = array('FILD'=>'registration_classification_id', 'DATA'=>$RegClassId, 'TYP'=>'s');
                                $resultFetchTariffAmount1 = $mycms->sql_select($sqlFetchTariffAmount1);
                                $sqlRegClasf 	 = array();	

                                $sqlRegClasf['QUERY']	= "SELECT `classification_title`,`id`,`currency` 
                                                            FROM "._DB_REGISTRATION_CLASSIFICATION_." WHERE `id` = '".$RegClassId."'";
                                $resRegClasf			= $mycms->sql_select($sqlRegClasf);		
                            	
                                if (!empty($resultFetchTariffAmount1)) {
                                    $rowsTariffAmount = $resultFetchTariffAmount1[0];
                                                                // print_r($rowsTariffAmount);
                                   ?>
                                     <?
                                    } else 
                                    { 
                                    
                                        $rowsTariffAmount = [
                                            'inr_amount' => '0.00',
                                            'usd_amount' => '0.00',
                                        ];
                                      
                                        }
                                     
                                 $currency = !empty($resRegClasf) ? $resRegClasf[0]['currency'] : '';
                                ?>

                                <input type="hidden" name="tariff_cutoff_id_edit[]" id="tariff_cutoff_id_edit_<?=$title['id']?>" value="<?=$title['id']?>" />
                                <input type="hidden" class="currencyClass" name="currency[<?=$title['id']?>]" id="currency_<?=$title['id']?>" value="<?=$currency?>" />
                                <div class="form_grid">
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">INR</p>
                                        <input value="<?=$rowsTariffAmount['inr_amount']?>" name="tariff_inr_cutoff_id_edit[<?=$title['id']?>]" id="tariff_inr_first_cutoff_id_edit_<?=$title['id']?>">
                                    </div>
                                    <div class="frm_grp span_2">
                                        <p class="frm-head">USD</p>
                                        <input value="<?=$rowsTariffAmount['usd_amount']?>" name="tariff_usd_cutoff_id_edit[<?=$title['id']?>]" id="tariff_usd_first_cutoff_id_edit_<?=$title['id']?>">
                                    </div>
                                </div>
                            </div>
                                <?
                            }
                            ?>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="registration-pop_footer">
                <div class="registration_btn_wrap">
                    <button class="popup_close badge_dark">Cancel</button>
                    <button type="submit" class="mi-1 badge_success">Update</button>
                </div>
            </div>
        </div>
    </div>
<!-- Edit workshop tariff pop up -->
</div>
 </div>
